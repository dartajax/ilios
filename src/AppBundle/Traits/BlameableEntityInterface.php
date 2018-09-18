<?php

namespace AppBundle\Traits;

use AppBundle\Entity\UserInterface;

/**
 * interface BlameableEntityInterface
 */
interface BlameableEntityInterface
{
    /**
     * @param  UserInterface $createdBy
     */
    public function setCreatedBy(UserInterface $createdBy);

    /**
     * Returns createdBy.
     *
     * @return UserInterface
     */
    public function getCreatedBy();

    /**
     * @param  UserInterface $updatedBy
     */
    public function setUpdatedBy(UserInterface $updatedBy);

    /**
     * @return UserInterface
     */
    public function getUpdatedBy();
}