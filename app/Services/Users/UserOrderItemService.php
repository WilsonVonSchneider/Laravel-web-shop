<?php

namespace App\Services\Users;

use App\Repositories\Users\UserOrderItemRepository;

use App\Models\Product;
use App\Models\UserOrderItem;

class UserOrderItemService
{
    private $userOrderItemRepository;

    public function __construct(UserOrderItemRepository $userOrderItemRepository)
    {
        $this->userOrderItemRepository = $userOrderItemRepository;
    }

    public function create(Product $product, string $orderId, float $productWithUserPricesPrice) : UserOrderItem
    {
        return $this->userOrderItemRepository->create($product, $orderId, $productWithUserPricesPrice);
    }

  
}
