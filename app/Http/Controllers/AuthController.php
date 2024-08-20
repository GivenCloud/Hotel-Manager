<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

/**
 * @OA\Info(
 *    title="Hotel Manager API",
 *    version="1.0.0",
 *    description="JWT Authentication API documentation"
 * )
 */

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * @OA\Post(
     *    path="/api/auth/register",
     *    summary="Register",
     *    tags={"Auth"},
     *    description="Register a new user",
     *    @OA\RequestBody(
     *       required=true,
     *       @OA\JsonContent(
     *           required={"name", "email", "password", "password_confirmation"},
     *           @OA\Property(property="name", type="string", example="John Doe"),
     *           @OA\Property(property="email", type="string", example="user@example.com"),
     *           @OA\Property(property="password", type="string", example="password123"),
     *           @OA\Property(property="password_confirmation", type="string", example="password123")
     *       )
     *    ),
     *    @OA\Response(
     *        response=201,
     *        description="User successfully registered",
     *        @OA\JsonContent(
     *            @OA\Property(property="message", type="string", example="User successfully registered"),
     *            @OA\Property(property="user", type="object",
     *                @OA\Property(property="id", type="integer", example=1),
     *                @OA\Property(property="name", type="string", example="John Doe"),
     *                @OA\Property(property="email", type="string", example="user@example.com"),
     *            )
     *        )
     *    ),
     *    @OA\Response(
     *        response=400,
     *        description="Validation error",
     *        @OA\JsonContent(
     *            @OA\Property(property="errors", type="object", example={"email": {"The email has already been taken."}})
     *        )
     *    )
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:30',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed|min:4',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    /**
     * @OA\Post(
     *    path="/api/auth/login",
     *    summary="Login",
     *    tags={"Auth"},
     *    description="Authenticate a user and return a JWT token",
     *    @OA\RequestBody(
     *       required=true,
     *       @OA\JsonContent(
     *           required={"email", "password"},
     *           @OA\Property(property="email", type="string", example="admin@gmail.com"),
     *           @OA\Property(property="password", type="string", example="123456789")
     *       )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="JWT token",
     *        @OA\JsonContent(
     *            @OA\Property(property="access_token", type="string", example="your-jwt-token"),
     *            @OA\Property(property="token_type", type="string", example="bearer"),
     *            @OA\Property(property="expires_in", type="integer", example=3600)
     *        )
     *    ),
     *    @OA\Response(
     *        response=401,
     *        description="Unauthorized"
     *    )
     * )
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
