<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') – Compliance Reporting System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex" style="background:#e8f5ee">

    <div class="hidden lg:flex lg:w-1/2 flex-col justify-between p-12" style="background:#0f2e1c">
        <div class="flex items-center gap-2">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background:#1b6840">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <span class="text-2xl font-bold text-white">Compliance <span style="color:#f4a823">Reporting
                    System</span></span>
        </div>

        <div>
            <h2 class="text-4xl font-bold text-white leading-tight mb-4">
                Compliance<br>Reporting System
            </h2>
            <p class="text-gray-300 text-lg leading-relaxed">
                Track inspections, manage findings, and ensure compliance with food safety standards across all
                departments.
            </p>

            <div class="mt-8 space-y-4">
                @foreach (['Inspection Tracking & Reporting', 'Corrective & Preventive Action (CAPA)', 'Department Compliance Monitoring', 'Audit Trail & Verification'] as $feat)
                    <div class="flex items-center gap-3">
                        <div class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0"
                            style="background:#1b6840">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span class="text-gray-300 text-sm">{{ $feat }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <p class="text-gray-600 text-xs">© {{ date('Y') }} Compliance Reporting System. All rights reserved.</p>
    </div>

    <div class="flex-1 flex items-center justify-center p-8">
        @yield('content')
    </div>

</body>

</html>
