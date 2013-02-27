<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage IntegrationTest
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Mvc\Application;

/**
 * Testing whether the controller plugins can be loaded from the ControllerPluginManager or not.
 *
 * @category   Dkplus
 * @package    Base
 * @subpackage IntegrationTest
 * @author     Oskar Bley <oskar@programming-php.net>
 * @coversNothing
 */
class ControllerPluginConfigurationTest extends TestCase
{
    /** @var \Zend\Mvc\Controller\PluginManager */
    private static $pluginManager;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $application = Application::init(
            array(
                'modules' => array('DkplusBase'),
                'module_listener_options' => array(
                    'module_paths' => array(__DIR__ . '../../')
                )
            )
        );
        self::$pluginManager = $application->getServiceManager()->get('ControllerPluginManager');
    }

    /** @test */
    public function providesTheNotFoundForward()
    {
        $this->assertInstanceOf(
            'DkplusBase\Mvc\Controller\Plugin\NotFoundForward',
            self::$pluginManager->get('notFoundForward')
        );
    }
}
