<?php
/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Listener;

use DkplusUnitTest\TestCase;

/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class IdentifierProviderListenerTest extends TestCase
{
    /** @var \Zend\Mvc\Router\RouteMatch|\PHPUnit_Framework_MockObject_MockObject */
    protected $routeMatch;

    /** @var \Zend\EventManager\EventInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $event;

    protected function setUp()
    {
        parent::setUp();
        $this->routeMatch = $this->getMockIgnoringConstructor('Zend\Mvc\Router\RouteMatch');
        $mvcEvent         = $this->getMock('Zend\Mvc\MvcEvent');
        $mvcEvent->expects($this->any())
                 ->method('getRouteMatch')
                 ->will($this->returnValue($this->routeMatch));
        $controller       = $this->getMock('stdClass', array('getEvent'));
        $controller->expects($this->any())
                   ->method('getEvent')
                   ->will($this->returnValue($mvcEvent));
        $this->event      = $this->getMockForAbstractClass('Zend\EventManager\EventInterface');
        $this->event->expects($this->any())
                    ->method('getTarget')
                    ->will($this->returnValue($controller));
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function isCrudListener()
    {
        $this->assertInstanceOf('DkplusBase\Crud\Listener\ListenerInterface', new IdentifierProviderListener());
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function usesIdAsDefaultRouteMatchParameterForIdentifier()
    {
        $this->routeMatch->expects($this->once())
                         ->method('getParam')
                         ->with('id');

        $listener = new IdentifierProviderListener();
        $listener->execute($this->event);
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function canConfigureCustomRouteMatchParameterForIdentifier()
    {
        $this->routeMatch->expects($this->once())
                         ->method('getParam')
                         ->with('my-id');

        $listener = new IdentifierProviderListener('my-id');
        $listener->execute($this->event);
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function putsTheIdentifierIntoTheEvent()
    {
        $identifier = 67;

        $this->routeMatch->expects($this->any())
                         ->method('getParam')
                         ->will($this->returnValue($identifier));
        $this->event->expects($this->once())
                    ->method('setParam')
                    ->with('identifier', $identifier);

        $listener = new IdentifierProviderListener();
        $listener->execute($this->event);
    }
}
