<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Listener;

use DkplusBase\Crud\Service\ServiceInterface as Service;
use Zend\EventManager\EventInterface;

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

    /** @var string */
    protected $template;

    public function __construct(Service $service, Options\SuccessOptions $options, $template)
    {
        $this->service  = $service;
        $this->options  = $options;
        $this->template = $template;
    }

    public function execute(EventInterface $event)
    {
        $opt        = $this->options;
        $ctrl       = $event->getTarget();
        $form       = $event->getParam('form');
        $identifier = $event->getParam('identifier');
        $storage    = $identifier === null
                    ? array($this->service, 'create')
                    : array($this->service, 'update');

        return $ctrl->dsl()
                    ->render($this->template)
                    ->and()->use($form)->and()->assign()
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
