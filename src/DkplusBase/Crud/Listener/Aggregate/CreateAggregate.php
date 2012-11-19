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
class CreateAggregate extends ActionAggregate
{
    /** @var string */
    protected $template;

    /** @var Listener\Options\SuccessOptions */
    protected $successOptions;

    public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function setSuccessOptions(Listener\Options\SuccessOptions $options)
    {
        $this->successOptions = $options;
    }

    public function attach(EventManager $eventManager)
    {
        $this->getAggregate()->addListener(
            new Listener\CreateFormRetrievalListener($this->service),
            'CrudController.preCreate'
        );
        $this->getAggregate()->addListener(
            new Listener\FormSubmissionRedirectListener($this->service, $this->successOptions, $this->template),
            'CrudController.create'
        );
        parent::attach($eventManager);
    }
}
