<?php
// Toast Notification System for SPARE XPRESS LTD
// Include this file in pages that need toast notifications
?>

<!-- Toast Notification Container -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1060;">
    <!-- Toasts will be dynamically added here -->
</div>

<style>
/* Enhanced Toast Styles */
.toast-container {
    max-width: 400px;
}

.toast {
    border: none;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    backdrop-filter: blur(10px);
    border-left: 4px solid;
    font-weight: 500;
}

.toast-success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border-left-color: #28a745;
    color: #155724;
}

.toast-error {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    border-left-color: #dc3545;
    color: #721c24;
}

.toast-warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border-left-color: #ffc107;
    color: #856404;
}

.toast-info {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    border-left-color: #17a2b8;
    color: #0c5460;
}

.toast .btn-close {
    filter: brightness(0.8);
}

.toast .toast-body {
    font-size: 0.95rem;
    line-height: 1.4;
}

.toast-icon {
    width: 20px;
    height: 20px;
    margin-right: 8px;
    flex-shrink: 0;
}

/* Animation for toast entrance */
@keyframes toastSlideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.toast.show {
    animation: toastSlideIn 0.3s ease-out;
}

/* Mobile responsive */
@media (max-width: 576px) {
    .toast-container {
        max-width: calc(100vw - 2rem);
        left: 1rem;
        right: 1rem;
    }
}
</style>

<script>
// Enhanced Toast Notification System
class ToastManager {
    constructor() {
        this.container = document.querySelector('.toast-container');
        if (!this.container) {
            console.error('Toast container not found. Make sure to include toast_notifications.php');
            return;
        }
        this.toasts = [];
        this.maxToasts = 5;
    }

    show(message, type = 'info', duration = 5000, title = null) {
        // Remove oldest toast if at max capacity
        if (this.toasts.length >= this.maxToasts) {
            this.remove(this.toasts[0]);
        }

        const toastId = 'toast-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
        const toast = this.createToast(toastId, message, type, title);
        this.container.appendChild(toast);
        this.toasts.push(toastId);

        // Initialize Bootstrap toast
        const bsToast = new bootstrap.Toast(toast, {
            delay: duration,
            autohide: duration > 0
        });

        bsToast.show();

        // Remove from array when hidden
        toast.addEventListener('hidden.bs.toast', () => {
            this.remove(toastId);
        });

        return toastId;
    }

    createToast(id, message, type, title) {
        const toast = document.createElement('div');
        toast.id = id;
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');

        const iconClass = this.getIconClass(type);
        const titleText = title || this.getDefaultTitle(type);

        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body d-flex align-items-start">
                    <i class="${iconClass} toast-icon mt-1"></i>
                    <div class="flex-grow-1">
                        <div class="fw-bold small mb-1">${titleText}</div>
                        <div class="toast-message">${message}</div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;

        return toast;
    }

    getIconClass(type) {
        const icons = {
            'success': 'fas fa-check-circle',
            'error': 'fas fa-exclamation-triangle',
            'warning': 'fas fa-exclamation-circle',
            'info': 'fas fa-info-circle'
        };
        return icons[type] || icons.info;
    }

    getDefaultTitle(type) {
        const titles = {
            'success': 'Success',
            'error': 'Error',
            'warning': 'Warning',
            'info': 'Information'
        };
        return titles[type] || 'Notification';
    }

    remove(toastId) {
        const index = this.toasts.indexOf(toastId);
        if (index > -1) {
            this.toasts.splice(index, 1);
        }

        const toast = document.getElementById(toastId);
        if (toast) {
            toast.remove();
        }
    }

    clear() {
        this.toasts.forEach(toastId => {
            const toast = document.getElementById(toastId);
            if (toast) {
                const bsToast = bootstrap.Toast.getInstance(toast);
                if (bsToast) {
                    bsToast.hide();
                }
            }
        });
        this.toasts = [];
    }

    // Convenience methods
    success(message, title = null, duration = 5000) {
        return this.show(message, 'success', duration, title);
    }

    error(message, title = null, duration = 7000) {
        return this.show(message, 'error', duration, title);
    }

    warning(message, title = null, duration = 6000) {
        return this.show(message, 'warning', duration, title);
    }

    info(message, title = null, duration = 5000) {
        return this.show(message, 'info', duration, title);
    }
}

// Global toast manager instance
const toastManager = new ToastManager();

// Global functions for easy access
function showToast(message, type = 'info', title = null, duration = null) {
    return toastManager.show(message, type, duration, title);
}

function showSuccessToast(message, title = null) {
    return toastManager.success(message, title);
}

function showErrorToast(message, title = null) {
    return toastManager.error(message, title);
}

function showWarningToast(message, title = null) {
    return toastManager.warning(message, title);
}

function showInfoToast(message, title = null) {
    return toastManager.info(message, title);
}

// Auto-initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Test toast functionality (remove in production)
    console.log('Toast notification system initialized');
});
</script>