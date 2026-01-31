@extends('layout.layout')

@php
$title = 'Просмотр страницы';
$subTitle = 'Детали страницы проекта: ' . $project->name;
@endphp

@section('content')

<div class="card h-100 p-0 radius-12">
    <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
        <div class="d-flex align-items-center flex-wrap gap-3">
            <h4 class="mb-0">Детали страницы</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap gap-3">
            <a href="{{ route('projects.pages.index', $project) }}" class="btn btn-secondary">Назад к списку</a>
            <a href="{{ route('projects.pages.edit', [$project, $page]) }}" class="btn btn-primary">Редактировать</a>
        </div>
    </div>
    <div class="card-body p-24">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">ID:</label>
                <p>{{ $page->id }}</p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Проект:</label>
                <p><a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a></p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label fw-bold">URL:</label>
                <p><a href="{{ $page->url }}" target="_blank" class="text-primary-600">{{ $page->url }}</a></p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Тип страницы:</label>
                <p>
                    @switch($page->type)
                        @case('home') Главная @break
                        @case('section') Раздел @break
                        @case('card') Карточка @break
                    @endswitch
                </p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Уровень вложенности:</label>
                <p>{{ $page->nesting_level }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label fw-bold">Заголовок страницы:</label>
                <p>{{ $page->title ?: '-' }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label fw-bold">Ключевые слова:</label>
                <p>{{ $page->keywords ?: '-' }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">Входящие ссылки:</label>
                <p>{{ $page->incoming_links_count }}</p>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">HTTP статус:</label>
                <p>
                    @if($page->status_code)
                        <span class="badge {{ $page->status_code == 200 ? 'bg-success' : 'bg-danger' }}">{{ $page->status_code }}</span>
                    @else
                        -
                    @endif
                </p>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">Индексация:</label>
                <p>
                    @if($page->is_indexable)
                        <span class="badge bg-success">Разрешена</span>
                    @else
                        <span class="badge bg-danger">Запрещена</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Создано:</label>
                <p>{{ $page->created_at->format('d.m.Y H:i') }}</p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Обновлено:</label>
                <p>{{ $page->updated_at->format('d.m.Y H:i') }}</p>
            </div>
        </div>

        <div class="d-flex gap-2 mt-3">
            <a href="{{ route('projects.pages.edit', [$project, $page]) }}" class="btn btn-primary">Редактировать</a>
            <form action="{{ route('projects.pages.destroy', [$project, $page]) }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить эту страницу?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Удалить</button>
            </form>
        </div>
    </div>
</div>

@endsection
