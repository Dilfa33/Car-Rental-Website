<?php

// GET all cars
Flight::route('GET /cars', function() {
    try {
        $cars = Flight::carService()->get_cars();
        Flight::json(['success' => true, 'data' => $cars]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

// GET car by ID
Flight::route('GET /cars/@id', function($id){
    try {
        $car = Flight::carService()->get_car_by_id($id);
        Flight::json(['success' => true, 'data' => $car]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

// POST - Add new car
Flight::route('POST /cars', function(){
    $data = Flight::request()->data->getData();
    try {
        Flight::json(Flight::carService()->add_car($data));
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }

});

// PUT - Update car
Flight::route('PUT /cars/@id', function($id){
    $data = Flight::request()->data->getData();
    try {
        Flight::json(Flight::carService()->update_car($id, $data));
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

// DELETE - Delete car
Flight::route('DELETE /cars/@id', function($id){
    try {
        Flight::json(Flight::carService()->delete_car($id));
        Flight::json(['success' => true, 'message' => 'Car deleted successfully']);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

?>