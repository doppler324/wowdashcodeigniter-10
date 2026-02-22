@extends('layout.layout')

@php
$title = 'Все доноры проекта';
$subTitle = 'Проект: ' . $project->name;
$script = '<script>
    // Инициализация DataTable
    var table;
    if (typeof DataTable !== "undefined") {
        var savedPageLength = localStorage.getItem("donorsTableLength");
        var initialPageLength = savedPageLength ? parseInt(savedPageLength) : 10;

        table = new DataTable("#donorsTable", {
            paging: true,
            lengthChange: true,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Все"]],
            pageLength: initialPageLength,
            ordering: true,
            info: true,
            searching: true,
        });

        table.on("length.dt", function(e, settings, len) {
            localStorage.setItem("donorsTableLength", len);
        });
    }
</script>';
@endphp

@section('content')

<div class="card basic-data-table">
    <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
        <div class="d-flex align-items-center flex-wrap gap-3">
            <h5 class="card-title mb-0">Все доноры проекта: {{ $project->name }}</h5>
        </div>
         <div class="d-flex align-items-center flex-wrap gap-3">
                <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">Назад к проекту</a>
                @if($pages->count() > 0)
                    <a href="{{ route('projects.pages.donors.create', [$project, $pages->first()]) }}" class="btn btn-primary">Добавить донора</a>
                @else
                    <a href="{{ route('projects.pages.create', $project) }}" class="btn btn-primary">Добавить страницу</a>
                @endif
            </div>
    </div>
    <div class="card-body p-24">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($donors->count() > 0)
            <div class="table-responsive">
                <table class="table bordered-table mb-0 w-100" id="donorsTable">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Страницы</th>
                        <th scope="col">Ссылка</th>
                        <th scope="col">Тип</th>
                        <th scope="col">Площадка</th>
                        <th scope="col">Цена</th>
                        <th scope="col">Статус</th>
                        <th scope="col">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($donors as $donor)
                        <tr>
                            <td>{{ $donor->id }}</td>
                            <td>
                                @foreach($donor->pages as $page)
                                    <a href="{{ route('projects.pages.show', [$project, $page]) }}">
                                        {{ $page->url }}
                                    </a><br>
                                @endforeach
                            </td>
                            <td>
                                <a href="{{ $donor->link }}" target="_blank" rel="noopener noreferrer">
                                    {{ $donor->link }}
                                </a>
                            </td>
                            <td>{{ $donor->type }}</td>
                            <td>{{ $donor->marketplace }}</td>
                            <td>{{ $donor->price ? number_format($donor->price, 2) . ' ₽' : '-' }}</td>
                            <td>
                                <span class="badge @if($donor->status == 'active') bg-success @elseif($donor->status == 'inactive') bg-warning @else bg-danger @endif">
                                    {{ $donor->status }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ route('projects.pages.donors.show', [$project, $donor->pages->first(), $donor]) }}" class="w-32-px h-32-px bg-info-focus text-info-600 rounded-circle d-flex justify-content-center align-items-center">
                                        <iconify-icon icon="uil:eye"></iconify-icon>
                                    </a>
                                    <a href="{{ route('projects.pages.donors.edit', [$project, $donor->pages->first(), $donor]) }}" class="w-32-px h-32-px bg-success-focus text-success-600 rounded-circle d-flex justify-content-center align-items-center">
                                        <iconify-icon icon="uil:edit"></iconify-icon>
                                    </a>
                                    <form action="{{ route('projects.pages.donors.destroy', [$project, $donor->pages->first(), $donor]) }}" method="POST" class="d-inline" onsubmit="return confirm('Вы уверены, что хотите удалить этот донор?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-32-px h-32-px bg-danger-focus text-danger-600 rounded-circle d-flex justify-content-center align-items-center border-0">
                                            <iconify-icon icon="uil:trash-alt"></iconify-icon>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-20">
                <p class="text-gray-500">Доноры не найдены. @if($pages->count() > 0) <a href="{{ route('projects.pages.donors.create', [$project, $pages->first()]) }}">Добавьте первого донора</a> или выберите страницу из выпадающего меню. @else <a href="{{ route('projects.pages.create', $project) }}">Добавьте первую страницу</a> и затем создайте доноров для нее. @endif</p>
            </div>
        @endif
    </div>
</div>

@endsection
