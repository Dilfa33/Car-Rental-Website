// Admin panel functionality
(function() {
    'use strict';

    // Global state
    let currentDeleteEntity = null;
    let currentDeleteId = null;

    // Load admin page
    window.loadAdminPage = function() {
        console.log('Admin page loaded');

        // Verify admin role
        const userData = JSON.parse(localStorage.getItem('user_data'));
        if (!userData || userData.role !== 'admin') {
            alert('Access denied. Admin privileges required.');
            window.location.hash = '#main';
            return;
        }

        loadAllData();
    };

    // Load all data
    function loadAllData() {
        showLoading();

        Promise.all([
            loadCars(),
            loadUsers(),
            loadBookings(),
            loadReviews()
        ])
            .then(() => {
                hideLoading();
                $('#adminContent').removeClass('d-none');
            })
            .catch(error => {
                console.error('Error loading admin data:', error);
                showError('Failed to load admin dashboard');
            });
    }

    // ========================================
    // LOAD DATA FUNCTIONS
    // ========================================

    function loadCars() {
        return apiRequest('GET', '/cars')
            .then(response => {
                if (response.success && response.data) {
                    displayCars(response.data);
                }
            });
    }

    function loadUsers() {
        return apiRequest('GET', '/users')
            .then(response => {
                if (response.success && response.data) {
                    displayUsers(response.data);
                }
            });
    }

    function loadBookings() {
        return apiRequest('GET', '/bookings')
            .then(response => {
                if (response.success && response.data) {
                    displayBookings(response.data);
                }
            });
    }

    function loadReviews() {
        return apiRequest('GET', '/reviews')
            .then(response => {
                if (response.success && response.data) {
                    displayReviews(response.data);
                }
            });
    }

    // ========================================
    // DISPLAY DATA FUNCTIONS
    // ========================================

    function displayCars(cars) {
        const tbody = $('#carsTableBody');
        tbody.empty();

        cars.forEach(car => {
            const statusClass = car.availability_status === 'available' ? 'success' :
                car.availability_status === 'rented' ? 'warning' : 'secondary';

            tbody.append(`
                <tr>
                    <td>${car.car_id}</td>
                    <td>${car.brand}</td>
                    <td>${car.model}</td>
                    <td>${car.year}</td>
                    <td>${car.transmission}</td>
                    <td>${car.fuel_type}</td>
                    <td>$${parseFloat(car.daily_rate).toFixed(2)}</td>
                    <td><span class="badge bg-${statusClass}">${car.availability_status}</span></td>
                    <td>
                        <button class="btn btn-sm btn-primary edit-car-btn" data-id="${car.car_id}">Edit</button>
                        <button class="btn btn-sm btn-danger delete-car-btn" data-id="${car.car_id}" data-name="${car.brand} ${car.model}">Delete</button>
                    </td>
                </tr>
            `);
        });
    }

    function displayUsers(users) {
        const tbody = $('#usersTableBody');
        tbody.empty();

        users.forEach(user => {
            const roleClass = user.role === 'admin' ? 'danger' : 'primary';

            tbody.append(`
                <tr>
                    <td>${user.user_id}</td>
                    <td>${user.first_name}</td>
                    <td>${user.last_name}</td>
                    <td>${user.email}</td>
                    <td>${user.phone || 'N/A'}</td>
                    <td>$${parseFloat(user.balance || 0).toFixed(2)}</td>
                    <td><span class="badge bg-${roleClass}">${user.role}</span></td>
                    <td>
                        <button class="btn btn-sm btn-primary edit-user-btn" data-id="${user.user_id}">Edit</button>
                        <button class="btn btn-sm btn-danger delete-user-btn" data-id="${user.user_id}" data-name="${user.first_name} ${user.last_name}">Delete</button>
                    </td>
                </tr>
            `);
        });
    }

    function displayBookings(bookings) {
        const tbody = $('#bookingsTableBody');
        tbody.empty();

        bookings.forEach(booking => {
            const statusClass = booking.status === 'confirmed' ? 'success' :
                booking.status === 'pending' ? 'warning' : 'secondary';

            tbody.append(`
                <tr>
                    <td>${booking.booking_id}</td>
                    <td>${booking.user_id}</td>
                    <td>${booking.car_id}</td>
                    <td>${formatDate(booking.start_date)}</td>
                    <td>${formatDate(booking.end_date)}</td>
                    <td>$${parseFloat(booking.total_price).toFixed(2)}</td>
                    <td><span class="badge bg-${statusClass}">${booking.status}</span></td>
                    <td>
                        <button class="btn btn-sm btn-primary edit-booking-btn" data-id="${booking.booking_id}">Edit</button>
                        <button class="btn btn-sm btn-danger delete-booking-btn" data-id="${booking.booking_id}">Delete</button>
                    </td>
                </tr>
            `);
        });
    }

    function displayReviews(reviews) {
        const tbody = $('#reviewsTableBody');
        tbody.empty();

        reviews.forEach(review => {
            tbody.append(`
                <tr>
                    <td>${review.review_id}</td>
                    <td>${review.car_id}</td>
                    <td>${review.user_id}</td>
                    <td>${review.rating}/5 ⭐</td>
                    <td>${review.comment ? review.comment.substring(0, 50) + '...' : 'N/A'}</td>
                    <td>${formatDate(review.created_at)}</td>
                    <td>
                        <button class="btn btn-sm btn-primary edit-review-btn" data-id="${review.review_id}">Edit</button>
                        <button class="btn btn-sm btn-danger delete-review-btn" data-id="${review.review_id}">Delete</button>
                    </td>
                </tr>
            `);
        });
    }

    // ========================================
    // CAR CRUD OPERATIONS
    // ========================================

    $(document).on('click', '#addCarBtn', function() {
        openCarModal();
    });

    $(document).on('click', '.edit-car-btn', function() {
        const carId = $(this).data('id');
        apiRequest('GET', `/cars/${carId}`)
            .then(response => {
                if (response.success && response.data) {
                    openCarModal(response.data);
                }
            });
    });

    function openCarModal(car = null) {
        const isEdit = car !== null;

        $('#carModalTitle').text(isEdit ? 'Edit Car' : 'Add New Car');
        $('#car_id').val(isEdit ? car.car_id : '');
        $('#car_brand').val(isEdit ? car.brand : '');
        $('#car_model').val(isEdit ? car.model : '');
        $('#car_year').val(isEdit ? car.year : '');
        $('#car_transmission').val(isEdit ? car.transmission : 'manual');
        $('#car_fuel_type').val(isEdit ? car.fuel_type : 'petrol');
        $('#car_daily_rate').val(isEdit ? car.daily_rate : '');
        $('#car_availability_status').val(isEdit ? car.availability_status : 'available');
        $('#car_mileage').val(isEdit ? car.mileage : 0);
        $('#car_image_url').val(isEdit ? car.image_url : '');

        const modal = new bootstrap.Modal(document.getElementById('carModal'));
        modal.show();
    }

    $(document).on('submit', '#carForm', function(e) {
        e.preventDefault();

        const carId = $('#car_id').val();
        const isEdit = carId !== '';

        const data = {
            brand: $('#car_brand').val(),
            model: $('#car_model').val(),
            year: parseInt($('#car_year').val()),
            transmission: $('#car_transmission').val(),
            fuel_type: $('#car_fuel_type').val(),
            daily_rate: parseFloat($('#car_daily_rate').val()),
            availability_status: $('#car_availability_status').val(),
            mileage: parseInt($('#car_mileage').val()),
            image_url: $('#car_image_url').val()
        };

        const method = isEdit ? 'PUT' : 'POST';
        const url = isEdit ? `/cars/${carId}` : '/cars';

        apiRequest(method, url, data)
            .then(response => {
                if (response.success) {
                    alert(isEdit ? 'Car updated successfully!' : 'Car added successfully!');
                    bootstrap.Modal.getInstance(document.getElementById('carModal')).hide();
                    loadCars();
                } else {
                    alert('Error: ' + response.message);
                }
            });
    });

    $(document).on('click', '.delete-car-btn', function() {
        currentDeleteEntity = 'car';
        currentDeleteId = $(this).data('id');
        const carName = $(this).data('name');

        $('#deleteEntityType').text(`car (${carName})`);
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    });

    // ========================================
    // USER CRUD OPERATIONS
    // ========================================

    $(document).on('click', '#addUserBtn', function() {
        openUserModal();
    });

    $(document).on('click', '.edit-user-btn', function() {
        const userId = $(this).data('id');
        apiRequest('GET', `/users/${userId}`)
            .then(response => {
                if (response.success && response.data) {
                    openUserModal(response.data);
                }
            });
    });

    function openUserModal(user = null) {
        const isEdit = user !== null;

        $('#userModalTitle').text(isEdit ? 'Edit User' : 'Add New User');
        $('#user_id').val(isEdit ? user.user_id : '');
        $('#user_first_name').val(isEdit ? user.first_name : '');
        $('#user_last_name').val(isEdit ? user.last_name : '');
        $('#user_email').val(isEdit ? user.email : '');
        $('#user_phone').val(isEdit ? user.phone : '');
        $('#user_password').val('');
        $('#user_balance').val(isEdit ? user.balance : 0);
        $('#user_role').val(isEdit ? user.role : 'customer');

        const modal = new bootstrap.Modal(document.getElementById('userModal'));
        modal.show();
    }

    $(document).on('submit', '#userForm', function(e) {
        e.preventDefault();

        const userId = $('#user_id').val();
        const isEdit = userId !== '';

        const data = {
            first_name: $('#user_first_name').val(),
            last_name: $('#user_last_name').val(),
            email: $('#user_email').val(),
            phone: $('#user_phone').val(),
            balance: parseFloat($('#user_balance').val()),
            role: $('#user_role').val()
        };

        const password = $('#user_password').val();
        if (password) {
            data.password = password;
        }

        const method = isEdit ? 'PUT' : 'POST';
        const url = isEdit ? `/users/${userId}` : '/users';

        apiRequest(method, url, data)
            .then(response => {
                if (response.success) {
                    alert(isEdit ? 'User updated successfully!' : 'User added successfully!');
                    bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
                    loadUsers();
                } else {
                    alert('Error: ' + response.message);
                }
            });
    });

    $(document).on('click', '.delete-user-btn', function() {
        currentDeleteEntity = 'user';
        currentDeleteId = $(this).data('id');
        const userName = $(this).data('name');

        $('#deleteEntityType').text(`user (${userName})`);
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    });

    // ========================================
    // BOOKING CRUD OPERATIONS
    // ========================================

    $(document).on('click', '#addBookingBtn', function() {
        openBookingModal();
    });

    $(document).on('click', '.edit-booking-btn', function() {
        const bookingId = $(this).data('id');
        apiRequest('GET', `/bookings/${bookingId}`)
            .then(response => {
                if (response.success && response.data) {
                    openBookingModal(response.data);
                }
            });
    });

    function openBookingModal(booking = null) {
        const isEdit = booking !== null;

        $('#bookingModalTitle').text(isEdit ? 'Edit Booking' : 'Add New Booking');
        $('#booking_id').val(isEdit ? booking.booking_id : '');
        $('#booking_user_id').val(isEdit ? booking.user_id : '');
        $('#booking_car_id').val(isEdit ? booking.car_id : '');
        $('#booking_start_date').val(isEdit ? booking.start_date.split(' ')[0] : '');
        $('#booking_end_date').val(isEdit ? booking.end_date.split(' ')[0] : '');
        $('#booking_total_price').val(isEdit ? booking.total_price : '');
        $('#booking_status').val(isEdit ? booking.status : 'pending');

        const modal = new bootstrap.Modal(document.getElementById('bookingModal'));
        modal.show();
    }

    $(document).on('submit', '#bookingForm', function(e) {
        e.preventDefault();

        const bookingId = $('#booking_id').val();
        const isEdit = bookingId !== '';

        const data = {
            user_id: parseInt($('#booking_user_id').val()),
            car_id: parseInt($('#booking_car_id').val()),
            start_date: $('#booking_start_date').val(),
            end_date: $('#booking_end_date').val(),
            total_price: parseFloat($('#booking_total_price').val()),
            status: $('#booking_status').val()
        };

        const method = isEdit ? 'PUT' : 'POST';
        const url = isEdit ? `/bookings/${bookingId}` : '/bookings';

        apiRequest(method, url, data)
            .then(response => {
                if (response.success) {
                    alert(isEdit ? 'Booking updated successfully!' : 'Booking added successfully!');
                    bootstrap.Modal.getInstance(document.getElementById('bookingModal')).hide();
                    loadBookings();
                } else {
                    alert('Error: ' + response.message);
                }
            });
    });

    $(document).on('click', '.delete-booking-btn', function() {
        currentDeleteEntity = 'booking';
        currentDeleteId = $(this).data('id');

        $('#deleteEntityType').text('booking');
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    });

    // ========================================
    // REVIEW CRUD OPERATIONS
    // ========================================

    $(document).on('click', '#addReviewBtn', function() {
        openReviewModal();
    });

    $(document).on('click', '.edit-review-btn', function() {
        const reviewId = $(this).data('id');
        apiRequest('GET', `/reviews/${reviewId}`)
            .then(response => {
                if (response.success && response.data) {
                    openReviewModal(response.data);
                }
            });
    });

    function openReviewModal(review = null) {
        const isEdit = review !== null;

        $('#reviewModalTitle').text(isEdit ? 'Edit Review' : 'Add New Review');
        $('#review_id').val(isEdit ? review.review_id : '');
        $('#review_car_id').val(isEdit ? review.car_id : '');
        $('#review_user_id').val(isEdit ? review.user_id : '');
        $('#review_rating').val(isEdit ? review.rating : '');
        $('#review_comment').val(isEdit ? review.comment : '');

        const modal = new bootstrap.Modal(document.getElementById('reviewModal'));
        modal.show();
    }

    $(document).on('submit', '#reviewForm', function(e) {
        e.preventDefault();

        const reviewId = $('#review_id').val();
        const isEdit = reviewId !== '';

        const data = {
            car_id: parseInt($('#review_car_id').val()),
            user_id: parseInt($('#review_user_id').val()),
            rating: parseInt($('#review_rating').val()),
            comment: $('#review_comment').val()
        };

        const method = isEdit ? 'PUT' : 'POST';
        const url = isEdit ? `/reviews/${reviewId}` : '/reviews';

        apiRequest(method, url, data)
            .then(response => {
                if (response.success) {
                    alert(isEdit ? 'Review updated successfully!' : 'Review added successfully!');
                    bootstrap.Modal.getInstance(document.getElementById('reviewModal')).hide();
                    loadReviews();
                } else {
                    alert('Error: ' + response.message);
                }
            });
    });

    $(document).on('click', '.delete-review-btn', function() {
        currentDeleteEntity = 'review';
        currentDeleteId = $(this).data('id');

        $('#deleteEntityType').text('review');
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    });

    // ========================================
    // DELETE CONFIRMATION
    // ========================================

    $(document).on('click', '#confirmDeleteBtn', function() {
        if (!currentDeleteEntity || !currentDeleteId) return;

        const urlMap = {
            'car': '/cars',
            'user': '/users',
            'booking': '/bookings',
            'review': '/reviews'
        };

        const url = `${urlMap[currentDeleteEntity]}/${currentDeleteId}`;

        apiRequest('DELETE', url)
            .then(response => {
                if (response.success) {
                    alert(`${currentDeleteEntity} deleted successfully!`);
                    bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();

                    // Reload appropriate data
                    if (currentDeleteEntity === 'car') loadCars();
                    else if (currentDeleteEntity === 'user') loadUsers();
                    else if (currentDeleteEntity === 'booking') loadBookings();
                    else if (currentDeleteEntity === 'review') loadReviews();
                } else {
                    alert('Error: ' + response.message);
                }
            });
    });

    // ========================================
    // UTILITY FUNCTIONS
    // ========================================

    function apiRequest(method, endpoint, data = null) {
        const token = localStorage.getItem('jwt_token');

        return $.ajax({
            url: 'http://localhost/Car-Rental-Website/backend/rest' + endpoint,
            method: method,
            headers: {
                'Authentication': token,
                'Content-Type': 'application/json'
            },
            data: data ? JSON.stringify(data) : null,
            dataType: 'json'
        }).fail(function(xhr) {
            console.error('API Error:', xhr);
            console.error('Response Text:', xhr.responseText); // ADD THIS LINE
        });
    }

    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    function showLoading() {
        $('#adminLoading').show();
        $('#adminError').addClass('d-none');
        $('#adminContent').addClass('d-none');
    }

    function hideLoading() {
        $('#adminLoading').hide();
    }

    function showError(message) {
        $('#adminError').text(message).removeClass('d-none');
        $('#adminLoading').hide();
    }

})();