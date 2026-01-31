@extends('layout.layout')

@php
$title = 'Редактировать страницу';
$subTitle = 'Редактирование страницы проекта: ' . $project->name;
@endphp

@section('content')

<div class="card h-100 p-0 radius-12">
    <div class="card-header border-bottom bg-base py-16 px-24">
        <h4 class="mb-0">Редактировать страницу</h4>
    </div>
    <div class="card-body p-24">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('projects.pages.update', [$project, $page]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="url" class="form-label">URL страницы <span class="text-danger">*</span></label>
                    <input type="url" class="form-control @error('url') is-invalid @enderror" id="url" name="url" value="{{ old('url', $page->url) }}" required placeholder="https://example.com/page">
                    @error('url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="type" class="form-label">Тип страницы <span class="text-danger">*</span></label>
                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                        <option value="">Выберите тип</option>
                        <option value="home" {{ old('type', $page->type) == 'home' ? 'selected' : '' }}>Главная</option>
                        <option value="section" {{ old('type', $page->type) == 'section' ? 'selected' : '' }}>Раздел</option>
                        <option value="card" {{ old('type', $page->type) == 'card' ? 'selected' : '' }}>Карточка</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="parent_id" class="form-label">Родительский раздел</label>
                    <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                        <option value="">Нет (корневой уровень)</option>
                        @foreach($potentialParents as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id', $page->parent_id) == $parent->id ? 'selected' : '' }}>
                                {{ str_repeat('— ', $parent->nesting_level) }}{{ $parent->title ?: $parent->url }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Для разделов и карточек можно выбрать родительский раздел</div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="title" class="form-label">Заголовок страницы</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $page->title) }}" placeholder="Заголовок страницы (title)">
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="keywords" class="form-label">Ключевые слова</label>
                    <textarea class="form-control @error('keywords') is-invalid @enderror" id="keywords" name="keywords" rows="3" placeholder="Ключевые слова, под которые продвигается страница">{{ old('keywords', $page->keywords) }}</textarea>
                    @error('keywords')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="status_code" class="form-label">Код ответа (HTTP статус)</label>
                    <input type="number" class="form-control @error('status_code') is-invalid @enderror" id="status_code" name="status_code" value="{{ old('status_code', $page->status_code) }}" placeholder="200">
                    @error('status_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" id="is_indexable" name="is_indexable" value="1" {{ old('is_indexable', $page->is_indexable) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_indexable">
                            Разрешена индексация
                        </label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Обновить</button>
                <a href="{{ route('projects.pages.index', $project) }}" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
</div>

@endsection
