@extends('layout.layout')

@php
$title = 'Просмотр проекта';
$subTitle = 'Проект: ' . $project->name;
$style = '
<style>
.pages-table .bordered-table {
    table-layout: auto;
    width: 100%;
    max-width: 100%;
}
.pages-table .bordered-table th,
.pages-table .bordered-table td {
    white-space: nowrap;
}
.pages-table .bordered-table td:nth-child(3) {
    width: auto;
    min-width: 200px;
    max-width: 50%;
}
/* Для таблицы ключевых слов разрешить перенос текста */
.pages-table #keywordsTable th,
.pages-table #keywordsTable td {
    white-space: normal;
    word-wrap: break-word;
}
.pages-table #keywordsTable td:nth-child(3) {
    width: auto;
    min-width: 150px;
    max-width: 30%;
}
/* Card и body */
.pages-table .card-body {
    max-width: 100%;
}
/* Tree lines */
.tree-cell {
    position: relative;
    padding-left: 6px !important;
}
.tree-cell .tree-line {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 1px;
    background-color: #e5e7eb;
}
.tree-cell .tree-corner {
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 12px;
    height: 1px;
    background-color: #e5e7eb;
}
.page-row {
    position: relative;
}
/* Search input */
.search-input {
    width: 280px;
    padding: 8px 40px 8px 16px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
}
.search-input:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}
/* Модальное окно с деталями активности для светлой темы */
[data-theme="light"] #activityDetailModal .modal-body {
    color: #333;
}
[data-theme="light"] #activityDetailModal .modal-body .text-primary-light {
    color: #333 !important;
}
[data-theme="light"] #activityDetailModal .modal-body #activityDetailDescription {
    color: #333 !important;
}
/* Модальное окно с деталями активности для темной темы */
[data-theme="dark"] #activityDetailModal .modal-body {
    color: #e5e7eb;
}
[data-theme="dark"] #activityDetailModal .modal-body .text-primary-light {
    color: #e5e7eb !important;
}
[data-theme="dark"] #activityDetailModal .modal-body #activityDetailDescription {
    color: #e5e7eb !important;
}
/* Фон для описания задачи в темной теме */
[data-theme="dark"] #activityDetailModal .modal-body #activityDetailDescription {
    background-color: #374151 !important;
}
</style>
';
$annotationsJson = json_encode($annotations);
$script = '<script src="' . asset('assets/js/lineChartPageChart.js') . '"></script>
<script src="' . asset('assets/js/flatpickr.js') . '"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // График за месяц
        // Данные о задачах по датам
          const activitiesByDate = ' . json_encode($activitiesByDate) . ';
          const chartData = ' . json_encode($chartData) . ';
          const annotationsData = ' . $annotationsJson . ';
          console.log("ActivitiesByDate JS:", activitiesByDate);
          console.log("ChartData JS:", chartData);
          console.log("AnnotationsData JS:", annotationsData);

          // Function to hide custom tooltip
          function hideCustomTooltip() {
              console.log("hideCustomTooltip called");
              const customTooltips = document.querySelectorAll(".custom-chart-tooltip");
              console.log("Found tooltips to remove:", customTooltips.length);
              customTooltips.forEach(function(tooltip) {
                  tooltip.remove();
              });
              window.tooltipPinned = false;
              console.log("window.tooltipPinned after hide:", window.tooltipPinned);
          }

          // Переменная для отслеживания зафиксированного тултипа
          window.tooltipPinned = false;
          window.pinnedDataPointIndex = null;

          // Функция для показа кастомного тултипа
          function showCustomTooltip(e, annotation, chartData, isPinned) {
              console.log("showCustomTooltip called", {e, annotation, chartData, isPinned, isPinnedGlobal: window.tooltipPinned});
              if (isPinned === undefined) { isPinned = false; }

              // Если уже есть зафиксированный тултип и мы не пытаемся показать новый зафиксированный, выходим
              if (window.tooltipPinned && !isPinned) {
                  console.log("Already have a pinned tooltip, ignoring showCustomTooltip for unpinned");
                  return;
              }

              // Удаляем предыдущий тултип
              hideCustomTooltip();

              if (!annotation || !annotation.tasks || annotation.tasks.length === 0) {
                  console.log("No tasks to show");
                  return;
              }

              var date = chartData.categories[annotation.x];
              var visits = chartData.data[annotation.x];

              var html = \'<div class="custom-chart-tooltip" style="position: fixed; background: #fff; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); padding: 12px; min-width: 200px; max-width: 300px; z-index: 10000;">\';

              // Кнопка закрытия для зафиксированного тултипа
              if (isPinned) {
                  html += \'<button onclick="hideCustomTooltip()" style="position: absolute; top: 8px; right: 8px; background: none; border: none; cursor: pointer; font-size: 18px; color: #999; padding: 0; line-height: 1;">&times;</button>\';
              }

              html += \'<div style="font-weight: 600; color: #333; margin-bottom: 8px; border-bottom: 1px solid #eee; padding-bottom: 8px; \' + (isPinned ? \'padding-right: 24px;\' : \'\') + \'">📅 \' + date + \'</div>\';
              html += \'<div style="color: #487FFF; font-weight: 600; margin-bottom: 8px;">👥 \' + visits + \' посещений</div>\';

              html += \'<div style="border-top: 1px solid #eee; padding-top: 8px; margin-top: 8px;">\';
              html += \'<div style="font-weight: 600; color: #666; margin-bottom: 6px; font-size: 12px;">📋 Задачи (\' + annotation.tasks.length + \'):</div>\';

              annotation.tasks.forEach(function(task, idx) {
                  var colors = {
                      content: "#FF9F29",
                      links: "#28C76F",
                      technical: "#FF4560",
                      meta: "#7367F0",
                      other: "#00CFE8"
                  };
                  var color = colors[task.category] || "#9F9F9F";
                  html += \'<div class="task-item" data-task-id="\' + task.id + \'" style="display: flex; align-items: center; padding: 4px 0; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background=\\\'#f5f5f5\\\'" onmouseout="this.style.background=\\\'transparent\\\'" onclick="showActivityDetails(\' + task.id + \')">\';
                  html += \'<span style="width: 8px; height: 8px; border-radius: 50%; background: \' + color + \'; margin-right: 8px; flex-shrink: 0;"></span>\';
                  html += \'<span style="color: #333; font-size: 12px; text-decoration: underline; text-decoration-style: dotted;">\' + task.title + \'</span>\';
                  html += \'</div>\';
              });

              html += \'</div>\';
              html += \'</div>\';

              // Создаем элемент тултипа
              var tooltip = document.createElement(\'div\');
              tooltip.innerHTML = html;
              tooltip.firstElementChild.style.left = (e.clientX + 15) + \'px\';
              tooltip.firstElementChild.style.top = (e.clientY + 15) + \'px\';

              // Проверяем, чтобы тултип не выходил за границы экрана
              document.body.appendChild(tooltip.firstElementChild);

              var tooltipEl = document.querySelector(\'.custom-chart-tooltip\');
              var rect = tooltipEl.getBoundingClientRect();

              if (rect.right > window.innerWidth) {
                  tooltipEl.style.left = (window.innerWidth - rect.width - 15) + \'px\';
              }
              if (rect.bottom > window.innerHeight) {
                  tooltipEl.style.top = (window.innerHeight - rect.height - 15) + \'px\';
              }
          }

        // Формируем аннотации для вертикальных линий
        console.log("AnnotationsData for xaxis:", annotationsData);
        const xaxisAnnotations = annotationsData.map(annotation => {
            console.log("Annotation date and index before xaxis:", annotation.date, annotation.x, typeof annotation.x);
            const taskCount = annotation.tasks ? annotation.tasks.length : 0;

            // Цвет по типу задачи
            const colors = {
                content: "#FF9F29",
                links: "#28C76F",
                technical: "#FF4560",
                meta: "#7367F0",
                other: "#00CFE8"
            };

            const borderColor = taskCount > 1 ? "#FF4560" : (colors[annotation.tasks[0].category] || "#9F9F9F");

            return {
                x: annotation.x,
                borderColor: borderColor,
                borderWidth: 2,
                strokeDashArray: 0,
                opacity: 0.8,
                label: {
                    borderColor: borderColor,
                    style: {
                        color: "#fff",
                        background: borderColor,
                        fontSize: "10px",
                        fontWeight: "bold",
                        padding: { left: 5, right: 5, top: 2, bottom: 2 }
                    },
                    text: taskCount > 1 ? taskCount + " задач" : "1 задача",
                    position: "top"
                }
            };
        });
        console.log("xaxisAnnotations x values:", xaxisAnnotations.map(annotation => annotation.x));

        console.log("Number of categories on chart:", chartData.categories.length);
        console.log("Expected index for 2026-02-13:", chartData.full_dates.indexOf("2026-02-13"));

        var monthOptions = {
            series: [{
                name: "Посещения",
                data: ' . json_encode($chartData['data']) . '
            }],
            chart: {
                height: 264,
                type: "line",
                zoom: {
                    enabled: false
                },
                toolbar: {
                    show: false
                },
                events: {
                    mouseMove: function(e, chartContext, config) {
                        // Не показываем тултип при наведении, если он уже зафиксирован
                        if (window.tooltipPinned) {
                            console.log("Tooltip is pinned, ignoring mouseMove");
                            return;
                        }

                        // Показываем кастомный тултип при наведении на точку с задачами
                        if (config.dataPointIndex >= 0) {
                            const currentDate = chartData.full_dates[config.dataPointIndex];
                            const annotation = annotationsData.find(function(a) { return a.date === currentDate; });
                            if (annotation && annotation.tasks) {
                                annotation.x = config.dataPointIndex;
                                showCustomTooltip(e, annotation, chartData);
                            }
                        }
                    },
                    click: function(e, chartContext, config) {
                        console.log("Chart click event:", config);
                        if (config.dataPointIndex >= 0) {
                            const currentDate = chartData.full_dates[config.dataPointIndex];
                            const annotation = annotationsData.find(function(a) { return a.date === currentDate; });
                            console.log("Found annotation:", annotation);
                            if (annotation && annotation.tasks && annotation.tasks.length > 0) {
                                // Удаляем предыдущий зафиксированный тултип
                                if (window.tooltipPinned) {
                                    hideCustomTooltip();
                                }
                                // Устанавливаем флаг фиксации ДО показа тултипа, чтобы tooltip.custom уже видал, что тултип зафиксирован
                                window.tooltipPinned = true;
                                window.pinnedDataPointIndex = config.dataPointIndex;
                                // Передаем правильный индекс для аннотации
                                annotation.x = config.dataPointIndex;
                                showCustomTooltip(e, annotation, chartData, true);
                                console.log("Tooltip is now pinned:", window.tooltipPinned);
                            }
                        }
                    }
                }
            },
            colors: ["#487FFF"],
            dataLabels: {
                enabled: true
            },
            xaxis: {
                categories: ' . json_encode($chartData['categories']) . ',
                tickAmount: ' . count($chartData['categories']) . ',
                labels: {
                    rotate: 0,
                    style: {
                        fontSize: "12px"
                    }
                }
            },
            yaxis: {
                title: {
                    text: "Посещения"
                },
                min: 0
            },
            stroke: {
                width: 3
            },
            markers: {
                size: 4,
                colors: ["#487FFF"],
                hover: {
                    size: 6
                }
            },
            grid: {
                strokeDashArray: 4
            },
            tooltip: {
                enabled: true,
                custom: function(opts) {
                    // Если есть зафиксированный тултип, не показываем стандартный
                    if (window.tooltipPinned) {
                        console.log("Tooltip is pinned, returning empty string from custom tooltip");
                        return \'\';
                    }

                    var series = opts.series;
                    var seriesIndex = opts.seriesIndex;
                    var dataPointIndex = opts.dataPointIndex;
                    var w = opts.w;

                    // Ищем аннотацию по индексу даты в full_dates
                    var currentDate = chartData.full_dates[dataPointIndex];
                    var annotation = annotationsData.find(function(a) { return a.date === currentDate; });
                    var visits = series[seriesIndex][dataPointIndex];
                    var date = chartData.categories[dataPointIndex];

                    var html = \'<div class="custom-chart-tooltip" style="background: #fff; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); padding: 12px; min-width: 200px; max-width: 300px;">\';
                    html += \'<div style="font-weight: 600; color: #333; margin-bottom: 8px; border-bottom: 1px solid #eee; padding-bottom: 8px;">📅 \' + date + \'</div>\';
                    html += \'<div style="color: #487FFF; font-weight: 600; margin-bottom: 8px;">👥 \' + visits + \' посещений</div>\';

                    if (annotation && annotation.tasks && annotation.tasks.length > 0) {
                        html += \'<div style="border-top: 1px solid #eee; padding-top: 8px; margin-top: 8px;">\';
                        html += \'<div style="font-weight: 600; color: #666; margin-bottom: 6px; font-size: 12px;">📋 Задачи (\' + annotation.tasks.length + \'):</div>\';
                        annotation.tasks.forEach(function(task, idx) {
                            var colors = {
                                content: "#FF9F29",
                                links: "#28C76F",
                                technical: "#FF4560",
                                meta: "#7367F0",
                                other: "#00CFE8"
                            };
                            var color = colors[task.category] || "#9F9F9F";
                            html += \'<div class="task-item" data-task-id="\' + task.id + \'" style="display: flex; align-items: center; padding: 4px 0; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background=\\\'#f5f5f5\\\'" onmouseout="this.style.background=\\\'transparent\\\'" onclick="showActivityDetails(\' + task.id + \')">\';
                            html += \'<span style="width: 8px; height: 8px; border-radius: 50%; background: \' + color + \'; margin-right: 8px; flex-shrink: 0;"></span>\';
                            html += \'<span style="color: #333; font-size: 12px; text-decoration: underline; text-decoration-style: dotted;">\' + task.title + \'</span>\';
                            html += \'</div>\';
                        });
                        html += \'</div>\';
                    }

                    html += \'</div>\';
                    return html;
                }
            },
            annotations: {
                xaxis: xaxisAnnotations
            }
        };

        // Обработчик клика по задачам в тултипе


            // Обработчик клика по задачам в тултипе (с делегацией для динамически созданных элементов)
            // Обработчик клика по задачам в тултипе
            document.addEventListener("click", function(e) {
                const taskItem = e.target.closest(".task-item");
                if (taskItem && document.querySelector(".custom-chart-tooltip")?.contains(taskItem)) {
                    const taskId = parseInt(taskItem.dataset.taskId);
                    if (taskId) {
                        showActivityDetails(taskId);
                    }
                }
            });

            // Предотвращаем скрытие тултипа при наведении на него
            document.addEventListener("mouseover", function(e) {
                if (e.target.closest(".custom-chart-tooltip")) {
                    window.tooltipHovered = true;
                }
            });

            document.addEventListener("mouseout", function(e) {
                if (e.target.closest(".custom-chart-tooltip")) {
                    window.tooltipHovered = false;
                    // Скрываем тултип с задержкой только если он не зафиксирован
                    if (!window.tooltipPinned) {
                        setTimeout(() => {
                            if (!window.tooltipHovered) {
                                hideCustomTooltip();
                            }
                        }, 500);
                    }
                }
            });

            // Клик вне тултипа снимает фиксацию
            document.addEventListener("click", function(e) {
                if (window.tooltipPinned && !e.target.closest(".custom-chart-tooltip") && !e.target.closest(".apexcharts-canvas")) {
                    hideCustomTooltip();
                }
            });

        console.log("Number of data points:", chartData.data.length);
        console.log("Number of annotations:", xaxisAnnotations.length);
        console.log("xaxisAnnotations:", xaxisAnnotations);
        var monthChart = new ApexCharts(document.querySelector("#lineMonthChart"), monthOptions);
        monthChart.render();

        // График за год
        var yearOptions = {
            series: [{
                name: "Посещения",
                data: ' . json_encode($yearlyChartData['data']) . '
            }],
            chart: {
                height: 264,
                type: "line",
                zoom: {
                    enabled: false
                },
                toolbar: {
                    show: false
                },
            },
            colors: ["#28C76F"],
            dataLabels: {
                enabled: true
            },
            xaxis: {
                categories: ' . json_encode($yearlyChartData['categories']) . ',
                tickAmount: ' . count($yearlyChartData['categories']) . ',
                labels: {
                    rotate: 0,
                    style: {
                        fontSize: "12px"
                    }
                }
            },
            yaxis: {
                title: {
                    text: "Посещения"
                },
            },
            stroke: {
                width: 3
            },
            markers: {
                size: 5,
                colors: ["#28C76F"]
            },
            grid: {
                strokeDashArray: 4
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " посещений"
                    }
                }
            }
        };
        var yearChart = new ApexCharts(document.querySelector("#lineYearChart"), yearOptions);
        yearChart.render();
    });
