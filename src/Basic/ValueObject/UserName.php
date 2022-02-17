<?php

namespace App\Basic\ValueObject;

class UserName
{
    /**
     * @var string $value
     */
    private string $value;


    /**
     * コンストラクタ
     *
     * @param string $value
     */
    public function __construct(string $value)
    {
        if (mb_strlen($value) < 3) {
            throw new \InvalidArgumentException("ユーザー名は３文字以上です");
        }

        $this->value = $value;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->value;
    }
}
