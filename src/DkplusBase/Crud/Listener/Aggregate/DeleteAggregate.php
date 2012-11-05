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
class DeleteAggregate implements ListenerAggregateInterface
{
    /** @var ActionAggregate */
    protected $aggregate;

    /** @var Service */
    protected $service;

    /** @var Listener\Options\NotFoundOptions */
    protected $notFoundOptions;

    /** @var Listener\Options\SuccessOptions */
    protected $successOptions;

    public function setService(Service $service)
    {
        $this->service = $service;
    }

    public function setSuccessOptions(Listener\Options\SuccessOptions $options)
    {
        $this->successOptions = $options;
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
        $this->aggregate->addListener(new Listener\IdentifierProviderListener(), 'CrudController.preDelete', 2);
        $this->aggregate->addListener(new Listener\EntityRetrievalListener($this->service), 'CrudController.preDelete');
        $this->aggregate->addListener(
            new Listener\DeleteRedirectListener($this->service, $this->successOptions),
            'CrudController.delete'
        );
        $this->aggregate->addListener(
            new Listener\NotFoundReplaceListener($this->notFoundOptions),
            'CrudController.deleteNotFound'
        );
        $this->aggregate->attach($eventManager);
    }

    public function detach(EventManager $eventManager)
    {
        $this->aggregate->detach($eventManager);
    }
}
