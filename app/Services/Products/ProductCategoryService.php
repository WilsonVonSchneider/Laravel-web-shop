<?php

namespace App\Services\Products;

use App\Repositories\Products\ProductCategoryRepository;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\ProductCategory;

class ProductCategoryService
{
    private $productCategoryRepository;

    public function __construct(ProductCategoryRepository $productCategoryRepository)
    {
        $this->productCategoryRepository = $productCategoryRepository;
    }

    public function paginated(string|null $search, string $sortBy, string $sort, int $perPage, int $page) : LengthAwarePaginator
    {
        return $this->productCategoryRepository->paginated($search, $sortBy, $sort, $perPage, $page);
    }

    public function getById(string $productCategoryId) : ProductCategory
    {
        $productCategory =  $this->productCategoryRepository->getById($productCategoryId);

        if (!$productCategory) {
            throw new \Exception('Product Category not found', 404);
        }

        return $productCategory;
    }
}

