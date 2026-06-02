@extends('layouts.public')
@section('title', 'About Us')

@section('content')
    <section class="py-16" style="background:#0f2e1c">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold text-white mb-3">About Compliance Reporting System</h1>
            <p class="text-gray-400 max-w-xl mx-auto">Dedicated to elevating food safety standards in the hospitality
                industry.</p>
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center mb-20">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Our Mission</h2>
                    <p class="text-gray-500 leading-relaxed mb-4">
                        Compliance Reporting System exists to make food safety management simple, systematic, and effective.
                        We believe that
                        every food establishment deserves a robust inspection and compliance system that protects both
                        guests and staff.
                    </p>
                    <p class="text-gray-500 leading-relaxed">
                        Modelled after the best practices in compliance management, our system provides a clear workflow
                        from initial inspection findings all the way through to verified corrective actions — giving
                        management complete visibility and accountability.
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    @foreach ([['🎯', 'Mission-Driven', 'Every decision is focused on protecting food safety'], ['🔬', 'Evidence-Based', 'Findings backed by root cause analysis'], ['🤝', 'Collaborative', 'Auditors and departments working together'], ['📈', 'Continuous Improvement', 'CAPA cycles drive ongoing excellence']] as [$icon, $title, $desc])
                        <div class="rounded-xl p-5" style="background:#e8f5ee">
                            <div class="text-2xl mb-2">{{ $icon }}</div>
                            <h4 class="font-semibold text-gray-900 text-sm mb-1">{{ $title }}</h4>
                            <p class="text-gray-500 text-xs">{{ $desc }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-gray-100 pt-16 text-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-12">Our Team</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-3xl mx-auto">
                    @foreach ([['Food Safety Auditors', 'Certified professionals who conduct thorough, objective inspections.'], ['Department Liaisons', 'Dedicated contacts ensuring smooth communication with each department.'], ['Quality Analysts', 'Experts who analyse compliance trends and recommend improvements.']] as [$role, $desc])
                        <div class="text-center">
                            <div class="w-16 h-16 rounded-full mx-auto flex items-center justify-center text-2xl mb-3"
                                style="background:#e8f5ee">
                                {{ ['Food Safety Auditors' => '🔍', 'Department Liaisons' => '📋', 'Quality Analysts' => '📊'][$role] }}
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-1">{{ $role }}</h4>
                            <p class="text-gray-500 text-sm">{{ $desc }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection
