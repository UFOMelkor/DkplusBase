<?php
/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage View\Helper
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Controller;

use DkplusControllerDsl\Test\TestCase;

/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage View\Helper
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class CrudControllerTest extends TestCase
{
    /** @var CrudController */
    protected $controller;

    /** @var \Zend\Form\FormInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $form;

    /** @var \Zend\Form\FormInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $service;

    protected function setUp()
    {
        parent::setUp();

        $this->form       = $this->getMockForAbstractClass('Zend\Form\FormInterface');
        $this->service    = $this->getMockForAbstractClass('DkplusBase\Service\CrudServiceInterface');
        $this->controller = new CrudController($this->service, $this->form);
        $this->setUpController($this->controller);
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
     * @testdox is a dsl controller
     */
    public function isDslController()
    {
        $this->assertInstanceOf('DkplusControllerDsl\Controller\AbstractActionController', $this->controller);
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
     */
    public function returnsDslWhileReading()
    {
        $this->assertDsl($this->controller->readAction());
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
     */
    public function returnsDslWhenNoDataHasBeenFoundWhileReading()
    {
        $exception = $this->getMockIgnoringConstructor('DkplusBase\Service\Exception\EntityNotFound');
        $this->service->expects($this->any())
                      ->method('get')
                      ->will($this->throwException($exception));
        $this->assertDsl($this->controller->readAction());
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
     * @testdox uses the id from the router to get data from service when reading
     */
    public function usesIdFromRouteToGetDataFromServiceWhileReading()
    {
        $this->setRouteMatchParams(array('id' => 42));

        $this->service->expects($this->once())
                      ->method('get')
                      ->with(42);

        $this->controller->readAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
     * @testdox uses the id from the router to get data from service when reading
     */
    public function assignsDataFromServiceIfDataAreFoundWhileReading()
    {
        $data = $this->getMock('stdClass');

        $this->service->expects($this->any())
                      ->method('get')
                      ->will($this->returnValue($data));

        $this->expectsDsl()->toAssign($data, 'data');

        $this->controller->readAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
     */
    public function redirectsWhenNoDataHasBeenFoundFoundWhileReading()
    {
        $exception = $this->getMockIgnoringConstructor('DkplusBase\Service\Exception\EntityNotFound');
        $this->service->expects($this->any())
                      ->method('get')
                      ->will($this->throwException($exception));

        $this->expectsDsl()->toRedirectToRoute();

        $this->controller->readAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
     */
    public function canConfigurateRouteForRedirectionWhenNoDataHasBeenFoundFoundWhileReading()
    {
        $route     = 'foo/bar';
        $exception = $this->getMockIgnoringConstructor('DkplusBase\Service\Exception\EntityNotFound');
        $this->controller->setRedirectRouteForNotFoundDataOnReading($route);
        $this->service->expects($this->any())
                      ->method('get')
                      ->will($this->throwException($exception));

        $this->expectsDsl()->toRedirectToRoute($route);

        $this->controller->readAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
     */
    public function addsNoErrorMessageUntilItIsConfiguratedWhenNoDataHasBeenFoundFoundWhileReading()
    {
        $exception = $this->getMockIgnoringConstructor('DkplusBase\Service\Exception\EntityNotFound');
        $this->service->expects($this->any())
                      ->method('get')
                      ->will($this->throwException($exception));

        $this->expectsDsl()->toDoNotAddFlashMessages();

        $this->controller->readAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
     * @testdox can configurate a error message when no data has been found while reading
     */
    public function canConfigurateErrorMessageWhenNoDataHasBeenFoundFoundWhileReading()
    {
        $message   = 'could not found any data';
        $exception = $this->getMockIgnoringConstructor('DkplusBase\Service\Exception\EntityNotFound');
        $this->controller->setErrorMessageForNotFoundDataOnReading($message);
        $this->service->expects($this->any())
                      ->method('get')
                      ->will($this->throwException($exception));

        $this->expectsDsl()->toAddFlashMessage($message, 'error');

        $this->controller->readAction();
    }
}
