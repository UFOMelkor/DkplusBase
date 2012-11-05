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
    protected $assignAlias;

    /** @var string */
    protected $eventParameter;

    /** @var string */
    protected $template;

    public function __construct($assignAlias, $eventParameter, $template)
    {
        $this->assignAlias    = $assignAlias;
        $this->eventParameter = $eventParameter;
        $this->template       = $template;
    }

    public function execute(MvcEvent $event)
    {
        $controller = $event->getTarget();
        $assignable = $event->getParam($this->eventParameter);
        return $controller->dsl()->assign($assignable)->as($this->assignAlias)->and()->render($this->template);
    }
}
