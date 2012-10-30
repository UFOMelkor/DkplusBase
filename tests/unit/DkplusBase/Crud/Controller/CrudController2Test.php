<?php
/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage Crud\Controller
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Controller;

use DkplusControllerDsl\Test\TestCase;

/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage Crud\Controller
 * @author     Oskar Bley <oskar@programming-php.net>
 * @covers     DkplusBase\Crud\Controller\CrudController
 */
class CrudController2Test extends TestCase
{
    /** @var CrudController */
    protected $controller;

    /** @var \DkplusBase\Crud\Service\ServiceInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $service;

    protected function setUp()
    {
        parent::setUp();
        $this->service    = $this->getMockForAbstractClass('DkplusBase\Crud\Service\ServiceInterface');
        $this->controller = new CrudController($this->service);
        $this->setUpController($this->controller);
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @testdox is a dsl controller
     */
    public function isDslController()
    {
        $this->assertInstanceOf('DkplusControllerDsl\Controller\AbstractActionController', $this->controller);
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     */
    public function implementsEventManagerAware()
    {
        $this->assertInstanceOf('Zend\EventManager\EventManagerAwareInterface', $this->controller);
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @covers DkplusBase\Crud\Controller\CrudController::setEventManager
     */
    public function hasCrudControllerAsEventIdentifier()
    {
        $eventManager = $this->getMockForAbstractClass('Zend\EventManager\EventManagerInterface');
        $eventManager->expects($this->once())
                     ->method('addIdentifiers')
                     ->with('DkplusBase\Crud\Controller\CrudController');
        $this->controller->setEventManager($eventManager);
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/read
     */
    public function returnsDslWhileReading()
    {
        $this->assertDsl($this->controller->readAction());
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/read
     * @covers DkplusBase\Crud\Controller\CrudController::readAction
     */
    public function triggersAnEventBeforeReading()
    {
        $eventManager = $this->getMockForAbstractClass('Zend\EventManager\EventManagerInterface');
        $eventManager->expects($this->at(3))
                     ->method('trigger')
                     ->with('CrudController.preRead', $this->controller);

        $this->controller->setEventManager($eventManager);
        $this->controller->readAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/read
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
     * @group unit
     * @group crud/read
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
     * @group unit
     * @group crud/read
     */
    public function canConfigureTheRouteMatchParameterForReading()
    {
        $this->setRouteMatchParams(array('id' => 42, 'foo' => 58));

        $this->service->expects($this->once())
                      ->method('get')
                      ->with(58);

        $this->controller->setRouteMatchIdentifier('foo');
        $this->controller->readAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/read
     * @testdox assigns the data from the service if the data are found while reading
     */
    public function assignsDataFromServiceIfDataAreFoundWhileReading()
    {
        $data = $this->getMock('stdClass');

        $this->service->expects($this->any())
                      ->method('get')
                      ->will($this->returnValue($data));

        $this->expectsDsl()->toAssign($data, 'entity');

        $this->controller->readAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/read
     */
    public function triggersAnEventWithTheEntityWhileReading()
    {
        $entity = $this->getMock('stdClass');

        $this->service->expects($this->any())
                      ->method('get')
                      ->will($this->returnValue($entity));

        $eventManager = $this->getMockForAbstractClass('Zend\EventManager\EventManagerInterface');
        $eventManager->expects($this->at(4))
                     ->method('trigger')
                     ->with('CrudController.read', $this->controller, array('entity' => $entity));
        $this->controller->setEventManager($eventManager);

        $this->controller->readAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/read
     */
    public function canReplaceTheReadEntityWithAnEventListener()
    {
        $entity = $this->getMock('stdClass');

        $eventResult = $this->getMockIgnoringConstructor('Zend\EventManager\ResponseCollection');
        $eventResult->expects($this->any())
                    ->method('first')
                    ->will($this->returnValue($entity));
        $eventResult->expects($this->any())
                    ->method('count')
                    ->will($this->returnValue(1));

        $eventManager = $this->getMockForAbstractClass('Zend\EventManager\EventManagerInterface');
        $eventManager->expects($this->any())
                     ->method('trigger')
                     ->will($this->returnValue($eventResult));

        $this->expectsDsl()->toAssign($entity);

        $this->controller->setEventManager($eventManager);
        $this->controller->readAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/read
     */
    public function replacesContentWithAnotherControllerActionWhenNoDataHasBeenFoundWhileReading()
    {
        $exception = $this->getMockIgnoringConstructor('DkplusBase\Service\Exception\EntityNotFound');
        $this->service->expects($this->any())
                      ->method('get')
                      ->will($this->throwException($exception));

        $this->expectsDsl()->toReplaceContentWithControllerAction();

        $this->controller->readAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/read
     */
    public function setsAn404ResponseHeaderButIgnoresZfErrorHandlingWhenNoDataHasBeenFoundWhileReading()
    {
        $exception = $this->getMockIgnoringConstructor('DkplusBase\Service\Exception\EntityNotFound');
        $this->service->expects($this->any())
                      ->method('get')
                      ->will($this->throwException($exception));

        $this->expectsDsl()->toMarkPageAsNotFound(true);

        $this->controller->readAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/read
     */
    public function canConfigurateControllerActionForContentReplacingWhenNoDataHasBeenFoundWhileReading()
    {
        $controller  = 'my-controller';
        $action      = 'my-action';
        $routeParams = array('my-route' => 'params');

        $options = $this->getMockIgnoringConstructor('DkplusBase\Crud\Controller\NotFoundOptions');
        $options->expects($this->any())
                ->method('getContentReplaceController')
                ->will($this->returnValue($controller));
        $options->expects($this->any())
                ->method('getContentReplaceAction')
                ->will($this->returnValue($action));
        $options->expects($this->any())
                ->method('getContentReplaceRouteParams')
                ->will($this->returnValue($routeParams));

        $exception = $this->getMockIgnoringConstructor('DkplusBase\Service\Exception\EntityNotFound');
        $this->controller->setNotFoundOptionsForReading($options);
        $this->service->expects($this->any())
                      ->method('get')
                      ->will($this->throwException($exception));

        $this->expectsDsl()->toReplaceContentWithControllerAction($controller, $action, $routeParams);

        $this->controller->readAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/read
     */
    public function addsNoErrorMessageUntilItIsConfiguratedWhenNoDataHasBeenFoundWhileReading()
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
     * @group unit
     * @group crud/read
     */
    public function canConfigurateA404NotFoundMessageWhenNoDataHasBeenFoundWhileReading()
    {
        $message   = 'could not found any data';

        $options = $this->getMockIgnoringConstructor('DkplusBase\Crud\Controller\NotFoundOptions');
        $options->expects($this->any())
                ->method('hasErrorMessage')
                ->will($this->returnValue(true));
        $options->expects($this->any())
                ->method('getErrorMessage')
                ->will($this->returnValue($message));

        $exception = $this->getMockIgnoringConstructor('DkplusBase\Service\Exception\EntityNotFound');
        $this->controller->setNotFoundOptionsForReading($options);
        $this->service->expects($this->any())
                      ->method('get')
                      ->will($this->throwException($exception));

        $this->expectsDsl()->toAddFlashMessage($message, 'notFound');

        $this->controller->readAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/create
     */
    public function returnsDslWhileCreating()
    {
        $this->assertDsl($this->controller->createAction());
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/create
     * @covers DkplusBase\Crud\Controller\CrudController::createAction
     */
    public function triggersAnEventBeforeCreating()
    {
        $eventManager = $this->getMockForAbstractClass('Zend\EventManager\EventManagerInterface');
        $eventManager->expects($this->once())
                     ->method('trigger')
                     ->with('CrudController.preCreate', $this->controller);

        $this->controller->setEventManager($eventManager);
        $this->controller->createAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/create
     */
    public function usesCreationFormFromServiceWhileCreating()
    {
        $form = $this->getMockForAbstractClass('Zend\Form\FormInterface');

        $this->service->expects($this->any())
                      ->method('getCreationForm')
                      ->will($this->returnValue($form));

        $this->expectsDslToUseForm($form);
        $this->controller->createAction();
    }

    protected function expectsDslToUseForm($form)
    {
        $dsl = $this->getDslMockBuilder()
                    ->withMockedPhrases(array('assign'))
                    ->getMock();
        $dsl->expects($this->at(0))
            ->method('__call')
            ->with('use', array($form))
            ->will($this->returnSelf());
        $dsl->expects($this->at(2))
            ->method('assign')
            ->will($this->returnSelf());
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/create
     */
    public function validesFormAgainstPostRedirectGetWhileCreating()
    {
        $this->expectsDslToValidateFormAgainstPostRedirectGet();
        $this->controller->createAction();
    }

    protected function expectsDslToValidateFormAgainstPostRedirectGet()
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
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/create
     */
    public function storesDataOnSuccessIntoServiceWhileCreating()
    {
        $dsl = $this->getDslMockBuilder()
                    ->withMockedPhrases(array('onSuccess'))
                    ->getMock();

        $successDsl = $this->expectsDslToStoreDataIntoMethod('create');

        $dsl->expects($this->once())
            ->method('onSuccess')
            ->with($successDsl)
            ->will($this->returnSelf());

        $this->controller->createAction();
    }

    /**
     * @param string $serviceMethod
     * @param int|null $additionalArgument
     * @return \DkplusControllerDsl\Dsl\DslInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function expectsDslToStoreDataIntoMethod($serviceMethod, $additionalArgument = null)
    {
        $phrases = array('store', 'formData', 'into');

        if ($additionalArgument !== null) {
            $phrases[] = 'with';
        }

        $dsl = $this->getDslMockBuilder()
                    ->withMockedPhrases($phrases)
                    ->getMock();
        $dsl->expects($this->once())
            ->method('store')
            ->will($this->returnSelf());
        $dsl->expects($this->once())
            ->method('formData')
            ->will($this->returnSelf());
        $dsl->expects($this->once())
            ->method('into')
            ->with(array($this->service, $serviceMethod))
            ->will($this->returnSelf());

        if ($additionalArgument !== null) {
            $dsl->expects($this->at(3)) //atLeastOnce() does not work here
                ->method('with')
                ->with($additionalArgument)
                ->will($this->returnSelf());
            $dsl->expects($this->any())
                ->method('with')
                ->will($this->returnSelf());

        }
        return $dsl;
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/create
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
     * @group unit
     * @group crud/create
     */
    public function canConfigureTheRedirectTargetAfterCreating()
    {
        $dsl = $this->getDslMockBuilder()
                    ->withMockedPhrases(array('onSuccess'))
                    ->getMock();

        $successDsl = $this->expectsDsl()
                           ->toRedirectToRoute('crud/read');

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
     * @group unit
     * @group crud/create
     * @testdox adds a callback as success message after creation
     */
    public function addsCallbackAsSuccessMessageAfterCreation()
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
     * @group unit
     * @group crud/create
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
     * @group unit
     * @group crud/create
     */
    public function returnsJsonWhenAnAjaxRequestIsDetectedWhileCreating()
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

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/update
     */
    public function returnsDslWhileUpdating()
    {
        $this->assertDsl($this->controller->updateAction());
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/update
     */
    public function usesUpdateFormFromServiceWhileUpdating()
    {
        $this->setRouteMatchParams(array('id' => 64));

        $form = $this->getMockForAbstractClass('Zend\Form\FormInterface');

        $this->service->expects($this->once())
                      ->method('getUpdateForm')
                      ->with(64)
                      ->will($this->returnValue($form));

        $this->expectsDslToUseForm($form);
        $this->controller->updateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/update
     */
    public function canConfigureTheRouteMatchParameterForUpdating()
    {
        $this->setRouteMatchParams(array('id' => 64, 'foo' => 13));

        $this->service->expects($this->once())
                      ->method('getUpdateForm')
                      ->with(13);

        $this->controller->setRouteMatchIdentifier('foo');
        $this->controller->updateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/update
     */
    public function validesFormAgainstPostRedirectGetWhileUpdating()
    {
        $this->expectsDslToValidateFormAgainstPostRedirectGet();
        $this->controller->updateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/update
     */
    public function storesDataOnSuccessIntoServiceWithIdWhileUpdating()
    {
        $this->setRouteMatchParams(array('id' => 87));

        $dsl = $this->getDslMockBuilder()
                    ->withMockedPhrases(array('onSuccess'))
                    ->getMock();

        $successDsl = $this->expectsDslToStoreDataIntoMethod('update', 87);

        $dsl->expects($this->once())
            ->method('onSuccess')
            ->with($successDsl)
            ->will($this->returnSelf());

        $this->controller->updateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/update
     */
    public function redirectsAfterUpdating()
    {
        $this->setMatchedRouteName('foo/bar/baz');

        $dsl = $this->getDslMockBuilder()
                    ->withMockedPhrases(array('onSuccess'))
                    ->getMock();

        $successDsl = $this->expectsDsl()
                           ->toRedirectToRoute('foo/bar/baz', array($this->controller, 'getUpdatingRedirectData'));

        $dsl->expects($this->once())
            ->method('onSuccess')
            ->with($successDsl)
            ->will($this->returnSelf());

        $this->controller->updateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/update
     */
    public function canConfigureTheRedirectTargetAfterUpdating()
    {
        $dsl = $this->getDslMockBuilder()
                    ->withMockedPhrases(array('onSuccess'))
                    ->getMock();

        $successDsl = $this->expectsDsl()
                           ->toRedirectToRoute('crud/read');

        $dsl->expects($this->once())
            ->method('onSuccess')
            ->with($successDsl)
            ->will($this->returnSelf());

        $this->controller->setRedirectRouteForSuccessfulUpdating('crud/read');
        $this->controller->updateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/update
     * @testdox adds a callback as success message after updating
     */
    public function addsCallbackAsSuccessMessageAfterUpdating()
    {
        $dsl = $this->getDslMockBuilder()
                    ->withMockedPhrases(array('onSuccess'))
                    ->getMock();

        $successDsl = $this->expectsDsl()
                           ->toAddFlashMessage(array($this->controller, 'getUpdatingMessage'), 'success');

        $dsl->expects($this->once())
            ->method('onSuccess')
            ->with($successDsl)
            ->will($this->returnSelf());

        $this->controller->updateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/update
     */
    public function assignsTheFormMessagesWhenAnAjaxRequestIsDetectedWhileUpdating()
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

        $this->controller->updateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/update
     */
    public function returnsJsonWhenAnAjaxRequestIsDetectedWhileUpdating()
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

        $this->controller->updateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/update
     */
    public function returnsDslWhenNoDataHasBeenFoundWhileUpdating()
    {
        $exception = $this->getMockIgnoringConstructor('DkplusBase\Service\Exception\EntityNotFound');
        $this->service->expects($this->any())
                      ->method('getUpdateForm')
                      ->will($this->throwException($exception));
        $this->assertDsl($this->controller->updateAction());
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/update
     */
    public function replacesContentWithAnotherControllerActionWhenNoDataHasBeenFoundWhileUpdating()
    {
        $exception = $this->getMockIgnoringConstructor('DkplusBase\Service\Exception\EntityNotFound');
        $this->service->expects($this->any())
                      ->method('getUpdateForm')
                      ->will($this->throwException($exception));

        $this->expectsDsl()->toReplaceContentWithControllerAction();

        $this->controller->updateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/update
     */
    public function setsAn404ResponseHeaderButIgnoresZfErrorHandlingWhenNoDataHasBeenFoundWhileUpdating()
    {
        $exception = $this->getMockIgnoringConstructor('DkplusBase\Service\Exception\EntityNotFound');
        $this->service->expects($this->any())
                      ->method('getUpdateForm')
                      ->will($this->throwException($exception));

        $this->expectsDsl()->toMarkPageAsNotFound(true);

        $this->controller->updateAction();
    }
    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/update
     */
    public function canConfigurateControllerActionForContentReplacingWhenNoDataHasBeenFoundWhileUpdating()
    {
        $controller  = 'my-controller';
        $action      = 'my-action';
        $routeParams = array('my-route' => 'params');

        $options = $this->getMockIgnoringConstructor('DkplusBase\Crud\Controller\NotFoundOptions');
        $options->expects($this->any())
                ->method('getContentReplaceController')
                ->will($this->returnValue($controller));
        $options->expects($this->any())
                ->method('getContentReplaceAction')
                ->will($this->returnValue($action));
        $options->expects($this->any())
                ->method('getContentReplaceRouteParams')
                ->will($this->returnValue($routeParams));

        $exception = $this->getMockIgnoringConstructor('DkplusBase\Service\Exception\EntityNotFound');
        $this->controller->setNotFoundOptionsForUpdating($options);
        $this->service->expects($this->any())
                      ->method('getUpdateForm')
                      ->will($this->throwException($exception));

        $this->expectsDsl()->toReplaceContentWithControllerAction($controller, $action, $routeParams);

        $this->controller->updateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/update
     */
    public function addsNoErrorMessageUntilItIsConfiguratedWhenNoDataHasBeenFoundWhileUpdating()
    {
        $exception = $this->getMockIgnoringConstructor('DkplusBase\Service\Exception\EntityNotFound');
        $this->service->expects($this->any())
                      ->method('getUpdateForm')
                      ->will($this->throwException($exception));

        $this->expectsDsl()->toDoNotAddFlashMessages();

        $this->controller->updateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/update
     */
    public function canConfigurateA404NotFoundMessageWhenNoDataHasBeenFoundWhileUpdating()
    {
        $message   = 'could not found any data';
        $exception = $this->getMockIgnoringConstructor('DkplusBase\Service\Exception\EntityNotFound');

        $options = $this->getMockIgnoringConstructor('DkplusBase\Crud\Controller\NotFoundOptions');
        $options->expects($this->any())
                ->method('hasErrorMessage')
                ->will($this->returnValue(true));
        $options->expects($this->any())
                ->method('getErrorMessage')
                ->will($this->returnValue($message));

        $this->controller->setNotFoundOptionsForUpdating($options);
        $this->service->expects($this->any())
                      ->method('getUpdateForm')
                      ->will($this->throwException($exception));

        $this->expectsDsl()->toAddFlashMessage($message, 'notFound');

        $this->controller->updateAction();
    }


    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/delete
     */
    public function returnsDslWhileDeleting()
    {
        $this->assertDsl($this->controller->deleteAction());
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/delete
     */
    public function returnsDslWhenNoDataHasBeenFoundWhileDeleting()
    {
        $exception = $this->getMockIgnoringConstructor('DkplusBase\Service\Exception\EntityNotFound');
        $this->service->expects($this->any())
                      ->method('delete')
                      ->will($this->throwException($exception));
        $this->assertDsl($this->controller->deleteAction());
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/delete
     * @testdox uses the id from the router to delete when deleting
     */
    public function usesIdFromRouteToDelete()
    {
        $this->setRouteMatchParams(array('id' => 42));

        $this->service->expects($this->once())
                      ->method('delete')
                      ->with(42);

        $this->controller->deleteAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/delete
     */
    public function canConfigureTheRouteMatchParameterForDeleting()
    {
        $this->setRouteMatchParams(array('id' => 42, 'foo' => 58));

        $this->service->expects($this->once())
                      ->method('delete')
                      ->with(58);

        $this->controller->setRouteMatchIdentifier('foo');
        $this->controller->deleteAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/delete
     */
    public function replacesContentWithAnotherControllerActionWhenNoDataHasBeenFoundWhileDeleting()
    {
        $exception = $this->getMockIgnoringConstructor('DkplusBase\Service\Exception\EntityNotFound');
        $this->service->expects($this->any())
                      ->method('delete')
                      ->will($this->throwException($exception));

        $this->expectsDsl()->toReplaceContentWithControllerAction();

        $this->controller->deleteAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/delete
     */
    public function setsAn404ResponseHeaderButIgnoresZfErrorHandlingWhenNoDataHasBeenFoundWhileDeleting()
    {
        $exception = $this->getMockIgnoringConstructor('DkplusBase\Service\Exception\EntityNotFound');
        $this->service->expects($this->any())
                      ->method('delete')
                      ->will($this->throwException($exception));

        $this->expectsDsl()->toMarkPageAsNotFound(true);

        $this->controller->deleteAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/delete
     */
    public function canConfigurateControllerActionForContentReplacingWhenNoDataHasBeenFoundWhileDeleting()
    {
        $controller  = 'my-controller';
        $action      = 'my-action';
        $routeParams = array('my-route' => 'params');

        $options = $this->getMockIgnoringConstructor('DkplusBase\Crud\Controller\NotFoundOptions');
        $options->expects($this->any())
                ->method('getContentReplaceController')
                ->will($this->returnValue($controller));
        $options->expects($this->any())
                ->method('getContentReplaceAction')
                ->will($this->returnValue($action));
        $options->expects($this->any())
                ->method('getContentReplaceRouteParams')
                ->will($this->returnValue($routeParams));

        $exception = $this->getMockIgnoringConstructor('DkplusBase\Service\Exception\EntityNotFound');
        $this->controller->setNotFoundOptionsForDeletion($options);
        $this->service->expects($this->any())
                      ->method('delete')
                      ->will($this->throwException($exception));

        $this->expectsDsl()->toReplaceContentWithControllerAction($controller, $action, $routeParams);

        $this->controller->deleteAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/delete
     */
    public function addsNo404NotFoundMessageUntilItIsConfiguratedWhenNoDataHasBeenFoundWhileDeleting()
    {
        $exception = $this->getMockIgnoringConstructor('DkplusBase\Service\Exception\EntityNotFound');
        $this->service->expects($this->any())
                      ->method('delete')
                      ->will($this->throwException($exception));

        $this->expectsDsl()->toDoNotAddFlashMessages();

        $this->controller->deleteAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/delete
     */
    public function canConfigurateA404NotFoundMessageWhenNoDataHasBeenFoundWhileDeleting()
    {
        $message   = 'could not found any data';
        $exception = $this->getMockIgnoringConstructor('DkplusBase\Service\Exception\EntityNotFound');

        $options = $this->getMockIgnoringConstructor('DkplusBase\Crud\Controller\NotFoundOptions');
        $options->expects($this->any())
                ->method('hasErrorMessage')
                ->will($this->returnValue(true));
        $options->expects($this->any())
                ->method('getErrorMessage')
                ->will($this->returnValue($message));
        $this->controller->setNotFoundOptionsForDeletion($options);
        $this->service->expects($this->any())
                      ->method('delete')
                      ->will($this->throwException($exception));

        $this->expectsDsl()->toAddFlashMessage($message, 'notFound');

        $this->controller->deleteAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/delete
     */
    public function redirectsWhenDeletionHasBeenSuccessful()
    {
        $this->expectsDsl()->toRedirectToRoute();

        $this->controller->deleteAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/delete
     */
    public function canConfigurateRouteForRedirectionWhenDeletionHasBeenSuccessful()
    {
        $route = 'foo/bar';

        $this->expectsDsl()->toRedirectToRoute($route);

        $this->controller->setRedirectRouteForSuccessfulDeletion($route);
        $this->controller->deleteAction();
    }

    /**
     * @ test
     * @group Component/Controller
     * @group unit
     * @group crud/delete
     */
    public function addsSuccessMessageWhenDeletionHasBeenSuccessful()
    {
        $this->expectsDsl()->toAddFlashMessage();

        $this->controller->deleteAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/paginate
     */
    public function assignsDataFromServiceWhilePaginating()
    {
        $this->setPostData(array());
        $paginator = $this->getMockIgnoringConstructor('Zend\Paginator\Paginator');

        $this->service->expects($this->once())
                      ->method('getPaginator')
                      ->will($this->returnValue($paginator));
        $this->expectsDsl()->toAssign($paginator, 'paginator');

        $this->controller->paginateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/paginate
     */
    public function getsPageNumberFromRouteMatchWhilePaginating()
    {
        $this->setPostData(array());
        $this->setRouteMatchParams(array('page' => 5));

        $this->service->expects($this->once())
                      ->method('getPaginator')
                      ->with(5);

        $this->controller->paginateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/paginate
     */
    public function canConfigurateTheRouteMatchParameterWhilePaginating()
    {
        $this->setPostData(array());
        $this->setRouteMatchParams(array('page' => 5, 'p' => 10));

        $this->service->expects($this->once())
                      ->method('getPaginator')
                      ->with(10);

        $this->controller->setPageParameter('p');
        $this->controller->paginateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/paginate
     */
    public function hasAnDefaultNumberOfEntriesPerPageOf10()
    {
        $this->setPostData(array());
        $this->service->expects($this->once())
                      ->method('getPaginator')
                      ->with($this->anything(), 10);

        $this->controller->paginateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/paginate
     */
    public function canConfigurateTheNumberOfEntriesPerPageWhilePaginating()
    {
        $this->setPostData(array());
        $this->service->expects($this->once())
                      ->method('getPaginator')
                      ->with($this->anything(), 20);

        $this->controller->setItemCountPerPage(20);
        $this->controller->paginateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/paginate
     */
    public function usesPostAsDefaultSearchDataForPaginating()
    {
        $post = array('foo' => 'bar', 'baz' => 'foo');
        $this->setPostData($post);

        $this->service->expects($this->once())
                      ->method('getPaginator')
                      ->with($this->anything(), $this->anything(), $post);

        $this->controller->paginateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/list
     */
    public function assignsDataFromServiceWhileListing()
    {
        $this->setPostData(array());
        $data = array(array('id' => 4, 'name' => 'foo'), array('id' => 5, 'name' => 'bar'));

        $this->service->expects($this->once())
                      ->method('getAll')
                      ->will($this->returnValue($data));
        $this->expectsDsl()->toAssign($data, 'items');

        $this->controller->listAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/list
     */
    public function usesPostAsDefaultSearchDataForListing()
    {
        $post = array('foo' => 'bar', 'baz' => 'foo');
        $this->setPostData($post);

        $this->service->expects($this->once())
                      ->method('getAll')
                      ->with($post);

        $this->controller->listAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/create
     */
    public function hasNoCreationRedirectData()
    {
        $container = $this->getMockForAbstractClass('DkplusControllerDsl\Dsl\ContainerInterface');
        $this->assertSame(array(), $this->controller->getCreationRedirectData($container));
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/create
     * @testdox has a simple creation message
     */
    public function hasSimpleCreationMessage()
    {
        $container = $this->getMockForAbstractClass('DkplusControllerDsl\Dsl\ContainerInterface');
        $this->assertSame('Item has been created.', $this->controller->getCreationMessage($container));
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/update
     */
    public function hasRouteMatchParamsAsUpdateRedirectData()
    {
        $container        = $this->getMockForAbstractClass('DkplusControllerDsl\Dsl\ContainerInterface');
        $routeMatchParams = array('foo' => 'bar', 'bar' => 'baz');

        $this->setRouteMatchParams($routeMatchParams);
        $this->assertSame($routeMatchParams, $this->controller->getUpdatingRedirectData($container));
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/update
     * @testdox has a simple update message
     */
    public function hasSimpleUpdateMessage()
    {
        $container = $this->getMockForAbstractClass('DkplusControllerDsl\Dsl\ContainerInterface');
        $this->assertSame('Item has been updated.', $this->controller->getUpdatingMessage($container));
    }
}