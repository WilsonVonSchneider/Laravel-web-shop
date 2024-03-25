<?php

namespace App\Repositories\Users;

use App\Models\TaxModifier;
use App\Models\DiscountModifier;


class UserOrderModifierRepository
{
   public function getTax() : int 
   {
    $tax = TaxModifier::where('type', 'tax')->where('active', true)->value('amount') ?? 25;
    
    return $tax;
   }

   public function getDiscount(float $totalPrice) : DiscountModifier|null
   {
    return DiscountModifier::where('active', true)->where('type', '<', $totalPrice)->first();
   }
}
