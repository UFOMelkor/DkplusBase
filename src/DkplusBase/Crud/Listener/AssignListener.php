<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Listener;

use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface as EventManager;
use Zend\EventManager\ListenerAggregateInterface;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class AssignListener implements ListenerAggregateInterface
{
    /** @var string */
    private $eventName;

    /** @var string */
    private $assignAlias;

    /** @var string */
    private $eventParameter;

    /** @var array */
    private $listeners = array();

    public function __construct($eventName, $assignAlias, $eventParameter)
    {
        $this->assignAlias    = $assignAlias;
        $this->eventName      = $eventName;
        $this->eventParameter = $eventParameter;
    }

    public function attach(EventManager $eventManager)
    {
        $this->listeners[] = $eventManager->attach($this->eventName, array($this, 'assign'));
    }

    public function detach(EventManager $eventManager)
    {
        foreach ($this->listeners as $listener) {
            $eventManager->detach($listener);
        }
    }

    public function assign(Event $event)
    {
        $controller = $event->getTarget();
        $assignable = $event->getParam($this->eventParameter);
        return $controller->dsl()->assign($assignable)->as($this->assignAlias);
    }
}
