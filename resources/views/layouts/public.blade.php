<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Compliance Reporting System')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white text-gray-800 font-sans">

    {{-- Navigation --}}
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                <a href="{{ url('/') }}" class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background:#1b6840">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold" style="color:#1b6840">Compliance <span
                            style="color:#f4a823">RS</span></span>
                </a>

                {{-- CTA --}}
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="text-sm font-medium px-4 py-2 rounded-lg text-white transition"
                            style="background:#1b6840">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-sm font-medium text-gray-600 hover:text-green-700">Login</a>
                        <a href="{{ route('login') }}"
                            class="text-sm font-medium px-4 py-2 rounded-lg text-white transition"
                            style="background:#1b6840">Staff Portal</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer style="background:#0f2e1c" class="text-white mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#1b6840">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <span class="font-bold text-lg">Compliance <span style="color:#f4a823">RS</span></span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Ensuring the highest standards of compliance and hygiene across all operations.
                    </p>
                </div>
                <div>
                    <h4 class="font-semibold mb-3 text-sm uppercase tracking-wide" style="color:#f4a823">Quick Links
                    </h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('login') }}" class="hover:text-white transition">Staff Login</a></li>
                        <li><a href="{{ url('/') }}" class="hover:text-white transition">Home</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-xs text-gray-500">
                &copy; {{ date('Y') }} Compliance Reporting System. All rights reserved.
            </div>
        </div>
    </footer>

</body>

</html>
