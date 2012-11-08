<?php
/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Listener\Aggregate\Parameter;

use DkplusUnitTest\TestCase;

/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 * @covers     DkplusBase\Crud\Listener\Aggregate\Parameter\Factory
 */
class FactoryTest extends TestCase
{
    /** @var Factory */
    protected $factory;

    protected function setUp()
    {
        parent::setUp();
        $this->factory = new Factory();
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function createsAggregateParameterFromAggregate()
    {
        $aggregate = $this->getMockForAbstractClass('Zend\EventManager\ListenerAggregateInterface');
        $parameter = $this->factory->create($aggregate);

        $this->assertInstanceOf('DkplusBase\Crud\Listener\Aggregate\Parameter\AggregateParameter', $parameter);
        $this->assertSame($aggregate, $parameter->getEvent());
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function createsListenerParameterFromListener()
    {
        $listener = $this->getMockForAbstractClass('DkplusBase\Crud\Listener\ListenerInterface');
        $parameter = $this->factory->create($listener, 'event');

        $this->assertInstanceOf('DkplusBase\Crud\Listener\Aggregate\Parameter\ListenerParameter', $parameter);
        $this->assertSame(array($listener, 'execute'), $parameter->getCallback());
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function createsListenerParameterFromCallback()
    {
        $parameter = $this->factory->create('stripos', 'event');

        $this->assertInstanceOf('DkplusBase\Crud\Listener\Aggregate\Parameter\ListenerParameter', $parameter);
        $this->assertSame('stripos', $parameter->getCallback());
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     * @expectedException InvalidArgumentException
     */
    public function throwsExceptionOnInvalidHandler()
    {
        $this->factory->create('foo');
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function setsTheEventForListenerParameter()
    {
        $parameter = $this->factory->create('stripos', 'event');

        $this->assertSame('event', $parameter->getEvent());
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function canSetThePriorityForAggregateParameter()
    {
        $aggregate = $this->getMockForAbstractClass('Zend\EventManager\ListenerAggregateInterface');
        $parameter = $this->factory->create($aggregate, 10);

        $this->assertEquals(10, $parameter->getCallback());
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function canSetThePriorityForListenerParameter()
    {
        $listener = $this->getMockForAbstractClass('DkplusBase\Crud\Listener\ListenerInterface');
        $parameter = $this->factory->create($listener, 'event', 5);

        $this->assertEquals(5, $parameter->getPriority());
    }
}
