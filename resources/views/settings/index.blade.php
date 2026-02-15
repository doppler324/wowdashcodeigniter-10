@extends('layout.layout2')

@php
$title = 'Настройки';
$subTitle = 'Настройки / Яндекс';
@endphp

@section('content')
    <div class="row gy-4">
        <div class="col-xxl-12">
            <div class="card p-0 overflow-hidden position-relative radius-12 h-100">
                <div class="card-header py-16 px-24 bg-base border border-end-0 border-start-0 border-top-0">
                    <h6 class="text-lg mb-0">Основные настройки</h6>
                </div>
                <div class="card-body p-24 pt-10">
                     <ul class="nav bordered-tab border border-top-0 border-start-0 border-end-0 d-inline-flex nav-pills mb-16" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link px-16 py-10 active" id="pills-yandex-tab" data-bs-toggle="pill" data-bs-target="#pills-yandex" type="button" role="tab" aria-controls="pills-yandex" aria-selected="true">Яндекс</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-yandex" role="tabpanel" aria-labelledby="pills-yandex-tab" tabindex="0">
                            <div>
                                <h6 class="text-lg mb-8">Яндекс Метрика</h6>
                                @if(session('success'))
                                    <div class="col-12">
                                        <div class="alert alert-success">
                                            {{ session('success') }}
                                        </div>
                                    </div>
                                @endif
                                @if($errors->any())
                                    <div class="col-12">
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                @foreach($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                                <form class="row g-3" method="POST" action="{{ route('settings.update') }}">
                                    @method('PUT')
                                    @csrf
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="yandex_client_id">Client ID</label>
                                        <input type="text" class="form-control" id="yandex_client_id" name="yandex_client_id" placeholder="1234567890" value="{{ old('yandex_client_id', $settings?->yandex_client_id) }}">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="yandex_metrika_counter">Счетчик</label>
                                        <input type="text" class="form-control" id="yandex_metrika_counter" name="yandex_metrika_counter" placeholder="12345678" value="{{ old('yandex_metrika_counter', $settings?->yandex_metrika_counter) }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="yandex_metrika_token">Токен</label>
                                        <input type="text" class="form-control" id="yandex_metrika_token" name="yandex_metrika_token" placeholder="AQAAAAABC123..." value="{{ old('yandex_metrika_token', $settings?->yandex_metrika_token) }}">
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">Сохранить настройки</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
