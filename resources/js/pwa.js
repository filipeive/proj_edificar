/**
 * MI-02: PWA Logic — Service Worker, Offline Queue & Universal Document Downloader
 * Portal Life Church — Immersive PWA & Native App JS
 */

import Swal from 'sweetalert2';

document.addEventListener('DOMContentLoaded', function () {
    // Register Service Worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js')
            .then(reg => console.log('SW Registered'))
            .catch(err => console.log('SW Error', err));
    }

    let deferredPrompt;
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        showImmersivePrompt(true);
    });

    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

    function showImmersivePrompt(canInstall) {
        if (sessionStorage.getItem('pwa_prompt_shown')) return;

        Swal.fire({
            title: 'Portal Life Church App',
            text: canInstall
                ? 'Deseja instalar o aplicativo para acesso rápido e em tela cheia?'
                : 'Para uma melhor experiência, você pode usar o modo tela cheia ou adicionar à tela de início.',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#ea580c',
            cancelButtonColor: '#6b7280',
            confirmButtonText: canInstall ? 'Instalar App' : 'Tela Cheia',
            cancelButtonText: 'Agora não',
            footer: '<span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Dica: Abrir como app economiza dados e melhora a navegação.</span>'
        }).then((result) => {
            if (result.isConfirmed) {
                if (canInstall && deferredPrompt) {
                    deferredPrompt.prompt();
                    deferredPrompt.userChoice.then((choiceResult) => {
                        deferredPrompt = null;
                    });
                } else {
                    enterFullScreen();
                }
            }
            sessionStorage.setItem('pwa_prompt_shown', 'true');
        });
    }

    function enterFullScreen() {
        const doc = window.document;
        const docEl = doc.documentElement;
        const requestFullScreen = docEl.requestFullscreen || docEl.mozRequestFullScreen || docEl.webkitRequestFullScreen || docEl.msRequestFullscreen;

        if (requestFullScreen) {
            requestFullScreen.call(docEl);
        } else if (isMobile && /iPhone|iPad|iPod/.test(navigator.userAgent)) {
            Swal.fire({
                title: 'Instalação no iOS',
                text: 'Para instalar o app no iPhone/iPad: clique no botão de Compartilhar e selecione "Adicionar à Tela de Início".',
                icon: 'info',
                confirmButtonText: 'Entendido'
            });
        }
    }

    // ===== 1. NO-FLICKER TOAST NOTIFICATIONS FOR PDF & DOCUMENT DOWNLOADS =====
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3500,
        timerProgressBar: true,
        background: '#0f172a',
        color: '#f8fafc',
    });

    document.addEventListener('click', function (e) {
        const link = e.target.closest('a[href]');
        if (!link) return;

        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;

        const isPdfOrReport = href.includes('/pdf') ||
            href.includes('/export') ||
            href.includes('/report') ||
            href.includes('/download') ||
            link.hasAttribute('download');

        if (isPdfOrReport) {
            e.preventDefault();

            Toast.fire({
                icon: 'info',
                title: 'A descarregar documento...'
            });

            fetch(href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(res => {
                    if (!res.ok) throw new Error('Falha ao descarregar documento.');
                    const disposition = res.headers.get('Content-Disposition');
                    let filename = 'documento.pdf';
                    if (disposition && disposition.indexOf('filename=') !== -1) {
                        const matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disposition);
                        if (matches != null && matches[1]) {
                            filename = matches[1].replace(/['"]/g, '');
                        }
                    } else if (href.includes('.pdf')) {
                        filename = href.split('/').pop().split('?')[0] || 'relatorio.pdf';
                    }
                    return res.blob().then(blob => ({ blob, filename }));
                })
                .then(({ blob, filename }) => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    setTimeout(() => {
                        document.body.removeChild(a);
                        window.URL.revokeObjectURL(url);
                    }, 1000);

                    Toast.fire({
                        icon: 'success',
                        title: `Ficheiro ${filename} guardado!`
                    });
                })
                .catch(err => {
                    console.error('Download error:', err);
                    window.location.href = href;
                });
        }
    });

    // ===== 2. OFFLINE DETECTOR & AUTOMATIC SYNCHRONIZATION QUEUE =====
    function createOfflineBanner() {
        if (document.getElementById('offline-banner')) return;
        const banner = document.createElement('div');
        banner.id = 'offline-banner';
        banner.className = 'fixed top-0 left-0 right-0 z-[10000] bg-amber-500 text-slate-950 font-bold text-xs py-2 px-4 text-center shadow-lg transition-transform duration-300 transform -translate-y-full flex items-center justify-center gap-2';
        banner.innerHTML = '<i class="bi bi-wifi-off text-base"></i> <span>Modo Offline: Suas alterações serão guardadas e sincronizadas ao reconectar à internet.</span>';
        document.body.appendChild(banner);
    }
    createOfflineBanner();

    function updateNetworkStatus() {
        const banner = document.getElementById('offline-banner');
        if (!banner) return;

        if (!navigator.onLine) {
            banner.classList.remove('-translate-y-full');
            banner.classList.add('translate-y-0');
        } else {
            banner.classList.remove('translate-y-0');
            banner.classList.add('-translate-y-full');
            syncOfflineQueue();
        }
    }

    window.addEventListener('online', updateNetworkStatus);
    window.addEventListener('offline', updateNetworkStatus);
    updateNetworkStatus();

    // Process queued offline requests when internet connection is restored
    function syncOfflineQueue() {
        try {
            const queue = JSON.parse(localStorage.getItem('life_offline_queue') || '[]');
            if (queue.length === 0) return;

            Toast.fire({
                icon: 'info',
                title: `A sincronizar ${queue.length} ação(ões) pendente(s)...`
            });

            const syncPromises = queue.map(item => {
                return fetch(item.url, {
                    method: item.method || 'POST',
                    headers: item.headers || {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: item.body ? JSON.stringify(item.body) : null
                });
            });

            Promise.all(syncPromises)
                .then(() => {
                    localStorage.removeItem('life_offline_queue');
                    Toast.fire({
                        icon: 'success',
                        title: 'Sincronização concluída com sucesso!'
                    });
                })
                .catch(err => {
                    console.error('Erro na sincronização offline:', err);
                });
        } catch (e) {
            console.error('Erro ao ler fila offline:', e);
        }
    }
});
