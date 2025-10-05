<!-- Approve Loan Modal -->
<div id="approve-loan-modal-{{ $loan->id }}" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-4xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Approve Loan Application
                </h3>
                <button type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="approve-loan-modal-{{ $loan->id }}">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <!-- Modal body -->
            <form action="{{ route('admin.loans.approve', $loan) }}" method="POST" id="approve-loan-form-{{ $loan->id }}">
                @csrf
                @method('PUT')

                <div class="p-4 md:p-5 space-y-4">
                    <!-- Customer Information -->
                    <div class="mb-6">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Customer Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="customer_id_{{ $loan->id }}"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Customer <span class="text-red-500">*</span>
                                </label>
                                <select id="customer_id_{{ $loan->id }}" name="customer_id" required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select Customer</option>
                                    @foreach($customers ?? [] as $customer)
                                        <option value="{{ $customer->id }}"
                                            {{ old('customer_id', $loan->customer_id) == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->user->name }} ({{ $customer->user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Loan Details -->
                    <div class="mb-6">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Loan Details</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Principal -->
                            <div>
                                <label for="principal_{{ $loan->id }}"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Loan Amount (₦) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="principal_{{ $loan->id }}" name="principal" step="0.01" min="1000"
                                    value="{{ old('principal', $loan->principal) }}" required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('principal')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Interest Rate -->
                            <div>
                                <label for="interest_rate_{{ $loan->id }}"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Interest Rate (%) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="interest_rate_{{ $loan->id }}" name="interest_rate" step="0.1"
                                    min="0" max="100" value="{{ old('interest_rate', $loan->interest_rate) }}" required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('interest_rate')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tenure -->
                            <div>
                                <label for="tenure_months_{{ $loan->id }}"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Tenure (Months) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="tenure_months_{{ $loan->id }}" name="tenure_months" min="1"
                                    max="60" value="{{ old('tenure_months', $loan->tenure_months) }}" required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('tenure_months')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Repayment Cycle -->
                            <div>
                                <label for="repayment_cycle_{{ $loan->id }}"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Repayment Cycle <span class="text-red-500">*</span>
                                </label>
                                <select id="repayment_cycle_{{ $loan->id }}" name="repayment_cycle" required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="weekly" {{ old('repayment_cycle', $loan->repayment_cycle) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="monthly" {{ old('repayment_cycle', $loan->repayment_cycle) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="quarterly" {{ old('repayment_cycle', $loan->repayment_cycle) == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                </select>
                                @error('repayment_cycle')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Start Date -->
                            <div>
                                <label for="start_date_{{ $loan->id }}"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Start Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="start_date_{{ $loan->id }}" name="start_date"
                                    value="{{ old('start_date', $loan->start_date?->format('Y-m-d') ?? date('Y-m-d')) }}"
                                    min="{{ date('Y-m-d') }}" required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('start_date')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    End date will be calculated automatically
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Calculated Preview -->
                    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <h4 class="text-sm font-medium text-blue-800 dark:text-blue-300 mb-2">Loan Summary Preview</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Reference:</span>
                                <p class="font-medium text-gray-900 dark:text-white" id="preview-reference-{{ $loan->id }}">
                                    Will be generated
                                </p>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Total Obligation:</span>
                                <p class="font-medium text-gray-900 dark:text-white" id="preview-obligation-{{ $loan->id }}">
                                    ₦0.00
                                </p>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Interest Amount:</span>
                                <p class="font-medium text-gray-900 dark:text-white" id="preview-interest-{{ $loan->id }}">
                                    ₦0.00
                                </p>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">End Date:</span>
                                <p class="font-medium text-gray-900 dark:text-white" id="preview-end-date-{{ $loan->id }}">
                                    -
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal footer -->
                <div
                    class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600 space-x-3">
                    <button type="button"
                        class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600"
                        data-modal-hide="approve-loan-modal-{{ $loan->id }}">
                        Cancel
                    </button>
                    <button type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Approve Loan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loanId = {{ $loan->id }};
    const form = document.getElementById(`approve-loan-form-${loanId}`);

    // Get form inputs
    const principalInput = document.getElementById(`principal_${loanId}`);
    const interestRateInput = document.getElementById(`interest_rate_${loanId}`);
    const tenureInput = document.getElementById(`tenure_months_${loanId}`);
    const startDateInput = document.getElementById(`start_date_${loanId}`);
    const customerIdInput = document.getElementById(`customer_id_${loanId}`);
    const repaymentCycleInput = document.getElementById(`repayment_cycle_${loanId}`);

    // Preview elements
    const previewReference = document.getElementById(`preview-reference-${loanId}`);
    const previewObligation = document.getElementById(`preview-obligation-${loanId}`);
    const previewInterest = document.getElementById(`preview-interest-${loanId}`);
    const previewEndDate = document.getElementById(`preview-end-date-${loanId}`);

    // Update preview on input change
    function updatePreview() {
        const principal = parseFloat(principalInput.value) || 0;
        const interestRate = parseFloat(interestRateInput.value) || 0;
        const tenure = parseInt(tenureInput.value) || 0;
        const startDate = startDateInput.value;
        const customerId = customerIdInput.value;
        const repaymentCycle = repaymentCycleInput.value;

        // Calculate interest amount
        const interestAmount = (principal * interestRate) / 100;

        // Calculate total obligation
        const totalObligation = principal + interestAmount;

        // Update preview values
        previewObligation.textContent = `₦${totalObligation.toLocaleString('en-NG', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        previewInterest.textContent = `₦${interestAmount.toLocaleString('en-NG', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;

        // Calculate end date
        if (startDate && tenure > 0) {
            const start = new Date(startDate);
            let endDate;

            switch(repaymentCycle) {
                case 'weekly':
                    endDate = new Date(start.getTime() + (tenure * 7 * 24 * 60 * 60 * 1000));
                    break;
                case 'quarterly':
                    endDate = new Date(start);
                    endDate.setMonth(endDate.getMonth() + (tenure * 3));
                    break;
                default: // monthly
                    endDate = new Date(start);
                    endDate.setMonth(endDate.getMonth() + tenure);
                    break;
            }

            previewEndDate.textContent = endDate.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        } else {
            previewEndDate.textContent = '-';
        }

        // Generate reference preview
        if (customerId && interestRate && startDate) {
            const date = new Date(startDate);

            // Format interest (remove decimal, add zero)
            const interestFormatted = interestRate.toString().replace('.', '');

            // Get 3-letter month
            const monthNames = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN',
                              'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
            const month = monthNames[date.getMonth()];

            // Get day
            const day = String(date.getDate()).padStart(2, '0');

            // Get 2-digit year
            const year = String(date.getFullYear()).slice(-2);

            const reference = `${customerId}/${interestFormatted}/${month}/${day}/${year}`;
            previewReference.textContent = reference;
        } else {
            previewReference.textContent = 'Will be generated';
        }
    }

    // Add event listeners
    [principalInput, interestRateInput, tenureInput, startDateInput, customerIdInput, repaymentCycleInput].forEach(input => {
        if (input) {
            input.addEventListener('input', updatePreview);
            input.addEventListener('change', updatePreview);
        }
    });

    // Initial preview update
    updatePreview();
});
</script>
