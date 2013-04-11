<?php
/**
 * @license MIT
 * @link    https://github.com/UFOMelkor/DkplusCrud canonical source repository
 */

namespace DkplusBase\Service\Exception;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * @author Oskar Bley <oskar@programming-php.net>
 * @since  0.1.0
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
