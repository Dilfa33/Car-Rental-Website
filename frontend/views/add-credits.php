<?php
require_once __DIR__ . '/../../backend/rest/dao/UserDao.php';
require_once __DIR__ . '/../../backend/rest/dao/TransactionDao.php';

session_start();
$user_id = $_SESSION['user_id'] ?? 1; // Default to 1 for now

$userDao = new UserDao();
$transactionDao = new TransactionDao();

$user = $userDao->getById($user_id);
$transactions = $transactionDao->getByUserId($user_id);

// If user not found, set default values
if (!$user) {
    $user = ['balance' => 0];
}
?>

<!-- Add Credits View -->
<div class="container my-5">
    <h2 class="text-center mb-4">Add Credits</h2>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">Your Current Balance: <span class="text-success fw-bold">$<?php echo number_format($user['balance'], 2); ?></span></h5>

                    <form id="topUpForm">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Enter Amount</label>
                            <input type="number" id="amount" class="form-control" placeholder="e.g. 50" min="5" required>
                        </div>

                        <div class="mb-3">
                            <label for="method" class="form-label">Payment Method</label>
                            <select id="method" class="form-select" required>
                                <option value="">Select a method</option>
                                <option value="card">Credit/Debit Card</option>
                                <option value="paypal">PayPal</option>
                                <option value="crypto">Crypto</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Add Credits</button>
                    </form>

                    <div class="text-center mt-4">
                        <p class="text-muted mb-1">Recent Transactions:</p>
                        <ul class="list-unstyled small">
                            <?php foreach(array_slice($transactions, 0, 5) as $transaction): ?>
                                <li>
                                    <?php echo $transaction['amount'] > 0 ? '+' : ''; ?>
                                    $<?php echo number_format($transaction['amount'], 2); ?>
                                    (<?php echo ucfirst($transaction['type']); ?>)
                                    — <?php echo date('M d, Y', strtotime($transaction['created_at'])); ?>
                                </li>
                            <?php endforeach; ?>
                            <?php if(empty($transactions)): ?>
                                <li>No transactions yet</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $("#topUpForm").on("submit", function(e){
        e.preventDefault();
        const amount = $("#amount").val();
        alert(`$${amount} successfully added to your balance!`);
    });
</script>