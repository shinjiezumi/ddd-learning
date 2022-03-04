<?php

namespace App\Basic\DTO;

use App\Basic\Entity\User;

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

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->id = $user->getId()->toString();
        $this->name = $user->getName()->toString();
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
