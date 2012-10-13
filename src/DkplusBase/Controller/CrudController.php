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
     * Replace the content with this controller action when no item has been found.
     * @var string
     */
    protected $read404Controller = array('Application\Controller\Index', 'index', array(), null);

    /**
     * Message that is shown when no item has been found.
     * @var string
     */
    protected $update404Message;

    /**
     * Replace the content with this controller action when no item has been found.
     * @var string
     */
    protected $update404Controller = array('Application\Controller\Index', 'index', array(), null);

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
     * Replace the content with this controller action when no item has been found.
     * @var string
     */
    protected $delete404Controller = array('Application\Controller\Index', 'index', array(), null);

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
            $dsl = $this->dsl()->replaceContent()->with()->controllerAction(
                $this->read404Controller[0],
                $this->read404Controller[1],
                $this->read404Controller[2]
            )->and()->with()->route($this->read404Controller[3])
             ->and()->pageNotFound()->but()->ignore404NotFoundController();

            if ($this->read404Message) {
                $dsl->and()->add()->notFound()->message($this->read404Message);
            }
            return $dsl;
        }

        return $this->dsl()->assign($data)->as('item');
    }

    public function setControllerActionForContentReplacingForNotFoundDataOnReading(
        $controller,
        $action,
        array $routeParams = array(),
        $route = null
    ) {
        $this->read404Controller = array($controller, $action, $routeParams, $route);
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
            $dsl = $this->dsl()->replaceContent()->with()->controllerAction(
                $this->update404Controller[0],
                $this->update404Controller[1],
                $this->update404Controller[2]
            )->and()->with()->route($this->update404Controller[3])
            ->and()->pageNotFound()->but()->ignore404NotFoundController();

            if ($this->update404Message) {
                $dsl->and()->add()->notFound()->message($this->update404Message);
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

    public function setControllerActionForContentReplacingForNotFoundDataOnUpdating(
        $controller,
        $action,
        array $routeParams = array(),
        $route = null
    ) {
        $this->update404Controller = array($controller, $action, $routeParams, $route);
    }

    public function setErrorMessageForNotFoundDataOnUpdating($message)
    {
        $this->update404Message = $message;
    }

    public function deleteAction()
    {
        $identifier = $this->getEvent()->getRouteMatch()->getParam($this->routeMatchIdentifier);

        try {
            $item = $this->service->get($identifier);
            $this->service->delete($identifier);

        } catch (EntityNotFoundException $e) {
            $dsl = $this->dsl()->replaceContent()->with()->controllerAction(
                $this->delete404Controller[0],
                $this->delete404Controller[1],
                $this->delete404Controller[2]
            )->and()->with()->route($this->read404Controller[3])
            ->and()->pageNotFound()->but()->ignore404NotFoundController();

            if ($this->delete404Message) {
                $dsl->and()->add()->notFound()->message($this->delete404Message);
            }
            return $dsl;
        }

        return $this->dsl()->redirect()->to()->route($this->deleteSuccessRoute)
                           ->with()->success()->message($this->getDeletionSuccessMessage($item));
    }

    public function setControllerActionForContentReplacingForNotFoundDataOnDeletion(
        $controller,
        $action,
        array $routeParams = array(),
        $route = null
    ) {
        $this->delete404Controller = array($controller, $action, $routeParams, $route);
    }

    public function setErrorMessageForNotFoundDataOnDeletion($message)
    {
        $this->delete404Message = $message;
    }

    public function getDeletionSuccessMessage($item)
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
        return $this->dsl()->assign($this->getPaginator($pageNumber, $itemCountPerPage))
                           ->as('paginator');
    }

    /**
     *
     * @param type $pageNumber
     * @param type $itemCountPerPage
     * @return \Zend\Paginator\Paginator
     */
    protected function getPaginator($pageNumber, $itemCountPerPage)
    {
        return $this->service->getPaginator(
            $pageNumber,
            $itemCountPerPage,
            $this->getRequest()->getPost()->toArray()
        );
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
        return $this->dsl()->assign($this->getAllItems())
                           ->as('items');
    }

    /** @return array */
    protected function getAllItems()
    {
        return $this->service->getAll($this->getRequest()->getPost()->toArray());
    }
}
