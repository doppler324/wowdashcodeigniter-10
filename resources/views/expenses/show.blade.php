@extends('layout.layout')

@section('content')
<div class="page-body">
    @include('components.breadcrumb', ['pageTitle' => 'Просмотр расхода'])
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Просмотр расхода</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Тип расхода</label>
                            <p>{{ $expense->type_name }}</p>
                        </div>
                        <div class="form-group">
                            <label>Сумма</label>
                            <p>{{ number_format($expense->amount, 2) }} ₽</p>
                        </div>
                        <div class="form-group">
                            <label>Страница</label>
                            <p>{{ $expense->page?->title ?? '-' }}</p>
                        </div>
                         <div class="form-group">
                             <label>Донор</label>
                             <p>{{ $expense->donor?->domain ?? '-' }}</p>
                         </div>
                         <div class="form-group">
                             <label>Комментарий</label>
                             <p>{{ $expense->comment ?? '-' }}</p>
                         </div>

                         <div class="form-group">
                             <label>Дата создания</label>
                            <p>{{ $expense->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                        <div class="form-group">
                            <label>Дата обновления</label>
                            <p>{{ $expense->updated_at->format('d.m.Y H:i') }}</p>
                        </div>
                        <a href="{{ route('projects.expenses.edit', [$project, $expense]) }}" class="btn btn-warning">Редактировать</a>
                        <a href="{{ route('projects.expenses.index', $project) }}" class="btn btn-secondary">Назад</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
