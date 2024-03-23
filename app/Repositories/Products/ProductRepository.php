<?php

namespace App\Repositories\Products;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\Product;

class ProductRepository
{
    public function paginated(string|null $search, array $filters, string $sortBy, string $sort, int $perPage, int $page) : LengthAwarePaginator
    {
        $query = Product::query()->with('categories');

        if ($search) {
            $query->where('name', 'ilike', '%' . $search . '%');
        }
        if (isset($filters['price'])) {
            $query->where('price', $filters['price']);
        }
        if (isset($filters['name'])) {
            $query->where('name', 'ilike', '%' . $filters['name'] . '%');
        }
        if (isset($filters['category'])) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->where('name', 'ilike', '%' . $filters['category'] . '%');
            });
        }

        $query->orderBy($sortBy, $sort ?? 'asc');

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function getById(string $productId) : Product|null
    {
        return Product::with('categories')->find($productId);
    }
}
