<?php

namespace App\Basic\ValueObject;

use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function test__construct_正常系()
    {
        $userId = new UserId("hoge");
        $userName = new UserName("hoge");
        $user = new User($userId, $userName);

        $this->assertEquals($userId, $user->getId());
        $this->assertEquals($userName, $user->getName());
    }

    public function test__construct_異常系()
    {
        $error = null;

        try {
            $userId = new UserId("hoge");
            $userName = new UserName("hoge");
            $user = new User($userName, $userName);
        } catch (\Error $e) {
            $error = $e;
        }

        $this->assertNotNull($error);
    }
}
