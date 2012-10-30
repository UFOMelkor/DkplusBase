<?php
/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage Crud\Controller
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Listener;

use DkplusUnitTest\TestCase;

/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage Crud\Controller
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class PaginationRetrievalListenerTest extends TestCase
{
    /** @var \Zend\Http\Request|\PHPUnit_Framework_MockObject_MockObject */
    protected $routeMatch;

    /** @var PaginationRetrieveListener */
    protected $listener;

    protected function setUp()
    {
        parent::setUp();
        $this->routeMatch = $this->getMockIgnoringConstructor('Zend\Mvc\Router\RouteMatch');
        $this->service    = $this->getMockForAbstractClass('DkplusBase\Crud\\Service\ServiceInterface');
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

        $listener = new PaginationRetrievalListener($this->routeMatch, $this->service);
        $listener->getPaginator();
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

        $listener = new PaginationRetrievalListener($this->routeMatch, $this->service, 'my-page-param');
        $listener->getPaginator();
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

        $listener = new PaginationRetrievalListener($this->routeMatch, $this->service);
        $this->assertSame($paginator, $listener->getPaginator());
    }
}
