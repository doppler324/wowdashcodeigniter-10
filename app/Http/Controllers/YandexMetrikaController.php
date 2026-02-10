<?php

namespace App\Http\Controllers;

use App\Services\YandexMetrikaService;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class YandexMetrikaController extends Controller
{
    /**
     * Сервис для работы с API Яндекс Метрики.
     *
     * @var YandexMetrikaService
     */
    protected $yandexMetrikaService;

    /**
     * Конструктор контроллера.
     *
     * @param YandexMetrikaService $yandexMetrikaService
     */
    public function __construct(YandexMetrikaService $yandexMetrikaService)
    {
        $this->yandexMetrikaService = $yandexMetrikaService;
    }

    /**
     * Получение данных из API Яндекс Метрики (метод bytime) с кэшированием.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData(Request $request)
    {
        try {
            // Проверка авторизации пользователя
            if (!auth()->check()) {
                return response()->json(['error' => 'Не авторизован'], 401);
            }

            // Получение настроек пользователя (для токена и счетчика)
            $settings = Setting::where('user_id', auth()->id())->first();

            if (!$settings || empty($settings->yandex_metrika_token)) {
                return response()->json(['error' => 'Токен Яндекс Метрики не настроен'], 400);
            }

            // Валидация входящих параметров
            $validated = $request->validate([
                'ids' => 'required|string', // ID счетчика
                'date1' => 'required|date', // Дата начала периода
                'date2' => 'required|date|after_or_equal:date1', // Дата конца периода
                'metrics' => 'required|string', // Метрики (например, "ym:s:visits,ym:s:pageviews")
                'dimensions' => 'nullable|string', // Разбивка по меткам (например, "ym:s:date")
            ]);

            // Переопределяем токен сервиса из настроек пользователя
            $this->yandexMetrikaService = new YandexMetrikaService($settings->yandex_metrika_token);

            // Получение данных с кэшированием
            $data = $this->yandexMetrikaService->getData($validated);

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Ошибка при работе с API Яндекс Метрики', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'error' => 'Ошибка при получении данных',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
