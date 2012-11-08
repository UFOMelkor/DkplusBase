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
class PaginationRetrievalListenerTest extends TestCase
{
    /** @var \Zend\Mvc\MvcEvent|\PHPUnit_Framework_MockObject_MockObject */
    protected $event;

    /** @var \Zend\Http\Request|\PHPUnit_Framework_MockObject_MockObject */
    protected $routeMatch;

    /** @var PaginationRetrieveListener */
    protected $listener;

    protected function setUp()
    {
        parent::setUp();
        $this->service    = $this->getMockForAbstractClass('DkplusBase\Crud\\Service\ServiceInterface');
        $this->listener   = new PaginationRetrievalListener($this->service);
        $this->routeMatch = $this->getMockIgnoringConstructor('Zend\Mvc\Router\RouteMatch');
        $this->event      = $this->getMockIgnoringConstructor('Zend\Mvc\MvcEvent');
        $this->event->expects($this->any())
                    ->method('getRouteMatch')
                    ->will($this->returnValue($this->routeMatch));
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function isListener()
    {
        $this->assertInstanceOf('DkplusBase\Crud\Listener\ListenerInterface', $this->listener);
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function usesPageAsDefaultRouteMatchParameterForActualPageNumber()
    {
        $this->routeMatch->expects($this->once())
                         ->method('getParam')
                         ->with('page', 0);

        $this->listener->execute($this->event);
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function canUseCustomRouteMatchParameterForActualPageNumber()
    {
        $this->routeMatch->expects($this->once())
                         ->method('getParam')
                         ->with('my-page-param', 0);

        $listener = new PaginationRetrievalListener($this->service, 'my-page-param');
        $listener->execute($this->event);
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function returnsThePaginatorFromTheService()
    {
        $paginator = $this->getMockIgnoringConstructor('Zend\Paginator\Paginator');

        $this->routeMatch->expects($this->any())
                         ->method('getParam')
                         ->will($this->returnValue(5));

        $this->service->expects($this->any())
                      ->method('getPaginator')
                      ->with(5)
                      ->will($this->returnValue($paginator));

        $this->assertSame($paginator, $this->listener->execute($this->event));
    }
}
