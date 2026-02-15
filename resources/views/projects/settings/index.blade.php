@extends('layout.layout')

@php
$title = 'Настройки проекта';
$subTitle = $project->name;
@endphp

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Настройки проекта "{{ $project->name }}"</h4>
                    </div>
                    <div class="card-body">
                        <!-- Tabs -->
                        <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="yandex-tab" data-bs-toggle="tab" data-bs-target="#yandex" type="button" role="tab" aria-controls="yandex" aria-selected="true">Яндекс</button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content mt-4" id="settingsTabsContent">
                            <!-- Яндекс Tab -->
                            <div class="tab-pane fade show active" id="yandex" role="tabpanel" aria-labelledby="yandex-tab">
                                <form action="{{ $setting->exists ? route('projects.settings.update', [$project, $setting]) : route('projects.settings.store', $project) }}" method="POST">
                                    @csrf
                                    @if ($setting->exists)
                                        @method('PUT')
                                    @endif

                                    <div class="mb-3">
                                        <label for="yandex_client_id" class="form-label">Yandex Client ID</label>
                                        <input type="text" class="form-control" id="yandex_client_id" name="yandex_client_id" value="{{ old('yandex_client_id', $setting->yandex_client_id) }}">
                                    </div>

                                    <div class="mb-3">
                                        <label for="yandex_metrika_token" class="form-label">Yandex Metrika Token</label>
                                        <input type="text" class="form-control" id="yandex_metrika_token" name="yandex_metrika_token" value="{{ old('yandex_metrika_token', $setting->yandex_metrika_token) }}">
                                    </div>

                                    <div class="mb-3">
                                        <label for="yandex_metrika_counter" class="form-label">Yandex Metrika Counter ID</label>
                                        <input type="text" class="form-control" id="yandex_metrika_counter" name="yandex_metrika_counter" value="{{ old('yandex_metrika_counter', $setting->yandex_metrika_counter) }}">
                                    </div>

                                    <button type="submit" class="btn btn-primary">Сохранить</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection