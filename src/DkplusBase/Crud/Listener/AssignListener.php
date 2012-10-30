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
class AssignListener implements ListenerInterface
{
    /** @var string */
    private $assignAlias;

    /** @var string */
    private $eventParameter;

    public function __construct($assignAlias, $eventParameter)
    {
        $this->assignAlias    = $assignAlias;
        $this->eventParameter = $eventParameter;
    }

    public function execute(MvcEvent $event)
    {
        $controller = $event->getTarget();
        $assignable = $event->getParam($this->eventParameter);
        return $controller->dsl()->assign($assignable)->as($this->assignAlias);
    }
}
