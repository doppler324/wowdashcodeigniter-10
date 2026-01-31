@extends('layout.layout')

@php
$title = 'Страницы';
$subTitle = 'Страницы проекта: ' . $project->name;
@endphp

@section('content')

<div class="card h-100 p-0 radius-12">
    <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
        <div class="d-flex align-items-center flex-wrap gap-3">
            <h4 class="mb-0">Страницы проекта: {{ $project->name }}</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap gap-3">
            <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">Назад к проекту</a>
            <a href="{{ route('projects.pages.create', $project) }}" class="btn btn-primary">Добавить страницу</a>
        </div>
    </div>
    <div class="card-body p-24">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($pages->count() > 0)
            <div class="table-responsive scroll-sm">
                <table class="table basic-table mb-0">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">URL</th>
                            <th scope="col">Тип</th>
                            <th scope="col">Заголовок</th>
                            <th scope="col">Входящие ссылки</th>
                            <th scope="col">Статус</th>
                            <th scope="col">Индексация</th>
                            <th scope="col">Уровень</th>
                            <th scope="col">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pages as $page)
                        <tr>
                            <td>{{ $page->id }}</td>
                            <td><a href="{{ $page->url }}" target="_blank" class="text-primary-600">{{ Str::limit($page->url, 40) }}</a></td>
                            <td>
                                @switch($page->type)
                                    @case('home') Главная @break
                                    @case('section') Раздел @break
                                    @case('card') Карточка @break
                                @endswitch
                            </td>
                            <td>{{ $page->title ? Str::limit($page->title, 30) : '-' }}</td>
                            <td>{{ $page->incoming_links_count }}</td>
                            <td>
                                @if($page->status_code)
                                    <span class="badge {{ $page->status_code == 200 ? 'bg-success' : 'bg-danger' }}">{{ $page->status_code }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($page->is_indexable)
                                    <span class="badge bg-success">Да</span>
                                @else
                                    <span class="badge bg-danger">Нет</span>
                                @endif
                            </td>
                            <td>{{ $page->nesting_level }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ route('projects.pages.show', [$project, $page]) }}" class="w-32-px h-32-px bg-info-focus text-info-600 rounded-circle d-flex justify-content-center align-items-center">
                                        <iconify-icon icon="uil:eye"></iconify-icon>
                                    </a>
                                    <a href="{{ route('projects.pages.edit', [$project, $page]) }}" class="w-32-px h-32-px bg-success-focus text-success-600 rounded-circle d-flex justify-content-center align-items-center">
                                        <iconify-icon icon="uil:edit"></iconify-icon>
                                    </a>
                                    <form action="{{ route('projects.pages.destroy', [$project, $page]) }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить эту страницу?')">
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
            <div class="mt-3">
                {{ $pages->links() }}
            </div>
        @else
            <div class="text-center py-20">
                <p class="text-gray-500">Страницы не найдены. <a href="{{ route('projects.pages.create', $project) }}">Добавить первую страницу</a>.</p>
            </div>
        @endif
    </div>
</div>

@endsection
