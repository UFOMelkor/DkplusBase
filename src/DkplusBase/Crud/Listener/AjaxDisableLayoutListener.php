<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Listener;

use Zend\EventManager\EventInterface;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class AjaxDisableLayoutListener implements ListenerInterface
{
    public function execute(EventInterface $event)
    {
        $ctrl       = $event->getTarget();
        $dsl        = $event->getParam('result');

        return $dsl->onAjaxRequest($ctrl->dsl()->disableLayout());
    }
}
