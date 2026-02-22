<aside class="sidebar">
    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a href="{{ route('projects.index') }}" class="sidebar-logo">
            <img src="{{ asset('assets/images/logo.png') }}" alt="site logo" class="light-logo">
            <img src="{{ asset('assets/images/logo-light.png') }}" alt="site logo" class="dark-logo">
            <img src="{{ asset('assets/images/logo-icon.png') }}" alt="site logo" class="logo-icon">
        </a>
    </div>
    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">
            <li>
                <a href="{{ route('projects.index') }}">
                    <iconify-icon icon="mdi:projector" class="menu-icon"></iconify-icon>
                    <span>Проекты</span>
                </a>
            </li>
            @if (request()->routeIs('projects.*'))
            @php
                $currentProject = null;
                if (request()->route('project')) {
                    $currentProject = request()->route('project');
                } elseif (request()->route('page')) {
                    $page = \App\Models\Page::find(request()->route('page'));
                    if ($page) {
                        $currentProject = $page->project;
                    }
                } elseif (request()->route('keyword')) {
                    $keyword = \App\Models\Keyword::find(request()->route('keyword'));
                    if ($keyword && $keyword->page) {
                        $currentProject = $keyword->page->project;
                    }
                } elseif (request()->route('donor')) {
                    $donor = \App\Models\Donor::find(request()->route('donor'));
                    if ($donor && $donor->pages->first()) {
                        $currentProject = $donor->pages->first()->project;
                    }
                } elseif (request()->route('activity')) {
                    $activity = \App\Models\Activity::find(request()->route('activity'));
                    if ($activity && $activity->project) {
                        $currentProject = $activity->project;
                    } elseif ($activity && $activity->page) {
                        $currentProject = $activity->page->project;
                    }
                }
            @endphp
            @if ($currentProject)
            <li>
                <a href="{{ route('projects.pages.index', $currentProject) }}">
                    <iconify-icon icon="mdi:format-list-bulleted-type" class="menu-icon"></iconify-icon>
                    <span>Страницы</span>
                </a>
            </li>
            <li>
                <a href="{{ route('projects.keywords.index', $currentProject) }}">
                    <iconify-icon icon="mdi:key-variant" class="menu-icon"></iconify-icon>
                    <span>Ключевые запросы</span>
                </a>
            </li>
            <li>
                <a href="{{ route('projects.activities.index', $currentProject) }}">
                    <iconify-icon icon="mdi:calendar-check" class="menu-icon"></iconify-icon>
                    <span>Задачи</span>
                </a>
            </li>
            <li>
                <a href="{{ route('projects.donors.index', $currentProject) }}">
                    <iconify-icon icon="mdi:link-variant" class="menu-icon"></iconify-icon>
                    <span>Доноры</span>
                </a>
            </li>
            <li>
                <a href="{{ route('projects.settings.index', $currentProject) }}">
                    <iconify-icon icon="icon-park-outline:setting-two" class="menu-icon"></iconify-icon>
                    <span>Настройки</span>
                </a>
            </li>
            @endif
            @endif

            <li class="sidebar-menu-group-title">Application</li>

        </ul>
    </div>
</aside>
