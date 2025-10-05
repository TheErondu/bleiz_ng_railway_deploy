<!-- Reject Loan Modal -->
<div id="reject-loan-modal-{{ $loan->id }}" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Reject Loan Application
                </h3>
                <button type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="reject-loan-modal-{{ $loan->id }}">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <!-- Modal body -->
            <form action="{{ route('admin.loans.reject', $loan) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="p-4 md:p-5">
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Are you sure you want to reject this loan application?
                        </p>

                        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg mb-4">
                            <p class="text-sm"><strong>Customer:</strong> {{ $loan->customer->user->name }}</p>
                            <p class="text-sm"><strong>Amount:</strong> ₦{{ number_format($loan->principal, 2) }}</p>
                            <p class="text-sm"><strong>Interest Rate:</strong> {{ $loan->interest_rate }}%</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="rejection_reason_{{ $loan->id }}"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Reason for Rejection <span class="text-red-500">*</span>
                        </label>
                        <textarea id="rejection_reason_{{ $loan->id }}" name="rejection_reason" rows="4" required
                            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                            placeholder="Enter the reason for rejecting this loan application..."></textarea>
                        @error('rejection_reason')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Modal footer -->
                <div
                    class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600 space-x-3">
                    <button type="button"
                        class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600"
                        data-modal-hide="reject-loan-modal-{{ $loan->id }}">
                        Cancel
                    </button>
                    <button type="submit"
                        class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                        Reject Loan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
