<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\InspectionTemplate;
use App\Models\TemplateItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InspectionTemplateController extends Controller
{
    public function index(): View
    {
        $templates   = InspectionTemplate::withCount('items')->orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        return view('admin.templates.index', compact('templates', 'departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        InspectionTemplate::create($validated);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template created.');
    }

    public function update(Request $request, InspectionTemplate $template): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active'   => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $template->update($validated);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template updated.');
    }

    public function destroy(InspectionTemplate $template): RedirectResponse
    {
        $template->delete();
        return redirect()->route('admin.templates.index')
            ->with('success', 'Template deleted.');
    }

    /** Show items for one template (used by the template detail panel). */
    public function show(InspectionTemplate $template): View
    {
        $template->load(['items.suggestedDepartment']);
        $departments = Department::orderBy('name')->get();
        return view('admin.templates.show', compact('template', 'departments'));
    }

    /** Add a checklist item to a template. */
    public function storeItem(Request $request, InspectionTemplate $template): RedirectResponse
    {
        $validated = $request->validate([
            'description'              => ['required', 'string'],
            'suggested_root_cause'     => ['nullable', 'in:people,facilities,training,others'],
            'suggested_department_id'  => ['nullable', 'exists:departments,id'],
            'sort_order'               => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['template_id'] = $template->id;
        $validated['sort_order']  = $validated['sort_order'] ?? ($template->items()->max('sort_order') + 1);

        TemplateItem::create($validated);

        return redirect()->route('admin.templates.show', $template)
            ->with('success', 'Item added.');
    }

    /** Remove a checklist item. */
    public function destroyItem(TemplateItem $item): RedirectResponse
    {
        $template = $item->template;
        $item->delete();
        return redirect()->route('admin.templates.show', $template)
            ->with('success', 'Item removed.');
    }
}
