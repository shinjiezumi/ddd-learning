<?php

namespace App\Basic\Repository;

use App\Basic\Entity\User;
use App\Basic\ValueObject\UserId;

class UserRepository implements IUserRepository
{
    /**
     * @param UserId $userId
     * @return void
     */
    public function find(UserId $userId): ?User
    {
        // 略
        return null;
    }

    /**
     * @param User $user
     * @return void
     */
    public function save(User $user): void
    {
        // 略
    }
}