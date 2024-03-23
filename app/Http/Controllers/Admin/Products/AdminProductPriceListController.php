<?php

namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use App\Services\Admin\Products\ProductPriceListService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Http\Request;

class AdminProductPriceListController extends Controller
{
    private $productPriceListService;

    public function __construct(ProductPriceListService $productPriceListService)
    {
        $this->productPriceListService = $productPriceListService;
    }

    /**
    * @OA\Post(
    *     path="/api/admin/product-price-lits",
    *     summary="Create a new product price list",
    *     description="Create a new product price list with the provided data.",
    *     tags={"AdminProductPriceLists"},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             required={"name"},
    *             @OA\Property(property="name", type="string", maxLength=255),
    *             @OA\Property(property="description", type="string", maxLength=1000),
    *         ),
    *     ),
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
            'description' => ['string', 'max:1000'],
        ]);

        try { 
            $this->productPriceListService->create($data);

            return response()->noContent();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        } catch (\Error $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

     /**
    * @OA\Get(
    *     path="/api/admin/product-price-lits",
    *     summary="Retrieve a paginated list of products price lists",
    *     description="Retrieve a paginated list of product price lists based on the provided sorting and pagination parameters.",
    *     tags={"AdminProductPriceLists"},
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
    *         @OA\Schema(type="string", enum={"name"})
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
    *         description="Number of product price lists per page. Must be between 1 and 100.",
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
    *                 property="ProductPriceLists",
    *                 type="array",
    *                 @OA\Items(
    *                     type="object",
    *                     @OA\Property(property="id", type="string", format="uuid"),
    *                     @OA\Property(property="name", type="string"),
    *                     @OA\Property(property="description", type="string"),
    *                     @OA\Property(property="sku", type="string"),
    *                     @OA\Property(property="active", type="bool"),
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
    
            $data = $this->productPriceListService->paginated($search, $sortBy, $sort, $perPage, $page);
    
            return response()->json([
                'productPriceLists' => $data->items(),
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
    *     path="/api/admin/product-price-lists/{product_price_list_id}",
    *     summary="Retrieve a product price list by ID",
    *     description="Retrieve a product price list by its ID.",
    *     tags={"AdminProductPriceLists"},
    *     @OA\Parameter(
    *         name="product_price_list_id",
    *         in="path",
    *         required=true,
    *         description="ID of the product price list to retrieve",
    *         @OA\Schema(type="string", format="uuid")
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(property="productPriceList", type="object",
    *                 @OA\Property(property="id", type="string", format="uuid"),
    *                 @OA\Property(property="name", type="string"),
    *                 @OA\Property(property="description", type="string"),
    *                 @OA\Property(property="sku", type="string"),
    *                 @OA\Property(property="active", type="bool"),
    *                 @OA\Property(property="created_at", type="string", format="date-time"),
    *                 @OA\Property(property="updated_at", type="string", format="date-time")
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Product price list not found",
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
            $productPriceListId = $request->route('product_price_list_id');

            $productPriceList = $this->productPriceListService->getById($productPriceListId);
            
            return response()->json(['productPriceList' => $productPriceList]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        } catch (\Error $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
    * @OA\Put(
    *     path="/api/admin/product-price-lists/{product_price_list_id}",
    *     summary="Update a product price list by ID",
    *     description="Update a product price list by its ID.",
    *     tags={"AdminProductPriceLists"},
    *     @OA\Parameter(
    *         name="product_price_list_id",
    *         in="path",
    *         required=true,
    *         description="ID of the product price list to update",
    *         @OA\Schema(type="string", format="uuid")
    *     ),
    *     @OA\RequestBody(
    *         required=true,
    *         description="Updated product price list data",
    *         @OA\JsonContent(
    *             required={"name"},
    *             @OA\Property(property="name", type="string", maxLength=255),
    *             @OA\Property(property="description", type="string", maxLength=1000),
    *             @OA\Property(property="active", type="bool", nullable=true)
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(property="message", type="string", example="Product Price List updated successfully")
    *         )
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Product Price List not found",
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
            'description' => ['string', 'max:1000'],
            'active' => ['nullable', 'bool'],
        ]);

        try {
            $productPriceListId = $request->route('product_price_list_id');
           
            $this->productPriceListService->update($productPriceListId, $data);
            
            return response()->json([], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        } catch (\Error $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
    * @OA\Delete(
    *     path="/api/admin/product-price-lists/{product_price_list_id}",
    *     summary="Delete a product price list by ID",
    *     description="Delete a product price list by its ID.",
    *     tags={"AdminProductPriceLists"},
    *     @OA\Parameter(
    *         name="product_price_list_id",
    *         in="path",
    *         description="ID of the product price list to delete",
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
    *         description="product price list not found",
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
            $productPriceListId = $request->route('product_price_list_id');

            $this->productPriceListService->delete($productPriceListId);

            return response()->json([], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        } catch (\Error $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
