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
class FactoryFormStrategyTest extends TestCase
{
    /** @var \Zend\Form\FormInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $form;

    /** @var \DkplusBase\Stdlib\Hydrator\HydrationFactoryInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $hydrationFactory;

    /** @var BindFormStrategy */
    private $formStrategy;

    protected function setUp()
    {
        parent::setUp();

        $this->form             = $this->getMockForAbstractClass('Zend\Form\FormInterface');
        $this->hydrationFactory = $this->getMockForAbstractClass(
            'DkplusBase\Stdlib\Hydrator\HydrationFactoryInterface'
        );
        $this->formStrategy     = new FactoryFormStrategy($this->form, $this->hydrationFactory);
    }

    /**
     * @test
     * @group Module/DkplusBase
     * @group Component/Service/Crud
     * @testdox is a form strategy
     */
    public function isFormStrategy()
    {
        $this->assertInstanceOf('DkplusBase\Service\Crud\FormStrategyInterface', $this->formStrategy);
    }

    /**
     * @test
     * @group Module/DkplusBase
     * @group Component/Service/Crud
     */
    public function returnsTheOvergivenFormAsCreationForm()
    {
        $this->assertSame($this->form, $this->formStrategy->getCreationForm());
    }

    /**
     * @test
     * @group Module/DkplusBase
     * @group Component/Service/Crud
     */
    public function returnsTheOvergivenFormAsUpdateForm()
    {
        $item = $this->getMock('stdClass');
        $this->assertSame($this->form, $this->formStrategy->getUpdateForm($item));
    }

    /**
     * @test
     * @group Module/DkplusBase
     * @group Component/Service/Crud
     */
    public function putsTheDataOfTheModelIntoTheUpdateForm()
    {
        $item = $this->getMock('stdClass');
        $data = array('foo' => 'bar');

        $this->hydrationFactory->expects($this->any())
                               ->method('extract')
                               ->with($item)
                               ->will($this->returnValue($data));

        $this->form->expects($this->once())
                   ->method('setData')
                   ->with($data);

        $this->formStrategy->getUpdateForm($item);
    }

    /**
     * @test
     * @group Module/DkplusBase
     * @group Component/Service/Crud
     */
    public function createsNewItemsUsingTheHydrationFactory()
    {
        $item = $this->getMock('stdClass');
        $data = array('foo' => 'bar');

        $this->hydrationFactory->expects($this->any())
                               ->method('create')
                               ->with($data)
                               ->will($this->returnValue($item));

        $this->assertSame($item, $this->formStrategy->createItem($data));
    }

    /**
     * @test
     * @group Module/DkplusBase
     * @group Component/Service/Crud
     */
    public function updatesItemsUsingTheHydrationFactory()
    {
        $data = array('foo', 'bar', 'baz');
        $item = $this->getMock('stdClass');

        $this->hydrationFactory->expects($this->once())
                               ->method('hydrate')
                               ->with($data, $item);

        $this->formStrategy->updateItem($data, $item);
    }

    /**
     * @test
     * @group Module/DkplusBase
     * @group Component/Service/Crud
     */
    public function returnsTheUpdatedItem()
    {
        $item = $this->getMock('stdClass');

        $this->assertSame($item, $this->formStrategy->updateItem(array(), $item));
    }
}
