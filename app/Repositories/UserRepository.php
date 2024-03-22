<?php

namespace App\Repositories;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\User;

class UserRepository
{
    public function update(User $user, array $data) : bool
    {
        return $user->update($data);
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
