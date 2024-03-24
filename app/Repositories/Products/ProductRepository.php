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
        if (isset($filters['price'])) {
            $query->where('price', '=', (float) $filters['price']);
        }
    
        $query->selectRaw('products.*, get_min_price(?, products.id) as price', [$user->id]);
    
        $query->orderBy($sortBy, $sort ?? 'asc');
    
        $products = $query->paginate($perPage, ['*'], 'page', $page);
    
        return $products;
    }
    
    public function getById(User $user, string $productId) : Product|null
    {
        $product = Product::selectRaw('products.*, get_min_price(?, products.id) as price', [$user->id])
            ->with('categories')
            ->where('products.published', true)
            ->where('products.id', $productId)
            ->first();

        return $product;
    }
}
