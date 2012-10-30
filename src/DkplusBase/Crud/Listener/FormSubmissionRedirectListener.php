<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Listener;

use DkplusBase\Crud\Service\ServiceInterface as Service;
use Zend\Mvc\MvcEvent;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class FormSubmissionRedirectListener implements ListenerInterface
{
    /** @var Options\SuccessOptions */
    protected $options;

    /** @var Service */
    protected $service;

    public function __construct(Service $service, Options\SuccessOptions $options)
    {
        $this->service = $service;
        $this->options = $options;
    }

    public function execute(MvcEvent $event)
    {
        $opt        = $this->options;
        $ctrl       = $event->getTarget();
        $form       = $event->getParam('form');
        $identifier = $event->getParam('identifier');
        $storage    = $identifier === null
                    ? array($this->service, 'create')
                    : array($this->service, 'update');

        return $ctrl->dsl()
                    ->use($form)->and()->assign()
                    ->and()->validate()->against('postredirectget')
                    ->and()->onSuccess(
                        $ctrl->dsl()
                             ->store()->formData()->into($storage)->with($identifier)
                             ->and()->redirect()
                             ->to()->route(
                                 $opt->getRedirectRoute(),
                                 array($opt, 'getComputatedRedirectRouteParams')
                             )
                             ->with()->success()->message(array($opt, 'getComputatedMessage'))
                    );
    }
}
