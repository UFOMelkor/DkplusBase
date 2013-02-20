<?php
/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage View\Helper
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\View\Helper\Service;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage View\Helper
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class FlashMessengerFactoryTest extends TestCase
{
    /** @var \Zend\View\HelperPluginManager|\PHPUnit_Framework_MockObject_MockObject */
    private $viewHelperPluginManager;

    protected function setUp()
    {
        parent::setUp();

        /* We need an flashMessenger-object but doesn't want to overwrite any method and
         * mocking of all methods causes this:
         * Declaration of Mock_FlashMessenger_df28b7fb::setSessionManager() should be compatible
         * with that of Zend\Mvc\Controller\Plugin\FlashMessenger::setSessionManager() */
        $controllerPlugin        = $this->getMock('Zend\Mvc\Controller\Plugin\FlashMessenger', array('foo'));
        $controllerPluginManager = $this->getMock('Zend\Mvc\Controller\PluginManager');
        $controllerPluginManager->expects($this->any())
                                ->method('get')
                                ->with('flashmessenger')
                                ->will($this->returnValue($controllerPlugin));

        $serviceLocator = $this->getMockForAbstractClass('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator->expects($this->any())
                       ->method('get')
                       ->with('ControllerPluginManager')
                       ->will($this->returnValue($controllerPluginManager));

        $this->viewHelperPluginManager = $this->getMockBuilder('Zend\View\HelperPluginManager')
                                              ->disableOriginalConstructor()
                                              ->getMock();
        $this->viewHelperPluginManager->expects($this->any())
                                      ->method('getServiceLocator')
                                      ->will($this->returnValue($serviceLocator));
    }

    /**
     * @test
     */
    public function providesTheFlashMessengerPlugin()
    {
        $factory    = new FlashMessengerFactory();
        $viewHelper = $factory->createService($this->viewHelperPluginManager);

        $this->assertInstanceOf('DkplusBase\View\Helper\FlashMessenger', $viewHelper);
    }
}
