<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Listener\Aggregate;

use DkplusBase\Crud\Listener;
use Zend\EventManager\EventManagerInterface as EventManager;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class ReadAggregate extends ActionAggregate
{
    /** @var string */
    protected $template;

    public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function attach(EventManager $eventManager)
    {
        $this->getAggregate()->addListener(
            new Listener\EntitiesRetrievalListener($this->service),
            'CrudController.preList'
        );
        $this->getAggregate()->addListener(
            new Listener\AssignListener('entities', 'data', $this->template),
            'CrudController.list'
        );
        parent::attach($eventManager);
    }
}
