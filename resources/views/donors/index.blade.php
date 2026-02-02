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
                        <th scope="col">Страница</th>
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
                                <a href="{{ route('projects.pages.show', [$project, $donor->page]) }}">
                                    {{ $donor->page->url }}
                                </a>
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
                                <a href="{{ route('projects.pages.donors.show', [$project, $donor->page, $donor]) }}" class="btn btn-sm btn-info">Просмотр</a>
                                <a href="{{ route('projects.pages.donors.edit', [$project, $donor->page, $donor]) }}" class="btn btn-sm btn-primary">Редактировать</a>
                                <form action="{{ route('projects.pages.donors.destroy', [$project, $donor->page, $donor]) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Вы уверены, что хотите удалить этот донор?')">Удалить</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-20">
                <p class="text-gray-500">Доноры не найдены. <a href="{{ route('projects.pages.create', $project) }}">Добавьте первую страницу</a> и затем создайте доноров для нее.</p>
            </div>
        @endif
    </div>
</div>

@endsection