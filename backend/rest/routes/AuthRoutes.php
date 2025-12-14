<?php



Flight::group('/auth', function() {

    /**
     * @OA\Post(
     *     path="/auth/register",
     *     summary="Register new user",
     *     description="Add a new user to the database",
     *     tags={"auth"},
     *     @OA\RequestBody(
     *         description="User registration data",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"first_name", "last_name", "email", "password"},
     *                 @OA\Property(property="first_name", type="string", example="John"),
     *                 @OA\Property(property="last_name", type="string", example="Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *                 @OA\Property(property="password", type="string", example="securePassword123"),
     *                 @OA\Property(property="phone", type="string", example="555-0101")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User registered successfully"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Registration failed"
     *     )
     * )
     */
    Flight::route('POST /register', function() {
        $data = Flight::request()->data->getData();
        $response = Flight::auth_service()->register($data);

        if ($response['success']) {
            Flight::json([
                'message' => 'User registered successfully',
                'data' => $response['data']
            ]);
        } else {
            Flight::halt(500, $response['error']);
        }
    });

    /**
     * @OA\Post(
     *     path="/auth/login",
     *     summary="Login to system",
     *     description="Login using email and password",
     *     tags={"auth"},
     *     @OA\RequestBody(
     *         description="Login credentials",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"email", "password"},
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *                 @OA\Property(property="password", type="string", example="securePassword123")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful, returns user data and JWT token"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Login failed"
     *     )
     * )
     */
    Flight::route('POST /login', function() {
        $data = Flight::request()->data->getData();
        $response = Flight::auth_service()->login($data);

        if ($response['success']) {
            Flight::json([
                'message' => 'Login successful',
                'data' => $response['data']
            ]);
        } else {
            Flight::halt(500, $response['error']);
        }
    });
});
?>