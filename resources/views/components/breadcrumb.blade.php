@props([
    'pageTitle' => '',
    'subTitle' => '',
    'breadcrumbs' => null,
])

{{-- Используем глобальные $breadcrumbs если не переданы явно --}}
@php
    $breadcrumbs = $breadcrumbs ?? ($breadcrumbs ?? []);
@endphp

{{-- Отладка: выводим содержимое хлебных крошек --}}
@if(app()->environment('local'))
    <!-- DEBUG: Breadcrumb component -->
    <!-- Breadcrumbs count: {{ count($breadcrumbs) }} -->
    <!-- SubTitle: {{ $subTitle }} -->
@endif

<div class="d-flex flex-wrap align-items-center justify-content-start gap-3 mb-24">
    <ul class="d-flex align-items-center gap-2 mb-0">
        @if(count($breadcrumbs) > 0)
            @foreach($breadcrumbs as $index => $crumb)
                <li class="fw-medium">
                    @if(isset($crumb['url']) && $crumb['url'] && !$loop->last)
                        <a href="{{ $crumb['url'] }}" class="d-flex align-items-center gap-1 hover-text-primary">
                            @if($index === 0)
                                <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                            @endif
                            {{ $crumb['title'] }}
                        </a>
                    @else
                        <span class="d-flex align-items-center gap-1">
                            @if($index === 0 && !isset($crumb['url']))
                                <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                            @endif
                            {{ $crumb['title'] }}
                        </span>
                    @endif
                </li>
                @if(!$loop->last)
                    <li>-</li>
                @endif
            @endforeach
        @else
            {{-- Запасной вариант: используем subTitle если нет хлебных крошек --}}
            <li class="fw-medium">
                <a href="{{ route('projects.index') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            @if(isset($subTitle) && $subTitle)
                <li>-</li>
                <li class="fw-medium">{{ $subTitle }}</li>
            @endif
        @endif
    </ul>
</div>