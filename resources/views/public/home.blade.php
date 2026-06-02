@extends('layouts.public')
@section('title', 'Home')

@section('content')

    {{-- ── Hero ── --}}
    <section class="relative overflow-hidden py-20 lg:py-32"
        style="background:linear-gradient(135deg,#0f2e1c 0%,#1b6840 100%)">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full mb-4"
                        style="background:#f4a823;color:#0f2e1c">
                        Food Safety Management
                    </span>
                    <h1 class="text-4xl lg:text-5xl font-bold text-white leading-tight mb-6">
                        The Experts in<br><span style="color:#f4a823">Food Safety Control</span>
                    </h1>
                    <p class="text-gray-300 text-lg leading-relaxed mb-8">
                        We provide comprehensive food safety inspection, audit management, and compliance solutions for
                        hotels, restaurants, and food service establishments.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('services') }}"
                            class="inline-flex items-center justify-center px-6 py-3 rounded-lg font-semibold text-sm transition hover:opacity-90"
                            style="background:#f4a823;color:#0f2e1c">
                            Explore Our Services
                        </a>
                        <a href="{{ route('contact') }}"
                            class="inline-flex items-center justify-center px-6 py-3 rounded-lg font-semibold text-sm border border-white text-white hover:bg-white hover:text-green-900 transition">
                            Contact Us
                        </a>
                    </div>
                </div>

                <div class="hidden lg:grid grid-cols-2 gap-4">
                    @foreach ([['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Certified Auditors', 'desc' => 'ISO-trained food safety professionals'], ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'title' => 'CAPA Tracking', 'desc' => 'Full corrective action lifecycle'], ['icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'title' => 'Real-time Reports', 'desc' => 'Live compliance dashboards'], ['icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'title' => 'Secure & Confidential', 'desc' => 'Role-based access control']] as $card)
                        <div class="rounded-xl p-5" style="background:rgba(255,255,255,0.08)">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-3"
                                style="background:#1b6840">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="{{ $card['icon'] }}" />
                                </svg>
                            </div>
                            <h3 class="text-white font-semibold text-sm">{{ $card['title'] }}</h3>
                            <p class="text-gray-400 text-xs mt-1">{{ $card['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ── Stats ── --}}
    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                @foreach ([['500+', 'Inspections Completed'], ['98%', 'Compliance Rate'], ['50+', 'Departments Served'], ['24/7', 'Monitoring']] as [$num, $label])
                    <div>
                        <div class="text-3xl font-bold" style="color:#1b6840">{{ $num }}</div>
                        <div class="text-sm text-gray-500 mt-1">{{ $label }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── How It Works ── --}}
    <section class="py-20" style="background:#e8f5ee">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-3">How Compliance Reporting System Works</h2>
                <p class="text-gray-500 max-w-xl mx-auto">Our structured 3-step inspection process ensures every food safety
                    issue is identified, acted upon, and verified.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach ([['01', 'Inspection & Finding', 'Our certified auditors conduct thorough food safety inspections, documenting findings with root cause analysis assigned to responsible departments.', '#1b6840'], ['02', 'Corrective Action', 'Department heads review findings and submit corrective and preventive action plans within the system to address identified issues.', '#f4a823'], ['03', 'Verification', 'Auditors re-inspect and verify all corrective actions, marking each finding as Complied or Not Complied with a verification date.', '#1b6840']] as [$step, $title, $desc, $color])
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm mb-4"
                            style="background:{{ $color }}">
                            {{ $step }}
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg mb-2">{{ $title }}</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── Industries ── --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-3">Industries We Serve</h2>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach (['Hotels', 'Restaurants', 'Food Manufacturing', 'Catering', 'Supermarkets', 'Hospitals'] as $industry)
                    <div
                        class="border border-gray-200 rounded-xl p-4 text-center hover:border-green-400 transition cursor-default">
                        <div class="text-2xl mb-2">
                            {{ ['Hotels' => '🏨', 'Restaurants' => '🍽️', 'Food Manufacturing' => '🏭', 'Catering' => '👨‍🍳', 'Supermarkets' => '🛒', 'Hospitals' => '🏥'][$industry] }}
                        </div>
                        <p class="text-xs font-medium text-gray-600">{{ $industry }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── CTA ── --}}
    <section class="py-20" style="background:#1b6840">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Ready to Improve Your Food Safety Standards?</h2>
            <p class="text-green-100 mb-8">Contact our team today and let us help you achieve full compliance.</p>
            <a href="{{ route('contact') }}"
                class="inline-block px-8 py-3 rounded-lg font-semibold text-sm transition hover:opacity-90"
                style="background:#f4a823;color:#0f2e1c">
                Get in Touch
            </a>
        </div>
    </section>

@endsection
