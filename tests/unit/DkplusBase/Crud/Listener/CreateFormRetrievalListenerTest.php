<?php
/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Listener;

use DkplusUnitTest\TestCase;

/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class CreateFormRetrievalListenerTest extends TestCase
{
    /** @var \DkplusBase\Crud\Service\ServiceInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $service;

    /** @var \Zend\EventManager\MvcEvent|\PHPUnit_Framework_MockObject_MockObject */
    protected $event;

    /** @var CreateFormRetrievalListener */
    protected $listener;

    protected function setUp()
    {
        parent::setUp();
        $this->service    = $this->getMockForAbstractClass('DkplusBase\Crud\Service\ServiceInterface');
        $this->event      = $this->getMockForAbstractClass('Zend\EventManager\EventInterface');

        $this->listener = new CreateFormRetrievalListener($this->service);
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
    public function returnsTheFormFromTheService()
    {
        $form = $this->getMockForAbstractClass('Zend\Form\FormInterface');

        $this->service->expects($this->any())
                      ->method('getCreationForm')
                      ->will($this->returnValue($form));

        $this->assertSame($form, $this->listener->execute($this->event));
    }
}
