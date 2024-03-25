<?php

namespace App\Services\Users;

use App\Models\DiscountModifier;
use App\Repositories\Users\UserOrderModifierRepository;

class UserOrderModifierService
{
    private $userOrderModifierRepository;

    public function __construct(UserOrderModifierRepository $userOrderModifierRepository)
    {
        $this->userOrderModifierRepository = $userOrderModifierRepository;
    }


    public function getTax() : int
    {
        return $this->userOrderModifierRepository->getTax();
    }

    public function getDiscount(float $totalPrice) : DiscountModifier|null
    {
        return $this->userOrderModifierRepository->getDiscount($totalPrice);
    }
}
