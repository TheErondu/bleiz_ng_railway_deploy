@extends('layouts.app')

@section('content')
    {{-- Hero Section --}}
    <section
        class="relative min-h-screen bg-gradient-to-br from-gray-900 via-blue-900 to-gray-900 text-white overflow-hidden">
        {{-- Animated Background --}}
        <div class="absolute inset-0 bg-[url('/images/hero-bg.jpg')] bg-cover bg-center opacity-20"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 via-purple-600/10 to-gray-900/40"></div>

        {{-- Floating Elements --}}
        <div class="absolute top-20 left-10 w-32 h-32 bg-blue-500/10 rounded-full blur-xl animate-pulse"></div>
        <div class="absolute bottom-32 right-16 w-48 h-48 bg-purple-500/10 rounded-full blur-2xl animate-pulse delay-1000">
        </div>
        <div class="absolute top-1/2 left-1/4 w-24 h-24 bg-cyan-400/10 rounded-full blur-lg animate-bounce delay-500"></div>

        <div class="relative z-10 container mx-auto px-6 py-32 min-h-screen flex items-center">
            <div class="w-full text-center">
                <div class="animate-fade-in-up">
                    <h1
                        class="text-5xl md:text-7xl font-black mb-6 bg-gradient-to-r from-white via-blue-200 to-cyan-300 bg-clip-text text-transparent leading-tight">
                        Smart Loans,<br>
                        <span class="text-4xl md:text-6xl">Built for Nigerians</span>
                    </h1>
                </div>

                <div class="animate-fade-in-up delay-300">
                    <p class="text-xl md:text-2xl mb-12 text-gray-300 max-w-3xl mx-auto leading-relaxed">
                        Get the capital you need without hidden fees or stress.
                        <span class="text-cyan-300">Fast, secure, and transparent.</span>
                    </p>
                </div>

                <div class="animate-fade-in-up delay-500 flex flex-col sm:flex-row justify-center gap-6 mb-16">
                    <a href="{{ route('onboarding.apply') }}"
                        class="group relative bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 transform hover:scale-105 hover:shadow-2xl hover:shadow-blue-500/25">
                        <span class="relative z-10">Apply for a Loan</span>
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-xl">
                        </div>
                    </a>

                    <a href="#how-it-works"
                        class="group bg-white/10 backdrop-blur-md border border-white/20 text-white hover:bg-white/20 px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 transform hover:scale-105">
                        Learn More
                        <span class="inline-block ml-2 transition-transform group-hover:translate-x-1">→</span>
                    </a>
                </div>

                {{-- Trust Indicators --}}
                <div class="animate-fade-in-up delay-700 flex justify-center items-center gap-8 text-sm text-gray-400">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span>100% Secure</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse delay-300"></div>
                        <span>5-Minute Approval</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-purple-400 rounded-full animate-pulse delay-700"></div>
                        <span>No Hidden Fees</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Scroll Indicator --}}
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <div class="w-6 h-10 border-2 border-white/30 rounded-full flex justify-center">
                <div class="w-1 h-3 bg-white/60 rounded-full mt-2 animate-pulse"></div>
            </div>
        </div>
    </section>

    {{-- How It Works --}}
    <section id="how-it-works" class="py-32 bg-gradient-to-b from-white to-gray-50 relative overflow-hidden">
        {{-- Background Pattern --}}
        <div class="absolute inset-0 opacity-5">
            <div
                class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(circle_at_25%_25%,_#3b82f6_1px,_transparent_1px)] bg-[length:50px_50px]">
            </div>
        </div>

        <div class="container mx-auto px-6 text-center relative z-10">
            <div class="animate-fade-in-up">
                <h2 class="text-4xl md:text-5xl font-black mb-6 text-gray-900">How It Works</h2>
                <p class="text-xl text-gray-600 mb-20 max-w-2xl mx-auto">Simple steps to get your loan approved and funds in
                    your account</p>
            </div>

            <div class="grid gap-8 md:grid-cols-4 max-w-6xl mx-auto">
                @php
                    $steps = [
                        [
                            'icon' => '📝',
                            'title' => 'Register',
                            'desc' => 'Create your account to get started',
                            'color' => 'from-blue-500 to-cyan-500',
                        ],
                        [
                            'icon' => '📄',
                            'title' => 'Apply',
                            'desc' => 'Fill out a simple loan request',
                            'color' => 'from-purple-500 to-pink-500',
                        ],
                        [
                            'icon' => '🔎',
                            'title' => 'Review',
                            'desc' => 'We evaluate your request quickly',
                            'color' => 'from-green-500 to-emerald-500',
                        ],
                        [
                            'icon' => '💵',
                            'title' => 'Receive Funds',
                            'desc' => 'Cash is disbursed directly to you',
                            'color' => 'from-orange-500 to-red-500',
                        ],
                    ];
                @endphp

                @foreach ($steps as $index => $step)
                    <div class="group animate-fade-in-up delay-{{ ($index + 1) * 200 }} relative">
                        {{-- Step Number --}}
                        <div
                            class="absolute -top-4 -left-4 w-8 h-8 bg-gradient-to-r {{ $step['color'] }} rounded-full flex items-center justify-center text-white font-bold text-sm z-10">
                            {{ $index + 1 }}
                        </div>

                        {{-- Card --}}
                        <div
                            class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 transform group-hover:scale-105 group-hover:-translate-y-2 border border-gray-100 relative overflow-hidden">
                            {{-- Gradient Overlay --}}
                            <div
                                class="absolute inset-0 bg-gradient-to-br {{ $step['color'] }} opacity-0 group-hover:opacity-5 transition-opacity duration-300">
                            </div>

                            <div class="relative z-10">
                                <div
                                    class="text-5xl mb-6 filter drop-shadow-lg transform group-hover:scale-110 transition-transform duration-300">
                                    {{ $step['icon'] }}
                                </div>
                                <h3
                                    class="text-2xl font-bold mb-4 text-gray-900 group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:{{ $step['color'] }} group-hover:bg-clip-text transition-all duration-300">
                                    {{ $step['title'] }}
                                </h3>
                                <p class="text-gray-600 leading-relaxed">{{ $step['desc'] }}</p>
                            </div>
                        </div>

                        {{-- Connection Line --}}
                        @if ($index < 3)
                            <div
                                class="hidden md:block absolute top-1/2 -right-4 w-8 h-0.5 bg-gradient-to-r from-gray-300 to-transparent transform -translate-y-1/2 z-0">
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Why Choose Us --}}
    <section class="py-32 bg-gradient-to-b from-gray-50 to-white relative overflow-hidden">
        {{-- Background Elements --}}
        <div class="absolute top-20 right-10 w-32 h-32 bg-blue-200/30 rounded-full blur-2xl"></div>
        <div class="absolute bottom-20 left-10 w-48 h-48 bg-purple-200/20 rounded-full blur-3xl"></div>

        <div class="container mx-auto px-6 text-center relative z-10">
            <div class="animate-fade-in-up">
                <h2 class="text-4xl md:text-5xl font-black mb-6 text-gray-900">Why Choose Us</h2>
                <p class="text-xl text-gray-600 mb-20 max-w-2xl mx-auto">Experience the difference with our customer-first
                    approach</p>
            </div>

            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4 max-w-6xl mx-auto">
                @php
                    $reasons = [
                        [
                            'icon' => '🔒',
                            'title' => 'Secure',
                            'desc' => 'Your data is safe and encrypted with bank-level security',
                            'color' => 'from-green-500 to-emerald-600',
                        ],
                        [
                            'icon' => '⚡',
                            'title' => 'Fast',
                            'desc' => 'Quick approval and disbursal within minutes',
                            'color' => 'from-yellow-500 to-orange-600',
                        ],
                        [
                            'icon' => '💰',
                            'title' => 'Transparent',
                            'desc' => 'No hidden fees or surprise rates, ever',
                            'color' => 'from-blue-500 to-cyan-600',
                        ],
                        [
                            'icon' => '👥',
                            'title' => 'Backed by Investors',
                            'desc' => 'Funded from a pooled capital base for reliability',
                            'color' => 'from-purple-500 to-pink-600',
                        ],
                    ];
                @endphp

                @foreach ($reasons as $index => $reason)
                    <div class="group animate-fade-in-up delay-{{ ($index + 1) * 200 }}">
                        <div
                            class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 transform group-hover:scale-105 group-hover:-translate-y-2 border border-gray-100 relative overflow-hidden h-full">
                            {{-- Background Gradient --}}
                            <div
                                class="absolute inset-0 bg-gradient-to-br {{ $reason['color'] }} opacity-0 group-hover:opacity-5 transition-opacity duration-300">
                            </div>

                            <div class="relative z-10">
                                <div
                                    class="w-16 h-16 mx-auto mb-6 bg-gradient-to-br {{ $reason['color'] }} rounded-2xl flex items-center justify-center text-2xl shadow-lg transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                                    {{ $reason['icon'] }}
                                </div>
                                <h3
                                    class="text-2xl font-bold mb-4 text-gray-900 group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:{{ $reason['color'] }} group-hover:bg-clip-text transition-all duration-300">
                                    {{ $reason['title'] }}
                                </h3>
                                <p class="text-gray-600 leading-relaxed">{{ $reason['desc'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Call to Action --}}
    <section
        class="py-32 bg-gradient-to-r from-blue-600 via-purple-600 to-blue-800 text-white text-center relative overflow-hidden">
        {{-- Animated Background --}}
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60"
            xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff"
            fill-opacity="0.05"%3E%3Cpath
            d="m36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"
            /%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>

        {{-- Floating Elements --}}
        <div class="absolute top-10 left-10 w-20 h-20 bg-white/10 rounded-full blur-xl animate-float"></div>
        <div class="absolute bottom-10 right-10 w-32 h-32 bg-cyan-400/20 rounded-full blur-2xl animate-float delay-1000">
        </div>
        <div class="absolute top-1/2 left-1/4 w-16 h-16 bg-purple-400/20 rounded-full blur-lg animate-pulse"></div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="animate-fade-in-up">
                <h2
                    class="text-4xl md:text-6xl font-black mb-6 bg-gradient-to-r from-white to-cyan-200 bg-clip-text text-transparent">
                    Need funds? Apply in <span class="text-yellow-300">minutes.</span>
                </h2>
                <p class="text-xl md:text-2xl mb-12 text-gray-200 max-w-2xl mx-auto leading-relaxed">
                    Our process is fast, safe and tailored for Nigerians. Join thousands who trust us with their financial
                    needs.
                </p>

                <div class="flex flex-col sm:flex-row justify-center items-center gap-6 mb-12">
                    <a href="{{ route('customer.loans.create') }}"
                        class="group relative bg-white text-blue-700 hover:bg-gray-100 px-10 py-4 rounded-xl font-bold text-lg transition-all duration-300 transform hover:scale-105 hover:shadow-2xl min-w-48">
                        <span class="relative z-10">Start Now</span>
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-cyan-500/20 to-blue-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-xl">
                        </div>
                    </a>

                    <div class="flex items-center gap-4 text-cyan-200">
                        <div class="flex -space-x-2">
                            <div
                                class="w-8 h-8 bg-gradient-to-r from-green-400 to-cyan-400 rounded-full border-2 border-white">
                            </div>
                            <div
                                class="w-8 h-8 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full border-2 border-white">
                            </div>
                            <div
                                class="w-8 h-8 bg-gradient-to-r from-yellow-400 to-orange-400 rounded-full border-2 border-white">
                            </div>
                        </div>
                        <span class="text-sm">Trusted by 10,000+ Nigerians</span>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 max-w-4xl mx-auto">
                    @php
                        $stats = [
                            ['value' => '₦2B+', 'label' => 'Loans Disbursed'],
                            ['value' => '10K+', 'label' => 'Happy Customers'],
                            ['value' => '5 Min', 'label' => 'Average Approval'],
                            ['value' => '99.9%', 'label' => 'Uptime'],
                        ];
                    @endphp

                    @foreach ($stats as $stat)
                        <div class="animate-fade-in-up delay-{{ $loop->index * 200 }}">
                            <div class="text-3xl md:text-4xl font-black text-white mb-2">{{ $stat['value'] }}</div>
                            <div class="text-cyan-200 text-sm">{{ $stat['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .animate-fade-in-up {
            animation: fade-in-up 0.8s ease-out forwards;
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .delay-200 {
            animation-delay: 0.2s;
        }

        .delay-300 {
            animation-delay: 0.3s;
        }

        .delay-500 {
            animation-delay: 0.5s;
        }

        .delay-700 {
            animation-delay: 0.7s;
        }

        .delay-1000 {
            animation-delay: 1s;
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Custom gradient text support */
        .bg-clip-text {
            -webkit-background-clip: text;
            background-clip: text;
        }
    </style>
@endpush
