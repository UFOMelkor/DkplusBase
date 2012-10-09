<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Service\Crud
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Service\Crud;

use Zend\Form\FormInterface as Form;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Service\Crud
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class BindFormStrategy implements FormStrategyInterface
{
    /** @var Form */
    private $form;

    /** @var string */
    private $modelClass;

    public function __construct(Form $form, $modelClass)
    {
        $this->form       = $form;
        $this->modelClass = $modelClass;
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    public function createItem($data)
    {
        return $data;
    }

    /**
     * @param mixed $data
     * @param mixed $item
     * @return mixed
     */
    public function updateItem($data, $item)
    {
        return $item;
    }

    /** @return Form */
    public function getCreationForm()
    {
        $modelClass = $this->modelClass;
        $this->form->bind(new $modelClass);
        return $this->form;
    }

    /**
     * @param mixed $item
     * @return Form
     */
    public function getUpdateForm($item)
    {
        $this->form->bind($item);
        return $this->form;
    }
}
