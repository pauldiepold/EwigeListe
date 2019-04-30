<template>
    <div id="containerDiagramRound">
        <canvas id="roundChart"></canvas>
    </div>
</template>

<script>
    import Chart from 'chart.js'
    export default {
        ready() {
            var ctx = $('#roundChart');

            var myChart = new Chart(ctx, {
                type: 'line',

                data: {
                    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                    datasets: [{
                        label: 'My First dataset',
                        backgroundColor: 'rgb(255, 99, 132)',
                        borderColor: 'rgb(255, 99, 132)',
                        data: [0, 10, 5, 2, 20, 30, 45]
                    }]
                },

                options: {}
            });
        }
    }
</script>