@extends('layouts.app')
@section('title', 'Reports')
@section('page-title', 'Reports')
@section('page-subtitle', 'Filter and review all findings across all inspections')

@section('content')

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach ([['Open', $summary['open'], 'bg-amber-100', 'text-amber-700'], ['Closed', $summary['closed'], 'bg-green-100', 'text-green-700'], ['Complied', $summary['complied'], 'bg-emerald-100', 'text-emerald-700'], ['Not Complied', $summary['not_complied'], 'bg-red-100', 'text-red-700']] as [$label, $val, $bg, $color])
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
                <p class="text-2xl font-bold {{ $color }}">{{ $val }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $label }}</p>
            </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
        <form method="GET" action="{{ route('reports.index') }}" class="space-y-3">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <select name="outlet_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">All Outlets</option>
                    @foreach ($outlets as $outlet)
                        <option value="{{ $outlet->id }}" {{ request('outlet_id') == $outlet->id ? 'selected' : '' }}>
                            {{ $outlet->name }}</option>
                    @endforeach
                </select>
                <select name="department_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">All Departments</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}</option>
                    @endforeach
                </select>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">All Status</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
                <select name="verification_status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">All Verifications</option>
                    <option value="pending" {{ request('verification_status') === 'pending' ? 'selected' : '' }}>Pending
                    </option>
                    <option value="complied" {{ request('verification_status') === 'complied' ? 'selected' : '' }}>Complied
                    </option>
                    <option value="not_complied" {{ request('verification_status') === 'not_complied' ? 'selected' : '' }}>
                        Not Complied</option>
                </select>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <select name="root_cause" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">All Root Causes</option>
                    @foreach (['people' => 'People', 'facilities' => 'Facilities', 'training' => 'Training', 'others' => 'Others'] as $val => $label)
                        <option value="{{ $val }}" {{ request('root_cause') === $val ? 'selected' : '' }}>
                            {{ $label }}</option>
                    @endforeach
                </select>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div class="flex items-center justify-between">
                <div class="flex gap-2">
                    <button type="submit" class="px-5 py-2 rounded-lg text-white text-sm font-medium hover:opacity-90"
                        style="background:#1b6840">Filter</button>
                    @if (request()->hasAny(['outlet_id', 'department_id', 'status', 'verification_status', 'root_cause', 'date_from', 'date_to']))
                        <a href="{{ route('reports.index') }}"
                            class="px-5 py-2 rounded-lg text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200">
                            Clear
                        </a>
                    @endif
                </div>
                <span class="text-xs text-gray-400">{{ $findings->total() }}
                    result{{ $findings->total() !== 1 ? 's' : '' }}</span>
            </div>
        </form>
    </div>

    {{-- Findings Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs">
                <thead>
                    <tr class="border-b border-gray-100" style="background:#e8f5ee">
                        <th class="px-4 py-3 text-left font-semibold text-gray-500 uppercase tracking-wide">Inspection</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-500 uppercase tracking-wide">Finding</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-500 uppercase tracking-wide">Root Cause</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-500 uppercase tracking-wide">Department</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-500 uppercase tracking-wide">Verification
                        </th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-500 uppercase tracking-wide">Verified</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($findings as $finding)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3">
                                <p class="font-mono text-gray-600">{{ $finding->inspection->reference_no ?? '–' }}</p>
                                <p class="text-gray-400 mt-0.5">
                                    {{ $finding->inspection->inspection_date?->format('d M Y') }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-700 max-w-xs">
                                <p class="line-clamp-2">{{ $finding->finding }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-block px-2 py-0.5 rounded-full font-medium
                                @if ($finding->root_cause === 'people') bg-purple-100 text-purple-700
                                @elseif($finding->root_cause === 'facilities') bg-blue-100 text-blue-700
                                @elseif($finding->root_cause === 'training') bg-orange-100 text-orange-700
                                @else bg-gray-100 text-gray-600 @endif">
                                    {{ ucfirst($finding->root_cause) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $finding->department->name ?? '–' }}</td>
                            <td class="px-4 py-3 text-center">
                                <span
                                    class="inline-block px-2 py-0.5 rounded-full font-medium
                                {{ $finding->status === 'open' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }}">
                                    {{ ucfirst($finding->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($finding->verification_status === 'complied')
                                    <span
                                        class="inline-block px-2 py-0.5 rounded-full font-medium bg-green-100 text-green-700">Complied</span>
                                @elseif($finding->verification_status === 'not_complied')
                                    <span
                                        class="inline-block px-2 py-0.5 rounded-full font-medium bg-red-100 text-red-700">Not
                                        Complied</span>
                                @else
                                    <span class="text-gray-400">Pending</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-gray-500">
                                {{ $finding->verification_date?->format('d M Y') ?? '–' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('inspections.show', $finding->inspection_id) }}"
                                    class="font-medium hover:underline" style="color:#1b6840">View →</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400">No findings match your
                                filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($findings->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $findings->links() }}
            </div>
        @endif
    </div>
@endsection
