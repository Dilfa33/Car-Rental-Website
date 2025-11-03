<?php
require_once __DIR__ . '/../../backend/rest/dao/CarDao.php';

$carDao = new CarDao();
$cars = $carDao->getAll();
?>

<!-- Cars View -->
<div class="cars-container min-vh-100 bg-light py-5">
    <div class="container">
        <h2 class="text-center mb-5">Available Cars</h2>

        <div class="row g-4">

            <?php foreach($cars as $car): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card car-card h-100 shadow-sm">
                        <img src="frontend/assets/imgs/<?php echo htmlspecialchars($car['image_url'] ?? 'default.jpg'); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h5>
                            <p class="text-muted mb-2"><?php echo htmlspecialchars($car['transmission']); ?> • <?php echo htmlspecialchars($car['fuel_type']); ?></p>
                            <div class="car-specs d-flex justify-content-between mb-3">
                                <div><strong><?php echo $car['year']; ?></strong></div>
                                <div><strong>A/C</strong></div>
                            </div>
                            <div class="car-price mb-3">$<?php echo number_format($car['daily_rate'], 2); ?><small>/day</small></div>
                            <button class="btn btn-primary w-100">Book Now</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>
</div>