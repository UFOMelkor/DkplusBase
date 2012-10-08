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

    /** @var string */
    protected $errorMessageForNotFoundDataWhileReading;

    /** @var string */
    protected $redirectRouteForNotFoundDataWhileReading = 'home';

    /** @var string */
    protected $errorMessageForNotFoundDataWhileUpdating;

    /** @var string */
    protected $redirectRouteForNotFoundDataWhileUpdating = 'home';

    /** @var string */
    protected $redirectRouteForSuccesfulUpdating = 'home';

    /** @var string */
    protected $redirectRouteForSuccesfulCreating = 'home';

    /** @var string */
    protected $errorMessageForNotFoundDataWhileDeleting;

    /** @var string */
    protected $redirectRouteForNotFoundDataWhileDeleting = 'home';

    /** @var string */
    protected $successMessageForSuccessfulDeletion;

    /** @var string */
    protected $redirectRouteForSuccessfulDeletion = 'home';

    /** @var string */
    protected $routeMatchIdentifier = 'id';

    /** @var string */
    protected $routeMatchPage = 'page';

    /** @var int */
    protected $itemCountPerPage = 10;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    public function setRouteMatchIdentifier($identifierParameter)
    {
        $this->routeMatchIdentifier = $identifierParameter;
    }

    public function createAction()
    {
        return $this->dsl()->use($this->service->getCreationForm())->and()->assign()
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
        $identifier = $this->getEvent()->getRouteMatch()->getParam($this->routeMatchIdentifier);

        try {
            $data = $this->service->get($identifier);

        } catch (EntityNotFoundException $e) {
            $dsl = $this->dsl()->redirect()->to()->route($this->redirectRouteForNotFoundDataWhileReading);

            if ($this->errorMessageForNotFoundDataWhileReading) {
                $dsl->with()->error()->message($this->errorMessageForNotFoundDataWhileReading);
            }
            return $dsl;
        }

        return $this->dsl()->assign($data)->as('item');
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
        $identifier = $this->getEvent()->getRouteMatch()->getParam($this->routeMatchIdentifier);

        try {
            $form = $this->service->getUpdateForm($identifier);

        } catch (EntityNotFoundException $e) {
            $dsl = $this->dsl()->redirect()->to()->route($this->redirectRouteForNotFoundDataWhileUpdating);

            if ($this->errorMessageForNotFoundDataWhileUpdating) {
                $dsl->with()->error()->message($this->errorMessageForNotFoundDataWhileUpdating);
            }
            return $dsl;
        }

        return $this->dsl()->use($form)->and()->assign()
                    ->and()->validate()->against('postredirectget')
                    ->and()->onSuccess(
                        $this->dsl()->store()->formData()->into(array($this->service, 'update'))->with($identifier)
                                    ->and()->redirect()
                                           ->to()->route(
                                               $this->redirectRouteForSuccesfulUpdating,
                                               array($this, 'getUpdatingRedirectData')
                                           )
                                           ->with()->success()->message(array($this, 'getUpdatingMessage'))
                    )->and()->onAjaxRequest(
                        $this->dsl()->assign()->formMessages()->asJson()
                    );
    }

    public function setRedirectRouteForSuccessfulUpdating($route)
    {
        $this->redirectRouteForSuccesfulUpdating = $route;
    }

    public function getUpdatingRedirectData(Container $container)
    {

    }

    public function getUpdatingMessage(Container $container)
    {

    }

    public function setRedirectRouteForNotFoundDataOnUpdating($route)
    {
        $this->redirectRouteForNotFoundDataWhileUpdating = $route;
    }

    public function setErrorMessageForNotFoundDataOnUpdating($message)
    {
        $this->errorMessageForNotFoundDataWhileUpdating = $message;
    }

    public function deleteAction()
    {
        $identifier = $this->getEvent()->getRouteMatch()->getParam($this->routeMatchIdentifier);

        try {
            $this->service->delete($identifier);

        } catch (EntityNotFoundException $e) {
            $dsl = $this->dsl()->redirect()->to()->route($this->redirectRouteForNotFoundDataWhileDeleting);

            if ($this->errorMessageForNotFoundDataWhileDeleting) {
                $dsl->with()->error()->message($this->errorMessageForNotFoundDataWhileDeleting);
            }
            return $dsl;
        }

        $dsl = $this->dsl()->redirect()->to()->route($this->redirectRouteForSuccessfulDeletion);
        if ($this->successMessageForSuccessfulDeletion) {
            $dsl->with()->success()->message($this->successMessageForSuccessfulDeletion);
        }
        return $dsl;
    }

    public function setRedirectRouteForNotFoundDataOnDeletion($route)
    {
        $this->redirectRouteForNotFoundDataWhileDeleting = $route;
    }

    public function setErrorMessageForNotFoundDataOnDeletion($message)
    {
        $this->errorMessageForNotFoundDataWhileDeleting = $message;
    }

    public function setRedirectRouteForSuccessfulDeletion($route)
    {
        $this->redirectRouteForSuccessfulDeletion = $route;
    }

    public function setSuccessMessageForDeletion($message)
    {
        $this->successMessageForSuccessfulDeletion = $message;
    }

    public function paginateAction()
    {
        $pageNumber       = $this->getEvent()->getRouteMatch()->getParam($this->routeMatchPage, 1);
        $itemCountPerPage = $this->itemCountPerPage;
        $searchData       = $this->getPaginationSearchData();
        return $this->dsl()->assign($this->service->getPaginator($pageNumber, $itemCountPerPage, $searchData))
                           ->as('paginator');
    }

    /** @return array */
    protected function getPaginationSearchData()
    {
        return $this->getRequest()->getPost();
    }

    public function setPageParameter($page)
    {
        $this->routeMatchPage = $page;
    }

    public function setItemCountPerPage($itemCount)
    {
        $this->itemCountPerPage = $itemCount;
    }

    public function listAction()
    {
        return $this->dsl()->assign($this->service->getAll($this->getListingSearchData()))
                           ->as('items');
    }


    /** @return array */
    protected function getListingSearchData()
    {
        return $this->getRequest()->getPost();
    }
}
