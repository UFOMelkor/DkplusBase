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
class EntityRetrievalListener implements ListenerInterface
{
    /** @var Service */
    protected $service;

    /**
     * @param Service $service
     * @param string $routeMatchParam
     */
    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    public function execute(MvcEvent $event)
    {
        $identifier = $event->getParam('identifier');
        return $this->service->get($identifier);
    }
}
