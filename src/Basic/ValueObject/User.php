<?php

namespace App\Basic\ValueObject;

class User
{
    /**
     * @var UserId $id ユーザーID
     */
    public UserId $id;

    /**
     * @var UserName $name ユーザー名
     */
    public UserName $name;

}
