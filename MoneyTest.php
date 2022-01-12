<?php

namespace App\Basic\ValueObject;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{

    public function testAdd_正常系()
    {
        $money = new Money(100, 'JPY');
        $target = new Money(200, 'JPY');

        $added = $money->Add($target);

        $this->assertEquals(300, $added->getAmount());
    }

    public function testAdd_通貨が異なる()
    {
        $money = new Money(100, 'JPY');
        $target = new Money(200, 'USD');

        $e = null;
        try {
            $money->Add($target);
        } catch (InvalidArgumentException $ie) {
            $e = $ie;
        }

        $this->assertInstanceOf(InvalidArgumentException::class, $e);
        $this->assertSame("通貨が異なります", $e->getMessage());
    }
}
