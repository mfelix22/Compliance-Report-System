@extends('layouts.app')
@section('title', $template->name . ' – Items')
@section('page-title', $template->name)
@section('page-subtitle', 'Manage checklist items for this template')

@section('header-actions')
    <a href="{{ route('admin.templates.index') }}"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
        ← Back to Templates
    </a>
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Add Item --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 h-fit">
            <h3 class="font-semibold text-gray-900 mb-4">Add Checklist Item</h3>
            <form method="POST" action="{{ route('admin.templates.items.store', $template) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Finding / Question <span
                            class="text-red-400">*</span></label>
                    <textarea name="description" rows="3" required
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none resize-none @error('description') border-red-400 @enderror"
                        placeholder="e.g. Check food temperature logs are maintained and within limits">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Suggested Root Cause</label>
                    <select name="suggested_root_cause"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none">
                        <option value="">— none —</option>
                        <option value="people" {{ old('suggested_root_cause') === 'people' ? 'selected' : '' }}>People
                        </option>
                        <option value="facilities" {{ old('suggested_root_cause') === 'facilities' ? 'selected' : '' }}>
                            Facilities</option>
                        <option value="training" {{ old('suggested_root_cause') === 'training' ? 'selected' : '' }}>Training
                        </option>
                        <option value="others" {{ old('suggested_root_cause') === 'others' ? 'selected' : '' }}>Others
                        </option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Suggested Department</label>
                    <select name="suggested_department_id"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none">
                        <option value="">— none —</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}"
                                {{ old('suggested_department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $template->items->count()) }}"
                        min="0"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none">
                </div>
                <button type="submit" class="w-full py-2.5 rounded-lg text-white text-sm font-medium hover:opacity-90"
                    style="background:#1b6840">
                    Add Item
                </button>
            </form>
        </div>

        {{-- Items List --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden h-fit">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Checklist Items ({{ $template->items->count() }})</h3>
                <p class="text-xs text-gray-400 mt-0.5">These will be pre-loaded as findings when this template is used.</p>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($template->items as $item)
                    <div class="px-5 py-4 flex items-start justify-between gap-4">
                        <div class="flex items-start gap-3 flex-1 min-w-0">
                            <span
                                class="flex-shrink-0 w-6 h-6 rounded-full text-xs font-bold flex items-center justify-center mt-0.5 text-white"
                                style="background:#1b6840">
                                {{ $loop->iteration }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-800 leading-snug">{{ $item->description }}</p>
                                <div class="flex flex-wrap gap-2 mt-1.5">
                                    @if ($item->suggested_root_cause)
                                        <span
                                            class="text-xs px-1.5 py-0.5 rounded-full font-medium
                                        @if ($item->suggested_root_cause === 'people') bg-purple-100 text-purple-700
                                        @elseif($item->suggested_root_cause === 'facilities') bg-blue-100 text-blue-700
                                        @elseif($item->suggested_root_cause === 'training') bg-orange-100 text-orange-700
                                        @else bg-gray-100 text-gray-600 @endif">
                                            {{ ucfirst($item->suggested_root_cause) }}
                                        </span>
                                    @endif
                                    @if ($item->suggestedDepartment)
                                        <span class="text-xs px-1.5 py-0.5 rounded-full bg-gray-100 text-gray-600">
                                            {{ $item->suggestedDepartment->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('admin.template-items.destroy', $item) }}">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="flex-shrink-0 text-xs px-2.5 py-1.5 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition">
                                Remove
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-sm text-gray-400">
                        No items yet. Add the first checklist question →
                    </div>
                @endforelse
            </div>
        </div>

    </div>
@endsection
