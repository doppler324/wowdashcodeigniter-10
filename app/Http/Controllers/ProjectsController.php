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

            if (!$settings || empty($settings->yandex_metrika_token) || empty($settings->yandex_metrika_counter)) {
                return $this->getMockYearlyChartData();
            }

            $yandexMetrikaService = new YandexMetrikaService($settings->yandex_metrika_token);

            $date1 = now()->subYear()->format('Y-m-d');
            $date2 = now()->format('Y-m-d');

            $params = [
                'ids' => $settings->yandex_metrika_counter,
                'date1' => $date1,
                'date2' => $date2,
                'metrics' => $settings->yandex_metrika_metrics ?? 'ym:s:visits',
                'dimensions' => 'ym:s:month',
                'group' => 'month',
            ];

            \Illuminate\Support\Facades\Log::info('Calling Yandex Metrika API for yearly data with params: ' . json_encode($params));
            $response = $yandexMetrikaService->getData($params);
            \Illuminate\Support\Facades\Log::info('Yandex Metrika API yearly response: ' . json_encode($response));

            return $this->formatYearlyChartData($response);
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
                $date = \Carbon\Carbon::parse($item['dimensions'][0]['name'])->format('M Y'); // Форматируем как "Jan 2024"
                $visitCount = (int)array_sum($item['metrics'][0]);
                $dateValues[$item['dimensions'][0]['name']] = ['date' => $date, 'value' => $visitCount];
            }

            ksort($dateValues);

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
                'group' => 'day',
            ];

            \Illuminate\Support\Facades\Log::info('Calling Yandex Metrika API with params: ' . json_encode($params));
            $response = $yandexMetrikaService->getData($params);
            \Illuminate\Support\Facades\Log::info('Yandex Metrika API response: ' . json_encode($response));

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
                $date = \Carbon\Carbon::parse($item['dimensions'][0]['name'])->format('d.m');
                $visitCount = (int)array_sum($item['metrics'][0]);
                $dateValues[$item['dimensions'][0]['name']] = ['date' => $date, 'value' => $visitCount];
            }

            ksort($dateValues);

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
     * Get mock yearly chart data.
     */
    private function getMockYearlyChartData()
    {
        $categories = [];
        $data = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i)->format('M Y');
            $categories[] = $date;
            $data[] = rand(500, 2000);
        }

        return [
            'categories' => $categories,
            'data' => $data,
        ];
    }

    /**
     * Get mock monthly chart data.
     */
    private function getMockChartData()
    {
        $categories = [];
        $data = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('d.m');
            $categories[] = $date;
            $data[] = rand(100, 500);
        }

        return [
            'categories' => $categories,
            'data' => $data,
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
