<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Project;
use App\Models\Page;
use App\Models\Donor;
use App\Models\Activity;
use Illuminate\Http\Request;

class ExpensesController extends Controller
{

    public function index(Project $project)
    {
        $this->authorize('view', $project);
        $expenses = $project->expenses()->with(['page', 'donor'])->paginate(10);

        // Агрегация расходов по типам для чарта
        $expensesByType = $project->expenses()
            ->selectRaw('type, SUM(amount) as total')
            ->groupBy('type')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->type => (float) $item->total];
            });

        // Маппинг типов на русские названия
        $typeLabels = [
            'hosting' => 'Хостинг',
            'taxes' => 'Налоги',
            'links' => 'Ссылки',
            'service' => 'Сервис',
            'domains' => 'Домены'
        ];

        // Подготовка данных для чарта
        $chartLabels = [];
        $chartSeries = [];
        $chartColors = ['#487FFF', '#FF9F29', '#45B369', '#EF4A00']; // цвета для типов

        foreach ($typeLabels as $key => $label) {
            $chartLabels[] = $label;
            $chartSeries[] = $expensesByType[$key] ?? 0.0;
        }

        return view('expenses.index', compact('project', 'expenses', 'chartLabels', 'chartSeries', 'chartColors'));
    }

    public function create(Project $project)
    {
        $this->authorize('update', $project);
        $pages = $project->pages;
        $donors = Donor::whereHas('pages', function ($query) use ($project) {
            $query->where('project_id', $project->id);
        })->get();
        $types = [
            'hosting' => 'Хостинг',
            'taxes' => 'Налоги',
            'links' => 'Ссылки',
            'service' => 'Сервис',
            'domains' => 'Домены'
        ];
        return view('expenses.create', compact('project', 'pages', 'donors', 'types'));
    }

    public function store(Request $request, Project $project)
    {
        $this->authorize('update', $project);
        $validated = $request->validate([
            'type' => 'required|in:hosting,taxes,links,service,domains',
            'amount' => 'required|numeric|min:0',
            'page_id' => 'nullable|exists:pages,id',
            'donor_id' => 'nullable|exists:donors,id',
            'comment' => 'nullable|string|max:1000'
        ]);

        $project->expenses()->create($validated);
        return redirect()->route('projects.expenses.index', $project)->with('success', 'Расход добавлен');
    }

    public function edit(Project $project, Expense $expense)
    {
        $this->authorize('update', $project);
        $pages = $project->pages;
        $donors = Donor::whereHas('pages', function ($query) use ($project) {
            $query->where('project_id', $project->id);
        })->get();
        $types = [
            'hosting' => 'Хостинг',
            'taxes' => 'Налоги',
            'links' => 'Ссылки',
            'service' => 'Сервис',
            'domains' => 'Домены'
        ];
        return view('expenses.edit', compact('project', 'expense', 'pages', 'donors', 'types'));
    }

    public function update(Request $request, Project $project, Expense $expense)
    {
        $this->authorize('update', $project);
        $validated = $request->validate([
            'type' => 'required|in:hosting,taxes,links,service,domains',
            'amount' => 'required|numeric|min:0',
            'page_id' => 'nullable|exists:pages,id',
            'donor_id' => 'nullable|exists:donors,id',
            'comment' => 'nullable|string|max:1000'
        ]);

        $expense->update($validated);
        return redirect()->route('projects.expenses.index', $project)->with('success', 'Расход обновлен');
    }

    public function destroy(Project $project, Expense $expense)
    {
        $this->authorize('update', $project);
        $expense->delete();
        return redirect()->route('projects.expenses.index', $project)->with('success', 'Расход удален');
    }

    public function show(Project $project, Expense $expense)
    {
        $this->authorize('view', $project);
        return view('expenses.show', compact('project', 'expense'));
    }
}
