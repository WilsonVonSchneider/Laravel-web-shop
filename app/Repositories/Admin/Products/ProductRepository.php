<?php

namespace App\Repositories\Admin\Products;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\Product;
use App\Models\ProductCategory;

class ProductRepository
{
    public function create(array $data): Product
    {
        $data['sku'] = Product::generateSku();
        $product = Product::create($data);
        $product->categories()->attach($data['product_category_id']);
        
        return $product;
    }

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

    public function update(Product $product, array $data) : bool
    {
        $updatedProduct = $product->update($data);

        if (isset($data['product_category_id'])) {
            $categoryId = $data['product_category_id'];
            $product->categories()->sync($categoryId);
        }

        return $updatedProduct;
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }
}
