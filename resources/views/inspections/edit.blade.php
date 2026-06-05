@extends('layouts.app')
@section('title', 'Edit Inspection')
@section('page-title', 'Edit Inspection')

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('inspections.update', $inspection) }}" class="space-y-5">
                @csrf @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title <span
                            class="text-red-400">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $inspection->title) }}" required
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Outlet to be Audited <span
                            class="text-red-400">*</span></label>
                    <select name="outlet_id" required
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none">
                        <option value="">— select outlet —</option>
                        @foreach ($outlets as $outlet)
                            <option value="{{ $outlet->id }}"
                                {{ old('outlet_id', $inspection->outlet_id) == $outlet->id ? 'selected' : '' }}>
                                {{ $outlet->name }}{{ $outlet->code ? ' (' . $outlet->code . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('outlet_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Inspection Date <span
                            class="text-red-400">*</span></label>
                    <input type="date" name="inspection_date"
                        value="{{ old('inspection_date', $inspection->inspection_date->format('Y-m-d')) }}" required
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Auditors <span class="text-red-400">*</span>
                        <span class="text-gray-400 font-normal text-xs ml-1">(tick all auditors)</span>
                    </label>
                    @php $selectedAuditorIds = old('auditor_ids', $inspection->auditors->pluck('id')->toArray()); @endphp
                    <div class="border border-gray-300 rounded-lg divide-y divide-gray-100 max-h-48 overflow-y-auto">
                        @foreach ($auditors as $auditor)
                            <label class="flex items-center gap-3 px-4 py-2.5 cursor-pointer hover:bg-gray-50 transition">
                                <input type="checkbox" name="auditor_ids[]" value="{{ $auditor->id }}"
                                    {{ in_array($auditor->id, $selectedAuditorIds) ? 'checked' : '' }}
                                    class="w-4 h-4 accent-green-700">
                                <span class="text-sm font-medium text-gray-800">{{ $auditor->name }}</span>
                                <span
                                    class="ml-auto text-xs px-1.5 py-0.5 rounded bg-gray-100 text-gray-500">{{ ucfirst($auditor->role) }}</span>
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
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none resize-none">{{ old('notes', $inspection->notes) }}</textarea>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="px-5 py-2.5 rounded-lg text-white text-sm font-medium hover:opacity-90"
                        style="background:#1b6840">
                        Save Changes
                    </button>
                    <a href="{{ route('inspections.show', $inspection) }}"
                        class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <form method="POST" action="{{ route('inspections.update', $inspection) }}" class="space-y-5">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Title <span
                    class="text-red-400">*</span></label>
            <input type="text" name="title" value="{{ old('title', $inspection->title) }}" required
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Outlet to be Audited <span
                    class="text-red-400">*</span></label>
            <input type="text" name="outlet" value="{{ old('outlet', $inspection->outlet) }}" required
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none"
                placeholder="e.g. 69 Bar & Resto">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Inspection Date <span
                    class="text-red-400">*</span></label>
            <input type="date" name="inspection_date"
                value="{{ old('inspection_date', $inspection->inspection_date->format('Y-m-d')) }}" required
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Auditors <span class="text-red-400">*</span>
                <span class="text-gray-400 font-normal text-xs ml-1">(tick all auditors on this inspection)</span>
            </label>
            @php $selectedAuditorIds = old('auditor_ids', $inspection->auditors->pluck('id')->toArray()); @endphp
            <div class="border border-gray-300 rounded-lg divide-y divide-gray-100 max-h-48 overflow-y-auto">
                @foreach ($auditors as $auditor)
                    <label class="flex items-center gap-3 px-4 py-2.5 cursor-pointer hover:bg-gray-50 transition">
                        <input type="checkbox" name="auditor_ids[]" value="{{ $auditor->id }}"
                            {{ in_array($auditor->id, $selectedAuditorIds) ? 'checked' : '' }}
                            class="w-4 h-4 accent-green-700">
                        <span class="text-sm font-medium text-gray-800">{{ $auditor->name }}</span>
                        <span
                            class="ml-auto text-xs px-1.5 py-0.5 rounded bg-gray-100 text-gray-500">{{ ucfirst($auditor->role) }}</span>
                    </label>
                @endforeach
            </div>
            @error('auditor_ids')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status"
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none">
                @foreach (['open' => 'Open', 'in_review' => 'In Review', 'closed' => 'Closed'] as $val => $lbl)
                    <option value="{{ $val }}" {{ $inspection->status === $val ? 'selected' : '' }}>
                        {{ $lbl }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" rows="3"
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none resize-none">{{ old('notes', $inspection->notes) }}</textarea>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="px-5 py-2.5 rounded-lg text-white text-sm font-medium hover:opacity-90"
                style="background:#1b6840">
                Save Changes
            </button>
            <a href="{{ route('inspections.show', $inspection) }}"
                class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
        </div>
    </form>
</div>
</div>
@endsection
