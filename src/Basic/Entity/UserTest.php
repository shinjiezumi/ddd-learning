<?php

namespace App\Basic\Entity;

use App\Basic\ValueObject\UserId;
use App\Basic\ValueObject\UserName;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function test__construct_正常系()
    {
        $userId = new UserId("hoge");
        $userName = new UserName("fuga");

        $user = new User($userId, $userName);

        $this->assertEquals($userId, $user->getId());
        $this->assertEquals($userName, $user->getName());
    }

    public function test__construct_異常系()
    {
        $error = null;

        try {
            $userName = new UserName("hoge");

            $user = new User($userName, $userName);
        } catch (\Error $e) {
            $error = $e;
        }

        $this->assertNotNull($error);
    }
}
