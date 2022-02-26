<?php

namespace App\Basic\DomainService;

use App\Basic\Entity\User;
use App\Basic\Repository\IUserRepository;

class UserService
{
    private IUserRepository $userRepository;

    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
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