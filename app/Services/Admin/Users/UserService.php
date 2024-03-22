<?php

namespace App\Services\Admin\Users;

use App\Repositories\Admin\Users\UserRepository;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository) 
    {
        $this->userRepository = $userRepository;
    }

    public function paginated(string $sortBy, string $sort, int $perPage, int $page) : LengthAwarePaginator
    {
        return $this->userRepository->paginated($sortBy, $sort, $perPage, $page);
    }

    public function getById(string $userId) : User
    {
        $user =  $this->userRepository->getById($userId);

        if (!$user) {
            throw new \Exception('User not found', 404);
        }

        return $user;
    }

    public function update(string $userId, array $data) : bool
    {
        $user = $this->getById($userId);

        return $this->userRepository->update($user, $data);  
    }

    public function delete(string $userId) : void
    {
        $user = $this->getById($userId);
       
        $this->userRepository->delete($user);
    }
}
