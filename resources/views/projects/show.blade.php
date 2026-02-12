@extends('layout.layout')

{{-- Переменные для layout --}}
<?php $title = 'Просмотр проекта'; ?>
<?php $subTitle = 'Проект: ' . $project->name; ?>

{{-- Стили для tooltip --}}
<style>
.tooltip-content {
    padding: 12px;
    min-width: 200px;
    font-family: Arial, sans-serif;
}
.tooltip-visits {
    font-size: 14px;
    font-weight: bold;
    margin-bottom: 8px;
    color: #333;
}
.tooltip-tasks-title {
    font-size: 12px;
    color: #666;
    margin-bottom: 8px;
    font-weight: bold;
    border-top: 1px solid #ddd;
    padding-top: 8px;
}
.tooltip-task {
    cursor: pointer;
    padding: 6px 8px;
    margin-bottom: 4px;
    background: #f5f5f5;
    border-radius: 4px;
    font-size: 13px;
}
.tooltip-task:hover {
    background: #e0e0e0;
}
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
</style>

{{-- JavaScript для графика --}}
<script src="{{ asset('assets/js/lineChartPageChart.js') }}"></script>
<script src="{{ asset('assets/js/flatpickr.js') }}"></script>
<script>
    // Глобальные переменные для задач
    const annotationsData = {!! $annotationsJson !!};
    const activitiesByDate = {!! json_encode($activitiesByDate) !!};

    // Функция для форматирования tooltip
    function getTooltipHtml(dataPointIndex) {
        const annotation = annotationsData[dataPointIndex];
        const chartDataLocal = {!! $chartDataJson !!};
        const val = chartDataLocal.data[dataPointIndex] || 0;
        let html = "<div class='tooltip-content'>";
        html += "<div class='tooltip-visits'>" + val + " посещений</div>";

        if (annotation && annotation.tasks && annotation.tasks.length > 0) {
            html += "<div class='tooltip-tasks-title'>Задачи:</div>";

            for (let i = 0; i < annotation.tasks.length; i++) {
                const task = annotation.tasks[i];
                html += "<div class='tooltip-task' onclick='showTaskFromTooltip(" + dataPointIndex + ", " + i + ")'>" + (i + 1) + ". " + task.title + "</div>";
            }
        }

        html += "</div>";
        return html;
    }

    // Функция для показа задачи из tooltip
    function showTaskFromTooltip(dataPointIndex, taskIndex) {
        const annotation = annotationsData[dataPointIndex];
        if (annotation && annotation.tasks && annotation.tasks[taskIndex]) {
            showTasksModal([annotation.tasks[taskIndex]]);
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        // График за месяц
        const chartData = {!! $chartDataJson !!};

        // Цвета по типу задачи
        const categoryColors = {
            content: "#FF9F29",
            links: "#28C76F",
            technical: "#FF4560",
            meta: "#7367F0",
            other: "#00CFE8"
        };

        // Формируем размеры маркеров для каждой точки данных
        const markerSizes = chartData.data.map((value, index) => {
            const annotation = annotationsData[index];
            if (annotation && annotation.tasks) {
                return annotation.tasks.length > 1 ? 10 : 8;
            }
            return 0; // Обычные точки без маркера
        });

        // Формируем цвета маркеров для каждой точки данных
        const markerColors = chartData.data.map((value, index) => {
            const annotation = annotationsData[index];
            if (annotation && annotation.tasks && annotation.tasks.length > 0) {
                // Берем цвет первой задачи
                const firstTask = annotation.tasks[0];
                return categoryColors[firstTask.category] || "#7367F0";
            }
            return "#5b5b5b"; // Обычный цвет для точек без задач
        });

        // График за месяц
        var optionsMonth = {
            series: [{
                name: "Visits",
                data: chartData.data
            }],
            chart: {
                height: 350,
                type: "area",
                toolbar: {
                    show: false
                },
                animations: {
                    enabled: true,
                    easing: "linear",
                    dynamicAnimation: {
                        speed: 1000
                    }
                },
                events: {
                    dataPointSelection: function(event, chartContext, config) {
                        const dataPointIndex = config.dataPointIndex;
                        showTaskFromTooltip(dataPointIndex, 0);
                    }
                }
            },
            colors: ["#5b5b5b"],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: "smooth"
            },
            xaxis: {
                categories: chartData.categories,
                labels: {
                    show: true,
                    rotate: -45,
                    rotateAlways: false,
                    hideOverlappingLabels: true,
                    showDuplicates: false,
                    trim: false,
                    minHeight: undefined,
                    maxHeight: 120,
                    style: {
                        colors: [],
                        fontSize: "12px",
                        fontFamily: "Helvetica, Arial, sans-serif",
                        fontWeight: 400,
                        cssClass: "apexcharts-xaxis-label"
                    },
                    offsetX: 0,
                    offsetY: 0,
                    format: undefined,
                    formatter: undefined,
                    dateTimeFormat: undefined
                },
                tickAmount: undefined,
                tickPlacement: "on",
                min: undefined,
                max: undefined,
                range: undefined,
                floating: false,
                decimalsInFloat: undefined,
                overwriteCategories: undefined
            },
            yaxis: {
                labels: {
                    formatter: function(val) {
                        return Math.round(val);
                    }
                }
            },
            annotations: {
                xaxis: chartData.annotations.map(ann => ({
                    x: ann.x,
                    borderColor: ann.borderColor,
                    borderWidth: 2,
                    label: {
                        borderColor: ann.borderColor,
                        borderRadius: 6,
                        borderWidth: 1,
                        style: {
                            fontSize: "12px",
                            color: "#fff",
                            background: ann.borderColor
                        },
                        text: ann.labelText || ""
                    }
                }))
            },
            tooltip: {
                custom: function(options) {
                    return getTooltipHtml(options.dataPointIndex);
                }
            },
            markers: {
                size: markerSizes,
                colors: markerColors,
                strokeColors: "#fff",
                strokeWidth: 2,
                strokeOpacity: 0.9,
                strokeDashArray: 0,
                fillOpacity: 1,
                discrete: [],
                shape: "circle",
                radius: 2,
                offsetX: 0,
                offsetY: 0,
                onClick: undefined,
                onDblClick: undefined,
                showNullDataPoints: true,
                hover: {
                    size: undefined,
                    sizeOffset: 3
                }
            }
        };

        var chartMonth = new ApexCharts(document.querySelector("#lineMonthChart"), optionsMonth);
        chartMonth.render();

        // График за год
        var optionsYear = {
            series: [{
                name: "Visits",
                data: chartData.yearData || chartData.data
            }],
            chart: {
                height: 350,
                type: "area",
                toolbar: {
                    show: false
                }
            },
            colors: ["#5b5b5b"],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: "smooth"
            },
            xaxis: {
                categories: chartData.yearCategories || chartData.categories,
                labels: {
                    show: true,
                    rotate: -45,
                    rotateAlways: false,
                    hideOverlappingLabels: true,
                    showDuplicates: false,
                    trim: false,
                    minHeight: undefined,
                    maxHeight: 120,
                    style: {
                        colors: [],
                        fontSize: "12px",
                        fontFamily: "Helvetica, Arial, sans-serif",
                        fontWeight: 400,
                        cssClass: "apexcharts-xaxis-label"
                    },
                    offsetX: 0,
                    offsetY: 0,
                    format: undefined,
                    formatter: undefined,
                    dateTimeFormat: undefined
                },
                tickAmount: undefined,
                tickPlacement: "on",
                min: undefined,
                max: undefined,
                range: undefined,
                floating: false,
                decimalsInFloat: undefined,
                overwriteCategories: undefined
            },
            yaxis: {
                labels: {
                    formatter: function(val) {
                        return Math.round(val);
                    }
                }
            },
            annotations: {
                xaxis: chartData.annotations.map(ann => ({
                    x: ann.x,
                    borderColor: ann.borderColor,
                    borderWidth: 2,
                    label: {
                        borderColor: ann.borderColor,
                        borderRadius: 6,
                        borderWidth: 1,
                        style: {
                            fontSize: "12px",
                            color: "#fff",
                            background: ann.borderColor
                        },
                        text: ann.labelText || ""
                    }
                }))
            },
            tooltip: {
                custom: function(options) {
                    return getTooltipHtml(options.dataPointIndex);
                }
            },
            markers: {
                size: markerSizes,
                colors: markerColors,
                strokeColors: "#fff",
                strokeWidth: 2,
                strokeOpacity: 0.9,
                strokeDashArray: 0,
                fillOpacity: 1,
                discrete: [],
                shape: "circle",
                radius: 2,
                offsetX: 0,
                offsetY: 0,
                onClick: undefined,
                onDblClick: undefined,
                showNullDataPoints: true,
                hover: {
                    size: undefined,
                    sizeOffset: 3
                }
            }
        };

        var chartYear = new ApexCharts(document.querySelector("#lineYearChart"), optionsYear);
        chartYear.render();
    });
