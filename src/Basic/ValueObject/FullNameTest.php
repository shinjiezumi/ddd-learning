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


    public function testEquals_等しい()
    {
        $name1 = new FullName('yamada', 'taro');
        $name2 = new FullName('yamada', 'taro');

        $this->assertTrue($name1->equals($name2));
    }

    public function testEquals_等しくない()
    {
        $name1 = new FullName('yamada', 'taro');
        $name2 = new FullName('yamada', 'jiro');

        $this->assertFalse($name1->equals($name2));
    }
}
