<?php

namespace App\Basic\Entity;

class User
{
    /**
     * @var string $name ユーザー名
     */
    private string $name;

    public function __construct(string $name)
    {
        $this->changeName($name);
    }

    public function changeName(string $name)
    {
        if (mb_strlen($name) < 3) {
            throw new \InvalidArgumentException("invalid name");
        }

        $this->name = $name;
    }
}
