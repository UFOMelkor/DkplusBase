<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Listener;

use Zend\EventManager\EventInterface;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class NotFoundReplaceListener implements ListenerInterface
{
    /** @var Options\NotFoundReplaceOptions */
    private $options;

    public function __construct(Options\NotFoundReplaceOptions $options)
    {
        $this->options = $options;
    }

    public function execute(EventInterface $event)
    {
        $opt        = $this->options;
        $controller = $event->getTarget();

        $dsl = $controller->dsl()->replaceContent()->with()->controllerAction(
            $opt->getContentReplaceController(),
            $opt->getContentReplaceAction(),
            $opt->getContentReplaceRouteParams()
        )->and()->with()->route($opt->getContentReplaceRoute())
         ->and()->pageNotFound()->but()->ignore404NotFoundController();

        if ($opt->hasErrorMessage()) {
            $dsl->and()->add()->notFound()->message($opt->getErrorMessage());
        }
        return $dsl;
    }
}
