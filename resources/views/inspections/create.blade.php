@extends('layouts.app')
@section('title', 'New Inspection')
@section('page-title', 'New Inspection')

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('inspections.store') }}" class="space-y-5">
                @csrf

                {{-- Template selector --}}
                @if ($templates->count())
                    <div class="p-4 rounded-lg border border-amber-200 bg-amber-50">
                        <label class="block text-sm font-semibold text-amber-800 mb-1">
                            Load from Template
                            <span class="font-normal text-amber-600 ml-1 text-xs">(optional — pre-fills findings
                                automatically)</span>
                        </label>
                        <select name="template_id"
                            class="w-full px-3 py-2.5 border border-amber-300 rounded-lg text-sm bg-white focus:outline-none">
                            <option value="">— start with a blank inspection —</option>
                            @foreach ($templates as $tmpl)
                                <option value="{{ $tmpl->id }}" {{ old('template_id') == $tmpl->id ? 'selected' : '' }}>
                                    {{ $tmpl->name }}{{ $tmpl->description ? ' — ' . $tmpl->description : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Inspection Title <span
                            class="text-red-400">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none @error('title') border-red-400 @enderror"
                        placeholder="e.g. Monthly Food Safety Audit – May 2026">
                    @error('title')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Outlet to be Audited <span
                            class="text-red-400">*</span></label>
                    <select name="outlet_id" required
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none @error('outlet_id') border-red-400 @enderror">
                        <option value="">— select outlet —</option>
                        @foreach ($outlets as $outlet)
                            <option value="{{ $outlet->id }}" {{ old('outlet_id') == $outlet->id ? 'selected' : '' }}>
                                {{ $outlet->name }}{{ $outlet->code ? ' (' . $outlet->code . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('outlet_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                    @if (!$outlets->count())
                        <p class="text-xs text-amber-600 mt-1">No outlets yet. <a href="{{ route('admin.outlets.index') }}"
                                class="underline font-medium">Add one in Admin → Outlets</a>.</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Inspection Date <span
                            class="text-red-400">*</span></label>
                    <input type="date" name="inspection_date" value="{{ old('inspection_date', date('Y-m-d')) }}"
                        required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Auditors <span class="text-red-400">*</span>
                        <span class="text-gray-400 font-normal text-xs ml-1">(tick all auditors on this inspection)</span>
                    </label>
                    <div class="border border-gray-300 rounded-lg divide-y divide-gray-100 max-h-48 overflow-y-auto">
                        @foreach ($auditors as $auditor)
                            <label class="flex items-center gap-3 px-4 py-2.5 cursor-pointer hover:bg-gray-50 transition">
                                <input type="checkbox" name="auditor_ids[]" value="{{ $auditor->id }}"
                                    {{ in_array($auditor->id, old('auditor_ids', [])) ? 'checked' : '' }}
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
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none resize-none"
                        placeholder="Optional remarks...">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="px-5 py-2.5 rounded-lg text-white text-sm font-medium hover:opacity-90"
                        style="background:#1b6840">
                        Create Inspection
                    </button>
                    <a href="{{ route('inspections.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
