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

    /** @var \DkplusBase\Service\CrudServiceInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $service;

    protected function setUp()
    {
        parent::setUp();

        $this->service    = $this->getMockForAbstractClass('DkplusBase\Service\CrudServiceInterface');
        $this->controller = new CrudController($this->service);
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
     * @group Module/DkplusBase
     * @testdox assigns the data from the service if the data are found while reading
     */
    public function assignsDataFromServiceIfDataAreFoundWhileReading()
    {
        $data = $this->getMock('stdClass');

        $this->service->expects($this->any())
                      ->method('get')
                      ->will($this->returnValue($data));

        $this->expectsDsl()->toAssign($data, 'item');

        $this->controller->readAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
     */
    public function redirectsWhenNoDataHasBeenFoundWhileReading()
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
    public function canConfigurateRouteForRedirectionWhenNoDataHasBeenFoundWhileReading()
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
     * @group Module/DkplusBase
     */
    public function canConfigurateAnErrorMessageWhenNoDataHasBeenFoundWhileReading()
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
     * @group Module/DkplusBase
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
     * @group Module/DkplusBase
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
     * @group Module/DkplusBase
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
     * @group Module/DkplusBase
     */
    public function returnsDslWhileUpdating()
    {
        $this->assertDsl($this->controller->updateAction());
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
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
     * @group Module/DkplusBase
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
     * @group Module/DkplusBase
     */
    public function validesFormAgainstPostRedirectGetWhileUpdating()
    {
        $this->expectsDslToValidateFormAgainstPostRedirectGet();
        $this->controller->updateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
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
     * @group Module/DkplusBase
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
     * @group Module/DkplusBase
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
     * @group Module/DkplusBase
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
     * @group Module/DkplusBase
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
     * @group Module/DkplusBase
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
     * @group Module/DkplusBase
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
     * @group Module/DkplusBase
     */
    public function redirectsWhenNoDataHasBeenFoundWhileUpdating()
    {
        $exception = $this->getMockIgnoringConstructor('DkplusBase\Service\Exception\EntityNotFound');
        $this->service->expects($this->any())
                      ->method('getUpdateForm')
                      ->will($this->throwException($exception));

        $this->expectsDsl()->toRedirectToRoute();

        $this->controller->updateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
     */
    public function canConfigurateRouteForRedirectionWhenNoDataHasBeenFoundWhileUpdating()
    {
        $route     = 'foo/bar';
        $exception = $this->getMockIgnoringConstructor('DkplusBase\Service\Exception\EntityNotFound');
        $this->controller->setRedirectRouteForNotFoundDataOnUpdating($route);
        $this->service->expects($this->any())
                      ->method('getUpdateForm')
                      ->will($this->throwException($exception));

        $this->expectsDsl()->toRedirectToRoute($route);

        $this->controller->updateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
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
     * @group Module/DkplusBase
     * @testdox can configurate a error message when no data has been found while updating
     */
    public function canConfigurateErrorMessageWhenNoDataHasBeenFoundWhileUpdating()
    {
        $message   = 'could not found any data';
        $exception = $this->getMockIgnoringConstructor('DkplusBase\Service\Exception\EntityNotFound');
        $this->controller->setErrorMessageForNotFoundDataOnUpdating($message);
        $this->service->expects($this->any())
                      ->method('getUpdateForm')
                      ->will($this->throwException($exception));

        $this->expectsDsl()->toAddFlashMessage($message, 'error');

        $this->controller->updateAction();
    }


    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
     */
    public function returnsDslWhileDeleting()
    {
        $this->assertDsl($this->controller->deleteAction());
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
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
     * @group Module/DkplusBase
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
     * @group Module/DkplusBase
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
     * @group Module/DkplusBase
     */
    public function redirectsWhenNoDataHasBeenFoundWhileDeleting()
    {
        $exception = $this->getMockIgnoringConstructor('DkplusBase\Service\Exception\EntityNotFound');
        $this->service->expects($this->any())
                      ->method('delete')
                      ->will($this->throwException($exception));

        $this->expectsDsl()->toRedirectToRoute();

        $this->controller->deleteAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
     */
    public function canConfigurateRouteForRedirectionWhenNoDataHasBeenFoundWhileDeleting()
    {
        $route     = 'foo/bar';
        $exception = $this->getMockIgnoringConstructor('DkplusBase\Service\Exception\EntityNotFound');
        $this->controller->setRedirectRouteForNotFoundDataOnDeletion($route);
        $this->service->expects($this->any())
                      ->method('delete')
                      ->will($this->throwException($exception));

        $this->expectsDsl()->toRedirectToRoute($route);

        $this->controller->deleteAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
     */
    public function addsNoErrorMessageUntilItIsConfiguratedWhenNoDataHasBeenFoundWhileDeleting()
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
     * @group Module/DkplusBase
     */
    public function canConfigurateAnErrorMessageWhenNoDataHasBeenFoundWhileDeleting()
    {
        $message   = 'could not found any data';
        $exception = $this->getMockIgnoringConstructor('DkplusBase\Service\Exception\EntityNotFound');
        $this->controller->setErrorMessageForNotFoundDataOnDeletion($message);
        $this->service->expects($this->any())
                      ->method('delete')
                      ->will($this->throwException($exception));

        $this->expectsDsl()->toAddFlashMessage($message, 'error');

        $this->controller->deleteAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
     */
    public function redirectsWhenDeletionHasBeenSuccessful()
    {
        $this->expectsDsl()->toRedirectToRoute();

        $this->controller->deleteAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
     */
    public function canConfigurateRouteForRedirectionWhenDeletionHasBeenSuccessful()
    {
        $route = 'foo/bar';

        $this->expectsDsl()->toRedirectToRoute($route);

        $this->controller->setRedirectRouteForSuccessfulDeletion($route);
        $this->controller->deleteAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
     */
    public function addsNoSuccessMessageUntilItIsConfiguratedWhenDeletionHasBeenSuccessful()
    {
        $this->expectsDsl()->toDoNotAddFlashMessages();

        $this->controller->deleteAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
     */
    public function canConfigurateAnSuccessMessageWhenDeletionHasBeenSuccessful()
    {
        $message = 'could not found any data';

        $this->expectsDsl()->toAddFlashMessage($message, 'success');

        $this->controller->setSuccessMessageForDeletion($message);
        $this->controller->deleteAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
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
     * @group Module/DkplusBase
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
     * @group Module/DkplusBase
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
     * @group Module/DkplusBase
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
     * @group Module/DkplusBase
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
     * @group Module/DkplusBase
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
     * @group Module/DkplusBase
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
     * @group Module/DkplusBase
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
     * @group Module/DkplusBase
     */
    public function hasNoCreationRedirectData()
    {
        $container = $this->getMockForAbstractClass('DkplusControllerDsl\Dsl\ContainerInterface');
        $this->assertSame(array(), $this->controller->getCreationRedirectData($container));
    }

    /**
     * @test
     * @group Component/Controller
     * @group Module/DkplusBase
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
     * @group Module/DkplusBase
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
     * @group Module/DkplusBase
     * @testdox has a simple update message
     */
    public function hasSimpleUpdateMessage()
    {
        $container = $this->getMockForAbstractClass('DkplusControllerDsl\Dsl\ContainerInterface');
        $this->assertSame('Item has been updated.', $this->controller->getUpdatingMessage($container));
    }
}
