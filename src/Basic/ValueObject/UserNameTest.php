<?php

namespace App\Basic\ValueObject;

use PHPUnit\Framework\TestCase;

class UserNameTest extends TestCase
{
    public function test__construct_下限()
    {
        $userName = null;
        $exception = null;

        try {
            $userName = new UserName("abc");
        } catch (\InvalidArgumentException $e) {
            $exception = $e;
        }

        $this->assertSame("abc", $userName->toString());
        $this->assertNull($exception);
    }

    public function test__construct_下限−１()
    {
        $userName = null;
        $exception = null;

        try {
            $userName = new UserName("ab");
        } catch (\Exception $e) {
            $exception = $e;
        }

        $this->assertNull($userName);
        $this->assertInstanceOf(\InvalidArgumentException::class, $exception);
        $this->assertSame("ユーザー名は３文字以上です", $exception->getMessage());
    }
}
