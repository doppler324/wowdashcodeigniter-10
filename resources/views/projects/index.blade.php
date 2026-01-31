@extends('layout.layout')

@php
$title = 'Проекты';
$subTitle = 'Список проектов';
@endphp

@section('content')

<div class="card h-100 p-0 radius-12">
    <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
        <div class="d-flex align-items-center flex-wrap gap-3">
            <h4 class="mb-0">Список проектов</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap gap-3">
            <a href="{{ route('projects.create') }}" class="btn btn-primary">Добавить проект</a>
        </div>
    </div>
    <div class="card-body p-24">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($projects->count() > 0)
            <div class="table-responsive scroll-sm">
                <table class="table basic-table mb-0">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Название</th>
                            <th scope="col">Домен</th>
                            <th scope="col">Описание</th>
                            <th scope="col">Дата создания</th>
                            <th scope="col">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projects as $project)
                        <tr>
                            <td>{{ $project->id }}</td>
                            <td><a href="{{ route('projects.show', $project) }}" class="text-primary-600">{{ $project->name }}</a></td>
                            <td>{{ $project->domain ?? '-' }}</td>
                            <td>{{ Str::limit($project->description, 50) }}</td>
                            <td>{{ $project->created_at->format('d.m.Y H:i') }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ route('projects.edit', $project) }}" class="w-32-px h-32-px bg-success-focus text-success-600 rounded-circle d-flex justify-content-center align-items-center">
                                        <iconify-icon icon="uil:edit"></iconify-icon>
                                    </a>
                                    <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить этот проект?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-32-px h-32-px bg-danger-focus text-danger-600 rounded-circle d-flex justify-content-center align-items-center">
                                            <iconify-icon icon="uil:trash-alt"></iconify-icon>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-20">
                <p class="text-gray-500">Проекты не найдены. <a href="{{ route('projects.create') }}">Создать первый проект</a>.</p>
            </div>
        @endif
    </div>
</div>

@endsection