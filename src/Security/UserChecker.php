<?php

namespace App\Security;

use App\Entity\User as AppUser;
use App\Enum\UserStatusEnum;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }

        if ($user->getStatus() === UserStatusEnum::Blocked) {
            throw new CustomUserMessageAccountStatusException('Your user account is blocked.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {

    }
}