@extends('layout.layout')

@php
$title = 'Активности';
$subTitle = 'Проект: ' . $project->name;
@endphp

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4">
            <span class="text-muted fw-light">
                @if($page)
                    {{ $project->name }} / {{ $page->title }} /
                @else
                    {{ $project->name }} /
                @endif
            </span>
            Активности
        </h4>

        <div class="card">
            <div class="card-header border-0 pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Список активностей</h5>
                    <a href="{{ $page ? route('projects.pages.activities.create', [$project, $page]) : route('projects.activities.create', $project) }}"
                       class="btn btn-primary">
                        <i class="ri-add-line me-2"></i>
                        Добавить активность
                    </a>
                </div>
            </div>

            <div class="card-datatable table-responsive">
                <table class="table table-hover" id="activitiesTable">
                    <thead>
                    <tr>
                        <th>Дата события</th>
                        <th>Категория</th>
                        <th>Заголовок</th>
                        <th>Описание</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($activities as $activity)
                        <tr>
                            <td>{{ $activity->event_date->format('d.m.Y H:i') }}</td>
                            <td><span class="badge bg-primary">{{ $activity->category }}</span></td>
                            <td>{{ $activity->title }}</td>
                            <td>{{ Str::limit($activity->description, 100) }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <a href="{{ $page ? route('projects.pages.activities.edit', [$project, $page, $activity]) : route('projects.activities.edit', [$project, $activity]) }}"
                                       class="btn btn-sm btn-outline-primary me-2">
                                        <i class="ri-edit-line"></i>
                                    </a>
                                    <form action="{{ $page ? route('projects.pages.activities.destroy', [$project, $page, $activity]) : route('projects.activities.destroy', [$project, $activity]) }}"
                                          method="POST" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Вы уверены, что хотите удалить эту активность?')">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#activitiesTable').DataTable({
                responsive: true,
                language: {
                    search: 'Поиск:',
                    lengthMenu: 'Показывать _MENU_ записей',
                    info: 'Показано _START_ до _END_ из _TOTAL_ записей',
                    paginate: {
                        first: 'Первая',
                        last: 'Последняя',
                        next: 'Следующая',
                        previous: 'Предыдущая'
                    }
                }
            });
        });
    </script>
@endpush
