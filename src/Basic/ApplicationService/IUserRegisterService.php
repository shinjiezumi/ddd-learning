<?php

namespace App\Basic\ApplicationService;

use App\Basic\Command\UserRegisterCommand;

interface IUserRegisterService
{
    public function handle(UserRegisterCommand $command): void;
}
