@extends('layout.layout')

@section('content')
<div class="page-body">
    @include('components.breadcrumb', ['pageTitle' => 'Расходы'])
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Расходы проекта: {{ $project->name }}</h5>
                        <div class="card-header-right">
                            <a href="{{ route('projects.expenses.create', $project) }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> Добавить расход
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Тип</th>
                                        <th>Сумма</th>
                                        <th>Страница</th>
                                        <th>Донор</th>
                                        <th>Дата создания</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expenses as $expense)
                                        <tr>
                                            <td>{{ $expense->type_name }}</td>
                                            <td>{{ number_format($expense->amount, 2) }} ₽</td>
                                            <td>{{ $expense->page?->title ?? '-' }}</td>
                                            <td>{{ $expense->donor?->domain ?? '-' }}</td>
                                            <td>{{ $expense->created_at->format('d.m.Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('projects.expenses.show', [$project, $expense]) }}" class="btn btn-info btn-sm">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('projects.expenses.edit', [$project, $expense]) }}" class="btn btn-warning btn-sm">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('projects.expenses.destroy', [$project, $expense]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Вы уверены?')">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $expenses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
