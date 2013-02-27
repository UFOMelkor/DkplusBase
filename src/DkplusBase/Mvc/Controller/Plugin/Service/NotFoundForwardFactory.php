<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Mvc\Controller\Plugin
 * @author     Oskar Bley <oskar@programming-php.net
 */

namespace DkplusBase\Mvc\Controller\Plugin\Service;

use DkplusBase\Mvc\Controller\Plugin\NotFoundForward;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceLocator;

/**
 * @category   DkplusBase
 * @package    Base
 * @subpackage Mvc\Controller\Plugin
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class NotFoundForwardFactory implements FactoryInterface
{
    public function createService(ServiceLocator $serviceLocator)
    {
        $notFoundStrategy = $serviceLocator->getServiceLocator()
                                           ->get('ViewManager')
                                           ->getRouteNotFoundStrategy();
        return new NotFoundForward($notFoundStrategy);
    }
}
