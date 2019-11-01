<template>
    <div class="tw-mx-auto tw-max-w-lg">
        <h5 class="tw-my-4 tw-font-bold">Punkteverlauf:</h5>
        <div>
            <canvas id="profilePointChart" height="400"></canvas>
        </div>

        <h5 class="tw-mt-6 tw-mb-4 tw-font-bold">Anzahl der Spiele</h5>
        <div>
            <canvas id="profileGameChart" height="400"></canvas>
        </div>
    </div>
</template>

<script>

    export default {
        props: ['profile_id'],
        mounted() {
            axios.get('/charts/profile/' + this.profile_id)
                .then(function (response) {
                    let data = response.data;
                    Chart.defaults.global.defaultFontFamily = '"Open Sans"';
                    Chart.defaults.global.animation.duration = 0;
                    console.log(data);

                    var chartdataPoints = {
                        labels: data.dates,
                        datasets: [
                            {
                                borderColor: "#E37222",
                                backgroundColor: "rgba(244,158,98,0.3)",
                                data: data.points
                            }
                        ]
                    };

                    var ctx = $("#profilePointChart");

                    var LineGraph = new Chart(ctx, {
                        type: 'line',
                        data: chartdataPoints,
                        options: {
                            tooltips: {
                                mode: 'index',
                                intersect: false
                            },
                            maintainAspectRatio: false,
                            scales: {
                                xAxes: [{
                                    distribution: 'series',
                                    ticks: {
                                        maxTicksLimit: 10,
                                    },
                                    gridLines: {}
                                }],
                            },
                            elements: {
                                point: {
                                    radius: 0,
                                    pointHitRadius: 10
                                },
                                line: {
                                    fill: true,
                                    lineTension: 0.2,
                                    borderWidth: 1.1
                                }
                            },
                            legend: {
                                display: false
                            },
                        }
                    });

                    var chartdataGames = {
                        labels: data.gameDates,
                        datasets: [
                            {
                                borderColor: "#E37222",
                                backgroundColor: "rgba(244,158,98,0.3)",
                                data: data.gameCounter
                            }
                        ]
                    };

                    var ctx = $("#profileGameChart");

                    var LineGraph = new Chart(ctx, {
                        type: 'line',
                        data: chartdataGames,
                        options: {
                            tooltips: {
                                mode: 'index',
                                intersect: false
                            },
                            maintainAspectRatio: false,
                            scales: {
                                xAxes: [{
                                    distribution: 'series',
                                    ticks: {
                                        maxTicksLimit: 10,
                                    },
                                    gridLines: {}
                                }],
                            },
                            elements: {
                                point: {
                                    radius: 0,
                                    pointHitRadius: 10
                                },
                                line: {
                                    fill: true,
                                    lineTension: 0.2,
                                    borderWidth: 1.1
                                }
                            },
                            legend: {
                                display: false
                            },
                        }
                    });

                })
                .catch(function (error) {
                    console.log(error);
                });

        },
    }
</script>
