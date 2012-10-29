<?php
/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage Crud\Controller
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Listener;

use DkplusBase\Crud\Controller\CrudController;
use DkplusUnitTest\TestCase;

/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage Crud\Controller
 * @author     Oskar Bley <oskar@programming-php.net>
 * @covers     DkplusBase\Crud\Listener\AssignListener
 */
class PaginationRetrieveListenerTest extends TestCase
{
    /** @var \Zend\Http\Request|\PHPUnit_Framework_MockObject_MockObject */
    protected $request;

    /** @var PaginationRetrieveListener */
    protected $listener;

    protected function setUp()
    {
        parent::setUp();
        $this->request  = $this->getMockIgnoringConstructor('Zend\Http\Request');
        $this->service  = $this->getMockForAbstractClass('DkplusBase\Crud\\Service\ServiceInterface');
        $this->listener = new PaginationRetrievelListener('preRead', $this->request, $this->service);
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function attachesTheGetPaginatorMethodToTheEventManager()
    {
        $eventManager = $this->getMockForAbstractClass('Zend\EventManager\EventManagerInterface');
        $eventManager->expects($this->once())
                     ->method('attach')
                     ->with('preRead', array($this->listener, 'getPaginator'));

        $this->listener->attach($eventManager);
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function canDetachTheAttachedMethods()
    {
        $listener = 123;

        $eventManager = $this->getMockForAbstractClass('Zend\EventManager\EventManagerInterface');
        $eventManager->expects($this->at(0))
                     ->method('attach')
                     ->will($this->returnValue($listener));
        $eventManager->expects($this->at(1))
                     ->method('detach')
                     ->with($listener);

        $this->listener->attach($eventManager);
        $this->listener->detach($eventManager);
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function assignsTheEventParameterAsAlias()
    {
        $this->setUpController($this->controller);

        $paginator = $this->getMockIgnoringConstructor('Zend\Paginator\Paginator');

        $event = $this->getMockIgnoringConstructor('Zend\EventManager\Event');
        $event->expects($this->any())
              ->method('getParam')
              ->with('paginator')
              ->will($this->returnValue($paginator));
        $event->expects($this->any())
              ->method('getTarget')
              ->will($this->returnValue($this->controller));

        $this->expectsDsl()->toAssign($paginator, 'data');
        $this->listener->assign($event);
    }
}
