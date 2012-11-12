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
 * @covers     DkplusBase\Crud\Listener\NotFoundReplaceListener
 */
class NotFoundReplaceListenerTest extends TestCase
{
    /** @var CrudController */
    protected $controller;

    /** @var NotFoundReplaceListener */
    protected $listener;

    /** @var Options\NotFoundOptions|\PHPUnit_Framework_MockObject_MockObject */
    protected $options;

    /** @var \Zend\EventManager\EventInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $event;

    protected function setUp()
    {
        parent::setUp();
        $this->options    = $this->getMockIgnoringConstructor(
            'DkplusBase\Crud\Listener\Options\NotFoundReplaceOptions'
        );
        $this->listener   = new NotFoundReplaceListener($this->options);
        $this->controller = new CrudController();
        $this->event      = $this->getMockForAbstractClass('Zend\EventManager\EventInterface');
        $this->event->expects($this->any())
                    ->method('getTarget')
                    ->will($this->returnValue($this->controller));

        $this->setUpController($this->controller);
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
    public function replacesContentWithAnotherControllerAction()
    {
        $this->expectsDsl()->toReplaceContentWithControllerAction();

        $this->listener->execute($this->event);
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function setsAn404ResponseHeaderButIgnoresZfErrorHandling()
    {
        $this->expectsDsl()->toMarkPageAsNotFound(true);

        $this->listener->execute($this->event);
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function canConfigurateControllerActionForContentReplacing()
    {
        $controller  = 'my-controller';
        $action      = 'my-action';
        $routeParams = array('my-route' => 'params');

        $this->options->expects($this->any())
             ->method('getContentReplaceController')
             ->will($this->returnValue($controller));
        $this->options->expects($this->any())
             ->method('getContentReplaceAction')
             ->will($this->returnValue($action));
        $this->options->expects($this->any())
             ->method('getContentReplaceRouteParams')
             ->will($this->returnValue($routeParams));

        $this->expectsDsl()->toReplaceContentWithControllerAction($controller, $action, $routeParams);

        $this->listener->execute($this->event);
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function addsNo404NotFoundMessageUntilItIsConfigurated()
    {
        $this->expectsDsl()->toDoNotAddFlashMessages();

        $this->listener->execute($this->event);
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function canConfigurateA404NotFoundMessage()
    {
        $message   = 'could not found any data';

        $this->options->expects($this->any())
             ->method('hasErrorMessage')
             ->will($this->returnValue(true));
        $this->options->expects($this->any())
             ->method('getErrorMessage')
             ->will($this->returnValue($message));

        $this->expectsDsl()->toAddFlashMessage($message, 'notFound');

        $this->listener->execute($this->event);
    }
}
