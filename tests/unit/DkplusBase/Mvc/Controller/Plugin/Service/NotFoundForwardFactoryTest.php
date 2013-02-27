<?php
/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage Mvc\Controller\Plugin
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Mvc\Controller\Plugin\Service;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage Mvc\Controller\Plugin
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class NotFoundForwardFactoryTest extends TestCase
{
    /** @var \Zend\Mvc\Controller\PluginManager|\PHPUnit_Framework_MockObject_MockObject */
    private $controllerPluginManager;

    protected function setUp()
    {
        parent::setUp();

        $viewManager = $this->getMock('Zend\Mvc\View\Http\ViewManager');
        $viewManager->expects($this->any())
                    ->method('getRouteNotFoundStrategy')
                    ->will($this->returnValue($this->getMock('Zend\Mvc\View\Http\RouteNotFoundStrategy')));

        $serviceLocator = $this->getMockForAbstractClass('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator->expects($this->any())
                       ->method('get')
                       ->with('ViewManager')
                       ->will($this->returnValue($viewManager));

        $this->controllerPluginManager = $this->getMock('Zend\Mvc\Controller\PluginManager');
        $this->controllerPluginManager->expects($this->any())
                                      ->method('getServiceLocator')
                                      ->will($this->returnValue($serviceLocator));
    }

    /** @test */
    public function providesTheFlashMessengerPlugin()
    {
        $factory = new NotFoundForwardFactory();
        $plugin  = $factory->createService($this->controllerPluginManager);

        $this->assertInstanceOf('DkplusBase\Mvc\Controller\Plugin\NotFoundForward', $plugin);
    }
}
