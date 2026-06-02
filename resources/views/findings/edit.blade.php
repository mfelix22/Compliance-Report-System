@extends('layouts.app')
@section('title', 'Respond to Finding')
@section('page-title', 'Respond to Finding')
@section('page-subtitle', 'Finding #' . $finding->number . ' – ' . $finding->inspection->reference_no)

@section('content')
    <div class="max-w-2xl space-y-5">

        {{-- Finding Detail (read-only) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Auditor's Finding</h3>
            @if ($finding->policy)
                <p class="text-xs text-green-700 font-medium mb-1">{{ $finding->policy->name }}</p>
            @endif
            <p class="text-gray-800 text-sm leading-relaxed mb-3">
                {{ $finding->policyItem?->text ?? $finding->finding }}
                @if ($finding->policyItem && $finding->finding)
                    <span class="block text-gray-500 text-xs mt-1">{{ $finding->finding }}</span>
                @endif
            </p>
            @if ($finding->photo)
                <div class="mb-3">
                    <a href="{{ Storage::url($finding->photo) }}" target="_blank">
                        <img src="{{ Storage::url($finding->photo) }}" alt="Foto temuan"
                            class="rounded-lg border border-gray-200 max-h-40 object-cover">
                    </a>
                </div>
            @endif
            @if ($finding->keterangan)
                <p class="text-xs text-gray-500 mb-3"><span class="font-medium">Keterangan:</span>
                    {{ $finding->keterangan }}</p>
            @endif
            <div class="flex flex-wrap gap-3 text-xs text-gray-500">
                <span class="inline-flex items-center gap-1">
                    Root Cause:
                    <span class="font-medium text-gray-700">{{ ucfirst($finding->root_cause) }}</span>
                </span>
                <span class="inline-flex items-center gap-1">
                    Department:
                    <span class="font-medium text-gray-700">{{ $finding->department->name }}</span>
                </span>
                @if ($finding->due_date)
                    <span class="inline-flex items-center gap-1">
                        Deadline:
                        <span class="font-medium {{ $finding->isOverdue ? 'text-red-600' : 'text-gray-700' }}">
                            {{ $finding->due_date->format('d M Y') }}
                            @if ($finding->isOverdue)
                                <span class="text-red-500">({{ abs($finding->daysUntilDue) }}d overdue)</span>
                            @endif
                        </span>
                    </span>
                @endif
            </div>
        </div>

        {{-- Auditor's verification note (shown when not_complied) --}}
        @if ($finding->verification_status === 'not_complied' && $finding->verification_notes)
            <div class="rounded-xl border border-red-200 bg-red-50 p-4">
                <p class="text-xs font-semibold text-red-700 uppercase tracking-wide mb-1">Catatan Auditor – Alasan Not
                    Complied</p>
                <p class="text-sm text-red-800">{{ $finding->verification_notes }}</p>
            </div>
        @endif
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-5">Your Response</h3>
            <form method="POST" action="{{ route('findings.update', $finding) }}" class="space-y-5">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Corrective Action <span
                            class="text-red-400">*</span></label>
                    <textarea name="corrective_action" rows="4" required
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none resize-none"
                        placeholder="What immediate action was taken to correct this finding?">{{ old('corrective_action', $finding->corrective_action) }}</textarea>
                    @error('corrective_action')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preventive Action <span
                            class="text-red-400">*</span></label>
                    <textarea name="preventive_action" rows="4" required
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none resize-none"
                        placeholder="What steps will prevent this from happening again?">{{ old('preventive_action', $finding->preventive_action) }}</textarea>
                    @error('preventive_action')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="px-5 py-2.5 rounded-lg text-white text-sm font-medium hover:opacity-90"
                        style="background:#1b6840">
                        Submit Response
                    </button>
                    <a href="{{ route('inspections.show', $finding->inspection_id) }}"
                        class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
