<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Service\Crud
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Service;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Service\Crud
 * @author     Oskar Bley <oskar@programming-php.net>
 */
interface ServiceInterface
{
    /**
     * @param mixed $data
     * @return mixed created item
     */
    public function create($data);

    /**
     * @throws \DkplusBase\Service\Exception\EntityNotFound
     */
    public function get($identifier);

    public function getCreationForm();

    public function getAll();

    /**
     * @param mixed $data
     * @param mixed $identifier
     * @return mixed updated item
     */
    public function update($data, $identifier);

    /**
     * @throws \DkplusBase\Service\Exception\EntityNotFound
     */
    public function getUpdateForm($identifier);

    public function delete($entity);

    /**
     * @param int $pageNumber
     * @param int $itemCountPerPage
     * @param array $searchData
     * @return \Zend\Paginator\Paginator
     */
    public function getPaginator($pageNumber);

    /** @param int $value */
    public function setItemCountPerPage($value);
}
