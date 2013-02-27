<?php
/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage Mvc\Controller\Plugin
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Mvc\Controller\Plugin;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage Mvc\Controller\Plugin
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class NotFoundForwardTest extends TestCase
{
    /** @var \Zend\Mvc\View\Http\RouteNotFoundStrategy|\PHPUnit_Framework_MockObject_MockObject */
    private $routeNotFoundStrategy;

    /** @var \Zend\Mvc\Router\RouteMatch|\PHPUnit_Framework_MockObject_MockObject */
    private $routeMatch;

    /** @var \Zend\Http\Response|\PHPUnit_Framework_MockObject_MockObject */
    private $response;

    /** @var \Zend\Mvc\Controller\Plugin\Forward|\PHPUnit_Framework_MockObject_MockObject */
    private $forwardPlugin;

    /** @var NotFoundForward */
    private $plugin;

    protected function setUp()
    {
        parent::setUp();

        $this->routeNotFoundStrategy = $this->getMock('Zend\Mvc\View\Http\RouteNotFoundStrategy');
        $this->forwardPlugin         = $this->getMock('Zend\Mvc\Controller\Plugin\Forward');
        $this->response              = $this->getMock('Zend\Http\Response');
        $this->routeMatch            = $this->getMockBuilder('Zend\Mvc\Router\RouteMatch')
                                            ->disableOriginalConstructor()
                                            ->getMock();

        $event = $this->getMock('Zend\Mvc\MvcEvent');
        $event->expects($this->any())->method('getResponse')->will($this->returnValue($this->response));
        $event->expects($this->any())->method('getRouteMatch')->will($this->returnValue($this->routeMatch));

        $controller = $this->getMock('Zend\Stdlib\DispatchableInterface', array('dispatch', 'forward', 'getEvent'));
        $controller->expects($this->any())->method('getEvent')->will($this->returnValue($event));
        $controller->expects($this->any())->method('forward')->will($this->returnValue($this->forwardPlugin));

        $this->plugin = new NotFoundForward($this->routeNotFoundStrategy);
        $this->plugin->setController($controller);
    }

    /** @test */
    public function setsA404StatusCode()
    {
        $this->response->expects($this->once())
                       ->method('setStatusCode')
                       ->with(404);

        $this->plugin->dispatch('bar');
    }

    /** @test */
    public function usesTheForwardPluginToGetTheResult()
    {
        $this->forwardPlugin->expects($this->once())->method('dispatch')->with('baz', array('foo' => 'bar'));

        $this->plugin->dispatch('baz', array('foo' => 'bar'));
    }

    /** @test */
    public function canInjectTheRouteNameIntoTheRouteMatch()
    {
        $this->routeMatch->expects($this->once())->method('setMatchedRouteName')->with('my-route');

        $this->plugin->dispatch('foo', array(), 'my-route');
    }

    /** @test */
    public function mustNotInjectARouteName()
    {
        $this->routeMatch->expects($this->never())->method('setMatchedRouteName');

        $this->plugin->dispatch('foo');
    }

    /** @test */
    public function usesTheViewModelTemplateAsNotFoundTemplate()
    {
        $viewModel = $this->getMockForAbstractClass('Zend\View\Model\ModelInterface');
        $viewModel->expects($this->any())->method('getTemplate')->will($this->returnValue('foo/bar/baz'));

        $this->forwardPlugin->expects($this->any())->method('dispatch')->will($this->returnValue($viewModel));

        $this->routeNotFoundStrategy->expects($this->once())->method('setNotFoundTemplate')->with('foo/bar/baz');

        $this->plugin->dispatch('foo');
    }
}
