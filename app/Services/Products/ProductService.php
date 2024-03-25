<?php

namespace App\Services\Products;

use App\Repositories\Products\ProductRepository;
use App\Services\UserService;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\Product;

class ProductService
{
    private $productRepository;
    private $userService;

    public function __construct(ProductRepository $productRepository, UserService $userService)
    {
        $this->productRepository = $productRepository;
        $this->userService = $userService;
    }

    public function getUserById($userId)
    {
        return $this->userService->getById($userId);
    }

    public function paginated(string|null $search, array $filters, string $sortBy, string $sort, int $perPage, int $page) : LengthAwarePaginator
    {
        $user = $this->getUserById(auth()->user()->id);

        return $this->productRepository->paginated($user, $search, $filters, $sortBy, $sort, $perPage, $page);
    }

    public function getById(string $productId) : Product
    {
        $user = $this->getUserById(auth()->user()->id);
        $product =  $this->productRepository->getById($user, $productId);

        if (!$product) {
            throw new \Exception('Product not found', 404);
        }

        return $product;
    }

    public function getByProductIdForOrders(string $productId) : Product|null
    {
        return $this->productRepository->getByIdForOrders($productId);
    }
}