</script>
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
        var startDate = document.getElementById(\'startdate\').value;
        var endDate = document.getElementById(\'enddate\').value;

        if (!startDate || !endDate) {
            alert(\'Please select both dates\');
            return;
        }

        // Show loading
        var chartContainer = document.querySelector(\'#lineMonthChart\');
        chartContainer.innerHTML = \'<div style="display: flex; align-items: center; justify-content: center; height: 200px;">Loading...</div>\';

        fetch(\'' . route('projects.chart-data', $project) . '\' + \'?date1=\' + startDate + \'&date2=\' + endDate)
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                console.log(\'Chart data received:\', data);

                // Update chartData and annotationsData variables
                chartData = data.chartData;
                annotationsData = data.annotations;
                activitiesByDate = data.activitiesByDate;

                // Destroy old chart
                if (typeof monthChart !== \'undefined\') {
                    monthChart.destroy();
                }

                // Формируем аннотации для вертикальных линий
                const xaxisAnnotations = annotationsData.map(annotation => {
                    const taskCount = annotation.tasks ? annotation.tasks.length : 0;

                    const colors = {
                        content: "#FF9F29",
                        links: "#28C76F",
                        technical: "#FF4560",
                        meta: "#7367F0",
                        other: "#00CFE8"
                    };

                    const borderColor = taskCount > 1 ? "#FF4560" : (colors[annotation.tasks[0].category] || "#9F9F9F");

                    return {
                        x: annotation.x,
                        borderColor: borderColor,
                        borderWidth: 2,
                        strokeDashArray: 0,
                        opacity: 0.8,
                        label: {
                            borderColor: borderColor,
                            style: {
                                color: "#fff",
                                background: borderColor,
                                fontSize: "10px",
                                fontWeight: "bold",
                                padding: { left: 5, right: 5, top: 2, bottom: 2 }
                            },
                            text: taskCount > 1 ? taskCount + " задач" : "1 задача",
                            position: "top"
                        }
                    };
                });

                // Create new chart options
                const newOptions = {
                    series: [{
                        name: "Посещения",
                        data: chartData.data
                    }],
                    chart: {
                        height: 264,
                        type: "line",
                        zoom: {
                            enabled: false
                        },
                        toolbar: {
                            show: false
                        },
                        events: {
                            mouseMove: function(e, chartContext, config) {
                                if (window.tooltipPinned) {
                                    return;
                                }

                                if (config.dataPointIndex >= 0) {
                                    const currentDate = chartData.full_dates[config.dataPointIndex];
                                    const annotation = annotationsData.find(function(a) { return a.date === currentDate; });
                                    if (annotation && annotation.tasks) {
                                        annotation.x = config.dataPointIndex;
                                        showCustomTooltip(e, annotation, chartData);
                                    }
                                }
                            },
                            click: function(e, chartContext, config) {
                                console.log("Chart click event:", config);
                                if (config.dataPointIndex >= 0) {
                                    const currentDate = chartData.full_dates[config.dataPointIndex];
                                    const annotation = annotationsData.find(function(a) { return a.date === currentDate; });
                                    console.log("Found annotation:", annotation);
                                    if (annotation && annotation.tasks && annotation.tasks.length > 0) {
                                        if (window.tooltipPinned) {
                                            hideCustomTooltip();
                                        }
                                        window.tooltipPinned = true;
                                        window.pinnedDataPointIndex = config.dataPointIndex;
                                        annotation.x = config.dataPointIndex;
                                        showCustomTooltip(e, annotation, chartData, true);
                                        console.log("Tooltip is now pinned:", window.tooltipPinned);
                                    }
                                }
                            }
                        }
                    },
                    colors: ["#487FFF"],
                    dataLabels: {
                        enabled: true
                    },
                    xaxis: {
                        categories: chartData.categories,
                        tickAmount: chartData.categories.length,
                        labels: {
                            rotate: 0,
                            style: {
                                fontSize: "12px"
                            }
                        }
                    },
                    yaxis: {
                        title: {
                            text: "Посещения"
                        },
                        min: 0
                    },
                    stroke: {
                        width: 3
                    },
                    markers: {
                        size: 4,
                        colors: ["#487FFF"],
                        hover: {
                            size: 6
                        }
                    },
                    grid: {
                        strokeDashArray: 4
                    },
                    tooltip: {
                        enabled: true,
                        custom: function(opts) {
                            if (window.tooltipPinned) {
                                return \'\';
                            }

                            var series = opts.series;
                            var seriesIndex = opts.seriesIndex;
                            var dataPointIndex = opts.dataPointIndex;

                            var currentDate = chartData.full_dates[dataPointIndex];
                            var annotation = annotationsData.find(function(a) { return a.date === currentDate; });
                            var visits = series[seriesIndex][dataPointIndex];
                            var date = chartData.categories[dataPointIndex];

                            var html = \'<div class="custom-chart-tooltip" style="background: #fff; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); padding: 12px; min-width: 200px; max-width: 300px;">\';
                            html += \'<div style="font-weight: 600; color: #333; margin-bottom: 8px; border-bottom: 1px solid #eee; padding-bottom: 8px;">📅 \' + date + \'</div>\';
                            html += \'<div style="color: #487FFF; font-weight: 600; margin-bottom: 8px;">👥 \' + visits + \' посещений</div>\';

                            if (annotation && annotation.tasks && annotation.tasks.length > 0) {
                                html += \'<div style="border-top: 1px solid #eee; padding-top: 8px; margin-top: 8px;">\';
                                html += \'<div style="font-weight: 600; color: #666; margin-bottom: 6px; font-size: 12px;">📋 Задачи (\' + annotation.tasks.length + \'):</div>\';
                                annotation.tasks.forEach(function(task, idx) {
                                    var colors = {
                                        content: "#FF9F29",
                                        links: "#28C76F",
                                        technical: "#FF4560",
                                        meta: "#7367F0",
                                        other: "#00CFE8"
                                    };
                                    var color = colors[task.category] || "#9F9F9F";
                                    html += \'<div class="task-item" data-task-id="\' + task.id + \'" style="display: flex; align-items: center; padding: 4px 0; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background=\\\'#f5f5f5\\\'" onmouseout="this.style.background=\\\'transparent\\\'" onclick="showActivityDetails(\' + task.id + \')">\';
                                    html += \'<span style="width: 8px; height: 8px; border-radius: 50%; background: \' + color + \'; margin-right: 8px; flex-shrink: 0;"></span>\';
                                    html += \'<span style="color: #333; font-size: 12px; text-decoration: underline; text-decoration-style: dotted;">\' + task.title + \'</span>\';
                                    html += \'</div>\';
                                });
                                html += \'</div>\';
                            }

                            html += \'</div>\';
                            return html;
                        }
                    },
                    annotations: {
                        xaxis: xaxisAnnotations
                    }
                };

                // Render new chart
                monthChart = new ApexCharts(document.querySelector("#lineMonthChart"), newOptions);
                monthChart.render();

                console.log("Chart updated successfully");
            })
            .catch(error => {
                console.error(\'Error updating chart:\', error);
                const chartContainer = document.querySelector(\'#lineMonthChart\');
                chartContainer.innerHTML = \'<div style="display: flex; align-items: center; justify-content: center; height: 200px; color: red;">Ошибка при загрузке данных</div>\';
            });
    }
</script>
<script>
    // Инициализация DataTable для ключевых слов
    var keywordsTable;
    if (typeof DataTable !== "undefined") {
        var savedKeywordsPageLength = localStorage.getItem("keywordsTableLength");
        var initialKeywordsPageLength = savedKeywordsPageLength ? parseInt(savedKeywordsPageLength) : 10;

        keywordsTable = new DataTable("#keywordsTable", {
            paging: true,
            lengthChange: true,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Все"]],
            pageLength: initialKeywordsPageLength,
            ordering: true,
            info: true,
            searching: true,
            columnDefs: [
                { targets: [0], width: "60px" },
                { targets: [1], width: "50px" },
                { targets: [4], width: "90px" },
                { targets: [5], width: "80px" },
                { targets: [6], width: "80px" },
                { targets: [7], width: "80px" },
                { targets: [8], width: "80px" },
                { targets: [9], width: "80px" },
                { targets: [10], width: "100px" },
                { targets: [11], width: "140px" }
            ]
        });

        keywordsTable.on("length.dt", function(e, settings, len) {
            localStorage.setItem("keywordsTableLength", len);
        });
    }
</script>
<script>
// Функция для сворачивания/разворачивания дочерних элементов
function toggleChildren(parentId) {
    const rows = document.querySelectorAll(`[data-parent-id="${parentId}"]`);
    const toggleBtn = document.getElementById(`toggle-${parentId}`);
    const isExpanded = toggleBtn.getAttribute("data-expanded") === "true";

    rows.forEach(row => {
        if (isExpanded) {
            row.style.display = "none";
            // Скрываем всех потомков рекурсивно
            const childId = row.getAttribute("data-page-id");
            if (childId) {
                hideAllChildren(childId);
            }
        } else {
            row.style.display = "table-row";
        }
    });

    toggleBtn.setAttribute("data-expanded", !isExpanded);
    toggleBtn.innerHTML = isExpanded ? `<iconify-icon icon="uil:plus"></iconify-icon>` : `<iconify-icon icon="uil:minus"></iconify-icon>`;
}

function hideAllChildren(parentId) {
    const rows = document.querySelectorAll(`[data-parent-id="${parentId}"]`);
    rows.forEach(row => {
        row.style.display = "none";
        const toggleBtn = document.getElementById(`toggle-${parentId}`);
        if (toggleBtn) {
            toggleBtn.setAttribute("data-expanded", "false");
            toggleBtn.innerHTML = `<iconify-icon icon="uil:plus"></iconify-icon>`;
        }
        const childId = row.getAttribute("data-page-id");
        if (childId) {
            hideAllChildren(childId);
        }
    });
}

       // Инициализация DataTable
      var table;
      if (typeof DataTable !== "undefined") {
          table = new DataTable("#dataTable", {
              paging: false, // Отключаем пагинацию для дерева
              lengthChange: false, // Отключаем выбор количества строк
              ordering: true,
              info: false, // Отключаем информацию о количестве записей
              searching: true,
              columnDefs: [
                  { targets: [0], width: "50px" },
                  { targets: [1], width: "60px" },
                  { targets: [3], width: "100px" },
                  { targets: [4], width: "90px" },
                  { targets: [5], width: "100px" },
                  { targets: [6], width: "110px" },
                  { targets: [7], width: "80px" },
                  { targets: [8], width: "140px" }
              ]
          });
      }

    // Настройки отображения столбцов — генерируются автоматически из заголовков таблицы
    let columnSettings = {};

    // Столбцы, которые всегда должны быть видимы (нельзя скрыть)
    const alwaysVisibleColumns = [0, 2, 8]; // Первая (пустая), URL (3-я), Действия (последняя)

    // Инициализация настроек столбцов из заголовков таблицы
    function initColumnSettings() {
        const headers = document.querySelectorAll("#dataTable thead th");
        columnSettings = {};

        headers.forEach((th, index) => {
            const key = "col" + index;
            const text = th.textContent.trim() || (index === 0 ? "Развернуть" : "Столбец " + index);
            const isAlwaysVisible = alwaysVisibleColumns.includes(index);

            columnSettings[key] = {
                index: index,
                key: key,
                title: text,
                default: true,
                alwaysVisible: isAlwaysVisible
            };
        });
    }

    // Загрузка настроек из localStorage
    function loadColumnSettings() {
        const saved = localStorage.getItem("pagesTableColumns_" + "{{ $project->id }}");
        if (saved) {
            try {
                const settings = JSON.parse(saved);
                Object.keys(columnSettings).forEach(key => {
                    if (settings.hasOwnProperty(key)) {
                        columnSettings[key].visible = settings[key];
                    } else {
                        columnSettings[key].visible = columnSettings[key].default;
                    }
                    // Принудительно показываем столбцы, которые всегда должны быть видимы
                    if (columnSettings[key].alwaysVisible) {
                        columnSettings[key].visible = true;
                    }
                });
            } catch (e) {
                // Если ошибка парсинга, используем значения по умолчанию
                Object.keys(columnSettings).forEach(key => {
                    columnSettings[key].visible = columnSettings[key].default;
                });
            }
        } else {
            Object.keys(columnSettings).forEach(key => {
                columnSettings[key].visible = columnSettings[key].default;
            });
        }
    }

    // Сохранение настроек в localStorage
    function saveColumnSettings() {
        const settings = {};
        Object.keys(columnSettings).forEach(key => {
            settings[key] = columnSettings[key].visible;
        });
        localStorage.setItem("pagesTableColumns_" + "{{ $project->id }}", JSON.stringify(settings));
    }

    // Применение настроек к таблице
    function applyColumnSettings() {
        Object.keys(columnSettings).forEach(key => {
            const col = columnSettings[key];

            // Скрываем/показываем столбцы с помощью DataTable
            if (table && table.columns) {
                if (col.visible) {
                    table.columns(col.index).visible(true);
                } else {
                    table.columns(col.index).visible(false);
                }
            }
        });

        // Обновляем состояние чекбоксов в меню
        Object.keys(columnSettings).forEach(key => {
            const checkbox = document.getElementById("setting-" + key);
            if (checkbox) {
                checkbox.checked = columnSettings[key].visible;
            }
        });
    }

    // Обработчик изменения чекбокса
    function toggleColumn(key) {
        if (columnSettings[key] && !columnSettings[key].alwaysVisible) {
            columnSettings[key].visible = !columnSettings[key].visible;
            saveColumnSettings();
            applyColumnSettings();
        }
    }

// Генерация dropdown меню настроек
function generateSettingsMenu() {
    const container = document.getElementById("columnSettingsContainer");
    if (!container) return;

    container.innerHTML = "";

    Object.keys(columnSettings).forEach(key => {
        const col = columnSettings[key];
        const div = document.createElement("div");
        div.className = "form-check style-check d-flex align-items-center justify-content-between mb-16";

        const label = document.createElement("label");
        label.className = "form-check-label line-height-1 fw-medium text-secondary-light";
        label.htmlFor = "setting-" + key;
        label.innerHTML = \'<span class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">\' +
            \'<span class="w-36-px flex-shrink-0"></span>\' +
            \'<span class="text-md fw-semibold mb-0">\' + col.title + \'</span>\' +
            \'</span>\';
        if (col.alwaysVisible) {
            label.style.opacity = "0.6";
        }

        const input = document.createElement("input");
        input.className = "form-check-input";
        input.type = "checkbox";
        input.id = "setting-" + key;
        input.checked = col.visible;
        input.disabled = col.alwaysVisible;
        input.onchange = function() {
            toggleColumn(key);
        };

        div.appendChild(label);
        div.appendChild(input);
        container.appendChild(div);
    });
}

// Инициализация при загрузке
document.addEventListener("DOMContentLoaded", function() {
    initColumnSettings();
    loadColumnSettings();
    generateSettingsMenu();
    applyColumnSettings();

    // Отладка
    console.log("Column settings initialized:", columnSettings);
});
</script>';
@endphp

<script>
    // Массив всех активностей для быстрого доступа
    const activitiesData = @json($activities);

    // Функция для отображения деталей активности в модальном окне
    function showActivityDetails(activityId) {
        const activity = activitiesData.find(item => item.id === activityId);

        if (!activity) {
            alert('Активность не найдена');
            return;
        }

        // Категория задачи для цветной метки
        const categoryClass = {
            'content': 'bg-warning',
            'links': 'bg-success',
            'technical': 'bg-danger',
            'meta': 'bg-primary',
            'other': 'bg-info'
        }[activity.category] || 'bg-secondary';

        const categoryLabel = {
            'content': 'Контент',
            'links': 'Ссылки',
            'technical': 'Техническое',
            'meta': 'Мета-теги',
            'other': 'Другое'
        }[activity.category] || activity.category;

        // Заполняем модальное окно
        document.getElementById('activityDetailTitle').textContent = activity.title;
        document.getElementById('activityDetailDate').textContent = activity.formatted_date;
        document.getElementById('activityDetailCategory').textContent = categoryLabel;
        document.getElementById('activityDetailCategory').className = `badge ${categoryClass}`;
        document.getElementById('activityDetailDescription').innerHTML = activity.description ? activity.description.replace(/\n/g, '<br>') : '<span class="text-secondary">Нет описания</span>';
        // Убеждаемся, что текст описания имеет контрастный цвет
        document.getElementById('activityDetailDescription').style.color = '#333';
        document.getElementById('activityDetailEditBtn').href = `/projects/{{ $project->id }}/activities/${activity.id}/edit`;

        // Показываем модальное окно
        const modal = new bootstrap.Modal(document.getElementById('activityDetailModal'));
        modal.show();
    }

    // Функция для показа подсказки при наведении на аннотацию
    function showAnnotationTooltip(tooltipText) {
        // Создаем элемент для подсказки
        const tooltip = document.createElement('div');
        tooltip.className = 'annotation-tooltip';
        tooltip.innerHTML = tooltipText.replace(/\n/g, '<br>');
        tooltip.style.cssText = `
            position: absolute;
            background: #333;
            color: #fff;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 12px;
            white-space: pre-wrap;
            z-index: 1000;
            pointer-events: none;
            max-width: 200px;
        `;

        document.body.appendChild(tooltip);

        // Позиционируем подсказку рядом с курсором
        document.addEventListener('mousemove', (e) => {
            tooltip.style.left = e.pageX + 10 + 'px';
            tooltip.style.top = e.pageY + 10 + 'px';
        });

        return tooltip;
    }

    // Функция для скрытия подсказки
    function hideAnnotationTooltip(tooltip) {
        if (tooltip) {
            document.body.removeChild(tooltip);
        }
    }
</script>

    <!-- Modal для показа задач -->
    <div class="modal fade" id="tasksModal" tabindex="-1" aria-labelledby="tasksModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tasksModalLabel">Задачи за дату</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul id="tasksList" class="list-unstyled"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal для деталей задачи -->
    <div class="modal fade" id="activityDetailModal" tabindex="-1" aria-labelledby="activityDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="activityDetailTitle">Детали задачи</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-16">
                        <span class="text-secondary-light text-sm">Дата: </span>
                        <span class="text-primary-light" id="activityDetailDate"></span>
                    </div>
                    <div class="mb-16">
                        <span class="text-secondary-light text-sm">Категория: </span>
                        <span class="badge" id="activityDetailCategory"></span>
                    </div>
                    <div class="mb-16">
                        <span class="text-secondary-light text-sm">Описание:</span>
                        <div class="text-primary-light mt-8 p-12 bg-light rounded" id="activityDetailDescription"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <a href="#" class="btn btn-primary" id="activityDetailEditBtn">Редактировать</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Функция для показа модалки с задачами -->
    <script>
        function showTasksModal(tasks) {
            const tasksList = document.getElementById('tasksList');
            tasksList.innerHTML = '';

            tasks.forEach(task => {
                const li = document.createElement('li');
                li.className = 'd-flex align-items-start gap-3 mb-24 p-16 border-bottom';

                // Категория задачи для цветной метки
                const categoryClass = {
                    'content': 'bg-primary',
                    'links': 'bg-success',
                    'technical': 'bg-danger',
                    'meta': 'bg-warning',
                    'other': 'bg-info'
                }[task.category] || 'bg-secondary';

                // Получаем полные данные задачи из глобального массива
                const fullTask = activitiesData.find(item => item.id === task.id);

                li.innerHTML = `
                    <span class="w-8-px h-8-px ${categoryClass} rounded-circle mt-2"></span>
                    <div class="flex-grow-1">
                        <h6 class="fw-semibold mb-0">${task.title}</h6>
                        <span class="text-sm text-secondary-light">${task.formatted_date}</span>
                        <span class="badge ${categoryClass} text-sm ms-2">${task.category}</span>
                        ${fullTask?.description ? `
                            <p class="text-sm text-primary-light mt-8">${fullTask.description}</p>
                        ` : ''}
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-info" onclick="showActivityDetails(${task.id})" title="Просмотреть детали">
                            <i class="bi bi-eye"></i>
                        </button>
                        <a href="{{ route('projects.activities.edit', [$project, ':id']) }}".replace(':id', task.id) class="btn btn-sm btn-success" title="Редактировать">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </div>
                `.replace(':id', task.id);

                tasksList.appendChild(li);
            });

            const modal = new bootstrap.Modal(document.getElementById('tasksModal'));
            modal.show();
        }
    </script>

@section('content')

<div class="card basic-data-table pages-table">
    <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
        <div class="d-flex align-items-center flex-wrap gap-3">
            <h5 class="card-title mb-0">Страницы сайта: {{ $project->name }}</h5>
        </div>
         <div class="d-flex align-items-center flex-wrap gap-3">
            <!-- Кнопка настроек столбцов -->
            <div class="dropdown">
                <button class="has-indicator w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center" type="button" id="tableSettingsDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Настройки столбцов">
                    <iconify-icon icon="heroicons:cog-6-tooth" class="text-primary-light text-xl"></iconify-icon>
                </button>
                <div class="dropdown-menu to-top dropdown-menu-sm p-0" aria-labelledby="tableSettingsDropdown" id="tableSettingsMenu">
                    <div class="m-16 py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                        <div>
                            <h6 class="text-lg text-primary-light fw-semibold mb-0">Показывать столбцы</h6>
                        </div>
                    </div>
                    <div class="max-h-400-px overflow-y-auto scroll-sm pe-8" id="columnSettingsContainer">
                        <!-- Столбцы генерируются автоматически -->
                    </div>
                </div>
            </div>
            <a href="{{ route('projects.pages.index', $project) }}" class="btn btn-info">Все страницы</a>
            <a href="{{ route('projects.pages.create', $project) }}" class="btn btn-primary">Добавить страницу</a>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importPagesModal">
                Импорт страниц
            </button>
            <a href="{{ route('projects.index') }}" class="btn btn-secondary">Назад к списку</a>
        </div>
    </div>
    <div class="card-body p-24">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($pages->count() > 0)
            <div class="table-responsive">
                <table class="table bordered-table mb-0 w-100" id="dataTable">
                <thead>
                    <tr>
                        <th scope="col" style="width: 50px;"></th>
                        <th scope="col" style="width: 60px;">ID</th>
                        <th scope="col">URL / Заголовок</th>
                        <th scope="col" style="width: 100px;">Тип</th>
                        <th scope="col" style="width: 90px;">Входящие</th>
                        <th scope="col" style="width: 100px;">Статус</th>
                        <th scope="col" style="width: 110px;">Индексация</th>
                        <th scope="col" style="width: 80px;">Уровень</th>
                        <th scope="col" style="width: 140px;">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pages->where('parent_id', null) as $rootPage)
                        @include('projects._page_row', ['page' => $rootPage, 'project' => $project, 'pages' => $pages])
                    @endforeach
                </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-20">
                <p class="text-gray-500">Страницы не найдены. <a href="{{ route('projects.pages.create', $project) }}">Добавить первую страницу</a>.</p>
            </div>
        @endif
    </div>
</div>

<!-- Tasks Block -->
<div class="row gy-4 mt-32">
    <div class="col-xxl-5 col-lg-5">
        <div class="card h-100 p-0">
            <div class="card-body p-24">
                <button type="button" class="btn btn-primary text-sm btn-sm px-12 py-12 w-100 radius-8 d-flex align-items-center gap-2 mb-32" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    <iconify-icon icon="fa6-regular:square-plus" class="icon text-lg line-height-1"></iconify-icon>
                    Add Task
                </button>

                <div class="mt-32">
                    @foreach($activities as $activity)
                        <div class="event-item d-flex align-items-center justify-content-between gap-4 pb-16 mb-16 border border-start-0 border-end-0 border-top-0">
                            <div class="">
                                <div class="d-flex align-items-center gap-10">
                                    <span class="w-12-px h-12-px bg-warning-600 rounded-circle fw-medium"></span>
                                    <span class="text-secondary-light">{{ $activity->event_date->format('d.m.Y H:i') }}</span>
                                </div>
                                <span class="text-primary-light fw-semibold text-md mt-4">{{ $activity->title }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="w-32-px h-32-px bg-primary-light text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center" onclick="showActivityDetails({{ $activity->id }})">
                                    <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
                                </button>
                                <a href="{{ route('projects.activities.edit', [$project, $activity]) }}" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center">
                                    <iconify-icon icon="lucide:edit"></iconify-icon>
                                </a>
                                <form action="{{ route('projects.activities.destroy', [$project, $activity]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this task?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center border-0">
                                        <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>

    <!-- Right Block from Chat -->
    <div class="col-xxl-7 col-lg-7">
        <div class="chat-main card h-100">
            <div class="chat-message-list">
                <!-- Пустая область для текста -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Task -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16 bg-base">
            <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Add New Task</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24">
                <form action="{{ route('projects.activities.store', $project) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-12 mb-20">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Task Title : </label>
                            <input type="text" class="form-control radius-8" name="title" placeholder="Enter Task Title ">
                        </div>
                        <div class="col-md-6 mb-20">
                            <label for="startDate" class="form-label fw-semibold text-primary-light text-sm mb-8">Start Date</label>
                            <div class=" position-relative">
                                <input class="form-control radius-8 bg-base" id="startDate" type="text" name="event_date" placeholder="03/12/2024, 10:30 AM">
                                <span class="position-absolute end-0 top-50 translate-middle-y me-12 line-height-1">
                                    <iconify-icon icon="solar:calendar-linear" class="icon text-lg"></iconify-icon>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-20">
                            <label for="endDate" class="form-label fw-semibold text-primary-light text-sm mb-8">Category</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">Select Category</option>
                                <option value="content">Content</option>
                                <option value="links">Links</option>
                                <option value="technical">Technical</option>
                                <option value="meta">Meta Tags</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-12 mb-20">
                            <label for="desc" class="form-label fw-semibold text-primary-light text-sm mb-8">Description</label>
                            <textarea class="form-control" id="desc" name="description" rows="4" cols="50" placeholder="Write some text"></textarea>
                        </div>

                        <div class="d-flex align-items-center justify-content-center gap-3 mt-24">
                            <button type="reset" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-40 py-11 radius-8">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary border border-primary-600 text-md px-24 py-12 radius-8">
                                Save
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



    <!-- Ключевые слова -->
    <div class="card basic-data-table h-100 p-0 radius-12 mt-32">
        <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
            <div class="d-flex align-items-center flex-wrap gap-3">
                <h4 class="mb-0">Ключевые слова проекта: {{ $project->name }}</h4>
            </div>

        </div>
        <div class="card-body p-24">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($keywords->count() > 0)
                <table class="table bordered-table mb-0 w-100" id="keywordsTable">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 60px;"></th>
                                <th scope="col">ID</th>
                                <th scope="col">Ключевое слово</th>
                                <th scope="col">Страница</th>
                                <th scope="col">Основное</th>
                                <th scope="col">Частота</th>
                                <th scope="col">CPC</th>
                                <th scope="col">Сложность</th>
                                <th scope="col">Позиция</th>
                                <th scope="col">Тренд</th>
                                <th scope="col">Регион</th>
                                <th scope="col">Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($keywords as $keyword)
                            <tr>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn px-18 py-11 text-primary-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <iconify-icon icon="entypo:dots-three-vertical" class="menu-icon"></iconify-icon>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900" href="{{ route('projects.pages.keywords.show', [$project, $keyword->page, $keyword]) }}">Просмотр</a></li>
                                            <li><a class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900" href="{{ route('projects.pages.keywords.edit', [$project, $keyword->page, $keyword]) }}">Редактирование</a></li>
                                            <li>
                                                <form action="{{ route('projects.pages.keywords.destroy', [$project, $keyword->page, $keyword]) }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить это ключевое слово?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 w-100 text-left">Удаление</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                                <td>{{ $keyword->id }}</td>
                                <td>{{ $keyword->keyword }}</td>
                                <td><a href="{{ route('projects.pages.show', [$project, $keyword->page]) }}" class="text-primary-600">{{ Str::limit($keyword->page->url, 40) }}</a></td>
                                <td>
                                    @if($keyword->is_main)
                                        <span class="badge bg-success">Да</span>
                                    @else
                                        <span class="badge bg-secondary">Нет</span>
                                    @endif
                                </td>
                                <td>{{ $keyword->volume ?: '-' }}</td>
                                <td>{{ $keyword->cpc ? number_format($keyword->cpc, 2) . ' ₽' : '-' }}</td>
                                <td>{{ $keyword->difficulty ?: '-' }}</td>
                                <td>{{ $keyword->current_position ?: '-' }}</td>
                                <td>{{ $keyword->trend ?: '-' }}</td>
                                <td>{{ $keyword->region ?: '-' }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ route('projects.pages.keywords.show', [$project, $keyword->page, $keyword]) }}" class="w-32-px h-32-px bg-info-focus text-info-600 rounded-circle d-flex justify-content-center align-items-center">
                                            <iconify-icon icon="uil:eye"></iconify-icon>
                                        </a>
                                        <a href="{{ route('projects.pages.keywords.edit', [$project, $keyword->page, $keyword]) }}" class="w-32-px h-32-px bg-success-focus text-success-600 rounded-circle d-flex justify-content-center align-items-center">
                                            <iconify-icon icon="uil:edit"></iconify-icon>
                                        </a>
                                        <form action="{{ route('projects.pages.keywords.destroy', [$project, $keyword->page, $keyword]) }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить это ключевое слово?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-32-px h-32-px bg-danger-focus text-danger-600 rounded-circle d-flex justify-content-center align-items-center">
                                                <iconify-icon icon="uil:trash-alt"></iconify-icon>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
            @else
                <div class="text-center py-20">
                    <p class="text-gray-500">Ключевые слова не найдены. Добавьте ключевые слова на страницы проекта.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Линейный график за месяц -->
    <div class="card h-100 p-0 radius-12 mt-32">
        <div class="card-header border-bottom bg-base py-16 px-24">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <h6 class="text-lg fw-semibold mb-0">Посещения за период</h6>
                <div class="d-flex gap-2">
                    <input type="text" id="startdate" class="form-control radius-8 bg-base" placeholder="Начальная дата" style="width: 150px;">
                    <input type="text" id="enddate" class="form-control radius-8 bg-base" placeholder="Конечная дата" style="width: 150px;">
                    <button type="button" class="btn btn-primary text-sm px-12 py-11 radius-8" onclick="updateChartData()">
                        Обновить
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-24">
            <div id="lineMonthChart"></div>
        </div>
    </div>

    <!-- Линейный график за год -->
    <div class="card h-100 p-0 radius-12 mt-32">
        <div class="card-header border-bottom bg-base py-16 px-24">
            <h6 class="text-lg fw-semibold mb-0">Посещения за год</h6>
        </div>
        <div class="card-body p-24">
            <div id="lineYearChart"></div>
        </div>
    </div>
@endsection

<!-- Modal для импорта страниц -->
<div class="modal fade" id="importPagesModal" tabindex="-1" aria-labelledby="importPagesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importPagesModalLabel">Импорт страниц</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('projects.pages.import', $project) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="pagesData" class="form-label">Данные страниц</label>
                        <textarea class="form-control" id="pagesData" name="pages_data" rows="10" placeholder="Введите данные страниц через точку с запятой. Пример:
/about|О нас|section|о нас, компания;
/contact|Контакты|card|контакты, обратная связь;
/blog|Блог|section|блог, статьи;
/blog/post-1|Первый пост|card|статья, первый пост;"></textarea>
                        <div class="text-light mt-2">Формат строки: URL|Заголовок|Тип|Ключевые слова|ID родителя;<br>
- URL (обязательно) - адрес страницы<br>
- Заголовок (необязательно) - название страницы<br>
- Тип (необязательно) - home/section/card (по умолчанию card)<br>
- Ключевые слова (необязательно) - через запятую<br>
- ID родителя (необязательно) - ID страницы-родителя<br>
Пример: /about|О нас|section|о нас, компания;<br>
Пример с родителем: /about/team|Команда|card|команда, сотрудники|2;
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Импортировать</button>
                </div>
            </form>
        </div>
    </div>
</div>
