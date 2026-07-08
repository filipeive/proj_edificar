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
});
