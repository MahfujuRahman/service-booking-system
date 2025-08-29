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
                <div  v-if="isAuthenticated" class="text-center">
                    <h5 class="mb-2">Welcome back, {{ currentUser?.first_name || 'User' }}!</h5>
                    <p class="mb-0">Browse and book services below</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Search / Filters -->
<section class="container py-5" ref="servicesSection">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="search-wrapper p-4 bg-white rounded-lg shadow-lg border-0">
                <div class="input-group input-group-lg">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-light border-0 rounded-0">
                            <i class="fas fa-search text-primary"></i>
                        </span>
                    </div>
                    <input v-model="query" @input="applyFilter" type="search"
                        class="form-control border-0 shadow-none"
                        placeholder="Search services by name, description, or price..."
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

    <!-- Services grid -->
    <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <p class="mt-3 text-muted">Loading services...</p>
    </div>

    <div v-else class="row">
        <div v-if="filtered.length === 0" class="col-12 text-center py-5">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No services found</h4>
            <p class="text-muted">Try adjusting your search or filters</p>
        </div>

        <div v-for="(service, index) in filtered" :key="service && service.id ? service.id : index"
            class="col-sm-6 col-md-4 col-lg-3 mb-4">
            <div class="service-card card h-100 border-0 shadow-sm hover-up"
                :class="{ 'booked': isAuthenticated && isServiceBooked(service.id) }">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="mb-1 font-weight-bold text-dark">{{ service ? (service.name || 'Untitled Service') : 'Untitled' }}</h6>
                        <div class="d-flex flex-column align-items-end">
                            <small class="text-muted mb-1">
                                <i :class="getStatusIcon(service)" class="mr-1"></i>
                                {{ service && service.status ? service.status : 'Unknown' }}
                            </small>
                            <span v-if="isAuthenticated && isServiceBooked(service.id)"
                                class="badge badge-success badge-sm">
                                <i class="fas fa-check mr-1"></i>Booked
                            </span>
                        </div>
                    </div>
                    <p class="text-muted small mb-3 flex-grow-1" style="line-height: 1.4;">
                        {{ service ? (service.description || 'No description available') : '' }}
                    </p>
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="price-tag">
                                <span class="price">৳ {{ service && service.price !== undefined ? service.price
                                    : 'Contact' }}</span>
                                <small class="text-muted d-block">per service</small>
                            </div>
                            <div class="rating">
                                <i class="fas fa-star text-warning"></i>
                                <small class="text-muted ml-1">4.5</small>
                            </div>
                        </div>
                        <div class="btn-group w-100">
                            <button v-if="!isAuthenticated" class="btn btn-outline-primary btn-sm flex-fill"
                                @click="goToLogin">
                                <i class="fas fa-sign-in-alt me-1"></i>Login to Book
                            </button>
                            <button v-else-if="isServiceBooked(service.id)"
                                class="btn btn-success btn-sm flex-fill" disabled>
                                <i class="fas fa-check-circle me-1"></i>Booked ({{ getBookingStatus(service.id)
                                }})
                            </button>
                            <button v-else class="btn btn-primary btn-sm flex-fill"
                                @click="openBookingModal(service)">
                                <i class="fas fa-calendar-check me-1"></i>Book Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div v-if="!loading && pagination && pagination.last_page > 1" class="d-flex justify-content-center mt-4">
        <nav aria-label="Services pagination">
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

<!-- Booking Date Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingModalLabel">Book Service</h5>
            </div>
            <div class="modal-body">
                <div v-if="selectedService" >
                    <h6 class="font-weight-bold">{{ selectedService.name }}</h6>
                    <p class="text-muted small">{{ selectedService.description }}</p>
                </div>
                <div class="form-group">
                    <label for="bookingDate" class="font-weight-bold">Select Booking Date:</label>
                    <input 
                        type="date" 
                        class="form-control" 
                        id="bookingDate" 
                        v-model="bookingDate" 
                        :min="minDate"
                        required>
                    <small class="form-text text-muted">Please select today or a future date</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" @click="confirmBooking" :disabled="!bookingDate || bookingLoading">
                    <span v-if="bookingLoading" class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
                    {{ bookingLoading ? 'Booking...' : 'Confirm Booking' }}
                </button>
            </div>
        </div>
    </div>
</div>
</div>
</template>

<script>
import axios from 'axios';

