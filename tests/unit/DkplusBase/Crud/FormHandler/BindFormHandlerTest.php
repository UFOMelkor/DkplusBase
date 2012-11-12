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
 * @covers     DkplusBase\Crud\FormHandler\BindFormHandler
 */
class BindFormHandlerTest extends TestCase
{
    /** @var \Zend\Form\FormInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $form;

    /** @var BindFormHandler */
    private $formHandler;

    protected function setUp()
    {
        parent::setUp();

        $this->form        = $this->getMockForAbstractClass('Zend\Form\FormInterface');
        $this->formHandler = new BindFormHandler($this->form, 'stdClass');
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     * @testdox Is a form strategy
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
    public function bindsAnInstanceOfTheModelToTheCreationForm()
    {
        $this->form->expects($this->once())
                   ->method('bind')
                   ->with($this->isInstanceOf('stdClass'));
        $this->formHandler->getCreationForm();
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
    public function bindsTheGivenInstanceOfTheModelToTheUpdateForm()
    {
        $entity = $this->getMock('stdClass');

        $this->form->expects($this->once())
                   ->method('bind')
                   ->with($entity);
        $this->formHandler->getUpdateForm($entity);
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function doesNotNeedToCreateNewEntitiesBecauseTheyAreAlreadyCreated()
    {
        $entity = $this->getMock('stdClass');
        $this->assertSame($entity, $this->formHandler->createEntity($entity));
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function doesNotNeedToUpdateNewEntitiesBecauseTheyAreAlreadyUpdated()
    {
        $data = array('foo', 'bar', 'baz');
        $entity = $this->getMock('stdClass');
        $this->assertSame($entity, $this->formHandler->updateEntity($data, $entity));
    }
}