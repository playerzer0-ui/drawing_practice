const ctx = document.getElementById("myChart").getContext("2d");

const myChart = new Chart(ctx, {
    type: "bar",
    data: {
        labels: [
            "Jan","Feb","Mar","Apr","May","Jun",
            "Jul","Aug","Sep","Oct","Nov","Dec"
        ],
        datasets: [
            {
                label: "Line Tracing",
                data: Array(12).fill(0),
                backgroundColor: "rgba(255, 255, 255, 1)",
                borderColor: "rgba(0, 0, 0, 1)",
                borderWidth: 1
            },
            {
                label: "Object to Drawing",
                data: Array(12).fill(0),
                backgroundColor: "rgba(0, 0, 0, 1)"
            },
            {
                label: "Prompt to Picture",
                data: Array(12).fill(0),
                backgroundColor: "rgba(150, 150, 150, 1)"
            }
        ]
    },
    options: {
        responsive: false,
        scales: {
            x: {
                stacked: false,
                grid: { display: false }
            },
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            legend: { position: "top" }
        }
    }
});


$.ajax({
    type: "get",
    url: "../controller/index.php?action=get_year_task_matrix",
    dataType: "json",
    success: function (response) {

        const months = [
            "January","February","March","April","May","June",
            "July","August","September","October","November","December"
        ];

        const lineTracing = [];
        const objectToDrawing = [];
        const promptToPicture = [];

        months.forEach(month => {
            const row = response[month] || {};
            lineTracing.push(row.line_tracing ?? 0);
            objectToDrawing.push(row.object_to_drawing ?? 0);
            promptToPicture.push(row.prompt_to_picture ?? 0);
        });

        myChart.data.datasets[0].data = lineTracing;
        myChart.data.datasets[1].data = objectToDrawing;
        myChart.data.datasets[2].data = promptToPicture;

        myChart.update();
    }
});
