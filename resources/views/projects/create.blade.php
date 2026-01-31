@extends('layout.layout')

@php
$title = 'Добавить проект';
$subTitle = 'Создание нового проекта';
@endphp

@section('content')

<div class="card h-100 p-0 radius-12">
    <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
        <h4 class="mb-0">Добавить новый проект</h4>
        <a href="{{ route('projects.index') }}" class="btn btn-light">Назад к списку</a>
    </div>
    <div class="card-body p-24">
        <form action="{{ route('projects.store') }}" method="POST">
            @csrf
            <div class="row gy-4">
                <div class="col-12">
                    <label for="name" class="h5 mb-8">Название проекта <span class="text-danger">*</span></label>
                    <input type="text" class="form-control radius-8 @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12">
                    <label for="domain" class="h5 mb-8">Домен сайта</label>
                    <input type="text" class="form-control radius-8 @error('domain') is-invalid @enderror" id="domain" name="domain" value="{{ old('domain') }}">
                    @error('domain')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12">
                    <label for="description" class="h5 mb-8">Описание</label>
                    <textarea class="form-control radius-8 @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12">
                    <div class="flex-align gap-3">
                        <button type="submit" class="btn btn-primary">Создать проект</button>
                        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">Отмена</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection