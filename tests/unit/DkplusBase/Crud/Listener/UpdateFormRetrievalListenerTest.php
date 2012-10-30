<?php
/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage Crud\Controller
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Listener;

use DkplusUnitTest\TestCase;

/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage Crud\Controller
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class UpdateFormRetrievalListenerTest extends TestCase
{
    /** @var \DkplusBase\Crud\Service\ServiceInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $service;

    /** @var \Zend\EventManager\MvcEvent|\PHPUnit_Framework_MockObject_MockObject */
    protected $event;

    protected function setUp()
    {
        parent::setUp();
        $this->service = $this->getMockForAbstractClass('DkplusBase\Crud\Service\ServiceInterface');
        $this->event   = $this->getMockIgnoringConstructor('Zend\Mvc\MvcEvent');
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
            new UpdateFormRetrievalListener($this->service)
        );
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function returnsTheFormFromTheService()
    {
        $form = $this->getMockForAbstractClass('Zend\Form\FormInterface');

        $this->event->expects($this->any())
                    ->method('getParam')
                    ->with('identifier')
                    ->will($this->returnValue(5));

        $this->service->expects($this->any())
                      ->method('getUpdateForm')
                      ->with(5)
                      ->will($this->returnValue($form));

        $listener = new UpdateFormRetrievalListener($this->service);
        $this->assertSame($form, $listener->execute($this->event));
    }
}
