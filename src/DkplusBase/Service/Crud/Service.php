<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Service\Crud
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Service\Crud;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Service\Crud
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class Service implements ServiceInterface
{
    /** @var MapperInterface */
    protected $mapper;

    /** @var FormStrategyInterface */
    protected $formStrategy;

    public function __construct(MapperInterface $mapper, FormStrategyInterface $formStrategy)
    {
        $this->mapper       = $mapper;
        $this->formStrategy = $formStrategy;
    }

    public function create($data)
    {
        $item = $this->formStrategy->createItem($data);
        return $this->mapper->save($item);
    }

    /**
     * @throws \DkplusBase\Service\Exception\EntityNotFound
     */
    public function get($identifier)
    {
        return $this->mapper->find($identifier);
    }

    public function getCreationForm()
    {
        return $this->formStrategy->getCreationForm();
    }

    public function getAll(array $searchData = array(), $orderCrit = null, $orderDirection = null)
    {
        return $this->mapper->findAll($searchData, $orderCrit, $orderDirection);
    }

    public function update($data, $identifier)
    {
        $item = $this->formStrategy->updateItem($data, $this->mapper->find($identifier));
        return $this->mapper->save($item);
    }

    /**
     * @throws \DkplusBase\Service\Exception\EntityNotFound
     */
    public function getUpdateForm($identifier)
    {
        $item = $this->mapper->find($identifier);
        return $this->formStrategy->getUpdateForm($item);
    }

    /**
     * @throws \DkplusBase\Service\Exception\EntityNotFound
     */
    public function delete($identifier)
    {
        $this->mapper->delete($identifier);
    }

    /**
     * @param int $pageNumber
     * @param int $itemCountPerPage
     * @param array $searchData
     * @param string $orderCrit
     * @param string $orderDirection
     * @return \Zend\Paginator\Paginator
     */
    public function getPaginator(
        $pageNumber,
        $itemCountPerPage,
        array $searchData = array(),
        $orderCrit = null,
        $orderDirection = null
    ) {
        $adapter   = $this->mapper->getPaginationAdapter($searchData, $orderCrit, $orderDirection);
        $paginator = new \Zend\Paginator\Paginator($adapter);
        $paginator->setItemCountPerPage($itemCountPerPage);
        $paginator->setCurrentPageNumber($pageNumber);
        return $paginator;
    }
}
