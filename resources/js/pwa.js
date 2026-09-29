/**
 * MI-02: PWA Logic — Extraído de app.blade.php
 * Portal Life Church — Immersive PWA Experience JS
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
        // Prevent Chrome 67 and earlier from automatically showing the prompt
        e.preventDefault();
        // Stash the event so it can be triggered later.
        deferredPrompt = e;
        console.log('beforeinstallprompt event fired');

        // If we are on mobile, show the prompt with the Install option
        showImmersivePrompt(true);
    });

    // check if on mobile
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
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#6b7280',
            confirmButtonText: canInstall ? 'Instalar App' : 'Tela Cheia',
            cancelButtonText: 'Agora não',
            footer: '<span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Dica: Abrir como app economiza dados e melhora a navegação.</span>'
        }).then((result) => {
            if (result.isConfirmed) {
                if (canInstall && deferredPrompt) {
                    deferredPrompt.prompt();
                    deferredPrompt.userChoice.then((choiceResult) => {
                        if (choiceResult.outcome === 'accepted') {
                            console.log('User accepted the install prompt');
                        }
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

    // ===== MOBILE & PWA UNIVERSAL DOCUMENT / PDF DOWNLOAD INTERCEPTOR =====
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a[href]');
        if (!link) return;

        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;

        // Check if link is a download / PDF / export link
        const isPdfOrReport = href.includes('/pdf') || 
                              href.includes('/export') || 
                              href.includes('/report') || 
                              href.includes('/download') ||
                              link.hasAttribute('download');

        if (isPdfOrReport) {
            e.preventDefault();

            Swal.fire({
                title: 'Descarregar Documento',
                text: 'A transferir o ficheiro para o seu dispositivo...',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
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

                Swal.fire({
                    title: 'Download Concluído!',
                    text: `Ficheiro ${filename} guardado com sucesso.`,
                    icon: 'success',
                    timer: 2500,
                    showConfirmButton: false
                });
            })
            .catch(err => {
                console.error('Download error:', err);
                // Fallback direct navigation
                window.location.href = href;
            });
        }
    });
});
