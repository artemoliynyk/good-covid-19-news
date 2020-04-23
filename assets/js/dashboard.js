require('chart.js');

$(document).ready(function () {
    // active cases
    var $chartActive = $('#chart-active');
    $.get({
        url: $chartActive.data('url'),
        dataType: 'json',
        success: function (json) {
            let labels = [];
            let data = [];

            $.each(json.data, function (title, value) {
                labels.push(title);
                data.push(value);
            });

            var chartActive = new Chart($chartActive[0], {
                type: 'line',
                options: {
                    //aspectRatio: 1.5,
                },
                data: {
                    datasets: [{
                        label: json.title,
                        data: data,
                        fill: false,
                        borderColor: '#FFCE67',
                        pointBorderColor: '#FFCE67',
                        borderWidth: 1
                    }],
                    labels: labels,
                }
            });
        }
    });


    // recovered cases
    var $chartRecovered = $('#chart-recovered');
    $.get({
        url: $chartRecovered.data('url'),
        dataType: 'json',
        success: function (json) {
            let labels = [];
            let data = [];

            $.each(json.data, function (title, value) {
                labels.push(title);
                data.push(value);
            });

            var chartRecovered = new Chart($chartRecovered[0], {
                type: 'line',
                options: {
                    //aspectRatio: 1.5,
                },
                data: {
                    datasets: [{
                        label: json.title,
                        data: data,
                        fill: false,
                        borderColor: '#56CC9D',
                        pointBorderColor: '#56CC9D',
                        borderWidth: 1
                    }],
                    labels: labels,
                }
            });
        }
    });

    // new cases daily
    var $chartNewDaily = $('#chart-new-daily');
    $.get({
        url: $chartNewDaily.data('url'),
        dataType: 'json',
        success: function (json) {
            let labels = [];
            let data = [];

            $.each(json.data, function (title, value) {
                labels.push(title);
                data.push(value);
            });

            var chartNewDaily = new Chart($chartNewDaily[0], {
                type: 'bar',
                options: {
                    //aspectRatio: 1.5,
                },
                data: {
                    datasets: [{
                        label: json.title,
                        data: data,
                        fill: false,
                        backgroundColor: '#F3969A',
                        borderColor: '#F3969A',
                        pointBorderColor: '#F3969A',
                        borderWidth: 1
                    }],
                    labels: labels,
                }
            });
        }
    });

    // new recovered daily
    var $chartRecoveredDaily = $('#chart-recovered-daily');
    $.get({
        url: $chartRecoveredDaily.data('url'),
        dataType: 'json',
        success: function (json) {
            let labels = [];
            let data = [];

            $.each(json.data, function (title, value) {
                labels.push(title);
                data.push(value);
            });

            var chartRecoveredDaily = new Chart($chartRecoveredDaily[0], {
                type: 'bar',
                options: {
                    //aspectRatio: 1.5,
                },
                data: {
                    datasets: [{
                        label: json.title,
                        data: data,
                        fill: false,
                        backgroundColor: '#6CC3D5',
                        borderColor: '#6CC3D5',
                        pointBorderColor: '#6CC3D5',
                        borderWidth: 1
                    }],
                    labels: labels,
                }
            });
        }
    });

});
