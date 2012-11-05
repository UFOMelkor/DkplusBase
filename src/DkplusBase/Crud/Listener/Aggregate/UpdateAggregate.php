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
class UpdateAggregate implements ListenerAggregateInterface
{
    /** @var ActionAggregate */
    protected $aggregate;

    /** @var Service */
    protected $service;

    /** @var Listener\Options\NotFoundOptions */
    protected $notFoundOptions;

    /** @var Listener\Options\SuccessOptions */
    protected $successOptions;

    /** @var string */
    protected $template;

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

    public function setTemplate($template)
    {
        $this->template = $template;
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
        $this->aggregate->addListener(new Listener\IdentifierProviderListener(), 'CrudController.preUpdate', 2);
        $this->aggregate->addListener(
            new Listener\UpdateFormRetrievalListener($this->service),
            'CrudController.preUpdate'
        );
        $this->aggregate->addListener(
            new Listener\FormSubmissionRedirectListener($this->service, $this->successOptions, $this->template),
            'CrudController.update'
        );
        $this->aggregate->addListener(
            new Listener\NotFoundReplaceListener($this->notFoundOptions),
            'CrudController.updateNotFound'
        );
        $this->aggregate->attach($eventManager);
    }

    public function detach(EventManager $eventManager)
    {
        $this->aggregate->detach($eventManager);
    }
}
