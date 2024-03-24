<?php

namespace App\Repositories\Admin\Users;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\User;

class UserRepository
{
    public function paginated(string $sortBy, string $sort, int $perPage, int $page) : LengthAwarePaginator
    {
        $query = User::with('contracts', 'priceList.products');

        if ($sortBy && in_array($sortBy, ['name', 'email'])) {
            $query->orderBy($sortBy, $sort ?? 'asc');
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function getById(string $userId) : User|null
    {
        return User::with('contracts', 'priceList.products')->find($userId);
    }

    public function update(User $user, array $data) : bool
    {
        return $user->update($data);
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
