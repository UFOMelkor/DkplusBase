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

    /** @var Listener\Options\NotFoundOptions */
    protected $notFoundOptions;

    public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function setNotFoundOptions(Listener\Options\NotFoundReplaceOptions $options)
    {
        $this->notFoundOptions = $options;
    }

    public function attach(EventManager $eventManager)
    {
        $this->getAggregate()->addListener(new Listener\IdentifierProviderListener(), 'CrudController.preRead', 2);
        $this->getAggregate()->addListener(
            new Listener\EntityRetrievalListener($this->service),
            'CrudController.preRead'
        );
        $this->getAggregate()->addListener(
            new Listener\AssignListener('entity', 'entity', $this->template),
            'CrudController.read'
        );
        $this->getAggregate()->addListener(
            new Listener\NotFoundReplaceListener($this->notFoundOptions),
            'CrudController.readNotFound'
        );
        parent::attach($eventManager);
    }
}
