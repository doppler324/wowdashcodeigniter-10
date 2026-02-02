@extends('layout.layout')

@php
$title = 'Добавить донора';
$subTitle = 'Добавить нового донора для страницы: ' . $page->url;
@endphp

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="mb-3">
        <a href="{{ route('projects.pages.show', [$project, $page]) }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back"></i> Назад к странице
        </a>
    </div>
    <h4 class="fw-bold py-3 mb-4">Добавить донор для страницы: {{ $page->url }}</h4>

    <!-- Donor Form -->
    <div class="card">
        <h5 class="card-header">Информация о доноре</h5>
        <div class="card-body">
            <form method="POST" action="{{ route('projects.pages.donors.store', [$project, $page]) }}">
                @csrf

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="link">Ссылка *</label>
                    <div class="col-sm-10">
                        <input type="url" id="link" name="link" class="form-control @error('link') is-invalid @enderror" value="{{ old('link') }}" placeholder="https://example.com" required>
                        @error('link')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="type">Тип *</label>
                    <div class="col-sm-10">
                        <select id="type" name="type" class="form-control @error('type') is-invalid @enderror" required>
                            <option value="">Выберите тип</option>
                            <option value="статья" {{ old('type') == 'статья' ? 'selected' : '' }}>Статья</option>
                            <option value="форум" {{ old('type') == 'форум' ? 'selected' : '' }}>Форум</option>
                            <option value="каталог" {{ old('type') == 'каталог' ? 'selected' : '' }}>Каталог</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="authority">Авторитетность</label>
                    <div class="col-sm-10">
                        <input type="number" id="authority" name="authority" class="form-control @error('authority') is-invalid @enderror" value="{{ old('authority') }}" min="0" max="100" placeholder="0-100">
                        @error('authority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="anchor">Анкор</label>
                    <div class="col-sm-10">
                        <input type="text" id="anchor" name="anchor" class="form-control @error('anchor') is-invalid @enderror" value="{{ old('anchor') }}" placeholder="Анкор">
                        @error('anchor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="link_type">Тип ссылки *</label>
                    <div class="col-sm-10">
                        <select id="link_type" name="link_type" class="form-control @error('link_type') is-invalid @enderror" required>
                            <option value="dofollow" {{ old('link_type') == 'dofollow' ? 'selected' : '' }}>dofollow</option>
                            <option value="nofollow" {{ old('link_type') == 'nofollow' ? 'selected' : '' }}>nofollow</option>
                        </select>
                        @error('link_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="added_at">Дата добавления</label>
                    <div class="col-sm-10">
                        <input type="date" id="added_at" name="added_at" class="form-control @error('added_at') is-invalid @enderror" value="{{ old('added_at') }}">
                        @error('added_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="is_image_link">Является ли ссылка картинкой</label>
                    <div class="col-sm-10">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_image_link" name="is_image_link" value="1" {{ old('is_image_link') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_image_link">Да</label>
                        </div>
                        @error('is_image_link')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="status">Статус ссылки *</label>
                    <div class="col-sm-10">
                        <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Активна</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Неактивна</option>
                            <option value="deleted" {{ old('status') == 'deleted' ? 'selected' : '' }}>Удалена</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="is_redirect">Ведет ли ссылка через редирект</label>
                    <div class="col-sm-10">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_redirect" name="is_redirect" value="1" {{ old('is_redirect') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_redirect">Да</label>
                        </div>
                        @error('is_redirect')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="duration">Срок действия ссылки</label>
                    <div class="col-sm-10">
                        <input type="text" id="duration" name="duration" class="form-control @error('duration') is-invalid @enderror" value="{{ old('duration') }}" placeholder="например, 1 год">
                        @error('duration')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="check_date">Дата проверки ссылки</label>
                    <div class="col-sm-10">
                        <input type="date" id="check_date" name="check_date" class="form-control @error('check_date') is-invalid @enderror" value="{{ old('check_date') }}">
                        @error('check_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="placement_type">Как размещена ссылка</label>
                    <div class="col-sm-10">
                        <select id="placement_type" name="placement_type" class="form-control @error('placement_type') is-invalid @enderror">
                            <option value="">Выберите способ размещения</option>
                            <option value="статья" {{ old('placement_type') == 'статья' ? 'selected' : '' }}>Статья</option>
                            <option value="обзор" {{ old('placement_type') == 'обзор' ? 'selected' : '' }}>Обзор</option>
                            <option value="контекстная" {{ old('placement_type') == 'контекстная' ? 'selected' : '' }}>Контекстная</option>
                        </select>
                        @error('placement_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="status_code">Код ответа страницы-донора</label>
                    <div class="col-sm-10">
                        <input type="number" id="status_code" name="status_code" class="form-control @error('status_code') is-invalid @enderror" value="{{ old('status_code') }}" min="100" max="599" placeholder="например, 200">
                        @error('status_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="price">Цена</label>
                    <div class="col-sm-10">
                        <input type="number" id="price" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" min="0" step="0.01" placeholder="0.00">
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="marketplace">Площадка закупки</label>
                    <div class="col-sm-10">
                        <select id="marketplace" name="marketplace" class="form-control @error('marketplace') is-invalid @enderror">
                            <option value="">Выберите площадку</option>
                            <option value="Miralinks" {{ old('marketplace') == 'Miralinks' ? 'selected' : '' }}>Miralinks</option>
                            <option value="Collaborator" {{ old('marketplace') == 'Collaborator' ? 'selected' : '' }}>Collaborator</option>
                            <option value="Gogetlinks" {{ old('marketplace') == 'Gogetlinks' ? 'selected' : '' }}>Gogetlinks</option>
                            <option value="прямой аутрич" {{ old('marketplace') == 'прямой аутрич' ? 'selected' : '' }}>Прямой аутрич</option>
                        </select>
                        @error('marketplace')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row justify-content-end">
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-primary">Добавить донор</button>
                        <a href="{{ route('projects.pages.donors.index', [$project, $page]) }}" class="btn btn-secondary">Отмена</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
