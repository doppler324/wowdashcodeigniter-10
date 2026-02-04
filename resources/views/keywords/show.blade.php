@extends('layout.layout')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Keyword Details</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ $keyword->keyword }}</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Page:</strong> {{ $keyword->page->url }}</p>
                    <p><strong>Main Keyword:</strong> {{ $keyword->is_main ? 'Yes' : 'No' }}</p>
                    <p><strong>Volume:</strong> {{ $keyword->volume }}</p>
                    <p><strong>Exact Volume:</strong> {{ $keyword->volume_exact }}</p>
                    <p><strong>CPC:</strong> {{ $keyword->cpc }}</p>
                    <p><strong>Difficulty:</strong> {{ $keyword->difficulty }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Current Position:</strong> {{ $keyword->current_position }}</p>
                    <p><strong>Best Position:</strong> {{ $keyword->best_position }}</p>
                    <p><strong>Start Position:</strong> {{ $keyword->start_position }}</p>
                    <p><strong>Trend:</strong> {{ $keyword->trend }}</p>
                    <p><strong>Region:</strong> {{ $keyword->region }}</p>
                    <p><strong>Actual URL:</strong> {{ $keyword->actual_url }}</p>
                </div>
            </div>
            <p><strong>Last Tracked At:</strong> {{ $keyword->last_tracked_at }}</p>

            <div class="mt-4">
                <a href="{{ route('projects.pages.keywords.edit', [$keyword->page->project, $keyword->page, $keyword]) }}" class="btn btn-warning">Edit</a>
                <a href="{{ route('projects.pages.show', [$keyword->page->project, $keyword->page]) }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
</div>
@endsection