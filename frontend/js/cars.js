// Cars page functionality
(function() {
    'use strict';

    // API Base URL
    const API_BASE_URL = 'https://seashell-app-mqlu9.ondigitalocean.app';

    window.loadCarsPage = function() {
        console.log('Cars page loaded');
        loadCars();
    };

    function loadCars() {
        const token = localStorage.getItem('jwt_token');

        if (!token) {
            showError('Please login to view cars');
            return;
        }

        showLoading();

        $.ajax({
            url: `${API_BASE_URL}/cars`,
            method: 'GET',
            headers: {
                'Authentication': token
            },
            success: function(response) {
                console.log('Cars loaded:', response);
                hideLoading();

                if (response.success && response.data && response.data.length > 0) {
                    displayCars(response.data);
                } else {
                    showError('No cars available at the moment');
                }
            },
            error: function(xhr) {
                console.error('Error loading cars:', xhr);
                hideLoading();
                showError('Failed to load cars: ' + (xhr.responseText || 'Unknown error'));
            }
        });
    }

    function displayCars(cars) {
        const grid = $('#carsGrid');
        grid.empty();

        cars.forEach(car => {
            const carCard = createCarCard(car);
            grid.append(carCard);
        });
    }

    function createCarCard(car) {
        const isAvailable = car.availability_status === 'available';
        const statusBadge = isAvailable ? 'bg-success' : 'bg-danger';
        const statusText = isAvailable ? 'Available' : 'Unavailable';
        const buttonText = isAvailable ? 'Book Now' : 'Not Available';

        return `
            <div class="col-md-6 col-lg-4">
                <div class="card car-card h-100 shadow-sm">
                    <img src="assets/imgs/${car.image_url || 'default.jpg'}"
                         class="card-img-top"
                         alt="${car.brand} ${car.model}"
                         onerror="this.src='assets/imgs/default.jpg'">
                    <div class="card-body">
                        <h5 class="card-title">${car.brand} ${car.model}</h5>
                        <p class="text-muted mb-2">
                            ${car.transmission} • ${car.fuel_type}
                        </p>
                        <div class="car-specs d-flex justify-content-between mb-3">
                            <div><strong>Year: ${car.year}</strong></div>
                            <div><strong>A/C</strong></div>
                        </div>
                        <div class="car-specs d-flex justify-content-between mb-3">
                            <div><small>Mileage: ${car.mileage || 0} km</small></div>
                        </div>
                        <div class="car-price mb-3">
                            $${parseFloat(car.daily_rate).toFixed(2)}<small>/day</small>
                        </div>
                        <span class="badge ${statusBadge} mb-2">
                            ${statusText}
                        </span>
                        <button class="btn btn-primary w-100 book-car-btn"
                                data-car-id="${car.car_id}"
                                data-car-name="${car.brand} ${car.model}"
                                data-daily-rate="${car.daily_rate}"
                                ${!isAvailable ? 'disabled' : ''}>
                            ${buttonText}
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    $(document).on('click', '.book-car-btn', function() {
        const carId = $(this).data('car-id');
        const carName = $(this).data('car-name');
        const dailyRate = $(this).data('daily-rate');

        console.log('Book car clicked:', carId, carName, dailyRate);

        openBookingModal(carId, carName, dailyRate);
    });

    function openBookingModal(carId, carName, dailyRate) {
        $('#bookingForm')[0].reset();
        $('#bookingError').addClass('d-none');
        $('#bookingSuccess').addClass('d-none');
        $('#bookingInfo').addClass('d-none');

        $('#booking_car_id').val(carId);
        $('#booking_daily_rate').val(dailyRate);
        $('#selectedCarName').text(carName);
        $('#selectedCarRate').text('$' + parseFloat(dailyRate).toFixed(2) + '/day');

        const today = new Date().toISOString().split('T')[0];
        $('#start_date').attr('min', today);
        $('#end_date').attr('min', today);

        $('#numDays').text('0');
        $('#totalPrice').text('0.00');

        loadUserBalance();

        const modal = new bootstrap.Modal(document.getElementById('bookingModal'));
        modal.show();
    }

    function loadUserBalance() {
        const token = localStorage.getItem('jwt_token');
        const userData = window.getUserFromToken();

        if (!userData) return;

        $.ajax({
            url: `${API_BASE_URL}/users/${userData.user_id}`,
            method: 'GET',
            headers: {
                'Authentication': token
            },
            success: function(response) {
                if (response.success && response.data) {
                    const balance = parseFloat(response.data.balance).toFixed(2);
                    $('#userBalanceInModal').text(balance);
                }
            },
            error: function(xhr) {
                console.error('Error loading balance:', xhr);
            }
        });
    }

    $(document).on('change', '#start_date, #end_date', function() {
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();
        const dailyRate = parseFloat($('#booking_daily_rate').val());

        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const diffTime = end - start;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (diffDays > 0) {
                const totalPrice = diffDays * dailyRate;
                $('#numDays').text(diffDays);
                $('#totalPrice').text(totalPrice.toFixed(2));

                $('#end_date').attr('min', startDate);
            } else {
                $('#numDays').text('0');
                $('#totalPrice').text('0.00');
                if (diffDays < 0) {
                    showBookingError('End date must be after start date');
                }
            }
        }
    });

    $(document).on('click', '#confirmBookingBtn', function() {
        const carId = $('#booking_car_id').val();
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();
        const dailyRate = parseFloat($('#booking_daily_rate').val());
        const numDays = parseInt($('#numDays').text());
        const totalPrice = parseFloat($('#totalPrice').text());
        const userBalance = parseFloat($('#userBalanceInModal').text());

        if (!startDate || !endDate) {
            showBookingError('Please select both start and end dates');
            return;
        }

        if (numDays <= 0) {
            showBookingError('Invalid date range');
            return;
        }

        if (totalPrice > userBalance) {
            showBookingError(`Insufficient balance! You need $${totalPrice.toFixed(2)} but have $${userBalance.toFixed(2)}. Please add credits first.`);
            return;
        }

        $('#confirmBookingBtn').prop('disabled', true).text('Processing...');

        const token = localStorage.getItem('jwt_token');
        const userData = window.getUserFromToken();

        $.ajax({
            url: `${API_BASE_URL}/bookings`,
            method: 'POST',
            headers: {
                'Authentication': token,
                'Content-Type': 'application/json'
            },
            data: JSON.stringify({
                user_id: userData.user_id,
                car_id: parseInt(carId),
                start_date: startDate,
                end_date: endDate,
                total_price: totalPrice,
                status: 'pending'
            }),
            success: function(response) {
                console.log('Booking response:', response);
                $('#confirmBookingBtn').prop('disabled', false).text('Confirm Booking');

                if (response.success) {
                    showBookingSuccess('Booking created successfully! Redirecting to your bookings...');

                    if (typeof window.updateNavbarBalance === 'function') {
                        window.updateNavbarBalance();
                    }

                    setTimeout(function() {
                        bootstrap.Modal.getInstance(document.getElementById('bookingModal')).hide();
                        window.location.hash = '#bookings';
                    }, 2000);
                } else {
                    showBookingError(response.message || 'Failed to create booking');
                }
            },
            error: function(xhr) {
                console.error('Booking error:', xhr);
                $('#confirmBookingBtn').prop('disabled', false).text('Confirm Booking');
                const errorMessage = xhr.responseText || 'Failed to create booking. Please try again.';
                showBookingError(errorMessage);
            }
        });
    });

    function showBookingError(message) {
        $('#bookingError').text(message).removeClass('d-none');
        $('#bookingSuccess').addClass('d-none');
    }

    function showBookingSuccess(message) {
        $('#bookingSuccess').text(message).removeClass('d-none');
        $('#bookingError').addClass('d-none');
    }

    function showLoading() {
        $('#carsLoading').show();
        $('#carsError').addClass('d-none');
        $('#carsGrid').empty();
    }

    function hideLoading() {
        $('#carsLoading').hide();
    }

    function showError(message) {
        $('#carsError').text(message).removeClass('d-none');
        $('#carsLoading').hide();
    }

})();