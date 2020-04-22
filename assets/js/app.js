/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../scss/app.scss';

require('bootstrap');
require('chart.js');
require('imports-loader?define=>false,this=>window!datatables.net')(window, $)

$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();

    setTimeout(function () {
        $('.alert-dismissible.alert-auto').fadeOut(200, function () {
            $(this).remove();
        })
    }, 2000);

    $('.table-sortable').DataTable({
        paging: false,
        searching: false,
        autoWidth: false,

        fixedHeader: {
            header: true,
            footer: false,
        },
    });
});
