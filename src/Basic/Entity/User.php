<?php

namespace App\Basic\Entity;

class User
{
    /**
     * @var UserId $id ユーザーID
     */
    private UserId $id;

    /**
     * @var string $name ユーザー名
     */
    private string $name;

    public function __construct(UserId $userId, string $name)
    {
        $this->id = $userId;
        $this->changeName($name);
    }

    /**
     * @param string $name
     * @return void
     */
    public function changeName(string $name)
    {
        if (mb_strlen($name) < 3) {
            throw new \InvalidArgumentException("invalid name");
        }

        $this->name = $name;
    }

    /**
     * @param User $other
     * @return bool
     */
    public function equals(User $other): bool
    {
        return $this->id->toString() === $other->id->toString();
    }
}
