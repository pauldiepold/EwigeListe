<template>
    <div class="row justify-content-center mt-4">
        <div class="col col-xl-8 col-lg-9 col-md-12 col-sm-12">
            <h5 class="my-3 font-weight-bold">Punkteverlauf:</h5>
            <div>
                <canvas id="profilePointChart" height="560"></canvas>
            </div>
            <hr>
            <h5 class="my-3 font-weight-bold">Anzahl der Spiele</h5>
            <div>
                <canvas id="profileGameChart" height="560"></canvas>
            </div>
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
