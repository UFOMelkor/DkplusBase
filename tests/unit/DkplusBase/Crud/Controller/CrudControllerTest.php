<?php
/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage Crud\Controller
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Controller;

use DkplusControllerDsl\Test\TestCase;
use Zend\EventManager\ResponseCollection;
use Zend\Form\FormInterface as Form;
use Zend\Paginator\Paginator;

/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage Crud\Controller
 * @author     Oskar Bley <oskar@programming-php.net>
 * @covers     DkplusBase\Crud\Controller\CrudController
 */
class CrudControllerTest extends TestCase
{
    /** @var CrudController */
    protected $controller;

    protected function setUp()
    {
        parent::setUp();
        $this->controller = new CrudController();
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
     * @covers DkplusBase\Crud\Controller\CrudController::setEventIdentifier
     */
    public function canHaveAnotherEventIdentifier()
    {
        $eventManager = $this->getMockForAbstractClass('Zend\EventManager\EventManagerInterface');
        $eventManager->expects($this->once())
                     ->method('setIdentifiers')
                     ->with($this->contains('My\Crud\Controller'));
        $this->controller->setEventIdentifier('My\Crud\Controller');
        $this->controller->setEventManager($eventManager);
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @covers DkplusBase\Crud\Controller\CrudController::setEventManager
     * @dataProvider provideRejectedResults
     */
    public function rejectsNotAllowedActionControllerReturnValuesAsResult($result)
    {
        $this->assertFalse($this->controller->isActionControllerResult($result));
    }

    public function provideRejectedResults()
    {
        return array(
            array('foo'),
            array(25),
            array($this->getMock('stdClass')),
            array(null)
        );
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @covers DkplusBase\Crud\Controller\CrudController::setEventManager
     * @dataProvider provideAcceptedResults
     */
    public function acceptsPossibleActionControllerReturnValuesAsResult($result)
    {
        $this->assertTrue($this->controller->isActionControllerResult($result));
    }

    public function provideAcceptedResults()
    {
        return array(
            array($this->getMockForAbstractClass('Zend\View\Model\ModelInterface')),
            array($this->getMockForAbstractClass('Zend\Stdlib\ResponseInterface')),
            array($this->getMockForAbstractClass('DkplusControllerDsl\Dsl\DslInterface')),
            array(array('foo' => 'bar'))
        );
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @covers DkplusBase\Crud\Controller\CrudController::setEventManager
     * @dataProvider provideRejectedForms
     */
    public function rejectsEverythingThatIsNoFormAsForm($result)
    {
        $this->assertFalse($this->controller->isForm($result));
    }

    public function provideRejectedForms()
    {
        return array(
            array('foo'),
            array(25),
            array($this->getMock('stdClass')),
            array(null)
        );
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @covers DkplusBase\Crud\Controller\CrudController::setEventManager
     */
    public function acceptsFormsAsForms()
    {
        $this->assertTrue($this->controller->isForm($this->getMockForAbstractClass('Zend\Form\FormInterface')));
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @covers DkplusBase\Crud\Controller\CrudController::setEventManager
     * @dataProvider provideRejectedPaginators
     */
    public function rejectsEverythingThatIsNoPaginatorAsPaginator($result)
    {
        $this->assertFalse($this->controller->isPaginator($result));
    }

    public function provideRejectedPaginators()
    {
        return array(
            array('foo'),
            array(25),
            array($this->getMock('stdClass')),
            array(null)
        );
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @covers DkplusBase\Crud\Controller\CrudController::setEventManager
     */
    public function acceptsPaginatorAsPaginator()
    {
        $paginator = $this->getMockIgnoringConstructor('Zend\Paginator\Paginator');
        $this->assertTrue($this->controller->isPaginator($paginator));
    }

    protected function mockEventManager()
    {
        $eventManager = $this->getMockForAbstractClass('Zend\EventManager\EventManagerInterface');
        $this->controller->setEventManager($eventManager);
    }

    /**
     * @param mixed $result
     * @return \Zend\EventManager\ResponseCollection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEventResponseCollectionMockThatHasAnValidResult($result)
    {
        $eventResult = $this->getMockIgnoringConstructor('Zend\EventManager\ResponseCollection');
        $eventResult->expects($this->any())
                    ->method('last')
                    ->will($this->returnValue($result));
        $eventResult->expects($this->any())
                    ->method('stopped')
                    ->will($this->returnValue(true));
        $eventResult->expects($this->any())
                    ->method('count')
                    ->will($this->returnValue(1));
        return $eventResult;
    }

    /**
     * @return \Zend\EventManager\ResponseCollection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEventResponseCollectionMockThatHasAnInvalidResult()
    {
        $eventResult = $this->getMockIgnoringConstructor('Zend\EventManager\ResponseCollection');
        $eventResult->expects($this->any())
                    ->method('last')
                    ->will($this->returnValue('foo'));
        $eventResult->expects($this->any())
                    ->method('stopped')
                    ->will($this->returnValue(false));
        $eventResult->expects($this->any())
                    ->method('count')
                    ->will($this->returnValue(1));
        return $eventResult;
    }

    /** @return \Zend\EventManager\ResponseCollection|\PHPUnit_Framework_MockObject_MockObject */
    protected function getEventResponseCollectionMockThatHasNoResults()
    {
        $eventResult = $this->getMockIgnoringConstructor('Zend\EventManager\ResponseCollection');
        $eventResult->expects($this->any())
                    ->method('last')
                    ->will($this->returnValue('foo'));
        $eventResult->expects($this->any())
                    ->method('stopped')
                    ->will($this->returnValue(false));
        $eventResult->expects($this->any())
                    ->method('count')
                    ->will($this->returnValue(1));
        return $eventResult;
    }

    /**
     * @param ResponseCollection $result
     */
    protected function preReadEventReturns(ResponseCollection $result)
    {
        $this->controller
            ->getEventManager()->expects($this->at(0))
                               ->method('trigger')
                               ->with('CrudController.preRead', $this->controller, array())
                               ->will($this->returnValue($result));
    }

    /**
     * @param ResponseCollection $result
     * @param mixed $entity
     */
    protected function readEventReturns(ResponseCollection $result, $entity)
    {
        $callback = array($this->controller, 'isActionControllerResult');
        $this->controller
             ->getEventManager()->expects($this->at(1))
                                ->method('trigger')
                                ->with('CrudController.read', $this->controller, array('entity' => $entity), $callback)
                                ->will($this->returnValue($result));
    }

    /**
     * @param mixed $entity
     * @param \Zend\View\Model\ModelInterface|
     *        \Zend\Stdlib\ResponseInterface|
     *        \DkplusControllerDsl\Dsl\DslInterface|
     *        array $result
     */
    protected function postReadEventIsTriggeredWith($entity, $result)
    {
        $eventParams = array('entity' => $entity, 'result' => $result);
        $this->controller
             ->getEventManager()->expects($this->at(2))
                                ->method('trigger')
                                ->with('CrudController.postRead', $this->controller, $eventParams);
    }

    /**
     * @param \Zend\View\Model\ModelInterface|
     *        \Zend\Stdlib\ResponseInterface|
     *        \DkplusControllerDsl\Dsl\DslInterface|
     *        array $result
     */
    protected function readNotFoundEventReturns($result)
    {
        $callback = array($this->controller, 'isActionControllerResult');
        $this->controller
             ->getEventManager()->expects($this->at(1))
                                ->method('trigger')
                                ->with('CrudController.readNotFound', $this->controller, array(), $callback)
                                ->will($this->returnValue($result));
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/read
     */
    public function getsEntityByTriggeringPreReadEvent()
    {
        $entity = $this->getMock('stdClass');

        $this->mockEventManager();
        $this->preReadEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($entity));
        $this->readEventReturns(
            $this->getEventResponseCollectionMockThatHasAnValidResult(array('foo' => 'bar')),
            $entity
        );

        $this->controller->readAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/read
     */
    public function returnsTheResultOfTheReadEventWhenAccepted()
    {
        $result = array('foo' => 'bar');
        $entity = $this->getMock('stdClass');

        $this->mockEventManager();
        $this->preReadEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($entity));
        $this->readEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($result), $entity);

        $this->assertSame($result, $this->controller->readAction());
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/read
     * @expectedException RuntimeException
     * @expectedExceptionMessage CrudController.read should result in a valid controller response
     */
    public function throwsAnExceptionWhenTheReadEventDoesNotResultInAnythingUsefull()
    {
        $entity = $this->getMock('stdClass');

        $this->mockEventManager();
        $this->preReadEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($entity));
        $this->readEventReturns($this->getEventResponseCollectionMockThatHasAnInvalidResult(), $entity);

        $this->controller->readAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/read
     * @expectedException RuntimeException
     * @expectedExceptionMessage CrudController.read should result in a valid controller response
     */
    public function throwsAnExceptionWhenTheReadEventHasNotBeenStopped()
    {
        $entity = $this->getMock('stdClass');

        $this->mockEventManager();
        $this->preReadEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($entity));
        $this->readEventReturns($this->getEventResponseCollectionMockThatHasNoResults(), $entity);

        $this->controller->readAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/read
     */
    public function triggersPostReadEventWithTheReadAndPreReadEventResults()
    {
        $entity    = $this->getMock('stdClass');
        $viewModel = $this->getMockForAbstractClass('Zend\View\Model\ModelInterface');

        $this->mockEventManager();
        $this->preReadEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($entity));
        $this->readEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($viewModel), $entity);
        $this->postReadEventIsTriggeredWith($entity, $viewModel);

        $this->controller->readAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/read
     */
    public function returnsResultOfReadNotFoundWhenPreReadEventReturnsCrap()
    {
        $readNotFoundResult = array('foo' => 'bar');

        $this->mockEventManager();
        $this->preReadEventReturns($this->getEventResponseCollectionMockThatHasAnInvalidResult());
        $this->readNotFoundEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($readNotFoundResult));

        $this->assertSame($readNotFoundResult, $this->controller->readAction());
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/read
     */
    public function returnsResultOfReadNotFoundWhenPreReadEventReturnsNothing()
    {
        $readNotFoundResult = array('foo' => 'bar');

        $this->mockEventManager();
        $this->preReadEventReturns($this->getEventResponseCollectionMockThatHasNoResults());
        $this->readNotFoundEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($readNotFoundResult));

        $this->assertSame($readNotFoundResult, $this->controller->readAction());
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/read
     * @expectedException RuntimeException
     * @expectedExceptionMessage CrudController.readNotFound should result in a valid controller response
     */
    public function throwsAnExceptionWhenReadNotFoundEventReturnsCrap()
    {
        $this->mockEventManager();
        $this->preReadEventReturns($this->getEventResponseCollectionMockThatHasAnInvalidResult());
        $this->readNotFoundEventReturns($this->getEventResponseCollectionMockThatHasAnInvalidResult());

        $this->controller->readAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/read
     * @expectedException RuntimeException
     * @expectedExceptionMessage CrudController.readNotFound should result in a valid controller response
     */
    public function throwsAnExceptionWhenReadNotFoundEventReturnsNothing()
    {
        $this->mockEventManager();
        $this->preReadEventReturns($this->getEventResponseCollectionMockThatHasNoResults());
        $this->readNotFoundEventReturns($this->getEventResponseCollectionMockThatHasAnInvalidResult());

        $this->controller->readAction();
    }

    /**
     * @param ResponseCollection $result
     */
    protected function preCreateEventReturns(ResponseCollection $result)
    {
        $callback = array($this->controller, 'isForm');
        $this->controller
            ->getEventManager()->expects($this->at(0))
                               ->method('trigger')
                               ->with('CrudController.preCreate', $this->controller, array(), $callback)
                               ->will($this->returnValue($result));
    }

    /**
     * @param ResponseCollection $result
     * @param Form $form
     */
    protected function createEventReturns(ResponseCollection $result, Form $form)
    {
        $callback = array($this->controller, 'isActionControllerResult');
        $this->controller
             ->getEventManager()->expects($this->at(1))
                                ->method('trigger')
                                ->with('CrudController.create', $this->controller, array('form' => $form), $callback)
                                ->will($this->returnValue($result));
    }

    /**
     * @param Form $form
     * @param \Zend\View\Model\ModelInterface|
     *        \Zend\Stdlib\ResponseInterface|
     *        \DkplusControllerDsl\Dsl\DslInterface|
     *        array $result
     */
    protected function postCreateEventIsTriggeredWith(Form $form, $result)
    {
        $eventParams = array('form' => $form, 'result' => $result);
        $this->controller
             ->getEventManager()->expects($this->at(2))
                                ->method('trigger')
                                ->with('CrudController.postCreate', $this->controller, $eventParams);
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/create
     */
    public function getsTheCreationFormByTriggeringThePreCreateEvent()
    {
        $form = $this->getMockForAbstractClass('Zend\Form\FormInterface');

        $this->mockEventManager();
        $this->preCreateEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($form));
        $this->createEventReturns(
            $this->getEventResponseCollectionMockThatHasAnValidResult(array('foo', 'bar')),
            $form
        );

        $this->controller->createAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/create
     * @expectedException RuntimeException
     * @expectedExceptionMessage CrudController.preCreate should result in a form
     */
    public function throwsAnExceptionIfNothingIsProvidedByThePreCreateEvent()
    {
        $this->mockEventManager();
        $this->preCreateEventReturns($this->getEventResponseCollectionMockThatHasNoResults());

        $this->controller->createAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/create
     * @expectedException RuntimeException
     * @expectedExceptionMessage CrudController.preCreate should result in a form
     */
    public function throwsAnExceptionIfCrapIsProvidedByThePreCreateEvent()
    {
        $this->mockEventManager();
        $this->preCreateEventReturns($this->getEventResponseCollectionMockThatHasAnInvalidResult());

        $this->controller->createAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/create
     */
    public function returnsTheResultOfTheCreateEventIfValidResponseIsProvided()
    {
        $form   = $this->getMockForAbstractClass('Zend\Form\FormInterface');
        $result = array('foo' => 'bar');

        $this->mockEventManager();
        $this->preCreateEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($form));
        $this->createEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($result), $form);

        $this->assertEquals($result, $this->controller->createAction());
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/create
     * @expectedException RuntimeException
     * @expectedExceptionMessage CrudController.create should result in a valid controller response
     */
    public function throwsAnExceptionIfCrapIsProvidedByTheCreateEvent()
    {
        $form   = $this->getMockForAbstractClass('Zend\Form\FormInterface');

        $this->mockEventManager();
        $this->preCreateEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($form));
        $this->createEventReturns($this->getEventResponseCollectionMockThatHasAnInvalidResult(), $form);

        $this->controller->createAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/create
     * @expectedException RuntimeException
     * @expectedExceptionMessage CrudController.create should result in a valid controller response
     */
    public function throwsAnExceptionIfNothingIsProvidedByTheCreateEvent()
    {
        $form   = $this->getMockForAbstractClass('Zend\Form\FormInterface');

        $this->mockEventManager();
        $this->preCreateEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($form));
        $this->createEventReturns($this->getEventResponseCollectionMockThatHasAnInvalidResult(), $form);

        $this->controller->createAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/create
     */
    public function triggersPostCreateEventWithTheResultsOfTheCreateAndPreCreateEvents()
    {
        $form   = $this->getMockForAbstractClass('Zend\Form\FormInterface');
        $result = array('foo' => 'bar');

        $this->mockEventManager();
        $this->preCreateEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($form));
        $this->createEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($result), $form);
        $this->postCreateEventIsTriggeredWith($form, $result);

        $this->assertEquals($result, $this->controller->createAction());
    }

    /**
     * @param ResponseCollection $result
     */
    protected function preUpdateEventReturns(ResponseCollection $result)
    {
        $callback = array($this->controller, 'isForm');
        $this->controller
            ->getEventManager()->expects($this->at(0))
                               ->method('trigger')
                               ->with('CrudController.preUpdate', $this->controller, array(), $callback)
                               ->will($this->returnValue($result));
    }

    /**
     * @param ResponseCollection $result
     * @param Form $form
     */
    protected function updateEventReturns(ResponseCollection $result, Form $form)
    {
        $callback = array($this->controller, 'isActionControllerResult');
        $this->controller
             ->getEventManager()->expects($this->at(1))
                                ->method('trigger')
                                ->with('CrudController.update', $this->controller, array('form' => $form), $callback)
                                ->will($this->returnValue($result));
    }

    /**
     * @param ResponseCollection $result
     * @param Form $form
     */
    protected function updateNotFoundEventReturns(ResponseCollection $result)
    {
        $callback = array($this->controller, 'isActionControllerResult');
        $this->controller
             ->getEventManager()->expects($this->at(1))
                                ->method('trigger')
                                ->with('CrudController.updateNotFound', $this->controller, array(), $callback)
                                ->will($this->returnValue($result));
    }

    /**
     * @param Form $form
     * @param \Zend\View\Model\ModelInterface|
     *        \Zend\Stdlib\ResponseInterface|
     *        \DkplusControllerDsl\Dsl\DslInterface|
     *        array $result
     */
    protected function postUpdateEventIsTriggeredWith(Form $form, $result)
    {
        $eventParams = array('form' => $form, 'result' => $result);
        $this->controller
             ->getEventManager()->expects($this->at(2))
                                ->method('trigger')
                                ->with('CrudController.postUpdate', $this->controller, $eventParams);
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/update
     */
    public function getsTheUpdateFormByTriggeringThePreUpdateEvent()
    {
        $form = $this->getMockForAbstractClass('Zend\Form\FormInterface');

        $this->mockEventManager();
        $this->preUpdateEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($form));
        $this->updateEventReturns(
            $this->getEventResponseCollectionMockThatHasAnValidResult(array('foo', 'bar')),
            $form
        );

        $this->controller->updateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/update
     */
    public function returnsTheResultOfTheUpdateNotFoundEventWhenItIsValidAndThePreUpdateEventReturnsNothing()
    {
        $result = array('foo' => 'bar');

        $this->mockEventManager();
        $this->preUpdateEventReturns($this->getEventResponseCollectionMockThatHasNoResults());
        $this->updateNotFoundEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($result));

        $this->assertEquals($result, $this->controller->updateAction());
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/update
     */
    public function returnsTheResultOfTheUpdateNotFoundEventWhenItIsValidAndThePreUpdateEventReturnsCrap()
    {
        $result = array('foo' => 'bar');

        $this->mockEventManager();
        $this->preUpdateEventReturns($this->getEventResponseCollectionMockThatHasAnInvalidResult());
        $this->updateNotFoundEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($result));

        $this->assertEquals($result, $this->controller->updateAction());
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/update
     * @expectedException RuntimeException
     * @expectedExceptionMessage CrudController.updateNotFound should result in a valid controller response
     */
    public function throwsAnExceptionIfNothingIsProvidedByTheUpdateNotFoundEvent()
    {
        $this->mockEventManager();
        $this->preUpdateEventReturns($this->getEventResponseCollectionMockThatHasNoResults());
        $this->updateNotFoundEventReturns($this->getEventResponseCollectionMockThatHasNoResults());

        $this->controller->updateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/update
     * @expectedException RuntimeException
     * @expectedExceptionMessage CrudController.updateNotFound should result in a valid controller response
     */
    public function throwsAnExceptionIfCrapIsProvidedByTheUpdateNotFoundEvent()
    {
        $this->mockEventManager();
        $this->preUpdateEventReturns($this->getEventResponseCollectionMockThatHasNoResults());
        $this->updateNotFoundEventReturns($this->getEventResponseCollectionMockThatHasAnInvalidResult());

        $this->controller->updateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/create
     */
    public function returnsTheResultOfTheUpdateEventIfValidResponseIsProvided()
    {
        $form   = $this->getMockForAbstractClass('Zend\Form\FormInterface');
        $result = array('foo' => 'bar');

        $this->mockEventManager();
        $this->preUpdateEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($form));
        $this->updateEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($result), $form);

        $this->assertEquals($result, $this->controller->updateAction());
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/create
     * @expectedException RuntimeException
     * @expectedExceptionMessage CrudController.update should result in a valid controller response
     */
    public function throwsAnExceptionIfCrapIsProvidedByTheUpdateEvent()
    {
        $form = $this->getMockForAbstractClass('Zend\Form\FormInterface');

        $this->mockEventManager();
        $this->preUpdateEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($form));
        $this->updateEventReturns($this->getEventResponseCollectionMockThatHasAnInvalidResult(), $form);

        $this->controller->updateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/create
     * @expectedException RuntimeException
     * @expectedExceptionMessage CrudController.update should result in a valid controller response
     */
    public function throwsAnExceptionIfNothingIsProvidedByTheUpdateEvent()
    {
        $form = $this->getMockForAbstractClass('Zend\Form\FormInterface');

        $this->mockEventManager();
        $this->preUpdateEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($form));
        $this->updateEventReturns($this->getEventResponseCollectionMockThatHasAnInvalidResult(), $form);

        $this->controller->updateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/create
     */
    public function triggersPostUpdateEventWithTheResultsOfTheUpdateAndPreUpdateEvents()
    {
        $form   = $this->getMockForAbstractClass('Zend\Form\FormInterface');
        $result = array('foo' => 'bar');

        $this->mockEventManager();
        $this->preUpdateEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($form));
        $this->updateEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($result), $form);
        $this->postUpdateEventIsTriggeredWith($form, $result);

        $this->assertEquals($result, $this->controller->updateAction());
    }































    /**
     * @param ResponseCollection $result
     */
    protected function preDeleteEventReturns(ResponseCollection $result)
    {
        $this->controller
            ->getEventManager()->expects($this->at(0))
                               ->method('trigger')
                               ->with('CrudController.preDelete', $this->controller, array())
                               ->will($this->returnValue($result));
    }

    /**
     * @param ResponseCollection $result
     * @param mixed $entity
     */
    protected function deleteEventReturns(ResponseCollection $result, $entity)
    {
        $callback = array($this->controller, 'isActionControllerResult');
        $this->controller
             ->getEventManager()
             ->expects($this->at(1))
             ->method('trigger')
             ->with('CrudController.delete', $this->controller, array('entity' => $entity), $callback)
             ->will($this->returnValue($result));
    }

    /**
     * @param mixed $entity
     * @param \Zend\View\Model\ModelInterface|
     *        \Zend\Stdlib\ResponseInterface|
     *        \DkplusControllerDsl\Dsl\DslInterface|
     *        array $result
     */
    protected function postDeleteEventIsTriggeredWith($entity, $result)
    {
        $eventParams = array('entity' => $entity, 'result' => $result);
        $this->controller
             ->getEventManager()->expects($this->at(2))
                                ->method('trigger')
                                ->with('CrudController.postDelete', $this->controller, $eventParams);
    }

    /**
     * @param \Zend\View\Model\ModelInterface|
     *        \Zend\Stdlib\ResponseInterface|
     *        \DkplusControllerDsl\Dsl\DslInterface|
     *        array $result
     */
    protected function deleteNotFoundEventReturns($result)
    {
        $callback = array($this->controller, 'isActionControllerResult');
        $this->controller
             ->getEventManager()->expects($this->at(1))
                                ->method('trigger')
                                ->with('CrudController.deleteNotFound', $this->controller, array(), $callback)
                                ->will($this->returnValue($result));
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/delete
     */
    public function getsEntityByTriggeringPreDeleteEvent()
    {
        $entity = $this->getMock('stdClass');

        $this->mockEventManager();
        $this->preDeleteEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($entity));
        $this->deleteEventReturns(
            $this->getEventResponseCollectionMockThatHasAnValidResult(array('foo' => 'bar')),
            $entity
        );

        $this->controller->deleteAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/delete
     */
    public function returnsTheResultOfTheDeleteEventWhenAccepted()
    {
        $result = array('foo' => 'bar');
        $entity = $this->getMock('stdClass');

        $this->mockEventManager();
        $this->preDeleteEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($entity));
        $this->deleteEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($result), $entity);

        $this->assertSame($result, $this->controller->deleteAction());
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/delete
     * @expectedException RuntimeException
     * @expectedExceptionMessage CrudController.delete should result in a valid controller response
     */
    public function throwsAnExceptionWhenTheDeleteEventDoesNotResultInAnythingUsefull()
    {
        $entity = $this->getMock('stdClass');

        $this->mockEventManager();
        $this->preDeleteEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($entity));
        $this->deleteEventReturns($this->getEventResponseCollectionMockThatHasAnInvalidResult(), $entity);

        $this->controller->deleteAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/delete
     * @expectedException RuntimeException
     * @expectedExceptionMessage CrudController.delete should result in a valid controller response
     */
    public function throwsAnExceptionWhenTheDeleteEventHasNotBeenStopped()
    {
        $entity = $this->getMock('stdClass');

        $this->mockEventManager();
        $this->preDeleteEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($entity));
        $this->deleteEventReturns($this->getEventResponseCollectionMockThatHasNoResults(), $entity);

        $this->controller->deleteAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/delete
     */
    public function triggersPostDeleteEventWithTheDeleteAndPreDeleteEventResults()
    {
        $entity    = $this->getMock('stdClass');
        $viewModel = $this->getMockForAbstractClass('Zend\View\Model\ModelInterface');

        $this->mockEventManager();
        $this->preDeleteEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($entity));
        $this->deleteEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($viewModel), $entity);
        $this->postDeleteEventIsTriggeredWith($entity, $viewModel);

        $this->controller->deleteAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/delete
     */
    public function returnsResultOfDeleteNotFoundWhenPreDeleteEventReturnsCrap()
    {
        $delNotFoundResult = array('foo' => 'bar');

        $this->mockEventManager();
        $this->preDeleteEventReturns($this->getEventResponseCollectionMockThatHasAnInvalidResult());
        $this->deleteNotFoundEventReturns(
            $this->getEventResponseCollectionMockThatHasAnValidResult($delNotFoundResult)
        );

        $this->assertSame($delNotFoundResult, $this->controller->deleteAction());
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/delete
     */
    public function returnsResultOfDeleteNotFoundWhenPreDeleteEventReturnsNothing()
    {
        $delNotFoundResult = array('foo' => 'bar');

        $this->mockEventManager();
        $this->preDeleteEventReturns($this->getEventResponseCollectionMockThatHasNoResults());
        $this->deleteNotFoundEventReturns(
            $this->getEventResponseCollectionMockThatHasAnValidResult($delNotFoundResult)
        );

        $this->assertSame($delNotFoundResult, $this->controller->deleteAction());
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/delete
     * @expectedException RuntimeException
     * @expectedExceptionMessage CrudController.deleteNotFound should result in a valid controller response
     */
    public function throwsAnExceptionWhenDeleteNotFoundEventReturnsCrap()
    {
        $this->mockEventManager();
        $this->preDeleteEventReturns($this->getEventResponseCollectionMockThatHasAnInvalidResult());
        $this->deleteNotFoundEventReturns($this->getEventResponseCollectionMockThatHasAnInvalidResult());

        $this->controller->deleteAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/delete
     * @expectedException RuntimeException
     * @expectedExceptionMessage CrudController.deleteNotFound should result in a valid controller response
     */
    public function throwsAnExceptionWhenDeleteNotFoundEventReturnsNothing()
    {
        $this->mockEventManager();
        $this->preDeleteEventReturns($this->getEventResponseCollectionMockThatHasNoResults());
        $this->deleteNotFoundEventReturns($this->getEventResponseCollectionMockThatHasAnInvalidResult());

        $this->controller->deleteAction();
    }

    /**
     * @param ResponseCollection $result
     */
    protected function preListEventReturns(ResponseCollection $result)
    {
        $this->controller
            ->getEventManager()->expects($this->at(0))
                               ->method('trigger')
                               ->with('CrudController.preList', $this->controller, array())
                               ->will($this->returnValue($result));
    }

    /**
     * @param ResponseCollection $result
     * @param mixed $data
     */
    protected function listEventReturns(ResponseCollection $result, $data)
    {
        $callback = array($this->controller, 'isActionControllerResult');
        $this->controller
             ->getEventManager()->expects($this->at(1))
                                ->method('trigger')
                                ->with('CrudController.list', $this->controller, array('data' => $data), $callback)
                                ->will($this->returnValue($result));
    }

    /**
     * @param mixed $data
     * @param \Zend\View\Model\ModelInterface|
     *        \Zend\Stdlib\ResponseInterface|
     *        \DkplusControllerDsl\Dsl\DslInterface|
     *        array $result
     */
    protected function postListEventIsTriggeredWith($data, $result)
    {
        $eventParams = array('data' => $data, 'result' => $result);
        $this->controller
             ->getEventManager()->expects($this->at(2))
                                ->method('trigger')
                                ->with('CrudController.postList', $this->controller, $eventParams);
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/list
     */
    public function getsTheListingDataByTriggeringThePreListEvent()
    {
        $data = array(array('id' => 4, 'name' => 'Charlie'), array('id' => 5, 'name' => 'Alan'));

        $this->mockEventManager();
        $this->preListEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($data));
        $this->listEventReturns(
            $this->getEventResponseCollectionMockThatHasAnValidResult(array('foo', 'bar')),
            $data
        );

        $this->controller->listAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/list
     * @expectedException RuntimeException
     * @expectedExceptionMessage CrudController.preList should result in anything not null
     */
    public function throwsAnExceptionIfNothingIsProvidedByThePreListEvent()
    {
        $this->mockEventManager();
        $this->preListEventReturns($this->getEventResponseCollectionMockThatHasNoResults());

        $this->controller->listAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/list
     */
    public function returnsTheResultOfTheListEventIfValidResponseIsProvided()
    {
        $data = array(array('id' => 4, 'name' => 'Charlie'), array('id' => 5, 'name' => 'Alan'));
        $result = array('foo' => 'bar');

        $this->mockEventManager();
        $this->preListEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($data));
        $this->listEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($result), $data);

        $this->assertEquals($result, $this->controller->listAction());
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/list
     * @expectedException RuntimeException
     * @expectedExceptionMessage CrudController.list should result in a valid controller response
     */
    public function throwsAnExceptionIfCrapIsProvidedByTheListEvent()
    {
        $data = array(array('id' => 4, 'name' => 'Charlie'), array('id' => 5, 'name' => 'Alan'));

        $this->mockEventManager();
        $this->preListEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($data));
        $this->listEventReturns($this->getEventResponseCollectionMockThatHasAnInvalidResult(), $data);

        $this->controller->listAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/list
     * @expectedException RuntimeException
     * @expectedExceptionMessage CrudController.list should result in a valid controller response
     */
    public function throwsAnExceptionIfNothingIsProvidedByTheListEvent()
    {
        $data = array(array('id' => 4, 'name' => 'Charlie'), array('id' => 5, 'name' => 'Alan'));

        $this->mockEventManager();
        $this->preListEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($data));
        $this->listEventReturns($this->getEventResponseCollectionMockThatHasAnInvalidResult(), $data);

        $this->controller->listAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/list
     */
    public function triggersPostListEventWithTheResultsOfTheListAndPreListEvents()
    {
        $data   = array(array('id' => 4, 'name' => 'Charlie'), array('id' => 5, 'name' => 'Alan'));
        $result = array('foo' => 'bar');

        $this->mockEventManager();
        $this->preListEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($data));
        $this->listEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($result), $data);
        $this->postListEventIsTriggeredWith($data, $result);

        $this->assertEquals($result, $this->controller->listAction());
    }

    /**
     * @param ResponseCollection $result
     */
    protected function prePaginateEventReturns(ResponseCollection $result)
    {
        $callback = array($this->controller, 'isPaginator');
        $this->controller
             ->getEventManager()->expects($this->at(0))
                               ->method('trigger')
                               ->with('CrudController.prePaginate', $this->controller, array(), $callback)
                               ->will($this->returnValue($result));
    }

    /**
     * @param ResponseCollection $result
     * @param Paginator $paginator
     */
    protected function paginateEventReturns(ResponseCollection $result, Paginator $paginator)
    {
        $callback = array($this->controller, 'isActionControllerResult');
        $this->controller
             ->getEventManager()
             ->expects($this->at(1))
             ->method('trigger')
             ->with('CrudController.paginate', $this->controller, array('paginator' => $paginator), $callback)
             ->will($this->returnValue($result));
    }

    /**
     * @param Paginator $paginator
     * @param \Zend\View\Model\ModelInterface|
     *        \Zend\Stdlib\ResponseInterface|
     *        \DkplusControllerDsl\Dsl\DslInterface|
     *        array $result
     */
    protected function postPaginateEventIsTriggeredWith(Paginator $paginator, $result)
    {
        $eventParams = array('paginator' => $paginator, 'result' => $result);
        $this->controller
             ->getEventManager()->expects($this->at(2))
                                ->method('trigger')
                                ->with('CrudController.postPaginate', $this->controller, $eventParams);
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/paginate
     */
    public function getsThePaginatorByTriggeringThePrePaginateEvent()
    {
        $paginator = $this->getMockIgnoringConstructor('Zend\Paginator\Paginator');

        $this->mockEventManager();
        $this->prePaginateEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($paginator));
        $this->paginateEventReturns(
            $this->getEventResponseCollectionMockThatHasAnValidResult(array('foo', 'bar')),
            $paginator
        );

        $this->controller->paginateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/paginate
     * @expectedException RuntimeException
     * @expectedExceptionMessage CrudController.prePaginate should result in a paginator
     */
    public function throwsAnExceptionIfNothingIsProvidedByThePrePaginateEvent()
    {
        $this->mockEventManager();
        $this->prePaginateEventReturns($this->getEventResponseCollectionMockThatHasNoResults());

        $this->controller->paginateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/paginate
     * @expectedException RuntimeException
     * @expectedExceptionMessage CrudController.prePaginate should result in a paginator
     */
    public function throwsAnExceptionIfCrapIsProvidedByThePrePaginateEvent()
    {
        $this->mockEventManager();
        $this->prePaginateEventReturns($this->getEventResponseCollectionMockThatHasAnInvalidResult());

        $this->controller->paginateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/paginate
     */
    public function returnsTheResultOfThePaginateEventIfValidResponseIsProvided()
    {
        $paginator = $this->getMockIgnoringConstructor('Zend\Paginator\Paginator');
        $result    = array('foo' => 'bar');

        $this->mockEventManager();
        $this->prePaginateEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($paginator));
        $this->paginateEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($result), $paginator);

        $this->assertEquals($result, $this->controller->paginateAction());
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/paginate
     * @expectedException RuntimeException
     * @expectedExceptionMessage CrudController.paginate should result in a valid controller response
     */
    public function throwsAnExceptionIfCrapIsProvidedByThePaginateEvent()
    {
        $paginator = $this->getMockIgnoringConstructor('Zend\Paginator\Paginator');

        $this->mockEventManager();
        $this->prePaginateEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($paginator));
        $this->paginateEventReturns($this->getEventResponseCollectionMockThatHasAnInvalidResult(), $paginator);

        $this->controller->paginateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/paginate
     * @expectedException RuntimeException
     * @expectedExceptionMessage CrudController.paginate should result in a valid controller response
     */
    public function throwsAnExceptionIfNothingIsProvidedByThePaginateEvent()
    {
        $paginator = $this->getMockIgnoringConstructor('Zend\Paginator\Paginator');

        $this->mockEventManager();
        $this->prePaginateEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($paginator));
        $this->paginateEventReturns($this->getEventResponseCollectionMockThatHasAnInvalidResult(), $paginator);

        $this->controller->paginateAction();
    }

    /**
     * @test
     * @group Component/Controller
     * @group unit
     * @group crud/paginate
     */
    public function triggersPostPaginateEventWithTheResultsOfThePaginateAndPrePaginateEvents()
    {
        $paginator = $this->getMockIgnoringConstructor('Zend\Paginator\Paginator');
        $result    = array('foo' => 'bar');

        $this->mockEventManager();
        $this->prePaginateEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($paginator));
        $this->paginateEventReturns($this->getEventResponseCollectionMockThatHasAnValidResult($result), $paginator);
        $this->postPaginateEventIsTriggeredWith($paginator, $result);

        $this->assertEquals($result, $this->controller->paginateAction());
    }
}
