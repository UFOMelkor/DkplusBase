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
class NotFoundOptions
{
    /** @var string */
    protected $crController = 'Application\Controller\Index';

    /** @var string */
    protected $crAction = 'index';

    /** @var string[] */
    protected $crRouteParams = array();

    /** @var string */
    protected $crRoute = 'home';

    /** @return string */
    public function getContentReplaceController()
    {
        return $this->crController;
    }

    /** @return string */
    public function getContentReplaceAction()
    {
        return $this->crAction;
    }

    /** @return string[] */
    public function getContentReplaceRouteParams()
    {
        return $this->crRouteParams;
    }

    /** @return string */
    public function getContentReplaceRoute()
    {
        return $this->crRoute;
    }

    public function hasErrorMessage()
    {
    }

    public function getErrorMessage()
    {
    }
}
