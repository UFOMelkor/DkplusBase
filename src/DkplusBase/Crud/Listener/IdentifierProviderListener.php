<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Listener;

use DkplusBase\Crud\Service\ServiceInterface as Service;
use Zend\EventManager\EventInterface;

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

    public function execute(EventInterface $event)
    {
        $event->setParam(
            'identifier',
            $event->getTarget()->getEvent()->getRouteMatch()->getParam($this->routeMatchParam)
        );
    }
}
