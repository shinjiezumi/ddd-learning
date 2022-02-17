<?php

namespace App\Basic\ValueObject;

use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function test__construct_正常系()
    {
        $userId = new UserId("hoge");
        $userName = new UserName("fuga");

        $user = new User();
        $user->id = $userId;
        $user->name = $userName;

        $this->assertEquals($userId, $user->id);
        $this->assertEquals($userName, $user->name);
    }

    public function test__construct_異常系()
    {
        $error = null;

        try {
            $userName = new UserName("hoge");

            $user = new User();
            $user->id = $userName;
        } catch (\Error $e) {
            $error = $e;
        }

        $this->assertNotNull($error);
    }
}
