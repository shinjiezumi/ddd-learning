<?php

namespace App\Basic;

use App\Basic\DomainService\UserService;
use App\Basic\Entity\User;
use App\Basic\Repository\IUserRepository;
use App\Basic\Repository\UserRepository;
use App\Basic\ValueObject\UserId;
use App\Basic\ValueObject\UserName;

/**
 * @deprecated ApplicationService参照
 */
class Program
{
    private IUserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    /**
     * @param string $userId
     * @param string $userName
     * @return void
     */
    public function createUser(string $userId, string $userName)
    {
        $user = new User(new UserId($userId), new UserName($userName));

        $userService = new UserService($this->userRepository);
        if ($userService->exists($user)) {
            throw new \InvalidArgumentException("${userName}は既に存在しています");
        }

        $this->userRepository->save($user);
    }
}
