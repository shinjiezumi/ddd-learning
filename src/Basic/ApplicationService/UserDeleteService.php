<?php

namespace App\Basic\ApplicationService;

use App\Basic\Command\UserDeleteCommand;
use App\Basic\Repository\IUserRepository;
use App\Basic\ValueObject\UserId;

class UserDeleteService
{
    private IUserRepository $userRepository;

    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handle(UserDeleteCommand $command)
    {
        $targetId = new UserId($command->getId());
        $user = $this->userRepository->find($targetId);

        if ($user === null) {
            return;
        }

        $this->userRepository->delete($user);
    }
}