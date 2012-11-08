<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Listener\Aggregate;

use DkplusBase\Crud\Listener\ListenerInterface as Listener;
use Zend\EventManager\EventManagerInterface as EventManager;
use Zend\EventManager\ListenerAggregateInterface as ListenerAggregate;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class Aggregate implements ListenerAggregate
{
    /** @var array */
    private $addedListeners = array();

    /** @var Parameter\Factory */
    protected $parameterFactory;

    /** @var ParameterInterface[] */
    protected $listeners = array();

    /** @return Parameter\Factory */
    public function getParameterFactory()
    {
        if ($this->parameterFactory === null) {
            $this->parameterFactory = new Parameter\Factory();
        }
        return $this->parameterFactory;
    }

    public function setParameterFactory(Parameter\Factory $factory)
    {
        $this->parameterFactory = $factory;
    }

    public function attach(EventManager $eventManager)
    {
        foreach ($this->listeners as $param) {
            $this->addedListeners[] = $eventManager->attach(
                $param->getEvent(),
                $param->getCallback(),
                $param->getPriority()
            );
        }
    }

    public function detach(EventManager $eventManager)
    {
        foreach ($this->addedListeners as $listener) {
            $eventManager->detach($listener);
        }
        $this->addedListeners = array();
    }

    public function addAggregate(ListenerAggregate $aggregate, $priority = 1)
    {
        $parameter = $this->getParameterFactory()->create($aggregate, $priority);
        $this->addParameter($parameter);
    }

    public function addListener(Listener $listener, $event, $priority = 1)
    {
        $parameter = $this->getParameterFactory()->create($listener, $event, $priority);
        $this->addParameter($parameter);
    }

    public function addCallback($callback, $event, $priority = 1)
    {
        $parameter = $this->getParameterFactory()->create($callback, $event, $priority);
        $this->addParameter($parameter);
    }

    public function addParameter(Parameter\ParameterInterface $parameter)
    {
        $this->listeners[] = $parameter;
    }
}
