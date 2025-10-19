@php
    $bankList = getbankList();
@endphp
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
/* Select2 Dark Mode Compatible Styles */

/* Container */
.select2-container--default .select2-selection--single {
  background-color: rgb(249 250 251); /* Light mode bg-gray-50 */
  border: 1px solid rgb(209 213 219); /* Light mode border-gray-300 */
  border-radius: 0.5rem;
  height: 42px;
  display: flex;
  align-items: center;
  transition: all 0.15s ease-in-out;
}

/* Dark mode container */
.dark .select2-container--default .select2-selection--single {
  background-color: rgb(75 85 99); /* Dark mode bg-gray-600 */
  border-color: rgb(107 114 128); /* Dark mode border-gray-500 */
}

/* Selected text */
.select2-container--default .select2-selection__rendered {
  color: rgb(17 24 39); /* Light mode text-gray-900 */
  font-size: 0.875rem;
  line-height: 42px;
  padding-left: 0.625rem;
  padding-right: 0.625rem;
}

/* Dark mode selected text */
.dark .select2-container--default .select2-selection__rendered {
  color: rgb(255 255 255); /* Dark mode text-white */
}

/* Placeholder */
.select2-container--default .select2-selection__placeholder {
  color: rgb(156 163 175); /* Light mode text-gray-400 */
}

/* Dark mode placeholder */
.dark .select2-container--default .select2-selection__placeholder {
  color: rgb(156 163 175); /* Dark mode placeholder-gray-400 */
}

/* Arrow */
.select2-container--default .select2-selection__arrow {
  height: 100%;
  right: 0.75rem;
}

