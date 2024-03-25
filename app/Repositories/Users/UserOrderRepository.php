<?php

namespace App\Repositories\Users;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\UserOrder;

class UserOrderRepository
{
  public function create(array $data) : UserOrder
  {
    $order = new UserOrder();
    $order->name = $data["name"];
    $order->email = $data["email"];
    $order->phone = $data["phone"];
    $order->address = $data["address"];
    $order->city_country = $data["city_country"];
    $order->user_id = $data["userId"];
    $order->save();

    return $order;
  }

  public function update(UserOrder $order, float $totalPrice, int $taxRate, float $tax) : UserOrder
  {
     $order->total = $totalPrice;
     $order->tax = $taxRate;
     $order->tax_amount = $tax;
     $order->total_with_tax = $totalPrice + $tax;
     $order->save();

     return $order;
  }
}
