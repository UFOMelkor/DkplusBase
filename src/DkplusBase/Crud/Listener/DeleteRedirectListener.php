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
class DeleteRedirectListener implements ListenerInterface
{
    /** @var Options\SuccessOptions */
    protected $options;

    /** @var Service */
    protected $service;

    public function __construct(Service $service, Options\SuccessOptions $options)
    {
        $this->service = $service;
        $this->options = $options;
    }

    public function execute(EventInterface $event)
    {
        $ctrl        = $event->getTarget();
        $entity      = $event->getParam('entity');
        $message     = $this->options->getComputatedMessage($entity);
        $route       = $this->options->getRedirectRoute();
        $routeParams = $this->options->getComputatedRedirectRouteParams($entity);

        $this->service->delete($entity);

        return $ctrl->dsl()->redirect()->to()->route($route, $routeParams)
                           ->with()->success()->message($message);
    }
}
