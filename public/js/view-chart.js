let ctx = document.getElementById("myChart").getContext("2d");
let myChart = new Chart(ctx, {
    type: "line", // Bisa diganti ke 'bar', 'pie', dll.
    data: {
        labels: [], // Tanggal dari database
        datasets: [
            {
                label: "Data Grafik",
                data: [], // Data angka dari database
                borderColor: "blue",
                backgroundColor: "rgba(0, 0, 255, 0.2)",
                borderWidth: 2,
            },
        ],
    },
    options: {
        responsive: true,
        scales: {
            x: { title: { display: true, text: "Tanggal" } },
            y: { title: { display: true, text: "Jumlah" } },
        },
    },
});

document.getElementById("loadData").addEventListener("click", function () {
    let kategori = document.getElementById("chart").value;
    let mulai = document.getElementById("mulai").value;
    let akhir = document.getElementById("akhir").value;

    fetch(`/getChartData?jenis=${kategori}&mulai=${mulai}&akhir=${akhir}`)
        .then((response) => response.json())
        .then((data) => {
            myChart.data.labels = data.labels; // Tanggal
            myChart.data.datasets[0].data = data.values; // Jumlah data
            myChart.update();
        })
        .catch((error) => console.error("Error:", error));
});
