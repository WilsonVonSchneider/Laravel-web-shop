<?php

namespace App\Repositories\Products;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\Product;
use App\Models\User;
use \Illuminate\Support\Facades\DB;
class ProductRepository
{
    public function paginated(User $user, string|null $search, array $filters, string $sortBy, string $sort, int $perPage, int $page) : LengthAwarePaginator
    {
        // Build the base query to fetch products
        $query = Product::query()
            ->with(['categories'])
            ->where('published', true);
    
        if ($search) {
            $query->where('name', 'ilike', '%' . $search . '%');
        }
        if (isset($filters['name'])) {
            $query->where('name', 'ilike', '%' . $filters['name'] . '%');
        }
        if (isset($filters['category'])) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->where('name', 'ilike', '%' . $filters['category'] . '%');
            });
        }
    
        // Select the appropriate price based on the user's product price list
        $query->select('products.*', DB::raw('COALESCE(product_product_price_list.price, products.price) as price'));
    
        // If the user has a product price list, join the product_price_list table
        if ($user->product_price_list_id) {
            $query->leftJoin('product_product_price_list', function ($join) use ($user) {
                $join->on('products.id', '=', 'product_product_price_list.product_id')
                    ->where('product_product_price_list.product_price_list_id', '=', $user->product_price_list_id);
            });
        }

        if (isset($filters['price'])) {
            $query->where(function($query) use ($filters) {
                $query->where('products.price', '=', (float) $filters['price'])
                      ->orWhere('product_product_price_list.price', '=', (float) $filters['price']);
            });
        }
    
        $query->orderBy($sortBy, $sort ?? 'asc');

        $products = $query->paginate($perPage, ['*'], 'page', $page);
    
        return $products;
    }
    
    public function getById(User $user, string $productId) : Product|null
    {
        $product = Product::select('products.*', DB::raw('COALESCE(product_product_price_list.price, products.price) as price'))
        ->leftJoin('product_product_price_list', function ($join) use ($user) {
            $join->on('products.id', '=', 'product_product_price_list.product_id')
                ->where('product_product_price_list.product_price_list_id', '=', $user->product_price_list_id);
        })
        ->with('categories')
        ->where('products.published', true)
        ->where('products.id', $productId)
        ->first();

        return $product;  
    }
}
