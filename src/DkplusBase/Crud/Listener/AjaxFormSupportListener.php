<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Listener;

use Zend\Mvc\MvcEvent;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class AjaxFormSupportListener implements ListenerInterface
{
    public function execute(MvcEvent $event)
    {
        $ctrl       = $event->getTarget();
        $dsl        = $event->getParam('result');

        return $dsl->onAjaxRequest($ctrl->dsl()->assign()->formMessages()->asJson());
    }
}
