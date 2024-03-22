<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Info(title="Web shop example", version="0.1")
 */
class AuthController extends Controller
{
    /**
    * @OA\Post(
    *     path="/api/auth/register",
    *     summary="Register a new user",
    *     tags={"Authentication"},
    *     @OA\RequestBody(
    *         required=true,
    *         description="User registration details",
    *         @OA\JsonContent(
    *             required={"name", "email", "password", "password_confirmation"},
    *             @OA\Property(property="name", type="string", example="John Doe", description="User's full name"),
    *             @OA\Property(property="email", type="string", format="email", example="john@example.com", description="User's email address"),
    *             @OA\Property(property="password", type="string", format="password", example="password123", description="User's password"),
    *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123", description="Confirm password"),
    *         ),
    *     ),
    *     @OA\Response(
    *         response=204,
    *         description="User registered successfully",
    *     ),
    *     @OA\Response(
    *         response=422,
    *         description="Validation error",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="The given data was invalid."),
    *             @OA\Property(property="errors", type="object", example={"email": {"The email field is required."}}),
    *         ),
    *     ),
    *     @OA\Response(
    *          response=500,
    *          description="Internal server error",
    *          @OA\JsonContent(
    *          @OA\Property(property="message", type="string", example="Internal server error occurred."),
    *          @OA\Property(property="error_code", type="integer", example=500, description="HTTP status code"),
    *          ),
    *      )
    * )
    */
    public function register(Request $request) : Response {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->noContent();
    }

    /**
    * @OA\Post(
    *     path="/api/auth/login",
    *     summary="Login",
    *     tags={"Authentication"},
    *     @OA\RequestBody(
    *         required=true,
    *         description="User login credentials",
    *         @OA\JsonContent(
    *             required={"email", "password"},
    *             @OA\Property(property="email", type="string", format="email", example="john@example.com", description="User's email address"),
    *             @OA\Property(property="password", type="string", format="password", example="password123", description="User's password"),
    *         ),
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful login",
    *         @OA\JsonContent(
    *             @OA\Property(property="access_token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c"),
    *         ),
    *     ),
    *     @OA\Response(
    *         response=422,
    *         description="Validation error",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="The given data was invalid."),
    *             @OA\Property(property="errors", type="object", example={"email": {"The email field is required."}}),
    *         ),
    *     ),
    *     @OA\Response(
    *          response=500,
    *          description="Internal server error",
    *          @OA\JsonContent(
    *          @OA\Property(property="message", type="string", example="Internal server error occurred."),
    *          @OA\Property(property="error_code", type="integer", example=500, description="HTTP status code"),
    *          ),
    *      )
    * )
    */
    public function login (Request $request) : JsonResponse {
        $data = $request->validate([
            'email' => ['required', 'email', 'string'],
            'password' => ['required', Rules\Password::defaults()],
        ]);

        $user = User::where('email', $data['email'])->first();
        if(!$user || !Hash::check($data['password'],$user->password)){
            return response()->json([
                'message' => 'Invalid Credentials'
            ],401);
        }

        $token = $user->createToken($user->name.'-AuthToken')->plainTextToken;
        
        return response()->json([
            'access_token' => $token,
        ]);
    }

    /**
    * @OA\Post(
    *     path="/api/logout",
    *     summary="Logout",
    *     tags={"Authentication"},
    *     security={{"bearerAuth":{}}},
    *     @OA\Response(
    *         response=200,
    *         description="Successful logout",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Logged out successfully."),
    *         ),
    *     ),
    *     @OA\Response(
    *         response=401,
    *         description="Unauthenticated",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Unauthenticated"),
    *         ),
    *     ),
    *     @OA\Response(
    *          response=500,
    *          description="Internal server error",
    *          @OA\JsonContent(
    *          @OA\Property(property="message", type="string", example="Internal server error occurred."),
    *          @OA\Property(property="error_code", type="integer", example=500, description="HTTP status code"),
    *          ),
    *      )
    * )
    */
    public function logout () : JsonResponse {
        auth()->user()->tokens()->delete();

        return response()->json([], 200);
    }
}
