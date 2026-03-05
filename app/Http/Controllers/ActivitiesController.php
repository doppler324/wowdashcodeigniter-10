<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Project;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivitiesController extends Controller
{


    public function index(Project $project, Page $page = null)
    {
        $this->authorize('view', $project);

        // Генерируем хлебные крошки
        $breadcrumbService = app(\App\Services\BreadcrumbService::class);
        $breadcrumbService->generateFromRoute();

        // Получаем сгенерированные крошки
        $breadcrumbs = $breadcrumbService->get();

        // Если последний элемент "Активности", добавляем перед ним элемент с названием проекта
        if (!empty($breadcrumbs) && end($breadcrumbs)['title'] === 'Активности') {
            array_pop($breadcrumbs); // Убираем "Активности"
            $breadcrumbs[] = [
                'title' => $project->name,
                'url' => route('projects.show', $project)
            ];
            $breadcrumbs[] = ['title' => 'Активности', 'url' => null];
        } else {
            // Иначе просто обновляем последний элемент
            $breadcrumbService->updateLastItem($project->name);
            $breadcrumbs = $breadcrumbService->get();
        }

        $query = Activity::where('project_id', $project->id);

        if ($page) {
            $query->where('page_id', $page->id);
        }

        $activities = $query->orderBy('event_date', 'desc')->get();

        return view('activities.index', compact('project', 'page', 'activities', 'breadcrumbs'));
    }

    public function create(Project $project, Page $page = null)
    {
        $this->authorize('update', $project);

        return view('activities.create', compact('project', 'page'));
    }

    public function store(Request $request, Project $project, Page $page = null)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'event_date' => 'required|date',
            'category' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Activity::create([
            'project_id' => $project->id,
            'page_id' => $page ? $page->id : null,
            'event_date' => $validated['event_date'],
            'category' => $validated['category'],
            'title' => $validated['title'],
            'description' => $validated['description'],
        ]);

        if ($page) {
            return redirect()->route('projects.pages.activities.index', [$project, $page])
                ->with('success', 'Активность добавлена');
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Активность добавлена');
    }

    public function edit(Project $project, Activity $activity, Page $page = null)
    {
        $this->authorize('update', $project);

        return view('activities.edit', compact('project', 'page', 'activity'));
    }

    public function update(Request $request, Project $project, Activity $activity, Page $page = null)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'event_date' => 'required|date',
            'category' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $activity->update($validated);

        if ($page) {
            return redirect()->route('projects.pages.activities.index', [$project, $page])
                ->with('success', 'Активность обновлена');
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Активность обновлена');
    }

    public function destroy(Project $project, Activity $activity, Page $page = null)
    {
        $this->authorize('update', $project);

        $activity->delete();

        if ($page) {
            return redirect()->route('projects.pages.activities.index', [$project, $page])
                ->with('success', 'Активность удалена');
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Активность удалена');
    }
}
