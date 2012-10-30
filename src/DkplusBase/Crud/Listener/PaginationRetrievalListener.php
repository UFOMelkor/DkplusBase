<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Listener;

use DkplusBase\Crud\Service\ServiceInterface as Service;
use Zend\Mvc\Router\RouteMatch;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class PaginationRetrievalListener
{
    /** @var RouteMatch */
    protected $routeMatch;

    /** @var Service */
    protected $service;

    /** @var string */
    protected $pageParameter;

    /**
     * @param RouteMatch $routeMatch
     * @param Service $service
     * @param string $pageParameter
     */
    public function __construct(RouteMatch $routeMatch, Service $service, $pageParameter = 'page')
    {
        $this->routeMatch    = $routeMatch;
        $this->service       = $service;
        $this->pageParameter = (string) $pageParameter;
    }

    public function getPaginator()
    {
        $pageNumber = $this->routeMatch->getParam($this->pageParameter);
        return $this->service->getPaginator($pageNumber);
    }
}
