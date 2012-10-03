<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Service
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Service;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Service
 * @author     Oskar Bley <oskar@programming-php.net>
 */
interface CrudServiceInterface
{
    public function create($data);

    public function get($identifier);

    public function getAll();

    public function update($data, $identifier);

    public function delete($identifier);

    public function getPaginator($pageNumber, $itemCountPerPage, array $searchData = array());
}
