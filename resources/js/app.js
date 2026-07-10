import './bootstrap';
import 'datatables.net';
import Chart from 'chart.js/auto';
import Alpine from 'alpinejs';

window.Chart = Chart;
window.Alpine = Alpine;
Alpine.start();

$.extend(true, $.fn.dataTable.defaults, {
    lengthMenu: [[10, 20, 30, 40, 50, 60, 70, 80, 90, 100], [10, 20, 30, 40, 50, 60, 70, 80, 90, 100]],
    pageLength: 10,
    language: {
        url: '//cdn.datatables.net/plug-ins/2.3.8/i18n/id.json',
    },
});

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.data-table').forEach(function (el) {
        if (!el.dataset.dtInitialized) {
            new DataTable(el);
            el.dataset.dtInitialized = '1';
        }
    });
});
