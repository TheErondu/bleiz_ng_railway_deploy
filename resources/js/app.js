import './bootstrap';

// Bleiz Custom JavaScript
window.Bleiz = {
    // Configuration
    config: {
        currency: '₦',
        dateFormat: 'DD/MM/YYYY',
        apiBaseUrl: '/api/v1'
    },

    // Utility functions
    utils: {
        // Format currency
        formatCurrency(amount, symbol = '₦') {
            return symbol + new Intl.NumberFormat('en-NG').format(amount);
        },

        // Format date
        formatDate(date, format = 'DD/MM/YYYY') {
            const d = new Date(date);
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = d.getFullYear();

            return format
                .replace('DD', day)
                .replace('MM', month)
                .replace('YYYY', year);
        },

        // Show notification
        showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transition-all ${this.getNotificationClasses(type)}`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <span class="flex-1">${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-gray-400 hover:text-gray-600">×</button>
                </div>
            `;

            document.body.appendChild(notification);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        },

        getNotificationClasses(type) {
            switch(type) {
                case 'success': return 'bg-green-100 text-green-800 border border-green-200';
                case 'error': return 'bg-red-100 text-red-800 border border-red-200';
                case 'warning': return 'bg-yellow-100 text-yellow-800 border border-yellow-200';
                default: return 'bg-blue-100 text-blue-800 border border-blue-200';
            }
        },

        // Show loading
        showLoading() {
            const loading = document.createElement('div');
            loading.id = 'bleiz-loading';
            loading.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            loading.innerHTML = '<div class="bg-white p-4 rounded-lg"><div class="animate-spin h-8 w-8 border-4 border-blue-500 border-t-transparent rounded-full"></div></div>';
            document.body.appendChild(loading);
        },

        // Hide loading
        hideLoading() {
            const loading = document.getElementById('bleiz-loading');
            if (loading) {
                loading.remove();
            }
        }
    }
};

// Initialize Flowbite components
import 'flowbite';

console.log('Bleiz Credit:\nSmart Loans, Built for Nigerians.\nGet the capital you need without hidden fees or stress.\nFast, secure, and transparent.');