/* Dropdown */
.select2-container--default .select2-dropdown {
  background-color: rgb(255 255 255); /* Light mode bg-white */
  border: 1px solid rgb(209 213 219); /* Light mode border-gray-300 */
  border-radius: 0.5rem;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

/* Dark mode dropdown */
.dark .select2-container--default .select2-dropdown {
  background-color: rgb(55 65 81); /* Dark mode bg-gray-700 */
  border-color: rgb(75 85 99); /* Dark mode border-gray-600 */
}

/* Search field */
.select2-container--default .select2-search--dropdown .select2-search__field {
  background-color: rgb(249 250 251); /* Light mode bg-gray-50 */
  border: 1px solid rgb(209 213 219); /* Light mode border-gray-300 */
  color: rgb(17 24 39); /* Light mode text-gray-900 */
  border-radius: 0.375rem;
  padding: 0.5rem;
  font-size: 0.875rem;
}

/* Dark mode search field */
.dark .select2-container--default .select2-search--dropdown .select2-search__field {
  background-color: rgb(75 85 99); /* Dark mode bg-gray-600 */
  border-color: rgb(107 114 128); /* Dark mode border-gray-500 */
  color: rgb(255 255 255); /* Dark mode text-white */
}

/* Search field placeholder */
.select2-container--default .select2-search--dropdown .select2-search__field::placeholder {
  color: rgb(156 163 175); /* Light mode placeholder-gray-400 */
}

/* Options */
.select2-container--default .select2-results__option {
  color: rgb(17 24 39); /* Light mode text-gray-900 */
  padding: 0.5rem 0.75rem;
  font-size: 0.875rem;
}

/* Dark mode options */
.dark .select2-container--default .select2-results__option {
  color: rgb(255 255 255); /* Dark mode text-white */
}

/* Highlighted option */
.select2-container--default .select2-results__option--highlighted {
  background-color: rgb(59 130 246); /* Light mode bg-blue-500 */
  color: rgb(255 255 255); /* text-white */
}

/* Dark mode highlighted option */
.dark .select2-container--default .select2-results__option--highlighted {
  background-color: rgb(37 99 235); /* Dark mode bg-blue-600 */
  color: rgb(255 255 255); /* text-white */
}

/* Selected option */
.select2-container--default .select2-results__option[aria-selected="true"] {
  background-color: rgb(239 246 255); /* Light mode bg-blue-50 */
  color: rgb(30 64 175); /* Light mode text-blue-900 */
}

/* Dark mode selected option */
.dark .select2-container--default .select2-results__option[aria-selected="true"] {
  background-color: rgba(59, 130, 246, 0.2); /* Dark mode bg-blue-900/20 */
  color: rgb(147 197 253); /* Dark mode text-blue-300 */
}

/* No results message */
.select2-container--default .select2-results__option--disabled {
  color: rgb(156 163 175); /* Light mode text-gray-400 */
}

/* Dark mode no results message */
.dark .select2-container--default .select2-results__option--disabled {
  color: rgb(156 163 175); /* Dark mode text-gray-400 */
}

/* Focus state */
.select2-container--default.select2-container--focus .select2-selection--single,
.select2-container--default.select2-container--open .select2-selection--single {
  border-color: rgb(59 130 246); /* Light mode border-blue-500 */
  outline: none;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Dark mode focus state */
.dark .select2-container--default.select2-container--focus .select2-selection--single,
.dark .select2-container--default.select2-container--open .select2-selection--single {
  border-color: rgb(96 165 250); /* Dark mode border-blue-400 */
  box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.1);
}

/* Hover effects */
.select2-container--default .select2-selection--single:hover {
  border-color: rgb(156 163 175); /* Light mode hover border-gray-400 */
}

/* Dark mode hover effects */
.dark .select2-container--default .select2-selection--single:hover {
  border-color: rgb(156 163 175); /* Dark mode hover border-gray-400 */
}
</style>
@endpush
{{-- Loan Application Modal --}}
<div id="apply-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-4xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    @if(!auth()->user()->customer)
                        Complete Your Profile & Apply for Loan
                    @else
                        Apply for Loan
                    @endif
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="apply-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-4 md:p-5 space-y-4">
                <form id="loanApplicationForm" action="{{ route('customer.loans.store') }}" method="POST">
                    @csrf

                    @if(!auth()->user()->customer)
                        {{-- Profile Information Section --}}
                        <div id="profileSection">
                            <h4 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Personal Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label for="address" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Home Address <span class="text-red-500">*</span></label>
                                    <input type="text" id="address" name="address" required
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                        placeholder="Enter your complete home address">
                                </div>
                                <div>
                                    <label for="phone" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Phone Number <span class="text-red-500">*</span></label>
                                    <input type="tel" id="phone" name="phone" required
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                        placeholder="+234800000000">
                                </div>
                            </div>

                            <h4 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Employment Details</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label for="employer" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Employer <span class="text-red-500">*</span></label>
                                    <input type="text" id="employer" name="employer" required
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                        placeholder="Company name">
                                </div>
                                <div>
                                    <label for="employer_address" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Employer Address <span class="text-red-500">*</span></label>
                                    <input type="text" id="employer_address" name="employer_address" required
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                        placeholder="Company address">
                                </div>
                                <div class="md:col-span-2">
                                    <label for="employment_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Employee ID <span class="text-red-500">*</span></label>
                                    <input type="text" id="employment_id" name="employment_id" required
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                        placeholder="Your employee ID">
                                </div>
                            </div>

                            <h4 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Bank Details</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label for="bank_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Bank <span class="text-red-500">*</span></label>
                                    <select id="bank_name" name="bank_name" required
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white select2">
                                        <option value="">Select your bank</option>
                                        @forelse ($bankList as $bank)
                                            <option value="{{ $bank['bank_name'] }}">{{ $bank['bank_name'] }}</option>

                                        @empty
                                            <option value="">No banks available</option>
                                        @endforelse
                                    </select>
                                </div>
                                <div>
                                    <label for="account_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Account Name <span class="text-red-500">*</span></label>
                                    <input type="text" id="account_name" name="account_name" required
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                        placeholder="Account holder name">
                                </div>
                                <div>
                                    <label for="account_number" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Account Number <span class="text-red-500">*</span></label>
                                    <input type="text" id="account_number" name="account_number" required maxlength="10"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                        placeholder="10-digit account number">
                                </div>
                                <div>
                                    <label for="bvn" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">BVN (Optional)</label>
                                    <input type="text" id="bvn" name="bvn" maxlength="11"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                        placeholder="11-digit BVN">
                                </div>
                            </div>

                            <div class="border-t pt-6 mb-6"></div>
                        </div>
                    @endif

                    {{-- Loan Details Section --}}
                    <div id="loanSection">
                        <h4 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Loan Details</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="loan_amount" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Loan Amount <span class="text-red-500">*</span></label>
                                <input type="number" id="loan_amount" name="principal" required min="50000" max="5000000" step="1000"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                    placeholder="₦50,000 - ₦5,000,000">
                                <p class="text-xs text-gray-500 mt-1">Minimum: ₦50,000 | Maximum: ₦5,000,000</p>
                            </div>
                            <div>
                                <label for="tenure" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Loan Tenure <span class="text-red-500">*</span></label>
                                <select id="tenure" name="tenure_months" required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                                    <option value="">Select tenure</option>
                                    <option value="3">3 Months</option>
                                    <option value="6">6 Months</option>
                                    <option value="9">9 Months</option>
                                    <option value="12">12 Months</option>
                                    <option value="18">18 Months</option>
                                    <option value="24">24 Months</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="purpose" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Purpose of Loan <span class="text-red-500">*</span></label>
                            <select id="purpose" name="purpose" required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                                <option value="">Select purpose</option>
                                <option value="business">Business Expansion</option>
                                <option value="education">Education</option>
                                <option value="medical">Medical Emergency</option>
                                <option value="home_improvement">Home Improvement</option>
                                <option value="debt_consolidation">Debt Consolidation</option>
                                <option value="investment">Investment</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="mb-6">
                            <label for="monthly_income" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Monthly Income <span class="text-red-500">*</span></label>
                            <input type="number" id="monthly_income" name="monthly_income" required min="50000"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                placeholder="Your monthly salary">
                        </div>

                        {{-- Loan Calculator Preview
                        <div id="loanPreview" class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mb-6 hidden">
                            <h5 class="text-md font-semibold text-blue-900 dark:text-blue-100 mb-3">Loan Preview</h5>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600 dark:text-gray-300">Principal:</span>
                                    <span id="previewPrincipal" class="font-semibold text-blue-900 dark:text-blue-100 float-right">₦0</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-300">Interest Rate:</span>
                                    <span id="previewRate" class="font-semibold text-blue-900 dark:text-blue-100 float-right">0%</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-300">Monthly Payment:</span>
                                    <span id="previewMonthly" class="font-semibold text-blue-900 dark:text-blue-100 float-right">₦0</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-300">Total Repayment:</span>
                                    <span id="previewTotal" class="font-semibold text-blue-900 dark:text-blue-100 float-right">₦0</span>
                                </div>
                            </div>
                        </div> --}}

                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="agree_terms" id="agree_terms" required
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <span class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                    I agree to the <a href="#" class="text-blue-600 hover:underline dark:text-blue-500">loan terms and conditions</a>
                                </span>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <!-- Modal footer -->
            <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button type="submit" form="loanApplicationForm" id="submitLoanBtn"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 disabled:bg-gray-400 disabled:cursor-not-allowed">
                    <span id="submitLoanText">Submit Application</span>
                    <span id="submitLoanLoader" class="hidden">
                        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    </span>
                </button>
                <button data-modal-hide="apply-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loanForm = document.getElementById('loanApplicationForm');
    const loanAmountInput = document.getElementById('loan_amount');
    const tenureSelect = document.getElementById('tenure');
    const loanPreview = document.getElementById('loanPreview');
    const submitBtn = document.getElementById('submitLoanBtn');
    const submitText = document.getElementById('submitLoanText');
    const submitLoader = document.getElementById('submitLoanLoader');
    const accountNumberInput = document.getElementById('account_number');
    const bvnInput = document.getElementById('bvn');
    const phoneInput = document.getElementById('phone');

    // Interest rates based on tenure (annual rates)
    const interestRates = {
        3: 24,
        6: 22,
        9: 20,
        12: 18,
        18: 16,
        24: 15
    };

    // Format number with commas
    function formatNumber(num) {
        return num.toLocaleString();
    }

    // Calculate loan details
    function calculateLoan() {
        const principal = parseFloat(loanAmountInput.value) || 0;
        const months = parseInt(tenureSelect.value) || 0;

        if (principal > 0 && months > 0) {
            const annualRate = interestRates[months] || 20;
            const monthlyRate = annualRate / 100 / 12;
            const totalInterest = (principal * annualRate / 100) * (months / 12);
            const totalAmount = principal + totalInterest;
            const monthlyPayment = totalAmount / months;

            document.getElementById('previewPrincipal').textContent = '₦' + formatNumber(principal);
            document.getElementById('previewRate').textContent = annualRate + '% p.a.';
            document.getElementById('previewMonthly').textContent = '₦' + formatNumber(Math.round(monthlyPayment));
            document.getElementById('previewTotal').textContent = '₦' + formatNumber(Math.round(totalAmount));

            loanPreview.classList.remove('hidden');
        } else {
            loanPreview.classList.add('hidden');
        }
    }

    // Numeric input validation
    if (accountNumberInput) {
        accountNumberInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });
    }

    if (bvnInput) {
        bvnInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });
    }

    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');

            e.target.value = value;
        });
    }

    // Loan calculation listeners
    loanAmountInput.addEventListener('input', calculateLoan);
    tenureSelect.addEventListener('change', calculateLoan);

    // Format loan amount input
    loanAmountInput.addEventListener('blur', function() {
        if (this.value) {
            const value = parseInt(this.value.replace(/,/g, ''));
            if (!isNaN(value)) {
                this.value = value;
            }
        }
    });

    // Form submission
    loanForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Show loading state
        submitBtn.disabled = true;
        submitText.classList.add('hidden');
        submitLoader.classList.remove('hidden');

        // Submit form
        setTimeout(() => {
            this.submit();
        }, 500);
    });

    // Prevent multiple submissions
    loanForm.addEventListener('submit', function() {
        setTimeout(() => {
            submitBtn.disabled = true;
        }, 100);
    });
});
</script>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
    $('.select2').select2(
        {
            width: '100%'
        }
    );
});
</script>
@endpush
