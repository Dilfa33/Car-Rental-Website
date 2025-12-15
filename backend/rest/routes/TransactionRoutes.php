<?php

/**
 * @OA\Get(
 *     path="/transactions",
 *     tags={"transactions"},
 *     summary="Get all transactions",
 *     @OA\Response(
 *         response=200,
 *         description="List of all transactions in the database"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Access denied: admin only"
 *     )
 * )
 */
Flight::route('GET /transactions', function() {
    $user = Flight::get('user');

    if ($user->role !== 'admin') {
        Flight::halt(403, 'Access denied: admin only');
    }

    try {
        $transactions = Flight::transactionService()->get_all();
        Flight::json(['success' => true, 'data' => $transactions]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Get(
 *     path="/transactions/user/{user_id}",
 *     tags={"transactions"},
 *     summary="Get transactions for specific user",
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns user's transactions"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Access denied: You can only view your own transactions"
 *     )
 * )
 */
Flight::route('GET /transactions/user/@user_id', function($user_id) {
    try {
        $current_user = Flight::get('user');

        // Users can only see their own transactions, admins can see any
        if ($current_user->role !== 'admin' && $current_user->user_id != $user_id) {
            Flight::halt(403, 'Access denied: You can only view your own transactions');
        }

        $transactions = Flight::transactionService()->get_user_transactions($user_id);
        Flight::json(['success' => true, 'data' => $transactions]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Get(
 *     path="/transactions/{id}",
 *     tags={"transactions"},
 *     summary="Get transaction by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Transaction ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns the transaction with the given ID"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Transaction not found"
 *     )
 * )
 */
Flight::route('GET /transactions/@id', function($id){
    try {
        $transaction = Flight::transactionService()->get_by_id($id);
        Flight::json(['success' => true, 'data' => $transaction]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Post(
 *     path="/transactions/add-credits",
 *     tags={"transactions"},
 *     summary="Add credits to user account (top up)",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"amount", "payment_method"},
 *             @OA\Property(property="amount", type="number", example=100.00),
 *             @OA\Property(property="payment_method", type="string", example="card"),
 *             @OA\Property(property="description", type="string", example="Credit card top-up")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Credits added successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation failed"
 *     )
 * )
 */
Flight::route('POST /transactions/add-credits', function(){
    try {
        $current_user = Flight::get('user');
        $data = Flight::request()->data->getData();

        $amount = $data['amount'] ?? 0;
        $payment_method = $data['payment_method'] ?? '';
        $description = $data['description'] ?? null;

        $result = Flight::transactionService()->add_credits(
            $current_user->user_id,
            $amount,
            $payment_method,
            $description
        );

        Flight::json($result);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Post(
 *     path="/transactions",
 *     tags={"transactions"},
 *     summary="Add a new transaction (admin only)",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id", "amount", "type"},
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="amount", type="number", example=100.00),
 *             @OA\Property(property="type", type="string", example="top_up"),
 *             @OA\Property(property="description", type="string", example="Manual credit adjustment")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Transaction successfully added"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation failed"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Access denied: admin only"
 *     )
 * )
 */
Flight::route('POST /transactions', function(){
    $user = Flight::get('user');

    if ($user->role !== 'admin') {
        Flight::halt(403, 'Access denied: admin only');
    }

    $data = Flight::request()->data->getData();
    try {
        $result = Flight::transactionService()->add($data);
        Flight::json(['success' => true, 'data' => $result]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Put(
 *     path="/transactions/{id}",
 *     tags={"transactions"},
 *     summary="Update a transaction by ID (admin only)",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Transaction ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="amount", type="number", example=120.00),
 *             @OA\Property(property="description", type="string", example="Updated description")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Transaction successfully updated"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Transaction not found"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Access denied: admin only"
 *     )
 * )
 */
Flight::route('PUT /transactions/@id', function($id){
    $user = Flight::get('user');

    if ($user->role !== 'admin') {
        Flight::halt(403, 'Access denied: admin only');
    }

    $data = Flight::request()->data->getData();
    try {
        $result = Flight::transactionService()->update($id, $data);
        Flight::json(['success' => true, 'data' => $result]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Delete(
 *     path="/transactions/{id}",
 *     tags={"transactions"},
 *     summary="Delete a transaction by ID (admin only)",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Transaction ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Transaction successfully deleted"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Transaction not found"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Access denied: admin only"
 *     )
 * )
 */
Flight::route('DELETE /transactions/@id', function($id){
    $user = Flight::get('user');

    if ($user->role !== 'admin') {
        Flight::halt(403, 'Access denied: admin only');
    }

    try {
        Flight::transactionService()->delete($id);
        Flight::json(['success' => true, 'message' => 'Transaction deleted successfully']);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});
?>