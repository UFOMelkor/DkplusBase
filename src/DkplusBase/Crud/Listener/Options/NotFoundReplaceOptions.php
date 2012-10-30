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
class NotFoundReplaceOptions
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
