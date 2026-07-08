import './bootstrap';

import Alpine from 'alpinejs';

// Expor função para carregar Chart.js dinamicamente e esvaziar a fila
window.loadChartJS = function() {
    if (window.loadChartJS.loading) return;
    window.loadChartJS.loading = true;
    
    import('chart.js/auto').then(({ default: Chart }) => {
        // Copiar os defaults configurados nas views antes do load
        if (window.ChartDefaults) {
            if (window.ChartDefaults.color) Chart.defaults.color = window.ChartDefaults.color;
            if (window.ChartDefaults.font.family) Chart.defaults.font.family = window.ChartDefaults.font.family;
            if (window.ChartDefaults.font.weight) Chart.defaults.font.weight = window.ChartDefaults.font.weight;
        }
        
        window.Chart = Chart;
        
        // Instanciar todos os gráficos da fila
        if (window.ChartQueue) {
            window.ChartQueue.forEach(item => {
                new Chart(item.ctx, item.config);
            });
            window.ChartQueue = [];
        }
    });
};

// Se já houver elementos na fila ou se for uma página com canvas de gráfico, inicia o load
if ((window.ChartQueue && window.ChartQueue.length > 0) || document.querySelector('canvas')) {
    window.loadChartJS();
}

window.Alpine = Alpine;
Alpine.start();

// Importar os scripts do layout e do PWA
import './layout.js';
import './pwa.js';

// O formatador de moeda é útil e não causa conflitos, por isso fica.
document.addEventListener('DOMContentLoaded', function() {
    const amountInputs = document.querySelectorAll('input[name="amount"]');
    amountInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });
    });
});