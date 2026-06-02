@extends('layouts.public')
@section('title', 'Contact Us')

@section('content')
    <section class="py-16" style="background:#0f2e1c">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold text-white mb-3">Contact Us</h1>
            <p class="text-gray-400 max-w-xl mx-auto">Get in touch with our food safety team.</p>
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                {{-- Contact Info --}}
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Get in Touch</h2>
                    <div class="space-y-5">
                        @foreach ([['📞', 'Phone', '+60 3-1234 5678'], ['✉️', 'Email', 'info@compliancereporting.com'], ['📍', 'Address', 'Level 10, Menara Compliance, Kuala Lumpur, Malaysia'], ['🕐', 'Office Hours', 'Monday – Friday: 8:00 AM – 6:00 PM']] as [$icon, $label, $value])
                            <div class="flex gap-4">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center text-lg flex-shrink-0"
                                    style="background:#e8f5ee">
                                    {{ $icon }}
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">{{ $label }}
                                    </p>
                                    <p class="text-gray-700 text-sm font-medium">{{ $value }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Contact Form --}}
                <div class="bg-gray-50 rounded-2xl p-8">
                    <h3 class="font-bold text-gray-900 mb-5">Send us a Message</h3>
                    <form class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Your Name</label>
                            <input type="text"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none"
                                placeholder="John Doe">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none"
                                placeholder="john@example.com">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                            <input type="text"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none"
                                placeholder="Food safety inquiry">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                            <textarea rows="4"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none resize-none"
                                placeholder="Tell us about your food safety needs..."></textarea>
                        </div>
                        <button type="submit"
                            class="w-full py-2.5 rounded-lg text-white text-sm font-semibold transition hover:opacity-90"
                            style="background:#1b6840">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
