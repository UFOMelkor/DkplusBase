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
use DkplusControllerDsl\Dsl\ContainerInterface as Container;
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
    protected $errorMessageForNotFoundDataWhileReading;

    /** @var string */
    protected $redirectRouteForNotFoundDataWhileReading = 'home';

    /** @var string */
    protected $redirectRouteForSuccesfulCreating = 'home';

    public function __construct(Service $service, Form $form)
    {
        $this->service = $service;
        $this->form    = $form;
    }

    public function createAction()
    {
        return $this->dsl()->use($this->form)->and()->assign()
                    ->and()->validate()->against('postredirectget')
                    ->and()->onSuccess(
                        $this->dsl()->store()->formData()->into(array($this->service, 'create'))
                                    ->and()->redirect()
                                           ->to()->route(
                                               $this->redirectRouteForSuccesfulCreating,
                                               array($this, 'getCreationRedirectData')
                                           )
                                           ->with()->success()->message(array($this, 'getCreationMessage'))
                    )->and()->onAjaxRequest(
                        $this->dsl()->assign()->formMessages()->asJson()
                    );
    }

    public function setRedirectRouteForSuccessfulCreating($route)
    {
        $this->redirectRouteForSuccesfulCreating = $route;
    }

    public function getCreationRedirectData(Container $container)
    {

    }

    public function getCreationMessage(Container $container)
    {

    }

    public function readAction()
    {
        try {
            $data = $this->service->get($this->getEvent()->getRouteMatch()->getParam('id'));

        } catch (EntityNotFoundException $e) {
            $dsl = $this->dsl()->redirect()->to()->route($this->redirectRouteForNotFoundDataWhileReading);

            if ($this->errorMessageForNotFoundDataWhileReading) {
                $dsl->with()->error()->message($this->errorMessageForNotFoundDataWhileReading);
            }
            return $dsl;
        }

        return $this->dsl()->assign($data)->as('data');
    }

    public function setRedirectRouteForNotFoundDataOnReading($route)
    {
        $this->redirectRouteForNotFoundDataWhileReading = $route;
    }

    public function setErrorMessageForNotFoundDataOnReading($message)
    {
        $this->errorMessageForNotFoundDataWhileReading = $message;
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
