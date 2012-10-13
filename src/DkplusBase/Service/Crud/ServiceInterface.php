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

    public function getAll(array $searchData = array(), $orderCrit = null, $orderDirection = null);

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

    /**
     * @throws \DkplusBase\Service\Exception\EntityNotFound
     */
    public function delete($identifier);

    /**
     * @param int $pageNumber
     * @param int $itemCountPerPage
     * @param array $searchData
     * @return \Zend\Paginator\Paginator
     */
    public function getPaginator(
        $pageNumber,
        $itemCountPerPage,
        array $searchData = array(),
        $orderCrit = null,
        $orderDirection = null
    );
}
