<?php

namespace App\Basic\ValueObject;

use PHPUnit\Framework\TestCase;

class FullNameTest extends TestCase
{
    public function testGetLastName()
    {
        $fullName = new FullName('yamada', 'taro');

        $this->assertEquals('yamada', $fullName->getLastName());
    }

    public function testGetFirstName()
    {
        $fullName = new FullName('yamada', 'taro');

        $this->assertEquals('taro', $fullName->getFirstName());
    }
}
