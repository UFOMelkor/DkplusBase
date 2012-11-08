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
class AggregateTest extends TestCase
{
    /** @var Aggregate */
    protected $aggregate;

    protected function setUp()
    {
        parent::setUp();
        $this->aggregate = new Aggregate();
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
    public function hasDefaultParameterFactory()
    {
        $this->assertInstanceOf(
            'DkplusBase\Crud\Listener\Aggregate\Parameter\Factory',
            $this->aggregate->getParameterFactory()
        );
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function canSetParameterFactory()
    {
        $factory = $this->getMock('DkplusBase\Crud\Listener\Aggregate\Parameter\Factory');
        $this->aggregate->setParameterFactory($factory);

        $this->assertSame($factory, $this->aggregate->getParameterFactory());
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function canAddParameters()
    {
        $event    = 'event';
        $callback = 'stripos';
        $priority = 10;

        $parameter = $this->getParameterMock($event, $callback, $priority);

        $eventManager = $this->getMockForAbstractClass('Zend\EventManager\EventManagerInterface');
        $eventManager->expects($this->once())
                     ->method('attach')
                     ->with($event, $callback, $priority);

        $this->aggregate->addParameter($parameter);
        $this->aggregate->attach($eventManager);
    }

    /**s
     * @param string $event
     * @param callback $callback
     * @param int $priority
     * @return \DkplusBase\Crud\Listener\Aggregate\Parameter\ParameterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getParameterMock($event, $callback, $priority)
    {
        $parameter = $this->getMockForAbstractClass('DkplusBase\Crud\Listener\Aggregate\Parameter\ParameterInterface');
        $parameter->expects($this->any())
                  ->method('getEvent')
                  ->will($this->returnValue($event));
        $parameter->expects($this->any())
                  ->method('getCallback')
                  ->will($this->returnValue($callback));
        $parameter->expects($this->any())
                  ->method('getPriority')
                  ->will($this->returnValue($priority));
        return $parameter;
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function canAddCallbacks()
    {
        $event    = 'event';
        $callback = 'stripos';
        $priority = 10;

        $parameter = $this->getParameterMock($event, $callback, $priority);

        $factory = $this->getMock('DkplusBase\Crud\Listener\Aggregate\Parameter\Factory');
        $factory->expects($this->once())
                ->method('create')
                ->with($callback, $event, $priority)
                ->will($this->returnValue($parameter));

        $eventManager = $this->getMockForAbstractClass('Zend\EventManager\EventManagerInterface');
        $eventManager->expects($this->once())
                     ->method('attach')
                     ->with($event, $callback, $priority);

        $this->aggregate->setParameterFactory($factory);
        $this->aggregate->addCallback($callback, $event, $priority);
        $this->aggregate->attach($eventManager);
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function canAddListener()
    {
        $event    = 'event';
        $listener = $this->getMockForAbstractClass('DkplusBase\Crud\Listener\ListenerInterface');
        $priority = 10;

        $parameter = $this->getParameterMock($event, array($listener, 'execute'), $priority);

        $factory = $this->getMock('DkplusBase\Crud\Listener\Aggregate\Parameter\Factory');
        $factory->expects($this->once())
                ->method('create')
                ->with($listener, $event, $priority)
                ->will($this->returnValue($parameter));

        $eventManager = $this->getMockForAbstractClass('Zend\EventManager\EventManagerInterface');
        $eventManager->expects($this->once())
                     ->method('attach')
                     ->with($event, array($listener, 'execute'), $priority);

        $this->aggregate->setParameterFactory($factory);
        $this->aggregate->addListener($listener, $event, $priority);
        $this->aggregate->attach($eventManager);
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function canAddAggregates()
    {
        $aggregate = $this->getMockForAbstractClass('Zend\EventManager\ListenerAggregateInterface');
        $priority  = 10;

        $parameter = $this->getParameterMock($aggregate, $priority, null);

        $factory = $this->getMock('DkplusBase\Crud\Listener\Aggregate\Parameter\Factory');
        $factory->expects($this->once())
                ->method('create')
                ->with($aggregate, $priority)
                ->will($this->returnValue($parameter));

        $eventManager = $this->getMockForAbstractClass('Zend\EventManager\EventManagerInterface');
        $eventManager->expects($this->once())
                     ->method('attach')
                     ->with($aggregate, $priority);

        $this->aggregate->setParameterFactory($factory);
        $this->aggregate->addAggregate($aggregate, $priority);
        $this->aggregate->attach($eventManager);
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function canDetachParameters()
    {
        $listener = 20;

        $parameter = $this->getParameterMock('foo', 'stripos', 5);

        $eventManager = $this->getMockForAbstractClass('Zend\EventManager\EventManagerInterface');
        $eventManager->expects($this->once())
                     ->method('attach')
                     ->will($this->returnValue($listener));
        $eventManager->expects($this->once())
                     ->method('detach')
                     ->with($listener);

        $this->aggregate->addParameter($parameter);
        $this->aggregate->attach($eventManager);
        $this->aggregate->detach($eventManager);
    }
}
