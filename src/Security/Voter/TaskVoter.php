<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskVoter extends Voter
{
    public const MANAGE = 'TASK_MANAGE';
    public const DELETE = 'TASK_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::MANAGE, self::DELETE])
            && $subject instanceof \App\Entity\Task;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        $task = $subject;

        switch ($attribute) {
            case self::MANAGE:
                return $user === $task->getUser();
            case self::DELETE:
                if ($user === $task->getUser() || ($task->getUser() === null && in_array('ROLE_ADMIN', $user->getRoles()))){
                    return true;
                } else {
                    return false;
                }
        }
    }
}
