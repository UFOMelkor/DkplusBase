<?php
/**
 * @license MIT
 * @link    https://github.com/UFOMelkor/DkplusCrud canonical source repository
 */

namespace DkplusBase\View\Helper;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * @author Oskar Bley <oskar@programming-php.net>
 * @since  0.1.0
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

        $methods                = array('hasMessages', 'setNamespace', 'getMessages',
                                        'hasCurrentMessages', 'getCurrentMessages',
                                        'clearCurrentMessages');
        $this->controllerPlugin = $this->getMock('Zend\Mvc\Controller\Plugin\FlashMessenger', $methods);
        $this->viewHelper       = new FlashMessenger($this->controllerPlugin);
    }

    /**
     * @test
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
     */
    public function canRetrieveMessages()
    {
        $messages = array('foo', 'bar', 'baz');

        $this->controllerPlugin->expects($this->any())
                               ->method('getMessages')
                               ->will($this->returnValue($messages));

        $this->assertEquals($messages, $this->viewHelper->getMessages());
    }

    /**
     * @test
     */
    public function canRetrieveCurrentMessages()
    {
        $messages = array('foo', 'bar', 'baz');

        $this->controllerPlugin->expects($this->any())
                               ->method('getCurrentMessages')
                               ->will($this->returnValue($messages));

        $this->assertEquals($messages, $this->viewHelper->getCurrentMessages());
    }

    /**
     * @test
     */
    public function cleansCurrentMessagesAfterRetrieving()
    {
        $this->controllerPlugin->expects($this->at(0))
                               ->method('getCurrentMessages');
        $this->controllerPlugin->expects($this->at(1))
                               ->method('clearCurrentMessages');

        $this->viewHelper->getCurrentMessages();
    }
}
