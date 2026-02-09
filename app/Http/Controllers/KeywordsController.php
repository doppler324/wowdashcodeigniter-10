<?php

namespace App\Http\Controllers;

use App\Models\Keyword;
use App\Models\Page;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class KeywordsController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of all keywords in the project.
     */
    public function indexForProject(Project $project)
    {
        $this->authorize('view', $project);

        $keywords = Keyword::with('page')
            ->whereHas('page.project', function ($query) use ($project) {
                $query->where('id', $project->id);
            })
            ->get();

        return view('keywords.index', compact('keywords', 'project'));
    }

    /**
     * Display a listing of the keywords.
     */
    public function index()
    {
        $keywords = Keyword::with('page.project')
            ->whereHas('page.project', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->get();

        return view('keywords.index', compact('keywords'));
    }

    /**
     * Show the form for creating a new keyword.
     */
    public function create(Project $project, Page $page)
    {
        $this->authorize('update', $project);

        return view('keywords.create', compact('page'));
    }

    /**
     * Store a newly created keyword in storage.
     */
    public function store(Request $request, Project $project, Page $page)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'keyword' => 'required|string|max:255',
            'is_main' => 'boolean',
            'volume' => 'nullable|integer|min:0',
            'volume_exact' => 'nullable|integer|min:0',
            'cpc' => 'nullable|numeric|min:0',
            'difficulty' => 'nullable|integer|min:0|max:100',
            'current_position' => 'nullable|integer|min:0',
            'best_position' => 'nullable|integer|min:0',
            'start_position' => 'nullable|integer|min:0',
            'trend' => 'nullable|integer',
            'region' => 'nullable|string|max:100',
            'actual_url' => 'nullable|url|max:255',
            'last_tracked_at' => 'nullable|date',
        ]);

        // If is_main is true, unset other main keywords for this page
        if ($validated['is_main'] ?? false) {
            $page->keywords()->where('is_main', true)->update(['is_main' => false]);
        }

        $page->keywords()->create($validated);

        return redirect()->route('projects.pages.show', [$page->project, $page])
            ->with('success', 'Keyword created successfully.');
    }

    /**
     * Display the specified keyword.
     */
    public function show(Project $project, Page $page, Keyword $keyword)
    {
        $this->authorize('view', $project);

        $keyword->load('page');
        return view('keywords.show', compact('keyword'));
    }

    /**
     * Show the form for editing the specified keyword.
     */
    public function edit(Project $project, Page $page, Keyword $keyword)
    {
        $this->authorize('update', $project);

        $keyword->load('page');
        return view('keywords.edit', compact('keyword'));
    }

    /**
     * Update the specified keyword in storage.
     */
    public function update(Request $request, Project $project, Page $page, Keyword $keyword)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'keyword' => 'required|string|max:255',
            'is_main' => 'boolean',
            'volume' => 'nullable|integer|min:0',
            'volume_exact' => 'nullable|integer|min:0',
            'cpc' => 'nullable|numeric|min:0',
            'difficulty' => 'nullable|integer|min:0|max:100',
            'current_position' => 'nullable|integer|min:0',
            'best_position' => 'nullable|integer|min:0',
            'start_position' => 'nullable|integer|min:0',
            'trend' => 'nullable|integer',
            'region' => 'nullable|string|max:100',
            'actual_url' => 'nullable|url|max:255',
            'last_tracked_at' => 'nullable|date',
        ]);

        // If is_main is true, unset other main keywords for this page
        if ($validated['is_main'] ?? false) {
            $keyword->page->keywords()->where('is_main', true)->where('id', '!=', $keyword->id)->update(['is_main' => false]);
        }

        $keyword->update($validated);

        return redirect()->route('projects.pages.show', [$keyword->page->project, $keyword->page])
            ->with('success', 'Keyword updated successfully.');
    }

    /**
     * Remove the specified keyword from storage.
     */
    public function destroy(Project $project, Page $page, Keyword $keyword)
    {
        $this->authorize('delete', $project);

        $page = $keyword->page;
        $keyword->delete();

        return redirect()->route('projects.pages.show', [$page->project, $page])
            ->with('success', 'Keyword deleted successfully.');
    }
}
