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
interface FormStrategyInterface
{
    /**
     * @param mixed $data
     * @return mixed the created item
     */
    public function createItem($data);

    /**
     * @param mixed $data
     * @param mixed $item
     * @return mixed the updated item
     */
    public function updateItem($data, $item);

    /** @return \Zend\Form\FormInterface */
    public function getCreationForm();

    /**
     * @param mixed $item The model
     * @return \Zend\Form\FormInterface
     */
    public function getUpdateForm($item);
}
