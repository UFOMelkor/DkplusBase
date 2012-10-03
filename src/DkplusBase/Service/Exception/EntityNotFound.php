<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Service\Exception
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Service\Exception;

use \RuntimeException;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Service\Exception
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class EntityNotFound extends RuntimeException
{
    public function __construct($message)
    {
        parent::__construct(\sprintf('Could not find data with identifier "%s"', $message));
    }
}
