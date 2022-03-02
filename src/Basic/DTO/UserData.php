<?php

namespace App\Basic\DTO;

class UserData
{
    /**
     * @var string $id ユーザーID
     */
    private string $id;

    /**
     * @var string $name ユーザー名
     */
    private string $name;

    public function __construct(string $userId, string $name)
    {
        $this->id = $userId;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
