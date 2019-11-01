<template>
    <div class="tw-max-w-lg mx-auto">
        <canvas id="groupChart"></canvas>
    </div>
</template>

<script>

    export default {
        props: ['group_id'],
        mounted() {
            axios.get('/charts/home/' + this.group_id)
                .then(function (response) {
                    let data = response.data;

                    Chart.defaults.global.defaultFontFamily = '"Open Sans"';
                    Chart.defaults.global.animation.duration = 0;

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

                    var ctx = $("#groupChart");

                    var LineGraph = new Chart(ctx, {
                        type: 'line',
                        data: chartdataGames,
                        options: {
                            tooltips: {
                                mode: 'index',
                                intersect: false
                            },
                            maintainAspectRatio: true,
                            aspectRatio: 1.4,
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
