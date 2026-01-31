@php
$hasChildren = $pages->where('parent_id', $page->id)->count() > 0;
$paddingLeft = $page->nesting_level * 10;
@endphp

<tr data-page-id="{{ $page->id }}" data-parent-id="{{ $page->parent_id ?? '' }}" @if($page->parent_id) style="display: none;" @endif>
    <td>
        @if($hasChildren)
            <button type="button" id="toggle-{{ $page->id }}" class="btn btn-sm btn-light p-1" onclick="toggleChildren({{ $page->id }})" data-expanded="false">
                <iconify-icon icon="uil:plus"></iconify-icon>
            </button>
        @endif
    </td>
    <td>{{ $page->id }}</td>
    <td>
        <div style="padding-left: {{ $paddingLeft }}px;">
            <a href="{{ $page->url }}" target="_blank" class="text-primary-600">{{ Str::limit($page->url, 50) }}</a>
            @if($page->title)
                <br><small class="text-muted">{{ Str::limit($page->title, 40) }}</small>
            @endif
        </div>
    </td>
    <td>
        @switch($page->type)
            @case('home') <span class="badge bg-primary">Главная</span> @break
            @case('section') <span class="badge bg-info">Раздел</span> @break
            @case('card') <span class="badge bg-secondary">Карточка</span> @break
        @endswitch
    </td>
    <td>{{ $page->incoming_links_count }}</td>
    <td>
        @if($page->status_code)
            <span class="bg-{{ $page->status_code == 200 ? 'success' : 'danger' }}-focus text-{{ $page->status_code == 200 ? 'success' : 'danger' }}-main px-24 py-4 rounded-pill fw-medium text-sm">{{ $page->status_code }}</span>
        @else
            -
        @endif
    </td>
    <td>
        @if($page->is_indexable)
            <span class="bg-success-focus text-success-main px-24 py-4 rounded-pill fw-medium text-sm">Да</span>
        @else
            <span class="bg-danger-focus text-danger-main px-24 py-4 rounded-pill fw-medium text-sm">Нет</span>
        @endif
    </td>
    <td>{{ $page->nesting_level }}</td>
    <td>
        <a href="{{ route('projects.pages.show', [$project, $page]) }}" class="w-32-px h-32-px bg-primary-light text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center">
            <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
        </a>
        <a href="{{ route('projects.pages.edit', [$project, $page]) }}" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center">
            <iconify-icon icon="lucide:edit"></iconify-icon>
        </a>
        <form action="{{ route('projects.pages.destroy', [$project, $page]) }}" method="POST" class="d-inline" onsubmit="return confirm('Вы уверены, что хотите удалить эту страницу?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center border-0">
                <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
            </button>
        </form>
    </td>
</tr>

@if($hasChildren)
    @foreach($pages->where('parent_id', $page->id) as $childPage)
        @include('projects._page_row', ['page' => $childPage, 'project' => $project, 'pages' => $pages])
    @endforeach
@endif
