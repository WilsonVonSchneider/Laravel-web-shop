<?php

namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Products\ProductService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdminProductController extends Controller
{
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
    * @OA\Post(
    *      path="/products",
    *      operationId="createProduct",
    *      tags={"AdminProducts"},
    *      summary="Create a new product",
    *      description="Create a new product",
    *      @OA\RequestBody(
    *          required=true,
    *          description="Product data",
    *          @OA\JsonContent(
    *              required={"name", "price", "product_category_id"},
    *              @OA\Property(
    *                  property="name",
    *                  type="string",
    *                  maxLength=255,
    *                  example="Product Name",
    *                  description="Name of the product"
    *              ),
    *              @OA\Property(
    *                  property="description",
    *                  type="string",
    *                  maxLength=1000,
    *                  example="Product description",
    *                  description="Description of the product"
    *              ),
    *              @OA\Property(
    *                  property="price",
    *                  type="number",
    *                  format="float",
    *                  example=10.99,
    *                  description="Price of the product"
    *              ),
    *              @OA\Property(
    *                  property="product_category_id",
    *                  type="string",
    *                  format="uuid",
    *                  example="550e8400-e29b-41d4-a716-446655440000",
    *                  description="ID of the product category"
    *              ),
    *              @OA\Property(
    *                  property="published",
    *                  type="boolean",
    *                  example=true,
    *                  description="Whether the product is published"
    *              ),
    *          ),
    *      ),
    *     @OA\Response(
    *         response=204,
    *         description="No Content: Successfully created",
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric'],
            'product_category_id' => ['required', 'uuid'],
            'published' => ['nullable', 'boolean'],
        ]);

        try { 
            $this->productService->create($data);

            return response()->noContent();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        } catch (\Error $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

   /**
    * @OA\Get(
    *      path="/products",
    *      operationId="getPaginatedProducts",
    *      tags={"AdminProducts"},
    *      summary="Get paginated list of products with filters and sorting",
    *      description="Returns a paginated list of products based on the provided filters, sorting, and pagination parameters.",
    *      @OA\Parameter(
    *          name="search",
    *          in="query",
    *          description="Search term to filter products by name",
    *          required=false,
    *          @OA\Schema(type="string")
    *      ),
    *      @OA\Parameter(
    *          name="sort_by",
    *          in="query",
    *          description="Field to sort products by",
    *          required=false,
    *          @OA\Schema(type="string", enum={"name", "price"})
    *      ),
    *      @OA\Parameter(
    *          name="sort",
    *          in="query",
    *          description="Sort order",
    *          required=false,
    *          @OA\Schema(type="string", enum={"asc", "desc"})
    *      ),
    *      @OA\Parameter(
    *          name="per_page",
    *          in="query",
    *          description="Number of products per page",
    *          required=false,
    *          @OA\Schema(type="integer", minimum=1, maximum=100)
    *      ),
    *      @OA\Parameter(
    *          name="page",
    *          in="query",
    *          description="Page number",
    *          required=false,
    *          @OA\Schema(type="integer", minimum=1)
    *      ),
    *      @OA\Parameter(
    *          name="price",
    *          in="query",
    *          description="Filter for price",
    *          required=false,
    *          @OA\Schema(type="float")
    *      ),
    *      @OA\Parameter(
    *          name="name",
    *          in="query",
    *          description="Filter for name",
    *          required=false,
    *          @OA\Schema(type="string")
    *      ),
    *      @OA\Parameter(
    *          name="category",
    *          in="query",
    *          description="Filter for category name",
    *          required=false,
    *          @OA\Schema(type="string")
    *      ),
    *      @OA\Response(
    *          response=200,
    *          description="Successful operation. Returns a paginated list of products.",
    *          @OA\JsonContent(
    *              @OA\Property(property="products", type="array", 
    *                  @OA\Items(
    *                      @OA\Property(property="id", type="string", format="uuid"),
    *                      @OA\Property(property="name", type="string"),
    *                      @OA\Property(property="description", type="string"),
    *                      @OA\Property(property="price", type="number", format="float"),
    *                      @OA\Property(property="created_at", type="string", format="date-time"),
    *                      @OA\Property(property="updated_at", type="string", format="date-time"),
    *                      @OA\Property(property="categories", type="array",
    *                          @OA\Items(
    *                              @OA\Property(property="id", type="string", format="uuid"),
    *                              @OA\Property(property="name", type="string"),
    *                              @OA\Property(property="created_at", type="string", format="date-time"),
    *                              @OA\Property(property="updated_at", type="string", format="date-time")
    *                          )
    *                      )
    *                  )
    *              ),
    *              @OA\Property(property="page_info", type="object",
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
            'search' => ['nullable', 'string'],
            'sort_by' => ['nullable', 'string', 'in:name,price'],
            'sort' => ['nullable', 'string', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'price' => ['nullable', 'numeric'],
            'name' => ['nullable', 'string'],
            'category' => ['nullable', 'string'],
        ]);

        try{
            $search = $request->input('search', null);
            $filters = $request->only(['price', 'name', 'category']);
            $sortBy = $request->input('sort_by', 'name');
            $sort = $request->input('sort', 'asc');
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1); 
    
            $data = $this->productService->paginated($search, $filters, $sortBy, $sort, $perPage, $page);
    
            return response()->json([
                'products' => $data->items(),
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
        } catch (\Error $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
    * @OA\Get(
    *      path="/products/{product_id}",
    *      operationId="getProductById",
    *      tags={"AdminProducts"},
    *      summary="Get a product by ID",
    *      description="Returns details of a product based on the provided ID",
    *      @OA\Parameter(
    *          name="product_id",
    *          in="path",
    *          required=true,
    *          description="ID of the product to retrieve",
    *          @OA\Schema(
    *              type="string",
    *              format="uuid",
    *              example="550e8400-e29b-41d4-a716-446655440000"
    *          )
    *      ),
    *      @OA\Response(
    *          response=200,
    *          description="Successful operation",
    *          @OA\JsonContent(
    *              type="object",
    *              @OA\Property(
    *                  property="product",
    *                  type="object",
    *                  @OA\Property(property="id", type="string", format="uuid"),
    *                  @OA\Property(property="name", type="string"),
    *                  @OA\Property(property="description", type="string"),
    *                  @OA\Property(property="price", type="number", format="float"),
    *                  @OA\Property(property="published", type="boolean"),
    *                  @OA\Property(property="created_at", type="string", format="date-time"),
    *                  @OA\Property(property="updated_at", type="string", format="date-time")
    *              )
    *          )
    *      ),
    *     @OA\Response(
    *         response=404,
    *         description="Product category not found",
    *         @OA\JsonContent(
    *             @OA\Property(property="error", type="string")
    *         )
    *     ),
    *     @OA\Response(
    *         response=422,
    *         description="Validation error",
    *         @OA\JsonContent(
    *             @OA\Property(property="error", type="string", example="Validation failed"),
    *             @OA\Property(property="message", type="string", example="The given data was invalid.")
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
    public function show(Request $request) : JsonResponse
    {
        try {
            $productId = $request->route('product_id');

            $product = $this->productService->getById($productId);
            
            return response()->json(['product' => $product]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        } catch (\Error $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
    * @OA\Post(
    *      path="/your-endpoint-path/{product_id}",
    *      operationId="updateProduct",
    *      tags={"AdminProducts"},
    *      summary="Update a product",
    *      description="Updates an existing product based on the provided ID",
    *      @OA\Parameter(
    *          name="product_id",
    *          in="path",
    *          required=true,
    *          description="ID of the product to update",
    *          @OA\Schema(
    *              type="string",
    *              format="uuid",
    *              example="550e8400-e29b-41d4-a716-446655440000"
    *          )
    *      ),
    *      @OA\RequestBody(
    *          required=true,
    *          description="Product data to update",
    *          @OA\JsonContent(
    *              required={"name", "price"},
    *              @OA\Property(property="name", type="string", maxLength=255),
    *              @OA\Property(property="description", type="string", maxLength=1000),
    *              @OA\Property(property="price", type="number", format="float"),
    *              @OA\Property(property="product_category_id", type="string", format="uuid"),
    *              @OA\Property(property="published", type="boolean")
    *          )
    *      ),
    *      @OA\Response(
    *          response=200,
    *          description="Successful operation. The product was updated successfully."
    *      ),
    *     @OA\Response(
    *         response=404,
    *         description="Product category not found",
    *         @OA\JsonContent(
    *             @OA\Property(property="error", type="string")
    *         )
    *     ),
    *     @OA\Response(
    *         response=422,
    *         description="Validation error",
    *         @OA\JsonContent(
    *             @OA\Property(property="error", type="string", example="Validation failed"),
    *             @OA\Property(property="message", type="string", example="The given data was invalid.")
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
    public function update (Request $request) : JsonResponse {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric'],
            'product_category_id' => ['uuid', 'nullable'],
            'published' => ['nullable', 'boolean'],
        ]);

        try {
            $productId = $request->route('product_id');
           
            $this->productService->update($productId, $data);
            
            return response()->json([], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        } catch (\Error $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
    * @OA\Delete(
    *     path="/api/admin/product/{product_id}",
    *     summary="Delete a product by ID",
    *     description="Delete a product by its ID.",
    *     tags={"AdminProducts"},
    *     @OA\Parameter(
    *         name="product_id",
    *         in="path",
    *         description="ID of the product to delete",
    *         required=true,
    *         @OA\Schema(
    *             type="string",
    *             format="uuid"
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation"
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Product not found",
    *         @OA\JsonContent(
    *             @OA\Property(property="error", type="string")
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
    *             @OA\Property(property="error", type="string")
    *         )
    *     )
    * )
    */
    public function delete (Request $request) : JsonResponse {
        try {
            $productId = $request->route('product_id');

            $this->productService->delete($productId);

            return response()->json([], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        } catch (\Error $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
