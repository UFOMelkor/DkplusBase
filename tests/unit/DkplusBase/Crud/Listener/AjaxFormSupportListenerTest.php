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
 * @covers     DkplusBase\Crud\Listener\FormSubmissionRedirectListener
 */
class AjaxFormSupportListenerTest extends TestCase
{
    /** @var CrudController */
    protected $controller;

    /** @var \Zend\EventManager\MvcEvent|\PHPUnit_Framework_MockObject_MockObject */
    protected $event;

    /** @var FormSubmissionRedirectListener */
    protected $listener;

    protected function setUp()
    {
        parent::setUp();

        $this->listener   = new AjaxFormSupportListener();
        $this->event      = $this->getMockIgnoringConstructor('Zend\Mvc\MvcEvent');
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
    public function assignsTheFormMessagesWhenAnAjaxRequestIsDetected()
    {
        $dsl = $this->getDslMockBuilder()
                    ->withMockedPhrases(array('onAjaxRequest'))
                    ->getMock();

        $ajaxDsl = $this->getDslMockBuilder()
                        ->withMockedPhrases(array('assign', 'formMessages'))
                        ->getMock();
        $ajaxDsl->expects($this->once())
                ->method('assign')
                ->will($this->returnSelf());
        $ajaxDsl->expects($this->once())
                ->method('formMessages')
                ->will($this->returnSelf());

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
