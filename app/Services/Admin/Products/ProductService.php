<?php

namespace App\Services\Admin\Products;

use App\Repositories\Admin\Products\ProductRepository;
use App\Services\Admin\Products\ProductCategoryService;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\Product;
use App\Models\ProductCategory;
use LengthException;

class ProductService
{
    private $productRepository;
    private $productCategoryService;

    public function __construct(ProductRepository $productRepository, ProductCategoryService $productCategoryService)
    {
        $this->productRepository = $productRepository;
        $this->productCategoryService = $productCategoryService;
    }

    public function getProductCategoryById($categoryId)
    {
        return $this->productCategoryService->getById($categoryId);
    }

    public function create(array $data): Product
    {
        $this->getProductCategoryById($data['product_category_id']);

        return $this->productRepository->create($data);
    }

    public function paginated(string|null $search, array $filters, string $sortBy, string $sort, int $perPage, int $page) : LengthAwarePaginator
    {
        return $this->productRepository->paginated($search, $filters, $sortBy, $sort, $perPage, $page);
    }

    public function getById(string $productId) : Product
    {
        $product =  $this->productRepository->getById($productId);

        if (!$product) {
            throw new \Exception('Product not found', 404);
        }

        return $product;
    }

    public function update(string $productId, array $data) : bool
    {
        $product = $this->getById($productId);

        if (!empty($data['product_category_id'])) {
            $this->getProductCategoryById($data['product_category_id']);
        }

        return $this->productRepository->update($product, $data);  
    }

    public function delete(string $productId) : void
    {
        $product = $this->getById($productId);

        $this->productRepository->delete($product);
    }
}
