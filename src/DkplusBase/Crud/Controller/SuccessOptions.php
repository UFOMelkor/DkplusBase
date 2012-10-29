<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Controller
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Controller;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Controller
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class SuccessOptions
{
    /** @var string */
    protected $message;

    /** @var string */
    protected $route = 'home';

    public function getRedirectRoute()
    {
        return $this->route;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
