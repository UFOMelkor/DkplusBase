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
class DeleteAggregate extends ActionAggregate
{
    /** @var Listener\Options\NotFoundOptions */
    protected $notFoundOptions;

    /** @var Listener\Options\SuccessOptions */
    protected $successOptions;

    public function setSuccessOptions(Listener\Options\SuccessOptions $options)
    {
        $this->successOptions = $options;
    }

    public function setNotFoundOptions(Listener\Options\NotFoundReplaceOptions $options)
    {
        $this->notFoundOptions = $options;
    }

    public function attach(EventManager $eventManager)
    {
        $this->getAggregate()->addListener(new Listener\IdentifierProviderListener(), 'CrudController.preDelete', 2);
        $this->getAggregate()->addListener(new Listener\EntityRetrievalListener($this->service), 'CrudController.preDelete');
        $this->getAggregate()->addListener(
            new Listener\DeleteRedirectListener($this->service, $this->successOptions),
            'CrudController.delete'
        );
        $this->getAggregate()->addListener(
            new Listener\NotFoundReplaceListener($this->notFoundOptions),
            'CrudController.deleteNotFound'
        );
        parent::attach($eventManager);
    }
}
