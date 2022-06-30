<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{

    public function __construct()
    {
    }

    public function is_store($userId): bool
    {
        return User::find($userId)->is_store;
    }

    public function verifyUserExists($userId): bool
    {
        return (bool) User::find($userId);
    }

    public function find($userId): User
    {
        try {
            return User::find($userId);
        } catch (\Exception $exception) {
            return $exception;
        }
    }
}
