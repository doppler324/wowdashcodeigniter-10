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
        return view('expenses.index', compact('project', 'expenses'));
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
            'service' => 'Сервис'
        ];
        return view('expenses.create', compact('project', 'pages', 'donors', 'types'));
    }

    public function store(Request $request, Project $project)
    {
        $this->authorize('update', $project);
        $validated = $request->validate([
            'type' => 'required|in:hosting,taxes,links,service',
            'amount' => 'required|numeric|min:0',
            'page_id' => 'nullable|exists:pages,id',
            'donor_id' => 'nullable|exists:donors,id'
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
            'service' => 'Сервис'
        ];
        return view('expenses.edit', compact('project', 'expense', 'pages', 'donors', 'types'));
    }

    public function update(Request $request, Project $project, Expense $expense)
    {
        $this->authorize('update', $project);
        $validated = $request->validate([
            'type' => 'required|in:hosting,taxes,links,service',
            'amount' => 'required|numeric|min:0',
            'page_id' => 'nullable|exists:pages,id',
            'donor_id' => 'nullable|exists:donors,id'
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
