<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Controller
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Controller;

use DkplusBase\Crud\Service\ServiceInterface as Service;
use DkplusBase\Service\Exception\EntityNotFound as EntityNotFoundException;
use DkplusControllerDsl\Controller\AbstractActionController;
use DkplusControllerDsl\Dsl\ContainerInterface as Container;
use Zend\EventManager\EventManagerInterface as EventManager;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Controller
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class CrudController2 extends AbstractActionController
{
    /** @var Service */
    protected $service;

    /**
     * Not found options for read action.
     * @var NotFoundOptions
     */
    protected $read404Options;

    /**
     * Not found options for update action.
     * @var NotFoundOptions
     */
    protected $update404Options;

    /** @var string */
    protected $updateSuccessRoute;

    /** @var string */
    protected $createSuccessRoute = 'home';

    /**
     * Not found options for delete action.
     * @var NotFoundOptions
     */
    protected $delete404Options;

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

    public function setEventManager(EventManager $eventManager)
    {
        parent::setEventManager($eventManager);
        $eventManager->addIdentifiers('DkplusBase\Crud\Controller\CrudController');
    }

    public function setRouteMatchIdentifier($identifierParameter)
    {
        $this->routeMatchIdentifier = $identifierParameter;
    }

    public function createAction()
    {
        $this->triggerEvent('CrudController.preCreate');
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

    protected function triggerEvent($name, array $arguments = array(), $callback = null)
    {
        return $this->getEventManager()->trigger($name, $this, $arguments, $callback);
    }

    protected function triggerEventAndGetResult($name, array $arguments = array())
    {
        $testFunction = function ($result) {
            return $result !== null;
        };
        $result = $this->triggerEvent($name, $arguments, $testFunction);
        return $result->first();
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
        $identifier = $this->triggerEventAndGetResult('CrudController.preRead', array(), 0);
        $identifier = $this->getEvent()->getRouteMatch()->getParam($this->routeMatchIdentifier);

        try {
            $data = $this->triggerEventAndGetResult('CrudController.read', array('identifier' => $identifier));
            //$data = $this->service->get($identifier);
            /*$resultResponse = $this->triggerEvent(
                'CrudController.read',
                array('entity' => $data),
                function ($result) {
                    return ($result !== null);
                }
            );*/

            if (count($resultResponse) > 0) {
                $data = $resultResponse->first();
            }

        } catch (EntityNotFoundException $e) {
            return $this->notFound($this->getNotFoundOptionsForReading());
        }

        return $this->dsl()->assign($data)->as('entity');
    }

    /** return \DkplusControllerDsl\Dsl\DslInterface */
    protected function notFound(NotFoundOptions $options)
    {
        $dsl = $this->dsl()->replaceContent()->with()->controllerAction(
            $options->getContentReplaceController(),
            $options->getContentReplaceAction(),
            $options->getContentReplaceRouteParams()
        )->and()->with()->route($options->getContentReplaceRoute())
         ->and()->pageNotFound()->but()->ignore404NotFoundController();

        if ($options->hasErrorMessage()) {
            $dsl->and()->add()->notFound()->message($options->getErrorMessage());
        }
        return $dsl;
    }

    public function setNotFoundOptionsForReading(NotFoundOptions $options)
    {
        $this->read404Options = $options;
    }

    /** @return NotFoundOptions */
    protected function getNotFoundOptionsForReading()
    {
        if ($this->read404Options === null) {
            $this->read404Options = new NotFoundOptions();
        }
        return $this->read404Options;
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
            return $this->notFound($this->getNotFoundOptionsForUpdating());
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

    public function setNotFoundOptionsForUpdating(NotFoundOptions $options)
    {
        $this->update404Options = $options;
    }

    /** @return NotFoundOptions */
    protected function getNotFoundOptionsForUpdating()
    {
        if ($this->update404Options === null) {
            $this->update404Options = new NotFoundOptions();
        }
        return $this->update404Options;
    }

    public function deleteAction()
    {
        $identifier = $this->getEvent()->getRouteMatch()->getParam($this->routeMatchIdentifier);

        try {
            $item = $this->service->get($identifier);
            $this->service->delete($identifier);

        } catch (EntityNotFoundException $e) {
            return $this->notFound($this->getNotFoundOptionsForDeletion());
        }

        return $this->dsl()->redirect()->to()->route($this->deleteSuccessRoute)
                           ->with()->success()->message($this->getDeletionSuccessMessage($item));
    }

    public function setNotFoundOptionsForDeletion(NotFoundOptions $options)
    {
        $this->delete404Options = $options;
    }

    /** @return NotFoundOptions */
    protected function getNotFoundOptionsForDeletion()
    {
        if ($this->delete404Options === null) {
            $this->delete404Options = new NotFoundOptions();
        }
        return $this->delete404Options;
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
