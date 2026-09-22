
var ctx = document.getElementById('statisticsChart').getContext('2d');
var statisticsChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu"],
        datasets: [{
            label: "Peminjaman Buku",
            borderColor: '#177dff',
            pointBorderColor: '#FFF',
            pointBackgroundColor: '#177dff',
            pointBorderWidth: 2,
            pointHoverRadius: 4,
            pointHoverBorderWidth: 1,
            pointRadius: 3,
            backgroundColor: 'rgba(23, 125, 255, 0.1)',
            fill: true,
            borderWidth: 2,
            data: [12, 19, 15, 25, 22, 30, 18]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        legend: { position: 'bottom' },
        scales: {
            yAxes: [{ ticks: { beginAtZero: true } }]
        }
    }
});