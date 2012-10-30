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
 * @covers     DkplusBase\Crud\Listener\DeleteRedirectListener
 */
class DeleteRedirectListenerTest extends TestCase
{
    /** @var CrudController */
    protected $controller;

    /** @var \DkplusBase\Crud\Service\ServiceInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $service;

    /** @var \Zend\EventManager\MvcEvent|\PHPUnit_Framework_MockObject_MockObject */
    protected $event;

    /** @var \DkplusBase\Crud\Listener\Options\SuccessOptions|\PHPUnit_Framework_MockObject_MockObject */
    protected $options;

    /** @var FormSubmissionRedirectListener */
    protected $listener;

    protected function setUp()
    {
        parent::setUp();
        $this->options  = $this->getMockIgnoringConstructor('DkplusBase\Crud\Listener\Options\SuccessOptions');
        $this->service  = $this->getMockForAbstractClass('DkplusBase\Crud\Service\ServiceInterface');
        $this->listener = new DeleteRedirectListener($this->service, $this->options);

        $this->event      = $this->getMockIgnoringConstructor('Zend\Mvc\MvcEvent');
        $this->controller = new CrudController();
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
    public function deletesTheEntity()
    {
        $entity = $this->getMock('stdClass');

        $this->service->expects($this->any())
                      ->method('delete')
                      ->with($entity);

        $this->event->expects($this->any())
                    ->method('getParam')
                    ->with('entity')
                    ->will($this->returnValue($entity));

        $this->listener->execute($this->event);
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function getsTheSuccessMessageForTheDeletedEntity()
    {
        $entity = $this->getMock('stdClass');

        $this->event->expects($this->any())
                    ->method('getParam')
                    ->with('entity')
                    ->will($this->returnValue($entity));

        $this->options->expects($this->once())
                      ->method('getComputatedMessage')
                      ->with($entity);

        $this->listener->execute($this->event);
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function redirectsToRouteAfterDeletion()
    {
        $entity     = $this->getMock('stdClass');
        $route      = 'my-route';
        $parameters = array('my' => 'param');

        $this->event->expects($this->any())
                    ->method('getParam')
                    ->with('entity')
                    ->will($this->returnValue($entity));

        $this->options->expects($this->any())
                      ->method('getRedirectRoute')
                      ->will($this->returnValue($route));
        $this->options->expects($this->any())
                      ->method('getComputatedRedirectRouteParams')
                      ->with($entity)
                      ->will($this->returnValue($parameters));


        $this->expectsDsl()->toRedirectToRoute($route, $parameters);
        $this->listener->execute($this->event);
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function addsSuccessMessageAfterDeletion()
    {
        $message = 'deletion successful';

        $this->options->expects($this->any())
                      ->method('getcomputatedMessage')
                      ->will($this->returnValue($message));

        $this->expectsDsl()->toAddFlashMessage($message, 'success');
        $this->listener->execute($this->event);
    }
}
