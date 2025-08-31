<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth dark">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="description"
        content="Smart loans built for Nigerians. Get fast, secure, and transparent loans without hidden fees.">
    <meta name="keywords" content="loans, nigeria, finance, quick loans, personal loans">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
      <link href="{{asset('build/assets/app.css')}}" rel="stylesheet">

    {{-- <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

    @stack('styles')
</head>

<body class="bg-white text-gray-800 antialiased font-inter">
    <div id="app" class="min-h-screen flex flex-col">
        <!-- Navigation -->
        <header class="fixed w-full top-0 z-50 transition-all duration-300" id="navbar">
            <div class="bg-white/95 backdrop-blur-md border-b border-gray-200/50 shadow-sm">
                <div class="container mx-auto px-6 py-4">
                    <div class="flex justify-between items-center">
                        <!-- Logo -->
                        <a href="{{ url('/') }}" class="group flex items-center space-x-3">
                            <div class="relative">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-blue-600 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300 transform group-hover:scale-105">
                                    <span class="text-white font-bold text-lg">₦</span>
                                </div>
                                <div
                                    class="absolute -top-1 -right-1 w-4 h-4 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full animate-pulse">
                                </div>
                            </div>
                            <span
                                class="text-2xl font-black bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                                {{ config('app.name', 'SmartLoan') }}
                            </span>
                        </a>

                        <!-- Desktop Navigation -->
                        <nav class="hidden md:flex items-center space-x-8">
                            <a href="{{ url('/') }}"
                                class="nav-link {{ request()->is('/') ? 'active' : '' }}">Home</a>
                            <a href="#how-it-works" class="nav-link">How it Works</a>
                            <a href="#" class="nav-link">About</a>
                            <a href="#" class="nav-link">Contact</a>

                            @guest
                                @if (Route::has('login'))
                                    <a href="{{ route('login') }}" class="nav-link">Login</a>
                                @endif

                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn-secondary">Get Started</a>
                                @endif
                            @else
                                <!-- User Dropdown -->
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open"
                                        class="group flex items-center space-x-3 px-3 py-2 rounded-xl hover:bg-gray-50 transition-all duration-300 transform hover:scale-105">
                                        <div class="relative">
                                            <div
                                                class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300">
                                                <span
                                                    class="text-white text-sm font-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                            </div>
                                            <div
                                                class="absolute -top-1 -right-1 w-4 h-4 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full border-2 border-white animate-pulse">
                                            </div>
                                        </div>
                                        <div class="hidden md:block text-left">
                                            <p
                                                class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                                                {{ Auth::user()->name }}</p>
                                            <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                                        </div>
                                        <svg class="w-4 h-4 text-gray-400 transition-all duration-300 group-hover:text-blue-600"
                                            :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div x-show="open" @click.away="open = false"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 scale-95 transform translate-y-2"
                                        x-transition:enter-end="opacity-100 scale-100 transform translate-y-0"
                                        x-transition:leave="transition ease-in duration-150"
                                        x-transition:leave-start="opacity-100 scale-100 transform translate-y-0"
                                        x-transition:leave-end="opacity-0 scale-95 transform translate-y-2"
                                        class="absolute right-0 mt-3 w-72 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-gray-200/50 py- z-50">

                                        <!-- User Info Header -->
                                        <div class="px-6 py-4 border-b border-gray-100/80">
                                            <div class="flex items-center space-x-4">
                                                <div class="relative">
                                                    <div
                                                        class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                                                        <span
                                                            class="text-white font-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                                    </div>
                                                    <div
                                                        class="absolute -bottom-1 -right-1 w-4 h-4 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full border-2 border-white">
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-bold text-gray-900 truncate">
                                                        {{ Auth::user()->name }}</p>
                                                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                                    <div class="flex items-center mt-1">
                                                        <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                                                        <span class="text-xs text-green-600 ml-2 font-medium">Active</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Quick Stats -->
                                        <div
                                            class="px-6 py-3 bg-gradient-to-r from-blue-50 to-purple-50 border-b border-gray-100/80">
                                            <div class="grid grid-cols-2 gap-4 text-center">
                                                <div>
                                                    <p class="text-lg font-bold text-blue-600">₦0</p>
                                                    <p class="text-xs text-gray-600">Active Loans</p>
                                                </div>
                                                <div>
                                                    <p class="text-lg font-bold text-green-600">100%</p>
                                                    <p class="text-xs text-gray-600">Credit Score</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="px-6 py-4 bg-gradient-to-r from-blue-50 to-purple-50 border-b border-gray-100/80">
                                            <div class="grid grid-cols-2 gap-4 text-center">
                                                <div>
                                                    <ul class=" font-medium">
                                                        <li>
                                                            <a href="{{ route('dashboard') }}"
                                                                class="flex flex-col items-center justify-center gap-1 p-4 text-center rounded-lg hover:bg-blue-100 dark:hover:bg-blue-600">
                                                                <svg class="w-6 h-6 text-blue-600 dark:text-white"
                                                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                                    width="24" height="24" fill="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path fill-rule="evenodd"
                                                                        d="M4.857 3A1.857 1.857 0 0 0 3 4.857v4.286C3 10.169 3.831 11 4.857 11h4.286A1.857 1.857 0 0 0 11 9.143V4.857A1.857 1.857 0 0 0 9.143 3H4.857Zm10 0A1.857 1.857 0 0 0 13 4.857v4.286c0 1.026.831 1.857 1.857 1.857h4.286A1.857 1.857 0 0 0 21 9.143V4.857A1.857 1.857 0 0 0 19.143 3h-4.286Zm-10 10A1.857 1.857 0 0 0 3 14.857v4.286C3 20.169 3.831 21 4.857 21h4.286A1.857 1.857 0 0 0 11 19.143v-4.286A1.857 1.857 0 0 0 9.143 13H4.857Zm10 0A1.857 1.857 0 0 0 13 14.857v4.286c0 1.026.831 1.857 1.857 1.857h4.286A1.857 1.857 0 0 0 21 19.143v-4.286A1.857 1.857 0 0 0 19.143 13h-4.286Z"
                                                                        clip-rule="evenodd" />
                                                                </svg>

                                                                <div
                                                                    class="text-sm font-medium text-gray-900 dark:text-white">
                                                                    Dashboard</div>
                                                            </a>
                                                        </li>


                                                    </ul>
                                                </div>
                                                <div>
                                                    <ul class=" font-medium">
                                                        <li>
                                                            <a href="{{ route('logout') }}"
                                                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                                                class="flex flex-col items-center justify-center gap-1 p-4 text-center rounded-lg hover:bg-blue-100 dark:hover:bg-blue-600">
                                                                <svg class="w-6 h-6 text-green-600 dark:text-white"
                                                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                                    width="24" height="24" fill="none"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke="currentColor" stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M20 12H8m12 0-4 4m4-4-4-4M9 4H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h2" />
                                                                </svg>

                                                                <div
                                                                    class="text-sm font-medium text-gray-900 dark:text-white">
                                                                    Logout</div>
                                                            </a>
                                                        </li>
                                                        <form id="logout-form" action="{{ route('logout') }}"
                                                            method="POST" class="hidden">
                                                            @csrf
                                                        </form>
                                                    </ul>
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endguest
                        </nav>

                        <!-- Mobile Menu Button -->
                        <button class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors"
                            id="mobile-menu-btn">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Mobile Navigation -->
                    <div class="md:hidden mt-4 pb-4 border-t border-gray-200 hidden" style="width: 50%" id="mobile-menu">
                        <ul
                            class="font-medium flex flex-col p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
                            <li>
                                <a href="#"
                                    class="block py-2 px-3 text-white bg-blue-700 rounded-sm md:bg-transparent md:text-blue-700 md:p-0 dark:text-white md:dark:text-blue-500"
                                    aria-current="page">Home</a>
                            </li>
                            <li>
                                <a href="#"
                                    class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">About</a>
                            </li>
                            <li>
                                <a href="#"
                                    class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Services</a>
                            </li>
                            <li>
                                <a href="#"
                                    class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Pricing</a>
                            </li>
                            <li>
                                <a href="#"
                                    class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Contact</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow pt-20">
            @if (session('success'))
                <div
                    class="fixed top-24 right-6 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg animate-slide-in-right">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div
                    class="fixed top-24 right-6 z-50 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg animate-slide-in-right">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 text-white relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60"
                    viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none"
                    fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.1"%3E%3Cpath
                    d="m36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"
                    /%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
            </div>

            <div class="container mx-auto px-6 py-16 relative z-10">
                <div class="grid md:grid-cols-4 gap-8 mb-8">
                    <!-- Company Info -->
                    <div class="md:col-span-2">
                        <div class="flex items-center space-x-3 mb-4">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center">
                                <span class="text-white font-bold text-lg">₦</span>
                            </div>
                            <span class="text-2xl font-black">{{ config('app.name', 'SmartLoan') }}</span>
                        </div>
                        <p class="text-gray-300 mb-6 max-w-md leading-relaxed">
                            Empowering Nigerians with smart, fast, and transparent loans. Your financial growth is our
                            mission.
                        </p>
                        <div class="flex space-x-4">
                            <a href="#" class="social-link">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                                </svg>
                            </a>
                            <a href="#" class="social-link">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z" />
                                </svg>
                            </a>
                            <a href="#" class="social-link">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.297 1.199-.335 1.363-.053.225-.172.271-.402.162-1.499-.699-2.436-2.888-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.357-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001.012.001z" />
                                </svg>
                            </a>
                            <a href="#" class="social-link">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h4 class="text-lg font-semibold mb-4 text-white">Quick Links</h4>
                        <ul class="space-y-2">
                            <li><a href="#" class="footer-link">About Us</a></li>
                            <li><a href="#" class="footer-link">How it Works</a></li>
                            <li><a href="#" class="footer-link">Loan Calculator</a></li>
                            <li><a href="#" class="footer-link">FAQs</a></li>
                            <li><a href="#" class="footer-link">Contact</a></li>
                        </ul>
                    </div>

                    <!-- Legal -->
                    <div>
                        <h4 class="text-lg font-semibold mb-4 text-white">Legal</h4>
                        <ul class="space-y-2">
                            <li><a href="#" class="footer-link">Privacy Policy</a></li>
                            <li><a href="#" class="footer-link">Terms of Service</a></li>
                            <li><a href="#" class="footer-link">Cookie Policy</a></li>
                            <li><a href="#" class="footer-link">Compliance</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Bottom Bar -->
                <div class="border-t border-gray-700 pt-8 flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 text-sm mb-4 md:mb-0">
                        &copy; {{ now()->year }} {{ config('app.name') }}. All rights reserved.
                    </p>
                    <div class="flex items-center space-x-4 text-sm text-gray-400">
                        <span>🇳🇬 Made in Nigeria</span>
                        <span>•</span>
                        <span>Licensed & Regulated</span>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });

        // Auto-hide flash messages
        setTimeout(function() {
            const flashMessages = document.querySelectorAll('.animate-slide-in-right');
            flashMessages.forEach(function(message) {
                message.style.transform = 'translateX(100%)';
                message.style.opacity = '0';
                setTimeout(function() {
                    message.remove();
                }, 300);
            });
        }, 5000);
    </script>

    @stack('scripts')
