<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Users\UserContractListService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdminUserContractListController extends Controller
{
    private $userContractListService;

    public function __construct(UserContractListService $userContractListService)
    {
        $this->userContractListService = $userContractListService;
    }

    /**
    * @OA\Post(
    *     path="/api/users/{user_id}/products/{product_id}/contracts",
    *     summary="Create a new contract for a user and product.",
    *     tags={"AdminUserContractPrices"},
    *     @OA\Parameter(
    *         name="user_id",
    *         in="path",
    *         description="ID of the user.",
    *         required=true,
    *         @OA\Schema(
    *             type="string",
    *             format="uuid"
    *         )
    *     ),
    *     @OA\Parameter(
    *         name="product_id",
    *         in="path",
    *         description="ID of the product.",
    *         required=true,
    *         @OA\Schema(
    *             type="string",
    *             format="uuid"
    *         )
    *     ),
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             required={"price"},
    *             @OA\Property(
    *                 property="price",
    *                 type="number",
    *                 format="float",
    *                 description="Price of the contract."
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Contract created successfully."
    *     ),
    *     @OA\Response(
    *         response=422,
    *         description="Unprocessable Entity: Validation error",
    *         @OA\JsonContent(
    *             @OA\Property(property="errors", type="object"),
    *         ),
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
    public function create(Request $request) : Response
    {
        $data = $request->validate([
            'price' => ['required', 'numeric:2'],
        ]);

        try { 
            $data['userId'] = $request->route('user_id');
            $data['productId'] = $request->route('product_id');

            $this->userContractListService->create($data);

            return response()->noContent();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

     /**
    * @OA\Put(
    *     path="/api/users/{user_id}/products/{product_id}/contracts",
    *     summary="Update a contract for a user and product.",
    *     tags={"AdminUserContractPrices"},
    *     @OA\Parameter(
    *         name="user_id",
    *         in="path",
    *         description="ID of the user.",
    *         required=true,
    *         @OA\Schema(
    *             type="string",
    *             format="uuid"
    *         )
    *     ),
    *     @OA\Parameter(
    *         name="product_id",
    *         in="path",
    *         description="ID of the product.",
    *         required=true,
    *         @OA\Schema(
    *             type="string",
    *             format="uuid"
    *         )
    *     ),
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             required={"price"},
    *             @OA\Property(
    *                 property="price",
    *                 type="number",
    *                 format="float",
    *                 description="Price of the contract."
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Contract created successfully."
    *     ),
    *     @OA\Response(
    *         response=422,
    *         description="Unprocessable Entity: Validation error",
    *         @OA\JsonContent(
    *             @OA\Property(property="errors", type="object"),
    *         ),
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
    public function update(Request $request) : JsonResponse
    {
        $data = $request->validate([
            'price' => ['required', 'numeric:2'],
        ]);

        try { 
            $data['userId'] = $request->route('user_id');
            $data['productId'] = $request->route('product_id');

            $this->userContractListService->update($data);

            return response()->json([], 200);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    /**
    * @OA\Delete(
    *     path="/api/users/{user_id}/products/{product_id}/contracts",
    *     summary="Delete a contract for a user and product.",
    *     tags={"AdminUserContractPrices"},
    *     @OA\Parameter(
    *         name="user_id",
    *         in="path",
    *         description="ID of the user.",
    *         required=true,
    *         @OA\Schema(
    *             type="string",
    *             format="uuid"
    *         )
    *     ),
    *     @OA\Parameter(
    *         name="product_id",
    *         in="path",
    *         description="ID of the product.",
    *         required=true,
    *         @OA\Schema(
    *             type="string",
    *             format="uuid"
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Contract deleted successfully."
    *     ),
    *     @OA\Response(
    *         response=422,
    *         description="Unprocessable Entity: Validation error",
    *         @OA\JsonContent(
    *             @OA\Property(property="errors", type="object"),
    *         ),
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
    public function delete(Request $request) : JsonResponse
    {
        try { 
            $data['userId'] = $request->route('user_id');
            $data['productId'] = $request->route('product_id');

            $this->userContractListService->delete($data);

            return response()->json([], 200);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }
}
