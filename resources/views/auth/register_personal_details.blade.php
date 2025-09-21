@extends('layouts.frontend')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-5xl bg-white p-8 rounded-lg shadow">
            <form action="{{ route('register') }}" method="POST" id="mainForm" novalidate>
                @csrf

                {{-- Basic --}}
                <h2 class="text-lg font-semibold mb-4">Basic</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900">First Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            class="bg-gray-50 border @error('name') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-red-600 hidden validation-error" id="first_name_error"></p>
                    </div>
                    <div>
                        <label for="address" class="block mb-2 text-sm font-medium text-gray-900">Home Address <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="address" name="address" value="{{ old('address') }}" required
                            class="bg-gray-50 border @error('address') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-red-600 hidden validation-error" id="address_error"></p>
                    </div>

                    <div>
                        <label for="phone" class="block mb-2 text-sm font-medium text-gray-900">Phone Number <span
                                class="text-red-500">*</span></label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                            class="bg-gray-50 border @error('phone') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-red-600 hidden validation-error" id="phone_error"></p>
                    </div>

                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email <span
                                class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            class="bg-gray-50 border @error('email') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-red-600 hidden validation-error" id="email_error"></p>
                    </div>
                </div>

                {{-- Employment Details --}}
                <h2 class="text-lg font-semibold mb-4">Employment Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label for="employer" class="block mb-2 text-sm font-medium text-gray-900">Employer <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="employer" name="employer" value="{{ old('employer') }}" required
                            class="bg-gray-50 border @error('employer') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                        @error('employer')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-red-600 hidden validation-error" id="employer_error"></p>
                    </div>

                    <div>
                        <label for="employer_address" class="block mb-2 text-sm font-medium text-gray-900">Employer Address
                            <span class="text-red-500">*</span></label>
                        <input type="text" id="employer_address" name="employer_address"
                            value="{{ old('employer_address') }}" required
                            class="bg-gray-50 border @error('employer_address') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                        @error('employer_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-red-600 hidden validation-error" id="employer_address_error"></p>
                    </div>

                    <div>
                        <label for="employment_id" class="block mb-2 text-sm font-medium text-gray-900">Employment ID No
                            <span class="text-red-500">*</span></label>
                        <input type="text" id="employment_id" name="employment_id" value="{{ old('employment_id') }}"
                            required
                            class="bg-gray-50 border @error('employment_id') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                        @error('employment_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-red-600 hidden validation-error" id="employment_id_error"></p>
                    </div>
                </div>

                {{-- Bank Details --}}
                <h2 class="text-lg font-semibold mb-4">Bank Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label for="bank" class="block mb-2 text-sm font-medium text-gray-900">Select Bank <span
                                class="text-red-500">*</span></label>
                        <select id="bank" name="bank" required
                            class="bg-gray-50 border @error('bank') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                            <option value="">Select Bank</option>
                            @foreach ($banks as $bankName => $bankCode)
                                <option value="{{ $bankName }}" {{ old('bank') == $bankName ? 'selected' : '' }}>
                                    {{ $bankName }}
                                </option>
                            @endforeach
                        </select>
                        @error('bank')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-red-600 hidden validation-error" id="bank_error"></p>
                    </div>

                    <div>
                        <label for="account_name" class="block mb-2 text-sm font-medium text-gray-900">Account Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="account_name" name="account_name" value="{{ old('account_name') }}"
                            required
                            class="bg-gray-50 border @error('account_name') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                        @error('account_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-red-600 hidden validation-error" id="account_name_error"></p>
                    </div>

                    <div>
                        <label for="account_no" class="block mb-2 text-sm font-medium text-gray-900">Account No. <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="account_no" name="account_no" value="{{ old('account_no') }}"
                            required
                            class="bg-gray-50 border @error('account_no') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                        @error('account_no')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-red-600 hidden validation-error" id="account_no_error"></p>
                    </div>

                    <div>
                        <label for="bvn" class="block mb-2 text-sm font-medium text-gray-900">BVN </label>
                        <input type="text" id="bvn" name="bvn" value="{{ old('bvn') }}" maxlength="11"
                            class="bg-gray-50 border @error('bvn') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                        @error('bvn')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-red-600 hidden validation-error" id="bvn_error"></p>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex justify-end">
                    <button type="submit" id="submitBtn"
                        class="px-5 py-2.5 text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm disabled:bg-gray-400 disabled:cursor-not-allowed">
                        <span id="submitText">Submit</span>
                        <span id="submitLoader" class="hidden">
                            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('mainForm');
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitLoader = document.getElementById('submitLoader');

            // Validation rules
            const validationRules = {
                first_name: {
                    required: true,
                    minLength: 2,
                    pattern: /^[a-zA-Z\s]+$/,
                    message: 'First name is required and must contain only letters'
                },
                last_name: {
                    required: true,
                    minLength: 2,
                    pattern: /^[a-zA-Z\s]+$/,
                    message: 'Last name is required and must contain only letters'
                },
                address: {
                    required: true,
                    minLength: 10,
                    message: 'Address is required and must be at least 10 characters'
                },
                phone: {
                    required: true,
                    pattern: /^(\+234|0)[789][01]\d{8}$/,
                    message: 'Please enter a valid Nigerian phone number (e.g., +2348012345678 or 08012345678)'
                },
                email: {
                    required: true,
                    pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                    message: 'Please enter a valid email address'
                },
                employer: {
                    required: true,
                    minLength: 2,
                    message: 'Employer name is required'
                },
                employer_address: {
                    required: true,
                    minLength: 5,
                    message: 'Employer address is required'
                },
                employment_id: {
                    required: true,
                    minLength: 3,
                    message: 'Employment ID is required'
                },
                bank: {
                    required: true,
                    message: 'Please select a bank'
                },
                account_name: {
                    required: true,
                    pattern: /^[a-zA-Z\s]+$/,
                    message: 'Account name is required and must contain only letters'
                },
                account_no: {
                    required: true,
                    pattern: /^\d{10}$/,
                    message: 'Account number must be exactly 10 digits'
                },
                bvn: {
                    required: false,
                    pattern: /^\d{11}$/,
                    message: 'BVN must be exactly 11 digits'
                }
            };

            // Clear validation error
            function clearValidationError(fieldName) {
                const field = document.getElementById(fieldName);
                const errorElement = document.getElementById(fieldName + '_error');

                field.classList.remove('border-red-500');
                field.classList.add('border-gray-300');
                errorElement.textContent = '';
                errorElement.classList.add('hidden');
            }

            // Show validation error
            function showValidationError(fieldName, message) {
                const field = document.getElementById(fieldName);
                const errorElement = document.getElementById(fieldName + '_error');

                field.classList.remove('border-gray-300');
                field.classList.add('border-red-500');
                errorElement.textContent = message;
                errorElement.classList.remove('hidden');
            }

            // Validate single field
            function validateField(fieldName, value) {
                const rule = validationRules[fieldName];
                if (!rule) return true;

                // Check if required
                if (rule.required && (!value || value.trim() === '')) {
                    showValidationError(fieldName, rule.message);
                    return false;
                }

                // Skip other validations if field is empty and not required
                if (!value || value.trim() === '') {
                    clearValidationError(fieldName);
                    return true;
                }

                // Check minimum length
                if (rule.minLength && value.length < rule.minLength) {
                    showValidationError(fieldName, rule.message);
                    return false;
                }

                // Check pattern
                if (rule.pattern && !rule.pattern.test(value)) {
                    showValidationError(fieldName, rule.message);
                    return false;
                }

                clearValidationError(fieldName);
                return true;
            }

            // Add real-time validation
            Object.keys(validationRules).forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (field) {
                    field.addEventListener('blur', function() {
                        validateField(fieldName, this.value);
                    });

                    field.addEventListener('input', function() {
                        // Clear error on input for better UX
                        clearValidationError(fieldName);
                    });
                }
            });

            // Phone number formatting
            const phoneField = document.getElementById('phone');
            phoneField.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, ''); // Remove non-digits

                // Auto-format Nigerian numbers
                if (value.startsWith('234') && value.length > 3) {
                    value = '+' + value;
                } else if (value.startsWith('0') && value.length > 1) {
                    // Keep as is for local format
                }

                e.target.value = value;
            });

            // BVN and Account number - numbers only
            ['bvn', 'account_no'].forEach(fieldName => {
                const field = document.getElementById(fieldName);
                field.addEventListener('input', function(e) {
                    e.target.value = e.target.value.replace(/\D/g, '');
                });
            });

            // Form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                let isValid = true;
                const formData = new FormData(form);

                // Validate all fields
                Object.keys(validationRules).forEach(fieldName => {
                    const value = formData.get(fieldName) || '';
                    if (!validateField(fieldName, value)) {
                        isValid = false;
                    }
                });

                if (isValid) {
                    // Show loading state
                    submitBtn.disabled = true;
                    submitText.classList.add('hidden');
                    submitLoader.classList.remove('hidden');

                    // Submit the form
                    form.submit();
                } else {
                    // Scroll to first error
                    const firstError = document.querySelector('.border-red-500');
                    if (firstError) {
                        firstError.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        firstError.focus();
                    }
                }
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
