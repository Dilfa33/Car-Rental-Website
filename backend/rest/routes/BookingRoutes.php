<?php

/**
 * @OA\Get(
 *     path="/bookings",
 *     tags={"bookings"},
 *     summary="Get all bookings",
 *     @OA\Response(
 *         response=200,
 *         description="List of all bookings in the database"
 *     )
 * )
 */
Flight::route('GET /bookings', function() {
    try {
        $bookings = Flight::bookingService()->get_bookings();
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
        $booking = Flight::bookingService()->get_booking_by_id($id);
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
 *             required={"user_id","car_id","start_date","end_date"},
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="car_id", type="integer", example=2),
 *             @OA\Property(property="start_date", type="string", example="2025-12-01 10:00:00"),
 *             @OA\Property(property="end_date", type="string", example="2025-12-05 10:00:00"),
 *             @OA\Property(property="total_price", type="number", example=200.00)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Booking successfully added"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation failed"
 *     )
 * )
 */
Flight::route('POST /bookings', function(){
    $data = Flight::request()->data->getData();
    try {
        Flight::json(Flight::bookingService()->add_booking($data));
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
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="car_id", type="integer", example=2),
 *             @OA\Property(property="start_date", type="string", example="2025-12-01 10:00:00"),
 *             @OA\Property(property="end_date", type="string", example="2025-12-05 10:00:00"),
 *             @OA\Property(property="total_price", type="number", example=220.00)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Booking successfully updated"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Booking not found"
 *     )
 * )
 */
Flight::route('PUT /bookings/@id', function($id){
    $data = Flight::request()->data->getData();
    try {
        Flight::json(Flight::bookingService()->update_booking($id, $data));
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
 *     )
 * )
 */
Flight::route('DELETE /bookings/@id', function($id){
    try {
        Flight::json(Flight::bookingService()->delete_booking($id));
        Flight::json(['success' => true, 'message' => 'Booking deleted successfully']);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});
?>
