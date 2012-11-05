<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Listener\Aggregate\Parameter;

use DkplusBase\Crud\Listener\ListenerInterface as Listener;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class ListenerParameter implements ParameterInterface
{
    /** @var string */
    protected $event;

    /** @var callback */
    protected $callback;

    /** @var $int */
    protected $priority;

    public function __construct($event, $priority = 1)
    {
        $this->event    = $event;
        $this->priority = $priority;
    }

    public function setCallback($callback)
    {
        $this->callback = $callback;
    }

    public function setListener(Listener $listener)
    {
        $this->setCallback(array($listener, 'execute'));
    }

    public function getCallback()
    {
        return $this->callback;
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function getPriority()
    {
        return $this->priority;
    }
}
