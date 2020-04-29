require('imports-loader?define=>false,this=>window!datatables.net')(window, $);

import '../scss/countries.scss';

$(document).ready(function () {
    var $tableCountries = $('.table-sortable');
    $tableCountries.DataTable({
        paging: false,
        info: false,
        autoWidth: false,

        // default order, starting from 0
        order: [[1, "desc"]]
    });

    var $filter = $('.dataTables_filter');
    $filter.addClass('form-inline').addClass('form-group');
    $filter.find(':input').addClass('form-control').prop('placeholder', $tableCountries.data('search-label'));

    // move to div
    $filter.find(':input').appendTo($filter);

    // translate label
    $filter.find('label').remove();
});
