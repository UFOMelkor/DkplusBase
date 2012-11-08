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
class EntitiesRetrievalListenerTest extends TestCase
{
    /** @var \Zend\Http\Request|\PHPUnit_Framework_MockObject_MockObject */
    protected $routeMatch;

    protected function setUp()
    {
        parent::setUp();
        $this->service = $this->getMockForAbstractClass('DkplusBase\Crud\\Service\ServiceInterface');
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
            new EntitiesRetrievalListener($this->service)
        );
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function returnsTheEntitiesFromTheService()
    {
        $entities = array($this->getMock('stdClass'), $this->getMock('stdClass'));

        $this->service->expects($this->any())
                      ->method('getAll')
                      ->will($this->returnValue($entities));

        $listener = new EntitiesRetrievalListener($this->service);
        $this->assertSame($entities, $listener->execute($this->getMockIgnoringConstructor('Zend\Mvc\MvcEvent')));
    }
}
