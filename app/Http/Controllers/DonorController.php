<?php

namespace App\Http\Controllers;

use App\Models\Donor;
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

        $donors = Donor::whereHas('page', function ($query) use ($project) {
            $query->where('project_id', $project->id);
        })->with('page')->get();

        return view('donors.index', compact('project', 'donors'));
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
            'authority' => 'nullable|integer|min:0|max:100',
            'anchor' => 'nullable|string|max:255',
            'link_type' => 'required|in:dofollow,nofollow',
            'added_at' => 'nullable|date',
            'is_image_link' => 'boolean',
            'status' => 'required|in:active,inactive,deleted',
            'is_redirect' => 'boolean',
            'duration' => 'nullable|string|max:255',
            'check_date' => 'nullable|date',
            'placement_type' => 'nullable|in:статья,обзор,контекстная',
            'status_code' => 'nullable|integer|min:100|max:599',
            'price' => 'nullable|numeric|min:0',
            'marketplace' => 'nullable|in:Miralinks,Collaborator,Gogetlinks,прямой аутрич',
        ]);

        $page->donors()->create($validated);

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
            'authority' => 'nullable|integer|min:0|max:100',
            'anchor' => 'nullable|string|max:255',
            'link_type' => 'required|in:dofollow,nofollow',
            'added_at' => 'nullable|date',
            'is_image_link' => 'boolean',
            'status' => 'required|in:active,inactive,deleted',
            'is_redirect' => 'boolean',
            'duration' => 'nullable|string|max:255',
            'check_date' => 'nullable|date',
            'placement_type' => 'nullable|in:статья,обзор,контекстная',
            'status_code' => 'nullable|integer|min:100|max:599',
            'price' => 'nullable|numeric|min:0',
            'marketplace' => 'nullable|in:Miralinks,Collaborator,Gogetlinks,прямой аутрич',
        ]);

        $donor->update($validated);

        return redirect()->route('projects.pages.show', [$project, $page])
            ->with('success', 'Донор успешно обновлен.');
    }

    // Remove the specified donor from storage
    public function destroy(Project $project, Page $page, Donor $donor)
    {
        $this->authorize('delete', $project);

        $donor->delete();

        return redirect()->route('projects.pages.show', [$project, $page])
            ->with('success', 'Донор успешно удален.');
    }
}
