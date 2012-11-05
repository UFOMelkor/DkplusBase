<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Listener\Aggregate;

use Zend\EventManager\ListenerAggregateInterface;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class AggregateParameter implements ParameterInterface
{
    /** @var ListenerAggregateInterface */
    protected $aggregate;

    /** @var int */
    protected $priority;


    public function __construct(ListenerAggregateInterface $aggregate, $priority = 1)
    {
        $this->aggregate = $aggregate;
        $this->priority  = $priority;
    }

    public function getCallback()
    {
        return $this->priority;
    }

    public function getEvent()
    {
        return $this->aggregate;
    }

    public function getPriority()
    {
        return null;
    }
}
