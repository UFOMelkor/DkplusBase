<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Listener\Aggregate;

use Zend\EventManager\EventManagerInterface as EventManager;
use Zend\EventManager\ListenerAggregateInterface;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class ReadAggregate implements ListenerAggregateInterface
{
    /** @var array */
    private $listeners = array();

    /** @var \DkplusBase\Crud\Listener\ListenerInterface */
    protected $entityRetrieval;

    /** @var \DkplusBase\Crud\Listener\ListenerInterface */
    protected $assigning;

    /** @var \DkplusBase\Crud\Listener\ListenerInterface */
    protected $notFoundListener;

    public function attach(EventManager $eventManager)
    {
        $this->listeners[] = $eventManager->attach('CrudController.preRead', array($this->entityRetrieval, 'execute'));
        $this->listeners[] = $eventManager->attach('CrudController.read', array($this->assigning, 'execute'));
        $this->listeners[] = $eventManager->attach('CrudController.readNotFound', array($this->notFoundListener, 'execute'));
    }

    public function detach(EventManager $eventManager)
    {
        foreach ($this->listeners as $listener) {
            $eventManager->detach($listener);
        }
    }
}
