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
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                                        <option value="">Select your bank</option>
                                        <option value="Access Bank">Access Bank</option>
                                        <option value="First Bank of Nigeria">First Bank of Nigeria</option>
                                        <option value="Zenith Bank">Zenith Bank</option>
                                        <option value="UBA">United Bank for Africa</option>
                                        <option value="GTBank">Guaranty Trust Bank</option>
                                        <option value="Fidelity Bank">Fidelity Bank</option>
                                        <option value="FCMB">First City Monument Bank</option>
                                        <option value="Sterling Bank">Sterling Bank</option>
                                        <option value="Union Bank">Union Bank</option>
                                        <option value="Wema Bank">Wema Bank</option>
                                        <option value="Ecobank">Ecobank</option>
                                        <option value="Stanbic IBTC">Stanbic IBTC</option>
                                        <option value="Heritage Bank">Heritage Bank</option>
                                        <option value="Keystone Bank">Keystone Bank</option>
                                        <option value="Polaris Bank">Polaris Bank</option>
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

                        {{-- Loan Calculator Preview --}}
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
                        </div>

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
            if (value.startsWith('0')) {
                value = '+234' + value.substring(1);
            } else if (!value.startsWith('+234') && value.length > 0) {
                value = '+234' + value;
            }
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
