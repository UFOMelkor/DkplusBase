<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Mapper
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Mapper;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Mapper
 * @author     Oskar Bley <oskar@programming-php.net>
 */
interface MapperInterface
{
    public function save($entity);

    /**
     * @throws \DkplusBase\Service\Exception\EntityNotFound
     */
    public function find($identifier);

    public function findAll();

    public function delete($entity);

    /** @return \Zend\Paginator\Adapter\AdapterInterface */
    public function getPaginationAdapter();
}
