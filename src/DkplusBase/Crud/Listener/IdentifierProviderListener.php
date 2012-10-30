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
class IdentifierProviderListener implements ListenerInterface
{
    /** @var string */
    protected $routeMatchParam;

    /**
     * @param string $routeMatchParam
     */
    public function __construct($routeMatchParam = 'id')
    {
        $this->routeMatchParam = (string) $routeMatchParam;
    }

    public function execute(MvcEvent $event)
    {
        $event->setParam('identifier', $event->getRouteMatch()->getParam($this->routeMatchParam));
    }
}
