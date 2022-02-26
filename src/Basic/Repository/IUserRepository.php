<?php

namespace App\Basic\Repository;

use App\Basic\Entity\User;
use App\Basic\ValueObject\UserId;

interface IUserRepository
{
    public function find(UserId $userId): ?User;

    public function save(User $user);
}

