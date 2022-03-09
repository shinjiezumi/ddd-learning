<?php

namespace App\Basic\ApplicationService;

use App\Basic\Command\UserRegisterCommand;
use App\Basic\DomainService\UserService;
use App\Basic\Entity\User;
use App\Basic\Repository\IUserRepository;
use App\Basic\ValueObject\UserId;
use App\Basic\ValueObject\UserName;

class UserRegisterService
{
    private UserService $userService;
    private IUserRepository $userRepository;

    public function __construct(UserService $userService, IUserRepository $userRepository)
    {
        $this->userService = $userService;
        $this->userRepository = $userRepository;
    }

    /**
     * @param UserRegisterCommand $command
     * @return void
     */
    public function handle(UserRegisterCommand $command)
    {
        $user = new User(new UserId($command->getId()), new UserName($command->getName()));

        if ($this->userService->exists($user)) {
            throw new \InvalidArgumentException("ユーザーは既に存在しています");
        }

        $this->userRepository->save($user);
    }
}