<?php

namespace App\Repositories\Admin\Users;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\UserContractPrice;

class UserContractListRepository
{
    public function create(array $data) : UserContractPrice
    {
        $userContractPrice = new UserContractPrice();
        $userContractPrice->user_id = $data['userId'];
        $userContractPrice->price = $data['price'];
        $userContractPrice->product_id = $data['productId'];
        $userContractPrice->save();
        
        return $userContractPrice;
    }

    public function getByUserIdProductId($userId, $productId) : UserContractPrice|null
    {
        return UserContractPrice::where('user_id', $userId)->where('product_id', $productId)->first();
    }

    public function update(array $data, $userContractPrice): UserContractPrice
    {
        $userContractPrice->user_id = $data['userId'];
        $userContractPrice->price = $data['price'];
        $userContractPrice->product_id = $data['productId'];
        $userContractPrice->save();
        
        return $userContractPrice;
    }

    public function delete($userContractPrice) : void
    {
        $userContractPrice->delete();
    }
}
