@extends('layout.layout')

@php
$title = 'Ключевые слова';
$subTitle = 'Список всех ключевых слов';
@endphp

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Ключевые слова</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Все ключевые слова</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Ключевое слово</th>
                            <th>Страница</th>
                            <th>Основное</th>
                            <th>Частота</th>
                            <th>CPC</th>
                            <th>Сложность</th>
                            <th>Позиция</th>
                            <th>Тренд</th>
                            <th>Регион</th>
                            <th>Последний трек</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($keywords as $keyword)
                        <tr>
                            <td>{{ $keyword->keyword }}</td>
                            <td>{{ $keyword->page->url }}</td>
                            <td>{{ $keyword->is_main ? 'Да' : 'Нет' }}</td>
                            <td>{{ $keyword->volume }}</td>
                            <td>{{ $keyword->cpc }}</td>
                            <td>{{ $keyword->difficulty }}</td>
                            <td>{{ $keyword->current_position }}</td>
                            <td>{{ $keyword->trend }}</td>
                            <td>{{ $keyword->region }}</td>
                            <td>{{ $keyword->last_tracked_at }}</td>
                            <td>
                                <a href="{{ route('projects.pages.keywords.show', [$keyword->page->project, $keyword->page, $keyword]) }}" class="btn btn-primary btn-sm">Show</a>
                                <a href="{{ route('projects.pages.keywords.edit', [$keyword->page->project, $keyword->page, $keyword]) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('projects.pages.keywords.destroy', [$keyword->page->project, $keyword->page, $keyword]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
