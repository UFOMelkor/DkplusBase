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
class ServiceTest extends TestCase
{
    /** @var MapperInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $mapper;

    /** @var FormStrategyInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $formStrategy;

    /** @var Service */
    private $service;

    /** @var \Zend\EventManager\EventManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $eventManager;

    protected function setUp()
    {
        parent::setUp();

        $this->mapper       = $this->getMockForAbstractClass('DkplusBase\Service\Crud\MapperInterface');
        $this->formStrategy = $this->getMockForAbstractClass('DkplusBase\Service\Crud\FormStrategyInterface');
        $this->eventManager = $this->getMockForAbstractClass('Zend\EventManager\EventManagerInterface');
        $this->service      = new Service($this->mapper, $this->formStrategy);
        $this->service->setEventManager($this->eventManager);
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     * @testdox is a crud service
     */
    public function isCrudService()
    {
        $this->assertInstanceOf('DkplusBase\Service\Crud\ServiceInterface', $this->service);
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function needsAnEventManager()
    {
        $this->assertInstanceOf('Zend\EventManager\EventManagerAwareInterface', $this->service);
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function providesTheEventManager()
    {
        $this->assertSame($this->eventManager, $this->service->getEventManager());
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function letTheFormStrategyCreateTheItem()
    {
        $data = array('foo' => 'bar', 'baz' => 'bar');

        $this->formStrategy->expects($this->once())
                           ->method('createItem')
                           ->with($data);

        $this->service->create($data);
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function storesTheCreatedItemIntoTheMapper()
    {
        $createdItem = $this->getMock('stdClass');

        $this->formStrategy->expects($this->any())
                           ->method('createItem')
                           ->will($this->returnValue($createdItem));

        $this->mapper->expects($this->once())
                     ->method('save')
                     ->with($createdItem);

        $this->service->create(array());
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function returnsTheCreatedItem()
    {
        $createdItem = $this->getMock('stdClass');

        $this->mapper->expects($this->any())
                     ->method('save')
                     ->will($this->returnValue($createdItem));

        $this->assertSame($createdItem, $this->service->create(array()));
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function getsTheItemToUpdateFromTheMapper()
    {
        $this->mapper->expects($this->once())
                     ->method('find')
                     ->with(34);

        $this->service->update(array(), 34);
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function letTheFormStrategyUpdateTheItem()
    {
        $item = $this->getMock('stdClass');
        $data = array('foo' => 'bar', 'baz' => 'bar');

        $this->mapper->expects($this->any())
                     ->method('find')
                     ->will($this->returnValue($item));

        $this->formStrategy->expects($this->once())
                           ->method('updateItem')
                           ->with($data, $item);

        $this->service->update($data, 15);
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function storesTheUpdatedItemIntoTheMapper()
    {
        $item = $this->getMock('stdClass');

        $this->formStrategy->expects($this->any())
                           ->method('updateItem')
                           ->will($this->returnValue($item));

        $this->mapper->expects($this->once())
                     ->method('save')
                     ->with($item);

        $this->service->update(array(), 15);
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function returnsTheUpdatedItem()
    {
        $updatedItem = $this->getMock('stdClass');

        $this->mapper->expects($this->any())
                     ->method('save')
                     ->will($this->returnValue($updatedItem));

        $this->assertSame($updatedItem, $this->service->update(array(), 10));
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function getsTheCreationFormFromFormStrategy()
    {
        $form = $this->getMock('Zend\Form\FormInterface');

        $this->formStrategy->expects($this->once())
                           ->method('getCreationForm')
                           ->will($this->returnValue($form));

        $this->assertSame($form, $this->service->getCreationForm());
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function findsItemsViaMapper()
    {
        $item = $this->getMock('stdClass');

        $this->mapper->expects($this->once())
                     ->method('find')
                     ->with(76)
                     ->will($this->returnValue($item));

        $this->assertSame($item, $this->service->get(76));
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     * @expectedException DkplusBase\Service\Exception\EntityNotFound
     */
    public function doesNotCatchExceptionsWhenFinding()
    {
        $exception = $this->getMockIgnoringConstructor('DkplusBase\Service\Exception\EntityNotFound');

        $this->mapper->expects($this->once())
                     ->method('find')
                     ->will($this->throwException($exception));

        $this->service->get(5);
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function deletesItemsViaMapper()
    {
        $this->mapper->expects($this->once())
                     ->method('delete')
                     ->with(86);

        $this->service->delete(86);
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     * @expectedException DkplusBase\Service\Exception\EntityNotFound
     */
    public function doesNotCatchExceptionsWhenDeleting()
    {
        $exception = $this->getMockIgnoringConstructor('DkplusBase\Service\Exception\EntityNotFound');

        $this->mapper->expects($this->once())
                     ->method('delete')
                     ->will($this->throwException($exception));

        $this->service->delete(2);
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function getsAllItemsViaMapper()
    {
        $items = array($this->getMock('stdClass'));

        $this->mapper->expects($this->once())
                     ->method('findAll')
                     ->will($this->returnValue($items));

        $this->assertSame($items, $this->service->getAll());
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function canUseSearchDataToGetAllItems()
    {
        $searchData = array('foo' => 'bar');

        $this->mapper->expects($this->once())
                     ->method('findAll')
                     ->with($searchData);

        $this->service->getAll($searchData);
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function canUseOrderingToGetAllItems()
    {
        $this->mapper->expects($this->once())
                     ->method('findAll')
                     ->with($this->anything(), 'name', 'ASC');

        $this->service->getAll(array(), 'name', 'ASC');
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function getsTheItemForTheUpdateFormFromTheMapper()
    {
        $this->mapper->expects($this->once())
                     ->method('find')
                     ->with(46);

        $this->service->getUpdateForm(46);
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function getsTheUpdateFormFromTheFormStrategy()
    {
        $item = $this->getMock('stdClass');
        $form = $this->getMockForAbstractClass('Zend\Form\FormInterface');

        $this->mapper->expects($this->any())
                     ->method('find')
                     ->will($this->returnValue($item));
        $this->formStrategy->expects($this->once())
                           ->method('getUpdateForm')
                           ->with($item)
                           ->will($this->returnValue($form));

        $this->assertSame($form, $this->service->getUpdateForm(46));
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     * @expectedException DkplusBase\Service\Exception\EntityNotFound
     */
    public function doesNotCatchExceptionsWhenGettingUpdateForm()
    {
        $exception = $this->getMockIgnoringConstructor('DkplusBase\Service\Exception\EntityNotFound');

        $this->mapper->expects($this->once())
                     ->method('find')
                     ->will($this->throwException($exception));

        $this->service->getUpdateForm(25);
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     * @testdox gets a paginator with an adapter from the mapper
     */
    public function getsPaginatorWithAnAdapterFromTheMapper()
    {
        $adapter = $this->getMock('Zend\Paginator\Adapter\AdapterInterface');

        $this->mapper->expects($this->once())
                     ->method('getPaginationAdapter')
                     ->will($this->returnValue($adapter));

        $paginator = $this->service->getPaginator(5, 10);
        $this->assertSame($adapter, $paginator->getAdapter());
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function canUseSearchDataToGetTheAdapterFromTheMapper()
    {
        $searchData = array('foo' => 'bar');
        $adapter    = $this->getMock('Zend\Paginator\Adapter\AdapterInterface');

        $this->mapper->expects($this->once())
                     ->method('getPaginationAdapter')
                     ->with($searchData)
                     ->will($this->returnValue($adapter));

        $this->service->getPaginator(5, 10, $searchData);
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function canUseSpecifiedOrderToGetTheAdapterFromTheMapper()
    {
        $adapter    = $this->getMock('Zend\Paginator\Adapter\AdapterInterface');

        $this->mapper->expects($this->once())
                     ->method('getPaginationAdapter')
                     ->with($this->anything(), 'name', 'ASC')
                     ->will($this->returnValue($adapter));

        $this->service->getPaginator(5, 10, array(), 'name', 'ASC');
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function assignsCurrentPageNumberToThePaginator()
    {
        $adapter = $this->getMock('Zend\Paginator\Adapter\AdapterInterface');

        $this->mapper->expects($this->once())
                     ->method('getPaginationAdapter')
                     ->will($this->returnValue($adapter));

        $paginator = $this->service->getPaginator(35, 10);
        $this->assertEquals(35, $paginator->getCurrentPageNumber());
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function assignsItemCountPerPageToThePaginator()
    {
        $adapter = $this->getMock('Zend\Paginator\Adapter\AdapterInterface');

        $this->mapper->expects($this->once())
                     ->method('getPaginationAdapter')
                     ->will($this->returnValue($adapter));

        $paginator = $this->service->getPaginator(1, 50);
        $this->assertEquals(50, $paginator->getItemCountPerPage());
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function triggersAnEventBeforeCreatingAnNewItem()
    {
        $this->eventManager->expects($this->once())
                           ->method('trigger')
                           ->with('crud.preCreate', $this->service);
        $this->service->create(array());
    }
}
