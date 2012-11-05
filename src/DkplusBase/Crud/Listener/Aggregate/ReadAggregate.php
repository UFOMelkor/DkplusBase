<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Listener\Aggregate;

use DkplusBase\Crud\Listener;
use DkplusBase\Crud\Service\ServiceInterface as Service;
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
    /** @var ActionAggregate */
    protected $aggregate;

    /** @var Service */
    protected $service;

    /** @var string */
    protected $template;

    /** @var Listener\Options\NotFoundOptions */
    protected $notFoundOptions;

    public function setService(Service $service)
    {
        $this->service = $service;
    }

    public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function setNotFoundOptions(Listener\Options\NotFoundReplaceOptions $options)
    {
        $this->notFoundOptions = $options;
    }

    /** @return ActionAggregate */
    public function getAggregate()
    {
        if ($this->aggregate === null) {
            $this->aggregate = new ActionAggregate();
        }
    }

    public function setAggregate(ActionAggregate $aggregate)
    {
        $this->aggregate = $aggregate;
    }

    public function attach(EventManager $eventManager)
    {
        $this->aggregate->addListener(new Listener\IdentifierProviderListener(), 'CrudController.preRead', 2);
        $this->aggregate->addListener(new Listener\EntityRetrievalListener($this->service), 'CrudController.preRead');
        $this->aggregate->addListener(
            new Listener\AssignListener('entity', 'entity', $this->template),
            'CrudController.read'
        );
        $this->aggregate->addListener(
            new Listener\NotFoundReplaceListener($this->notFoundOptions),
            'CrudController.readNotFound'
        );
        $this->aggregate->attach($eventManager);
    }

    public function detach(EventManager $eventManager)
    {
        $this->aggregate->detach($eventManager);
    }
}
