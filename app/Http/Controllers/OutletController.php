<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OutletController extends Controller
{
    public function index(): View
    {
        $outlets = Outlet::withCount('inspections')->orderBy('name')->get();
        return view('admin.outlets.index', compact('outlets'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'code'        => ['nullable', 'string', 'max:20', 'unique:outlets,code'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        Outlet::create($validated);

        return redirect()->route('admin.outlets.index')
            ->with('success', 'Outlet created.');
    }

    public function update(Request $request, Outlet $outlet): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'code'        => ['nullable', 'string', 'max:20', 'unique:outlets,code,' . $outlet->id],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active'   => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $outlet->update($validated);

        return redirect()->route('admin.outlets.index')
            ->with('success', 'Outlet updated.');
    }

    public function destroy(Outlet $outlet): RedirectResponse
    {
        $outlet->delete();
        return redirect()->route('admin.outlets.index')
            ->with('success', 'Outlet deleted.');
    }
}
