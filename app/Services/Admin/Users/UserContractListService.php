<?php

namespace App\Services\Admin\Users;

use App\Services\Admin\Users\UserService;
use App\Services\Admin\Products\ProductService;
use App\Repositories\Admin\Users\UserContractListRepository;

use App\Models\UserContractPrice;

class UserContractListService
{
    private $userContractListRepository;
    private $userService;
    private $productService;

    public function __construct(UserContractListRepository $userContractListRepository, ProductService $productService, UserService $userService)
    {
        $this->userContractListRepository = $userContractListRepository;
        $this->productService = $productService;
        $this->userService = $userService;
    }

    public function getProductById($productId)
    {
        return $this->productService->getById($productId);
    }

    public function getUserById($userId)
    {
        return $this->userService->getById($userId);
    }

    public function getByUserIdProductId(string $userId, string $productId) : UserContractPrice|null
    {
        return $this->userContractListRepository->getByUserIdProductId($userId, $productId);
    }

    public function validateUserProject(string $userId, string $productId) : UserContractPrice|null
    {
        $product = $this->getProductById($productId);

        if (!$product || !$product->published) {
            throw new \Exception('The product does not exist or is not published.', 404);

        }
        
        $user = $this->getUserById($userId);

        if (!$user) {
            throw new \Exception('The user does not exist.', 404);
        }

        return $this->getByUserIdProductId($user->id, $product->id);
    }

    public function create(array $data) : UserContractPrice
    {
        $userContractPrice = $this->validateUserProject($data['userId'], $data['productId']);

        if ($userContractPrice) {
            throw new \Exception('The contract already exists.', 404);
        }

        return $this->userContractListRepository->create($data);
    }

    public function update(array $data) : UserContractPrice
    {
        $userContractPrice = $this->validateUserProject($data['userId'], $data['productId']);

        if (!$userContractPrice) {
            throw new \Exception('The contract does not exists.', 404);
        }

        return $this->userContractListRepository->update($data, $userContractPrice);
    }

    public function delete(array $data) : void
    {
        $userContractPrice = $this->validateUserProject($data['userId'], $data['productId']);

        if (!$userContractPrice) {
            throw new \Exception('The contract does not exists.', 404);
        }

        $this->userContractListRepository->delete($userContractPrice);
    }
}

