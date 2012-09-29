<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Module
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface as AutoloaderProvider;
use Zend\ModuleManager\Feature\ConfigProviderInterface as ConfigProvider;
use Zend\ModuleManager\Feature\ControllerPluginProviderInterface as ControllerPluginProvider;
use Zend\ModuleManager\Feature\ServiceProviderInterface as ServiceProvider;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface as ViewHelperProvider;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Module
 * @author     Oskar Bley <oskar@programming-php.net>
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
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getControllerPluginConfig()
    {
        return include __DIR__ . '/../../config/controller_plugin.config.php';

    }

    public function getServiceConfig()
    {
        return include __DIR__ . '/../../config/service.config.php';
    }

    public function getViewHelperConfig()
    {
        return include __DIR__ . '/../../config/view_helper.config.php';
    }
}
