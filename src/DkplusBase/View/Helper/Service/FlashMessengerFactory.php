<?php
/**
 * @license MIT
 * @link    https://github.com/UFOMelkor/DkplusCrud canonical source repository
 */

namespace DkplusBase\View\Helper\Service;

use DkplusBase\View\Helper\FlashMessenger;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceLocator;

/**
 * @author Oskar Bley <oskar@programming-php.net>
 * @since  0.1.0
 */
class FlashMessengerFactory implements FactoryInterface
{
    public function createService(ServiceLocator $serviceLocator)
    {
        $controllerPlugin = $serviceLocator->getServiceLocator()
                                           ->get('ControllerPluginManager')
                                           ->get('flashmessenger');
        return new FlashMessenger($controllerPlugin);
    }
}
