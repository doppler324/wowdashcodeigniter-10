@php
    $pageTitle = 'Расходы';
    $subTitle = 'Проект: ' . $project->name;
    $breadcrumbs = [
        ['title' => 'Проекты', 'url' => route('projects.index')],
        ['title' => $project->name, 'url' => route('projects.show', $project)],
        ['title' => 'Расходы', 'url' => null],
    ];
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
                plotOptions: {
                    pie: {
                        donut: {
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: \'Общая сумма\',
                                    formatter: function(w) {
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0) + \' ₽\';
                                    },
                                    style: {
                                        fontSize: \'24px\',
                                        fontWeight: \'600\'
                                    }
                                }
                            }
                        }
                    }
                },
                legend: {
                    show: true,
                    position: \'right\',
                    formatter: function(seriesName, opts) {
                        var total = opts.w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                        var value = opts.w.globals.series[opts.seriesIndex];
                        var percent = total > 0 ? Math.round((value / total) * 100) : 0;
                        return seriesName + \': \' + value + \' ₽ (\' + percent + \'%)\';
                    },
                    labels: {
                        style: {
                            fontSize: \'25px\',
                            fontWeight: \'500\',
                            lineHeight: \'35px\'
                        }
                    },
                    offsetX: -20
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
    <div class="container-fluid">
        <div class="row mt-4">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Расходы проекта: {{ $project->name }}</h5>
                        <a href="{{ route('projects.expenses.create', $project) }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus"></i> Добавить расход
                        </a>
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
                                         <th>Комментарий</th>
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
                                             <td>{{ Str::limit($expense->comment, 50) ?? '-' }}</td>
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
        <div class="row mt-24">
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Распределение расходов по типам</h5>
                    </div>
                    <div class="card-body p-24">
                        <div class="mb-8">
                            <div id="expensesDonutChart"></div>
                        </div>
                        <style>
                            /* Размер шрифта легенды 25px и line-height */
                            .apexcharts-legend-text {
                                font-size: 25px !important;
                                font-weight: 500;
                                line-height: 35px !important;
                            }
                            /* Прижать легенду к графику */
                            .apexcharts-legend {
                                margin-right: 0 !important;
                                padding-right: 0 !important;
                            }
                            /* Цвет общей суммы под цвет сайта (primary) */
                            .apexcharts-datalabel-label {
                                fill: var(--bs-primary, #0d6efd) !important;
                                font-size: 24px !important;
                                font-weight: 600;
                            }
                            .apexcharts-datalabel-value {
                                fill: var(--bs-primary, #0d6efd) !important;
                                font-size: 24px !important;
                                font-weight: 600;
                            }
                        </style>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
