import 'bootstrap';
import Chart from 'chart.js/auto';
import Alpine from 'alpinejs'
import axios from "axios";

window.Alpine = Alpine

Alpine.start();

const ctx = document.getElementById('dividendChart');

const dividendChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [],
        datasets: [{
            label: 'Dividend',
            data: [],
            borderWidth: 1,
            backgroundColor: '#7D00E4',
            barThickness: 40,
            borderRadius: {
                bottomLeft: 12,
                bottomRight: 12,
                topLeft: 12,
                topRight: 12
            }
        }]
    },
    options: {
        scales: {
            x: {
                grid: {
                    display: false
                },
            },
            y: {
                border: {
                    dash: [6, 6],
                },
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                display: false,
                labels: {
                    font: {
                        size: 16,
                        family: 'AvertaStd'
                    }
                }
            }
        },
    }
});

axios.get('api/dividends').then((response) => {
    const { labels, datasets } = response.data;
    dividendChart.data.labels = labels;
    dividendChart.data.datasets[0].data = datasets.data;
    dividendChart.update();
})
