<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Services\Users\UserOrderService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Illuminate\Http\Request;

class UserOrderController extends Controller
{
    private $userOrderService;

    public function __construct(UserOrderService $userOrderService)
    {
        $this->userOrderService = $userOrderService;
    }

    /**
    * @OA\Post(
    *      path="/api/users/orders",
    *      tags={"UserOrders"},
    *      summary="Create a new user order",
    *      description="Create a new user order with phone number, address, city and country, and a list of product IDs.",
    *      @OA\RequestBody(
    *          required=true,
    *          description="User order details",
    *          @OA\JsonContent(
    *              required={"phone", "address", "city_country", "products"},
    *              @OA\Property(property="phone", type="string", example="0976305912"),
    *              @OA\Property(property="address", type="string", example="Brune Bjelinskog 54"),
    *              @OA\Property(property="city_country", type="string", example="3100, Osijek, Croatia"),
    *              @OA\Property(
    *                  property="products",
    *                  type="array",
    *                  @OA\Items(type="string", format="uuid", example="9ba414e3-b9d2-4f22-90b0-abda3244ba50")
    *              ),
    *          ),
    *      ),
    *      @OA\Response(
    *          response=204,
    *          description="User order created successfully"
    *      ),
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
    public function create(Request $request) : Response
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'city_country' => ['required', 'string', 'max:255'],
            'products' => ['required', 'array'],
            'products.*' => ['required', 'uuid'],
        ]);

        try { 
            $user = auth()->user();

            $data['userId'] = $user->id;
            $data['name'] = $user->name;
            $data['email'] = $user->email;

            $this->userOrderService->create($data);

            return response()->noContent();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        } 
    }
}
