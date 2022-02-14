<?php

namespace App\Basic\ValueObject;

use PHPUnit\Framework\TestCase;

class ModelNumberTest extends TestCase
{
    public function testToString()
    {
        $modelNumber = new ModelNumber("test", 100, 1);

        $this->assertSame("test-100-1", $modelNumber->toString());
    }
}
