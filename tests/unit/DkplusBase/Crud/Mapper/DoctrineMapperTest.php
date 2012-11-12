<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Mapper
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Mapper;

use DkplusUnitTest\TestCase;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Mapper
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class DoctrineMapperTest extends TestCase
{
    /** @var \Doctrine\ORM\EntityManager|\PHPUnit_Framework_MockObject_MockObject */
    private $entityManager;

    /** @var DoctrineMapper */
    private $mapper;

    protected function setUp()
    {
        parent::setUp();

        $this->entityManager = $this->getMockIgnoringConstructor('Doctrine\ORM\EntityManager');
        $this->mapper        = new DoctrineMapper($this->entityManager, 'stdClass');
    }

    /**
     * @test
     * @group unit
     * @group Component/Mapper
     * @testdox is a crud mapper
     */
    public function isCrudMapper()
    {
        $this->assertInstanceOf('DkplusBase\Crud\Mapper\MapperInterface', $this->mapper);
    }

    /**
     * @test
     * @group unit
     * @group Component/Mapper
     */
    public function savesEntitiesByPuttingThemIntoTheEntityManager()
    {
        $entity = $this->getMock('stdClass');

        $this->entityManager->expects($this->at(0))
                            ->method('persist')
                            ->with($entity);
        $this->entityManager->expects($this->at(1))
                            ->method('flush');

        $this->mapper->save($entity);
    }

    /**
     * @test
     * @group unit
     * @group Component/Mapper
     */
    public function returnsTheSavedEntity()
    {
        $entity = $this->getMock('stdClass');

        $this->assertSame($entity, $this->mapper->save($entity));
    }

    /**
     * @test
     * @group unit
     * @group Component/Mapper
     */
    public function findsAnEntityUsingTheEntityManager()
    {
        $entity = $this->getMock('stdClass');

        $this->entityManager->expects($this->any())
                            ->method('find')
                            ->with('stdClass', 56)
                            ->will($this->returnValue($entity));

        $this->assertSame($entity, $this->mapper->find(56));
    }

    /**
     * @test
     * @group unit
     * @group Component/Mapper
     * @expectedException DkplusBase\Service\Exception\EntityNotFound
     */
    public function throwsAnExceptionWhenNoEntityHasBeenFound()
    {
        $this->entityManager->expects($this->any())
                            ->method('find')
                            ->with('stdClass', 71)
                            ->will($this->returnValue(null));

        $this->mapper->find(71);
    }

    /**
     * @test
     * @group unit
     * @group Component/Mapper
     */
    public function canDeleteEntities()
    {
        $entity = $this->getMock('stdClass');

        $this->entityManager->expects($this->once())
                            ->method('remove')
                            ->with($entity);
        $this->entityManager->expects($this->once())
                            ->method('flush');

        $this->mapper->delete($entity);
    }

    /**
     * @test
     * @group unit
     * @group Component/Mapper
     * @testdox creates a query builder to find entities
     */
    public function createsQueryBuilderToFindEntities()
    {
        $query = $this->getMock('stdClass', array('execute'));

        $queryBuilder = $this->getMockIgnoringConstructor('Doctrine\ORM\QueryBuilder');
        $queryBuilder->expects($this->once())
                     ->method('select')
                     ->with('e');
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with('stdClass', 'e');
        $queryBuilder->expects($this->any())
                     ->method('getQuery')
                     ->will($this->returnValue($query));

        $this->entityManager->expects($this->any())
                            ->method('createQueryBuilder')
                            ->will($this->returnValue($queryBuilder));

        $this->mapper->findAll();
    }

    /**
     * @test
     * @group unit
     * @group Component/Mapper
     */
    public function returnsTheExecutedQueryAsFoundResult()
    {
        $executionResult = array('firstEntity', 'secondEntity');

        $query = $this->getMock('stdClass', array('execute'));

        $query->expects($this->once())
              ->method('execute')
              ->will($this->returnValue($executionResult));

        $queryBuilder = $this->getMockIgnoringConstructor('Doctrine\ORM\QueryBuilder');

        $queryBuilder->expects($this->any())
                     ->method('getQuery')
                     ->will($this->returnValue($query));

        $this->entityManager->expects($this->any())
                            ->method('createQueryBuilder')
                            ->will($this->returnValue($queryBuilder));

        $this->assertSame($executionResult, $this->mapper->findAll());
    }
}
