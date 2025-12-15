<?php

/**
 * @OA\Get(
 *     path="/bookings",
 *     tags={"bookings"},
 *     summary="Get all bookings",
 *     @OA\Response(
 *         response=200,
 *         description="List of all bookings in the database"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Access denied: admin only"
 *     )
 * )
 */
Flight::route('GET /bookings', function() {
    $user = Flight::get('user');

    if ($user->role !== 'admin') {
        Flight::halt(403, 'Access denied: admin only');
    }

    try {
        $bookings = Flight::bookingService()->get_all();
        Flight::json(['success' => true, 'data' => $bookings]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Get(
 *     path="/bookings/user/{user_id}",
 *     tags={"bookings"},
 *     summary="Get bookings for specific user",
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns user's bookings"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Access denied: You can only view your own bookings"
 *     )
 * )
 */
Flight::route('GET /bookings/user/@user_id', function($user_id) {
    try {
        $current_user = Flight::get('user');

        // Users can only see their own bookings, admins can see any
        if ($current_user->role !== 'admin' && $current_user->user_id != $user_id) {
            Flight::halt(403, 'Access denied: You can only view your own bookings');
        }

        $bookings = Flight::bookingService()->get_user_bookings($user_id);
        Flight::json(['success' => true, 'data' => $bookings]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Get(
 *     path="/bookings/{id}",
 *     tags={"bookings"},
 *     summary="Get booking by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Booking ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns the booking with the given ID"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Booking not found"
 *     )
 * )
 */
Flight::route('GET /bookings/@id', function($id){
    try {
        $booking = Flight::bookingService()->get_by_id($id);
        Flight::json(['success' => true, 'data' => $booking]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Post(
 *     path="/bookings",
 *     tags={"bookings"},
 *     summary="Add a new booking",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id", "car_id", "start_date", "end_date", "total_price"},
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="car_id", type="integer", example=2),
 *             @OA\Property(property="start_date", type="string", format="date", example="2025-12-01"),
 *             @OA\Property(property="end_date", type="string", format="date", example="2025-12-05"),
 *             @OA\Property(property="total_price", type="number", example=200.00),
 *             @OA\Property(property="status", type="string", example="pending")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Booking successfully added"
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
Flight::route('POST /bookings', function(){
    $user = Flight::get('user');
    $data = Flight::request()->data->getData();

    // Ensure user can only create booking for themselves (unless admin)
    if ($user->role !== 'admin' && $data['user_id'] != $user->user_id) {
        Flight::halt(403, 'Access denied: You can only create bookings for yourself');
    }

    try {
        $result = Flight::bookingService()->create_booking_with_payment($data);
        Flight::json($result);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Put(
 *     path="/bookings/{id}",
 *     tags={"bookings"},
 *     summary="Update a booking by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Booking ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="start_date", type="string", format="date", example="2025-12-01"),
 *             @OA\Property(property="end_date", type="string", format="date", example="2025-12-05"),
 *             @OA\Property(property="total_price", type="number", example=220.00),
 *             @OA\Property(property="status", type="string", example="confirmed")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Booking successfully updated"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Booking not found"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Access denied: admin only"
 *     )
 * )
 */
Flight::route('PUT /bookings/@id', function($id){
    $user = Flight::get('user');

    if ($user->role !== 'admin') {
        Flight::halt(403, 'Access denied: admin only');
    }

    $data = Flight::request()->data->getData();
    try {
        $result = Flight::bookingService()->update($id, $data);
        Flight::json(['success' => true, 'data' => $result]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Delete(
 *     path="/bookings/{id}",
 *     tags={"bookings"},
 *     summary="Delete a booking by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Booking ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Booking successfully deleted"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Booking not found"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Access denied: admin only"
 *     )
 * )
 */
Flight::route('DELETE /bookings/@id', function($id){
    $user = Flight::get('user');

    if ($user->role !== 'admin') {
        Flight::halt(403, 'Access denied: admin only');
    }

    try {
        Flight::bookingService()->delete($id);
        Flight::json(['success' => true, 'message' => 'Booking deleted successfully']);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Put(
 *     path="/bookings/{id}/cancel",
 *     tags={"bookings"},
 *     summary="Cancel a booking",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Booking ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Booking cancelled successfully"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Booking not found"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Access denied: You can only cancel your own bookings"
 *     )
 * )
 */
Flight::route('PUT /bookings/@id/cancel', function($id){
    try {
        $current_user = Flight::get('user');
        $result = Flight::bookingService()->cancel_booking($id, $current_user->user_id);
        Flight::json(['success' => true, 'data' => $result]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Put(
 *     path="/bookings/{id}/extend",
 *     tags={"bookings"},
 *     summary="Extend a booking",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Booking ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"new_end_date"},
 *             @OA\Property(property="new_end_date", type="string", format="date", example="2025-12-10")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Booking extended successfully"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Booking not found"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Access denied: You can only extend your own bookings"
 *     )
 * )
 */
Flight::route('PUT /bookings/@id/extend', function($id){
    try {
        $current_user = Flight::get('user');
        $data = Flight::request()->data->getData();
        $result = Flight::bookingService()->extend_booking($id, $current_user->user_id, $data['new_end_date']);
        Flight::json(['success' => true, 'data' => $result]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});
?>