<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\FormHandler
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\FormHandler;

use DkplusUnitTest\TestCase;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\FormHandler
 * @author     Oskar Bley <oskar@programming-php.net>
 * @covers     DkplusBase\Crud\FormHandler\FactoryFormHandler
 */
class FactoryFormHandlerTest extends TestCase
{
    /** @var \Zend\Form\FormInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $form;

    /** @var \DkplusBase\Stdlib\Hydrator\HydrationFactoryInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $hydrationFactory;

    /** @var FactoryFormHandler */
    private $formHandler;

    protected function setUp()
    {
        parent::setUp();

        $this->form             = $this->getMockForAbstractClass('Zend\Form\FormInterface');
        $this->hydrationFactory = $this->getMockForAbstractClass(
            'DkplusBase\Stdlib\Hydrator\HydrationFactoryInterface'
        );
        $this->formHandler     = new FactoryFormHandler($this->form, $this->hydrationFactory);
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     * @testdox is a form strategy
     */
    public function isFormStrategy()
    {
        $this->assertInstanceOf('DkplusBase\Crud\FormHandler\FormHandlerInterface', $this->formHandler);
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function returnsTheOvergivenFormAsCreationForm()
    {
        $this->assertSame($this->form, $this->formHandler->getCreationForm());
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function returnsTheOvergivenFormAsUpdateForm()
    {
        $entity = $this->getMock('stdClass');
        $this->assertSame($this->form, $this->formHandler->getUpdateForm($entity));
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function putsTheDataOfTheModelIntoTheUpdateForm()
    {
        $entity = $this->getMock('stdClass');
        $data = array('foo' => 'bar');

        $this->hydrationFactory->expects($this->any())
                               ->method('extract')
                               ->with($entity)
                               ->will($this->returnValue($data));

        $this->form->expects($this->once())
                   ->method('populateValues')
                   ->with($data);

        $this->formHandler->getUpdateForm($entity);
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function createsNewEntitiesUsingTheHydrationFactory()
    {
        $entity = $this->getMock('stdClass');
        $data = array('foo' => 'bar');

        $this->hydrationFactory->expects($this->any())
                               ->method('create')
                               ->with($data)
                               ->will($this->returnValue($entity));

        $this->assertSame($entity, $this->formHandler->createEntity($data));
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function updatesEntitiesUsingTheHydrationFactory()
    {
        $data = array('foo', 'bar', 'baz');
        $entity = $this->getMock('stdClass');

        $this->hydrationFactory->expects($this->once())
                               ->method('hydrate')
                               ->with($data, $entity);

        $this->formHandler->updateEntity($data, $entity);
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function returnsTheUpdatedEntity()
    {
        $entity = $this->getMock('stdClass');

        $this->assertSame($entity, $this->formHandler->updateEntity(array(), $entity));
    }
}
