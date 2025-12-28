// Bookings page functionality
(function() {
    'use strict';

    // API Base URL
    const API_BASE_URL = 'https://jellyfish-app-659ha.ondigitalocean.app';

    let currentBookingId = null;
    let currentEndDate = null;

    window.loadBookingsPage = function() {
        console.log('Bookings page loaded');
        loadBookings();
    };

    function loadBookings() {
        console.log('=== loadBookings function called ===');

        const token = localStorage.getItem('jwt_token');
        const userData = window.getUserFromToken();

        if (!token || !userData) {
            showError('Please login to view bookings');
            return;
        }

        console.log('Loading bookings for user:', userData.user_id);
        showLoading();

        $.ajax({
            url: `${API_BASE_URL}/bookings/user/${userData.user_id}`,
            method: 'GET',
            headers: {
                'Authentication': token
            },
            success: function(response) {
                console.log('Bookings loaded:', response);
                hideLoading();

                if (response.success && response.data && response.data.length > 0) {
                    displayBookings(response.data);
                    console.log(response.data);
                } else {
                    showNoBookings();
                }
            },
            error: function(xhr) {
                console.error('Error loading bookings:', xhr);
                hideLoading();
                showError('Failed to load bookings: ' + (xhr.responseText || 'Unknown error'));
            }
        });
    }

    function displayBookings(bookings) {
        const tbody = $('#bookingsTableBody');
        tbody.empty();

        bookings.forEach((booking, index) => {
            const row = createBookingRow(booking, index + 1);
            tbody.append(row);
        });

        $('#bookingsTable').removeClass('d-none');
        $('#noBookings').addClass('d-none');
    }

    function createBookingRow(booking, rowNumber) {
        const statusBadge = getStatusBadge(booking.status);
        const isActive = booking.status === 'confirmed' || booking.status === 'pending';
        const isCompleted = booking.status === 'completed';
        const isCancelled = booking.status === 'cancelled';

        return `
            <tr>
                <td>${rowNumber}</td>
                <td><strong>Car ID: ${booking.car_id}</strong></td>
                <td>${formatDate(booking.start_date)}</td>
                <td>${formatDate(booking.end_date)}</td>
                <td><strong>$${parseFloat(booking.total_price).toFixed(2)}</strong></td>
                <td><span class="badge ${statusBadge}">${booking.status}</span></td>
                <td>
                    ${isActive ? `
                        <button class="btn btn-sm btn-warning me-1 extend-btn" 
                                data-booking-id="${booking.booking_id}"
                                data-end-date="${booking.end_date}">
                            Extend
                        </button>
                        <button class="btn btn-sm btn-danger cancel-btn" 
                                data-booking-id="${booking.booking_id}">
                            Cancel
                        </button>
                    ` : ''}
                    ${isCompleted ? `
                        <button class="btn btn-sm btn-dark review-btn" 
                                data-booking-id="${booking.booking_id}">
                            Add Review
                        </button>
                    ` : ''}
                    ${isCancelled ? `
                        <span class="text-muted">No actions</span>
                    ` : ''}
                </td>
            </tr>
        `;
    }

    function getStatusBadge(status) {
        switch(status) {
            case 'confirmed': return 'bg-success';
            case 'pending': return 'bg-warning';
            case 'completed': return 'bg-secondary';
            case 'cancelled': return 'bg-danger';
            default: return 'bg-info';
        }
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    $(document).on('click', '.extend-btn', function() {
        currentBookingId = $(this).data('booking-id');
        currentEndDate = $(this).data('end-date');

        console.log('Extend booking clicked:', currentBookingId);

        $('#currentEndDate').text(formatDate(currentEndDate));

        const minDate = new Date(currentEndDate);
        minDate.setDate(minDate.getDate() + 1);
        $('#newEndDate').attr('min', minDate.toISOString().split('T')[0]);
        $('#newEndDate').val('');

        const modal = new bootstrap.Modal(document.getElementById('extendModal'));
        modal.show();
    });

    $(document).on('click', '#confirmExtendBtn', function() {
        const newEndDate = $('#newEndDate').val();

        if (!newEndDate) {
            alert('Please select a new end date');
            return;
        }

        console.log('Extending booking to:', newEndDate);

        const token = localStorage.getItem('jwt_token');

        $.ajax({
            url: `${API_BASE_URL}/bookings/${currentBookingId}/extend`,
            method: 'PUT',
            headers: {
                'Authentication': token,
                'Content-Type': 'application/json'
            },
            data: JSON.stringify({
                new_end_date: newEndDate
            }),
            success: function(response) {
                console.log('Extend response:', response);

                if (response.success) {
                    alert('Booking extended successfully!');
                    bootstrap.Modal.getInstance(document.getElementById('extendModal')).hide();
                    loadBookings();
                } else {
                    alert('Failed to extend booking: ' + response.message);
                }
            },
            error: function(xhr) {
                console.error('Extend error:', xhr);
                alert('Failed to extend booking: ' + (xhr.responseText || 'Unknown error'));
            }
        });
    });

    $(document).on('click', '.cancel-btn', function() {
        const bookingId = $(this).data('booking-id');

        console.log('Cancel booking clicked:', bookingId);

        if (!confirm('Are you sure you want to cancel this booking?')) {
            return;
        }

        const token = localStorage.getItem('jwt_token');

        $.ajax({
            url: `${API_BASE_URL}/bookings/${bookingId}/cancel`,
            method: 'PUT',
            headers: {
                'Authentication': token
            },
            success: function(response) {
                console.log('Cancel response:', response);

                if (response.success) {
                    alert('Booking cancelled successfully');
                    loadBookings();
                } else {
                    alert('Failed to cancel booking: ' + response.message);
                }
            },
            error: function(xhr) {
                console.error('Cancel error:', xhr);
                alert('Failed to cancel booking: ' + (xhr.responseText || 'Unknown error'));
            }
        });
    });

    $(document).on('click', '.review-btn', function() {
        currentBookingId = $(this).data('booking-id');

        console.log('Add review clicked:', currentBookingId);

        $('#reviewRating').val('');
        $('#reviewComment').val('');

        const modal = new bootstrap.Modal(document.getElementById('reviewModal'));
        modal.show();
    });

    $(document).on('click', '#confirmReviewBtn', function() {
        const rating = $('#reviewRating').val();
        const comment = $('#reviewComment').val();

        if (!rating || !comment) {
            alert('Please provide both rating and comment');
            return;
        }

        console.log('Submitting review:', rating, comment);

        const token = localStorage.getItem('jwt_token');

        $.ajax({
            url: `${API_BASE_URL}/bookings/${currentBookingId}/review`,
            method: 'POST',
            headers: {
                'Authentication': token,
                'Content-Type': 'application/json'
            },
            data: JSON.stringify({
                rating: parseInt(rating),
                comment: comment
            }),
            success: function(response) {
                console.log('Review response:', response);

                if (response.success) {
                    alert('Review submitted successfully!');
                    bootstrap.Modal.getInstance(document.getElementById('reviewModal')).hide();
                } else {
                    alert('Failed to submit review: ' + response.message);
                }
            },
            error: function(xhr) {
                console.error('Review error:', xhr);
                alert('Failed to submit review: ' + (xhr.responseText || 'Unknown error'));
            }
        });
    });

    function showLoading() {
        $('#bookingsLoading').show();
        $('#bookingsError').addClass('d-none');
        $('#bookingsTable').addClass('d-none');
        $('#noBookings').addClass('d-none');
    }

    function hideLoading() {
        $('#bookingsLoading').hide();
    }

    function showError(message) {
        $('#bookingsError').text(message).removeClass('d-none');
        $('#bookingsLoading').hide();
    }

    function showNoBookings() {
        $('#noBookings').removeClass('d-none');
        $('#bookingsTable').addClass('d-none');
    }

})();