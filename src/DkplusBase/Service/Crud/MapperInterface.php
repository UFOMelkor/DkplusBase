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
interface MapperInterface
{
    public function save($item);

    /**
     * @throws \DkplusBase\Service\Exception\EntityNotFound
     */
    public function find($identifier);

    public function findAll(array $searchData);

    /**
     * @throws \DkplusBase\Service\Exception\EntityNotFound
     */
    public function delete($identifier);

    /**
     * @param array $searchData
     * @return \Zend\Paginator\Adapter\AdapterInterface
     */
    public function getPaginationAdapter(array $searchData);
}
