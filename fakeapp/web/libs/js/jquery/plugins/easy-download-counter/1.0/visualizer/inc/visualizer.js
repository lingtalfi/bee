(function ($) {
    $(document).ready(function () {


        var jContainer = $("#chartscontainer");


        var url = window.location.href;

        $(".edctrigger").on('click', function () {

            var id = $(this).attr("data-edc-id");
            $.post(url, {
                id: id
            }, function (data) {


                jContainer.empty();
                for (var year in data) {

                    var jGraph = $('<div class="graph"></div>');
                    var jTitle = $('<h2>' + year + '</h2>');
                    var jCanvas = $('<canvas width="400" height="400"></canvas>');
                    jContainer.append(jGraph);
                    jGraph.append(jTitle);
                    jGraph.append(jCanvas);

                    var ctx = jCanvas.get(0).getContext("2d");
                    var options = {};
                    var months = data[year];

                    var cData = {};
                    var labels = [];
                    var monthData = [];

                    for (var month in months) {
                        labels.push(month);
                        monthData.push(months[month]);
                    }
                    cData.labels = labels;
                    cData.datasets = [
                        {
                            label: "My First dataset",
                            fillColor : "rgba(151,187,205,0.2)",
                            strokeColor : "rgba(151,187,205,1)",
                            pointColor : "rgba(151,187,205,1)",
                            pointStrokeColor : "#fff",
                            pointHighlightFill : "#fff",
                            pointHighlightStroke : "rgba(151,187,205,1)",
                            data: monthData
                        }
                    ];
                    var myLineChart = new Chart(ctx).Line(cData, options);
                }

            }, 'json');

            return false;
        });


    });
})(jQuery);