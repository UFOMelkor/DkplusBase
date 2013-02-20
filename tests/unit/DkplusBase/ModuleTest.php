<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Module
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Module
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class ModuleTest extends TestCase
{
    /** @var Module */
    private $module;

    protected function setUp()
    {
        parent::setUp();
        $this->module = new Module();
    }

    /**
     * @test
     */
    public function implementsConfigProviderInterface()
    {
        $this->assertInstanceOf('Zend\ModuleManager\Feature\ConfigProviderInterface', $this->module);
    }

    /**
     * @test
     * @depends implementsConfigProviderInterface
     */
    public function providesConfigAsArray()
    {
        $this->assertInternalType('array', $this->module->getConfig());
    }

    /**
     * @test
     */
    public function implementsServiceProviderInterface()
    {
        $this->assertInstanceOf('Zend\ModuleManager\Feature\ServiceProviderInterface', $this->module);
    }

    /**
     * @test
     * @depends implementsServiceProviderInterface
     */
    public function providesServiceConfigAsArray()
    {
        $this->assertInternalType('array', $this->module->getServiceConfig());
    }

    /**
     * @test
     */
    public function implementsControllerPluginProviderInterface()
    {
        $this->assertInstanceOf('Zend\ModuleManager\Feature\ControllerPluginProviderInterface', $this->module);
    }

    /**
     * @test
     * @depends implementsControllerPluginProviderInterface
     */
    public function providesControllerPluginConfigAsArray()
    {
        $this->assertInternalType('array', $this->module->getControllerPluginConfig());
    }

    /**
     * @test
     */
    public function implementsViewHelperProviderInterface()
    {
        $this->assertInstanceOf('Zend\ModuleManager\Feature\ViewHelperProviderInterface', $this->module);
    }

    /**
     * @test
     * @depends implementsViewHelperProviderInterface
     */
    public function providesViewHelperConfigAsArray()
    {
        $this->assertInternalType('array', $this->module->getViewHelperConfig());
    }

    /**
     * @test
     */
    public function implementsAutoloaderProviderInterface()
    {
        $this->assertInstanceOf('Zend\ModuleManager\Feature\AutoloaderProviderInterface', $this->module);
    }

    /**
     * @test
     * @depends implementsAutoloaderProviderInterface
     */
    public function providesAutoloaderConfigAsArray()
    {
        $this->assertInternalType('array', $this->module->getAutoloaderConfig());
    }
}
