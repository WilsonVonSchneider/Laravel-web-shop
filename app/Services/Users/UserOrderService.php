<?php

namespace App\Services\Users;

use App\Repositories\Users\UserOrderRepository;
use App\Services\Products\ProductService;
use App\Services\Users\UserOrderItemService;

use App\Models\Product;
use App\Models\UserOrderItem;
use App\Models\UserOrder;

class UserOrderService
{
    private $userOrderRepository;
    private $productService;
    private $userOrderItemService;

    public function __construct(UserOrderRepository $userOrderRepository, ProductService $productService, UserOrderItemService $userOrderItemService)
    {
        $this->userOrderRepository = $userOrderRepository;
        $this->productService = $productService;
        $this->userOrderItemService = $userOrderItemService;
    }

    public function getByProductIdForOrders($productId): Product 
    {
        return $this->productService->getByProductIdForOrders($productId);
    }
 
    public function getWithUserPrices($productId): Product 
    {
        return $this->productService->getById($productId);
    }

    public function createOrderItem(Product $product, string $orderId,  float $productWithUserPricesPrice) : UserOrderItem
    {
        return $this->userOrderItemService->create($product, $orderId,  $productWithUserPricesPrice);
    }

    public function create(array $data) : UserOrder
    {
        $order = $this->userOrderRepository->create($data);

        $totalPrice = 0;
        foreach ($data["products"] as $productId) {

            $product = $this->getByProductIdForOrders($productId);

            if (!$product) {
                continue;
            }

            $productWithUserPrices = $this->getWithUserPrices($productId);
           
            if ($productWithUserPrices) {
                $this->createOrderItem($product, $order->id, $productWithUserPrices->price);

                $totalPrice += $productWithUserPrices->price;
            }
        }

        // Calculate tax
        $taxRate = 25; 
        $tax = $totalPrice * $taxRate / 100;

        $finalOrder = $this->userOrderRepository->update($order, $totalPrice, $taxRate, $tax);
       
        return $finalOrder;
   }

  
}
