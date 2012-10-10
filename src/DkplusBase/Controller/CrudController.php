<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Controller
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Controller;

use DkplusBase\Service\Crud\ServiceInterface as Service;
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

    /**
     * Message that is shown when no item has been found.
     * @var string
     */
    protected $read404Message;

    /**
     * Redirect to this route when no item has been found.
     * @var string
     */
    protected $read404Route = 'home';

    /**
     * Message that is shown when no item has been found.
     * @var string
     */
    protected $update404Message;

    /**
     * Redirect to this route when no item has been found.
     * @var string
     */
    protected $update404Route = 'home';

    /** @var string */
    protected $updateSuccessRoute;

    /** @var string */
    protected $createSuccessRoute = 'home';

    /**
     * Message that is shown when no item has been found.
     * @var string
     */
    protected $delete404Message;

    /**
     * Redirect to this route when no item has been found.
     * @var string
     */
    protected $delete404Route = 'home';

    /** @var string */
    protected $deleteSuccessRoute = 'home';

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
                                               $this->createSuccessRoute,
                                               array($this, 'getCreationRedirectData')
                                           )
                                           ->with()->success()->message(array($this, 'getCreationMessage'))
                    )->and()->onAjaxRequest(
                        $this->dsl()->assign()->formMessages()->asJson()
                    );
    }

    public function setRedirectRouteForSuccessfulCreating($route)
    {
        $this->createSuccessRoute = $route;
    }

    public function getCreationRedirectData(Container $container)
    {
        return array();
    }

    public function getCreationMessage(Container $container)
    {
        return 'Item has been created.';
    }

    public function readAction()
    {
        $identifier = $this->getEvent()->getRouteMatch()->getParam($this->routeMatchIdentifier);

        try {
            $data = $this->service->get($identifier);

        } catch (EntityNotFoundException $e) {
            $dsl = $this->dsl()->redirect()->to()->route($this->read404Route);

            if ($this->read404Message) {
                $dsl->with()->error()->message($this->read404Message);
            }
            return $dsl;
        }

        return $this->dsl()->assign($data)->as('item');
    }

    public function setRedirectRouteForNotFoundDataOnReading($route)
    {
        $this->read404Route = $route;
    }

    public function setErrorMessageForNotFoundDataOnReading($message)
    {
        $this->read404Message = $message;
    }

    public function updateAction()
    {
        $identifier = $this->getEvent()->getRouteMatch()->getParam($this->routeMatchIdentifier);
        $route      = $this->updateSuccessRoute === null
                    ? $this->getEvent()->getRouteMatch()->getMatchedRouteName()
                    : $this->updateSuccessRoute;

        try {
            $form = $this->service->getUpdateForm($identifier);

        } catch (EntityNotFoundException $e) {
            $dsl = $this->dsl()->redirect()->to()->route($this->update404Route);

            if ($this->update404Message) {
                $dsl->with()->error()->message($this->update404Message);
            }
            return $dsl;
        }

        return $this->dsl()->use($form)->and()->assign()
                    ->and()->validate()->against('postredirectget')
                    ->and()->onSuccess(
                        $this->dsl()->store()->formData()->into(array($this->service, 'update'))->with($identifier)
                                    ->and()->redirect()
                                           ->to()->route(
                                               $route,
                                               array($this, 'getUpdatingRedirectData')
                                           )
                                           ->with()->success()->message(array($this, 'getUpdatingMessage'))
                    )->and()->onAjaxRequest(
                        $this->dsl()->assign()->formMessages()->asJson()
                    );
    }

    public function setRedirectRouteForSuccessfulUpdating($route)
    {
        $this->updateSuccessRoute = $route;
    }

    public function getUpdatingRedirectData(Container $container)
    {
        return $this->getEvent()->getRouteMatch()->getParams();
    }

    public function getUpdatingMessage(Container $container)
    {
        return 'Item has been updated.';
    }

    public function setRedirectRouteForNotFoundDataOnUpdating($route)
    {
        $this->update404Route = $route;
    }

    public function setErrorMessageForNotFoundDataOnUpdating($message)
    {
        $this->update404Message = $message;
    }

    public function deleteAction()
    {
        $identifier = $this->getEvent()->getRouteMatch()->getParam($this->routeMatchIdentifier);

        try {
            $this->service->delete($identifier);

        } catch (EntityNotFoundException $e) {
            $dsl = $this->dsl()->redirect()->to()->route($this->delete404Route);

            if ($this->delete404Message) {
                $dsl->with()->error()->message($this->delete404Message);
            }
            return $dsl;
        }

        return $this->dsl()->redirect()->to()->route($this->deleteSuccessRoute)
                           ->with()->success()->message(array($this, 'getDeletionSuccessMessage'));
    }

    public function setRedirectRouteForNotFoundDataOnDeletion($route)
    {
        $this->delete404Route = $route;
    }

    public function setErrorMessageForNotFoundDataOnDeletion($message)
    {
        $this->delete404Message = $message;
    }

    public function getDeletionSuccessMessage(Container $container)
    {
        return 'An Item has been deleted.';
    }

    public function setRedirectRouteForSuccessfulDeletion($route)
    {
        $this->deleteSuccessRoute = $route;
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
        return $this->getRequest()->getPost()->toArray();
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
        return $this->getRequest()->getPost()->toArray();
    }
}
