<?php

namespace App\Basic\ValueObject;

use PHPUnit\Framework\TestCase;

class ModelNumberTest extends TestCase
{
    public function test__construct_正常系()
    {
        $modelNumber = null;
        $exception = null;

        try {
            $modelNumber = new ModelNumber("test", 100, 1);
        } catch (\Exception $e) {
            $exception = $e;
        }

        $this->assertSame("test-100-1", $modelNumber->toString());
        $this->assertNull($exception);
    }

    public function test__construct_製品コードが不正()
    {
        $modelNumber = null;
        $exception = null;

        try {
            $modelNumber = new ModelNumber("", 100, 1);
        } catch (\InvalidArgumentException $e) {
            $exception = $e;
        }

        $this->assertNull($modelNumber);
        $this->assertInstanceOf(\InvalidArgumentException::class, $exception);
        $this->assertSame("invalid product code", $exception->getMessage());
    }

    public function test__construct_枝番が不正()
    {
        $modelNumber = null;
        $exception = null;

        try {
            $modelNumber = new ModelNumber("test", -1, 1);
        } catch (\InvalidArgumentException $e) {
            $exception = $e;
        }

        $this->assertNull($modelNumber);
        $this->assertInstanceOf(\InvalidArgumentException::class, $exception);
        $this->assertSame("invalid branch", $exception->getMessage());
    }

    public function test__construct_ロット番号が不正()
    {
        $modelNumber = null;
        $exception = null;

        try {
            $modelNumber = new ModelNumber("test", 100, -1);
        } catch (\InvalidArgumentException $e) {
            $exception = $e;
        }

        $this->assertNull($modelNumber);
        $this->assertInstanceOf(\InvalidArgumentException::class, $exception);
        $this->assertSame("invalid lot", $exception->getMessage());
    }
}
