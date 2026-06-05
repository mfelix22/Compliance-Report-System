@extends('layouts.app')
@section('title', 'Inspections')
@section('page-title', 'Inspections')
@section('page-subtitle', 'All food safety inspection records')

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
    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('inspections.index') }}"
        class="flex flex-wrap items-end gap-3 mb-4">
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs font-medium text-gray-500 mb-1">Outlet</label>
            <select name="outlet_id"
                class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white">
                <option value="">All Outlets</option>
                @foreach ($outlets as $outlet)
                    <option value="{{ $outlet->id }}" {{ request('outlet_id') == $outlet->id ? 'selected' : '' }}>
                        {{ $outlet->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="min-w-[140px]">
            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
            <select name="status"
                class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white">
                <option value="">All Statuses</option>
                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit"
                class="px-4 py-2 text-sm font-medium text-white rounded-lg transition"
                style="background:#1b6840">
                Filter
            </button>
            @if (request('outlet_id') || request('status'))
                <a href="{{ route('inspections.index') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    Clear
                </a>
            @endif
        </div>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100" style="background:#e8f5ee">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Ref No.</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Title /
                        Outlet</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Auditor(s)
                    </th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Findings
                    </th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($inspections as $inspection)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 font-mono text-xs text-gray-600">{{ $inspection->reference_no }}</td>
                        <td class="px-5 py-3">
                            <div class="font-medium text-gray-900">{{ $inspection->title }}</div>
                            @if ($inspection->outlet)
                                <div class="text-xs text-gray-400 mt-0.5">{{ $inspection->outlet->name }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-500">{{ $inspection->inspection_date->format('d M Y') }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $inspection->auditors->pluck('name')->implode(', ') ?: '–' }}
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span
                                class="inline-block px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                {{ $inspection->findings_count ?? $inspection->findings->count() }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <span
                                class="text-xs px-2 py-0.5 rounded-full font-medium
                            @if ($inspection->status === 'open') bg-amber-100 text-amber-700
                            @elseif($inspection->status === 'closed') bg-green-100 text-green-700
                            @else bg-blue-100 text-blue-700 @endif">
                                {{ ucfirst(str_replace('_', ' ', $inspection->status)) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('inspections.show', $inspection) }}"
                                class="text-xs font-medium hover:underline" style="color:#1b6840">View →</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-sm text-gray-400">
                            No inspections found. Create your first inspection to get started.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($inspections->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $inspections->links() }}
            </div>
        @endif
    </div>
@endsection
