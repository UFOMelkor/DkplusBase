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
class ListenerParameterTest extends TestCase
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
            new ListenerParameter('event')
        );
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function providesTheEventName()
    {
        $parameter = new ListenerParameter('my-event');

        $this->assertSame('my-event', $parameter->getEvent());
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function providesThePriorityName()
    {
        $parameter = new ListenerParameter('my-event', -1);

        $this->assertSame(-1, $parameter->getPriority());
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function hasDefaultPriorityOfOne()
    {
        $parameter = new ListenerParameter('event-name');

        $this->assertSame(1, $parameter->getPriority());
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function canUseCallbackAsCallback()
    {
        $parameter = new ListenerParameter('event-name');
        $parameter->setCallback('stripos');

        $this->assertSame('stripos', $parameter->getCallback());
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function canUseListenerAsCallback()
    {
        $listener = $this->getMockForAbstractClass('DkplusBase\Crud\Listener\ListenerInterface');

        $parameter = new ListenerParameter('event-name');
        $parameter->setListener($listener);

        $this->assertSame(array($listener, 'execute'), $parameter->getCallback());
    }
}
