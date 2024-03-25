<?php

namespace App\Repositories\Users;

use App\Models\UserOrderItem;
use App\Models\Product;

class UserOrderItemRepository
{
    public function create(Product $product, string $orderId, float $productWithUserPricesPrice) : UserOrderItem
    {
        $orderItem = new UserOrderItem();
        $orderItem->product_id = $product->id;
        $orderItem->user_order_id = $orderId;
        $orderItem->price = $product->price;
        $orderItem->final_price = $productWithUserPricesPrice; 
        $orderItem->save();
    
        return $orderItem;
   }
}
