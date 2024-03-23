<?php

namespace App\Services\Products;

use App\Repositories\Products\ProductRepository;
use App\Services\Products\ProductCategoryService;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\Product;

class ProductService
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
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
}
