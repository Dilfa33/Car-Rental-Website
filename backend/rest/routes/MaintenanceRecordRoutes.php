<?php

/**
 * @OA\Get(
 *     path="/maintenance",
 *     tags={"maintenance"},
 *     summary="Get all maintenance records",
 *     @OA\Response(
 *         response=200,
 *         description="List of all maintenance records in the database"
 *     )
 * )
 */
Flight::route('GET /maintenance', function() {
    try {
        $records = Flight::maintenanceRecordService()->get_records();
        Flight::json(['success' => true, 'data' => $records]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Get(
 *     path="/maintenance/{id}",
 *     tags={"maintenance"},
 *     summary="Get maintenance record by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Maintenance ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns the maintenance record with the given ID"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Maintenance record not found"
 *     )
 * )
 */
Flight::route('GET /maintenance/@id', function($id){
    try {
        $record = Flight::maintenanceRecordService()->get_record_by_id($id);
        Flight::json(['success' => true, 'data' => $record]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Post(
 *     path="/maintenance",
 *     tags={"maintenance"},
 *     summary="Add a new maintenance record",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"car_id","service_date","description","cost"},
 *             @OA\Property(property="car_id", type="integer", example=1),
 *             @OA\Property(property="service_date", type="string", example="2025-11-01"),
 *             @OA\Property(property="description", type="string", example="Oil change and filter"),
 *             @OA\Property(property="cost", type="number", example=120.50)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Maintenance record successfully added"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation failed"
 *     )
 * )
 */
Flight::route('POST /maintenance', function(){
    $data = Flight::request()->data->getData();
    try {
        Flight::json(Flight::maintenanceRecordService()->add_record($data));
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Put(
 *     path="/maintenance/{id}",
 *     tags={"maintenance"},
 *     summary="Update a maintenance record by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Maintenance ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="service_date", type="string", example="2025-11-01"),
 *             @OA\Property(property="description", type="string", example="Updated description"),
 *             @OA\Property(property="cost", type="number", example=80.00)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Maintenance record successfully updated"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Maintenance record not found"
 *     )
 * )
 */
Flight::route('PUT /maintenance/@id', function($id){
    $data = Flight::request()->data->getData();
    try {
        Flight::json(Flight::maintenanceRecordService()->update_record($id, $data));
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Delete(
 *     path="/maintenance/{id}",
 *     tags={"maintenance"},
 *     summary="Delete a maintenance record by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Maintenance ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Maintenance record successfully deleted"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Maintenance record not found"
 *     )
 * )
 */
Flight::route('DELETE /maintenance/@id', function($id){
    try {
        Flight::json(Flight::maintenanceRecordService()->delete_record($id));
        Flight::json(['success' => true, 'message' => 'Maintenance record deleted']);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});
?>
