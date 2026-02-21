<!DOCTYPE html>
<html>
<body>

<?php
// Тестовый файл для проверки ошибки
echo "Тестовый файл для проверки ошибки\n";

// Копируем сюда часть кода из show.blade.php, чтобы локализовать проблему
?>

<script>
    // Date pickers for chart
    flatpickr("#startdate", {
        enableTime: false,
        dateFormat: "Y-m-d"
    });

    flatpickr("#enddate", {
        enableTime: false,
        dateFormat: "Y-m-d"
    });

    // Function to update chart data
    function updateChartData() {
        var startDate = document.getElementById('startdate').value;
        var endDate = document.getElementById('enddate').value;

        if (!startDate || !endDate) {
            alert('Пожалуйста, выберите оба периода');
            return;
        }

        // Show loading
        var chartContainer = document.querySelector('#lineMonthChart');
        chartContainer.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 200px;">Загрузка...</div>';

        fetch('{{ route('projects.chart-data', $project) }}' + '?date1=' + startDate + '&date2=' + endDate)
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                console.log('Chart data received:', data);

                // Update chartData and annotationsData variables
                chartData = data.chartData;
                annotationsData = data.annotations;
                activitiesByDate = data.activitiesByDate;
    </script>

</body>
</html>
