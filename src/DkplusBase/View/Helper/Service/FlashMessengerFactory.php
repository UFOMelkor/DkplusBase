<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage View\Helper
 * @author     Oskar Bley <oskar@programming-php.net
 */

namespace DkplusBase\View\Helper\Service;

use DkplusBase\View\Helper\FlashMessenger;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceLocator;

/**
 * @category   DkplusBase
 * @package    Base
 * @subpackage View\Helper
 * @author     Oskar Bley <oskar@programming-php.net>
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
