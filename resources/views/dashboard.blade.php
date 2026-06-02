@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', auth()->user()->isAuditee() ? 'Temuan Saya' : 'Dashboard')
@section('page-subtitle', auth()->user()->isAuditee() ? (auth()->user()->department?->name ?? 'My Department') . ' –
    Corrective Action Tasks' : 'Food Safety Management Overview')

@section('header-actions')
    @if (auth()->user()->isAuditor() || auth()->user()->isAdmin())
        <a href="{{ route('inspections.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white text-sm font-medium"
            style="background:#1b6840">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Inspection
        </a>
    @endif
@endsection

@section('content')

    @if (auth()->user()->isAuditee())
        {{-- ════════════════════════════════════════
         AUDITEE DASHBOARD
    ════════════════════════════════════════ --}}

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            @foreach ([['label' => 'Perlu Respon', 'value' => $stats['needs_response'], 'color' => '#b45309', 'bg' => '#fef3c7', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'], ['label' => 'Overdue', 'value' => $stats['overdue'], 'color' => '#dc2626', 'bg' => '#fee2e2', 'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'], ['label' => 'Sudah Ditutup', 'value' => $stats['closed'], 'color' => '#065f46', 'bg' => '#d1fae5', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'], ['label' => 'Menunggu Verifikasi', 'value' => $stats['pending_verify'], 'color' => '#1e40af', 'bg' => '#dbeafe', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z']] as $card)
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide leading-tight">
                            {{ $card['label'] }}</p>
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0"
                            style="background:{{ $card['bg'] }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                style="color:{{ $card['color'] }}">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="{{ $card['icon'] }}" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold" style="color:{{ $card['color'] }}">{{ $card['value'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Findings List --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Daftar Temuan Departemen Anda</h3>
                <p class="text-xs text-gray-400 mt-0.5">Klik "Isi Respons" untuk mengisi tindakan korektif & preventif</p>
            </div>

            @forelse ($myFindings as $finding)
                @php
                    $needsResponse = $finding->status === 'open' && empty($finding->corrective_action);
                    $isOverdue = $finding->isOverdue;
                    $isClosed = $finding->status === 'closed';
                @endphp
                <div class="px-5 py-4 border-b border-gray-50 last:border-b-0">
                    <div class="flex items-start gap-4">

                        {{-- Status dot --}}
                        <div class="flex-shrink-0 mt-1.5">
                            @if ($isClosed)
                                <span class="inline-block w-2.5 h-2.5 rounded-full bg-green-500"></span>
                            @elseif ($isOverdue)
                                <span class="inline-block w-2.5 h-2.5 rounded-full bg-red-500"></span>
                            @elseif ($needsResponse)
                                <span class="inline-block w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                            @else
                                <span class="inline-block w-2.5 h-2.5 rounded-full bg-blue-400"></span>
                            @endif
                        </div>

                        {{-- Main info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-800 leading-snug">
                                        {{ $finding->policyItem?->text ?? ($finding->finding ?? '(no description)') }}
                                    </p>
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 mt-1">
                                        <span class="text-xs text-gray-400">
                                            {{ $finding->inspection->reference_no ?? '–' }}
                                            @if ($finding->inspection->outlet)
                                                · {{ $finding->inspection->outlet->name }}
                                            @endif
                                        </span>
                                        @if ($finding->due_date)
                                            <span
                                                class="text-xs {{ $isOverdue ? 'text-red-600 font-medium' : 'text-gray-400' }}">
                                                Deadline: {{ $finding->due_date->format('d M Y') }}
                                                @if ($isOverdue)
                                                    ({{ abs($finding->daysUntilDue) }}d overdue)
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                    @if (!$needsResponse && $finding->corrective_action)
                                        <p class="text-xs text-gray-500 mt-1.5 line-clamp-1">
                                            <span class="font-medium text-gray-600">CA:</span>
                                            {{ $finding->corrective_action }}
                                        </p>
                                    @endif
                                </div>

                                {{-- Badge + CTA --}}
                                <div class="flex flex-col items-end gap-2 flex-shrink-0">
                                    @if ($isClosed)
                                        <span
                                            class="text-xs px-2 py-0.5 rounded-full font-medium bg-green-100 text-green-700">
                                            Closed
                                        </span>
                                        @if ($finding->verification_status === 'pending')
                                            <span
                                                class="text-xs px-2 py-0.5 rounded-full font-medium bg-blue-100 text-blue-700">
                                                Menunggu verifikasi
                                            </span>
                                        @elseif ($finding->verification_status === 'complied')
                                            <span
                                                class="text-xs px-2 py-0.5 rounded-full font-medium bg-green-100 text-green-800">
                                                ✓ Verified
                                            </span>
                                        @elseif ($finding->verification_status === 'not_complied')
                                            <span
                                                class="text-xs px-2 py-0.5 rounded-full font-medium bg-red-100 text-red-700">
                                                Not Complied
                                            </span>
                                        @endif
                                    @elseif ($needsResponse)
                                        <a href="{{ route('findings.edit', $finding) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-white text-xs font-semibold"
                                            style="background:#1b6840">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Isi Respons
                                        </a>
                                    @else
                                        {{-- Has response but still open --}}
                                        <span
                                            class="text-xs px-2 py-0.5 rounded-full font-medium bg-amber-100 text-amber-700">Open</span>
                                        <a href="{{ route('findings.edit', $finding) }}"
                                            class="text-xs text-gray-500 hover:text-gray-700 underline">Edit</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-5 py-12 text-center">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-gray-400">Tidak ada temuan untuk departemen Anda.</p>
                </div>
            @endforelse
        </div>
    @else
        {{-- ════════════════════════════════════════
         ADMIN / AUDITOR DASHBOARD
    ════════════════════════════════════════ --}}

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            @foreach ([['label' => 'Total Inspections', 'value' => $stats['total_inspections'], 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2', 'color' => '#1b6840', 'bg' => '#e8f5ee'], ['label' => 'Open Findings', 'value' => $stats['open_findings'], 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'color' => '#b45309', 'bg' => '#fef3c7'], ['label' => 'Closed Findings', 'value' => $stats['closed_findings'], 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => '#065f46', 'bg' => '#d1fae5'], ['label' => 'Pending Verification', 'value' => $stats['pending_verify'], 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => '#1e40af', 'bg' => '#dbeafe']] as $card)
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ $card['label'] }}</p>
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                            style="background:{{ $card['bg'] }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                style="color:{{ $card['color'] }}">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="{{ $card['icon'] }}" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold" style="color:{{ $card['color'] }}">{{ $card['value'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Recent Findings --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">Recent Findings</h3>
                    <a href="{{ route('reports.index') }}" class="text-xs font-medium hover:underline"
                        style="color:#1b6840">View All</a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($recentFindings as $finding)
                        <div class="px-5 py-3 flex items-start gap-3">
                            <div class="flex-shrink-0 mt-0.5">
                                @if ($finding->status === 'open')
                                    <span class="inline-block w-2 h-2 rounded-full bg-amber-400 mt-1.5"></span>
                                @else
                                    <span class="inline-block w-2 h-2 rounded-full bg-green-500 mt-1.5"></span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-800 truncate">{{ $finding->finding }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-xs text-gray-400">{{ $finding->department->name ?? '–' }}</span>
                                    <span class="text-gray-200">•</span>
                                    <span
                                        class="text-xs text-gray-400">{{ $finding->inspection->reference_no ?? '–' }}</span>
                                </div>
                            </div>
                            <span
                                class="text-xs px-2 py-0.5 rounded-full font-medium flex-shrink-0
                        {{ $finding->status === 'open' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }}">
                                {{ ucfirst($finding->status) }}
                            </span>
                        </div>
                    @empty
                        <div class="px-5 py-8 text-center text-sm text-gray-400">No findings yet.</div>
                    @endforelse
                </div>
            </div>

            {{-- Recent Inspections --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">Recent Inspections</h3>
                    <a href="{{ route('inspections.index') }}" class="text-xs font-medium hover:underline"
                        style="color:#1b6840">View All</a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($recentInspections as $inspection)
                        <a href="{{ route('inspections.show', $inspection) }}"
                            class="px-5 py-3 flex items-center gap-3 hover:bg-gray-50 transition block">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $inspection->title }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $inspection->reference_no }} ·
                                    {{ $inspection->inspection_date->format('d M Y') }}</p>
                            </div>
                            <span
                                class="text-xs px-2 py-0.5 rounded-full font-medium flex-shrink-0
                        @if ($inspection->status === 'open') bg-amber-100 text-amber-700
                        @elseif($inspection->status === 'closed') bg-green-100 text-green-700
                        @else bg-blue-100 text-blue-700 @endif">
                                {{ ucfirst(str_replace('_', ' ', $inspection->status)) }}
                            </span>
                        </a>
                    @empty
                        <div class="px-5 py-8 text-center text-sm text-gray-400">No inspections yet.</div>
                    @endforelse
                </div>
            </div>

        </div>

    @endif
@endsection
