<?php
/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Listener;

use DkplusBase\Crud\Controller\CrudController;
use DkplusControllerDsl\Test\TestCase;

/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 * @covers     DkplusBase\Crud\Listener\AjaxFormSupportListener
 */
class AjaxDisableLayoutListenerTest extends TestCase
{
    /** @var CrudController */
    protected $controller;

    /** @var \Zend\EventManager\EventInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $event;

    /** @var AjaxDisableLayoutListener */
    protected $listener;

    protected function setUp()
    {
        parent::setUp();

        $this->listener   = new AjaxDisableLayoutListener();
        $this->event      = $this->getMockForAbstractClass('Zend\EventManager\EventInterface');
        $this->controller = new CrudController();

        $this->setUpController($this->controller);
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function isCrudListener()
    {
        $this->assertInstanceOf(
            'DkplusBase\Crud\Listener\ListenerInterface',
            $this->listener
        );
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function disablesTheLayoutWhenAnAjaxRequestIsDetected()
    {
        $dsl = $this->getDslMockBuilder()
                    ->withMockedPhrases(array('onAjaxRequest'))
                    ->getMock();
        $ajaxDsl = $this->expectsDsl()->toDisableLayout();

        $dsl->expects($this->once())
            ->method('onAjaxRequest')
            ->with($ajaxDsl)
            ->will($this->returnSelf());

        $this->event->expects($this->any())
                    ->method('getParam')
                    ->with('result')
                    ->will($this->returnValue($this->controller->dsl()));
        $this->event->expects($this->any())
                    ->method('getTarget')
                    ->will($this->returnValue($this->controller));

        $this->listener->execute($this->event);
    }
}
