<?php

/**
 * @OA\Get(
 *     path="/reviews",
 *     tags={"car_reviews"},
 *     summary="Get all car reviews",
 *     @OA\Response(
 *         response=200,
 *         description="List of all car reviews in the database"
 *     )
 * )
 */
Flight::route('GET /reviews', function() {
    try {
        $reviews = Flight::carReviewService()->get_all();
        Flight::json(['success' => true, 'data' => $reviews]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Get(
 *     path="/reviews/{id}",
 *     tags={"car_reviews"},
 *     summary="Get review by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Review ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns the review with the given ID"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Review not found"
 *     )
 * )
 */
Flight::route('GET /reviews/@id', function($id){
    try {
        $review = Flight::carReviewService()->get_by_id($id);
        Flight::json(['success' => true, 'data' => $review]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Post(
 *     path="/reviews",
 *     tags={"car_reviews"},
 *     summary="Add a new review",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"car_id", "user_id", "rating", "comment"},
 *             @OA\Property(property="car_id", type="integer", example=1),
 *             @OA\Property(property="user_id", type="integer", example=2),
 *             @OA\Property(property="rating", type="integer", example=5),
 *             @OA\Property(property="comment", type="string", example="Great car! Very comfortable and fuel efficient.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Review successfully added"
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
Flight::route('POST /reviews', function(){
    $user = Flight::get('user');

    if ($user->role !== 'admin') {
        Flight::halt(403, 'Access denied: admin only');
    }

    $data = Flight::request()->data->getData();
    try {
        $result = Flight::carReviewService()->add($data);
        Flight::json(['success' => true, 'data' => $result]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Put(
 *     path="/reviews/{id}",
 *     tags={"car_reviews"},
 *     summary="Update a review by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Review ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="rating", type="integer", example=4),
 *             @OA\Property(property="comment", type="string", example="Updated: Good car, but could be better.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Review successfully updated"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Review not found"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Access denied: admin only"
 *     )
 * )
 */
Flight::route('PUT /reviews/@id', function($id){
    $user = Flight::get('user');

    if ($user->role !== 'admin') {
        Flight::halt(403, 'Access denied: admin only');
    }

    $data = Flight::request()->data->getData();
    try {
        $result = Flight::carReviewService()->update($id, $data);
        Flight::json(['success' => true, 'data' => $result]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Delete(
 *     path="/reviews/{id}",
 *     tags={"car_reviews"},
 *     summary="Delete a review by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Review ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Review successfully deleted"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Review not found"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Access denied: admin only"
 *     )
 * )
 */
Flight::route('DELETE /reviews/@id', function($id){
    $user = Flight::get('user');

    if ($user->role !== 'admin') {
        Flight::halt(403, 'Access denied: admin only');
    }

    try {
        Flight::carReviewService()->delete($id);
        Flight::json(['success' => true, 'message' => 'Review deleted successfully']);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});
?>