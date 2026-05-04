<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function get(string $id): ?User
    {
        return User::query()->find($id, ['users.id', 'users.email']);
    }
}
