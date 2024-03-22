<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Services\Admin\Users\UserService;

class AdminUserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

   /**
    * @OA\Get(
    *     path="/api/paginated",
    *     summary="Retrieve a paginated list of users",
    *     description="Retrieve a paginated list of users based on the provided sorting and pagination parameters.",
    *     tags={"AdminUsers"},
    *     @OA\Parameter(
    *         name="sort_by",
    *         in="query",
    *         description="Sort the results by the specified field.",
    *         @OA\Schema(type="string", enum={"name", "email"})
    *     ),
    *     @OA\Parameter(
    *         name="sort",
    *         in="query",
    *         description="Sort direction for the results.",
    *         @OA\Schema(type="string", enum={"asc", "desc"})
    *     ),
    *     @OA\Parameter(
    *         name="per_page",
    *         in="query",
    *         description="Number of users per page. Must be between 1 and 100.",
    *         @OA\Schema(type="integer", minimum=1, maximum=100)
    *     ),
    *     @OA\Parameter(
    *         name="page",
    *         in="query",
    *         description="Page number to retrieve. Must be a positive integer.",
    *         @OA\Schema(type="integer", minimum=1)
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(
    *                 property="users",
    *                 type="array",
    *                 @OA\Items(
    *                     type="object",
    *                     @OA\Property(property="id", type="string"),
    *                     @OA\Property(property="name", type="string"),
    *                     @OA\Property(property="email", type="string", format="email"),
    *                     @OA\Property(property="created_at", type="string", format="date-time"),
    *                     @OA\Property(property="updated_at", type="string", format="date-time")
    *                 )
    *             ),
    *             @OA\Property(
    *                 property="page_info",
    *                 type="object",
    *                 @OA\Property(property="current_page", type="integer"),
    *                 @OA\Property(property="total", type="integer"),
    *                 @OA\Property(property="per_page", type="integer"),
    *                 @OA\Property(property="last_page", type="integer"),
    *                 @OA\Property(property="requested_page", type="integer")
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=422,
    *         description="Validation error",
    *         @OA\JsonContent(
    *             @OA\Property(property="errors", type="object")
    *         )
    *     ),
    *     @OA\Response(
    *         response=401,
    *         description="Unauthenticated",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Unauthenticated")
    *         )
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Internal Server Error",
    *         @OA\JsonContent(
    *             @OA\Property(property="error", type="string", example="Internal Server Error"),
    *             @OA\Property(property="message", type="string", example="An unexpected error occurred while processing the request.")
    *         )
    *     )
    * )
    */
    public function paginated (Request $request) : JsonResponse {
        $request->validate([
            'sort_by' => ['nullable', 'string', 'in:name,email'],
            'sort' => ['nullable', 'string', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        try{
            $sortBy = $request->input('sort_by', 'name');
            $sort = $request->input('sort', 'asc');
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1); 
    
            $data = $this->userService->paginated($sortBy, $sort, $perPage, $page);
    
            return response()->json([
                'users' => $data->items(),
                'page_info' => [
                'current_page' => $data->currentPage(),
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'last_page' => $data->lastPage(),
                'requested_page' => $page, 
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
       
    }

    /**
    * @OA\Get(
    *     path="/api/users/{user_id}",
    *     summary="Show user details",
    *     tags={"AdminUsers"},
    *     security={{"bearerAuth":{}}},
    *     @OA\Parameter(
    *         name="user_id",
    *         in="path",
    *         required=true,
    *         description="ID of the user to retrieve",
    *         @OA\Schema(type="integer", format="int64")
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *             @OA\Property(property="user", type="object",
    *                 @OA\Property(property="id", type="integer", example=1),
    *                 @OA\Property(property="name", type="string", example="John Doe"),
    *                 @OA\Property(property="email", type="string", format="email", example="john@example.com"),
    *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-21T14:20:55.000000Z"),
    *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-03-21T14:20:55.000000Z"),
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="User not found",
    *         @OA\JsonContent(
    *             @OA\Property(property="error", type="string", example="User not found"),
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
    public function show (Request $request) : JsonResponse {
        try {
            $userId = $request->route('user_id');

            $user = $this->userService->getById($userId);
            
            return response()->json(['user' => $user]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    /**
    * @OA\Post(
    *     path="/users/{user_id}",
    *     tags={"AdminUsers"},
    *     summary="Update user information",
    *     description="Update the information of a user in the system.",
    *     operationId="updateUser",
    *     @OA\Parameter(
    *         name="user_id",
    *         in="path",
    *         required=true,
    *         description="ID of the user to update",
    *         @OA\Schema(
    *             type="string",
    *         )
    *     ),
    *     @OA\RequestBody(
    *         required=true,
    *         description="User data to update",
    *         @OA\JsonContent(
    *             required={"name"},
    *             @OA\Property(property="name", type="string", example="John Doe", maxLength=255),
    *             @OA\Property(property="is_admin", type="boolean", example=true)
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="User updated successfully",
    *         @OA\JsonContent(
    *             @OA\Property(property="user", type="object",
    *                 @OA\Property(property="id", type="string", example="1"),
    *                 @OA\Property(property="name", type="string", example="John Doe"),
    *                 @OA\Property(property="is_admin", type="boolean", example=true),
    *                 @OA\Property(property="created_at", type="string", format="date-time"),
    *                 @OA\Property(property="updated_at", type="string", format="date-time")
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="User not found",
    *         @OA\JsonContent(
    *             @OA\Property(property="error", type="string", example="User not found")
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
    public function update (Request $request) : JsonResponse {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'is_admin' => ['boolean'],
        ]);

        try {
            $userId = $request->route('user_id');
           
            $this->userService->update($userId, $data);
            
            return response()->json([], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    /**
    * @OA\Delete(
    *     path="/api/users/{user_id}",
    *     summary="Delete a user",
    *     description="Delete a user by ID.",
    *     tags={"AdminUsers"},
    *     @OA\Parameter(
    *         name="user_id",
    *         in="path",
    *         required=true,
    *         description="ID of the user to delete",
    *         @OA\Schema(type="string")
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="User deleted successfully",
    *         @OA\JsonContent(
    *             type="object"
    *         )
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="User not found",
    *         @OA\JsonContent(
    *             @OA\Property(property="error", type="string", example="User not found")
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
    public function delete (Request $request) : JsonResponse {
        try {
            $userId = $request->route('user_id');

            $this->userService->delete($userId);

            return response()->json([], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }
}
