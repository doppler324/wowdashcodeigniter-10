<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Models\Expense;
use App\Models\Page;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DonorController extends Controller
{
    use AuthorizesRequests;


    // Display all donors for a project
    public function index(Project $project)
    {
        $this->authorize('view', $project);

        $donors = Donor::whereHas('pages', function ($query) use ($project) {
            $query->where('project_id', $project->id);
        })->with('pages')->get();

        $pages = $project->pages()->get();

        return view('donors.index', compact('project', 'donors', 'pages'));
    }

    // Display all donors for a specific page
    public function indexForPage(Project $project, Page $page)
    {
        $this->authorize('view', $project);

        $donors = $page->donors()->get();
        return view('donors.index', compact('project', 'page', 'donors'));
    }

    // Show the form for creating a new donor
    public function create(Project $project, Page $page)
    {
        $this->authorize('update', $project);

        return view('donors.create', compact('project', 'page'));
    }

    // Store a newly created donor in storage
    public function store(Request $request, Project $project, Page $page)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'link' => 'required|url|max:2048',
            'type' => 'required|in:статья,форум,каталог',
            'link_type' => 'required|in:dofollow,nofollow',
            'added_at' => 'nullable|date',
            'is_image_link' => 'boolean',
            'status' => 'required|in:active,inactive,deleted',
            'is_redirect' => 'boolean',
            'check_date' => 'nullable|date',
            'status_code' => 'nullable|integer|min:100|max:599',
            'price' => 'nullable|numeric|min:0',
            'marketplace' => 'nullable|in:Miralinks,Collaborator,Gogetlinks,прямой аутрич',
            'anchor_links' => 'required|array',
            'anchor_links.*.anchor' => 'nullable|string|max:255',
            'anchor_links.*.page_id' => 'required|exists:pages,id',
        ]);

        $donor = Donor::create($validated);

        foreach ($validated['anchor_links'] as $anchorLink) {
            $donor->pages()->attach($anchorLink['page_id'], [
                'anchor' => $anchorLink['anchor']
            ]);
        }

        // Создать расход, если указана цена
        if (isset($validated['price']) && $validated['price'] > 0) {
            Expense::create([
                'project_id' => $project->id,
                'page_id' => null,
                'donor_id' => $donor->id,
                'activity_id' => null,
                'type' => 'links',
                'amount' => $validated['price'],
            ]);
        }

        return redirect()->route('projects.pages.show', [$project, $page])
            ->with('success', 'Донор успешно добавлен.');
    }

    // Display the specified donor
    public function show(Project $project, Page $page, Donor $donor)
    {
        $this->authorize('view', $project);

        return view('donors.show', compact('project', 'page', 'donor'));
    }

    // Show the form for editing the specified donor
    public function edit(Project $project, Page $page, Donor $donor)
    {
        $this->authorize('update', $project);

        return view('donors.edit', compact('project', 'page', 'donor'));
    }

    // Update the specified donor in storage
    public function update(Request $request, Project $project, Page $page, Donor $donor)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'link' => 'required|url|max:2048',
            'type' => 'required|in:статья,форум,каталог',
            'link_type' => 'required|in:dofollow,nofollow',
            'added_at' => 'nullable|date',
            'is_image_link' => 'boolean',
            'status' => 'required|in:active,inactive,deleted',
            'is_redirect' => 'boolean',
            'check_date' => 'nullable|date',
            'status_code' => 'nullable|integer|min:100|max:599',
            'price' => 'nullable|numeric|min:0',
            'marketplace' => 'nullable|in:Miralinks,Collaborator,Gogetlinks,прямой аутрич',
            'anchor_links' => 'required|array',
            'anchor_links.*.anchor' => 'nullable|string|max:255',
            'anchor_links.*.page_id' => 'required|exists:pages,id',
        ]);

        $donor->update($validated);

        // Update pivot table
        $donor->pages()->detach();
        foreach ($validated['anchor_links'] as $anchorLink) {
            $donor->pages()->attach($anchorLink['page_id'], [
                'anchor' => $anchorLink['anchor']
            ]);
        }

        // Обновить или создать/удалить расход
        $expense = Expense::where('donor_id', $donor->id)->first();
        if (isset($validated['price']) && $validated['price'] > 0) {
            if ($expense) {
                // Обновить существующий расход
                $expense->update([
                    'amount' => $validated['price'],
                ]);
            } else {
                // Создать новый расход
                Expense::create([
                    'project_id' => $project->id,
                    'page_id' => null,
                    'donor_id' => $donor->id,
                    'activity_id' => null,
                    'type' => 'links',
                    'amount' => $validated['price'],
                ]);
            }
        } else {
            // Если цена не указана или равна 0, удалить расход если существует
            if ($expense) {
                $expense->delete();
            }
        }

        return redirect()->route('projects.pages.show', [$project, $page])
            ->with('success', 'Донор успешно обновлен.');
    }

    // Remove the specified donor from storage
    public function destroy(Project $project, Page $page, Donor $donor)
    {
        $this->authorize('delete', $project);

        // Удалить связанный расход
        Expense::where('donor_id', $donor->id)->delete();

        $donor->delete();

        return redirect()->route('projects.pages.show', [$project, $page])
            ->with('success', 'Донор успешно удален.');
    }
}
