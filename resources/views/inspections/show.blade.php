@extends('layouts.app')

@section('title', $inspection->title)

@section('content')
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $inspection->title }}</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Ref: {{ $inspection->reference_no }} &bull;
                    {{ $inspection->outlet->name }} &bull;
                    {{ $inspection->inspection_date->format('d M Y') }}
                    @if ($inspection->audit_time)
                        &bull; {{ $inspection->audit_time }}
                    @endif
                </p>
            </div>
            <div class="flex gap-2">
                @can('update', $inspection)
                    @if ($inspection->status !== 'closed')
                        <form method="POST" action="{{ route('inspections.close', $inspection) }}">
                            @csrf
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white rounded-lg"
                                style="background-color:#1b6840"
                                onclick="return confirm('Close this inspection? This will be checked for completeness.')">
                                Close Inspection
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('inspections.edit', $inspection) }}"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Edit</a>
                @endcan
                <a href="{{ route('inspections.pdf', $inspection) }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    PDF
                </a>
                <a href="{{ route('inspections.index') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Kembali
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="p-4 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="p-4 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
                {{ session('error') }}
            </div>
        @endif
        @if (session('close_warnings'))
            <div class="p-4 rounded-lg bg-amber-50 border border-amber-300 text-amber-800 text-sm">
                <p class="font-semibold mb-2">⚠ Inspection cannot be closed yet — the following items are incomplete:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach (session('close_warnings') as $w)
                        <li>{{ $w }}</li>
                    @endforeach
                </ul>
                <p class="mt-2 text-xs text-amber-600">Resolve all items above, then try closing again.</p>
            </div>
        @endif
        @if ($errors->any())
            <div class="p-4 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Meta + Reporter --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Detail Inspeksi</h3>
                <dl class="text-sm space-y-1.5">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Status</dt>
                        <dd>
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            @if ($inspection->status === 'open') bg-blue-100 text-blue-800
                            @elseif($inspection->status === 'in_review') bg-amber-100 text-amber-800
                            @else bg-green-100 text-green-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $inspection->status)) }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Outlet</dt>
                        <dd class="text-gray-900 font-medium">{{ $inspection->outlet->name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Auditor</dt>
                        <dd class="text-gray-900">{{ $inspection->auditors->pluck('name')->join(', ') }}</dd>
                    </div>
                    @if ($inspection->reporter_name)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Pelapor</dt>
                            <dd class="text-gray-900">{{ $inspection->reporter_name }}</dd>
                        </div>
                    @endif
                    @if ($inspection->parentInspection)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Follow-up dari</dt>
                            <dd>
                                <a href="{{ route('inspections.show', $inspection->parentInspection) }}"
                                    class="text-green-700 underline text-xs">
                                    {{ $inspection->parentInspection->reference_no }}
                                </a>
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- @if (in_array(auth()->user()->role, ['admin', 'auditor']))
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Pelapor &amp; Selesaikan
                    </h3>
                    <form method="POST" action="{{ route('inspections.update', $inspection) }}" class="space-y-3">
                        @csrf @method('PUT')
                        <input type="hidden" name="title" value="{{ $inspection->title }}">
                        <input type="hidden" name="outlet_id" value="{{ $inspection->outlet_id }}">
                        <input type="hidden" name="inspection_date"
                            value="{{ $inspection->inspection_date->format('Y-m-d') }}">
                        @foreach ($inspection->auditors as $aud)
                            <input type="hidden" name="auditor_ids[]" value="{{ $aud->id }}">
                        @endforeach
                        <input type="hidden" name="status"
                            value="{{ $inspection->status === 'open' ? 'in_review' : $inspection->status }}">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Nama Pelapor</label>
                            <input type="text" name="reporter_name"
                                value="{{ old('reporter_name', $inspection->reporter_name) }}"
                                placeholder="Nama lengkap pelapor"
                                class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Waktu Audit</label>
                            <input type="text" name="audit_time"
                                value="{{ old('audit_time', $inspection->audit_time) }}" placeholder="09:30"
                                class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500">
                        </div>
                        @if ($inspection->status === 'open')
                            <button type="submit"
                                class="w-full py-2 text-sm font-semibold text-white rounded-lg active:bg-green-700 transition-colors cursor-pointer"
                                style="background-color:#1b6840">
                                Selesaikan Audit
                            </button>
                        @else
                            <button type="submit"
                                class="w-full py-2 text-sm font-semibold text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 hover:border-gray-400 active:bg-gray-300 transition-colors cursor-pointer">
                                Update Info Pelapor
                            </button>
                        @endif
                    </form>
                </div>
            @endif --}}
        </div>


        {{-- Progress Summary --}}
        @php
            $totalCategories = $policies->count();
            $assessed = $statusByPolicy->count();
            $cCount = $statusByPolicy->where('status', 'C')->count();
            $ncCount = $statusByPolicy->where('status', 'NC')->count();
            $naCount = $statusByPolicy->where('status', 'NA')->count();
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
            <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
                <div class="text-2xl font-bold text-gray-700">{{ $assessed }}/{{ $totalCategories }}</div>
                <div class="text-xs text-gray-500 mt-0.5">Dinilai</div>
            </div>
            <div class="bg-white rounded-xl border border-green-200 p-3 text-center">
                <div class="text-2xl font-bold text-green-700">{{ $cCount }}</div>
                <div class="text-xs text-green-600 mt-0.5">Compliant</div>
            </div>
            <div class="bg-white rounded-xl border border-red-200 p-3 text-center">
                <div class="text-2xl font-bold text-red-600">{{ $ncCount }}</div>
                <div class="text-xs text-red-500 mt-0.5">Non-Compliant</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
                <div class="text-2xl font-bold text-gray-500">{{ $naCount }}</div>
                <div class="text-xs text-gray-500 mt-0.5">N/A</div>
            </div>
        </div>

        {{-- Audit Checklist Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-800">Checklist Audit</h2>
                <span class="text-xs text-gray-400">Klik C / NC / N/A pada setiap kategori</span>
            </div>

            <table class="w-full text-sm">
                <thead
                    class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wide border-b border-gray-200">
                    <tr>
                        <th class="px-5 py-3 text-left w-12">#</th>
                        <th class="px-5 py-3 text-left">Kategori</th>
                        <th class="px-4 py-3 text-center w-16">C</th>
                        <th class="px-4 py-3 text-center w-16">NC</th>
                        <th class="px-4 py-3 text-center w-16">N/A</th>
                        <th class="px-4 py-3 text-center w-24">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($policies as $policy)
                        @php
                            $catStatus = $statusByPolicy->get($policy->id);
                            $catFindings = $findingsByPolicy->get($policy->id, collect());
                            $isC = $catStatus?->status === 'C';
                            $isNC = $catStatus?->status === 'NC';
                            $isNA = $catStatus?->status === 'NA';
                            $ncFormId = 'nc-form-' . $policy->id;
                            // Expand NC form if: status is NC and no findings yet (need to fill), OR if there was a validation error for this policy
                            $expandForm = $isNC && $catFindings->isEmpty();
                            // Also expand if old input has this policy selected (validation failed)
                            if (old('inspection_policy_id') == $policy->id) {
                                $expandForm = true;
                            }
                        @endphp

                        {{-- Category row --}}
                        <tr
                            class="border-b border-gray-100
                    @if ($isC) bg-green-50 @elseif($isNC) bg-red-50 @elseif($isNA) bg-gray-50 @endif">

                            <td class="px-5 py-3 text-gray-400 text-xs font-mono">
                                {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>

                            <td class="px-5 py-3">
                                <div class="font-medium text-gray-800">{{ $policy->name }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">Due: {{ $policy->due_label }} dari tgl inspeksi
                                </div>
                            </td>

                            {{-- C Button --}}
                            <td class="px-4 py-3 text-center">
                                @if (in_array(auth()->user()->role, ['admin', 'auditor']) && $inspection->status !== 'closed')
                                    <form method="POST"
                                        action="{{ route('category-status.update', [$inspection, $policy]) }}">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="C">
                                        <button type="submit" onclick="hideNCForm('{{ $ncFormId }}')"
                                            title="Tandai Compliant" @class([
                                                'w-10 h-8 rounded-lg text-xs font-bold border-2 transition-all',
                                                'bg-green-600 border-green-600 text-white shadow-md scale-105' => $isC,
                                                'border-green-500 text-green-600 opacity-40 hover:opacity-100 hover:bg-green-50' =>
                                                    !$isC && ($isNC || $isNA),
                                                'border-green-500 text-green-600 hover:bg-green-50' =>
                                                    !$isC && !$isNC && !$isNA,
                                            ])>
                                            {{ $isC ? '✓' : 'C' }}
                                        </button>
                                    </form>
                                @elseif($isC)
                                    <span
                                        class="inline-flex items-center justify-center w-10 h-8 rounded-lg bg-green-600 text-white text-xs font-bold">C</span>
                                @endif
                            </td>

                            {{-- NC Button --}}
                            <td class="px-4 py-3 text-center">
                                @if (in_array(auth()->user()->role, ['admin', 'auditor']) && $inspection->status !== 'closed')
                                    <button type="button" onclick="toggleNCForm('{{ $ncFormId }}')"
                                        title="Tambah Temuan Non-Compliant" @class([
                                            'w-10 h-8 rounded-lg text-xs font-bold border-2 transition-all',
                                            'bg-red-600 border-red-600 text-white shadow-md scale-105' => $isNC,
                                            'border-red-500 text-red-600 opacity-40 hover:opacity-100 hover:bg-red-50' =>
                                                !$isNC && ($isC || $isNA),
                                            'border-red-500 text-red-600 hover:bg-red-50' => !$isNC && !$isC && !$isNA,
                                        ])>
                                        NC
                                    </button>
                                @elseif($isNC)
                                    <span
                                        class="inline-flex items-center justify-center w-10 h-8 rounded-lg bg-red-600 text-white text-xs font-bold">NC</span>
                                @endif
                            </td>

                            {{-- N/A Button --}}
                            <td class="px-4 py-3 text-center">
                                @if (in_array(auth()->user()->role, ['admin', 'auditor']) && $inspection->status !== 'closed')
                                    <form method="POST"
                                        action="{{ route('category-status.update', [$inspection, $policy]) }}">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="NA">
                                        <button type="submit" onclick="hideNCForm('{{ $ncFormId }}')"
                                            title="Tandai Tidak Berlaku (N/A)" @class([
                                                'w-10 h-8 rounded-lg text-xs font-bold border-2 transition-all',
                                                'bg-gray-500 border-gray-500 text-white shadow-md scale-105' => $isNA,
                                                'border-gray-400 text-gray-500 opacity-40 hover:opacity-100 hover:bg-gray-50' =>
                                                    !$isNA && ($isC || $isNC),
                                                'border-gray-400 text-gray-500 hover:bg-gray-50' =>
                                                    !$isNA && !$isC && !$isNC,
                                            ])>
                                            N/A
                                        </button>
                                    </form>
                                @elseif($isNA)
                                    <span
                                        class="inline-flex items-center justify-center w-10 h-8 rounded-lg bg-gray-400 text-white text-xs font-bold">N/A</span>
                                @endif
                            </td>

                            {{-- Status badge --}}
                            <td class="px-4 py-3 text-center">
                                @if ($isC)
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Compliant</span>
                                @elseif($isNC)
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Non-Compliant</span>
                                @elseif($isNA)
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">N/A</span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-600">—</span>
                                @endif
                            </td>
                        </tr>

                        {{-- Existing findings for this category --}}
                        @foreach ($catFindings as $finding)
                            <tr class="border-b border-gray-100 bg-white">
                                <td class="px-5 py-2"></td>
                                <td class="px-5 py-2" colspan="4">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-1 text-xs space-y-1">
                                            @if ($finding->parent_finding_id)
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-xs font-semibold">
                                                    &#x21A9; Follow-up dari Finding
                                                    #{{ $finding->parentFinding->number ?? $finding->parent_finding_id }}
                                                </span>
                                            @endif
                                            {{-- Selected items --}}
                                            @if ($finding->selected_policy_item_ids)
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach ($finding->selected_policy_item_ids as $itemId)
                                                        @php $item = $policy->items->find($itemId) @endphp
                                                        @if ($item)
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded bg-red-100 text-red-700 text-xs">
                                                                {{ $item->text }}
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                            {{-- Custom / added items --}}
                                            @if ($finding->custom_finding_items)
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach ($finding->custom_finding_items as $ci)
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded bg-orange-100 text-orange-700 text-xs">
                                                            &#x2713; {{ $ci }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                            @if ($finding->finding)
                                                <p class="text-gray-700 italic">{{ $finding->finding }}</p>
                                            @endif
                                            <div class="flex flex-wrap gap-3 text-gray-500 mt-1">
                                                <span>Root cause:
                                                    <strong>{{ ucfirst($finding->root_cause) }}</strong></span>
                                                <span>PIC: <strong>{{ $finding->department?->name }}</strong></span>
                                                @if ($finding->due_date)
                                                    <span
                                                        class="{{ $finding->isOverdue && $finding->status !== 'closed' ? 'text-red-600 font-semibold' : '' }}">
                                                        Due: {{ $finding->due_date->format('d M Y') }}
                                                    </span>
                                                @endif
                                                <span
                                                    class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium
                                        {{ $finding->status === 'closed' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                                    {{ ucfirst($finding->status) }}
                                                </span>
                                            </div>
                                            @if ($finding->keterangan)
                                                <p class="text-gray-500 italic">Catatan: {{ $finding->keterangan }}</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                            @if ($finding->photo)
                                                <a href="{{ Storage::url($finding->photo) }}" target="_blank"
                                                    class="text-xs text-blue-600 hover:underline">Foto</a>
                                            @endif
                                            @if (auth()->user()->isAuditee())
                                                <a href="{{ route('findings.edit', $finding) }}"
                                                    class="text-xs text-blue-600 hover:underline">Isi Respons</a>
                                            @endif
                                            @if (in_array(auth()->user()->role, ['admin', 'auditor']))
                                                @if ($finding->followUpFinding)
                                                    <span class="text-xs text-amber-600 cursor-default"
                                                        title="Follow-up finding #{{ $finding->followUpFinding->number }} sudah dibuat">&#x21A9;
                                                        Follow-up</span>
                                                @elseif ($finding->corrective_action)
                                                    <a href="{{ route('findings.verify', $finding) }}"
                                                        class="text-xs text-purple-600 hover:underline">Verifikasi</a>
                                                @else
                                                    <span class="text-xs text-gray-300 cursor-not-allowed"
                                                        title="Auditee belum mengisi respons">Verifikasi</span>
                                                @endif
                                                @if ($inspection->status !== 'closed')
                                                    <form method="POST"
                                                        action="{{ route('findings.destroy', $finding) }}"
                                                        onsubmit="return confirm('Hapus temuan ini?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                            class="text-xs text-red-500 hover:underline">Hapus</button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td></td>
                            </tr>
                        @endforeach

                        {{-- NC inline finding form --}}
                        @if (in_array(auth()->user()->role, ['admin', 'auditor']))
                            @php $hasItems = $policy->items->count() > 0; @endphp
                            <tr id="{{ $ncFormId }}"
                                class="{{ $expandForm ? '' : 'hidden' }} border-b border-red-300 bg-white">
                                <td class="px-5 py-4" colspan="6">
                                    <form method="POST" action="{{ route('findings.store', $inspection) }}"
                                        enctype="multipart/form-data" class="space-y-5">
                                        @csrf
                                        <input type="hidden" name="inspection_policy_id" value="{{ $policy->id }}">

                                        {{-- Header --}}
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="text-sm font-bold" style="color:#b91c1c">Temuan Non-Compliance
                                                </h4>
                                                <p class="text-xs text-gray-500 mt-0.5">{{ $policy->name }} — tiap item
                                                    yang dipilih menjadi <strong>1 temuan terpisah</strong></p>
                                            </div>
                                            <button type="button" onclick="hideNCForm('{{ $ncFormId }}')"
                                                class="text-gray-400 hover:text-gray-600 text-xs shrink-0 ml-4">&#x2715;
                                                Tutup</button>
                                        </div>

                                        @if ($errors->any() && old('inspection_policy_id') == $policy->id)
                                            <div
                                                class="p-3 rounded-lg bg-red-50 border border-red-200 text-xs text-red-700">
                                                {{ $errors->first() }}
                                            </div>
                                        @endif

                                        @if ($hasItems)
                                            {{-- Step 1: Item chips — each chip = one finding row --}}
                                            <div class="!mb-2">
                                                <p class="text-xs font-semibold text-gray-700 mb-2">Pilih item yang tidak
                                                    sesuai <span class="text-red-500">*</span></p>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach ($policy->items as $item)
                                                        @php $isLainLain = strtolower(trim($item->text)) === 'lain-lain'; @endphp
                                                        <label
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border-2 text-xs font-medium cursor-pointer select-none transition-all border-gray-300 text-gray-600 hover:border-red-400 hover:text-red-600 has-[:checked]:border-red-500 has-[:checked]:bg-red-500 has-[:checked]:text-white">
                                                            <input type="checkbox" class="sr-only finding-item-cb"
                                                                data-policy="{{ $policy->id }}"
                                                                data-item-id="{{ $item->id }}"
                                                                data-item-text="{{ $item->text }}"
                                                                data-lain-lain="{{ $isLainLain ? '1' : '0' }}"
                                                                onchange="onFindingItemChange(this, '{{ $policy->id }}')">
                                                            {{ $item->text }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                                <p id="no-items-msg-{{ $policy->id }}"
                                                    class="text-xs text-gray-400 mt-2 italic">
                                                    Pilih minimal satu item di atas untuk mulai mengisi temuan.
                                                </p>
                                            </div>
                                        @endif

                                        {{-- Step 2: Finding cards (one per selected item, generated by JS) --}}
                                        <div id="finding-cards-{{ $policy->id }}"
                                            class="space-y-4{{ $hasItems ? ' hidden' : '' }}">
                                            @if (!$hasItems)
                                                {{-- Policy has no predefined items: always show one static card --}}
                                                <div
                                                    class="finding-card space-y-4 p-4 rounded-xl border-2 border-red-200 bg-red-50/30">
                                                    <div class="flex items-center gap-2">
                                                        <span
                                                            class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-600 text-white text-xs font-bold shrink-0">1</span>
                                                        <span class="text-xs font-semibold text-red-700">Temuan</span>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Item
                                                            Temuan <span class="text-red-500">*</span></label>
                                                        <input type="text" name="findings[0][item_text]"
                                                            value="{{ old('findings.0.item_text') }}"
                                                            placeholder="Jelaskan item temuan secara spesifik…"
                                                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-400 bg-white">
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="block text-xs font-semibold text-gray-700 mb-1">Deskripsi
                                                            <span class="text-red-500">*</span></label>
                                                        <textarea name="findings[0][description]" rows="2" placeholder="Jelaskan temuan secara detail…" required
                                                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-400 bg-white">{{ old('findings.0.description') }}</textarea>
                                                    </div>
                                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                                        <div>
                                                            <label
                                                                class="block text-xs font-semibold text-gray-700 mb-2">Root
                                                                Cause <span class="text-red-500">*</span></label>
                                                            <div class="space-y-1.5">
                                                                @foreach (['people' => 'People (Behaviour)', 'facilities' => 'Facilities', 'training' => 'Training', 'others' => 'Others'] as $rcVal => $rcLbl)
                                                                    <label
                                                                        class="flex items-center gap-2 cursor-pointer text-xs text-gray-700">
                                                                        <input type="radio"
                                                                            name="findings[0][root_cause]"
                                                                            value="{{ $rcVal }}"
                                                                            {{ old('findings.0.root_cause') === $rcVal ? 'checked' : '' }}
                                                                            class="text-red-600 focus:ring-red-500">
                                                                        {{ $rcLbl }}
                                                                    </label>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <label
                                                                class="block text-xs font-semibold text-gray-700 mb-2">Dept
                                                                Responsible <span class="text-red-500">*</span></label>
                                                            <select name="findings[0][department_id]" required
                                                                class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-400 bg-white">
                                                                <option value="">Pilih dept…</option>
                                                                @foreach ($departments as $dept)
                                                                    <option value="{{ $dept->id }}"
                                                                        {{ old('findings.0.department_id') == $dept->id ? 'selected' : '' }}>
                                                                        {{ $dept->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label
                                                                class="block text-xs font-semibold text-gray-700 mb-2">Documentation
                                                                <span class="text-red-500">*</span></label>
                                                            <input type="file" name="findings[0][photo]"
                                                                accept="image/*" required
                                                                class="w-full text-xs border border-gray-300 rounded-lg px-2 py-1.5 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-medium file:bg-red-50 file:text-red-700 hover:file:bg-red-100 bg-white">
                                                            <p class="text-xs text-gray-400 mt-1">Maks. 5MB.</p>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="block text-xs font-semibold text-gray-700 mb-1">Notes</label>
                                                        <input type="text" name="findings[0][keterangan]"
                                                            value="{{ old('findings.0.keterangan') }}"
                                                            placeholder="Catatan tambahan (opsional)…"
                                                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-400 bg-white">
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Submit buttons --}}
                                        <div class="flex gap-3 !mt-2">
                                            <button type="submit" id="submit-btn-{{ $policy->id }}"
                                                class="px-5 py-2 text-sm font-semibold text-white rounded-lg bg-red-600 hover:bg-red-700 disabled:opacity-40 disabled:cursor-not-allowed transition-opacity"
                                                {{ $hasItems ? 'disabled' : '' }}>
                                                Simpan Temuan NC
                                            </button>
                                            <button type="button" onclick="hideNCForm('{{ $ncFormId }}')"
                                                class="px-5 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                                Batal
                                            </button>
                                        </div>
                                    </form>

                                    {{-- Finding card template — inert HTML, cloned by JS for each selected item --}}
                                    <template id="finding-card-template-{{ $policy->id }}">
                                        <div
                                            class="finding-card space-y-4 p-4 rounded-xl border-2 border-red-200 bg-red-50/30">
                                            <div class="flex items-start justify-between gap-2">
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="card-number inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-600 text-white text-xs font-bold shrink-0">1</span>
                                                    <span class="card-item-name text-xs font-semibold text-red-700"></span>
                                                </div>
                                                <button type="button"
                                                    class="card-remove-btn shrink-0 text-gray-400 hover:text-red-500 text-xs font-medium">&#x2715;
                                                    Hapus</button>
                                            </div>
                                            <input type="hidden" class="input-item-id" value="">
                                            {{-- Lain-lain extra text (shown only for "Lain-lain" items) --}}
                                            <div class="lainlain-text-row hidden">
                                                <label
                                                    class="block text-xs font-semibold text-gray-700 mb-1">Spesifikasikan
                                                    temuan <span class="text-red-500">*</span></label>
                                                <input type="text"
                                                    class="input-item-text w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-400 bg-white"
                                                    placeholder="Tulis temuan Lain-lain secara spesifik…">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-1">Deskripsi
                                                    <span class="text-red-500">*</span></label>
                                                <textarea rows="2" required
                                                    class="input-description w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-400 bg-white"
                                                    placeholder="Jelaskan temuan secara detail…"></textarea>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-700 mb-2">Root
                                                        Cause <span class="text-red-500">*</span></label>
                                                    <div class="space-y-1.5">
                                                        <label
                                                            class="flex items-center gap-2 cursor-pointer text-xs text-gray-700"><input
                                                                type="radio" value="people"
                                                                class="text-red-600 focus:ring-red-500"> People
                                                            (Behaviour)</label>
                                                        <label
                                                            class="flex items-center gap-2 cursor-pointer text-xs text-gray-700"><input
                                                                type="radio" value="facilities"
                                                                class="text-red-600 focus:ring-red-500"> Facilities</label>
                                                        <label
                                                            class="flex items-center gap-2 cursor-pointer text-xs text-gray-700"><input
                                                                type="radio" value="training"
                                                                class="text-red-600 focus:ring-red-500"> Training</label>
                                                        <label
                                                            class="flex items-center gap-2 cursor-pointer text-xs text-gray-700"><input
                                                                type="radio" value="others"
                                                                class="text-red-600 focus:ring-red-500"> Others</label>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-700 mb-2">Dept
                                                        Responsible <span class="text-red-500">*</span></label>
                                                    <select required
                                                        class="input-department w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-400 bg-white">
                                                        <option value="">Pilih dept…</option>
                                                        @foreach ($departments as $dept)
                                                            <option value="{{ $dept->id }}">{{ $dept->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-semibold text-gray-700 mb-2">Documentation
                                                        <span class="text-red-500">*</span></label>
                                                    <input type="file" accept="image/*" required
                                                        class="input-photo w-full text-xs border border-gray-300 rounded-lg px-2 py-1.5 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-medium file:bg-red-50 file:text-red-700 hover:file:bg-red-100 bg-white">
                                                    <p class="text-xs text-gray-400 mt-1">Maks. 5MB.</p>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-1">Notes</label>
                                                <input type="text"
                                                    class="input-keterangan w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-400 bg-white"
                                                    placeholder="Catatan tambahan (opsional)…">
                                            </div>
                                        </div>
                                    </template>
                                </td>
                            </tr>
                        @endif

                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Follow-up button --}}
        {{-- @if (in_array(auth()->user()->role, ['admin', 'auditor']))
            @php $notComplied = $inspection->findings->where('verification_status', 'not_complied')->count(); @endphp
            @if ($notComplied > 0)
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-amber-800">{{ $notComplied }} temuan Not Complied</p>
                        <p class="text-xs text-amber-600 mt-0.5">Buat inspeksi tindak lanjut.</p>
                    </div>
                    <a href="{{ route('inspections.follow-up.create', $inspection) }}"
                        class="px-4 py-2 text-sm font-semibold rounded-lg"
                        style="background-color:#f4a823; color:#0f2e1c">
                        Buat Follow-up
                    </a>
                </div>
            @endif
        @endif --}}

    </div>

    <script>
        function toggleNCForm(id) {
            const row = document.getElementById(id);
            if (!row) return;
            if (row.classList.contains('hidden')) {
                row.classList.remove('hidden');
                row.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            } else {
                row.classList.add('hidden');
            }
        }

        function hideNCForm(id) {
            document.getElementById(id)?.classList.add('hidden');
        }

        // ── Per-item finding card system ──────────────────────────────────────────
        function onFindingItemChange(checkbox, policyId) {
            const itemId = checkbox.dataset.itemId;
            const itemText = checkbox.dataset.itemText;
            const isLainLain = checkbox.dataset.lainLain === '1';
            if (checkbox.checked) {
                addFindingCard(policyId, itemId, itemText, isLainLain);
            } else {
                removeFindingCard(policyId, itemId);
            }
            reindexFindingCards(policyId);
            updateSubmitBtn(policyId);
            const remainingCards = document.querySelectorAll(`#finding-cards-${policyId} .finding-card`).length;
            if (!checkbox.checked && remainingCards === 0) {
                hideNCForm('nc-form-' + policyId);
                return;
            }
            scrollFormView(policyId);
        }

        function addFindingCard(policyId, itemId, itemText, isLainLain) {
            const template = document.getElementById('finding-card-template-' + policyId);
            if (!template) return;
            const clone = template.content.cloneNode(true);
            const card = clone.querySelector('.finding-card');
            card.dataset.itemId = itemId;
            card.querySelector('.card-item-name').textContent = itemText;
            card.querySelector('.input-item-id').value = itemId;
            if (isLainLain) {
                card.querySelector('.lainlain-text-row').classList.remove('hidden');
            }
            card.querySelector('.card-remove-btn').addEventListener('click', function() {
                const cb = document.querySelector(
                    `.finding-item-cb[data-policy="${policyId}"][data-item-id="${itemId}"]`
                );
                if (cb) cb.checked = false;
                this.closest('.finding-card').remove();
                reindexFindingCards(policyId);
                updateSubmitBtn(policyId);
                const remainingCards = document.querySelectorAll(`#finding-cards-${policyId} .finding-card`).length;
                if (remainingCards === 0) {
                    hideNCForm('nc-form-' + policyId);
                    return;
                }
                scrollFormView(policyId);
            });
            const container = document.getElementById('finding-cards-' + policyId);
            container.appendChild(clone);
        }

        function getMainScrollContainer() {
            return document.querySelector('main.flex-1.overflow-y-auto');
        }

        function clampMainScroll() {
            const main = getMainScrollContainer();
            if (!main) return;
            const maxScrollTop = Math.max(0, main.scrollHeight - main.clientHeight);
            if (main.scrollTop > maxScrollTop) {
                main.scrollTop = maxScrollTop;
            }
        }

        // Scroll so the form sits correctly in the <main overflow-y-auto> container after any change.
        // Cards present → anchor submit button to bottom edge (card fills the visible area above).
        // No cards left  → show the form row from the top so chips are visible again.
        function scrollFormView(policyId) {
            requestAnimationFrame(() => {
                clampMainScroll();

                const count = document.querySelectorAll(`#finding-cards-${policyId} .finding-card`).length;
                if (count > 0) {
                    const submitBtn = document.getElementById('submit-btn-' + policyId);
                    if (submitBtn) {
                        submitBtn.scrollIntoView({
                            behavior: 'instant',
                            block: 'end'
                        });
                    }
                } else {
                    const formRow = document.getElementById('nc-form-' + policyId);
                    if (formRow) {
                        formRow.scrollIntoView({
                            behavior: 'instant',
                            block: 'nearest'
                        });
                    }
                }

                clampMainScroll();
            });
        }

        function removeFindingCard(policyId, itemId) {
            const card = document.querySelector(
                `#finding-cards-${policyId} .finding-card[data-item-id="${itemId}"]`
            );
            if (card) card.remove();
        }

        function reindexFindingCards(policyId) {
            const cards = document.querySelectorAll(`#finding-cards-${policyId} .finding-card`);
            const noMsg = document.getElementById('no-items-msg-' + policyId);
            if (noMsg) noMsg.classList.toggle('hidden', cards.length > 0);
            // Hide the container itself when empty so it doesn't add a dead-zone gap (space-y-5 margins)
            const cardsContainer = document.getElementById('finding-cards-' + policyId);
            if (cardsContainer) cardsContainer.classList.toggle('hidden', cards.length === 0);
            cards.forEach((card, i) => {
                const numEl = card.querySelector('.card-number');
                if (numEl) numEl.textContent = i + 1;
                const setName = (sel, name) => {
                    const el = card.querySelector(sel);
                    if (el) el.name = name;
                };
                setName('.input-item-id', `findings[${i}][item_id]`);
                setName('.input-item-text', `findings[${i}][item_text]`);
                setName('.input-description', `findings[${i}][description]`);
                setName('.input-department', `findings[${i}][department_id]`);
                setName('.input-photo', `findings[${i}][photo]`);
                setName('.input-keterangan', `findings[${i}][keterangan]`);
                card.querySelectorAll('input[type="radio"]').forEach(r => {
                    r.name = `findings[${i}][root_cause]`;
                });
            });
        }

        function updateSubmitBtn(policyId) {
            const count = document.querySelectorAll(`#finding-cards-${policyId} .finding-card`).length;
            const btn = document.getElementById('submit-btn-' + policyId);
            if (!btn) return;
            btn.disabled = count === 0;
            btn.textContent = count > 1 ? `Simpan ${count} Temuan NC` : 'Simpan Temuan NC';
        }
    </script>
@endsection
