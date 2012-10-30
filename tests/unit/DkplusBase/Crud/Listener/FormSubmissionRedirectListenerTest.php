<?php
/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage Crud\Controller
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Listener;

use DkplusBase\Crud\Controller\CrudController;
use DkplusControllerDsl\Test\TestCase;

/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage Crud\Controller
 * @author     Oskar Bley <oskar@programming-php.net>
 * @covers     DkplusBase\Crud\Listener\FormSubmissionRedirectListener
 */
class FormSubmissionRedirectListenerTest extends TestCase
{
    /** @var CrudController */
    protected $controller;

    /** @var \DkplusBase\Crud\Service\ServiceInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $service;

    /** @var \Zend\EventManager\MvcEvent|\PHPUnit_Framework_MockObject_MockObject */
    protected $event;

    /** @var \DkplusBase\Crud\Listener\Options\SuccessOptions|\PHPUnit_Framework_MockObject_MockObject */
    protected $options;

    /** @var FormSubmissionRedirectListener */
    protected $listener;

    protected function setUp()
    {
        parent::setUp();
        $this->options  = $this->getMockIgnoringConstructor('DkplusBase\Crud\Listener\Options\SuccessOptions');
        $this->service  = $this->getMockForAbstractClass('DkplusBase\Crud\Service\ServiceInterface');
        $this->listener = new FormSubmissionRedirectListener($this->service, $this->options);

        $this->event      = $this->getMockIgnoringConstructor('Zend\Mvc\MvcEvent');
        $this->controller = new CrudController();
        $this->event->expects($this->any())
                    ->method('getTarget')
                    ->will($this->returnValue($this->controller));

        $this->setUpController($this->controller);
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function isCrudListener()
    {
        $this->assertInstanceOf(
            'DkplusBase\Crud\Listener\ListenerInterface',
            $this->listener
        );
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function returnsDsl()
    {
        $this->assertDsl($this->listener->execute($this->event));
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function usesTheProvidedForm()
    {
        $form = $this->getMockForAbstractClass('Zend\Form\FormInterface');

        $this->event->expects($this->at(1))
                    ->method('getParam')
                    ->with('form')
                    ->will($this->returnValue($form));

        $this->expectsDslToUseForm($form);
        $this->listener->execute($this->event);
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
     * @group Component/Listener
     * @group unit
     */
    public function validesFormAgainstPostRedirectGet()
    {
        $this->expectsDslToValidateFormAgainstPostRedirectGet();
        $this->listener->execute($this->event);
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
     * @group Component/Listener
     * @group unit
     */
    public function storesDataOnSuccessIntoServiceCreateIfNoIdentifierHasBeenProvided()
    {
        $dsl = $this->getDslMockBuilder()
                    ->withMockedPhrases(array('onSuccess'))
                    ->getMock();

        $successDsl = $this->expectsDslToStoreDataIntoMethod('create');

        $dsl->expects($this->once())
            ->method('onSuccess')
            ->with($successDsl)
            ->will($this->returnSelf());

        $this->listener->execute($this->event);
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function storesDataOnSuccessIntoServiceUpdateIfAnIdentifierHasBeenProvided()
    {
        $dsl = $this->getDslMockBuilder()
                    ->withMockedPhrases(array('onSuccess'))
                    ->getMock();

        $successDsl = $this->expectsDslToStoreDataIntoMethod('update', 5);

        $dsl->expects($this->once())
            ->method('onSuccess')
            ->with($successDsl)
            ->will($this->returnSelf());

        $this->event->expects($this->at(2))
                    ->method('getParam')
                    ->with('identifier')
                    ->will($this->returnValue(5));

        $this->listener->execute($this->event);
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
     * @group Component/Listener
     * @group unit
     */
    public function redirectsOnSuccess()
    {
        $dsl = $this->getDslMockBuilder()
                    ->withMockedPhrases(array('onSuccess'))
                    ->getMock();

        $this->options->expects($this->any())
                      ->method('getRedirectRoute')
                      ->will($this->returnValue('my-redirect-route'));

        $successDsl = $this->expectsDsl()->toRedirectToRoute(
            'my-redirect-route',
            array($this->options, 'getComputatedRedirectRouteParams')
        );

        $dsl->expects($this->once())
            ->method('onSuccess')
            ->with($successDsl)
            ->will($this->returnSelf());

        $this->listener->execute($this->event);
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     * @testdox adds a callback as success message
     */
    public function addsCallbackAsSuccessMessage()
    {
        $dsl = $this->getDslMockBuilder()
                    ->withMockedPhrases(array('onSuccess'))
                    ->getMock();

        $successDsl = $this->expectsDsl()
                           ->toAddFlashMessage(array($this->options, 'getComputatedMessage'), 'success');

        $dsl->expects($this->once())
            ->method('onSuccess')
            ->with($successDsl)
            ->will($this->returnSelf());

        $this->listener->execute($this->event);
    }
}
