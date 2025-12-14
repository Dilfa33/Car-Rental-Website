<?php

/**
 * @OA\Get(
 *     path="/cars",
 *     tags={"cars"},
 *     summary="Get all cars",
 *     @OA\Response(
 *         response=200,
 *         description="List of all cars in the database"
 *     )
 * )
 */

require_once __DIR__ . '/../data/Roles.php';

Flight::route('GET /cars', function() {
    try {
        $cars = Flight::carService()->get_cars();
        Flight::json(['success' => true, 'data' => $cars]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Get(
 *     path="/cars/{id}",
 *     tags={"cars"},
 *     summary="Get car by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Car ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns the car with the given ID"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Car not found"
 *     )
 * )
 */
Flight::route('GET /cars/@id', function($id){
    try {
        $car = Flight::carService()->get_car_by_id($id);
        Flight::json(['success' => true, 'data' => $car]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Post(
 *     path="/cars",
 *     tags={"cars"},
 *     summary="Add a new car",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"brand", "model", "year", "transmission", "fuel_type", "daily_rate"},
 *             @OA\Property(property="brand", type="string", example="Volkswagen"),
 *             @OA\Property(property="model", type="string", example="Golf 7"),
 *             @OA\Property(property="year", type="integer", example=2019),
 *             @OA\Property(property="transmission", type="string", example="manual"),
 *             @OA\Property(property="fuel_type", type="string", example="diesel"),
 *             @OA\Property(property="daily_rate", type="number", example=50.00),
 *             @OA\Property(property="availability_status", type="string", example="available"),
 *             @OA\Property(property="mileage", type="integer", example=120000)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Car successfully added"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation failed"
 *     )
 * )
 */
Flight::route('POST /cars', function(){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $data = Flight::request()->data->getData();
    try {
        Flight::json(Flight::carService()->add_car($data));
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Put(
 *     path="/cars/{id}",
 *     tags={"cars"},
 *     summary="Update a car by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Car ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="brand", type="string", example="BMW"),
 *             @OA\Property(property="model", type="string", example="320d"),
 *             @OA\Property(property="year", type="integer", example=2020),
 *             @OA\Property(property="transmission", type="string", example="automatic"),
 *             @OA\Property(property="fuel_type", type="string", example="diesel"),
 *             @OA\Property(property="daily_rate", type="number", example=75.00),
 *             @OA\Property(property="availability_status", type="string", example="unavailable"),
 *             @OA\Property(property="mileage", type="integer", example=80000)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Car successfully updated"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Car not found"
 *     )
 * )
 */
Flight::route('PUT /cars/@id', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $data = Flight::request()->data->getData();
    try {
        Flight::json(Flight::carService()->update_car($id, $data));
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Delete(
 *     path="/cars/{id}",
 *     tags={"cars"},
 *     summary="Delete a car by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Car ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Car successfully deleted"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Car not found"
 *     )
 * )
 */
Flight::route('DELETE /cars/@id', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    try {
        Flight::json(Flight::carService()->delete_car($id));
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});
?>
