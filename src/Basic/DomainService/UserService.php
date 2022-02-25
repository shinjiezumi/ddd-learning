<?php

namespace App\Basic\DomainService;

use App\Basic\Entity\User;

class UserService
{
    /**
     * @param User $user
     * @return bool
     */
    public function exists(User $user): bool
    {
        // 重複を確認する処理(DB問い合わせ)を記述する。割愛
        return true;
    }
}