@extends('layouts.app')
@section('title', 'Verify Finding')
@section('page-title', 'Verify Finding')
@section('page-subtitle', 'Finding #' . $finding->number . ' – ' . $finding->inspection->reference_no)

@section('content')
    <div class="max-w-2xl space-y-5">

        {{-- Finding Info --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Finding Details</h3>
            <div class="space-y-3 text-sm">
                <div><span class="text-gray-400">Finding:</span> <span class="text-gray-800">{{ $finding->finding }}</span>
                </div>
                <div><span class="text-gray-400">Department:</span> <span
                        class="font-medium text-gray-800">{{ $finding->department->name }}</span></div>
                <div><span class="text-gray-400">Root Cause:</span> <span
                        class="font-medium text-gray-800">{{ ucfirst($finding->root_cause) }}</span></div>
                <hr class="border-gray-100">
                <div><span class="text-gray-400">Corrective Action:</span> <span
                        class="text-gray-800">{{ $finding->corrective_action ?? '–' }}</span></div>
                <div><span class="text-gray-400">Preventive Action:</span> <span
                        class="text-gray-800">{{ $finding->preventive_action ?? '–' }}</span></div>
                <div><span class="text-gray-400">Date Closed:</span> <span
                        class="text-gray-800">{{ $finding->date_closed?->format('d M Y') ?? '–' }}</span></div>
            </div>
        </div>

        {{-- Verification Form --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-5">Verification</h3>
            <form method="POST" action="{{ route('findings.update', $finding) }}" class="space-y-5">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Verification Result <span
                            class="text-red-400">*</span></label>
                    <div class="grid grid-cols-2 gap-3">
                        <label
                            class="flex items-center gap-3 border-2 border-gray-200 rounded-xl px-4 py-3 cursor-pointer hover:border-green-400 transition has-[:checked]:border-green-600 has-[:checked]:bg-green-50">
                            <input type="radio" name="verification_status" value="complied"
                                {{ $finding->verification_status === 'complied' ? 'checked' : '' }} required
                                class="accent-green-700">
                            <div>
                                <p class="font-medium text-sm text-gray-800">✓ Complied</p>
                                <p class="text-xs text-gray-400">Action verified as complete</p>
                            </div>
                        </label>
                        <label
                            class="flex items-center gap-3 border-2 border-gray-200 rounded-xl px-4 py-3 cursor-pointer hover:border-red-400 transition has-[:checked]:border-red-500 has-[:checked]:bg-red-50">
                            <input type="radio" name="verification_status" value="not_complied"
                                {{ $finding->verification_status === 'not_complied' ? 'checked' : '' }}
                                class="accent-red-600">
                            <div>
                                <p class="font-medium text-sm text-gray-800">✗ Not Complied</p>
                                <p class="text-xs text-gray-400">Action not satisfactorily completed</p>
                            </div>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Verification Date</label>
                    <input type="date" name="verification_date"
                        value="{{ old('verification_date', $finding->verification_date?->format('Y-m-d') ?? date('Y-m-d')) }}"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none max-w-xs">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Catatan Auditor
                        <span id="notes-hint" class="text-xs text-red-500 font-normal hidden">– wajib diisi jika Not
                            Complied</span>
                    </label>
                    <textarea name="verification_notes" id="verification_notes" rows="3"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none resize-none"
                        placeholder="Tuliskan alasan / catatan verifikasi untuk auditee...">{{ old('verification_notes', $finding->verification_notes) }}</textarea>
                </div> {{-- only visible when Not Complied is selected --}}
                <div id="followup-section" class="hidden rounded-xl border border-red-200 bg-red-50 p-4 space-y-3">
                    <p class="text-sm font-semibold text-red-800">Buat Follow-up Finding?</p>
                    @if ($existingFollowUp)
                        <p class="text-sm text-red-700">
                            Follow-up finding untuk temuan ini sudah ada (Finding #{{ $existingFollowUp->number }},
                            status: <strong>{{ $existingFollowUp->status === 'closed' ? 'Closed' : 'Open' }}</strong>).
                            Tidak perlu membuat follow-up baru.
                        </p>
                    @else
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" id="create_followup_cb" name="create_followup" value="1"
                                class="mt-0.5 accent-red-600">
                            <span class="text-sm text-red-700">
                                Buat follow-up finding baru (due date sama dengan finding ini:
                                <strong>{{ $finding->due_date?->format('d M Y') ?? '–' }}</strong>)
                            </span>
                        </label>
                    @endif
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="px-5 py-2.5 rounded-lg text-white text-sm font-medium hover:opacity-90"
                        style="background:#1b6840">
                        Submit Verification
                    </button>
                    <a href="{{ route('inspections.show', $finding->inspection_id) }}"
                        class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const radios = document.querySelectorAll('input[name="verification_status"]');
        const followupSection = document.getElementById('followup-section');
        const createCb = document.getElementById('create_followup_cb');
        const notesHint = document.getElementById('notes-hint');
        const notesField = document.getElementById('verification_notes');

        function syncFollowupSection() {
            const notComplied = document.querySelector('input[value="not_complied"]').checked;
            followupSection.classList.toggle('hidden', !notComplied);
            notesHint.classList.toggle('hidden', !notComplied);
            notesField.required = notComplied;
            if (!notComplied && createCb) {
                createCb.checked = false;
            }
        }

        radios.forEach(r => r.addEventListener('change', syncFollowupSection));

        // Init on page load (if returning with validation error)
        syncFollowupSection();
    </script>
@endsection
