<?php

namespace Ilios\CoreBundle\Traits;

use Ilios\CoreBundle\Entity\SchoolInterface;

/**
 * Class SchoolEntity
 * @package Ilios\CoreBundle\Traits
 */
trait SchoolEntity
{
    /**
     * @return SchoolInterface|null
     */
    public function getSchool()
    {
        return $this->school;
    }

    /**
     * @param SchoolInterface $school
     */
    public function setSchool(SchoolInterface $school)
    {
        $this->school = $school;
    }
}
