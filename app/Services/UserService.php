<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository) 
    {
        $this->userRepository = $userRepository;
    }


    public function update(User $user, array $data) : bool
    {
        return $this->userRepository->update($user, $data);  
    }

    public function delete(User $user) : void
    {
        $this->userRepository->delete($user);
    }

    public function getById(string $userId) : User 
    {
        return $this->userRepository->getById($userId);
    }
}