</script>

    <!-- Modal для просмотра/редактирования задачи -->
    <div class="modal fade" id="activityModal" tabindex="-1" aria-labelledby="activityModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content radius-16 bg-base">
                <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                    <h1 class="modal-title fs-5" id="activityModalLabel">Просмотр задачи</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-24" id="activityModalBody">
                    <!-- Содержимое будет загружено через AJAX -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal для списка задач -->
    <div class="modal fade" id="tasksModal" tabindex="-1" aria-labelledby="tasksModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content radius-16 bg-base">
                <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                    <h1 class="modal-title fs-5" id="tasksModalLabel">Задачи за этот день</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-24">
                    <ul id="tasksList" class="list-unstyled"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Функция для показа модалки с задачами -->
    <script>
        function showTasksModal(tasks) {
            const tasksList = document.getElementById("tasksList");
            tasksList.innerHTML = "";

            tasks.forEach(task => {
                const li = document.createElement("li");
                li.className = "d-flex align-items-start gap-3 mb-24 p-16 border-bottom";

                // Категория задачи для цветной метки
                const categoryClass = {
                    "content": "bg-primary",
                    "links": "bg-success",
                    "technical": "bg-danger",
                    "meta": "bg-warning",
                    "other": "bg-info"
                }[task.category] || "bg-secondary";

                // Получаем полные данные задачи из глобального массива
                let fullTask = null;
                for (let i = 0; i < activitiesData.length; i++) {
                    if (activitiesData[i].id === task.id) {
                        fullTask = activitiesData[i];
                        break;
                    }
                }

                li.innerHTML = `
                    <span class="w-8-px h-8-px ${categoryClass} rounded-circle mt-2"></span>
                    <div class="flex-grow-1">
                        <h6 class="fw-semibold mb-0">${task.title}</h6>
                        <span class="text-sm text-secondary-light">${task.formatted_date}</span>
                        <span class="badge ${categoryClass} text-sm ms-2">${task.category}</span>
                        ${fullTask?.description ? `
                            <p class="text-sm text-primary-light mt-8">${fullTask.description}</p>
                        ` : ""}
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

            const modal = new bootstrap.Modal(document.getElementById("tasksModal"));
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
</div}



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
            <h6 class="text-lg fw-semibold mb-0">Посещения за месяц</h6>
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
/about시오 нас|section|о нас, компания;
/contact-КОНТАКТЫ|card|контакты, обратная связь;
/blog-Блог|section|блог, статьи;
/blog/post-1-Первый пост|card|статья, первый пост;"></textarea>
                        <div class="text-light mt-2">Формат строки: URL|Заголовок |Тип|Keywords|ID родителя;<br>
- URL (обязательно) - адрес страницы<br>
- Заголовок (необязательно) - название страницы<br>
- Тип (необязательно) - home/section/card (по умолчанию card)<br>
- Keywords (необязательно) - через запятую<br>
- ID родителя (необязательно) - ID страницы-родителя<br>
Пример: /aboutmico нас|section|о нас, компания;<br>
Пример с родителем: /about/team-Команда|card|команда, сотрудники|2;
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
