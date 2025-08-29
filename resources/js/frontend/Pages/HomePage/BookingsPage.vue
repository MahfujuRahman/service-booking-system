<template>
    <div class="landing-page">
        <!-- Header -->
        <header class="site-navbar navbar navbar-expand-lg navbar-dark sticky-top shadow-sm">
            <div class="container d-flex align-items-center justify-content-between">
                <a class="navbar-brand d-flex align-items-center" href="/">
                    <span class="brand-icon me-2"><i class="fas fa-tools"></i></span>
                    <span class="brand-text">QTech Professional Services</span>
                </a>

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav align-items-center">
                        <li class="nav-item me-2" v-if="!isAuthenticated">
                            <button class="btn btn-outline-light nav-btn" @click="goToLogin">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </button>
                        </li>
                        <li class="nav-item me-2" v-if="!isAuthenticated">
                            <button class="btn btn-light nav-btn register-btn" @click="goToRegister">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </button>
                        </li>

                        <li class="nav-item me-2" v-if="isAuthenticated">
                            <button class="btn btn-outline-light nav-btn" @click="goToBookings">
                                <i class="fas fa-calendar-alt me-1"></i>All Bookings
                            </button>
                        </li>

                        <li class="nav-item d-flex align-items-center" v-if="isAuthenticated">
                            <button class="btn btn-outline-light nav-btn" @click="logout">
                                <i class="fas fa-sign-out-alt me-1"></i>Logout
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </header>


        <!-- Hero -->
        <section class="hero text-white d-flex align-items-center">
            <div class="container py-5">
                <div class="row align-items-center">
                    <div class="col-md-12 text-center">
                        <h1 class="display-4 font-weight-bold mb-3">Professional Services Marketplace</h1>
                        <p class="lead mt-3 mb-4">Discover and book verified professionals for all your needs. Browse
                            our comprehensive service catalog — booking requires a quick sign-in.</p>
                    </div>
                    <div class="col-md-12 text-center">
                        <div  v-if="!isAuthenticated" class="text-center">
                            <h5 class="mb-2">Welcome back, {{ currentUser?.name || 'User' }}!</h5>
                            <p class="mb-0">Browse and book services below</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Bookings Section -->
        <section class="container py-5" ref="bookingsSection">
            <div class="row mb-4">
                <div class="col-md-12">
                    <h2 class="text-center mb-4">All Bookings</h2>
                    <div class="search-wrapper p-4 bg-white rounded-lg shadow-lg border-0">
                        <div class="input-group input-group-lg">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-0 rounded-0">
                                    <i class="fas fa-search text-primary"></i>
                                </span>
                            </div>
                            <input v-model="query" @input="applyFilter" type="search"
                                class="form-control border-0 shadow-none"
                                placeholder="Search bookings by service name, user, or status..."
                                style="font-size: 1rem;" />
                            <div class="input-group-append">
                                <button class="btn btn-primary rounded-0 px-4" @click="applyFilter"
                                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                                    <i class="fas fa-search me-1"></i>Search
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bookings List -->
            <div v-if="loading" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Loading bookings...</p>
            </div>

            <div v-else class="row">
                <div v-if="filtered.length === 0" class="col-12 text-center py-5">
                    <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No bookings found</h4>
                    <p class="text-muted">Try adjusting your search or check back later</p>
                </div>

                <div v-for="(booking, index) in filtered" :key="booking && booking.id ? booking.id : index"
                    class="col-12 mb-3">
                    <div class="booking-card card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <h6 class="mb-1 font-weight-bold text-dark">
                                        {{ booking && booking.service ? booking.service.name : 'Unknown Service' }}
                                    </h6>
                                </div>
                                <div class="col-md-2">
                                    <small class="text-muted d-block">Customer</small>
                                    <span class="font-weight-bold">
                                        {{ booking && booking.user ? booking.user.name : 'Unknown User' }}
                                    </span>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Booking Date</small>
                                    <span class="font-weight-bold">
                                        {{ booking && booking.booking_date ? formatDate(booking.booking_date) : 'N/A' }}
                                    </span>
                                </div>
                                <div class="col-md-2">
                                    <small class="text-muted d-block">Created</small>
                                    <span>{{ booking && booking.created_at ? formatDate(booking.created_at) : 'N/A' }}</span>
                                </div>
                                <div class="col-md-2">
                                    <span class="badge" :class="getBookingStatusClass(booking.status)">
                                        <i :class="getBookingStatusIcon(booking.status)" class="me-1"></i>
                                        {{ booking && booking.status ? booking.status : 'Unknown' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="!loading && pagination && pagination.last_page > 1" class="d-flex justify-content-center mt-4">
                <nav aria-label="Bookings pagination">
                    <ul class="pagination">
                        <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                            <button class="page-link" @click="changePage(pagination.current_page - 1)">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                        </li>
                        <li v-for="page in getVisiblePages()" :key="page" class="page-item"
                            :class="{ active: page === pagination.current_page }">
                            <button class="page-link" @click="changePage(page)">{{ page }}</button>
                        </li>
                        <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                            <button class="page-link" @click="changePage(pagination.current_page + 1)">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </li>
                    </ul>
                </nav>
            </div>
        </section>

    </div>
</template>

<script>
import axios from 'axios';

export default {

    data: () => ({
        bookings: [],
        loading: true,
        query: '',
        selectedStatus: '',
        filtered: [],
        pagination: null,
        currentPage: 1,
        showLoginModal: false,
        isAuthenticated: false,
        currentUser: null,
        selectedBooking: null,
    }),
    created: async function () {
        // Set authorization header if token exists
        const token = localStorage.getItem('admin_token');
        if (token) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            await this.checkAuthentication();
        }
        await this.fetchBookings();
    },
    methods: {
        async checkAuthentication() {
            try {
                const authResponse = await axios.get('/auth-check');
                const userResponse = await axios.get('/check_user');

                if (authResponse.status === 200 && userResponse.data?.status === 'success') {
                    this.isAuthenticated = true;
                    this.currentUser = userResponse.data.data;
                }
            } catch (error) {
                this.isAuthenticated = false;
                this.currentUser = null;
                console.log('User not authenticated');
            }
        },

        async fetchBookings(page = 1) {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page: page,
                    limit: 15, // Show 15 bookings per page
                    bookings: 'bookings'
                });

                const res = await axios.get(`/bookings?${params}`);
                if (res.status === 200) {
                    const responseData = res.data.data || {};
                    this.bookings = responseData.data || [];
                    this.pagination = {
                        current_page: responseData.current_page || 1,
                        last_page: responseData.last_page || 1,
                        per_page: responseData.per_page || 15,
                        total: responseData.total || 0,
                    };
                    this.filtered = this.bookings.slice();
                    this.applyFilter();
                }
            } catch (e) {
                console.error('Failed to load bookings', e);
                this.bookings = [];
                this.filtered = [];
            } finally {
                this.loading = false;
            }
        },

        applyFilter() {
            let filtered = this.bookings.slice();

            // Search filter
            const q = (this.query || '').toLowerCase().trim();
            if (q) {
                filtered = filtered.filter((booking) => {
                    if (!booking) return false;
                    const serviceName = (booking.service && booking.service.name || '').toString().toLowerCase();
                    const userName = (booking.user && booking.user.name || '').toString().toLowerCase();
                    const status = (booking.status || '').toString().toLowerCase();
                    const bookingDate = (booking.booking_date || '').toString().toLowerCase();
                    return serviceName.includes(q) || userName.includes(q) || status.includes(q) || bookingDate.includes(q);
                });
            }

            // Status filter
            if (this.selectedStatus) {
                filtered = filtered.filter((booking) => booking && booking.status === this.selectedStatus);
            }

            this.filtered = filtered;
        },

        changePage(page) {
            if (page >= 1 && page <= (this.pagination?.last_page || 1)) {
                this.currentPage = page;
                this.fetchBookings(page);
                this.scrollToBookings();
            }
        },

        getVisiblePages() {
            if (!this.pagination) return [];
            const current = this.pagination.current_page;
            const last = this.pagination.last_page;
            const pages = [];

            if (last <= 7) {
                for (let i = 1; i <= last; i++) pages.push(i);
            } else {
                pages.push(1);
                if (current > 4) pages.push('...');
                const start = Math.max(2, current - 1);
                const end = Math.min(last - 1, current + 1);
                for (let i = start; i <= end; i++) pages.push(i);
                if (current < last - 3) pages.push('...');
                pages.push(last);
            }

            return pages;
        },

        scrollToBookings() {
            const el = this.$refs.bookingsSection;
            if (el && el.scrollIntoView) el.scrollIntoView({ behavior: 'smooth' });
        },

        formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        },

        getBookingStatusClass(status) {
            if (!status) return 'badge-secondary';
            switch (status.toLowerCase()) {
                case 'confirmed': return 'badge-success';
                case 'pending': return 'badge-warning';
                case 'cancelled': return 'badge-danger';
                case 'completed': return 'badge-primary';
                default: return 'badge-secondary';
            }
        },

        getBookingStatusIcon(status) {
            if (!status) return 'fas fa-question-circle';
            switch (status.toLowerCase()) {
                case 'confirmed': return 'fas fa-check-circle';
                case 'pending': return 'fas fa-clock';
                case 'cancelled': return 'fas fa-times-circle';
                case 'completed': return 'fas fa-flag-checkered';
                default: return 'fas fa-question-circle';
            }
        },

        goToLogin() {

            try {
                if (this.$router && typeof this.$router.push === 'function') {
                    try {
                        this.$router.push({ name: 'Login' });
                        return;
                    } catch (e) {
                        try {
                            this.$router.push('/login');
                            return;
                        } catch (e2) {
                        }
                    }
                } else {
                    console.log('Router not available');
                }
            } catch (e) {
                console.log('Router error:', e.message);
                // ignore and hard redirect below
            }
            // final fallback
            window.location.href = '/login';
        },
        goToRegister() {
            try {
                if (this.$router && typeof this.$router.push === 'function') {
                    try {
                        this.$router.push({ name: 'Register' });
                        return;
                    } catch (e) {
                        try {
                            this.$router.push('/register');
                            return;
                        } catch (e2) {
                        }
                    }
                } else {
                    console.log('Router not available');
                }
            } catch (e) {
                console.log('Router error:', e.message);
                // ignore and hard redirect below
            }
            // final fallback
            window.location.href = '/register';
        },
        goToBookings() {
            const backendBookingsUrl = 'http://127.0.0.1:8000/bookings';
            try {
                if (this.$router && typeof this.$router.push === 'function') {
                    try {
                        this.$router.push({ path: '/bookings' });
                        return;
                    } catch (e) {
                        // fall back to absolute URL
                    }
                }
            } catch (e) {
                // ignore
            }
            window.location.href = backendBookingsUrl;
        },
        async logout() {
            let con = await window.s_confirm("Are you sure want to logout?");
            if (con) {
                localStorage.removeItem("admin_token");
                window.location.href = "/";
            }
        },
    },
};
</script>

