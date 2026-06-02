@extends('layouts.public')
@section('title', 'Our Services')

@section('content')
    <section class="py-16" style="background:#0f2e1c">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold text-white mb-3">Our Services</h1>
            <p class="text-gray-400 max-w-xl mx-auto">Comprehensive food safety management solutions tailored to the
                hospitality and food service industry.</p>
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ([
            ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'title' => 'Food Safety Inspections', 'desc' => 'Scheduled and surprise food safety audits covering kitchen hygiene, food handling, storage temperature, and personal hygiene compliance.', 'tags' => ['Audits', 'Hygiene', 'Compliance']],
            ['icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', 'title' => 'Corrective Action Management', 'desc' => 'Full CAPA (Corrective and Preventive Action) system. Track findings from initial report through departmental response to final verification.', 'tags' => ['CAPA', 'Tracking', 'Closure']],
            ['icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'title' => 'Compliance Reporting', 'desc' => 'Real-time dashboards and exportable reports showing inspection history, open findings, department performance, and compliance trends.', 'tags' => ['Reports', 'Dashboard', 'Analytics']],
            ['icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'title' => 'Department Accountability', 'desc' => 'Findings are assigned directly to responsible departments. Department heads are notified and required to submit responses within defined timeframes.', 'tags' => ['Accountability', 'Departments', 'Workflow']],
            ['icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'title' => 'Food Safety Training', 'desc' => 'Identification of training gaps through inspection findings. Root cause categorization includes People, Facilities, Training, and Other factors.', 'tags' => ['Training', 'Root Cause', 'People']],
            ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title' => 'Audit Verification', 'desc' => 'All closed findings undergo a formal verification process by the original auditor, confirming compliance before a finding is permanently closed.', 'tags' => ['Verification', 'Compliance', 'Closure']],
        ] as $s)
                    <div class="border border-gray-200 rounded-2xl p-6 hover:shadow-md transition">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4" style="background:#e8f5ee">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                style="color:#1b6840">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="{{ $s['icon'] }}" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg mb-2">{{ $s['title'] }}</h3>
                        <p class="text-gray-500 text-sm leading-relaxed mb-4">{{ $s['desc'] }}</p>
                        <div class="flex flex-wrap gap-1">
                            @foreach ($s['tags'] as $tag)
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium"
                                    style="background:#e8f5ee;color:#1b6840">{{ $tag }}</span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
