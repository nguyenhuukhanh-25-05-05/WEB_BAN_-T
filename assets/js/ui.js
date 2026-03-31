/**
 * UI & Toast Helpers for NHK Mobile
 */

const ui = {
    // Show a global toast notification
    showToast: function(title, message, iconType = 'cart') {
        const toastEl = document.getElementById('liveToast');
        const toastTitle = document.getElementById('toastTitle');
        const toastMessage = document.getElementById('toastMessage');
        const toastIcon = document.getElementById('toastIcon');
        
        if (!toastEl) return;
        
        toastTitle.textContent = title;
        toastMessage.textContent = message;
        
        // Update icon based on type
        if (iconType === 'cart') {
            toastIcon.innerHTML = '<i class="bi bi-cart-plus-fill"></i>';
            toastIcon.className = 'bg-primary p-2 rounded-3 text-white';
        } else if (iconType === 'success') {
            toastIcon.innerHTML = '<i class="bi bi-check-lg"></i>';
            toastIcon.className = 'bg-success p-2 rounded-3 text-white';
        } else if (iconType === 'error') {
            toastIcon.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i>';
            toastIcon.className = 'bg-danger p-2 rounded-3 text-white';
        }
        
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
    },

    // Global AJAX Add to Cart
    addToCart: function(productId, installment = 0) {
        // Get CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('installment', installment);
        formData.append('csrf_token', csrfToken);
        
        return fetch('api/add_to_cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                this.showToast('Giỏ hàng', data.message, 'cart');
                // Update cart count if exists in UI
                const cartCountEl = document.getElementById('cart-count');
                if (cartCountEl) {
                    cartCountEl.textContent = data.cart_count;
                    cartCountEl.classList.remove('d-none');
                }
            } else if (data.status === 'unauthorized') {
                window.location.href = 'login.php';
            } else {
                this.showToast('Lỗi', data.message, 'error');
            }
            return data;
        })
        .catch(err => {
            console.error('Add to cart error:', err);
            this.showToast('Lỗi', 'Không thể kết nối đến máy chủ.', 'error');
        });
    },

    // Global AJAX Update Cart Quantity
    updateCartQty: function(productId, quantity) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity', quantity);
        formData.append('csrf_token', csrfToken);
        
        return fetch('api/update_cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Update cart count
                const cartCountEl = document.getElementById('cart-count');
                if (cartCountEl) {
                    cartCountEl.textContent = data.cart_count;
                    if (data.cart_count > 0) cartCountEl.classList.remove('d-none');
                    else cartCountEl.classList.add('d-none');
                }

                // Update item subtotal
                const subtotalEl = document.getElementById(`subtotal-${productId}`);
                if (subtotalEl) {
                    subtotalEl.textContent = `Thành tiền: ${data.item_subtotal}`;
                }

                // Update cart total
                const cartTotalEls = document.querySelectorAll('.cart-total-value');
                cartTotalEls.forEach(el => el.textContent = data.cart_total);

                if (data.is_removed) {
                    const row = document.getElementById(`cart-row-${productId}`);
                    if (row) row.remove();
                    if (data.cart_count === 0) window.location.reload(); // Reload to show empty cart message
                }

                if (data.message && data.message !== 'Đã cập nhật giỏ hàng.') {
                    this.showToast('Cảnh báo', data.message, 'error');
                }
            } else {
                this.showToast('Lỗi', data.message, 'error');
            }
            return data;
        })
        .catch(err => {
            console.error('Update cart error:', err);
            this.showToast('Lỗi', 'Không thể cập nhật giỏ hàng.', 'error');
        });
    },

    // Mobile Menu Toggle
    initMobileMenu: function() {
        const toggle = document.getElementById('navToggle');
        const overlay = document.getElementById('navMobileOverlay');
        const body = document.body;

        if (toggle && overlay) {
            toggle.addEventListener('click', () => {
                toggle.classList.toggle('active');
                overlay.classList.toggle('active');
                body.style.overflow = overlay.classList.contains('active') ? 'hidden' : '';
            });

            // Close menu on link click
            overlay.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    toggle.classList.remove('active');
                    overlay.classList.remove('active');
                    body.style.overflow = '';
                });
            });
        }
    }
};

// Initialize UI
document.addEventListener('DOMContentLoaded', function() {
    ui.initMobileMenu();
});

// Initialize event listeners for dynamic add to cart buttons
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-add-to-cart-ajax');
    if (btn) {
        e.preventDefault();
        const pid = btn.dataset.productId;
        const installment = btn.dataset.installment || 0;
        ui.addToCart(pid, installment);
    }
});

// Initialize event listeners for cart quantity inputs
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('cart-qty-input')) {
        const pid = e.target.dataset.productId;
        const qty = e.target.value;
        ui.updateCartQty(pid, qty);
    }
});
