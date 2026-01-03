const ctx = document.getElementById("myChart");

new Chart(ctx, {
    type: "bar",
    data: {
        labels: [
            "Jan","Feb","Mar","Apr","May","Jun",
            "Jul","Aug","Sep","Oct","Nov","Dec"
        ],
        datasets: [
            {
                label: "Line Tracing",
                data: [5,5,5,5,5,5,5,5,5,5,5,5],
                backgroundColor: "rgba(255, 255, 255, 1)",
                borderColor: "rgba(0, 0, 0, 1)",
                borderWidth: 1
            },
            {
                label: "Object to Drawing",
                data: [3,3,3,3,3,3,3,3,3,3,3,3],
                backgroundColor: "rgba(0, 0, 0, 1)",
            },
            {
                label: "Prompt to Picture",
                data: [2,2,2,2,2,2,2,2,2,2,2,2],
                backgroundColor: "rgba(150, 150, 150, 1)",
            }
        ]
    },
    options: {
        responsive: false,
        scales: {
            x: {
                stacked: false,   // 🔑 grouped bars
                grid: {
                    display: false
                }
            },
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                position: "top"
            }
        }
    }
});

