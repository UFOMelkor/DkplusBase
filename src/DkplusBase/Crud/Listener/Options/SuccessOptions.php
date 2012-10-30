<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Listener\Options;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class SuccessOptions
{
    /** @var string|callable */
    protected $message = '';

    /** @var string|callable */
    protected $redirectRoute = 'home';

    /** @var array|callable */
    protected $redirectRouteParams = array();

    /** @return string */
    public function getRedirectRoute()
    {
        return $this->redirectRoute;
    }

    /** @return array */
    public function getComputatedRedirectRouteParams($entity)
    {
        if (\is_callable($this->redirectRouteParams)) {
            return \call_user_func($this->redirectRouteParams, $entity);
        }

        return $this->redirectRouteParams;
    }

    /** @return string */
    public function getComputatedMessage($entity)
    {
        if (\is_callable($this->message)) {
            return \call_user_func($this->message, $entity);
        }

        return $this->message;
    }
}
