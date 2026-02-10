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
        $chartData = $this->getChartData();
        $yearlyChartData = $this->getYearlyChartData();

        return view('projects.show', compact('project', 'pages', 'activities', 'keywords', 'chartData', 'yearlyChartData'));
    }

    /**
     * Get monthly chart data for last year.
     */
    private function getYearlyChartData()
    {
        try {
            $settings = Setting::where('user_id', auth()->id())->first();

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
     * Get chart data from Yandex Metrika API for last month.
     */
    private function getChartData()
    {
        try {
            $settings = Setting::where('user_id', auth()->id())->first();

            \Illuminate\Support\Facades\Log::info('Settings found: ' . json_encode($settings));

            if (!$settings || empty($settings->yandex_metrika_token) || empty($settings->yandex_metrika_counter)) {
                \Illuminate\Support\Facades\Log::info('Yandex Metrika settings not found, returning mock data');
                return $this->getMockChartData();
            }

            $yandexMetrikaService = new YandexMetrikaService($settings->yandex_metrika_token);

            $date1 = now()->subMonth()->format('Y-m-d');
            $date2 = now()->format('Y-m-d');

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

        if (isset($response['data'])) {
            $dateValues = [];

            foreach ($response['data'] as $item) {
                $date = \Carbon\Carbon::parse($item['dimensions'][0]['name'])->isoFormat('DD.MM'); // Форматируем как "01.02"
                $visitCount = (int)$item['metrics'][0];
                $dateValues[$item['dimensions'][0]['name']] = ['date' => $date, 'value' => $visitCount];
            }

            ksort($dateValues);

            foreach ($dateValues as $value) {
                $categories[] = $value['date'];
                $data[] = $value['value'];
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
            foreach ($fullDateValues as $value) {
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
    private function getMockChartData()
    {
        return [
            'categories' => [],
            'data' => [],
        ];
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
