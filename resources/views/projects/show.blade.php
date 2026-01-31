@extends('layout.layout')

@php
$title = 'Просмотр проекта';
$subTitle = 'Проект: ' . $project->name;
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
let table = new DataTable("#dataTable", {
    paging: false,
    ordering: true,
    info: false,
    searching: true
});
</script>';
@endphp

@section('content')

<div class="card basic-data-table">
    <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
        <div class="d-flex align-items-center flex-wrap gap-3">
            <h5 class="card-title mb-0">Страницы сайта: {{ $project->name }}</h5>
        </div>
        <div class="d-flex align-items-center flex-wrap gap-3">
            <a href="{{ route('projects.pages.create', $project) }}" class="btn btn-primary">Добавить страницу</a>
            <a href="{{ route('projects.index') }}" class="btn btn-secondary">Назад к списку</a>
        </div>
    </div>
    <div class="card-body p-24">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($pages->count() > 0)
            <table class="table bordered-table mb-0" id="dataTable">
                <thead>
                    <tr>
                        <th scope="col" style="width: 50px;"></th>
                        <th scope="col">ID</th>
                        <th scope="col">URL / Заголовок</th>
                        <th scope="col">Тип</th>
                        <th scope="col">Входящие</th>
                        <th scope="col">Статус</th>
                        <th scope="col">Индексация</th>
                        <th scope="col">Уровень</th>
                        <th scope="col">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pages->where('parent_id', null) as $rootPage)
                        @include('projects._page_row', ['page' => $rootPage, 'project' => $project, 'pages' => $pages])
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center py-20">
                <p class="text-gray-500">Страницы не найдены. <a href="{{ route('projects.pages.create', $project) }}">Добавить первую страницу</a>.</p>
            </div>
        @endif
    </div>
</div>

@endsection
