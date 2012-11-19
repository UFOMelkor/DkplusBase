<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage EventManager
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\EventManager;

use BadMethodCallException;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\EventManager\StaticEventManager;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage EventManager
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class SharedEventManagerAdapter implements EventManagerInterface
{
    /** @var array */
    private $identifiers = array();

    /** @var SharedEventManagerInterface */
    private $sharedManager;

    public function addIdentifiers($identifiers)
    {
        $this->identifiers = \array_merge($this->identifiers, $identifiers);
    }

    public function attach($event, $callback = null, $priority = 1)
    {
        foreach ($this->identifiers as $identifier) {
            $this->getSharedManager()->attach($identifier, $event, $callback, $priority);
        }
    }

    public function attachAggregate(\Zend\EventManager\ListenerAggregateInterface $aggregate,
                                    $priority = 1)
    {
        $aggregate->attach($this);
    }

    public function clearListeners($event)
    {
        foreach ($this->identifiers as $identifier) {
            $this->getSharedManager()->clearListeners($identifier, $event);
        }
    }

    public function detach($listener)
    {
        foreach ($this->identifiers as $identifier) {
            $this->getSharedManager()->detach($identifier, $listener);
        }
    }

    public function detachAggregate(\Zend\EventManager\ListenerAggregateInterface $aggregate)
    {
        $aggregate->detach($this);
    }

    public function getEvents()
    {
        $events = array();
        foreach ($this->identifiers as $identifier) {
            $events = \array_merge($events, $this->getSharedManager()->getEvents($identifier));
        }
        return \array_unique($events);
    }

    public function getIdentifiers()
    {
        return $this->identifiers;
    }

    public function getListeners($event)
    {
        $listeners = array();
        foreach ($this->identifiers as $identifier) {
            $listeners = \array_merge($listeners, $this->getSharedManager()->getListeners($identifier, $event));
        }
        return \array_unique($listeners);
    }

    public function getSharedManager()
    {
        if ($this->sharedManager === null) {
            $this->sharedManager = StaticEventManager::getInstance();
        }
        return $this->sharedManager;
    }

    public function setEventClass($class)
    {
        throw new BadMethodCallException();
    }

    public function setIdentifiers($identifiers)
    {
        $this->identifiers = $identifiers;
    }

    public function setSharedManager(SharedEventManagerInterface $sharedEventManager)
    {
        $this->sharedManager = $sharedEventManager;
    }

    public function trigger($event, $target = null, $argv = array(),
                            $callback = null)
    {
        throw new BadMethodCallException();
    }

    public function triggerUntil($event, $target, $argv = null, $callback = null)
    {
        throw new BadMethodCallException();
    }

    public function unsetSharedManager()
    {
        $this->sharedManager = null;
    }
}
