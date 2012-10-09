<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Stdlib\Hydrator
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Stdlib\Hydrator;

use Zend\Stdlib\Hydrator\HydratorInterface as Hydrator;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Stdlib\Hydrator
 * @author     Oskar Bley <oskar@programming-php.net>
 */
interface HydrationFactoryInterface extends Hydrator
{
    public function create(array $data);
}
