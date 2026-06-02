@extends('layouts.app')
@section('title', 'Departments')
@section('page-title', 'Departments')
@section('page-subtitle', 'Manage hotel departments')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Add Department --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 h-fit">
            <h3 class="font-semibold text-gray-900 mb-4">Add Department</h3>
            <form method="POST" action="{{ route('admin.departments.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name <span
                            class="text-red-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none"
                        placeholder="e.g. Kitchen">
                    @error('name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                    <input type="text" name="code" value="{{ old('code') }}"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none"
                        placeholder="e.g. KIT">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none resize-none">{{ old('description') }}</textarea>
                </div>
                <button type="submit" class="w-full py-2.5 rounded-lg text-white text-sm font-medium hover:opacity-90"
                    style="background:#1b6840">
                    Add Department
                </button>
            </form>
        </div>

        {{-- Department List --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden h-fit">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">All Departments ({{ $departments->count() }})</h3>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($departments as $dept)
                    <div class="px-5 py-4 flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="font-medium text-gray-900 text-sm">{{ $dept->name }}</p>
                                @if ($dept->code)
                                    <span
                                        class="text-xs px-1.5 py-0.5 rounded bg-gray-100 text-gray-500 font-mono">{{ $dept->code }}</span>
                                @endif
                            </div>
                            @if ($dept->description)
                                <p class="text-xs text-gray-400 mt-0.5">{{ $dept->description }}</p>
                            @endif
                            <p class="text-xs text-gray-400 mt-0.5">{{ $dept->findings_count }} findings ·
                                {{ $dept->users_count }} users</p>
                        </div>
                        <div class="flex items-center gap-2 ml-3">
                            <button
                                onclick="document.getElementById('edit-{{ $dept->id }}').classList.toggle('hidden')"
                                class="text-xs px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
                                Edit
                            </button>
                            <form method="POST" action="{{ route('admin.departments.destroy', $dept) }}"
                                onsubmit="return confirm('Delete {{ $dept->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="text-xs px-3 py-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition">Del</button>
                            </form>
                        </div>
                    </div>
                    {{-- Inline edit form --}}
                    <div id="edit-{{ $dept->id }}" class="hidden px-5 pb-4">
                        <form method="POST" action="{{ route('admin.departments.update', $dept) }}"
                            class="grid grid-cols-3 gap-2">
                            @csrf @method('PUT')
                            <input type="text" name="name" value="{{ $dept->name }}" required
                                class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <input type="text" name="code" value="{{ $dept->code }}"
                                class="px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Code">
                            <button type="submit"
                                class="px-3 py-2 rounded-lg text-white text-sm font-medium hover:opacity-90"
                                style="background:#1b6840">Save</button>
                        </form>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-sm text-gray-400">No departments yet. Add one to get started.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
@endsection
