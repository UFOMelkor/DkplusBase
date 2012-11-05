<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Listener\Aggregate;

use DkplusBase\Crud\Listener;
use DkplusBase\Crud\Service\ServiceInterface as Service;
use Zend\EventManager\EventManagerInterface as EventManager;
use Zend\EventManager\ListenerAggregateInterface;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class ActionAggregate implements ListenerAggregateInterface
{
    /** @var Aggregate */
    protected $aggregate;

    /** @var Service */
    protected $service;

    public function setService(Service $service)
    {
        $this->service = $service;
    }

    /** @return Service */
    public function getService()
    {
        return $this->service;
    }

    /** @return Aggregate */
    public function getAggregate()
    {
        if ($this->aggregate === null) {
            $this->aggregate = new Aggregate();
        }
    }

    public function setAggregate(Aggregate $aggregate)
    {
        $this->aggregate = $aggregate;
    }

    public function attach(EventManager $eventManager)
    {
        $this->aggregate->attach($eventManager);
    }

    public function detach(EventManager $eventManager)
    {
        $this->aggregate->detach($eventManager);
    }
}
