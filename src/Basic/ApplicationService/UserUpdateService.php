<?php

namespace App\Basic\ApplicationService;

use App\Basic\Command\UserUpdateCommand;
use App\Basic\Repository\IUserRepository;
use App\Basic\ValueObject\UserId;
use App\Basic\ValueObject\UserName;

class UserUpdateService
{
    private IUserRepository $userRepository;

    /**
     * @param IUserRepository $userRepository
     */
    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param UserUpdateCommand $command
     * @return void
     */
    public function update(UserUpdateCommand $command)
    {
        $targetId = new UserId($command->getId());
        $user = $this->userRepository->find($targetId);

        if ($user === null) {
            throw new \InvalidArgumentException('ユーザー情報がありません');
        }

        $name = $command->getName();
        if ($name !== null) {
            $userName = new UserName($name);
            $user->changeName($userName);
        }

        $this->userRepository->save($user);
    }
}