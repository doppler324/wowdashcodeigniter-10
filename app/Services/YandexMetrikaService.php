<?php

namespace App\Services;

use App\Models\ApiCache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YandexMetrikaService
{
    /**
     * URL API Яндекс Метрики для обычных запросов.
     *
     * @var string
     */
    protected $apiUrl = 'https://api-metrika.yandex.net/stat/v1/data';

    /**
     * URL API Яндекс Метрики для запросов по времени.
     *
     * @var string
     */
    protected $apiUrlByTime = 'https://api-metrika.yandex.net/stat/v1/data/bytime';

    /**
     * Время жизни кэша в минутах.
     *
     * @var int
     */
    protected $cacheTTL = 60;

    /**
     * Конструктор сервиса.
     *
     * @param string|null $token
     */
    public function __construct(?string $token = null)
    {
        $this->token = $token ?? config('services.yandex_metrika.token');
    }

    /**
     * Получение данных из API Яндекс Метрики с кэшированием.
     *
     * @param array $params Параметры запроса (ids, date1, date2, metrics, dimensions, group)
     * @return array
     */
    public function getData(array $params): array
    {
        // Создаем уникальный хеш для запроса
        $requestHash = $this->generateRequestHash($params);
        Log::info('Request hash generated: ' . $requestHash);

        // Пытаемся получить данные из кэша
        $cachedData = $this->getCachedData($requestHash);
        Log::info('Cached data found: ' . json_encode($cachedData));

        // Если данные в кэше актуальны — возвращаем их
        if ($cachedData) {
            Log::info('Данные получены из кэша', ['hash' => $requestHash]);
            return $cachedData;
        }

        try {
            // Делаем запрос к API Яндекс Метрики
            Log::info('Sending API request to Yandex Metrika');
            $response = $this->sendApiRequest($params);
            Log::info('API response received: ' . json_encode($response));

            // Сохраняем данные в кэш
            Log::info('Saving data to cache');
            $this->saveToCache($requestHash, $response, $params);
            Log::info('Data saved to cache');

            Log::info('Данные получены из API и сохранены в кэш', ['hash' => $requestHash]);
            return $response;
        } catch (\Exception $e) {
            Log::error('Ошибка при запросе к API Яндекс Метрики', [
                'message' => $e->getMessage(),
                'hash' => $requestHash,
                'params' => $params,
            ]);

            // Если API недоступно и есть старые данные — возвращаем их как запасной вариант
            if (!empty($cachedData)) {
                Log::warning('API недоступно, возвращены старые данные из кэша', ['hash' => $requestHash]);
                return $cachedData;
            }

            // Если нет данных в кэше — выбрасываем исключение
            throw $e;
        }
    }

    /**
     * Генерация уникального хеша для запроса на основе всех параметров.
     *
     * @param array $params Параметры запроса
     * @return string
     */
    protected function generateRequestHash(array $params): string
    {
        // Сортируем параметры для стабильности хеша
        ksort($params);
        // Генерируем SHA-256 хеш
        return hash('sha256', json_encode($params));
    }

    /**
     * Получение данных из кэша БД.
     *
     * @param string $requestHash Хеш запроса
     * @return array|null
     */
    protected function getCachedData(string $requestHash): ?array
    {
        $cache = ApiCache::where('request_hash', $requestHash)->first();

        if ($cache) {
            // Проверяем актуальность кэша
            if ($cache->updated_at->diffInMinutes(now()) <= $this->cacheTTL) {
                return $cache->response_json;
            }
        }

        return null;
    }

    /**
     * Отправка запроса к API Яндекс Метрики.
     *
     * @param array $params Параметры запроса
     * @return array
     * @throws \Exception
     */
    protected function sendApiRequest(array $params): array
    {
        // Выбираем API endpoint в зависимости от группировки
        $apiUrl = $this->apiUrl;

        $response = Http::withToken($this->token)
            ->get($apiUrl, $params);

        if (!$response->successful()) {
            throw new \Exception(
                'API Яндекс Метрики ответил с ошибкой: ' . $response->status() . ' ' . $response->body(),
                $response->status()
            );
        }

        return $response->json();
    }

    /**
     * Сохранение/обновление данных в кэш.
     *
     * @param string $requestHash Хеш запроса
     * @param array $response Ответ от API
     * @param array $params Параметры запроса для отладки
     * @return void
     */
    protected function saveToCache(string $requestHash, array $response, array $params): void
    {
        ApiCache::updateOrCreate(
            ['request_hash' => $requestHash],
            [
                'response_json' => $response,
                'params_info' => json_encode($params),
                'updated_at' => now(),
            ]
        );
    }
}
