/* import Chart from 'chart.js'

$(document).ready(function () {
    $.ajax({
        url: "/charts/1",
        method: "GET",
        success: function (data) {
            Chart.defaults.global.defaultFontFamily = '"Open Sans",sans-serif';
            var counter = [];
            var punkte_1 = data.points[0];
            var punkte_2 = data.points[1];
            var punkte_3 = data.points[2];
            var punkte_4 = data.points[3];
            var punkte_5 = data.points[4];

            for (var i in data.points[0]) {
                counter.push(i);
            }

            if (true) {
                var chartdata = {
                    labels: counter,
                    datasets: [
                        {
                            label: data.names[0],
                            borderWidth: 1.8,
                            fill: false,
                            lineTension: 0.1,
                            borderColor: "#E37222",
                            backgroundColor: "#E37222",
                            data: punkte_1
                        },
                        {
                            label: data.names[1],
                            borderWidth: 1.8,
                            fill: false,
                            lineTension: 0.1,
                            borderColor: "#07889B",
                            backgroundColor: "#07889B",
                            data: punkte_2
                        },
                        {
                            label: data.names[2],
                            borderWidth: 1.8,
                            fill: false,
                            lineTension: 0.1,
                            borderColor: "#24980C",
                            backgroundColor: "#24980C",
                            data: punkte_3
                        },
                        {
                            label: data.names[3],
                            borderWidth: 1.8,
                            fill: false,
                            lineTension: 0.1,
                            borderColor: "#C70E00",
                            backgroundColor: "#C70E00",
                            data: punkte_4
                        },
                        {
                            label: data.names[4],
                            borderWidth: 1.8,
                            fill: false,
                            lineTension: 0.1,
                            borderColor: "#481620",
                            backgroundColor: "#481620",
                            data: punkte_5
                        }
                    ]
                };
            } else {
                var chartdata = {
                    labels: counter,
                    datasets: [
                        {
                            label: data[0].name_1,
                            borderWidth: 1.8,
                            fill: false,
                            lineTension: 0.1,
                            borderColor: "#E37222",
                            backgroundColor: "#E37222",
                            data: punkte_1
                        },
                        {
                            label: data[0].name_2,
                            borderWidth: 1.8,
                            fill: false,
                            lineTension: 0.1,
                            borderColor: "#07889B",
                            backgroundColor: "#07889B",
                            data: punkte_2
                        },
                        {
                            label: data[0].name_3,
                            borderWidth: 1.8,
                            fill: false,
                            lineTension: 0.1,
                            borderColor: "#24980C",
                            backgroundColor: "#24980C",
                            data: punkte_3
                        },
                        {
                            label: data[0].name_4,
                            borderWidth: 1.8,
                            fill: false,
                            lineTension: 0.1,
                            borderColor: "#C70E00",
                            backgroundColor: "#C70E00",
                            data: punkte_4
                        }
                    ]
                };
            }


            var ctx = $('#roundChart');

            var LineGraph = new Chart(ctx, {
                type: 'line',
                data: chartdata,
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        xAxes: [{
                            ticks: {
                                display: true
                            },
                            scaleLabel: {
                                display: true,
                                labelString: "Spiele",
                                lineHeight: 0.2
                            },
                            gridLines: {
                                drawBorder: false
                            }
                        }],
                        yAxes: [{
                            scaleLabel: {
                                display: true,
                                labelString: "Punkte",
                                lineHeight: 0.3
                            },
                            gridLines: {
                                drawBorder: false
                            }
                        }]
                    },
                    tooltips: {
                        //intersect: false
                    },
                    elements: {
                        point: {
                            radius: 0,
                            pointHitRadius: 10
                        }
                    },
                    legend: {
                        labels: {
                            boxWidth: 12
                        }
                    },
                }
            });


        },
        error: function (data) {
            console.log(data);
        },
        dataType: "json"
    });
});

 */