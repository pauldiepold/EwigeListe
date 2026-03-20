<template>
    <div class="mx-auto max-w-lg">
        <h5 class="my-4 font-bold">Punkteverlauf:</h5>
        <div>
            <canvas id="profilePointChart" height="400"></canvas>
        </div>

        <h5 class="mt-6 mb-4 font-bold">Anzahl der Spiele</h5>
        <div>
            <canvas id="profileGameChart" height="400"></canvas>
        </div>
    </div>
</template>

<script>
    import Chart from 'chart.js/auto';

    export default {
        props: ['profile_id'],
        mounted() {
            axios.get('/charts/profile/' + this.profile_id)
                .then(function (response) {
                    let data = response.data;
                    Chart.defaults.font.family = '"Open Sans"';
                    Chart.defaults.animation.duration = 0;
                    //console.log(data);

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

                    var ctx = document.getElementById("profilePointChart");

                    var LineGraph = new Chart(ctx, {
                        type: 'line',
                        data: chartdataPoints,
                        options: {
                            maintainAspectRatio: false,
                            scales: {
                                x: {
                                    ticks: {
                                        maxTicksLimit: 10,
                                    },
                                    grid: {}
                                },
                            },
                            plugins: {
                                tooltip: {
                                    mode: 'index',
                                    intersect: false
                                },
                                legend: {
                                    display: false
                                }
                            },
                            elements: {
                                point: {
                                    radius: 0,
                                    pointHitRadius: 10
                                },
                                line: {
                                    fill: true,
                                    tension: 0.2,
                                    borderWidth: 1.1
                                }
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

                    var ctx = document.getElementById("profileGameChart");

                    var LineGraph = new Chart(ctx, {
                        type: 'line',
                        data: chartdataGames,
                        options: {
                            maintainAspectRatio: false,
                            scales: {
                                x: {
                                    ticks: {
                                        maxTicksLimit: 10,
                                    },
                                    grid: {}
                                },
                            },
                            plugins: {
                                tooltip: {
                                    mode: 'index',
                                    intersect: false
                                },
                                legend: {
                                    display: false
                                }
                            },
                            elements: {
                                point: {
                                    radius: 0,
                                    pointHitRadius: 10
                                },
                                line: {
                                    fill: true,
                                    tension: 0.2,
                                    borderWidth: 1.1
                                }
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
