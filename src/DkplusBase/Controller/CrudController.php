<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Controller
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Controller;

use DkplusBase\Service\CrudServiceInterface as Service;
use DkplusBase\Service\Exception\EntityNotFound as EntityNotFoundException;
use DkplusControllerDsl\Controller\AbstractActionController;
use Zend\Form\FormInterface as Form;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Controller
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class CrudController extends AbstractActionController
{
    /** @var Service */
    protected $service;

    /** @var Form */
    protected $form;

    /** @var string */
    protected $errorMessageForNotFoundDataOnReading;

    /** @var string */
    protected $redirectRouteForNotFoundDataOnReading = 'home';

    public function __construct(Service $service, Form $form)
    {
        $this->service = $service;
        $this->form    = $form;
    }

    public function createAction()
    {

    }

    public function readAction()
    {
        try {
            $data = $this->service->get($this->getEvent()->getRouteMatch()->getParam('id'));
        } catch (EntityNotFoundException $e) {
            $dsl = $this->dsl()->redirect()->to()->route($this->redirectRouteForNotFoundDataOnReading);

            if ($this->errorMessageForNotFoundDataOnReading) {
                $dsl->with()->error()->message($this->errorMessageForNotFoundDataOnReading);
            }

            return $dsl;
        }

        return $this->dsl()->assign($data)->as('data');
    }

    public function setRedirectRouteForNotFoundDataOnReading($route)
    {
        $this->redirectRouteForNotFoundDataOnReading = $route;
    }

    public function setErrorMessageForNotFoundDataOnReading($message)
    {
        $this->errorMessageForNotFoundDataOnReading = $message;
    }

    public function updateAction()
    {

    }

    public function deleteAction()
    {

    }

    public function paginateAction()
    {

    }

    public function listAllAction()
    {

    }
}
