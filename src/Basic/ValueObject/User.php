<?php

namespace App\Basic\ValueObject;

class User
{
    /**
     * @var UserId $id ユーザーID
     */
    private $id;

    /**
     * @var UserName $name ユーザー名
     */
    private $name;

    /**
     * @param UserId $id
     * @param UserName $name
     */
    public function __construct(UserId $id, UserName $name)
    {
        $this->id = $id;
        $this->name = $name;
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
