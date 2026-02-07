@extends('layout.layout')

@php
$title = 'Добавить активность';
$subTitle = 'Проект: ' . $project->name;
$script = '<script src="' . asset('assets/js/flatpickr.js') . '"></script>
<script>
flatpickr("#event_date", {
    enableTime: true,
    dateFormat: "Y-m-d H:i",
});
</script>';
@endphp

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4">
            <span class="text-muted fw-light">
                @if($page)
                    {{ $project->name }} / {{ $page->title }} /
                @else
                    {{ $project->name }} /
                @endif
            </span>
            Добавить активность
        </h4>

        <div class="card">
            <div class="card-body">
                <form action="{{ $page ? route('projects.pages.activities.store', [$project, $page]) : route('projects.activities.store', $project) }}"
                      method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label" for="event_date">Дата события <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control @error('event_date') is-invalid @enderror"
                               id="event_date" name="event_date"
                               value="{{ old('event_date', now()->format('Y-m-d\TH:i')) }}">
                        @error('event_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="category">Категория <span class="text-danger">*</span></label>
                        <select class="form-select @error('category') is-invalid @enderror"
                                id="category" name="category">
                            <option value="">Выберите категорию</option>
                            <option value="content" {{ old('category') == 'content' ? 'selected' : '' }}>Контент</option>
                            <option value="links" {{ old('category') == 'links' ? 'selected' : '' }}>Ссылки</option>
                            <option value="technical" {{ old('category') == 'technical' ? 'selected' : '' }}>Техническое</option>
                            <option value="meta" {{ old('category') == 'meta' ? 'selected' : '' }}>Мета теги</option>
                            <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Другое</option>
                        </select>
                        @error('category')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="title">Заголовок <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" placeholder="Краткое описание активности"
                               value="{{ old('title') }}">
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="description">Описание</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="4"
                                  placeholder="Подробности, ссылки, отчеты">{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ $page ? route('projects.pages.activities.index', [$project, $page]) : route('projects.show', $project) }}"
                           class="btn btn-outline-secondary me-2">
                            Отмена
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Сохранить
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
