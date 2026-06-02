<?php

namespace App\Http\Controllers;

use App\Models\InspectionPolicy;
use App\Models\PolicyItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PolicyItemController extends Controller
{
    public function index(): View
    {
        $policies = InspectionPolicy::with('items')->orderBy('sort_order')->get();
        return view('admin.policies.index', compact('policies'));
    }

    public function store(Request $request, InspectionPolicy $policy): RedirectResponse
    {
        $validated = $request->validate([
            'text' => ['required', 'string', 'max:500'],
        ]);

        $maxOrder = $policy->items()->max('sort_order') ?? 0;

        $policy->items()->create([
            'text'       => $validated['text'],
            'sort_order' => $maxOrder + 1,
        ]);

        return redirect()->route('admin.policies.index', ['open' => $policy->id])
            ->with('success', 'Item berhasil ditambahkan.');
    }

    public function update(Request $request, InspectionPolicy $policy, PolicyItem $item): RedirectResponse
    {
        abort_if($item->inspection_policy_id !== $policy->id, 404);

        $validated = $request->validate([
            'text' => ['required', 'string', 'max:500'],
        ]);

        $item->update(['text' => $validated['text']]);

        return redirect()->route('admin.policies.index', ['open' => $policy->id])
            ->with('success', 'Item berhasil diperbarui.');
    }

    public function destroy(InspectionPolicy $policy, PolicyItem $item): RedirectResponse
    {
        abort_if($item->inspection_policy_id !== $policy->id, 404);

        $item->delete();

        return redirect()->route('admin.policies.index', ['open' => $policy->id])
            ->with('success', 'Item berhasil dihapus.');
    }
}
