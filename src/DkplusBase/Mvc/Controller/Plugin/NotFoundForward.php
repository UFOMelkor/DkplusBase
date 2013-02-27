<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Mvc\Controller\Plugin
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Mvc\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Model\ModelInterface as ViewModelInterface;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Mvc\Controller\Plugin
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class NotFoundForward extends AbstractPlugin
{
    /** @var \Zend\Mvc\View\Http\RouteNotFoundStrategy */
    protected $notFoundStrategy;

    public function __construct($notFoundStrategy)
    {
        $this->notFoundStrategy = $notFoundStrategy;
    }

    public function dispatch($name, array $params = null, $matchedRouteName = null)
    {
        if ($matchedRouteName) {
            $routeMatch = $this->getController()->getEvent()->getRouteMatch();
            $routeMatch->setMatchedRouteName($matchedRouteName);
        }

        $this->getController()->getEvent()->getResponse()->setStatusCode(404);

        $result = $this->getController()->forward()->dispatch($name, $params);

        if ($result instanceof ViewModelInterface) {
            $this->notFoundStrategy->setNotFoundTemplate($result->getTemplate());
        }
    }
}
