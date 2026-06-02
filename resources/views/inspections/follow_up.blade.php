@extends('layouts.app')
@section('title', 'Follow-up Inspection')
@section('page-title', 'Raise Follow-up Inspection')
@section('page-subtitle', 'Based on ' . $parent->reference_no . ' – ' . $parent->title)

@section('content')
    <div class="max-w-2xl space-y-5">

        {{-- Parent Inspection Context --}}
        <div class="p-4 rounded-lg border border-red-200 bg-red-50">
            <h3 class="text-sm font-semibold text-red-800 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                </svg>
                Not-Complied Findings to be Carried Over
            </h3>
            <p class="text-xs text-red-700 mb-3">
                The following findings from
                <a href="{{ route('inspections.show', $parent) }}"
                    class="underline font-semibold">{{ $parent->reference_no }}</a>
                were marked <strong>Not Complied</strong> and will be copied into this follow-up inspection automatically.
            </p>
            <ol class="space-y-1.5">
                @foreach ($parent->notCompliedFindings as $nc)
                    <li class="text-xs bg-white border border-red-100 rounded px-3 py-2 flex items-start gap-2">
                        <span class="font-bold text-red-500 flex-shrink-0">#{{ $nc->number }}</span>
                        <span class="text-gray-700 flex-1">{{ $nc->finding }}</span>
                        <span class="text-gray-400 flex-shrink-0">{{ $nc->department->name ?? '' }}</span>
                    </li>
                @endforeach
            </ol>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('inspections.follow-up.store', $parent) }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Inspection Title <span
                            class="text-red-400">*</span></label>
                    <input type="text" name="title" value="{{ old('title', 'Follow-up: ' . $parent->title) }}" required
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none @error('title') border-red-400 @enderror">
                    @error('title')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Outlet <span
                            class="text-red-400">*</span></label>
                    <select name="outlet_id" required
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none @error('outlet_id') border-red-400 @enderror">
                        <option value="">— select outlet —</option>
                        @foreach ($outlets as $outlet)
                            <option value="{{ $outlet->id }}"
                                {{ old('outlet_id', $parent->outlet_id) == $outlet->id ? 'selected' : '' }}>
                                {{ $outlet->name }}{{ $outlet->code ? ' (' . $outlet->code . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('outlet_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Follow-up Date <span
                            class="text-red-400">*</span></label>
                    <p class="text-xs text-gray-400 mb-1">Defaults to the original inspection date — the deadlines on the
                        carried-over findings have not changed.</p>
                    <input type="date" name="inspection_date"
                        value="{{ old('inspection_date', $parent->inspection_date->format('Y-m-d')) }}" required
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Auditors <span class="text-red-400">*</span>
                        <span class="text-gray-400 font-normal text-xs ml-1">(tick all auditors)</span>
                    </label>
                    <div class="border border-gray-300 rounded-lg divide-y divide-gray-100 max-h-48 overflow-y-auto">
                        @foreach ($auditors as $auditor)
                            @php
                                $parentAuditorIds = $parent->auditors->pluck('id')->toArray();
                                $checked = in_array($auditor->id, old('auditor_ids', $parentAuditorIds));
                            @endphp
                            <label class="flex items-center gap-3 px-4 py-2.5 cursor-pointer hover:bg-gray-50 transition">
                                <input type="checkbox" name="auditor_ids[]" value="{{ $auditor->id }}"
                                    {{ $checked ? 'checked' : '' }} class="w-4 h-4 accent-green-700">
                                <span class="text-sm text-gray-700">{{ $auditor->name }}</span>
                                <span class="text-xs text-gray-400 ml-auto">{{ ucfirst($auditor->role) }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('auditor_ids')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="3"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none resize-none"
                        placeholder="Optional notes for this follow-up inspection…">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="px-5 py-2.5 rounded-lg text-white text-sm font-medium hover:opacity-90"
                        style="background:#c0392b">
                        Create Follow-up Inspection
                    </button>
                    <a href="{{ route('inspections.show', $parent) }}"
                        class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
