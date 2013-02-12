<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Service\Exception
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Service\Exception;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Service\Exception
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class EntityNotFoundTest extends TestCase
{
    /**
     * @test
     */
    public function isARuntimeException()
    {
        $this->assertInstanceOf('RuntimeException', new EntityNotFound(42));
    }

    /**
     * @test
     */
    public function providesAnMessageIncludingTheGivenIdentifier()
    {
        $exception = new EntityNotFound('my-identifier');
        $this->assertContains('my-identifier', $exception->getMessage());
    }
}
