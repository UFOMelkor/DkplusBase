<?php
/**
 * @license MIT
 * @link    https://github.com/UFOMelkor/DkplusCrud canonical source repository
 */

namespace DkplusBase\Mvc\Controller\Plugin\Service;

use DkplusBase\Mvc\Controller\Plugin\NotFoundForward;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceLocator;

/**
 * @author Oskar Bley <oskar@programming-php.net>
 * @since  0.1.0
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
