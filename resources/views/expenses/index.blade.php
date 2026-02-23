@php
    $script = '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var options = {
                series: ' . json_encode($chartSeries) . ',
                chart: {
                    height: 264,
                    type: "donut",
                },
                colors: ' . json_encode($chartColors) . ',
                labels: ' . json_encode($chartLabels) . ',
                dataLabels: {
                    enabled: false
                },
                legend: {
                    show: false
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        }
                    }
                }]
            };
            var chart = new ApexCharts(document.querySelector("#expensesDonutChart"), options);
            chart.render();
        });
    </script>';
@endphp

@extends('layout.layout')

@section('content')
<div class="page-body">
    @include('components.breadcrumb', ['pageTitle' => 'Расходы'])
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Распределение расходов по типам</h5>
                    </div>
                    <div class="card-body p-24 text-center d-flex flex-wrap align-items-start gap-5 justify-content-center">
                        <div class="position-relative">
                            <div id="expensesDonutChart" class="w-auto d-inline-block"></div>
                            <div class="position-absolute start-50 top-50 translate-middle">
                                <span class="text-lg text-secondary-light fw-medium">Общая сумма</span>
                                <h4 class="mb-0">{{ round(array_sum($chartSeries)) }} ₽</h4>
                            </div>
                        </div>

                        <div class="max-w-290-px w-100">
                            <div class="d-flex align-items-center justify-content-between gap-12 border pb-12 mb-12 border-end-0 border-top-0 border-start-0">
                                <span class="text-primary-light fw-medium text-lg">Тип</span>
                                <span class="text-primary-light fw-medium text-lg">Сумма</span>
                                <span class="text-primary-light fw-medium text-lg">%</span>
                            </div>
                            @php
                                $total = array_sum($chartSeries);
                            @endphp
                            @foreach($chartLabels as $index => $label)
                                @php
                                    $value = $chartSeries[$index];
                                    $percentage = $total > 0 ? round(($value / $total) * 100) : 0;
                                    $color = $chartColors[$index] ?? '#cccccc';
                                @endphp
                                <div class="d-flex align-items-center justify-content-between gap-12 mb-12">
                                    <span class="text-primary-light fw-medium text-lg d-flex align-items-center gap-12">
                                        <span class="w-12-px h-12-px rounded-circle" style="background-color: {{ $color }}"></span> {{ $label }}
                                    </span>
                                    <span class="text-primary-light fw-medium text-lg">{{ round($value) }} ₽</span>
                                    <span class="text-primary-light fw-medium text-lg">{{ $percentage }}%</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
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
