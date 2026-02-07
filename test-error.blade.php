<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Test</title>
</head>
<body>
    <?php
    $project = new stdClass();
    $project->id = 1;

    $script = '<script src="' . 'assets/js/flatpickr.js.js' . '"></script>
<script>
// Flat pickr or date picker js
function getDatePicker(receiveID) {
    flatpickr(receiveID, {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
    });
}
getDatePicker("#startDate");
getDatePicker("#endDate");

getDatePicker("#editstartDate");
getDatePicker("#editendDate");
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
        const saved = localStorage.getItem("pagesTableColumns_' . $project->id . '");
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
        localStorage.setItem("pagesTableColumns_' . $project->id . '", JSON.stringify(settings));
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
    const projectId = ' . $project->id . ';
    initColumnSettings();
    loadColumnSettings();
    generateSettingsMenu();
    applyColumnSettings();

    // Отладка
    console.log("Column settings initialized:", columnSettings);

    // Обработчики для модальных окна просмотра, редактирования и удаления
    document.querySelectorAll(\'[data-bs-target="#exampleModalView"]\').forEach(button => {
        button.addEventListener(\'click\', function() {
            const activityData = JSON.parse(this.getAttribute(\'data-activity\').replace(/"/g, \'"\'));
            document.getElementById(\'viewTitle\').textContent = activityData.title;
            document.getElementById(\'viewEventDate\').textContent = new Date(activityData.event_date).toLocaleString(\'ru-RU\', {
                year: \'numeric\',
                month: \'2-digit\',
                day: \'2-digit\',
                hour: \'2-digit\',
                minute: \'2-digit\'
            });
            document.getElementById(\'viewCategory\').textContent = getCategoryName(activityData.category);
            document.getElementById(\'viewDescription\').textContent = activityData.description ?? \'N/A\';
        });
    });

    document.querySelectorAll(\'[data-bs-target="#exampleModalEdit"]\').forEach(button => {
        button.addEventListener(\'click\', function() {
            const activityData = JSON.parse(this.getAttribute(\'data-activity\').replace(/"/g, \'"\'));
            document.getElementById(\'editForm\').action = `/projects/${projectId}/activities/${activityData.id}`;
            document.getElementById(\'editTitle\').value = activityData.title;
            document.getElementById(\'editstartDate\').value = new Date(activityData.event_date).toISOString().slice(0, 16);
            document.getElementById(\'editCategory\').value = activityData.category;
            document.getElementById(\'editdesc\').value = activityData.description ?? \'\';
        });
    });

    document.querySelectorAll(\'[data-bs-target="#exampleModalDelete"]\').forEach(button => {
        button.addEventListener(\'click\', function() {
            const activityData = JSON.parse(this.getAttribute(\'data-activity\').replace(/"/g, \'"\'));
            document.getElementById(\'deleteForm\').action = `/projects/${projectId}/activities/${activityData.id}`;
        });
    });

    function getCategoryName(category) {
        switch(category) {
            case \'content\':
                return \'Контент\';
            case \'links\':
                return \'Ссылки\';
            case \'technical\':
                return \'Техническое\';
            case \'meta\':
                return \'Мета теги\';
            default:
                return \'Другое\';
        }
    }
});
</script>';
    ?>
    <?php echo $script; ?>
</body>
</html>