</body>

</html>

<style>
    .font-inter {
        font-family: 'Inter', sans-serif;
    }

    .nav-link {
        @apply text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200 relative;
    }

    .nav-link.active {
        @apply text-blue-600;
    }

    .nav-link.active::after {
        content: '';
        @apply absolute -bottom-1 left-0 w-full h-0.5 bg-blue-600;
    }

    .btn-secondary {
        @apply bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white px-6 py-2 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg;
    }

    .dropdown-item {
        @apply flex items-center space-x-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg mx-2 transition-all duration-200 transform hover:scale-[1.02];
    }

    .mobile-nav-link {
        @apply block px-3 py-2 text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-lg transition-colors duration-200;
    }

    .social-link {
        @apply w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center text-gray-300 hover:text-white hover:bg-white/20 transition-all duration-300 transform hover:scale-110;
    }

    .footer-link {
        @apply text-gray-400 hover:text-white transition-colors duration-200;
    }

    .navbar-scrolled {
        @apply shadow-lg;
    }

    .navbar-scrolled .bg-white\/95 {
        @apply bg-white/98;
    }

    @keyframes slide-in-right {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .animate-slide-in-right {
        animation: slide-in-right 0.3s ease-out;
    }

    /* Smooth scroll offset for fixed header */
    html {
        scroll-padding-top: 5rem;
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    ::-webkit-scrollbar-thumb {
        background: linear-gradient(to bottom, #3b82f6, #06b6d4);
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(to bottom, #2563eb, #0891b2);
    }
</style>
