<?php

namespace App\Basic\ValueObject;

class UserId
{

    /**
     * @var string 値
     */
    private $value;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        if ($value === '') {
            throw new \InvalidArgumentException("invalid user id");
        }

        $this->value = $value;
    }

    /**
     * @return string
     */
    public function toString():string
    {
        return $this->value;
    }
}
