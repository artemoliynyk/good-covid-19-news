/*
 * Global JS
 */
import '../scss/app.scss';

require('bootstrap');

$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();

    setTimeout(function () {
        $('.alert-dismissible.alert-auto').fadeOut(200, function () {
            $(this).remove();
        })
    }, 2000);
});
