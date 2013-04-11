<?php
/**
 * @license MIT
 * @link    https://github.com/UFOMelkor/DkplusCrud canonical source repository
 */

namespace DkplusBase\Service\Exception;

use \RuntimeException;

/**
 * @author Oskar Bley <oskar@programming-php.net>
 * @since  0.1.0
 */
class EntityNotFound extends RuntimeException
{
    public function __construct($message)
    {
        parent::__construct(\sprintf('Could not find data with identifier "%s"', $message));
    }
}
