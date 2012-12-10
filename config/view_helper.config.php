<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Config
 * @author     Oskar Bley <oskar@programming-php.net>
 */

use Zend\View\HelperPluginManager;
use DkplusBase\View\Helper as Helper;

return array(
    'factories' => array(
        'flashMessenger' => function (HelperPluginManager $manager) {
            $controllerPlugin = $manager->getServiceLocator()
                                        ->get('Zend\Mvc\Controller\PluginManager')
                                        ->get('Zend\Mvc\Controller\Plugin\FlashMessenger');
            return new Helper\FlashMessenger($controllerPlugin);
        }
    )
);
