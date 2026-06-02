@extends('layouts.auth')
@section('title', 'Login')

@section('content')
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-lg p-8">
            {{-- Header --}}
            <div class="flex items-center gap-2 mb-8 lg:hidden">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#1b6840">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <span class="text-xl font-bold" style="color:#1b6840">Compliance <span style="color:#f4a823">Reporting
                        System</span></span>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-1">Welcome back</h1>
            <p class="text-sm text-gray-500 mb-6">Sign in to the staff portal</p>

            {{-- Errors --}}
            @if ($errors->any())
                <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:border-transparent @error('email') border-red-400 @enderror"
                        style="--tw-ring-color:#1b6840" placeholder="you@example.com">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:border-transparent"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded">
                        <span class="text-gray-600">Remember me</span>
                    </label>
                </div>

                <button type="submit"
                    class="w-full py-2.5 px-4 rounded-lg text-white text-sm font-semibold transition hover:opacity-90"
                    style="background:#1b6840">
                    Sign in
                </button>
            </form>

            {{-- Demo credentials --}}
            <div class="mt-6 rounded-xl border border-gray-200 bg-gray-50 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Demo Accounts</p>
                <div class="space-y-2 text-xs">
                    @foreach ([['Admin', 'admin@foodcontrol.com', 'Admin'], ['Auditor', 'auditor@foodcontrol.com', 'Auditor'], ['Auditee', 'kitchen@foodcontrol.com', 'FB Product'], ['Auditee', 'fnb@foodcontrol.com', 'FB Service']] as [$role, $email, $dept])
                        <button type="button"
                            onclick="document.getElementById('email').value='{{ $email }}'; document.getElementById('password').value='password';"
                            class="w-full flex items-center justify-between px-3 py-2 rounded-lg bg-white border border-gray-200 hover:border-green-400 hover:bg-green-50 transition-colors text-left">
                            <span class="font-medium text-gray-700">{{ $email }}</span>
                            <span
                                class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium
                            @if ($role === 'Admin') bg-purple-100 text-purple-700
                            @elseif($role === 'Auditor') bg-blue-100 text-blue-700
                            @else bg-amber-100 text-amber-700 @endif">
                                {{ $role }}{{ $dept !== $role ? ' · ' . $dept : '' }}
                            </span>
                        </button>
                    @endforeach
                    <p class="text-gray-400 text-center pt-1">Password: <code class="font-mono">password</code></p>
                </div>
            </div>
        </div>
    </div>
@endsection
