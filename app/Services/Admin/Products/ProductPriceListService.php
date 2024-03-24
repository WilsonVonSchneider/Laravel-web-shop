<?php

namespace App\Services\Admin\Products;

use App\Repositories\Admin\Products\ProductPriceListRepository;
use App\Services\Admin\Products\ProductService;
use App\Services\UserService;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\ProductPriceList;

class ProductPriceListService
{
    private $productPriceListRepository;
    private $productService;
    private $userService;

    public function __construct(ProductPriceListRepository $productPriceListRepository, ProductService $productService, UserService $userService)
    {
        $this->productPriceListRepository = $productPriceListRepository;
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

    public function paginated(string|null $search, string $sortBy, string $sort, int $perPage, int $page) : LengthAwarePaginator
    {
        return $this->productPriceListRepository->paginated($search, $sortBy, $sort, $perPage, $page);
    }

    public function getById(string $productPriceListId) : ProductPriceList
    {
        $productPriceList =  $this->productPriceListRepository->getById($productPriceListId);

        if (!$productPriceList) {
            throw new \Exception('Product Price List not found', 404);
        }

        return $productPriceList;
    }

    public function create(array $data) : ProductPriceList
    {
        return $this->productPriceListRepository->create($data);
    }

    public function update(string $productPriceListId, array $data) : bool
    {
        $productPriceList = $this->getById($productPriceListId);

        return $this->productPriceListRepository->update($productPriceList, $data);  
    }

    public function delete(string $productPriceListId) : void
    {
        $productPriceList = $this->getById($productPriceListId);
       
        $this->productPriceListRepository->delete($productPriceList);
    }

    public function assign(string $productPriceListId, string $productId, float $price) : bool
    {
        $productPriceList = $this->getById($productPriceListId);

        if (!$productPriceList || !$productPriceList->active) {
            return response()->json(['error' => 'The product price list does not exist or is not active.'], 404);
        }

        $product = $this->getProductById($productId);

        if (!$product || !$product->published) {
            return response()->json(['error' => 'The product does not exist or is not published.'], 404);
        }

        return $this->productPriceListRepository->assign($productPriceList, $productId, $price);
    }

    public function remove(string $productPriceListId, string $productId) : bool
    {
        $productPriceList = $this->getById($productPriceListId);

        if (!$productPriceList || !$productPriceList->active) {
            return response()->json(['error' => 'The product price list does not exist or is not active.'], 404);
        }

        $product = $this->getProductById($productId);

        if (!$product || !$product->published) {
            return response()->json(['error' => 'The product does not exist or is not published.'], 404);
        }

        return $this->productPriceListRepository->remove($productPriceList, $productId);
    }

    public function updatePrice(string $productPriceListId, string $productId, float $price) : bool
    {
        $productPriceList = $this->getById($productPriceListId);

        if (!$productPriceList || !$productPriceList->active) {
            return response()->json(['error' => 'The product price list does not exist or is not active.'], 404);
        }

        $product = $this->getProductById($productId);

        if (!$product || !$product->published) {
            return response()->json(['error' => 'The product does not exist or is not published.'], 404);
        }

        return $this->productPriceListRepository->updatePrice($productPriceList, $productId, $price);
    }

    public function updateUserPriceList(string $productPriceListId, string $userId) : bool 
    {
        $productPriceList = $this->getById($productPriceListId);

        if (!$productPriceList || !$productPriceList->active) {
            return response()->json(['error' => 'The product price list does not exist or is not active.'], 404);
        }

        $user = $this->getUserById($userId);

        return $this->productPriceListRepository->updateUserPriceList($productPriceListId, $user);
    }

}

