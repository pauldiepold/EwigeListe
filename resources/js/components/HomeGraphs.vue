<template>
    <div class="row justify-content-center mt-4">
        <div class="col col-xl-8 col-lg-9 col-md-12 col-sm-12">
            <div>
                <canvas id="homeGameChart" height="500"></canvas>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        props: ['player_id'],
        mounted() {
            axios.get('/charts/home/')
                .then(function (response) {
                    let data = response.data;
                    console.log(data);

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