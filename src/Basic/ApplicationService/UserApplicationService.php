<?php

namespace App\Basic\ApplicationService;

use App\Basic\DomainService\UserService;
use App\Basic\DTO\UserData;
use App\Basic\Entity\User;
use App\Basic\Repository\IUserRepository;
use App\Basic\ValueObject\UserId;
use App\Basic\ValueObject\UserName;

class UserApplicationService
{
    private UserService $userService;
    private IUserRepository $userRepository;

    public function __construct(UserService $userService, IUserRepository $userRepository)
    {
        $this->userService = $userService;
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $userId
     * @return User|null
     */
    public function get(string $userId): ?User
    {
        $target = new UserId($userId);
        $user = $this->userRepository->find($target);
        if ($user === null) {
            return null;
        }

        return new UserData($user->getId()->toString(), $user->getName()->toString());
    }

    /**
     * @param string $userId
     * @param string $userName
     * @return void
     */
    public function register(string $userId, string $userName)
    {
        $user = new User(new UserId($userId), new UserName($userName));

        if ($this->userService->exists($user)) {
            throw new \InvalidArgumentException("ユーザーは既に存在しています");
        }

        $this->userRepository->save($user);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function exists(User $user): bool
    {
        $user = $this->userRepository->find($user->getId());

        return $user !== null;
    }
}