<?php

namespace App\Services\Admin\Products;

use App\Repositories\Admin\Products\ProductCategoryRepository;
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

    public function create(array $data): ProductCategory
    {
        if (!empty($data['parent_id'])) {
            $this->getById($data['parent_id']);
        }

        return $this->productCategoryRepository->create($data);
    }

    public function update(string $productCategoryId, array $data) : bool
    {
        if (!empty($data['parent_id'])) {
            $this->getById($data['parent_id']);
        }

        $productCategory = $this->getById($productCategoryId);

        return $this->productCategoryRepository->update($productCategory, $data);  
    }

    public function delete(string $productCategoryId) : void
    {
        $productCategory = $this->getById($productCategoryId);
       
        $this->productCategoryRepository->delete($productCategory);
    }
}

