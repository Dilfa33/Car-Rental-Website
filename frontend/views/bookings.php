<?php
require_once __DIR__ . '/../../backend/rest/dao/BookingDao.php';

// Assuming user is logged in and user_id is in session
session_start();
$user_id = $_SESSION['user_id'] ?? 1; // Default to 1 for now

$bookingDao = new BookingDao();
$bookings = $bookingDao->getByUserId($user_id);
?>

<!-- Bookings View -->
<div class="container my-5">
    <h2 class="text-center mb-4">My Bookings</h2>

    <div class="table-responsive shadow-sm">
        <table class="table table-striped align-middle">
            <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Car</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($bookings as $booking): ?>
                <tr>
                    <td><?php echo $booking['booking_id']; ?></td>
                    <td>Car ID: <?php echo $booking['car_id']; ?></td>
                    <td><?php echo date('Y-m-d', strtotime($booking['start_date'])); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($booking['end_date'])); ?></td>
                    <td>$<?php echo number_format($booking['total_price'], 2); ?></td>
                    <td><span class="badge bg-<?php echo $booking['status'] == 'confirmed' ? 'success' : ($booking['status'] == 'pending' ? 'warning' : 'secondary'); ?>"><?php echo ucfirst($booking['status']); ?></span></td>
                    <td>
                        <button class="btn btn-sm btn-warning me-1">Extend</button>
                        <button class="btn btn-sm btn-danger">Cancel</button>
                        <button class="btn btn-sm btn-dark">Add a review</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if(empty($bookings)): ?>
                <tr>
                    <td colspan="7" class="text-center">No bookings found</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>