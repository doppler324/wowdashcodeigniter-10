@props([
    'breadcrumbs' => [], // Обязательный параметр
])

{{-- Минимальная отладка --}}
<!-- BREADCRUMBS DEBUG: count={{ count($breadcrumbs) }} -->

@if(count($breadcrumbs) > 0)
<div class="d-flex flex-wrap align-items-center justify-content-start gap-3 mb-24">
    <ul class="d-flex align-items-center gap-2 mb-0 breadcrumb-list">
        @foreach($breadcrumbs as $index => $crumb)
            <li class="fw-medium breadcrumb-item">
                @if(isset($crumb['url']) && $crumb['url'] && !$loop->last)
                    <a href="{{ $crumb['url'] }}" class="d-flex align-items-center gap-1 breadcrumb-link">
                        @if($index === 0)
                            <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg breadcrumb-icon"></iconify-icon>
                        @endif
                        <span class="breadcrumb-text">{{ $crumb['title'] }}</span>
                    </a>
                @else
                    <span class="d-flex align-items-center gap-1 breadcrumb-current">
                        @if($index === 0 && !isset($crumb['url']))
                            <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg breadcrumb-icon"></iconify-icon>
                        @endif
                        <span class="breadcrumb-text">{{ $crumb['title'] }}</span>
                    </span>
                @endif
            </li>
            @if(!$loop->last)
                <li class="breadcrumb-separator">/</li>
            @endif
        @endforeach
    </ul>
</div>

<style>
    .breadcrumb-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .breadcrumb-item {
        display: flex;
        align-items: center;
    }

    .breadcrumb-link {
        color: var(--breadcrumb-link-color, #6c757d);
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .breadcrumb-link:hover {
        color: var(--breadcrumb-link-hover-color, #007bff);
    }

    .breadcrumb-current {
        color: var(--breadcrumb-current-color, #007bff);
        font-weight: 600;
    }

    .breadcrumb-separator {
        color: var(--breadcrumb-separator-color, #adb5bd);
        margin: 0 0.5rem;
        user-select: none;
        font-weight: 400;
        opacity: 0.8;
    }

    .breadcrumb-icon {
        color: var(--breadcrumb-icon-color, #6c757d);
    }

    .breadcrumb-text {
        font-size: 0.875rem;
    }

    /* Темная тема */
    [data-theme="dark"] .breadcrumb-link {
        color: var(--breadcrumb-link-color-dark, #adb5bd);
    }

    [data-theme="dark"] .breadcrumb-link:hover {
        color: var(--breadcrumb-link-hover-color-dark, #6ea8fe);
    }

    [data-theme="dark"] .breadcrumb-current {
        color: var(--breadcrumb-current-color-dark, #6ea8fe);
    }

    [data-theme="dark"] .breadcrumb-separator {
        color: var(--breadcrumb-separator-color-dark, #6c757d);
        opacity: 1;
        font-weight: 500;
    }

    [data-theme="dark"] .breadcrumb-icon {
        color: var(--breadcrumb-icon-color-dark, #adb5bd);
    }

    /* Высокий контраст для разделителей */
    .breadcrumb-separator {
        filter: contrast(1.2);
    }

    [data-theme="dark"] .breadcrumb-separator {
        filter: contrast(1.5);
    }
</style>
@endif
