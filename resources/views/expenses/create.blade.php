@extends('layout.layout')

@section('content')
<div class="page-body">
    @include('components.breadcrumb', ['pageTitle' => 'Добавить расход'])
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Добавить расход</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('projects.expenses.store', $project) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="type">Тип расхода</label>
                                <select name="type" id="type" class="form-control">
                                    @foreach($types as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="amount">Сумма</label>
                                <input type="number" name="amount" id="amount" class="form-control" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label for="page_id">Страница</label>
                                <select name="page_id" id="page_id" class="form-control">
                                    <option value="">- Не выбрано -</option>
                                    @foreach($pages as $page)
                                        <option value="{{ $page->id }}">{{ $page->title ?: $page->url }}</option>
                                    @endforeach
                                </select>
                                @if($pages->isEmpty())
                                    <small class="form-text text-muted">
                                        Нет страниц. <a href="{{ route('projects.pages.create', $project) }}">Создать страницу</a>
                                    </small>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="donor_id">Донор</label>
                                <select name="donor_id" id="donor_id" class="form-control">
                                    <option value="">- Не выбрано -</option>
                                    @foreach($donors as $donor)
                                        <option value="{{ $donor->id }}">{{ $donor->domain ?: $donor->link }}</option>
                                    @endforeach
                                </select>
                                @if($donors->isEmpty())
                                    <small class="form-text text-muted">
                                        Нет доноров. <a href="{{ route('projects.donors.index', $project) }}">Перейти к донорам</a>
                                    </small>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="comment">Комментарий</label>
                                <textarea name="comment" id="comment" class="form-control" rows="3" placeholder="Необязательный комментарий"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Добавить</button>
                            <a href="{{ route('projects.expenses.index', $project) }}" class="btn btn-secondary">Отмена</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
