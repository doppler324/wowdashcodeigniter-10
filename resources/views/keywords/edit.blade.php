@extends('layout.layout')

@php
$title = 'Редактировать ключевое слово';
$subTitle = 'Редактирование ключевого слова: ' . $keyword->keyword;
@endphp

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Редактировать ключевое слово</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Редактировать ключевое слово</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('projects.pages.keywords.update', [$keyword->page->project, $keyword->page, $keyword]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="keyword">Ключевое слово</label>
                    <input type="text" name="keyword" id="keyword" class="form-control @error('keyword') is-invalid @enderror" value="{{ old('keyword', $keyword->keyword) }}" required>
                    @error('keyword')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="is_main">Основное ключевое слово</label>
                    <input type="checkbox" name="is_main" id="is_main" value="1" {{ old('is_main', $keyword->is_main) ? 'checked' : '' }}>
                </div>

                <div class="form-group">
                    <label for="volume">Частота</label>
                    <input type="number" name="volume" id="volume" class="form-control @error('volume') is-invalid @enderror" value="{{ old('volume', $keyword->volume) }}">
                    @error('volume')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="volume_exact">Частота точного совпадения</label>
                    <input type="number" name="volume_exact" id="volume_exact" class="form-control @error('volume_exact') is-invalid @enderror" value="{{ old('volume_exact', $keyword->volume_exact) }}">
                    @error('volume_exact')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="cpc">CPC</label>
                    <input type="number" name="cpc" id="cpc" class="form-control @error('cpc') is-invalid @enderror" value="{{ old('cpc', $keyword->cpc) }}" step="0.01">
                    @error('cpc')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="difficulty">Сложность</label>
                    <input type="number" name="difficulty" id="difficulty" class="form-control @error('difficulty') is-invalid @enderror" value="{{ old('difficulty', $keyword->difficulty) }}" min="0" max="100">
                    @error('difficulty')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="current_position">Current Position</label>
                    <input type="number" name="current_position" id="current_position" class="form-control @error('current_position') is-invalid @enderror" value="{{ old('current_position', $keyword->current_position) }}">
                    @error('current_position')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="best_position">Best Position</label>
                    <input type="number" name="best_position" id="best_position" class="form-control @error('best_position') is-invalid @enderror" value="{{ old('best_position', $keyword->best_position) }}">
                    @error('best_position')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="start_position">Start Position</label>
                    <input type="number" name="start_position" id="start_position" class="form-control @error('start_position') is-invalid @enderror" value="{{ old('start_position', $keyword->start_position) }}">
                    @error('start_position')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="trend">Trend</label>
                    <input type="number" name="trend" id="trend" class="form-control @error('trend') is-invalid @enderror" value="{{ old('trend', $keyword->trend) }}">
                    @error('trend')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="region">Region</label>
                    <input type="text" name="region" id="region" class="form-control @error('region') is-invalid @enderror" value="{{ old('region', $keyword->region) }}">
                    @error('region')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="actual_url">Actual URL</label>
                    <input type="url" name="actual_url" id="actual_url" class="form-control @error('actual_url') is-invalid @enderror" value="{{ old('actual_url', $keyword->actual_url) }}">
                    @error('actual_url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="last_tracked_at">Last Tracked At</label>
                    <input type="datetime-local" name="last_tracked_at" id="last_tracked_at" class="form-control @error('last_tracked_at') is-invalid @enderror" value="{{ old('last_tracked_at', $keyword->last_tracked_at ? $keyword->last_tracked_at->format('Y-m-d\TH:i') : '') }}">
                    @error('last_tracked_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Update Keyword</button>
                <a href="{{ route('projects.pages.show', [$keyword->page->project, $keyword->page]) }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
