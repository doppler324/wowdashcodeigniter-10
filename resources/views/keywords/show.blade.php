@extends('layout.layout')

@php
$title = 'Детали ключевого слова';
$subTitle = 'Ключевое слово: ' . $keyword->keyword;
@endphp

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Детали ключевого слова</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ $keyword->keyword }}</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Страница:</strong> {{ $keyword->page->url }}</p>
                    <p><strong>Основное ключевое слово:</strong> {{ $keyword->is_main ? 'Да' : 'Нет' }}</p>
                    <p><strong>Частота:</strong> {{ $keyword->volume }}</p>
                    <p><strong>Частота точного совпадения:</strong> {{ $keyword->volume_exact }}</p>
                    <p><strong>CPC:</strong> {{ $keyword->cpc }}</p>
                    <p><strong>Сложность:</strong> {{ $keyword->difficulty }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Текущая позиция:</strong> {{ $keyword->current_position }}</p>
                    <p><strong>Лучшая позиция:</strong> {{ $keyword->best_position }}</p>
                    <p><strong>Начальная позиция:</strong> {{ $keyword->start_position }}</p>
                    <p><strong>Тренд:</strong> {{ $keyword->trend }}</p>
                    <p><strong>Регион:</strong> {{ $keyword->region }}</p>
                    <p><strong>Фактический URL:</strong> {{ $keyword->actual_url }}</p>
                </div>
            </div>
            <p><strong>Последний трек:</strong> {{ $keyword->last_tracked_at }}</p>

            <div class="mt-4">
                <a href="{{ route('projects.pages.keywords.edit', [$keyword->page->project, $keyword->page, $keyword]) }}" class="btn btn-warning">Редактировать</a>
                <a href="{{ route('projects.pages.show', [$keyword->page->project, $keyword->page]) }}" class="btn btn-secondary">Назад</a>
            </div>
        </div>
    </div>
</div>
@endsection
