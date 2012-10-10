<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Service\Crud
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Service\Crud;

use DkplusUnitTest\TestCase;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Service\Crud
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
     * @group Component/Service/Crud
     * @testdox is a crud mapper
     */
    public function isCrudMapper()
    {
        $this->assertInstanceOf('DkplusBase\Service\Crud\MapperInterface', $this->mapper);
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function savesItemsByPuttingThemIntoTheEntityManager()
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
     * @group Component/Service/Crud
     */
    public function returnsTheSavedItem()
    {
        $entity = $this->getMock('stdClass');

        $this->assertSame($entity, $this->mapper->save($entity));
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
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
     * @group Component/Service/Crud
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
     * @group Component/Service/Crud
     */
    public function canDeleteEntities()
    {
        $entity = $this->getMock('stdClass');

        $this->entityManager->expects($this->any())
                            ->method('find')
                            ->with('stdClass', 21)
                            ->will($this->returnValue($entity));

        $this->entityManager->expects($this->once())
                            ->method('remove')
                            ->with($entity);
        $this->entityManager->expects($this->once())
                            ->method('flush');

        $this->mapper->delete(21);
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     * @expectedException DkplusBase\Service\Exception\EntityNotFound
     */
    public function throwsAnExceptionWhenAnNotExistingEntityShouldBeDeleted()
    {
        $this->entityManager->expects($this->any())
                            ->method('find')
                            ->with('stdClass', 39)
                            ->will($this->returnValue(null));

        $this->mapper->delete(39);
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
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

        $this->mapper->findAll(array());
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function searchesForEntitiesThatHaveValuesLikeTheSearchParamsWhenFinding()
    {
        $query = $this->getMock('stdClass', array('execute'));

        $nameExpression  = $this->getMockIgnoringConstructor('Doctrine\ORM\Query\Expr\Comparison');
        $emailExpression = $this->getMockIgnoringConstructor('Doctrine\ORM\Query\Expr\Comparison');
        $andExpression   = $this->getMockIgnoringConstructor('Doctrine\ORM\Query\Expr\Andx');

        $expressionBuilder = $this->getMockIgnoringConstructor('Doctrine\ORM\Query\Expr');
        $expressionBuilder->expects($this->at(0))
                          ->method('like')
                          ->with('e.name', '%foo%')
                          ->will($this->returnValue($nameExpression));
        $expressionBuilder->expects($this->at(1))
                          ->method('like')
                          ->with('e.email', '%bar%')
                          ->will($this->returnValue($emailExpression));
        $expressionBuilder->expects($this->at(2))
                          ->method('andX')
                          ->with(array($nameExpression, $emailExpression))
                          ->will($this->returnValue($andExpression));

        $queryBuilder = $this->getMockIgnoringConstructor('Doctrine\ORM\QueryBuilder');

        $queryBuilder->expects($this->any())
                     ->method('expr')
                     ->will($this->returnValue($expressionBuilder));
        $queryBuilder->expects($this->once())
                     ->method('where')
                     ->with($andExpression);
        $queryBuilder->expects($this->any())
                     ->method('getQuery')
                     ->will($this->returnValue($query));

        $this->entityManager->expects($this->any())
                            ->method('createQueryBuilder')
                            ->will($this->returnValue($queryBuilder));

        $this->mapper->findAll(array('name' => 'foo', 'email' => 'bar'));
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function searchesForEntitiesThatHaveIntValuesThatAreEqualToTheTheSearchParamsWhenFinding()
    {
        $query = $this->getMock('stdClass', array('execute'));

        $yearExpression = $this->getMockIgnoringConstructor('Doctrine\ORM\Query\Expr\Comparison');
        $andExpression  = $this->getMockIgnoringConstructor('Doctrine\ORM\Query\Expr\Andx');

        $expressionBuilder = $this->getMockIgnoringConstructor('Doctrine\ORM\Query\Expr');
        $expressionBuilder->expects($this->at(0))
                          ->method('eq')
                          ->with('e.year', '1925')
                          ->will($this->returnValue($yearExpression));
        $expressionBuilder->expects($this->at(1))
                          ->method('andX')
                          ->with(array($yearExpression))
                          ->will($this->returnValue($andExpression));

        $queryBuilder = $this->getMockIgnoringConstructor('Doctrine\ORM\QueryBuilder');

        $queryBuilder->expects($this->any())
                     ->method('expr')
                     ->will($this->returnValue($expressionBuilder));
        $queryBuilder->expects($this->once())
                     ->method('where')
                     ->with($andExpression);
        $queryBuilder->expects($this->any())
                     ->method('getQuery')
                     ->will($this->returnValue($query));

        $this->entityManager->expects($this->any())
                            ->method('createQueryBuilder')
                            ->will($this->returnValue($queryBuilder));

        $this->mapper->findAll(array('year' => '1925'));
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
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

        $this->assertSame($executionResult, $this->mapper->findAll(array()));
    }
}
