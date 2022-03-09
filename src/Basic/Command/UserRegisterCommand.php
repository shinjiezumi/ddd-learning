<?php

namespace App\Basic\Command;

class UserRegisterCommand
{
    private string $id;

    private string $name;

    private string $mailAddress;

    /**
     * @param string $id
     * @param string|null $name
     * @param string|null $mailAddress
     */
    public function __construct(string $id, ?string $name, ?string $mailAddress)
    {
        $this->id = $id;
        $this->name = $name;
        $this->mailAddress = $mailAddress;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getMailAddress(): ?string
    {
        return $this->mailAddress;
    }
}