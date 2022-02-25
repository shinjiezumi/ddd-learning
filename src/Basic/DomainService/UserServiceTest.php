<?php

namespace App\Basic\DomainService;

use App\Basic\Entity\User;
use App\Basic\ValueObject\UserId;
use App\Basic\ValueObject\UserName;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    public function test__construct_正常系()
    {
        $userService = new UserService();

        $userId = new UserId('hoge');
        $userName = new UserName('hogehoge');
        $user = new User($userId, $userName);

        $exists = $userService->exists($user);
    }

    public function test__createUser_正常系()
    {
        $user = new User(new UserId('hoge'), new UserName('fuga'));

        $userService = new UserService();
        if ($userService->exists($user)) {
            // エラー処理
        }

        // DB保存など

    }
}
