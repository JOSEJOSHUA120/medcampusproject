import './bootstrap';
import 'datatables.net';
import Chart from 'chart.js/auto';
import Alpine from 'alpinejs';

window.Chart = Chart;
window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.data-table').forEach(function (el) {
        if (!el.dataset.dtInitialized) {
            new DataTable(el, {
                language: {
                    url: '//cdn.datatables.net/plug-ins/2.3.8/i18n/id.json',
                },
            });
            el.dataset.dtInitialized = '1';
        }
    });
});
