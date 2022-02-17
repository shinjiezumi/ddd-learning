<?php

namespace App\Basic\ValueObject;


use InvalidArgumentException;

class Money
{
    /**
     * @var float $amount 金額
     */
    private float $amount;

    /**
     * @var string $currency 通貨
     */
    private string $currency;

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * コンストラクタ
     *
     * @param float $amount 金額
     * @param string $currency 通貨
     */
    public function __construct(float $amount, string $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    /**
     * 加算する
     *
     * @param Money $arg 加算対象
     * @return Money 加算後のお金
     */
    public function Add(Money $arg): Money
    {
        if ($this->currency !== $arg->currency) {
            throw new InvalidArgumentException("通貨が異なります");
        }

        return new Money($this->amount + $arg->amount, $this->currency);
    }
}
