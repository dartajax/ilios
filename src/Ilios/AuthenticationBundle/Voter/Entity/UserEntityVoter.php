<?php

namespace Ilios\AuthenticationBundle\Voter\Entity;

use Ilios\CoreBundle\Entity\Manager\PermissionManager;
use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class UserEntityVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class UserEntityVoter extends AbstractVoter
{
    /**
     * @var PermissionManager
     */
    protected $permissionManager;

    /**
     * @param PermissionManager $permissionManager
     */
    public function __construct(PermissionManager $permissionManager)
    {
        $this->permissionManager = $permissionManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof UserInterface && in_array($attribute, array(
            self::CREATE, self::VIEW, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param UserInterface $requestedUser
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $requestedUser, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($user->isRoot()) {
            return true;
        }

        switch ($attribute) {
            // at least one of these must be true.
            // 1. the requested user is the current user
            // 2. the current user has faculty/course director/developer role
            case self::VIEW:
                return (
                    $user->getId() === $requestedUser->getId()
                    || $this->userHasRole($user, ['Course Director', 'Faculty', 'Developer'])
                );
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                return $this->canCreateEditDeleteUser($user, $requestedUser);
                break;
        }
        return false;
    }

    /**
     * @param UserInterface $user
     * @param UserInterface $requestedUser
     * @return bool
     */
    protected function canCreateEditDeleteUser(UserInterface $user, UserInterface $requestedUser)
    {
        // only root users can edit/delete/create root users
        if (! $user->isRoot() && $requestedUser->isRoot()) {
            return false;
        }

        /**
         * Temporary mitigation for #1762
         */
        if ($this->usersAreIdentical($user, $requestedUser)) {
            return false;
        }

        // current user must have developer role and share the same school affiliations than the requested user.
        if ($this->userHasRole($user, ['Developer'])
            && ($requestedUser->getAllSchools()->contains($user->getSchool())
                || $this->permissionManager->userHasReadPermissionToSchools($user, $requestedUser->getAllSchools()))) {
            return true;
        }

        return false;
    }
}
