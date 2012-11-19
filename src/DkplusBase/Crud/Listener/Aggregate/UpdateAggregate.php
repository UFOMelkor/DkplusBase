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
class UpdateAggregate extends ActionAggregate
{
    /** @var Listener\Options\NotFoundOptions */
    protected $notFoundOptions;

    /** @var Listener\Options\SuccessOptions */
    protected $successOptions;

    /** @var string */
    protected $template;

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

    public function attach(EventManager $eventManager)
    {
        $this->getAggregate()->addListener(new Listener\IdentifierProviderListener(), 'CrudController.preUpdate', 2);
        $this->getAggregate()->addListener(
            new Listener\UpdateFormRetrievalListener($this->service),
            'CrudController.preUpdate'
        );
        $this->getAggregate()->addListener(
            new Listener\FormSubmissionRedirectListener($this->service, $this->successOptions, $this->template),
            'CrudController.update'
        );
        $this->getAggregate()->addListener(
            new Listener\NotFoundReplaceListener($this->notFoundOptions),
            'CrudController.updateNotFound'
        );
        parent::attach($eventManager);
    }
}
