@extends('layout.layout')

@php
$title = 'Ключевые слова';
if (isset($project)) {
    $subTitle = 'Проект: ' . $project->name;
} else {
    $subTitle = 'Список всех ключевых слов';
}
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
</style>
';
$script = '<script>
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
</script>';
@endphp

@section('content')
<div class="card basic-data-table pages-table">
    <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
        <div class="d-flex align-items-center flex-wrap gap-3">
            <h5 class="card-title mb-0">{{ isset($project) ? 'Ключевые слова проекта: ' . $project->name : 'Все ключевые слова' }}</h5>
        </div>
         <div class="d-flex align-items-center flex-wrap gap-3">
            @if (isset($project))
            <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">Назад к проекту</a>
            @endif
            <a href="{{ route('projects.index') }}" class="btn btn-secondary">Назад к списку проектов</a>
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
                                            <li><a class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900" href="{{ route('projects.pages.keywords.show', [$keyword->page->project, $keyword->page, $keyword]) }}">Просмотр</a></li>
                                            <li><a class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900" href="{{ route('projects.pages.keywords.edit', [$keyword->page->project, $keyword->page, $keyword]) }}">Редактирование</a></li>
                                            <li>
                                                <form action="{{ route('projects.pages.keywords.destroy', [$keyword->page->project, $keyword->page, $keyword]) }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить это ключевое слово?')">
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
                                <td><a href="{{ route('projects.pages.show', [$keyword->page->project, $keyword->page]) }}" class="text-primary-600">{{ Str::limit($keyword->page->url, 40) }}</a></td>
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
                                        <a href="{{ route('projects.pages.keywords.show', [$keyword->page->project, $keyword->page, $keyword]) }}" class="w-32-px h-32-px bg-info-focus text-info-600 rounded-circle d-flex justify-content-center align-items-center">
                                            <iconify-icon icon="uil:eye"></iconify-icon>
                                        </a>
                                        <a href="{{ route('projects.pages.keywords.edit', [$keyword->page->project, $keyword->page, $keyword]) }}" class="w-32-px h-32-px bg-success-focus text-success-600 rounded-circle d-flex justify-content-center align-items-center">
                                            <iconify-icon icon="uil:edit"></iconify-icon>
                                        </a>
                                        <form action="{{ route('projects.pages.keywords.destroy', [$keyword->page->project, $keyword->page, $keyword]) }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить это ключевое слово?')">
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
@endsection
