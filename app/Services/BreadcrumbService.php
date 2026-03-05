<?php

namespace App\Services;

use Illuminate\Support\Facades\Route;

class BreadcrumbService
{
    protected $breadcrumbs = [];
    protected $customTitles = [];
    protected $contextData = [];

    /**
     * Добавить элемент в хлебные крошки
     */
    public function add(string $title, ?string $url = null): self
    {
        $this->breadcrumbs[] = [
            'title' => $title,
            'url' => $url
        ];
        return $this;
    }

    /**
     * Установить кастомный заголовок для текущей страницы
     */
    public function setCustomTitle(string $title): self
    {
        $this->customTitles[Route::currentRouteName()] = $title;
        return $this;
    }

    /**
     * Установить контекстные данные (например, модель проекта)
     */
    public function setContextData(array $data): self
    {
        $this->contextData = array_merge($this->contextData, $data);
        return $this;
    }

    /**
     * Обновить последний элемент хлебных крошек
     */
    public function updateLastItem(string $title, ?string $url = null): self
    {
        if (!empty($this->breadcrumbs)) {
            $lastIndex = count($this->breadcrumbs) - 1;
            $this->breadcrumbs[$lastIndex]['title'] = $title;
            if ($url !== null) {
                $this->breadcrumbs[$lastIndex]['url'] = $url;
            }
        }
        return $this;
    }

    /**
     * Получить все хлебные крошки
     */
    public function get(): array
    {
        return $this->breadcrumbs;
    }

    /**
     * Очистить хлебные крошки
     */
    public function clear(): self
    {
        $this->breadcrumbs = [];
        return $this;
    }

    /**
     * Автоматически сгенерировать хлебные крошки на основе текущего маршрута
     */
    public function generateFromRoute(): self
    {
        $this->clear();

        $routeName = Route::currentRouteName();
        $segments = request()->segments();

        // Генерация на основе имени маршрута
        if ($routeName) {
            $this->generateFromRouteName($routeName);
        } else {
            // Генерация на основе URL сегментов
            $this->generateFromSegments($segments);
        }

        return $this;
    }

    /**
     * Генерация на основе имени маршрута
     */
    protected function generateFromRouteName(string $routeName): void
    {
        // Для маршрутов, начинающихся с projects., добавляем элемент "Проекты"
        if (str_starts_with($routeName, 'projects.')) {
            // Добавляем "Проекты" только если это не сам маршрут projects.index
            if ($routeName !== 'projects.index') {
                try {
                    $this->add('Проекты', route('projects.index'));
                } catch (\Exception $e) {
                    $this->add('Проекты', null);
                }
            }
        }

        $routeMap = [
            // Проекты
            'projects.index' => ['Проекты', null],
            'projects.create' => ['Проекты', route('projects.index')],
            'projects.show' => ['Проекты', route('projects.index')],
            'projects.edit' => ['Проекты', route('projects.index')],

            // Страницы проекта
            'projects.pages.index' => ['Страницы', null], // Контроллер добавит проект
            'projects.pages.create' => ['Страницы', null],
            'projects.pages.show' => ['Страницы', null],
            'projects.pages.edit' => ['Страницы', null],

            // Активности проекта
            'projects.activities.index' => ['Активности', null],
            'projects.activities.create' => ['Активности', null],
            'projects.activities.show' => ['Активности', null],
            'projects.activities.edit' => ['Активности', null],

            // Ключевые слова
            'keywords.index' => ['Ключевые слова', null],
            'keywords.create' => ['Ключевые слова', null],
            'keywords.edit' => ['Ключевые слова', null],
            'projects.keywords.index' => ['Ключевые слова', null],
            'projects.pages.keywords.create' => ['Ключевые слова', null],
            'projects.pages.keywords.edit' => ['Ключевые слова', null],

            // Доноры
            'donors.index' => ['Доноры', null],
            'donors.create' => ['Доноры', null],
            'donors.edit' => ['Доноры', null],
            'projects.donors.index' => ['Доноры', null],

            // Старые маршруты (для совместимости)
            'pages.index' => ['Страницы', null],
            'pages.create' => ['Страницы', null],
            'pages.edit' => ['Страницы', null],

            // Другие
            'settings.index' => ['Настройки', null],
            'expenses.index' => ['Расходы', null],
            'users.index' => ['Пользователи', null],
        ];

        if (isset($routeMap[$routeName])) {
            [$title, $url] = $routeMap[$routeName];

            // Добавляем элемент, даже если URL null (текущая страница)
            $this->add($title, $url);

            // Проверяем, есть ли кастомный заголовок для текущей страницы
            if (isset($this->customTitles[$routeName])) {
                $this->add($this->customTitles[$routeName]);
            } else {
                // Для детальных страниц используем контекстные данные или стандартные названия
                $this->addDetailPageTitle($routeName);
            }
        }
    }

    /**
     * Добавить заголовок для детальной страницы
     */
    protected function addDetailPageTitle(string $routeName): void
    {
        // Стандартные заголовки для разных типов страниц
        // Контроллеры могут обновить последний элемент через updateLastItem()
        if (in_array($routeName, ['projects.show', 'projects.activities.show', 'keywords.edit', 'donors.edit', 'pages.edit', 'projects.pages.keywords.edit'])) {
            $this->add('Детали');
        } elseif (in_array($routeName, ['projects.create', 'projects.activities.create', 'keywords.create', 'donors.create', 'pages.create', 'projects.pages.keywords.create'])) {
            $this->add('Создание');
        } elseif (in_array($routeName, ['projects.edit', 'projects.activities.edit'])) {
            $this->add('Редактирование');
        }
    }

    /**
     * Генерация на основе URL сегментов
     */
    protected function generateFromSegments(array $segments): void
    {
        $url = '';
        foreach ($segments as $segment) {
            $url .= '/' . $segment;
            $title = $this->formatSegmentTitle($segment);
            $this->add($title, $url);
        }
    }

    /**
     * Форматирование заголовка сегмента
     */
    protected function formatSegmentTitle(string $segment): string
    {
        $titles = [
            'projects' => 'Проекты',
            'activities' => 'Активности',
            'keywords' => 'Ключевые слова',
            'donors' => 'Доноры',
            'pages' => 'Страницы',
            'settings' => 'Настройки',
            'expenses' => 'Расходы',
            'users' => 'Пользователи',
            'create' => 'Создание',
            'edit' => 'Редактирование',
            'show' => 'Детали',
        ];

        return $titles[$segment] ?? ucfirst(str_replace(['-', '_'], ' ', $segment));
    }

    /**
     * Рендер хлебных крошек
     */
    public function render(): string
    {
        if (empty($this->breadcrumbs)) {
            $this->generateFromRoute();
        }

        return view('components.breadcrumbs', [
            'breadcrumbs' => $this->breadcrumbs
        ])->render();
    }

    /**
     * Статический метод для быстрого доступа
     */
    public static function make(): self
    {
        return app(self::class);
    }
}
