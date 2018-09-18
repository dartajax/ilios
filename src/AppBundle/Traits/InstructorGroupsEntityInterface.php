<?php

namespace AppBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use AppBundle\Entity\InstructorGroupInterface;

/**
 * Interface InstructorGroupsEntityInterface
 */
interface InstructorGroupsEntityInterface
{
    /**
     * @param Collection $instructorGroups
     */
    public function setInstructorGroups(Collection $instructorGroups);

    /**
     * @param InstructorGroupInterface $instructorGroup
     */
    public function addInstructorGroup(InstructorGroupInterface $instructorGroup);

    /**
     * @param InstructorGroupInterface $instructorGroup
     */
    public function removeInstructorGroup(InstructorGroupInterface $instructorGroup);

    /**
    * @return InstructorGroupInterface[]|ArrayCollection
    */
    public function getInstructorGroups();
}