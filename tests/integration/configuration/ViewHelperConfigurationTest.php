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
 * Testing whether the view helpers can be loaded from the ViewHelperManager or not.
 *
 * @category   Dkplus
 * @package    Base
 * @subpackage IntegrationTest
 * @author     Oskar Bley <oskar@programming-php.net>
 * @coversNothing
 */
class ViewHelperConfigurationTest extends TestCase
{
    /** @var \Zend\View\HelperPluginManager */
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
        self::$pluginManager = $application->getServiceManager()->get('ViewHelperManager');
    }

    /**
     * @test
     */
    public function providesTheFlashMessengerHelper()
    {
        $this->assertInstanceOf('DkplusBase\View\Helper\FlashMessenger', self::$pluginManager->get('flashMessenger'));
    }
}
