<template>
    <div class="mx-auto max-w-xl">
        <canvas id="roundChart" height="450"></canvas>
    </div>
</template>

<script>
    import Chart from 'chart.js/auto'

    export default {
        props: ['round_id'],
        mounted() {
            axios.get('/charts/round/' + this.round_id)
                .then(function (response) {
                    let data = response.data;
                    Chart.defaults.font.family = '"Open Sans"';
                    Chart.defaults.animation.duration = 0;
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
                                tension: 0.1,
                            }
                        )
                    }


                    let ctx = document.getElementById('roundChart');

                    let LineGraph = new Chart(ctx, {
                        type: 'line',
                        data: chartdata,
                        options: {
                            maintainAspectRatio: false,
                            scales: {
                                x: {
                                    ticks: {
                                        display: true
                                    },
                                    title: {
                                        display: true,
                                        text: "Spiele"
                                    },
                                    border: {
                                        display: false
                                    }
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: "Punkte"
                                    },
                                    border: {
                                        display: false
                                    }
                                }
                            },
                            plugins: {
                                tooltip: {
                                    // intersect: false
                                },
                                legend: {
                                    labels: {
                                        boxWidth: 10,
                                        usePointStyle: true,
                                        font: {
                                            size: 13
                                        },
                                        padding: 20
                                    }
                                }
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
                        }
                    });
                })
                .catch(function (error) {
                    console.log(error);
                });

        }
    }
</script>
