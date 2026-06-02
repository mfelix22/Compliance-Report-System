@extends('layouts.app')
@section('title', 'Inspection Templates')
@section('page-title', 'Inspection Templates')
@section('page-subtitle', 'Reusable checklists to pre-populate inspection findings')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Create Template --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 h-fit">
            <h3 class="font-semibold text-gray-900 mb-4">New Template</h3>
            <form method="POST" action="{{ route('admin.templates.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Template Name <span
                            class="text-red-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none"
                        placeholder="e.g. Bar & Beverage Audit">
                    @error('name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <input type="text" name="description" value="{{ old('description') }}"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none"
                        placeholder="Short description">
                </div>
                <button type="submit" class="w-full py-2.5 rounded-lg text-white text-sm font-medium hover:opacity-90"
                    style="background:#1b6840">
                    Create Template
                </button>
            </form>
        </div>

        {{-- Template List --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden h-fit">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">All Templates ({{ $templates->count() }})</h3>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($templates as $tmpl)
                    <div class="px-5 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="font-medium text-gray-900 text-sm">{{ $tmpl->name }}</p>
                                    @if (!$tmpl->is_active)
                                        <span class="text-xs px-1.5 py-0.5 rounded bg-red-100 text-red-600">Inactive</span>
                                    @endif
                                </div>
                                @if ($tmpl->description)
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $tmpl->description }}</p>
                                @endif
                                <p class="text-xs text-gray-400 mt-0.5">{{ $tmpl->items_count }} checklist item(s)</p>
                            </div>
                            <div class="flex items-center gap-2 ml-3">
                                <a href="{{ route('admin.templates.show', $tmpl) }}"
                                    class="text-xs px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
                                    Edit Items
                                </a>
                                <button
                                    onclick="document.getElementById('edit-tmpl-{{ $tmpl->id }}').classList.toggle('hidden')"
                                    class="text-xs px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
                                    Rename
                                </button>
                                <form method="POST" action="{{ route('admin.templates.destroy', $tmpl) }}"
                                    onsubmit="return confirm('Delete template \'{{ $tmpl->name }}\'?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="text-xs px-3 py-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition">Del</button>
                                </form>
                            </div>
                        </div>
                        {{-- Inline rename --}}
                        <div id="edit-tmpl-{{ $tmpl->id }}" class="hidden mt-3 pt-3 border-t border-gray-100">
                            <form method="POST" action="{{ route('admin.templates.update', $tmpl) }}" class="flex gap-2">
                                @csrf @method('PUT')
                                <input type="text" name="name" value="{{ $tmpl->name }}" required
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none">
                                <input type="text" name="description" value="{{ $tmpl->description }}"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none"
                                    placeholder="Description">
                                <label class="flex items-center gap-1.5 text-sm text-gray-600 cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1"
                                        {{ $tmpl->is_active ? 'checked' : '' }} class="accent-green-700">
                                    Active
                                </label>
                                <button type="submit" class="px-4 py-2 rounded-lg text-white text-sm font-medium"
                                    style="background:#1b6840">Save</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-sm text-gray-400">No templates yet. Create the first one →</div>
                @endforelse
            </div>
        </div>

    </div>
@endsection
