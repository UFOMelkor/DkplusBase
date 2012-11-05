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
class CreateAggregate implements ListenerAggregateInterface
{
    /** @var ActionAggregate */
    protected $aggregate;

    /** @var Service */
    protected $service;

    /** @var string */
    protected $template;

    /** @var Listener\Options\SuccessOptions */
    protected $successOptions;

    public function setService(Service $service)
    {
        $this->service = $service;
    }

    public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function setSuccessOptions(Listener\Options\SuccessOptions $options)
    {
        $this->successOptions = $options;
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
        $this->aggregate->addListener(new Listener\CreateFormRetrievalListener($this->service), 'CrudController.preCreate');
        $this->aggregate->addListener(
            new Listener\FormSubmissionRedirectListener($this->service, $this->successOptions, $this->template),
            'CrudController.create'
        );
        $this->aggregate->attach($eventManager);
    }

    public function detach(EventManager $eventManager)
    {
        $this->aggregate->detach($eventManager);
    }
}
