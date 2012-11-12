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
class PaginateAggregate extends ActionAggregate
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
            new Listener\PaginationRetrievalListener($this->service),
            'CrudController.prePaginate'
        );
        $this->getAggregate()->addListener(
            new Listener\AssignListener('paginator', 'paginator', $this->template),
            'CrudController.paginate'
        );
        parent::attach($eventManager);
    }
}
