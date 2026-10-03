<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Pages/Index', [
            'pages' => Page::orderBy('title')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Pages/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        $page = Page::create($data);

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('success', "Page \"{$page->title}\" created. Add it to the navigation menu to make it visible on the site.");
    }

    public function edit(Page $page): Response
    {
        return Inertia::render('Admin/Pages/Edit', [
            'page' => $page,
        ]);
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $data = $this->validateData($request, $page->id);

        $page->update($data);

        return back()->with('success', "Page \"{$page->title}\" updated.");
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->delete();

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'Page deleted. Any navigation links pointing to it were removed automatically.');
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable', 'string', 'max:255', 'alpha_dash',
                Rule::unique('pages', 'slug')->ignore($ignoreId),
            ],
            'content' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'is_published' => ['boolean'],
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);

        return $validated;
    }
}
