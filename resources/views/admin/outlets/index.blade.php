@extends('layouts.app')
@section('title', 'Outlets')
@section('page-title', 'Outlets')
@section('page-subtitle', 'Manage auditable outlets / venues')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Add Outlet --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 h-fit">
            <h3 class="font-semibold text-gray-900 mb-4">Add Outlet</h3>
            <form method="POST" action="{{ route('admin.outlets.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name <span
                            class="text-red-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none"
                        placeholder="e.g. 69 Bar & Resto">
                    @error('name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                    <input type="text" name="code" value="{{ old('code') }}"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none"
                        placeholder="e.g. BAR69">
                    @error('code')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <input type="text" name="description" value="{{ old('description') }}"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none"
                        placeholder="Optional note">
                </div>
                <button type="submit" class="w-full py-2.5 rounded-lg text-white text-sm font-medium hover:opacity-90"
                    style="background:#1b6840">
                    Add Outlet
                </button>
            </form>
        </div>

        {{-- Outlet List --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden h-fit">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">All Outlets ({{ $outlets->count() }})</h3>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($outlets as $outlet)
                    <div class="px-5 py-4 flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="font-medium text-gray-900 text-sm">{{ $outlet->name }}</p>
                                @if ($outlet->code)
                                    <span
                                        class="text-xs px-1.5 py-0.5 rounded bg-gray-100 text-gray-500 font-mono">{{ $outlet->code }}</span>
                                @endif
                                @if (!$outlet->is_active)
                                    <span class="text-xs px-1.5 py-0.5 rounded bg-red-100 text-red-600">Inactive</span>
                                @endif
                            </div>
                            @if ($outlet->description)
                                <p class="text-xs text-gray-400 mt-0.5">{{ $outlet->description }}</p>
                            @endif
                            <p class="text-xs text-gray-400 mt-0.5">{{ $outlet->inspections_count }} inspection(s)</p>
                        </div>
                        <div class="flex items-center gap-2 ml-3">
                            <button
                                onclick="document.getElementById('edit-outlet-{{ $outlet->id }}').classList.toggle('hidden')"
                                class="text-xs px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
                                Edit
                            </button>
                            <form method="POST" action="{{ route('admin.outlets.destroy', $outlet) }}"
                                onsubmit="return confirm('Delete {{ $outlet->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="text-xs px-3 py-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition">Del</button>
                            </form>
                        </div>
                    </div>
                    {{-- Inline edit --}}
                    <div id="edit-outlet-{{ $outlet->id }}" class="hidden px-5 pb-4 bg-gray-50 border-t border-gray-100">
                        <form method="POST" action="{{ route('admin.outlets.update', $outlet) }}"
                            class="grid grid-cols-2 gap-3 pt-3">
                            @csrf @method('PUT')
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Name</label>
                                <input type="text" name="name" value="{{ $outlet->name }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Code</label>
                                <input type="text" name="code" value="{{ $outlet->code }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                                <input type="text" name="description" value="{{ $outlet->description }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none">
                            </div>
                            <div class="col-span-2 flex items-center justify-between">
                                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1"
                                        {{ $outlet->is_active ? 'checked' : '' }} class="accent-green-700">
                                    Active
                                </label>
                                <button type="submit"
                                    class="px-4 py-1.5 rounded-lg text-white text-sm font-medium hover:opacity-90"
                                    style="background:#1b6840">
                                    Save
                                </button>
                            </div>
                        </form>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-sm text-gray-400">No outlets yet. Add the first one →</div>
                @endforelse
            </div>
        </div>

    </div>
@endsection
