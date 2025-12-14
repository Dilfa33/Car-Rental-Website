// Cars page functionality
(function() {
    'use strict';

    // Load cars when page is accessed
    window.loadCarsPage = function() {
        console.log('Cars page loaded');
        loadCars();
    };

    // Load cars from API
    function loadCars() {
        const token = localStorage.getItem('jwt_token');

        if (!token) {
            showError('Please login to view cars');
            return;
        }

        showLoading();

        $.ajax({
            url: 'http://localhost/Car-Rental-Website/backend/rest/cars',
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

    // Display cars in grid
    function displayCars(cars) {
        const grid = $('#carsGrid');
        grid.empty();

        cars.forEach(car => {
            const carCard = createCarCard(car);
            grid.append(carCard);
        });
    }

    // Create individual car card HTML
    function createCarCard(car) {
        const isAvailable = car.availability_status === 'available';
        const statusBadge = isAvailable ? 'bg-success' : 'bg-danger';

        return `
            <div class="col-md-6 col-lg-4">
                <div class="card car-card h-100 shadow-sm">
                    <img src="frontend/assets/imgs/${car.image_url || 'default.jpg'}" 
                         class="card-img-top" 
                         alt="${car.brand} ${car.model}"
                         onerror="this.src='frontend/assets/imgs/default.jpg'">
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
                            ${car.availability_status}
                        </span>
                        <button class="btn btn-primary w-100 book-car-btn" 
                                data-car-id="${car.car_id}"
                                data-car-name="${car.brand} ${car.model}"
                                data-daily-rate="${car.daily_rate}"
                                ${!isAvailable ? 'disabled' : ''}>
                            ${isAvailable ? 'Book Now' : 'Not Available'}
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    // Handle book car button click
    $(document).on('click', '.book-car-btn', function() {
        const carId = $(this).data('car-id');
        const carName = $(this).data('car-name');
        const dailyRate = $(this).data('daily-rate');

        console.log('Book car clicked:', carId, carName, dailyRate);

        // TODO: Implement booking modal or redirect to booking page
        alert(`Booking ${carName} at $${dailyRate}/day\nFeature coming soon!`);
    });

    // UI Helper Functions
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