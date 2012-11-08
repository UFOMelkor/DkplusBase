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
class EntityRetrievalListenerTest extends TestCase
{
    /** @var \DkplusBase\Crud\Service\ServiceInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $service;

    /** @var \Zend\EventManager\MvcEvent|\PHPUnit_Framework_MockObject_MockObject */
    protected $event;

    protected function setUp()
    {
        parent::setUp();
        $this->service = $this->getMockForAbstractClass('DkplusBase\Crud\Service\ServiceInterface');
        $this->event   = $this->getMockIgnoringConstructor('Zend\Mvc\MvcEvent');
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
            new EntityRetrievalListener($this->service)
        );
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function returnsTheEntityFromTheService()
    {
        $entity = $this->getMock('stdClass');

        $this->event->expects($this->any())
                    ->method('getParam')
                    ->with('identifier')
                    ->will($this->returnValue(5));

        $this->service->expects($this->any())
                      ->method('get')
                      ->with(5)
                      ->will($this->returnValue($entity));

        $listener = new EntityRetrievalListener($this->service);
        $this->assertSame($entity, $listener->execute($this->event));
    }
}
