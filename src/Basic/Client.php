<?php

use App\Basic\ApplicationService\IUserRegisterService;
use App\Basic\Command\UserRegisterCommand;

class Client
{
    private IUserRegisterService $userRegisterService;

    /**
     * @param string $id
     * @param string $name
     * @param string $mailAddress
     * @return void
     */
    public function register(string $id, string $name, string $mailAddress)
    {
        $command = new UserRegisterCommand($id, $name, $mailAddress);
        $this->userRegisterService->handle($command);
    }
}
