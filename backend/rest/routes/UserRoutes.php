<?php

/**
 * @OA\Get(
 *     path="/users",
 *     tags={"users"},
 *     summary="Get all users",
 *     @OA\Response(
 *         response=200,
 *         description="List of all users in the database"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Access denied: admin only"
 *     )
 * )
 */
Flight::route('GET /users', function() {
    $user = Flight::get('user');

    if ($user->role !== 'admin') {
        Flight::halt(403, 'Access denied: admin only');
    }

    try {
        $users = Flight::userService()->get_all();
        Flight::json(['success' => true, 'data' => $users]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Get(
 *     path="/users/{id}",
 *     tags={"users"},
 *     summary="Get user by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns the user with the given ID"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found"
 *     )
 * )
 */
Flight::route('GET /users/@id', function($id){
    try {
        $user = Flight::userService()->get_by_id($id);
        Flight::json(['success' => true, 'data' => $user]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Post(
 *     path="/users",
 *     tags={"users"},
 *     summary="Add a new user",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"first_name", "last_name", "email", "password"},
 *             @OA\Property(property="first_name", type="string", example="John"),
 *             @OA\Property(property="last_name", type="string", example="Doe"),
 *             @OA\Property(property="email", type="string", example="john.doe@example.com"),
 *             @OA\Property(property="password", type="string", example="securepassword123"),
 *             @OA\Property(property="phone_number", type="string", example="+1234567890"),
 *             @OA\Property(property="role", type="string", example="customer"),
 *             @OA\Property(property="balance", type="number", example=100.00)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User successfully added"
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
Flight::route('POST /users', function(){
    $user = Flight::get('user');

    if ($user->role !== 'admin') {
        Flight::halt(403, 'Access denied: admin only');
    }

    $data = Flight::request()->data->getData();
    try {
        $result = Flight::userService()->add($data);
        Flight::json(['success' => true, 'data' => $result]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Put(
 *     path="/users/{id}",
 *     tags={"users"},
 *     summary="Update a user by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="first_name", type="string", example="Jane"),
 *             @OA\Property(property="last_name", type="string", example="Smith"),
 *             @OA\Property(property="email", type="string", example="jane.smith@example.com"),
 *             @OA\Property(property="password", type="string", example="newpassword456"),
 *             @OA\Property(property="phone_number", type="string", example="+9876543210"),
 *             @OA\Property(property="role", type="string", example="admin"),
 *             @OA\Property(property="balance", type="number", example=250.00)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User successfully updated"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Access denied: admin only"
 *     )
 * )
 */
Flight::route('PUT /users/@id', function($id){
    $user = Flight::get('user');

    if ($user->role !== 'admin') {
        Flight::halt(403, 'Access denied: admin only');
    }

    $data = Flight::request()->data->getData();
    try {
        $result = Flight::userService()->update($id, $data);
        Flight::json(['success' => true, 'data' => $result]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Delete(
 *     path="/users/{id}",
 *     tags={"users"},
 *     summary="Delete a user by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User successfully deleted"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Access denied: admin only"
 *     )
 * )
 */
Flight::route('DELETE /users/@id', function($id){
    $user = Flight::get('user');

    if ($user->role !== 'admin') {
        Flight::halt(403, 'Access denied: admin only');
    }

    try {
        Flight::userService()->delete($id);
        Flight::json(['success' => true, 'message' => 'User deleted successfully']);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Post(
 *     path="/users/{id}/add-credits",
 *     tags={"users"},
 *     summary="Add credits to user account",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"amount"},
 *             @OA\Property(property="amount", type="number", example=50.00),
 *             @OA\Property(property="payment_method", type="string", example="card")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Credits successfully added"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid amount"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Access denied"
 *     )
 * )
 */
Flight::route('POST /users/@id/add-credits', function($id){
    $user = Flight::get('user');

    // Users can only add credits to their own account
    if ($user->user_id != $id) {
        Flight::halt(403, 'Access denied: You can only add credits to your own account');
    }

    $data = Flight::request()->data->getData();

    try {
        $result = Flight::userService()->add_credits($id, $data);
        Flight::json(['success' => true, 'data' => $result]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});
?>