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
</style>
';
$script = '<script>
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
/ (Главная) [home];
/about (О нас) [section];
/contact (Контакты) [card];
/blog (Блог) [section];
/blog/post-1 (Первый пост) [card];
/blog/post-2 (Второй пост) [card]"></textarea>
                        <div class="text-light">Формат строки: URL|Заголовок|Тип|Ключевые слова|ID родителя;<br>
- URL (обязательно) - адрес страницы<br>
- Заголовок (необязательно) - название страницы<br>
- Тип (необязательно) - home/section/card (по умолчанию card)<br>
- Ключевые слова (необязательно) - через запятую<br>
- ID родителя (необязательно) - ID страницы-родителя<br>
Пример: /about|О нас|section|о нас, компания;<br>
Пример с родителем: /about/team|Команда|card|команда, сотрудники|2;<br>
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
