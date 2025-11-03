<?php
require_once __DIR__ . '/../../backend/rest/dao/CarDao.php';
require_once __DIR__ . '/../../backend/rest/dao/UserDao.php';
require_once __DIR__ . '/../../backend/rest/dao/BookingDao.php';
require_once __DIR__ . '/../../backend/rest/dao/CarReviewDao.php';

$carDao = new CarDao();
$userDao = new UserDao();
$bookingDao = new BookingDao();
$reviewDao = new CarReviewDao();

$cars = $carDao->getAll();
$users = $userDao->getAll();
$bookings = $bookingDao->getAll();
$reviews = $reviewDao->getAll();
?>

<!-- Admin Panel View -->
<div class="admin-container min-vh-100 py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold">Admin Dashboard</h2>

        <!-- Section: Cars -->
        <section class="admin-section mb-5">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white fw-semibold d-flex justify-content-between align-items-center">
                    <span>Cars</span>
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addCarModal">+ Add New Car</button>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered align-middle text-center">
                        <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Brand</th>
                            <th>Model</th>
                            <th>Year</th>
                            <th>Transmission</th>
                            <th>Fuel Type</th>
                            <th>Daily Rate</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($cars as $car): ?>
                            <tr>
                                <td><?php echo $car['car_id']; ?></td>
                                <td><?php echo htmlspecialchars($car['brand']); ?></td>
                                <td><?php echo htmlspecialchars($car['model']); ?></td>
                                <td><?php echo $car['year']; ?></td>
                                <td><?php echo ucfirst($car['transmission']); ?></td>
                                <td><?php echo ucfirst($car['fuel_type']); ?></td>
                                <td>$<?php echo number_format($car['daily_rate'], 2); ?></td>
                                <td><span class="badge bg-<?php echo $car['availability_status'] == 'available' ? 'success' : ($car['availability_status'] == 'rented' ? 'warning' : 'secondary'); ?>"><?php echo ucfirst($car['availability_status']); ?></span></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="editCar(<?php echo htmlspecialchars(json_encode($car)); ?>)">Edit</button>
                                    <button class="btn btn-sm btn-danger" onclick="confirmDeleteCar(<?php echo $car['car_id']; ?>, '<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>')">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Section: Users -->
        <section class="admin-section mb-5">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white fw-semibold d-flex justify-content-between align-items-center">
                    <span>Users</span>
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">+ Add New User</button>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered align-middle text-center">
                        <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($users as $user): ?>
                            <tr>
                                <td><?php echo $user['user_id']; ?></td>
                                <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                                <td><span class="badge bg-<?php echo $user['role'] == 'admin' ? 'danger' : 'primary'; ?>"><?php echo ucfirst($user['role']); ?></span></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)">Edit</button>
                                    <button class="btn btn-sm btn-danger" onclick="confirmDeleteUser(<?php echo $user['user_id']; ?>, '<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>')">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Section: Bookings -->
        <section class="admin-section mb-5">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white fw-semibold d-flex justify-content-between align-items-center">
                    <span>Bookings</span>
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addBookingModal">+ Add New Booking</button>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered align-middle text-center">
                        <thead class="table-dark">
                        <tr>
                            <th>Booking ID</th>
                            <th>User ID</th>
                            <th>Car ID</th>
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
                                <td><?php echo $booking['user_id']; ?></td>
                                <td><?php echo $booking['car_id']; ?></td>
                                <td><?php echo date('Y-m-d', strtotime($booking['start_date'])); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($booking['end_date'])); ?></td>
                                <td>$<?php echo number_format($booking['total_price'], 2); ?></td>
                                <td><span class="badge bg-<?php echo $booking['status'] == 'confirmed' ? 'success' : ($booking['status'] == 'pending' ? 'warning' : 'secondary'); ?>"><?php echo ucfirst($booking['status']); ?></span></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="editBooking(<?php echo htmlspecialchars(json_encode($booking)); ?>)">Edit</button>
                                    <button class="btn btn-sm btn-danger" onclick="confirmDeleteBooking(<?php echo $booking['booking_id']; ?>)">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Section: Manage User Credits -->
        <section class="admin-section mb-5">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white fw-semibold">💰 Manage User Credits</div>
                <div class="card-body">
                    <!-- Add Credits Form -->
                    <form id="addCreditsForm" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="userId" class="form-label">User ID</label>
                            <input type="number" id="userId" class="form-control" placeholder="e.g., 1" required>
                        </div>
                        <div class="col-md-4">
                            <label for="creditAmount" class="form-label">Amount ($)</label>
                            <input type="number" id="creditAmount" class="form-control" placeholder="e.g., 50" required>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-success w-100">Add Credits</button>
                        </div>
                    </form>

                    <!-- User Balances Table -->
                    <table class="table table-striped table-bordered align-middle text-center" id="userCreditsTable">
                        <thead class="table-dark">
                        <tr>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Balance ($)</th>
                            <th>Last Updated</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($users as $user): ?>
                            <tr>
                                <td><?php echo $user['user_id']; ?></td>
                                <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>$<?php echo number_format($user['balance'], 2); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($user['updated_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Section: Car Reviews -->
        <section class="admin-section mb-5">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white fw-semibold d-flex justify-content-between align-items-center">
                    <span>Car Reviews</span>
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addReviewModal">+ Add New Review</button>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered align-middle text-center">
                        <thead class="table-dark">
                        <tr>
                            <th>Review ID</th>
                            <th>Car ID</th>
                            <th>User ID</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($reviews as $review): ?>
                            <tr>
                                <td><?php echo $review['review_id']; ?></td>
                                <td><?php echo $review['car_id']; ?></td>
                                <td><?php echo $review['user_id']; ?></td>
                                <td><?php echo $review['rating']; ?>/5</td>
                                <td><?php echo htmlspecialchars($review['comment']); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($review['created_at'])); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="editReview(<?php echo htmlspecialchars(json_encode($review)); ?>)">Edit</button>
                                    <button class="btn btn-sm btn-danger" onclick="confirmDeleteReview(<?php echo $review['review_id']; ?>)">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Add Car Modal -->
<div class="modal fade" id="addCarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Car</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCarForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Brand</label>
                        <input type="text" class="form-control" name="brand" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Model</label>
                        <input type="text" class="form-control" name="model" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Year</label>
                        <input type="number" class="form-control" name="year" required min="1900" max="2030">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Transmission</label>
                        <select class="form-select" name="transmission" required>
                            <option value="manual">Manual</option>
                            <option value="automatic">Automatic</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fuel Type</label>
                        <select class="form-select" name="fuel_type" required>
                            <option value="petrol">Petrol</option>
                            <option value="diesel">Diesel</option>
                            <option value="electric">Electric</option>
                            <option value="hybrid">Hybrid</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Daily Rate ($)</label>
                        <input type="number" class="form-control" name="daily_rate" required min="0" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image Filename (e.g., car.jpg)</label>
                        <input type="text" class="form-control" name="image_url" placeholder="car.jpg">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mileage</label>
                        <input type="number" class="form-control" name="mileage" value="0" min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Car</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Car Modal -->
<div class="modal fade" id="editCarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Car</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCarForm">
                <input type="hidden" name="car_id" id="edit_car_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Brand</label>
                        <input type="text" class="form-control" name="brand" id="edit_brand" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Model</label>
                        <input type="text" class="form-control" name="model" id="edit_model" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Year</label>
                        <input type="number" class="form-control" name="year" id="edit_year" required min="1900" max="2030">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Transmission</label>
                        <select class="form-select" name="transmission" id="edit_transmission" required>
                            <option value="manual">Manual</option>
                            <option value="automatic">Automatic</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fuel Type</label>
                        <select class="form-select" name="fuel_type" id="edit_fuel_type" required>
                            <option value="petrol">Petrol</option>
                            <option value="diesel">Diesel</option>
                            <option value="electric">Electric</option>
                            <option value="hybrid">Hybrid</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Daily Rate ($)</label>
                        <input type="number" class="form-control" name="daily_rate" id="edit_daily_rate" required min="0" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="availability_status" id="edit_availability_status" required>
                            <option value="available">Available</option>
                            <option value="rented">Rented</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image Filename</label>
                        <input type="text" class="form-control" name="image_url" id="edit_image_url" placeholder="car.jpg">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mileage</label>
                        <input type="number" class="form-control" name="mileage" id="edit_mileage" min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Car Confirmation Modal -->
<div class="modal fade" id="deleteCarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="delete_car_name"></strong>?</p>
                <p class="text-danger">This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteCarBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addUserForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control" name="first_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="last_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" name="phone">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" name="role" required>
                            <option value="customer">Customer</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm">
                <input type="hidden" name="user_id" id="edit_user_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control" name="first_name" id="edit_first_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="last_name" id="edit_last_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="edit_email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" name="phone" id="edit_phone">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" name="role" id="edit_role" required>
                            <option value="customer">Customer</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete User Confirmation Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete user <strong id="delete_user_name"></strong>?</p>
                <p class="text-danger">This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteUserBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Review Modal -->
<div class="modal fade" id="addReviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addReviewForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Car ID</label>
                        <input type="number" class="form-control" name="car_id" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">User ID</label>
                        <input type="number" class="form-control" name="user_id" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rating (1-5)</label>
                        <input type="number" class="form-control" name="rating" required min="1" max="5">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Comment</label>
                        <textarea class="form-control" name="comment" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Review Modal -->
<div class="modal fade" id="editReviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editReviewForm">
                <input type="hidden" name="review_id" id="edit_review_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Car ID</label>
                        <input type="number" class="form-control" name="car_id" id="edit_review_car_id" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">User ID</label>
                        <input type="number" class="form-control" name="user_id" id="edit_review_user_id" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rating (1-5)</label>
                        <input type="number" class="form-control" name="rating" id="edit_rating" required min="1" max="5">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Comment</label>
                        <textarea class="form-control" name="comment" id="edit_comment" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Review Confirmation Modal -->
<div class="modal fade" id="deleteReviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this review?</p>
                <p class="text-danger">This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteReviewBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Booking Modal -->
<div class="modal fade" id="addBookingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addBookingForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">User ID</label>
                        <input type="number" class="form-control" name="user_id" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Car ID</label>
                        <input type="number" class="form-control" name="car_id" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" name="start_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control" name="end_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total Price ($)</label>
                        <input type="number" class="form-control" name="total_price" required min="0" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Booking Modal -->
<div class="modal fade" id="editBookingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editBookingForm">
                <input type="hidden" name="booking_id" id="edit_booking_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">User ID</label>
                        <input type="number" class="form-control" name="user_id" id="edit_booking_user_id" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Car ID</label>
                        <input type="number" class="form-control" name="car_id" id="edit_booking_car_id" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" name="start_date" id="edit_start_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control" name="end_date" id="edit_end_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total Price ($)</label>
                        <input type="number" class="form-control" name="total_price" id="edit_total_price" required min="0" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" id="edit_booking_status" required>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Booking Confirmation Modal -->
<div class="modal fade" id="deleteBookingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this booking?</p>
                <p class="text-danger">This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBookingBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
    // ========== CAR CRUD OPERATIONS ==========

    // Add Car Form Submit (POST)
    document.getElementById('addCarForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);

        fetch('backend/rest/car_actions.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(result => {
                if(result.success) {
                    alert('Car added successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => {
                alert('Error adding car');
                console.error(error);
            });
    });

    // Edit Car - Populate Modal
    function editCar(car) {
        document.getElementById('edit_car_id').value = car.car_id;
        document.getElementById('edit_brand').value = car.brand;
        document.getElementById('edit_model').value = car.model;
        document.getElementById('edit_year').value = car.year;
        document.getElementById('edit_transmission').value = car.transmission;
        document.getElementById('edit_fuel_type').value = car.fuel_type;
        document.getElementById('edit_daily_rate').value = car.daily_rate;
        document.getElementById('edit_availability_status').value = car.availability_status;
        document.getElementById('edit_image_url').value = car.image_url || '';
        document.getElementById('edit_mileage').value = car.mileage;

        new bootstrap.Modal(document.getElementById('editCarModal')).show();
    }

    // Edit Car Form Submit (PUT)
    document.getElementById('editCarForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        const carId = data.car_id;
        delete data.car_id;

        fetch('backend/rest/car_actions.php?car_id=' + carId, {
            method: 'PUT',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(result => {
                if(result.success) {
                    alert('Car updated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => {
                alert('Error updating car');
                console.error(error);
            });
    });

    // Delete Car - Show Confirmation
    let deleteCarId = null;
    function confirmDeleteCar(carId, carName) {
        deleteCarId = carId;
        document.getElementById('delete_car_name').textContent = carName;
        new bootstrap.Modal(document.getElementById('deleteCarModal')).show();
    }

    // Delete Car - Confirmed (DELETE)
    document.getElementById('confirmDeleteCarBtn').addEventListener('click', function() {
        if(!deleteCarId) return;

        fetch('backend/rest/car_actions.php?car_id=' + deleteCarId, {
            method: 'DELETE'
        })
            .then(response => response.json())
            .then(result => {
                if(result.success) {
                    alert('Car deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => {
                alert('Error deleting car');
                console.error(error);
            });
    });

    // ========== USER CRUD OPERATIONS ==========

    // Add User Form Submit (POST)
    document.getElementById('addUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);

        fetch('backend/rest/user_actions.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(result => {
                if(result.success) {
                    alert('User added successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => {
                alert('Error adding user');
                console.error(error);
            });
    });

    // Edit User - Populate Modal
    function editUser(user) {
        document.getElementById('edit_user_id').value = user.user_id;
        document.getElementById('edit_first_name').value = user.first_name;
        document.getElementById('edit_last_name').value = user.last_name;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_phone').value = user.phone || '';
        document.getElementById('edit_role').value = user.role;

        new bootstrap.Modal(document.getElementById('editUserModal')).show();
    }

    // Edit User Form Submit (PUT)
    document.getElementById('editUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        const userId = data.user_id;
        delete data.user_id;

        fetch('backend/rest/user_actions.php?user_id=' + userId, {
            method: 'PUT',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(result => {
                if(result.success) {
                    alert('User updated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => {
                alert('Error updating user');
                console.error(error);
            });
    });

    // Delete User - Show Confirmation
    let deleteUserId = null;
    function confirmDeleteUser(userId, userName) {
        deleteUserId = userId;
        document.getElementById('delete_user_name').textContent = userName;
        new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
    }

    // Delete User - Confirmed (DELETE)
    document.getElementById('confirmDeleteUserBtn').addEventListener('click', function() {
        if(!deleteUserId) return;

        fetch('backend/rest/user_actions.php?user_id=' + deleteUserId, {
            method: 'DELETE'
        })
            .then(response => response.json())
            .then(result => {
                if(result.success) {
                    alert('User deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => {
                alert('Error deleting user');
                console.error(error);
            });
    });

    // ========== REVIEW CRUD OPERATIONS ==========

    // Add Review Form Submit (POST)
    document.getElementById('addReviewForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);

        fetch('backend/rest/review_actions.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(result => {
                if(result.success) {
                    alert('Review added successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => {
                alert('Error adding review');
                console.error(error);
            });
    });

    // Edit Review - Populate Modal
    function editReview(review) {
        document.getElementById('edit_review_id').value = review.review_id;
        document.getElementById('edit_review_car_id').value = review.car_id;
        document.getElementById('edit_review_user_id').value = review.user_id;
        document.getElementById('edit_rating').value = review.rating;
        document.getElementById('edit_comment').value = review.comment;

        new bootstrap.Modal(document.getElementById('editReviewModal')).show();
    }

    // Edit Review Form Submit (PUT)
    document.getElementById('editReviewForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        const reviewId = data.review_id;
        delete data.review_id;

        fetch('backend/rest/review_actions.php?review_id=' + reviewId, {
            method: 'PUT',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(result => {
                if(result.success) {
                    alert('Review updated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => {
                alert('Error updating review');
                console.error(error);
            });
    });

    // Delete Review - Show Confirmation
    let deleteReviewId = null;
    function confirmDeleteReview(reviewId) {
        deleteReviewId = reviewId;
        new bootstrap.Modal(document.getElementById('deleteReviewModal')).show();
    }

    // Delete Review - Confirmed (DELETE)
    document.getElementById('confirmDeleteReviewBtn').addEventListener('click', function() {
        if(!deleteReviewId) return;

        fetch('backend/rest/review_actions.php?review_id=' + deleteReviewId, {
            method: 'DELETE'
        })
            .then(response => response.json())
            .then(result => {
                if(result.success) {
                    alert('Review deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => {
                alert('Error deleting review');
                console.error(error);
            });
    });

    // ========== ADD CREDITS FUNCTIONALITY ==========

    // Add Credits Form Submit
    document.getElementById('addCreditsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const userId = document.getElementById('userId').value;
        const amount = document.getElementById('creditAmount').value;

        fetch('backend/rest/user_actions.php?user_id=' + userId, {
            method: 'PUT',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ balance: amount })
        })
            .then(response => response.json())
            .then(result => {
                if(result.success) {
                    alert('Credits updated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => {
                alert('Error updating credits');
                console.error(error);
            });
    });

    // ========== BOOKING CRUD OPERATIONS ==========

    // Add Booking Form Submit (POST)
    document.getElementById('addBookingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);

        fetch('backend/rest/booking_actions.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(result => {
                if(result.success) {
                    alert('Booking added successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => {
                alert('Error adding booking');
                console.error(error);
            });
    });

    // Edit Booking - Populate Modal
    function editBooking(booking) {
        document.getElementById('edit_booking_id').value = booking.booking_id;
        document.getElementById('edit_booking_user_id').value = booking.user_id;
        document.getElementById('edit_booking_car_id').value = booking.car_id;
        document.getElementById('edit_start_date').value = booking.start_date;
        document.getElementById('edit_end_date').value = booking.end_date;
        document.getElementById('edit_total_price').value = booking.total_price;
        document.getElementById('edit_booking_status').value = booking.status;

        new bootstrap.Modal(document.getElementById('editBookingModal')).show();
    }

    // Edit Booking Form Submit (PUT)
    document.getElementById('editBookingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        const bookingId = data.booking_id;
        delete data.booking_id;

        fetch('backend/rest/booking_actions.php?booking_id=' + bookingId, {
            method: 'PUT',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(result => {
                if(result.success) {
                    alert('Booking updated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => {
                alert('Error updating booking');
                console.error(error);
            });
    });

    // Delete Booking - Show Confirmation
    let deleteBookingId = null;
    function confirmDeleteBooking(bookingId) {
        deleteBookingId = bookingId;
        new bootstrap.Modal(document.getElementById('deleteBookingModal')).show();
    }

    // Delete Booking - Confirmed (DELETE)
    document.getElementById('confirmDeleteBookingBtn').addEventListener('click', function() {
        if(!deleteBookingId) return;

        fetch('backend/rest/booking_actions.php?booking_id=' + deleteBookingId, {
            method: 'DELETE'
        })
            .then(response => response.json())
            .then(result => {
                if(result.success) {
                    alert('Booking deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => {
                alert('Error deleting booking');
                console.error(error);
            });
    });
</script>