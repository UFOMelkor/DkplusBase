<?php
/**
 * @license MIT
 * @link    https://github.com/UFOMelkor/DkplusCrud canonical source repository
 */

namespace DkplusBase\Stdlib\Hydrator;

use Zend\Stdlib\Hydrator\HydratorInterface as Hydrator;

/**
 * @author Oskar Bley <oskar@programming-php.net>
 * @since  0.1.0
 */
interface HydrationFactoryInterface extends Hydrator
{
    public function create(array $data);
}
