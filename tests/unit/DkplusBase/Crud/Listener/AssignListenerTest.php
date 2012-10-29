<?php
/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage Crud\Controller
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Listener;

use DkplusBase\Crud\Controller\CrudController;
use DkplusControllerDsl\Test\TestCase;

/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage Crud\Controller
 * @author     Oskar Bley <oskar@programming-php.net>
 * @covers     DkplusBase\Crud\Listener\AssignListener
 */
class AssignListenerTest extends TestCase
{
    /** @var CrudController */
    protected $controller;

    /** @var AssignListener */
    protected $listener;

    protected function setUp()
    {
        parent::setUp();
        $this->controller = new CrudController();
        $this->listener   = new AssignListener('read', 'data', 'paginator');
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function attachesTheAssignMethodToTheEventManager()
    {
        $eventManager = $this->getMockForAbstractClass('Zend\EventManager\EventManagerInterface');
        $eventManager->expects($this->once())
                     ->method('attach')
                     ->with('read', array($this->listener, 'assign'));

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
