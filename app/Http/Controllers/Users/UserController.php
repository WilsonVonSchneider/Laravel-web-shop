<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
    * @OA\Get(
    *     path="/api/users",
    *     summary="Get authenticated user",
    *     tags={"Users"},
    *     security={{"bearerAuth":{}}},
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *                 @OA\Property(property="user", type="object",
    *                 @OA\Property(property="id", type="string", example="9b9e0aa2-ad0a-4b22-94d8-56cad40c6694"),
    *                 @OA\Property(property="name", type="string", example="Matej"),
    *                 @OA\Property(property="email", type="string", format="email", example="matej.zagar+mz19@gmail.com"),
    *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-21T14:20:55.000000Z"),
    *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-03-21T14:20:55.000000Z"),
    *             )
    *         )
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
    public function show () : JsonResponse {
        $user = auth()->user();

        return response()->json(['user' => $user,]);
    }

    /**
    * @OA\Put(
    *     path="/api/users",
    *     summary="Update user details",
    *     tags={"Users"},
    *     security={{"bearerAuth":{}}},
    *     @OA\RequestBody(
    *         required=true,
    *         description="User data to be updated",
    *         @OA\JsonContent(
    *             required={"name"},
    *             @OA\Property(property="name", type="string", example="John Doe", description="User's new name"),
    *         ),
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful update",
    *         @OA\JsonContent(
    *             @OA\Property(property="user", type="object",
    *                 @OA\Property(property="id", type="string", example="9b9e0aa2-ad0a-4b22-94d8-56cad40c6694"),
    *                 @OA\Property(property="name", type="string", example="John Doe"),
    *                 @OA\Property(property="email", type="string", format="email", example="user@example.com"),
    *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-21T14:20:55.000000Z"),
    *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-03-21T14:25:55.000000Z"),
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=401,
    *         description="Unauthenticated",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Unauthenticated"),
    *         ),
    *     ),
    *     @OA\Response(
    *         response=422,
    *         description="Validation error",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="The given data was invalid."),
    *             @OA\Property(property="errors", type="object", example={"name": {"The name field is required."}}),
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
    public function update(Request $request) : JsonResponse {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $user = auth()->user();
        $user->name = $data['name'];
        $user->save();

        return response()->json(['user' => $user]);
    }

    /**
    * @OA\Delete(
    *     path="/api/users",
    *     summary="Delete user",
    *     tags={"Users"},
    *     security={{"bearerAuth":{}}},
    *     @OA\Response(
    *         response=200,
    *         description="Successful deletion",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="User deleted successfully"),
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
    public function delete() : JsonResponse {
        $user = auth()->user();
        $user->delete();

        return response()->json([], 200);
    }
}
