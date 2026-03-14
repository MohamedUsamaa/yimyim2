// api.js — Shared API helper for yimyim2 PHP backend

const API_BASE = 'api';

const api = {
    async request(endpoint, options = {}) {
        const url = `${API_BASE}/${endpoint}`;
        const defaults = {
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin'
        };
        const res = await fetch(url, { ...defaults, ...options });
        return res.json();
    },

    // Auth
    async login(email, password) {
        return this.request('auth.php?action=login', {
            method: 'POST',
            body: JSON.stringify({ email, password })
        });
    },
    async register(name, email, password) {
        return this.request('auth.php?action=register', {
            method: 'POST',
            body: JSON.stringify({ name, email, password })
        });
    },
    async logout() {
        return this.request('auth.php?action=logout');
    },
    async getSession() {
        return this.request('auth.php?action=session');
    },
    async getProfile() {
        return this.request('auth.php?action=profile');
    },
    async updateProfile(name, email) {
        return this.request('auth.php?action=update_profile', {
            method: 'POST',
            body: JSON.stringify({ name, email })
        });
    },

    // Products
    async getProducts() {
        return this.request('products.php');
    },
    async getProduct(id) {
        return this.request(`products.php?id=${id}`);
    },

    // Cart
    async getCart() {
        return this.request('cart.php');
    },
    async addToCart(productId, quantity = 1) {
        return this.request('cart.php?action=add', {
            method: 'POST',
            body: JSON.stringify({ product_id: productId, quantity })
        });
    },
    async removeFromCart(productId) {
        return this.request('cart.php?action=remove', {
            method: 'POST',
            body: JSON.stringify({ product_id: productId })
        });
    },
    async updateCartQty(productId, delta) {
        return this.request('cart.php?action=update', {
            method: 'POST',
            body: JSON.stringify({ product_id: productId, delta })
        });
    },

    // Checkout
    async getCheckoutSummary() {
        return this.request('checkout.php?action=summary');
    },
    async submitCheckout(firstname, lastname, address, email, phone) {
        return this.request('checkout.php?action=submit', {
            method: 'POST',
            body: JSON.stringify({ firstname, lastname, address, email, phone })
        });
    },

    // Promo
    async applyPromo(code) {
        return this.request('promo.php', {
            method: 'POST',
            body: JSON.stringify({ code })
        });
    },

    // Orders
    async getOrders() {
        return this.request('orders.php');
    },

    // Contact
    async sendContact(name, email, phone, message) {
        return this.request('contact.php', {
            method: 'POST',
            body: JSON.stringify({ name, email, phone, message })
        });
    }
};
