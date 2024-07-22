import 'bootstrap';
import Chart from 'chart.js/auto';

const ctx = document.getElementById('dividendChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [2020, 2021, 2022, 2023, 2024],
        datasets: [{
            label: 'Dividend',
            data: [18.23, 24.52, 56.11, 78.27, 90.02],
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
