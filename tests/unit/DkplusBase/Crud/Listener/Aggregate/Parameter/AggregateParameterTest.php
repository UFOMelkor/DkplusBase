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
 */
class AggregateParameterTest extends TestCase
{
    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function isParameter()
    {
        $this->assertInstanceOf(
            'DkplusBase\Crud\Listener\Aggregate\Parameter\ParameterInterface',
            new AggregateParameter($this->getMock('Zend\EventManager\ListenerAggregateInterface'))
        );
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function providesTheGivenAggregateAsEventParameter()
    {
        $aggregate = $this->getMock('Zend\EventManager\ListenerAggregateInterface');
        $parameter = new AggregateParameter($aggregate);

        $this->assertSame($aggregate, $parameter->getEvent());
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function providesThePriorityAsCallbackParameter()
    {
        $parameter = new AggregateParameter($this->getMock('Zend\EventManager\ListenerAggregateInterface'), 5);

        $this->assertSame(5, $parameter->getCallback());
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function hasDefaultPriorityOfOne()
    {
        $parameter = new AggregateParameter($this->getMock('Zend\EventManager\ListenerAggregateInterface'));

        $this->assertSame(1, $parameter->getCallback());
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function providesNullAsPriorityParameter()
    {
        $parameter = new AggregateParameter($this->getMock('Zend\EventManager\ListenerAggregateInterface'));

        $this->assertNull($parameter->getPriority());
    }
}
