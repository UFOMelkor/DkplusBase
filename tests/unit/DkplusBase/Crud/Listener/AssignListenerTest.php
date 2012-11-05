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
        $this->listener   = new AssignListener('data', 'paginator', 'crud/read');
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function isCrudListener()
    {
        $this->assertInstanceOf('DkplusBase\Crud\Listener\ListenerInterface', $this->listener);
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function returnsDsl()
    {
        $this->setUpController($this->controller);

        $event = $this->getMockIgnoringConstructor('Zend\Mvc\MvcEvent');
        $event->expects($this->any())
              ->method('getTarget')
              ->will($this->returnValue($this->controller));

        $this->assertDsl($this->listener->execute($event));
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

        $event = $this->getMockIgnoringConstructor('Zend\Mvc\MvcEvent');
        $event->expects($this->any())
              ->method('getParam')
              ->with('paginator')
              ->will($this->returnValue($paginator));
        $event->expects($this->any())
              ->method('getTarget')
              ->will($this->returnValue($this->controller));

        $dsl = $this->getDslMockBuilder()
                    ->withMockedPhrases(array('assign'))
                    ->getMock();
        $dsl->expects($this->at(0))
            ->method('assign')
            ->with($paginator)
            ->will($this->returnSelf());
        $dsl->expects($this->at(1))
            ->method('__call')
            ->with('as', array('data'))
            ->will($this->returnSelf());
        $this->listener->execute($event);
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function rendersTheTemplate()
    {
        $this->setUpController($this->controller);

        $event = $this->getMockIgnoringConstructor('Zend\Mvc\MvcEvent');
        $event->expects($this->any())
              ->method('getTarget')
              ->will($this->returnValue($this->controller));

        $this->expectsDsl()->toRender('crud/read');
        $this->listener->execute($event);
    }
}
