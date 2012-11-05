<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Listener;

use DkplusBase\Crud\Service\ServiceInterface as Service;
use Zend\Mvc\MvcEvent;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class PaginationRetrievalListener implements ListenerInterface
{
    /** @var Service */
    protected $service;

    /** @var string */
    protected $pageParameter;

    /**
     * @param Service $service
     * @param string $pageParameter
     */
    public function __construct(Service $service, $pageParameter = 'page')
    {
        $this->service       = $service;
        $this->pageParameter = (string) $pageParameter;
    }

    public function execute(MvcEvent $event)
    {
        $pageNumber = $event->getRouteMatch()->getParam($this->pageParameter);
        return $this->service->getPaginator($pageNumber);
    }
}
