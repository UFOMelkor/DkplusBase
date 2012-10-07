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

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
     */
    public function returnsDslWhileCreating()
    {
        $this->assertDsl($this->controller->createAction());
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
     */
    public function usesFormGivenByConstructorWhileCreating()
    {
        $dsl = $this->getDslMockBuilder()
                    ->withMockedPhrases(array('assign'))
                    ->getMock();
        $dsl->expects($this->at(0))
            ->method('__call')
            ->with('use', array($this->form))
            ->will($this->returnSelf());
        $dsl->expects($this->at(2))
            ->method('assign')
            ->will($this->returnSelf());

        $this->controller->createAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
     */
    public function validesFormAgainstPostRedirectGetWhileCreating()
    {
        $dsl = $this->getDslMockBuilder()
                    ->withMockedPhrases(array('validate', 'against'))
                    ->getMock();
        $dsl->expects($this->once())
            ->method('validate')
            ->will($this->returnSelf());
        $dsl->expects($this->once())
            ->method('against')
            ->with('postredirectget')
            ->will($this->returnSelf());

        $this->controller->createAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
     */
    public function storesDataOnSuccessIntoServiceWhileCreating()
    {
        $dsl = $this->getDslMockBuilder()
                    ->withMockedPhrases(array('onSuccess'))
                    ->getMock();

        $successDsl = $this->getDslMockBuilder()
                           ->withMockedPhrases(array('store', 'formData', 'into'))
                           ->getMock();
        $successDsl->expects($this->once())
                   ->method('store')
                   ->will($this->returnSelf());
        $successDsl->expects($this->once())
                   ->method('formData')
                   ->will($this->returnSelf());
        $successDsl->expects($this->once())
                   ->method('into')
                   ->with(array($this->service, 'create'))
                   ->will($this->returnSelf());

        $dsl->expects($this->once())
            ->method('onSuccess')
            ->with($successDsl)
            ->will($this->returnSelf());

        $this->controller->createAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
     */
    public function redirectsAfterCreating()
    {
        $dsl = $this->getDslMockBuilder()
                    ->withMockedPhrases(array('onSuccess'))
                    ->getMock();

        $successDsl = $this->expectsDsl()
                           ->toRedirectToRoute('home', array($this->controller, 'getCreationRedirectData'));

        $dsl->expects($this->once())
            ->method('onSuccess')
            ->with($successDsl)
            ->will($this->returnSelf());

        $this->controller->createAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
     */
    public function canConfigureTheRedirectTargetAfterCreating()
    {
        $dsl = $this->getDslMockBuilder()
                    ->withMockedPhrases(array('onSuccess'))
                    ->getMock();

        $successDsl = $this->expectsDsl()
                           ->toRedirectToRoute('crud/read', array($this->controller, 'getCreationRedirectData'));

        $dsl->expects($this->once())
            ->method('onSuccess')
            ->with($successDsl)
            ->will($this->returnSelf());

        $this->controller->setRedirectRouteForSuccessfulCreating('crud/read');
        $this->controller->createAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
     * @testdox adds a callback as success message after creating
     */
    public function addsCallbackAsSuccessMessageAfterCreating()
    {
        $dsl = $this->getDslMockBuilder()
                    ->withMockedPhrases(array('onSuccess'))
                    ->getMock();

        $successDsl = $this->expectsDsl()
                           ->toAddFlashMessage(array($this->controller, 'getCreationMessage'), 'success');

        $dsl->expects($this->once())
            ->method('onSuccess')
            ->with($successDsl)
            ->will($this->returnSelf());

        $this->controller->createAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
     */
    public function assignsTheFormMessagesWhenAnAjaxRequestIsDetectedWhileCreating()
    {
        $dsl = $this->getDslMockBuilder()
                    ->withMockedPhrases(array('onAjaxRequest'))
                    ->getMock();

        $ajaxDsl = $this->getDslMockBuilder()
                        ->usedAt(2)
                        ->withMockedPhrases(array('assign', 'formMessages'))
                        ->getMock();
        $ajaxDsl->expects($this->once())
                ->method('assign')
                ->will($this->returnSelf());
        $ajaxDsl->expects($this->once())
                ->method('formMessages')
                ->will($this->returnSelf());

        $dsl->expects($this->once())
            ->method('onAjaxRequest')
            ->with($ajaxDsl)
            ->will($this->returnSelf());

        $this->controller->createAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
     */
    public function returnsJesonWhenCreating()
    {
        $dsl = $this->getDslMockBuilder()
                    ->withMockedPhrases(array('onAjaxRequest'))
                    ->getMock();

        $ajaxDsl = $this->getDslMockBuilder()
                        ->usedAt(2)
                        ->withMockedPhrases(array('asJson'))
                        ->getMock();
        $ajaxDsl->expects($this->once())
                ->method('asJson')
                ->will($this->returnSelf());

        $dsl->expects($this->once())
            ->method('onAjaxRequest')
            ->with($ajaxDsl)
            ->will($this->returnSelf());

        $this->controller->createAction();
    }
}
