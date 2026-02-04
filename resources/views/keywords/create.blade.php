@extends('layout.layout')

@php
$title = 'Добавить ключевое слово';
$subTitle = 'Создание нового ключевого слова для страницы';
@endphp

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Create Keyword</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Add New Keyword</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('projects.pages.keywords.store', [$page->project, $page]) }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="keyword">Keyword</label>
                    <input type="text" name="keyword" id="keyword" class="form-control @error('keyword') is-invalid @enderror" value="{{ old('keyword') }}" required>
                    @error('keyword')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="is_main">Основное ключевое слово</label>
                    <input type="checkbox" name="is_main" id="is_main" value="1" {{ old('is_main') ? 'checked' : '' }}>
                </div>

                <div class="form-group">
                    <label for="volume">Volume</label>
                    <input type="number" name="volume" id="volume" class="form-control @error('volume') is-invalid @enderror" value="{{ old('volume') }}">
                    @error('volume')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="volume_exact">Exact Volume</label>
                    <input type="number" name="volume_exact" id="volume_exact" class="form-control @error('volume_exact') is-invalid @enderror" value="{{ old('volume_exact') }}">
                    @error('volume_exact')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="cpc">CPC</label>
                    <input type="number" name="cpc" id="cpc" class="form-control @error('cpc') is-invalid @enderror" value="{{ old('cpc') }}" step="0.01">
                    @error('cpc')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="difficulty">Difficulty</label>
                    <input type="number" name="difficulty" id="difficulty" class="form-control @error('difficulty') is-invalid @enderror" value="{{ old('difficulty') }}" min="0" max="100">
                    @error('difficulty')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="current_position">Current Position</label>
                    <input type="number" name="current_position" id="current_position" class="form-control @error('current_position') is-invalid @enderror" value="{{ old('current_position') }}">
                    @error('current_position')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="best_position">Best Position</label>
                    <input type="number" name="best_position" id="best_position" class="form-control @error('best_position') is-invalid @enderror" value="{{ old('best_position') }}">
                    @error('best_position')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="start_position">Start Position</label>
                    <input type="number" name="start_position" id="start_position" class="form-control @error('start_position') is-invalid @enderror" value="{{ old('start_position') }}">
                    @error('start_position')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="trend">Trend</label>
                    <input type="number" name="trend" id="trend" class="form-control @error('trend') is-invalid @enderror" value="{{ old('trend') }}">
                    @error('trend')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="region">Region</label>
                    <input type="text" name="region" id="region" class="form-control @error('region') is-invalid @enderror" value="{{ old('region') }}">
                    @error('region')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="actual_url">Actual URL</label>
                    <input type="url" name="actual_url" id="actual_url" class="form-control @error('actual_url') is-invalid @enderror" value="{{ old('actual_url') }}">
                    @error('actual_url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="last_tracked_at">Last Tracked At</label>
                    <input type="datetime-local" name="last_tracked_at" id="last_tracked_at" class="form-control @error('last_tracked_at') is-invalid @enderror" value="{{ old('last_tracked_at') }}">
                    @error('last_tracked_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Create Keyword</button>
                <a href="{{ route('projects.pages.show', [$page->project, $page]) }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
