<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Service\Crud
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Service\Crud;

use DkplusUnitTest\TestCase;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Service\Crud
 * @author     Oskar Bley <oskar@programming-php.net>
 * @covers     DkplusBase\Service\Crud\BindFormStrategy
 */
class BindFormStrategyTest extends TestCase
{
    /** @var \Zend\Form\FormInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $form;

    /** @var BindFormStrategy */
    private $formStrategy;

    protected function setUp()
    {
        parent::setUp();

        $this->form         = $this->getMockForAbstractClass('Zend\Form\FormInterface');
        $this->formStrategy = new BindFormStrategy($this->form, 'stdClass');
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     * @testdox is a form strategy
     */
    public function isFormStrategy()
    {
        $this->assertInstanceOf('DkplusBase\Service\Crud\FormStrategyInterface', $this->formStrategy);
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function returnsTheOvergivenFormAsCreationForm()
    {
        $this->assertSame($this->form, $this->formStrategy->getCreationForm());
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
        $this->formStrategy->getCreationForm();
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function returnsTheOvergivenFormAsUpdateForm()
    {
        $item = $this->getMock('stdClass');
        $this->assertSame($this->form, $this->formStrategy->getUpdateForm($item));
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function bindsTheGivenInstanceOfTheModelToTheUpdateForm()
    {
        $item = $this->getMock('stdClass');

        $this->form->expects($this->once())
                   ->method('bind')
                   ->with($item);
        $this->formStrategy->getUpdateForm($item);
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function doesNotNeedToCreateNewItemsBecauseTheyAreAlreadyCreated()
    {
        $item = $this->getMock('stdClass');
        $this->assertSame($item, $this->formStrategy->createItem($item));
    }

    /**
     * @test
     * @group unit
     * @group Component/Service/Crud
     */
    public function doesNotNeedToUpdateNewItemsBecauseTheyAreAlreadyUpdated()
    {
        $data = array('foo', 'bar', 'baz');
        $item = $this->getMock('stdClass');
        $this->assertSame($item, $this->formStrategy->updateItem($data, $item));
    }
}
