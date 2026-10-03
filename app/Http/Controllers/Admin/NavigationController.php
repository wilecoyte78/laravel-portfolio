<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Target;
use App\Http\Controllers\Controller;
use App\Models\NavigationItem;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Validation\Rules\Enum;

class NavigationController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Navigation/Index', [
            'tree' => NavigationItem::roots()
                ->with(['page', 'children.page', 'children.children.page'])
                ->get(),
            'pages' => Page::orderBy('title')->get(['id', 'title', 'slug', 'is_published']),
            'targets' => collect(Target::cases())->map(fn (Target $target) => [
                'value' => $target->value,
                'label' => $target->label(),
            ])->values(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'page_id' => ['nullable', 'exists:pages,id'],
            'parent_id' => ['nullable', 'exists:navigation_items,id'],
            'external_url' => ['nullable', 'string', 'max:255'],
            'target' => [new Enum(Target::class)],
        ]);

        $data['sort_order'] = NavigationItem::where('parent_id', $data['parent_id'] ?? null)->max('sort_order') + 1;

        NavigationItem::create($data);

        return back()->with('success', 'Navigation item added.');
    }

    public function update(Request $request, NavigationItem $navigationItem): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'page_id' => ['nullable', 'exists:pages,id'],
            'parent_id' => ['nullable', 'exists:navigation_items,id'],
            'external_url' => ['nullable', 'string', 'max:255'],
            'target' => [new Enum(Target::class)],
        ]);

        $navigationItem->update($data);

        return back()->with('success', "Navigation link '{$request['label']}' was updated.");
    }

    /**
     * Bulk re-order / re-parent, used by the drag-and-drop tree editor.
     * Payload: [{ id, parent_id, sort_order }, ...]
     */
    public function reorder(Request $request): RedirectResponse
    {
        $items = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'exists:navigation_items,id'],
            'items.*.parent_id' => ['nullable', 'exists:navigation_items,id'],
            'items.*.sort_order' => ['required', 'integer'],
        ])['items'];

        foreach ($items as $item) {
            NavigationItem::where('id', $item['id'])->update([
                'parent_id' => $item['parent_id'],
                'sort_order' => $item['sort_order'],
            ]);
        }

        return back()->with('success', 'Navigation order updated.');
    }

    public function destroy(NavigationItem $navigationItem): RedirectResponse
    {
        $navigationItem->delete();

        return back()->with('success', 'Navigation item removed.');
    }
}
