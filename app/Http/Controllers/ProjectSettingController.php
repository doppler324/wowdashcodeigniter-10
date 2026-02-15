<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Project $project)
    {
        $setting = $project->setting ?? new Setting(['project_id' => $project->id, 'user_id' => Auth::id()]);

        return view('projects.settings.index', [
            'project' => $project,
            'setting' => $setting,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Project $project)
    {
        $setting = new Setting(['project_id' => $project->id, 'user_id' => Auth::id()]);

        return view('projects.settings.create', [
            'project' => $project,
            'setting' => $setting,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'yandex_client_id' => 'nullable|string',
            'yandex_metrika_token' => 'nullable|string',
            'yandex_metrika_counter' => 'nullable|string',
        ]);

        $setting = $project->settings()->create(array_merge($validated, [
            'user_id' => Auth::id(),
        ]));

        return redirect()->route('projects.settings.index', $project)
            ->with('success', 'Настройки сохранены');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project, Setting $setting)
    {
        return view('projects.settings.show', [
            'project' => $project,
            'setting' => $setting,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project, Setting $setting)
    {
        return view('projects.settings.edit', [
            'project' => $project,
            'setting' => $setting,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project, Setting $setting)
    {
        $validated = $request->validate([
            'yandex_client_id' => 'nullable|string',
            'yandex_metrika_token' => 'nullable|string',
            'yandex_metrika_counter' => 'nullable|string',
        ]);

        $setting->update($validated);

        return redirect()->route('projects.settings.index', $project)
            ->with('success', 'Настройки обновлены');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, Setting $setting)
    {
        $setting->delete();

        return redirect()->route('projects.settings.index', $project)
            ->with('success', 'Настройки удалены');
    }
}
