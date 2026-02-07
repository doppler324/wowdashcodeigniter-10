<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Activity;
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
     * Display the specified project.
     */
    public function show(Project $project)
    {
        $this->authorize('view', $project);

        $pages = $project->pages()->get();
        $activities = $project->activities()->orderBy('event_date', 'desc')->get();
        return view('projects.show', compact('project', 'pages', 'activities'));
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