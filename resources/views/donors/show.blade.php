@extends('layout.layout')

@php
$title = 'Просмотр донора';
$subTitle = 'Просмотр донора для страницы: ' . $page->url;
@endphp

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Просмотр донора для страницы: {{ $page->url }}</h4>

    <!-- Donor Details -->
    <div class="card">
        <h5 class="card-header">Информация о доноре</h5>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Ссылка:</label>
                        <a href="{{ $donor->link }}" target="_blank">{{ $donor->link }}</a>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Тип:</label>
                        {{ $donor->type }}
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Тип ссылки:</label>
                        <span class="badge bg-{{ $donor->link_type === 'dofollow' ? 'success' : 'warning' }}">
                            {{ $donor->link_type }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Дата добавления:</label>
                        {{ $donor->added_at ? $donor->added_at->format('d.m.Y') : '-' }}
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Является ли ссылка картинкой:</label>
                        {{ $donor->is_image_link ? 'Да' : 'Нет' }}
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Статус ссылки:</label>
                        <span class="badge bg-{{ $donor->status === 'active' ? 'success' : ($donor->status === 'inactive' ? 'warning' : 'danger') }}">
                            {{ $donor->status }}
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Ведет ли ссылка через редирект:</label>
                        {{ $donor->is_redirect ? 'Да' : 'Нет' }}
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Дата проверки ссылки:</label>
                        {{ $donor->check_date ? $donor->check_date->format('d.m.Y') : '-' }}
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Код ответа страницы-донора:</label>
                        {{ $donor->status_code ?? '-' }}
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Цена:</label>
                        {{ $donor->price ? number_format($donor->price, 2) . ' ₽' : '-' }}
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Площадка закупки:</label>
                        {{ $donor->marketplace ?? '-' }}
                    </div>
                </div>
            </div>

            <!-- Anchor Links -->
            <div class="row mt-4">
                <div class="col-12">
                    <h6>Ссылки на страницы сайта:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Анкор</th>
                                    <th>Страница</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($donor->pages as $p)
                                    <tr>
                                        <td>{{ $p->pivot->anchor ?? '-' }}</td>
                                        <td>{{ $p->url }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('projects.pages.donors.edit', [$project, $page, $donor]) }}" class="btn btn-primary">Редактировать</a>
            <a href="{{ route('projects.pages.donors.index', [$project, $page]) }}" class="btn btn-secondary">Назад</a>
        </div>
    </div>
</div>
@endsection
