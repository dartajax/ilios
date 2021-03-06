<?php
namespace Tests\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\CourseManager;
use Mockery as m;

/**
 * Class CourseManagerTest
 * @package Tests\CoreBundle\\Entity\Manager
 */
class CourseManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Remove all mock objects
     */
    public function tearDown()
    {
        m::close();
    }
    
    /**
     * @covers \Ilios\CoreBundle\Entity\Manager\CourseManager::delete
     */
    public function testDeleteCourse()
    {
        $class = 'Ilios\CoreBundle\Entity\Course';
        $em = m::mock('Doctrine\ORM\EntityManager')
            ->shouldReceive('remove')->shouldReceive('flush')->mock();
        $repository = m::mock('Doctrine\ORM\Repository');
        $registry = m::mock('Doctrine\Bundle\DoctrineBundle\Registry')
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($repository)
            ->mock();
        
        $entity = m::mock($class);
        $manager = new CourseManager($registry, $class);
        $manager->delete($entity);
    }
}
