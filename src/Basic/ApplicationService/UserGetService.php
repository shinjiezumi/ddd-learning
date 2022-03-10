<?php

namespace App\Basic\ApplicationService;

use App\Basic\DTO\UserData;
use App\Basic\Repository\IUserRepository;
use App\Basic\ValueObject\UserId;

class UserGetService
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
     * @param string $userId
     * @return UserData
     */
    public function handle(string $userId): ?UserData
    {
        $target = new UserId($userId);
        $user = $this->userRepository->find($target);
        if ($user === null) {
            return null;
        }

        return new UserData($user);
    }
}