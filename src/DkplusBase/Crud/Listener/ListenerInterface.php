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
interface ListenerInterface
{
    public function execute(MvcEvent $event);
}
