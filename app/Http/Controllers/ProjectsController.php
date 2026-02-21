<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Activity;
use App\Models\Setting;
use App\Services\YandexMetrikaService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectsController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the projects.
     */
    public function index()
    {
        $projects = auth()->user()->projects;
        return view('projects.index', compact('projects'));
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project)
    {
        $this->authorize('view', $project);

        $pages = $project->pages()->get();
        $activities = $project->activities()->orderBy('event_date', 'desc')->get();
        $keywords = $project->keywords()->get();
        $chartData = $this->getChartData($project);
        $yearlyChartData = $this->getYearlyChartData($project);

        // Группируем активности по датам для графика
        $activitiesByDate = $activities->groupBy(function($item) {
            return $item->event_date->format('Y-m-d');
        })->map(function($group) {
            return $group->map(function($activity) {
                return [
                    'id' => $activity->id,
                    'title' => $activity->title,
                    'category' => $activity->category,
                    'formatted_date' => $activity->formatted_date
                ];
            })->toArray();
        })->toArray(); // Преобразуем в массив для удобства

        // Формируем маркеры для графика
        $markers = [];
        if (!empty($chartData['full_dates'])) {
            foreach ($chartData['full_dates'] as $index => $fullDate) {
                if (isset($activitiesByDate[$fullDate])) {
                    $taskCount = count($activitiesByDate[$fullDate]);

                    // Определяем букву для кружка
                    $labelText = $taskCount > 1 ? 'У' : mb_substr($activitiesByDate[$fullDate][0]['category'], 0, 1);

                    // Цвет по типу задачи
                    $colors = [
                        'content' => '#FF9F29',
                        'links' => '#28C76F',
                        'technical' => '#FF4560',
                        'meta' => '#7367F0',
                        'other' => '#00CFE8'
                    ];

                    $markerColor = $taskCount > 1 ? '#FF4560' : ($colors[$activitiesByDate[$fullDate][0]['category']] ?? '#9F9F9F');

                    // Формируем текст подсказки
                    $tooltipText = '';
                    foreach ($activitiesByDate[$fullDate] as $taskIndex => $task) {
                        $tooltipText .= ($taskIndex + 1) . '. ' . $task['title'] . "\n";
                    }

                    $markers[$index] = [
                        'symbol' => 'circle',
                        'fillColor' => $markerColor,
                        'strokeColor' => $markerColor,
                        'size' => $taskCount > 1 ? 12 : 10,
                        'strokeWidth' => 2,
                        'text' => $labelText,
                        'textColor' => '#fff',
                        'fontSize' => $taskCount > 1 ? 8 : 7,
                        'fontWeight' => 'bold',
                        'tasks' => $activitiesByDate[$fullDate],
                        'tooltipText' => $tooltipText
                    ];
                }
            }
        }

        // Формируем аннотации для вертикальных линий
        $annotations = [];
        if (!empty($chartData['full_dates'])) {
            \Illuminate\Support\Facades\Log::info('ActivitiesByDate dates:', [
                'dates' => array_keys($activitiesByDate)
            ]);
            \Illuminate\Support\Facades\Log::info('ChartData full dates:', [
                'dates' => $chartData['full_dates']
            ]);

            // Для каждой активности ищем соответствующую дату в график и добавляем аннотацию
            foreach ($activitiesByDate as $activityDate => $tasks) {
                // Находим индекс даты в chartData['full_dates']
                $index = array_search($activityDate, $chartData['full_dates']);
                if ($index !== false) {
                    $taskCount = count($tasks);

                    // Цвет по типу задачи
                    $colors = [
                        'content' => '#FF9F29',
                        'links' => '#28C76F',
                        'technical' => '#FF4560',
                        'meta' => '#7367F0',
                        'other' => '#00CFE8'
                    ];

                    $borderColor = $taskCount > 1 ? '#FF4560' : ($colors[$tasks[0]['category']] ?? '#9F9F9F');

                    $annotations[] = [
                        'x' => $chartData['categories'][$index], // Используем категорию вместо индекса
                        'borderColor' => $borderColor,
                        'borderWidth' => 3, // Жирная линия
                        'tasks' => $tasks,
                        'tooltipText' => '', // Мы используем подсказку для маркера
                        'date' => $activityDate,
                        'visits' => $chartData['data'][$index] ?? 0
                    ];

                    \Illuminate\Support\Facades\Log::info('Annotation added:', [
                        'activityDate' => $activityDate,
                        'index' => $index,
                        'chartDate' => $chartData['full_dates'][$index]
                    ]);
                } else {
                    \Illuminate\Support\Facades\Log::warning('Date not found in chart data:', [
                        'activityDate' => $activityDate,
                        'chartDates' => $chartData['full_dates']
                    ]);
                }
            }
        }

        // Кодируем аннотации в JSON с JSON_NUMERIC_CHECK
        $annotationsJson = json_encode($annotations, JSON_NUMERIC_CHECK);

        return view('projects.show', compact('project', 'pages', 'activities', 'keywords', 'chartData', 'yearlyChartData', 'activitiesByDate', 'annotations'));
    }

    /**
     * Get monthly chart data for last year.
     */
    private function getYearlyChartData($project)
    {
        try {
            $settings = $project->setting ?? Setting::where('user_id', auth()->id())->first();

            \Illuminate\Support\Facades\Log::info('Yandex Metrika settings:', [
                'settings' => $settings,
                'has_token' => !empty($settings->yandex_metrika_token),
                'has_counter' => !empty($settings->yandex_metrika_counter)
            ]);

            if (!$settings || empty($settings->yandex_metrika_token) || empty($settings->yandex_metrika_counter)) {
                \Illuminate\Support\Facades\Log::info('Yandex Metrika settings not found or invalid, returning mock data');
                return $this->getMockYearlyChartData();
            }

            $yandexMetrikaService = new YandexMetrikaService($settings->yandex_metrika_token);

            $date1 = now()->subMonths(11)->firstOfMonth()->format('Y-m-d'); // Начало 12 месяцев назад
            $date2 = now()->lastOfMonth()->format('Y-m-d'); // Конец текущего месяца

            $params = [
                'ids' => $settings->yandex_metrika_counter,
                'date1' => $date1,
                'date2' => $date2,
                'metrics' => $settings->yandex_metrika_metrics ?? 'ym:s:users', // Метрика пользователей вместо визитов
                'dimensions' => 'ym:s:month',
            ];

            \Illuminate\Support\Facades\Log::info('Calling Yandex Metrika API for yearly data with params: ' . json_encode($params));
            $response = $yandexMetrikaService->getData($params);
            \Illuminate\Support\Facades\Log::info('Yandex Metrika API yearly response: ' . print_r($response, true));

            $formattedData = $this->formatYearlyChartData($response);
            \Illuminate\Support\Facades\Log::info('Formatted yearly chart data: ' . print_r($formattedData, true));

            return $formattedData;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error fetching yearly chart data: ' . $e->getMessage());
            return $this->getMockYearlyChartData();
        }
    }

    /**
     * Format yearly Yandex Metrika data.
     */
    private function formatYearlyChartData($response)
    {
        $categories = [];
        $data = [];

        if (isset($response['data'])) {
            $dateValues = [];

            foreach ($response['data'] as $item) {
                $monthNumber = (int)$item['dimensions'][0]['name'];
                $currentYear = now()->year;

                // Определяем год для месяца
                $year = $currentYear;
                if ($monthNumber > now()->month) {
                    $year = $currentYear - 1;
                }

                $date = \Carbon\Carbon::createFromDate($year, $monthNumber, 1);
                $dateValues[$date->format('Y-m')] = [
                    'date' => $date->isoFormat('MMMM YYYY'), // Форматируем как "январь 2024" (русский)
                    'value' => (int)$item['metrics'][0],
                    'sort_key' => $date->timestamp // Ключ для сортировки
                ];
            }

            // Сортируем по времени (слева направо — от старого к новому)
            usort($dateValues, function($a, $b) {
                return $a['sort_key'] <=> $b['sort_key'];
            });

            foreach ($dateValues as $value) {
                $categories[] = $value['date'];
                $data[] = $value['value'];
            }
        }

        return [
            'categories' => $categories,
            'data' => $data,
        ];
    }

    /**
     * Get chart data from Yandex Metrika API for specified date range.
     */
    private function getChartData($project, $date1 = null, $date2 = null)
    {
        try {
            $settings = $project->setting ?? Setting::where('user_id', auth()->id())->first();

            \Illuminate\Support\Facades\Log::info('Settings found: ' . json_encode($settings));

            if (!$settings || empty($settings->yandex_metrika_token) || empty($settings->yandex_metrika_counter)) {
                \Illuminate\Support\Facades\Log::info('Yandex Metrika settings not found, returning mock data');
                return $this->getMockChartData($date1, $date2);
            }

            $yandexMetrikaService = new YandexMetrikaService($settings->yandex_metrika_token);

            if (!$date1 || !$date2) {
                $date1 = now()->subMonth()->format('Y-m-d');
                $date2 = now()->format('Y-m-d');
            }

            $params = [
                'ids' => $settings->yandex_metrika_counter,
                'date1' => $date1,
                'date2' => $date2,
                'metrics' => $settings->yandex_metrika_metrics ?? 'ym:s:visits',
                'dimensions' => $settings->yandex_metrika_dimensions ?? 'ym:s:date',
            ];

            \Illuminate\Support\Facades\Log::info('Calling Yandex Metrika API for daily data with params: ' . json_encode($params));
            $response = $yandexMetrikaService->getData($params);
            \Illuminate\Support\Facades\Log::info('Yandex Metrika API daily response: ' . print_r($response, true));

            return $this->formatChartData($response);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error fetching chart data: ' . $e->getMessage());
            return $this->getMockChartData();
        }
    }

    /**
     * Format Yandex Metrika data for monthly chart.
     */
    private function formatChartData($response)
    {
        $categories = [];
        $data = [];
        $fullDates = [];

        if (isset($response['data'])) {
            $dateValues = [];

            foreach ($response['data'] as $item) {
                \Illuminate\Support\Facades\Log::info('Yandex Metrika date format:', [
                    'rawDate' => $item['dimensions'][0]['name']
                ]);
                $date = \Carbon\Carbon::parse($item['dimensions'][0]['name']);
                $dateStr = $date->isoFormat('DD.MM'); // Форматируем как "01.02"
                $visitCount = (int)$item['metrics'][0];
                $dateKey = $date->format('Y-m-d'); // Используем единый формат даты
                $dateValues[$dateKey] = ['date' => $dateStr, 'value' => $visitCount];
            }

            ksort($dateValues);

            foreach ($dateValues as $key => $value) {
                $categories[] = $value['date'];
                $data[] = $value['value'];
                $fullDates[] = $key;
            }
        }

        // Заполняем пропущенные даты нулями для плавного графика
        if (!empty($categories)) {
            $startDate = \Carbon\Carbon::parse(array_key_first($dateValues));
            $endDate = \Carbon\Carbon::parse(array_key_last($dateValues));
            $fullDateValues = [];

            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $dateKey = $currentDate->format('Y-m-d');
                $dateLabel = $currentDate->isoFormat('DD.MM');
                if (isset($dateValues[$dateKey])) {
                    $fullDateValues[$dateKey] = $dateValues[$dateKey];
                } else {
                    $fullDateValues[$dateKey] = ['date' => $dateLabel, 'value' => 0];
                }
                $currentDate->addDay();
            }

            $categories = [];
            $data = [];
            $fullDates = [];
            foreach ($fullDateValues as $key => $value) {
                $categories[] = $value['date'];
                $data[] = $value['value'];
                $fullDates[] = $key;
            }
        }

        \Illuminate\Support\Facades\Log::info('Formatted chart data:', [
            'categories' => $categories,
            'full_dates' => $fullDates,
            'data' => $data
        ]);

        return [
            'categories' => $categories,
            'data' => $data,
            'full_dates' => $fullDates
        ];
    }

    /**
     * Get mock yearly chart data.
     */
    private function getMockYearlyChartData()
    {
        return [
            'categories' => [],
            'data' => [],
        ];
    }

    /**
     * Get mock monthly chart data.
     */
    private function getMockChartData($date1 = null, $date2 = null)
    {
        // Генерируем данные за указанный диапазон или за последние 30 дней по умолчанию
        $categories = [];
        $data = [];
        $fullDates = [];

        if (!$date1 || !$date2) {
            $startDate = \Carbon\Carbon::now()->subDays(29);
            $endDate = \Carbon\Carbon::now();
        } else {
            $startDate = \Carbon\Carbon::parse($date1);
            $endDate = \Carbon\Carbon::parse($date2);
        }

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $categories[] = $currentDate->isoFormat('DD.MM');
            $data[] = rand(10, 100);
            $fullDates[] = $currentDate->format('Y-m-d');
            $currentDate->addDay();
        }

        return [
            'categories' => $categories,
            'data' => $data,
            'full_dates' => $fullDates
        ];
    }

    /**
     * Get chart data for specified date range (API endpoint).
     */
    public function getChartDataByDateRange(Project $project, Request $request)
    {
        $this->authorize('view', $project);

        $date1 = $request->input('date1');
        $date2 = $request->input('date2');

        $chartData = $this->getChartData($project, $date1, $date2);

        // Группируем активности по датам для графика
        $activities = $project->activities()->orderBy('event_date', 'desc')->get();
        $activitiesByDate = $activities->groupBy(function($item) {
            return $item->event_date->format('Y-m-d');
        })->map(function($group) {
            return $group->map(function($activity) {
                return [
                    'id' => $activity->id,
                    'title' => $activity->title,
                    'category' => $activity->category,
                    'formatted_date' => $activity->formatted_date
                ];
            })->toArray();
        })->toArray();

        // Формируем аннотации для вертикальных линий
        $annotations = [];
        if (!empty($chartData['full_dates'])) {
            foreach ($activitiesByDate as $activityDate => $tasks) {
                $index = array_search($activityDate, $chartData['full_dates']);
                if ($index !== false) {
                    $taskCount = count($tasks);

                    $colors = [
                        'content' => '#FF9F29',
                        'links' => '#28C76F',
                        'technical' => '#FF4560',
                        'meta' => '#7367F0',
                        'other' => '#00CFE8'
                    ];

                    $borderColor = $taskCount > 1 ? '#FF4560' : ($colors[$tasks[0]['category']] ?? '#9F9F9F');

                    $annotations[] = [
                        'x' => $chartData['categories'][$index],
                        'borderColor' => $borderColor,
                        'borderWidth' => 3,
                        'tasks' => $tasks,
                        'tooltipText' => '',
                        'date' => $activityDate,
                        'visits' => $chartData['data'][$index] ?? 0
                    ];
                }
            }
        }

        return response()->json([
            'chartData' => $chartData,
            'annotations' => $annotations,
            'activitiesByDate' => $activitiesByDate
        ]);
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        return view('projects.create');
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        auth()->user()->projects()->create($request->all());

        return redirect()->route('projects.index')
            ->with('success', 'Проект успешно создан.');
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project)
    {
        $this->authorize('update', $project);

        return view('projects.edit', compact('project'));
    }

    /**
     * Update the specified project in storage.
     */
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project->update($request->all());

        return redirect()->route('projects.index')
            ->with('success', 'Проект успешно обновлен.');
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Проект успешно удален.');
    }
}
