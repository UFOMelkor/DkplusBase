<?php
/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Listener\Options;

use DkplusUnitTest\TestCase;

/**
 * @category   DkplusTesting
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class SuccessOptionsTest extends TestCase
{
    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function isAnOptionsInstance()
    {
        $this->assertInstanceOf('Zend\Stdlib\AbstractOptions', new SuccessOptions());
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function providesRedirectRoute()
    {
        $options = new SuccessOptions();
        $options->setRedirectRoute('foo/bar');

        $this->assertSame('foo/bar', $options->getRedirectRoute());
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function providesHomeAsInitialRedirectRoute()
    {
        $options = new SuccessOptions();

        $this->assertSame('home', $options->getRedirectRoute());
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function providesRedirectRouteParams()
    {
        $options = new SuccessOptions();
        $options->setRedirectRouteParams(array('foo' => 'bar'));

        $this->assertSame(array('foo' => 'bar'), $options->getComputatedRedirectRouteParams(null));
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function providesAnEmptyArrayAsInitialRedirectRouteParams()
    {
        $options = new SuccessOptions();

        $this->assertSame(array(), $options->getComputatedRedirectRouteParams(null));
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function canUseAnCallbackAsRedirectRouteParams()
    {
        $callbackObject = $this->getMock('stdClass', array('execute'));
        $callbackObject->expects($this->once())
                       ->method('execute')
                       ->will($this->returnValue(array('foo' => 'bar')));

        $options = new SuccessOptions();
        $options->setRedirectRouteParams(array($callbackObject, 'execute'));

        $this->assertSame(array('foo' => 'bar'), $options->getComputatedRedirectRouteParams(null));
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function canUseTheGivenEntityWithinTheRedirectRouteParamsCallback()
    {
        $entity = $this->getMock('stdClass');

        $callbackObject = $this->getMock('stdClass', array('execute'));
        $callbackObject->expects($this->once())
                       ->method('execute')
                       ->with($entity);

        $options = new SuccessOptions();
        $options->setRedirectRouteParams(array($callbackObject, 'execute'));

        $options->getComputatedRedirectRouteParams($entity);
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function providesMessage()
    {
        $options = new SuccessOptions();
        $options->setMessage('successful done');

        $this->assertSame('successful done', $options->getComputatedMessage(null));
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function providesAnEmptyStringAsInitialMessage()
    {
        $options = new SuccessOptions();

        $this->assertSame('', $options->getComputatedMessage(null));
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function canUseAnCallbackAsMessage()
    {
        $callbackObject = $this->getMock('stdClass', array('execute'));
        $callbackObject->expects($this->once())
                       ->method('execute')
                       ->will($this->returnValue(':-)'));

        $options = new SuccessOptions();
        $options->setMessage(array($callbackObject, 'execute'));

        $this->assertSame(':-)', $options->getComputatedMessage(null));
    }

    /**
     * @test
     * @group Component/Listener
     * @group unit
     */
    public function canUseTheGivenEntityWithinTheMessageCallback()
    {
        $entity = $this->getMock('stdClass');

        $callbackObject = $this->getMock('stdClass', array('execute'));
        $callbackObject->expects($this->once())
                       ->method('execute')
                       ->with($entity);

        $options = new SuccessOptions();
        $options->setMessage(array($callbackObject, 'execute'));

        $options->getComputatedMessage($entity);
    }
}
