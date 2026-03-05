<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\BreadcrumbService;

class BreadcrumbsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Этот middleware выполняется ПОСЛЕ контроллера
        // Генерируем или обновляем хлебные крошки здесь

        $breadcrumbService = app(BreadcrumbService::class);

        // Если хлебные крошки еще не сгенерированы, генерируем их
        if (empty($breadcrumbService->get())) {
            $breadcrumbService->generateFromRoute();
        }

        // Получаем текущие хлебные крошки
        $breadcrumbs = $breadcrumbService->get();

        // Логируем для отладки
        \Log::info('BREADCRUMBS DEBUG - Middleware:', [
            'route' => \Route::currentRouteName(),
            'breadcrumbs_count' => count($breadcrumbs),
            'breadcrumbs_data' => $breadcrumbs,
            'last_item' => !empty($breadcrumbs) ? end($breadcrumbs)['title'] ?? 'N/A' : 'N/A'
        ]);

        // Передаем хлебные крошки в представление
        // Для этого нужно получить содержимое ответа и добавить данные
        if ($response instanceof \Illuminate\Http\Response) {
            $content = $response->getContent();

            // Это сложный подход, проще использовать View Composer
            // Но View Composer выполняется до контроллера...
        }

        return $response;
    }
}