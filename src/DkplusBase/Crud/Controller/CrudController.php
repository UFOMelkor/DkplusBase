<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Controller
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Controller;

use DkplusControllerDsl\Controller\AbstractActionController;
use DkplusControllerDsl\Dsl\DslInterface as Dsl;
use RuntimeException;
use Zend\EventManager\EventManagerInterface as EventManager;
use Zend\Form\FormInterface as Form;
use Zend\Paginator\Paginator;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\View\Model\ModelInterface as ViewModel;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Controller
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class CrudController extends AbstractActionController
{
    public function setEventManager(EventManager $eventManager)
    {
        parent::setEventManager($eventManager);
        $eventManager->addIdentifiers('DkplusBase\Crud\Controller\CrudController');
    }

    /**
     * @param string $name
     * @param array $arguments
     * @param callable $callback
     * @return \Zend\EventManager\ResponseCollection
     */
    protected function triggerEvent($name, array $arguments = array(), $callback = null)
    {
        return $this->getEventManager()->trigger($name, $this, $arguments, $callback);
    }

    /**
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    protected function triggerEventAndGetResult($name, array $arguments = array(), $callback = null)
    {
        if ($callback === null) {
            $callback = function ($result) {
                return $result !== null;
            };
        }
        $result = $this->triggerEvent($name, $arguments, $callback);

        return count($result) > 0 && $result->stopped()
               ? $result->last()
               : null;
    }

    /**
     * @param mixed $result
     * @return boolean
     */
    public function isActionControllerResult($result)
    {
        return (
            $result instanceof ViewModel
            || $result instanceof Dsl
            || $result instanceof Response
            || \is_array($result)
        );
    }

    /**
     * @param mixed $result
     * @return boolean
     */
    public function isForm($result)
    {
        return ($result instanceof Form);
    }

    /**
     * @param mixed $result
     * @return boolean
     */
    public function isPaginator($result)
    {
        return ($result instanceof Paginator);
    }

    public function readAction()
    {
        return $this->singleEntityAction('read');
    }

    protected function singleEntityAction($eventName)
    {
        $eventName            = strtolower($eventName);
        $capitalizedEventName = ucFirst($eventName);

        $entity = $this->triggerEventAndGetResult('CrudController.pre' . $capitalizedEventName, array());

        if ($entity === null) {
            $notFoundResult = $this->triggerEventAndGetResult(
                'CrudController.' . $eventName . 'NotFound',
                array(),
                array($this, 'isActionControllerResult')
            );
            if ($notFoundResult === null) {
                throw new \RuntimeException(
                    'CrudController.' . $eventName . 'NotFound should result in a valid controller response'
                );
            }
            return $notFoundResult;
        }

        $result = $this->triggerEventAndGetResult(
            'CrudController.' . $eventName,
            array('entity' => $entity),
            array($this, 'isActionControllerResult')
        );

        if ($result === null) {
            throw new \RuntimeException(
                'CrudController.' . $eventName . ' should result in a valid controller response'
            );
        }

        $this->triggerEvent(
            'CrudController.post' . $capitalizedEventName,
            array('entity' => $entity, 'result' => $result)
        );

        return $result;
    }

    public function createAction()
    {
        $form = $this->triggerEventAndGetResult('CrudController.preCreate', array(), array($this, 'isForm'));

        if ($form === null) {
            throw new RuntimeException('CrudController.preCreate should result in a form');
        }

        $result = $this->triggerEventAndGetResult(
            'CrudController.create',
            array('form' => $form),
            array($this, 'isActionControllerResult')
        );

        if ($result === null) {
            throw new RuntimeException('CrudController.create should result in a valid controller response');
        }

        $this->triggerEvent('CrudController.postCreate', array('form' => $form, 'result' => $result));

        return $result;
    }

    public function updateAction()
    {
        $form = $this->triggerEventAndGetResult('CrudController.preUpdate', array(), array($this, 'isForm'));

        if ($form === null) {
            $result = $this->triggerEventAndGetResult(
                'CrudController.updateNotFound',
                array(),
                array($this, 'isActionControllerResult')
            );

            if ($result === null) {
                throw new RuntimeException(
                    'CrudController.updateNotFound should result in a valid controller response'
                );
            }
            return $result;
        }

        $result = $this->triggerEventAndGetResult(
            'CrudController.update',
            array('form' => $form),
            array($this, 'isActionControllerResult')
        );

        if ($result === null) {
            throw new RuntimeException('CrudController.update should result in a valid controller response');
        }

        $this->triggerEvent('CrudController.postUpdate', array('form' => $form, 'result' => $result));

        return $result;
    }

    public function deleteAction()
    {
        return $this->singleEntityAction('delete');
    }

    public function listAction()
    {
        $data = $this->triggerEventAndGetResult('CrudController.preList', array());

        if ($data === null) {
            throw new RuntimeException('CrudController.preList should result in anything not null');
        }

        $result = $this->triggerEventAndGetResult(
            'CrudController.list',
            array('data' => $data),
            array($this, 'isActionControllerResult')
        );

        if ($result === null) {
            throw new RuntimeException('CrudController.list should result in a valid controller response');
        }

        $this->triggerEvent('CrudController.postList', array('data' => $data, 'result' => $result));

        return $result;
    }

    public function paginateAction()
    {
        $paginator = $this->triggerEventAndGetResult(
            'CrudController.prePaginate',
            array(),
            array($this, 'isPaginator')
        );

        if ($paginator === null) {
            throw new RuntimeException('CrudController.prePaginate should result in a paginator');
        }

        $result = $this->triggerEventAndGetResult(
            'CrudController.paginate',
            array('paginator' => $paginator),
            array($this, 'isActionControllerResult')
        );

        if ($result === null) {
            throw new RuntimeException('CrudController.paginate should result in a valid controller response');
        }

        $this->triggerEvent('CrudController.postPaginate', array('paginator' => $paginator, 'result' => $result));

        return $result;
    }
}
