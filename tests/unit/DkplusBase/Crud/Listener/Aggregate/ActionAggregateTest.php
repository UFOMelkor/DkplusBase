<?php
/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Listener\Aggregate;

use DkplusUnitTest\TestCase;

/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 * @covers     DkplusBase\Crud\Listener\Aggregate\Aggregate
 */
class ActionAggregateTest extends TestCase
{
    /** @var ActionAggregate */
    protected $aggregate;

    protected function setUp()
    {
        parent::setUp();
        $this->aggregate = new ActionAggregate();
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function isListenerAggregate()
    {
        $this->assertInstanceOf(
            'Zend\EventManager\ListenerAggregateInterface',
            $this->aggregate
        );
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function hasAnDefaultAggregate()
    {
        $this->assertInstanceOf(
            'DkplusBase\Crud\Listener\Aggregate\Aggregate',
            $this->aggregate->getAggregate()
        );
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function hasUseAnOwnAggregate()
    {
        $aggregate = $this->getMock('DkplusBase\Crud\Listener\Aggregate\Aggregate');
        $this->aggregate->setAggregate($aggregate);
        $this->assertSame($aggregate, $this->aggregate->getAggregate());
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function providesService()
    {
        $service = $this->getMockForAbstractClass('DkplusBase\Crud\Service\ServiceInterface');
        $this->aggregate->setService($service);
        $this->assertSame($service, $this->aggregate->getService());
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function attachesEventsByReferingToTheAggregate()
    {
        $eventManager = $this->getMockForAbstractClass('Zend\EventManager\EventManagerInterface');

        $aggregate = $this->getMock('DkplusBase\Crud\Listener\Aggregate\Aggregate');
        $aggregate->expects($this->once())
                  ->method('attach')
                  ->with($eventManager);

        $this->aggregate->setAggregate($aggregate);
        $this->aggregate->attach($eventManager);
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function detachesEventsByReferingToTheAggregate()
    {
        $eventManager = $this->getMockForAbstractClass('Zend\EventManager\EventManagerInterface');

        $aggregate = $this->getMock('DkplusBase\Crud\Listener\Aggregate\Aggregate');
        $aggregate->expects($this->once())
                  ->method('detach')
                  ->with($eventManager);

        $this->aggregate->setAggregate($aggregate);
        $this->aggregate->detach($eventManager);
    }
}
