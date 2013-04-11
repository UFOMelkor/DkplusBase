<?php
/**
 * @license MIT
 * @link    https://github.com/UFOMelkor/DkplusCrud canonical source repository
 */

namespace DkplusBase;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface as AutoloaderProvider;
use Zend\ModuleManager\Feature\ConfigProviderInterface as ConfigProvider;
use Zend\ModuleManager\Feature\ControllerPluginProviderInterface as ControllerPluginProvider;
use Zend\ModuleManager\Feature\ServiceProviderInterface as ServiceProvider;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface as ViewHelperProvider;

/**
 * @author Oskar Bley <oskar@programming-php.net>
 * @since  0.1.0
 */
class Module implements
    AutoloaderProvider,
    ConfigProvider,
    ControllerPluginProvider,
    ServiceProvider,
    ViewHelperProvider
{

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/../../autoload_classmap.php',
            )
        );
    }

    public function getConfig()
    {
        return array();
    }

    public function getControllerPluginConfig()
    {
        return array(
            'factories' => array(
                'notfoundforward' => 'DkplusBase\Mvc\Controller\Plugin\Service\NotFoundForwardFactory'
            )
        );
    }

    public function getServiceConfig()
    {
        return array();
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'flashmessenger' => 'DkplusBase\View\Helper\Service\FlashMessengerFactory'
            )
        );
    }
}
