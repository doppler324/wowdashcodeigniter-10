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
        $pages = $project->pages()->get();
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
        ]);

        $data = $request->all();
        $data['project_id'] = $project->id;
        $data['is_indexable'] = $request->boolean('is_indexable', true);

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
        ]);

        $data = $request->all();
        $data['is_indexable'] = $request->boolean('is_indexable', true);

        $page->update($data);

        return redirect()->route('projects.pages.index', $project)
            ->with('success', 'Страница успешно обновлена.');
    }

    /**
     * Import pages from text input.
     */
    public function import(Request $request, Project $project)
    {
        $request->validate([
            'pages_data' => 'required|string',
        ]);

        $pagesData = $request->input('pages_data');
        $lines = explode(';', $pagesData);
        $importedCount = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            // Parse line - expecting format: URL|Title|Type|Keywords|ParentID
            $parts = explode('|', $line);
            $url = trim($parts[0]);

            if (empty($url)) {
                continue;
            }

            $title = isset($parts[1]) ? trim($parts[1]) : null;
            $type = isset($parts[2]) ? trim($parts[2]) : 'card';
            $keywords = isset($parts[3]) ? trim($parts[3]) : null;
            $parentId = isset($parts[4]) ? trim($parts[4]) : null;

            // Validate type
            $type = in_array($type, ['home', 'section', 'card']) ? $type : 'card';

            // Validate parent ID
            if (!empty($parentId)) {
                $parentPage = Page::where('project_id', $project->id)
                    ->where('id', $parentId)
                    ->first();
                $validParentId = $parentPage ? $parentId : null;
            } else {
                $validParentId = null;
            }

            // Check if page already exists
            $existingPage = Page::where('project_id', $project->id)
                ->where('url', $url)
                ->first();

            if (!$existingPage) {
                $pageData = [
                    'project_id' => $project->id,
                    'url' => $url,
                    'title' => $title,
                    'type' => $type,
                    'keywords' => $keywords,
                    'is_indexable' => true,
                    'parent_id' => $validParentId,
                ];

                Page::create($pageData);
                $importedCount++;
            }
        }

        return redirect()->back()
            ->with('success', "Успешно импортировано $importedCount страниц.");
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
