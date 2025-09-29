// Bleiz Custom JavaScript
(function() {
    'use strict';

    // Global Bleiz object
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
                notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${this.getNotificationClasses(type)}`;
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
                loading.className = 'loading-overlay';
                loading.innerHTML = '<div class="spinner"></div>';
                document.body.appendChild(loading);
            },

            // Hide loading
            hideLoading() {
                const loading = document.getElementById('bleiz-loading');
                if (loading) {
                    loading.remove();
                }
            },

            // Confirm dialog
            confirm(message, callback) {
                if (window.confirm(message)) {
                    callback();
                }
            },

            // Debounce function
            debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }
        },

        // AJAX helpers
        api: {
            async get(url) {
                try {
                    const response = await fetch(url, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                    return await response.json();
                } catch (error) {
                    console.error('API GET Error:', error);
                    throw error;
                }
            },

            async post(url, data) {
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(data)
                    });
                    return await response.json();
                } catch (error) {
                    console.error('API POST Error:', error);
                    throw error;
                }
            }
        },

        // Form handling
        forms: {
            // Submit form with loading state
            submitWithLoading(formElement, callback) {
                const submitBtn = formElement.querySelector('[type="submit"]');
                const originalText = submitBtn.textContent;

                submitBtn.disabled = true;
                submitBtn.classList.add('loading');
                submitBtn.textContent = 'Processing...';

                callback().finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('loading');
                    submitBtn.textContent = originalText;
                });
            },

            // Validate form
            validate(formElement) {
                const requiredFields = formElement.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('border-red-500');
                        isValid = false;
                    } else {
                        field.classList.remove('border-red-500');
                    }
                });

                return isValid;
            }
        }
    };

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-hide alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        });

        // Initialize tooltips and other interactive elements
        // This would work with Flowbite components

        console.log('Bleiz JavaScript initialized');
    });
})();
