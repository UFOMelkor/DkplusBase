<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Authentication
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Authentication\Storage;

use DkplusUnitTest\TestCase;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Authentication
 * @author     Oskar Bley <oskar@programming-php.net>
 * @covers     DkplusBase\Authentication\Storage\Chain
 */
class ChainTest extends TestCase
{
    /** @var Chain */
    private $chain;

    /** @var \Zend\Authentication\Storage\StorageInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $storageA;

    /** @var \Zend\Authentication\Storage\StorageInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $storageB;

    protected function setUp()
    {
        parent::setUp();

        $this->storageA = $this->getMockForAbstractClass('Zend\Authentication\Storage\StorageInterface');
        $this->storageB = $this->getMockForAbstractClass('Zend\Authentication\Storage\StorageInterface');

        $this->chain = new Chain();
        $this->chain->addStorage($this->storageA);
        $this->chain->addStorage($this->storageB);
    }

    /**
     * @test
     * @group Module/DkplusBase
     * @group Component/Authentication
     */
    public function isAnAuthenticationStorage()
    {
        $this->assertInstanceOf('Zend\Authentication\Storage\StorageInterface', $this->chain);
    }

    /**
     * @test
     * @group Module/DkplusBase
     * @group Component/Authentication
     */
    public function isNotEmptyIfAtLeastOneStorageIsNotEmpty()
    {
        $this->storageA->expects($this->any())
                       ->method('isEmpty')
                       ->will($this->returnValue(true));

        $this->storageB->expects($this->any())
                       ->method('isEmpty')
                       ->will($this->returnValue(false));

        $this->assertFalse($this->chain->isEmpty());
    }

    /**
     * @test
     * @group Module/DkplusBase
     * @group Component/Authentication
     */
    public function isEmptyIfAllStoragesAreEmpty()
    {
        $this->storageA->expects($this->any())
                       ->method('isEmpty')
                       ->will($this->returnValue(true));

        $this->storageB->expects($this->any())
                       ->method('isEmpty')
                       ->will($this->returnValue(true));

        $this->assertTrue($this->chain->isEmpty());
    }

    /**
     * @test
     * @group Module/DkplusBase
     * @group Component/Authentication
     */
    public function readsDataFromTheFirstNotEmptyStorage()
    {
        $readData = array('foo' => 'bar', 'bar' => 'baz');

        $this->storageA->expects($this->any())
                       ->method('isEmpty')
                       ->will($this->returnValue(true));

        $this->storageB->expects($this->any())
                       ->method('isEmpty')
                       ->will($this->returnValue(false));
        $this->storageB->expects($this->once())
                       ->method('read')
                       ->will($this->returnValue($readData));

        $this->assertEquals($readData, $this->chain->read());
    }

    /**
     * @test
     * @group Module/DkplusBase
     * @group Component/Authentication
     */
    public function readsNullWhenAllStoragesAreEmpty()
    {
        $this->storageA->expects($this->any())
                       ->method('isEmpty')
                       ->will($this->returnValue(true));

        $this->storageB->expects($this->any())
                       ->method('isEmpty')
                       ->will($this->returnValue(true));

        $this->assertNull($this->chain->read());
    }

    /**
     * @test
     * @group Module/DkplusBase
     * @group Component/Authentication
     */
    public function writesDataToAllStorages()
    {
        $writeData = array('foo' => 'bar', 'bar' => 'baz');

        $this->storageA->expects($this->once())
                       ->method('write')
                       ->with($writeData);

        $this->storageB->expects($this->once())
                       ->method('write')
                       ->with($writeData);

        $this->chain->write($writeData);
    }

    /**
     * @test
     * @group Module/DkplusBase
     * @group Component/Authentication
     */
    public function clearsAllStorages()
    {
        $this->storageA->expects($this->once())
                       ->method('clear');

        $this->storageB->expects($this->once())
                       ->method('clear');

        $this->chain->clear();
    }
}
