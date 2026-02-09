@extends('layout.layout2')

@php
$title = 'Настройки';
$subTitle = 'Настройки / Общие';
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
                            <button class="nav-link px-16 py-10 active" id="pills-general-tab" data-bs-toggle="pill" data-bs-target="#pills-general" type="button" role="tab" aria-controls="pills-general" aria-selected="true">Общие</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link px-16 py-10" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Профиль</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link px-16 py-10" id="pills-security-tab" data-bs-toggle="pill" data-bs-target="#pills-security" type="button" role="tab" aria-controls="pills-security" aria-selected="false">Безопасность</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link px-16 py-10" id="pills-notifications-tab" data-bs-toggle="pill" data-bs-target="#pills-notifications" type="button" role="tab" aria-controls="pills-notifications" aria-selected="false">Уведомления</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link px-16 py-10" id="pills-ranking-services-tab" data-bs-toggle="pill" data-bs-target="#pills-ranking-services" type="button" role="tab" aria-controls="pills-ranking-services" aria-selected="false">Сервисы проверки позиций</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link px-16 py-10" id="pills-yandex-tab" data-bs-toggle="pill" data-bs-target="#pills-yandex" type="button" role="tab" aria-controls="pills-yandex" aria-selected="false">Яндекс</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-general" role="tabpanel" aria-labelledby="pills-general-tab" tabindex="0">
                            <div>
                                <h6 class="text-lg mb-8">Общие настройки</h6>
                                <p class="text-secondary-light mb-0">Содержимое вкладки Общие будет здесь...</p>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
                            <div>
                                <h6 class="text-lg mb-8">Профиль</h6>
                                <p class="text-secondary-light mb-0">Содержимое вкладки Профиль будет здесь...</p>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-security" role="tabpanel" aria-labelledby="pills-security-tab" tabindex="0">
                            <div>
                                <h6 class="text-lg mb-8">Безопасность</h6>
                                <p class="text-secondary-light mb-0">Содержимое вкладки Безопасность будет здесь...</p>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-notifications" role="tabpanel" aria-labelledby="pills-notifications-tab" tabindex="0">
                            <div>
                                <h6 class="text-lg mb-8">Уведомления</h6>
                                <p class="text-secondary-light mb-0">Содержимое вкладки Уведомления будет здесь...</p>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-ranking-services" role="tabpanel" aria-labelledby="pills-ranking-services-tab" tabindex="0">
                            <div>
                                <h6 class="text-lg mb-8">Сервисы проверки позиций</h6>
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
                                <form class="row g-3" method="POST" action="{{ route('settings.store') }}">
                                    @csrf
                                    <div class="col-12">
                                        <label class="form-label" for="api_url">URL API сервиса</label>
                                        <input type="text" class="form-control" id="api_url" name="api_url" placeholder="https://api.example.com/rank-check" value="{{ old('api_url', $settings?->api_url) }}">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="groupby">Топ позиций</label>
                                        <input type="number" class="form-control" id="groupby" name="groupby" value="{{ old('groupby', $settings?->groupby ?? 10) }}" min="1" max="100">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="lr">Регион (ID)</label>
                                        <input type="number" class="form-control" id="lr" name="lr" placeholder="213" value="{{ old('lr', $settings?->lr) }}">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="domain">Домен Яндекс</label>
                                        <select class="form-select" id="domain" name="domain">
                                            <option value="ru" {{ old('domain', $settings?->domain ?? 'ru') === 'ru' ? 'selected' : '' }}>ru</option>
                                            <option value="com" {{ old('domain', $settings?->domain ?? 'ru') === 'com' ? 'selected' : '' }}>com</option>
                                            <option value="ua" {{ old('domain', $settings?->domain ?? 'ru') === 'ua' ? 'selected' : '' }}>ua</option>
                                            <option value="com.tr" {{ old('domain', $settings?->domain ?? 'ru') === 'com.tr' ? 'selected' : '' }}>com.tr</option>
                                            <option value="by" {{ old('domain', $settings?->domain ?? 'ru') === 'by' ? 'selected' : '' }}>by</option>
                                            <option value="kz" {{ old('domain', $settings?->domain ?? 'ru') === 'kz' ? 'selected' : '' }}>kz</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="lang">Язык</label>
                                        <select class="form-select" id="lang" name="lang">
                                            <option value="ru" {{ old('lang', $settings?->lang ?? 'ru') === 'ru' ? 'selected' : '' }}>ru</option>
                                            <option value="uk" {{ old('lang', $settings?->lang ?? 'ru') === 'uk' ? 'selected' : '' }}>uk</option>
                                            <option value="en" {{ old('lang', $settings?->lang ?? 'ru') === 'en' ? 'selected' : '' }}>en</option>
                                            <option value="tr" {{ old('lang', $settings?->lang ?? 'ru') === 'tr' ? 'selected' : '' }}>tr</option>
                                            <option value="be" {{ old('lang', $settings?->lang ?? 'ru') === 'be' ? 'selected' : '' }}>be</option>
                                            <option value="kk" {{ old('lang', $settings?->lang ?? 'ru') === 'kk' ? 'selected' : '' }}>kk</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="device">Устройство</label>
                                        <select class="form-select" id="device" name="device">
                                            <option value="desktop" {{ old('device', $settings?->device ?? 'desktop') === 'desktop' ? 'selected' : '' }}>desktop</option>
                                            <option value="tablet" {{ old('device', $settings?->device ?? 'desktop') === 'tablet' ? 'selected' : '' }}>tablet</option>
                                            <option value="mobile" {{ old('device', $settings?->device ?? 'desktop') === 'mobile' ? 'selected' : '' }}>mobile</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="page">Страница результатов</label>
                                        <input type="number" class="form-control" id="page" name="page" value="{{ old('page', $settings?->page ?? 0) }}" min="0">
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">Сохранить настройки</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-yandex" role="tabpanel" aria-labelledby="pills-yandex-tab" tabindex="0">
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
                                <form class="row g-3" method="POST" action="{{ route('settings.store') }}">
                                    @csrf
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="yandex_client_id">Client ID</label>
                                        <input type="text" class="form-control" id="yandex_client_id" name="yandex_client_id" placeholder="1234567890" value="{{ old('yandex_client_id', $settings?->yandex_client_id) }}">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="yandex_client_secret">Client Secret</label>
                                        <input type="text" class="form-control" id="yandex_client_secret" name="yandex_client_secret" placeholder="abc123def456" value="{{ old('yandex_client_secret', $settings?->yandex_client_secret) }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="yandex_redirect_uri">Redirect URI</label>
                                        <input type="text" class="form-control" id="yandex_redirect_uri" name="yandex_redirect_uri" placeholder="https://site.com/auth/yandex/callback" value="{{ old('yandex_redirect_uri', $settings?->yandex_redirect_uri) }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="yandex_metrika_token">Токен</label>
                                        <input type="text" class="form-control" id="yandex_metrika_token" name="yandex_metrika_token" placeholder="AQAAAAABC123..." value="{{ old('yandex_metrika_token', $settings?->yandex_metrika_token) }}">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="yandex_metrika_counter">Счетчик</label>
                                        <input type="text" class="form-control" id="yandex_metrika_counter" name="yandex_metrika_counter" placeholder="12345678" value="{{ old('yandex_metrika_counter', $settings?->yandex_metrika_counter) }}">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="yandex_metrika_period">Период</label>
                                        <input type="text" class="form-control" id="yandex_metrika_period" name="yandex_metrika_period" placeholder="30d" value="{{ old('yandex_metrika_period', $settings?->yandex_metrika_period) }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="yandex_metrika_metrics">Метрики</label>
                                        <textarea class="form-control" id="yandex_metrika_metrics" name="yandex_metrika_metrics" rows="3" placeholder="ym:s:visits,ym:s:pageviews">{{ old('yandex_metrika_metrics', $settings?->yandex_metrika_metrics) }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="yandex_metrika_filters">Фильтры</label>
                                        <textarea class="form-control" id="yandex_metrika_filters" name="yandex_metrika_filters" rows="3" placeholder="ym:s:trafficSource=='organic'">{{ old('yandex_metrika_filters', $settings?->yandex_metrika_filters) }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="yandex_metrika_dimensions">Группировки (Dimensions)</label>
                                        <textarea class="form-control" id="yandex_metrika_dimensions" name="yandex_metrika_dimensions" rows="3" placeholder="ym:s:date,ym:s:trafficSource">{{ old('yandex_metrika_dimensions', $settings?->yandex_metrika_dimensions) }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="yandex_metrika_sort">Сортировка</label>
                                        <input type="text" class="form-control" id="yandex_metrika_sort" name="yandex_metrika_sort" placeholder="ym:s:visits" value="{{ old('yandex_metrika_sort', $settings?->yandex_metrika_sort) }}">
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
