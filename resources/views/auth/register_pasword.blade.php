@extends('layouts.frontend')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
        <div class="w-full max-w-md bg-white shadow-md rounded-lg p-8">
            <div class="text-center mb-6">
                @include('components.logo')
                <h2 class="mt-6 text-2xl font-semibold text-gray-800">Create your account</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Join thousands of Nigerians accessing smart loans
                </p>
            </div>

            <form id="registrationForm" method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Full Name -->
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                        placeholder="Enter your full name">
                    @error('name')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Address -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                        placeholder="your@email.com">
                    @error('email')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input id="password" type="password" name="password" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                        placeholder="Create a strong password">
                    @error('password')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Password must be at least 8 characters</p>
                </div>

                <!-- Confirm Password -->
                <div class="mb-4">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                        Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Confirm your password">
                    <p id="passwordError" class="hidden text-sm text-red-600 mt-1">Passwords do not match</p>
                </div>

                <!-- Terms and Conditions -->
                <div class="mb-6">
                    <label class="flex items-start">
                        <input type="checkbox" name="terms" id="terms" required
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-1">
                        <span class="ml-2 text-sm text-gray-600">
                            I agree to the <a href="#" class="text-blue-600 hover:underline">Terms of Service</a>
                            and <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>
                        </span>
                    </label>
                    @error('terms')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="mb-6">
                    <button type="submit" id="submitBtn"
                        class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:bg-gray-400 disabled:cursor-not-allowed">
                        <span id="submitText">Create Account</span>
                        <span id="submitLoader" class="hidden">
                            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Creating account...
                        </span>
                    </button>
                </div>

                <!-- Login Link -->
                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Already have an account?
                        <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-medium">
                            Sign in
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    {{-- Frontend validation --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registrationForm');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('password_confirmation');
            const passwordError = document.getElementById('passwordError');
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitLoader = document.getElementById('submitLoader');
            const termsCheckbox = document.getElementById('terms');

            // Password confirmation validation
            function validatePasswords() {
                if (confirmPassword.value && password.value !== confirmPassword.value) {
                    passwordError.classList.remove('hidden');
                    confirmPassword.classList.add('border-red-500');
                    return false;
                } else {
                    passwordError.classList.add('hidden');
                    confirmPassword.classList.remove('border-red-500');
                    return true;
                }
            }

            confirmPassword.addEventListener('input', validatePasswords);
            password.addEventListener('input', validatePasswords);

            // Form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validate passwords match
                if (!validatePasswords()) {
                    confirmPassword.focus();
                    return;
                }

                // Check terms acceptance
                if (!termsCheckbox.checked) {
                    alert('Please accept the Terms of Service and Privacy Policy to continue.');
                    return;
                }

                // Show loading state
                submitBtn.disabled = true;
                submitText.classList.add('hidden');
                submitLoader.classList.remove('hidden');

                // Submit the form
                form.submit();
            });

            // Prevent multiple submissions
            form.addEventListener('submit', function() {
                setTimeout(() => {
                    submitBtn.disabled = true;
                }, 100);
            });
        });
    </script>
@endsection
