<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage View\Helper
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\View\Helper;

use Zend\Mvc\Controller\Plugin\FlashMessenger as ControllerPlugin;
use Zend\View\Helper\AbstractHelper;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage View\Helper
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class FlashMessenger extends AbstractHelper
{
    /** @var ControllerPlugin */
    private $plugin;

    public function __construct(ControllerPlugin $plugin)
    {
        $this->plugin = $plugin;
    }

    public function __invoke($namespace = 'default')
    {
        $this->plugin->setNamespace($namespace);
        return $this;
    }

    /** @return boolean */
    public function hasMessages()
    {
        return $this->plugin->hasMessages();
    }

    /** @return string[] */
    public function getMessages()
    {
        return $this->plugin->getMessages();
    }

    /** @return string[] */
    public function getCurrentMessages()
    {
        $result = $this->plugin->getCurrentMessages();
        $this->plugin->clearCurrentMessages();
        return $result;
    }
}
