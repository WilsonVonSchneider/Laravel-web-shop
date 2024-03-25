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

    /**
    * @OA\Get(
    *      path="/api/users/orders",
    *      tags={"UserOrders"},
    *      summary="Get paginated user orders",
    *      description="Retrieve a paginated list of user orders.",
    *      @OA\Parameter(
    *          name="sort",
    *          in="query",
    *          description="Sort order",
    *          @OA\Schema(type="string", enum={"asc", "desc"})
    *      ),
    *      @OA\Parameter(
    *          name="per_page",
    *          in="query",
    *          description="Number of orders per page",
    *          @OA\Schema(type="integer", minimum=1, maximum=100, default=10)
    *      ),
    *      @OA\Parameter(
    *          name="page",
    *          in="query",
    *          description="Page number",
    *          @OA\Schema(type="integer", minimum=1)
    *      ),
    *      @OA\Response(
    *          response=200,
    *          description="Successful operation",
    *          @OA\JsonContent(
    *              @OA\Property(
    *                  property="orders",
    *                  type="array",
    *                  @OA\Items(
    *                      @OA\Property(property="id", type="string", example="9ba41431-6ebf-4d1f-a3d9-769884cf4ece"),
    *                      @OA\Property(property="name", type="string", example="John Doe"),
    *                      @OA\Property(property="email", type="string", format="email", example="john@example.com"),
    *                      @OA\Property(property="phone", type="string", example="123456789"),
    *                      @OA\Property(property="address", type="string", example="123 Main St"),
    *                      @OA\Property(property="city_country", type="string", example="New York, USA"),
    *                      @OA\Property(property="total", type="number", format="float", example=100.00),
    *                      @OA\Property(property="tax", type="integer", example=10),
    *                      @OA\Property(property="total_with_tax", type="number", format="float", example=110.00),
    *                      @OA\Property(property="created_at", type="string", format="date-time"),
    *                      @OA\Property(property="updated_at", type="string", format="date-time"),
    *                      @OA\Property(property="user_id", type="string", example="9ba41431-6ebf-4d1f-a3d9-769884cf4ece"),
    *                  )
    *              ),
    *              @OA\Property(
    *                  property="page_info",
    *                  type="object",
    *                  @OA\Property(property="current_page", type="integer"),
    *                  @OA\Property(property="total", type="integer"),
    *                  @OA\Property(property="per_page", type="integer"),
    *                  @OA\Property(property="last_page", type="integer"),
    *                  @OA\Property(property="requested_page", type="integer")
    *              )
    *          )
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
    public function paginated (Request $request) : JsonResponse {
        $request->validate([
            'sort' => ['nullable', 'string', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        try{
            $sortBy = 'total_with_tax';
            $sort = $request->input('sort', 'asc');
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1); 
    
            $data = $this->userOrderService->paginated($sortBy, $sort, $perPage, $page);
    
            return response()->json([
                'orders' => $data->items(),
                'page_info' => [
                'current_page' => $data->currentPage(),
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'last_page' => $data->lastPage(),
                'requested_page' => $page, 
                ]
            ]);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        } 
    }
}
