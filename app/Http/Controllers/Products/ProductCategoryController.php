<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Products\ProductCategoryService;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductCategoryController extends Controller
{
    private $productCategoryService;

    public function __construct(ProductCategoryService $productCategoryService)
    {
        $this->productCategoryService = $productCategoryService;
    }

     /**
    * @OA\Get(
    *     path="/api/product-categories",
    *     summary="Retrieve a paginated list of products categories",
    *     description="Retrieve a paginated list of product categories based on the provided sorting and pagination parameters.",
    *     tags={"ProductCategories"},
    *     @OA\Parameter(
    *         name="search",
    *         in="query",
    *         description="Search the results by the specified field.",
    *         @OA\Schema(type="string")
    *     ), 
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
    *         description="Number of product categories per page. Must be between 1 and 100.",
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
    *                 property="productCategories",
    *                 type="array",
    *                 @OA\Items(
    *                     type="object",
    *                     @OA\Property(property="id", type="string", format="uuid"),
    *                     @OA\Property(property="name", type="string"),
    *                     @OA\Property(property="description", type="string"),
    *                     @OA\Property(property="parent_id", type="string", format="uuid"),
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
            'search' => ['nullable', 'string'],
            'sort_by' => ['nullable', 'string', 'in:name'],
            'sort' => ['nullable', 'string', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        try{
            $search = $request->input('search', null);
            $sortBy = $request->input('sort_by', 'name');
            $sort = $request->input('sort', 'asc');
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1); 
    
            $data = $this->productCategoryService->paginated($search, $sortBy, $sort, $perPage, $page);
    
            return response()->json([
                'productCategories' => $data->items(),
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
    *     path="/api/product-categories/{product_category_id}",
    *     summary="Retrieve a product category by ID",
    *     description="Retrieve a product category by its ID.",
    *     tags={"ProductCategories"},
    *     @OA\Parameter(
    *         name="product_category_id",
    *         in="path",
    *         required=true,
    *         description="ID of the product category to retrieve",
    *         @OA\Schema(type="string", format="uuid")
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(property="productCategory", type="object",
    *                 @OA\Property(property="id", type="string", format="uuid"),
    *                 @OA\Property(property="name", type="string"),
    *                 @OA\Property(property="description", type="string"),
    *                 @OA\Property(property="parent_id", type="string", format="uuid"),
    *                 @OA\Property(property="created_at", type="string", format="date-time"),
    *                 @OA\Property(property="updated_at", type="string", format="date-time")
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Product category not found",
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
    *             @OA\Property(property="error", type="string", example="Internal Server Error"),
    *             @OA\Property(property="message", type="string", example="An unexpected error occurred while processing the request.")
    *         )
    *     )
    * )
    */
    public function show(Request $request) : JsonResponse
    {
        try {
            $productCategoryId = $request->route('product_category_id');

            $productCategory = $this->productCategoryService->getById($productCategoryId);
            
            return response()->json(['productCategory' => $productCategory]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }
}