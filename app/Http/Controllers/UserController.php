<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('departments')->orderBy('name')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $departments = Department::orderBy('name')->get();
        return view('admin.users.create', compact('departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'email', 'unique:users,email'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
            'role'             => ['required', 'in:admin,auditor,auditee'],
            'department_ids'   => ['nullable', 'array'],
            'department_ids.*' => ['exists:departments,id'],
        ]);

        $departmentIds = $validated['department_ids'] ?? [];
        unset($validated['department_ids']);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);
        $user->departments()->sync($departmentIds);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created.');
    }

    public function edit(User $user): View
    {
        $departments = Department::orderBy('name')->get();
        $user->load('departments');
        return view('admin.users.edit', compact('user', 'departments'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'email', 'unique:users,email,' . $user->id],
            'role'             => ['required', 'in:admin,auditor,auditee'],
            'department_ids'   => ['nullable', 'array'],
            'department_ids.*' => ['exists:departments,id'],
            'password'         => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $departmentIds = $validated['department_ids'] ?? [];
        unset($validated['department_ids']);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        $user->departments()->sync($departmentIds);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted.');
    }
}
