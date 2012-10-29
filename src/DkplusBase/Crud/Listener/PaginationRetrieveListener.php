<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Listener;

use DkplusBase\Crud\Service\ServiceInterface as Service;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface as EventManager;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\Router\RouteMatch;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class PaginationRetrieveListener implements ListenerAggregateInterface
{
    /** @var string */
    private $eventName;

    /** @var RouteMatch */
    private $routeMatch;

    /** @var Service */
    private $service;

    /** @var array */
    private $listeners = array();

    public function __construct($eventName, RouteMatch $routeMatch, Service $service)
    {
        $this->eventName = $eventName;
        $this->routeMatch   = $routeMatch;
        $this->service   = $service;
    }

    public function attach(EventManager $eventManager)
    {
        $this->listeners[] = $eventManager->attach($this->eventName, array($this, 'getPaginator'));
    }

    public function detach(EventManager $eventManager)
    {
        foreach ($this->listeners as $listener) {
            $eventManager->detach($listener);
        }
    }

    public function getPaginator()
    {
        $pageNumber       = $this->routeMatch->getParam('page');
        $itemCountPerPage = $this->routeMatch->getParam('itemCountPerPage');
        return $this->service->getPaginator($pageNumber, $itemCountPerPage);
    }
}
