@extends('layout.layout')

@php
$title = 'Просмотр страницы';
$subTitle = 'Детали страницы проекта: ' . $project->name;
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
</style>
';
$script = '<script>
    // Инициализация DataTable
    let table;
    if (typeof DataTable !== "undefined") {
        table = new DataTable("#dataTable", {
            paging: false,
            ordering: true,
            info: false,
            searching: true,
            columnDefs: [
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
    const alwaysVisibleColumns = [0, 1, 10]; // ID, Ссылка, Действия

    // Инициализация настроек столбцов из заголовков таблицы
    function initColumnSettings() {
        const headers = document.querySelectorAll("#dataTable thead th");
        columnSettings = {};

        headers.forEach((th, index) => {
            const key = "col" + index;
            const text = th.textContent.trim() || (index === 0 ? "ID" : "Столбец " + index);
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
        const saved = localStorage.getItem("donorsTableColumns_" + "{{ $page->id }}");
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
        localStorage.setItem("donorsTableColumns_" + "{{ $page->id }}", JSON.stringify(settings));
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
        label.innerHTML = `
            <span class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                <span class="w-36-px flex-shrink-0"></span>
                <span class="text-md fw-semibold mb-0">${col.title}</span>
            </span>
        `;
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

@section('content')

<div class="card basic-data-table pages-table">
    <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
        <div class="d-flex align-items-center flex-wrap gap-3">
            <h5 class="card-title mb-0">Доноры для страницы: {{ $page->url }}</h5>
        </div>
         <div class="d-flex align-items-center flex-wrap gap-3">
            <a href="{{ route('projects.pages.index', $project) }}" class="btn btn-secondary">Назад к списку</a>
            <a href="{{ route('projects.pages.edit', [$project, $page]) }}" class="btn btn-primary">Редактировать</a>
            <form action="{{ route('projects.pages.destroy', [$project, $page]) }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить эту страницу?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Удалить</button>
            </form>
        </div>
    </div>
    <div class="card-body p-24">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Table Control Panel -->
        <div class="d-flex align-items-center flex-wrap gap-3 mb-3">
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
            <a href="{{ route('projects.pages.donors.create', [$project, $page]) }}" class="btn btn-primary">Добавить донора</a>
        </div>

         @if($page->donors->count() > 0)
            <div class="table-responsive">
                <table class="table bordered-table mb-0 w-100" id="dataTable">
                    <thead>
                    <tr>
                        <th scope="col" style="width: 60px;">ID</th>
                        <th scope="col">Ссылка</th>
                        <th scope="col">Тип</th>
                        <th scope="col" style="width: 100px;">Авторитетность</th>
                        <th scope="col" style="width: 90px;">Анкор</th>
                        <th scope="col" style="width: 100px;">Тип ссылки</th>
                        <th scope="col" style="width: 110px;">Дата добавления</th>
                        <th scope="col" style="width: 80px;">Статус</th>
                        <th scope="col" style="width: 100px;">Цена</th>
                        <th scope="col" style="width: 120px;">Площадка</th>
                        <th scope="col" style="width: 140px;">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($page->donors as $donor)
                        <tr>
                            <td>{{ $donor->id }}</td>
                            <td><a href="{{ $donor->link }}" target="_blank">{{ Str::limit($donor->link, 50) }}</a></td>
                            <td>{{ $donor->type }}</td>
                            <td>{{ $donor->authority }}</td>
                            <td>{{ $donor->anchor ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $donor->link_type === 'dofollow' ? 'success' : 'warning' }}">
                                    {{ $donor->link_type }}
                                </span>
                            </td>
                            <td>{{ $donor->added_at ? $donor->added_at->format('d.m.Y') : '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $donor->status === 'active' ? 'success' : ($donor->status === 'inactive' ? 'warning' : 'danger') }}">
                                    {{ $donor->status }}
                                </span>
                            </td>
                            <td>{{ $donor->price ? number_format($donor->price, 2) . ' ₽' : '-' }}</td>
                            <td>{{ $donor->marketplace ?? '-' }}</td>
                             <td>
                                 <a href="{{ route('projects.pages.donors.show', [$project, $page, $donor]) }}" class="w-32-px h-32-px bg-primary-light text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center">
                                     <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
                                 </a>
                                 <a href="{{ route('projects.pages.donors.edit', [$project, $page, $donor]) }}" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center">
                                     <iconify-icon icon="lucide:edit"></iconify-icon>
                                 </a>
                                 <form action="{{ route('projects.pages.donors.destroy', [$project, $page, $donor]) }}" method="POST" class="d-inline" onsubmit="return confirm('Вы уверены, что хотите удалить этого донора?');">
                                     @csrf
                                     @method('DELETE')
                                     <button type="submit" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center border-0">
                                         <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                                     </button>
                                 </form>
                             </td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-20">
                <p class="text-gray-500">Доноры не найдены. <a href="{{ route('projects.pages.donors.create', [$project, $page]) }}">Добавить первого донора</a>.</p>
            </div>
        @endif
    </div>
</div>

<!-- Keywords Section -->
<div class="card basic-data-table pages-table mt-4">
    <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
        <div class="d-flex align-items-center flex-wrap gap-3">
            <h5 class="card-title mb-0">Ключевые слова для страницы: {{ $page->url }}</h5>
        </div>
        <div class="d-flex align-items-center flex-wrap gap-3">
            <a href="{{ route('projects.pages.keywords.create', [$project, $page]) }}" class="btn btn-primary">Добавить ключевое слово</a>
        </div>
    </div>
    <div class="card-body p-24">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($page->keywords->count() > 0)
            <div class="table-responsive">
                <table class="table bordered-table mb-0 w-100">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 60px;">ID</th>
                            <th scope="col">Ключевое слово</th>
                            <th scope="col">Основное</th>
                            <th scope="col">Частота</th>
                            <th scope="col">CPC</th>
                            <th scope="col">Сложность</th>
                            <th scope="col">Позиция</th>
                            <th scope="col">Лучшая позиция</th>
                            <th scope="col">Тренд</th>
                            <th scope="col">Регион</th>
                            <th scope="col">Фактический URL</th>
                            <th scope="col">Последняя проверка</th>
                            <th scope="col" style="width: 140px;">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($page->keywords as $keyword)
                            <tr>
                                <td>{{ $keyword->id }}</td>
                                <td>{{ $keyword->keyword }}</td>
                                <td>
                                    <span class="badge bg-{{ $keyword->is_main ? 'success' : 'secondary' }}">
                                        {{ $keyword->is_main ? 'Да' : 'Нет' }}
                                    </span>
                                </td>
                                <td>{{ $keyword->volume ?? '-' }}</td>
                                <td>{{ $keyword->cpc ? number_format($keyword->cpc, 2) . ' ₽' : '-' }}</td>
                                <td>{{ $keyword->difficulty ?? '-' }}</td>
                                <td>{{ $keyword->current_position ?? '-' }}</td>
                                <td>{{ $keyword->best_position ?? '-' }}</td>
                                <td>{{ $keyword->trend ?? '-' }}</td>
                                <td>{{ $keyword->region ?? '-' }}</td>
                                <td>{{ $keyword->actual_url ?? '-' }}</td>
                                <td>{{ $keyword->last_tracked_at ? $keyword->last_tracked_at->format('d.m.Y H:i') : '-' }}</td>
                                <td>
                                    <a href="{{ route('projects.pages.keywords.show', [$project, $page, $keyword]) }}" class="w-32-px h-32-px bg-primary-light text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center">
                                        <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
                                    </a>
                                    <a href="{{ route('projects.pages.keywords.edit', [$project, $page, $keyword]) }}" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center">
                                        <iconify-icon icon="lucide:edit"></iconify-icon>
                                    </a>
                                    <form action="{{ route('projects.pages.keywords.destroy', [$project, $page, $keyword]) }}" method="POST" class="d-inline" onsubmit="return confirm('Вы уверены, что хотите удалить это ключевое слово?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center border-0">
                                            <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-20">
                <p class="text-gray-500">Ключевые слова не найдены. <a href="{{ route('projects.pages.keywords.create', [$project, $page]) }}">Добавить первое ключевое слово</a>.</p>
            </div>
        @endif
    </div>
</div>

@endsection