<style scoped>
.hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 400px;
    position: relative;
}

/* Header / Navbar styling improvements */
.site-navbar {
    background: linear-gradient(135deg, #5563d6 0%, #6f4bb3 100%);
    border-bottom: 1px solid rgba(255,255,255,0.08);
}
.site-navbar .brand-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border-radius: 8px;
    background: rgba(255,255,255,0.08);
    color: #ffd970;
    font-size: 16px;
}
.site-navbar .brand-text {
    font-weight: 700;
    color: #fff;
    font-size: 1.05rem;
    text-shadow: 0 1px 2px rgba(0,0,0,0.25);
}
.nav-btn {
    border-radius: 22px;
    padding: 8px 14px;
    font-weight: 600;
    transition: transform 0.12s ease, box-shadow 0.12s ease;
    border-width: 1px;
}
.nav-btn:hover {
    transform: translateY(-2px);
}
.register-btn {
    color: #5563d6;
}
.user-welcome {
    font-weight: 600;
    white-space: nowrap;
}

@media (max-width: 767px) {
    .site-navbar .brand-text { font-size: 0.95rem; }
    .nav-btn { padding: 6px 10px; }
    .user-welcome { display: none; }
}

.hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.1);
    pointer-events: none;
}

.hero .hero-card {
    border-radius: 15px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Booking-specific styles */
.booking-card {
    border-radius: 12px;
    transition: all 0.2s ease;
}

.booking-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.booking-card .card-body {
    padding: 1.25rem;
}

.badge {
    font-size: 0.75rem;
    padding: 0.4em 0.8em;
    font-weight: 600;
}

.badge-success {
    background-color: #28a745;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.badge-danger {
    background-color: #dc3545;
}

.badge-primary {
    background-color: #007bff;
}

.badge-secondary {
    background-color: #6c757d;
}

.dropdown-toggle::after {
    display: none;
}

.hover-up {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-up:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
}

.pagination .page-link {
    border-radius: 8px;
    margin: 0 2px;
    border: 1px solid #e9ecef;
}

.pagination .page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
}

.modal-content {
    border-radius: 15px;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}

@media (max-width: 767px) {
    .hero {
        min-height: 300px;
        padding: 2rem 0;
    }

    .hero h1 {
        font-size: 2rem;
    }

    .booking-card .card-body {
        padding: 1rem;
    }

    .booking-card .row {
        text-align: center;
    }

    .booking-card .col-md-3,
    .booking-card .col-md-2,
    .booking-card .col-md-1 {
        margin-bottom: 0.5rem;
    }
}
</style>
