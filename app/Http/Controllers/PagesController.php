<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Project;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    /**
     * Display a listing of the pages for a project.
     */
    public function index(Project $project)
    {
        $pages = $project->pages()->paginate(20);
        return view('pages.index', compact('project', 'pages'));
    }

    /**
     * Show the form for creating a new page.
     */
    public function create(Project $project)
    {
        // Получаем только разделы (section) и главную (home) для выбора родителя
        $potentialParents = $project->pages()
            ->whereIn('type', ['home', 'section'])
            ->orderBy('nesting_level')
            ->orderBy('title')
            ->get();

        return view('pages.create', compact('project', 'potentialParents'));
    }

    /**
     * Store a newly created page in storage.
     */
    public function store(Request $request, Project $project)
    {
        $request->validate([
            'url' => 'required|string|max:2048',
            'type' => 'required|in:home,section,card',
            'parent_id' => 'nullable|exists:pages,id',
            'title' => 'nullable|string|max:255',
            'keywords' => 'nullable|string',
            'status_code' => 'nullable|integer',
            'is_indexable' => 'boolean',
            'nesting_level' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        $data['project_id'] = $project->id;
        $data['is_indexable'] = $request->boolean('is_indexable', true);

        // Автоматически рассчитываем уровень вложенности на основе родителя
        if ($request->parent_id) {
            $parentNestingLevel = Page::where('id', $request->parent_id)->value('nesting_level');
            $data['nesting_level'] = $parentNestingLevel !== null ? $parentNestingLevel + 1 : 0;
        } else {
            $data['nesting_level'] = 0;
        }

        Page::create($data);

        return redirect()->route('projects.pages.index', $project)
            ->with('success', 'Страница успешно создана.');
    }

    /**
     * Display the specified page.
     */
    public function show(Project $project, Page $page)
    {
        $page->load('donors');
        return view('pages.show', compact('project', 'page'));
    }

    /**
     * Show the form for editing the specified page.
     */
    public function edit(Project $project, Page $page)
    {
        // Получаем только разделы (section) и главную (home) для выбора родителя
        // Исключаем текущую страницу и её дочерние элементы из списка потенциальных родителей
        $potentialParents = $project->pages()
            ->whereIn('type', ['home', 'section'])
            ->where('id', '!=', $page->id)
            ->whereNotIn('id', $this->getAllChildrenIds($page))
            ->orderBy('nesting_level')
            ->orderBy('title')
            ->get();

        return view('pages.edit', compact('project', 'page', 'potentialParents'));
    }

    /**
     * Get all children IDs recursively.
     */
    private function getAllChildrenIds(Page $page): array
    {
        $ids = [];
        foreach ($page->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->getAllChildrenIds($child));
        }
        return $ids;
    }

    /**
     * Update the specified page in storage.
     */
    public function update(Request $request, Project $project, Page $page)
    {
        $request->validate([
            'url' => 'required|string|max:2048',
            'type' => 'required|in:home,section,card',
            'parent_id' => 'nullable|exists:pages,id',
            'title' => 'nullable|string|max:255',
            'keywords' => 'nullable|string',
            'status_code' => 'nullable|integer',
            'is_indexable' => 'boolean',
            'nesting_level' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        $data['is_indexable'] = $request->boolean('is_indexable', true);

        // Автоматически рассчитываем уровень вложенности на основе родителя
        if ($request->parent_id) {
            $parentNestingLevel = Page::where('id', $request->parent_id)->value('nesting_level');
            $data['nesting_level'] = $parentNestingLevel !== null ? $parentNestingLevel + 1 : 0;
        } else {
            $data['nesting_level'] = 0;
        }

        $page->update($data);

        return redirect()->route('projects.pages.index', $project)
            ->with('success', 'Страница успешно обновлена.');
    }

    /**
     * Remove the specified page from storage.
     */
    public function destroy(Project $project, Page $page)
    {
        $page->delete();

        return redirect()->route('projects.pages.index', $project)
            ->with('success', 'Страница успешно удалена.');
    }
}
