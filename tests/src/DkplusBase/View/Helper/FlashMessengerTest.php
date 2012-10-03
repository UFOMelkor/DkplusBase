<?php
/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage View\Helper
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\View\Helper;

use DkplusUnitTest\TestCase;

/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage View\Helper
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class FlashMessengerTest extends TestCase
{
    /** @var \Zend\Mvc\Controller\Plugin\FlashMessenger|\PHPUnit_Framework_MockObject_MockObject */
    private $controllerPlugin;

    /** @var FlashMessenger */
    private $viewHelper;

    protected function setUp()
    {
        parent::setUp();
        $this->controllerPlugin = $this->getMock('Zend\Mvc\Controller\Plugin\FlashMessenger');
        $this->viewHelper       = new FlashMessenger($this->controllerPlugin);
    }

    /**
     * @test
     * @group Component/ViewHelper
     * @group Module/DkplusBase
     * @dataProvider hasMessagesProvider
     */
    public function canDetectWhetherMessagesDoesExistOrDoesNot($hasMessages)
    {
        $this->controllerPlugin->expects($this->any())
                               ->method('hasMessages')
                               ->will($this->returnValue($hasMessages));
        $this->assertSame($hasMessages, $this->viewHelper->hasMessages());
    }

    public static function hasMessagesProvider()
    {
        return array(array(true), array(false));
    }

    /**
     * @test
     * @group Component/ViewHelper
     * @group Module/DkplusBase
     */
    public function usesDefaultAsNamespaceWhenNoOtherNamespaceIsGiven()
    {
        $this->controllerPlugin->expects($this->once())
                               ->method('setNamespace')
                               ->with('default');
        $this->viewHelper->__invoke();
    }

    /**
     * @test
     * @group Component/ViewHelper
     * @group Module/DkplusBase
     */
    public function canUseCustomNamespaces()
    {
        $this->controllerPlugin->expects($this->once())
                               ->method('setNamespace')
                               ->with('any namespace');
        $this->viewHelper->__invoke('any namespace');
    }

    /**
     * @test
     * @group Component/ViewHelper
     * @group Module/DkplusBase
     */
    public function canRetrieveMessages()
    {
        $messages = array('foo', 'bar', 'baz');

        $this->controllerPlugin->expects($this->any())
                               ->method('getMessages')
                               ->will($this->returnValue($messages));

        $this->assertEquals($messages, $this->viewHelper->getMessages());
    }
}
