<?php

namespace App\Basic\ValueObject;

class FullName
{

    /**
     * @var string 姓
     */
    private $lastName;

    /**
     * @var string 名
     */
    private $firstName;


    /**
     * @param string $lastName 姓
     * @param string $firstName 名
     */
    public function __construct(string $lastName, string $firstName)
    {
        $this->lastName = $lastName;
        $this->firstName = $firstName;
    }

    /**
     * @return string 姓
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string 名
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }
}
