@extends('layout.layout')

@section('content')
<div class="page-body">
    @include('components.breadcrumb', ['pageTitle' => 'Редактировать расход'])
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Редактировать расход</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('projects.expenses.update', [$project, $expense]) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="type">Тип расхода</label>
                                <select name="type" id="type" class="form-control">
                                    @foreach($types as $value => $label)
                                        <option value="{{ $value }}" {{ $expense->type == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="amount">Сумма</label>
                                <input type="number" name="amount" id="amount" class="form-control" step="0.01" value="{{ $expense->amount }}" required>
                            </div>
                            <div class="form-group">
                                <label for="page_id">Страница</label>
                                <select name="page_id" id="page_id" class="form-control">
                                    <option value="">- Не выбрано -</option>
                                    @foreach($pages as $page)
                                        <option value="{{ $page->id }}" {{ $expense->page_id == $page->id ? 'selected' : '' }}>{{ $page->title ?: $page->url }}</option>
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
                                        <option value="{{ $donor->id }}" {{ $expense->donor_id == $donor->id ? 'selected' : '' }}>{{ $donor->domain ?: $donor->link }}</option>
                                    @endforeach
                                </select>
                                @if($donors->isEmpty())
                                    <small class="form-text text-muted">
                                        Нет доноров. <a href="{{ route('projects.donors.index', $project) }}">Перейти к донорам</a>
                                    </small>
                                @endif
                            </div>

                            <button type="submit" class="btn btn-primary">Обновить</button>
                            <a href="{{ route('projects.expenses.index', $project) }}" class="btn btn-secondary">Отмена</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