export default {
mounted() {
// Ensure jQuery is available for Bootstrap modals
if (typeof window.$ === 'undefined') {
    console.warn('jQuery not found, modal may not work properly');
}
},
data: () => ({
services: [],
loading: true,
query: '',
selectedStatus: '',
filtered: [],
pagination: null,
currentPage: 1,
showLoginModal: false,
isAuthenticated: false,
currentUser: null,
userBookings: [],
selectedService: null,
bookingDate: '',
bookingLoading: false,
minDate: '',
}),
created: async function () {
// Set minimum date to today
const today = new Date();
this.minDate = today.toISOString().split('T')[0];

// Set authorization header if token exists
const token = localStorage.getItem('admin_token');
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    await this.checkAuthentication();
}
await this.fetchServices();
},
methods: {
async checkAuthentication() {
    try {
        const authResponse = await axios.get('/auth-check');
        const userResponse = await axios.get('/check_user');

        if (authResponse.status === 200 && userResponse.data?.status === 'success') {
            this.isAuthenticated = true;
            this.currentUser = userResponse.data.data;
            await this.fetchUserBookings();
        }
    } catch (error) {
        this.isAuthenticated = false;
        this.currentUser = null;
        console.log('User not authenticated');
    }
},

async fetchUserBookings() {
    try {
        const response = await axios.get('/bookings');
            if (response.data?.status === 'success') {
                this.userBookings = response.data.data.data || [];
            }
        } catch (error) {
            console.error('Failed to fetch user bookings:', error);
            this.userBookings = [];
        }
    },

    isServiceBooked(serviceId) {
        return this.userBookings.some(booking => booking.service_id == serviceId && booking.user_id == this.currentUser?.id);
    },

getBookingStatus(serviceId) {
    const booking = this.userBookings.find(booking => booking.service_id == serviceId);
    return booking ? booking.status : null;
},
async fetchServices(page = 1) {
    this.loading = true;
    try {
        const params = new URLSearchParams({
            page: page,
            limit: 12, // Show 12 services per page
            status: 'active', // Only show active services by default
        });

        const res = await axios.get(`/services?${params}`);
        if (res.status === 200) {
            const responseData = res.data.data || {};
            this.services = responseData.data || [];
            this.pagination = {
                current_page: responseData.current_page || 1,
                last_page: responseData.last_page || 1,
                per_page: responseData.per_page || 12,
                total: responseData.total || 0,
            };
            this.filtered = this.services.slice();
            this.applyFilter();
        }
    } catch (e) {
        console.error('Failed to load services', e);
        this.services = [];
        this.filtered = [];
    } finally {
        this.loading = false;
    }
},
applyFilter() {
    let filtered = this.services.slice();

    // Search filter
    const q = (this.query || '').toLowerCase().trim();
    if (q) {
        filtered = filtered.filter((s) => {
            if (!s) return false;
            const title = (s.name || '').toString().toLowerCase();
            const desc = (s.description || '').toString().toLowerCase();
            const price = (s.price || '').toString().toLowerCase();
            return title.includes(q) || desc.includes(q) || price.includes(q);
        });
    }

    // Status filter
    if (this.selectedStatus) {
        filtered = filtered.filter((s) => s && s.status === this.selectedStatus);
    }

    this.filtered = filtered;
},
changePage(page) {
    if (page >= 1 && page <= (this.pagination?.last_page || 1)) {
        this.currentPage = page;
        this.fetchServices(page);
        this.scrollToServices();
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
scrollToServices() {
    const el = this.$refs.servicesSection;
    if (el && el.scrollIntoView) el.scrollIntoView({ behavior: 'smooth' });
},

openBookingModal(service) {
    this.selectedService = service;
    this.bookingDate = '';
    // Show modal using Bootstrap's modal method
    $('#bookingModal').modal('show');
},

async confirmBooking() {
    if (!this.bookingDate) {
        window.s_error('Please select a booking date');
        return;
    }

    // Validate date is today or future
    const selectedDate = new Date(this.bookingDate);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    if (selectedDate < today) {
        window.s_error('Please select today or a future date');
        return;
    }

    this.bookingLoading = true;

    try {
        const authResponse = await axios.get('/auth-check');

        if (authResponse && authResponse.status === 200) {
            const checkUser = await axios.get('/check_user');

            if (checkUser.data?.status === 'success' && checkUser.data?.data) {
                try {
                    const bookingData = {
                        service_id: this.selectedService.id,
                        user_id: checkUser.data.data.id,
                        booking_date: this.bookingDate,
                    };

                    const bookingResponse = await axios.post('/bookings/store', bookingData);

                    if (bookingResponse.data?.status === 'success') {
                        window.s_alert('Booking confirmed for ' + this.bookingDate + '!');
                        // Close modal
                        $('#bookingModal').modal('hide');
                        // Refresh user bookings to update UI
                        await this.fetchUserBookings();
                    } else {
                        window.s_error('Failed to create booking: ' + (bookingResponse.data?.message || 'Unknown error'));
                    }
                } catch (bookingError) {
                    console.error('Booking creation failed:', bookingError);
                    const errorMessage = bookingError.response?.data?.message || 'Failed to book service';
                    window.s_error(errorMessage);
                }
            } else {
                console.error('User data not found:', checkUser.data);
                window.s_error('Unable to get user information for booking');
            }
        } else {
            window.s_error('Please login to book a service');
        }
    } catch (err) {
        // Handle authentication errors
        if (err && err.response && err.response.status === 401) {
            window.s_error('Please login to book a service');
        } else {
            console.error('Auth check failed', err);
            window.s_error('Unable to verify authentication. Please try again.');
        }
    } finally {
        this.bookingLoading = false;
    }
},

async bookService(service) {
    try {
        const authResponse = await axios.get('/auth-check');

        if (authResponse && authResponse.status === 200) {
            const checkUser = await axios.get('/check_user');

            if (checkUser.data?.status === 'success' && checkUser.data?.data) {
                try {
                    const bookingData = {
                        service_id: service.id,
                        user_id: checkUser.data.data.id,
                    };

                    const bookingResponse = await axios.post('/bookings/store', bookingData);

                    if (bookingResponse.data?.status === 'success') {
                        window.s_alert('Booking successfully!');
                        // Refresh user bookings to update UI
                        await this.fetchUserBookings();
                    } else {
                        window.s_error('Failed to booking: ' + (bookingResponse.data?.message || 'Unknown error'));
                    }
                } catch (bookingError) {
                    console.error('Booking creation failed:', bookingError);
                    const errorMessage = bookingError.response?.data?.message || 'Failed to book service';
                    window.s_error(errorMessage);
                }
            } else {
                console.error('User data not found:', checkUser.data);
                window.s_error('Unable to get user information for booking');
            }
        } else {
            window.s_error('Please login to book a service');
        }
    } catch (err) {
        // Handle authentication errors
        if (err && err.response && err.response.status === 401) {
            window.s_error('Please login to book a service');
        } else {
            console.error('Auth check failed', err);
            window.s_error('Unable to verify authentication. Please try again.');
        }
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
    // Prefer SPA router if available, otherwise fall back to absolute backend URL
    const backendBookingsUrl = 'http://127.0.0.1:8000/bookings';
    try {
        if (this.$router && typeof this.$router.push === 'function') {
            try {
                this.$router.push({ path: '/bookings' });
                return;
            } catch (e) {
                // router push failed; fall back
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
getStatusClass(service) {
    if (!service || !service.status) return 'badge-secondary';
    return service.status === 'active' ? 'badge-success' : 'badge-warning';
},
getStatusIcon(service) {
    if (!service || !service.status) return 'fas fa-question-circle';
    return service.status === 'active' ? 'fas fa-check-circle' : 'fas fa-pause-circle';
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

.service-card {
border-radius: 15px;
overflow: hidden;
transition: all 0.3s ease;
}

.service-card .card-img-top {
height: 180px;
}

.service-card .card-img-top img {
height: 100%;
object-fit: cover;
transition: transform 0.3s ease;
}

.service-card:hover .card-img-top img {
transform: scale(1.05);
}

.status-badge {
position: absolute;
top: 10px;
right: 10px;
padding: 4px 8px;
border-radius: 20px;
font-size: 0.75rem;
font-weight: 600;
text-transform: uppercase;
letter-spacing: 0.5px;
}

.service-card h6 {
font-size: 1rem;
line-height: 1.3;
}

.price-tag .price {
font-size: 1.25rem;
font-weight: 700;
color: #1a73e8;
}

.rating {
display: flex;
align-items: center;
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

.service-card.booked {
border: 2px solid #28a745;
background: linear-gradient(to bottom right, #f8f9fa, #e9ecef);
}

.badge-sm {
font-size: 0.7rem;
padding: 0.25rem 0.5rem;
}

@media (max-width: 767px) {
.hero {
min-height: 300px;
padding: 2rem 0;
}

.hero h1 {
font-size: 2rem;
}

.service-card .card-img-top {
height: 140px;
}

.btn-group .btn {
font-size: 0.8rem;
}
}
</style>
