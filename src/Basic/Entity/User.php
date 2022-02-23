<?php

namespace App\Basic\Entity;

use App\Basic\ValueObject\UserId;
use App\Basic\ValueObject\UserName;

class User
{
    /**
     * @var UserId $id ユーザーID
     */
    private UserId $id;

    /**
     * @var UserName $name ユーザー名
     */
    private UserName $name;

    public function __construct(UserId $userId, UserName $name)
    {
        $this->id = $userId;
        $this->changeName($name);
    }

    /**
     * @param UserName $name
     * @return void
     */
    public function changeName(UserName $name)
    {
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

    /**
     * @return UserId
     */
    public function getId(): UserId
    {
        return $this->id;
    }

    /**
     * @return UserName
     */
    public function getName(): UserName
    {
        return $this->name;
    }
}
