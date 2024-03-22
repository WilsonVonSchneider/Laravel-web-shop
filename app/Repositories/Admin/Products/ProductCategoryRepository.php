<?php

namespace App\Repositories\Admin\Products;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\ProductCategory;

class ProductCategoryRepository
{

    public function paginated(string|null $search, string $sortBy, string $sort, int $perPage, int $page) : LengthAwarePaginator
    {
        $query = ProductCategory::query();

        if ($search) {
            $query->where('name', 'ilike', '%' . $search . '%');
        }

        if ($sortBy && in_array($sortBy, ['name'])) {
            $query->orderBy($sortBy, $sort ?? 'asc');
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function getById(string $productCategoryId) : ProductCategory|null
    {
        return ProductCategory::with('children', 'parent')->find($productCategoryId);
    }

    public function create(array $data): ProductCategory
    {
        return ProductCategory::create($data);
    }

    public function update(ProductCategory $productCategory, array $data) : bool
    {
        return $productCategory->update($data);
    }

    public function delete(ProductCategory $productCategory): void
    {
        $productCategory->delete();
    }
}
