<?php

namespace App\Basic\ApplicationService;

use App\Basic\Command\UserRegisterCommand;

class MockUserRegisterService implements IUserRegisterService
{
    /**
     * @param UserRegisterCommand $command
     * @return void
     */
    public function handle(UserRegisterCommand $command): void
    {
        // nop
    }
}