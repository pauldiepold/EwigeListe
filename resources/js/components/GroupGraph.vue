<template>
    <div class="mx-auto max-w-2xl">
        <canvas id="groupChart" height="450"></canvas>
    </div>
</template>

<script>
    import Chart from 'chart.js/auto';

    export default {
        props: ['group_id'],
        mounted() {
            axios.get('/charts/home/' + this.group_id)
                .then(function (response) {
                    let data = response.data;

                    Chart.defaults.font.family = '"Open Sans"';
                    Chart.defaults.animation.duration = 0;

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

                    var ctx = document.getElementById("groupChart");

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
