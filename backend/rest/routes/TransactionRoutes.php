<?php

/**
 * @OA\Get(
 *     path="/transactions",
 *     tags={"transactions"},
 *     summary="Get all transactions",
 *     @OA\Response(
 *         response=200,
 *         description="List of all transactions in the database"
 *     )
 * )
 */
Flight::route('GET /transactions', function() {
    try {
        $transactions = Flight::transactionService()->get_transactions();
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
        $transaction = Flight::transactionService()->get_transaction_by_id($id);
        Flight::json(['success' => true, 'data' => $transaction]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Post(
 *     path="/transactions",
 *     tags={"transactions"},
 *     summary="Add a new transaction",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id","amount","type"},
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="amount", type="number", example=100.00),
 *             @OA\Property(property="type", type="string", example="payment"),
 *             @OA\Property(property="description", type="string", example="Payment for booking #2")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Transaction successfully added"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation failed"
 *     )
 * )
 */
Flight::route('POST /transactions', function(){
    $data = Flight::request()->data->getData();
    try {
        Flight::json(Flight::transactionService()->add_transaction($data));
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Put(
 *     path="/transactions/{id}",
 *     tags={"transactions"},
 *     summary="Update a transaction by ID",
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
 *     )
 * )
 */
Flight::route('PUT /transactions/@id', function($id){
    $data = Flight::request()->data->getData();
    try {
        Flight::json(Flight::transactionService()->update_transaction($id, $data));
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Delete(
 *     path="/transactions/{id}",
 *     tags={"transactions"},
 *     summary="Delete a transaction by ID",
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
 *     )
 * )
 */
Flight::route('DELETE /transactions/@id', function($id){
    try {
        Flight::json(Flight::transactionService()->delete_transaction($id));
        Flight::json(['success' => true, 'message' => 'Transaction deleted successfully']);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});
?>
