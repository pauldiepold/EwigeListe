<template>
    <div class="tw-mx-auto tw-max-w-xl">
        <canvas id="roundChart" height="450"></canvas>
    </div>
</template>

<script>
    import Chart from 'chart.js'

    export default {
        props: ['round_id'],
        mounted() {
            axios.get('/charts/round/' + this.round_id)
                .then(function (response) {
                    let data = response.data;
                    Chart.defaults.global.defaultFontFamily = '"Open Sans"';
                    Chart.defaults.global.animation.duration = 0;
                    let counter = [];
                    const colors = [
                        '#E37222',
                        '#07889B',
                        '#24980C',
                        '#C70E00',
                        '#481620',
                        '#e22fe3',
                        '#5d0be3',
                    ];

                    for (let i in data.points[0]) {
                        counter.push(i);
                    }

                    let chartdata = {
                        labels: counter,
                        datasets: []
                    };
                    for (let i in data.names) {
                        chartdata.datasets.push(
                            {
                                label: data.names[i],
                                data: data.points[i],
                                borderColor: colors[i],
                                backgroundColor: colors[i],
                                lineTension: 0.1,
                            }
                        )
                    }


                    let ctx = $('#roundChart');

                    let LineGraph = new Chart(ctx, {
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
                                },
                                line: {
                                    borderWidth: 1.8,
                                    fill: false
                                }
                            },
                            legend: {
                                labels: {
                                    boxWidth: 10,
                                    usePointStyle: true,
                                    fontSize: 13,
                                    padding: 20
                                }
                            },
                        }
                    });
                })
                .catch(function (error) {
                    console.log(error);
                });

        }
    }
</script>
