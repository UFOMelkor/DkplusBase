<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\FormHandler
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\FormHandler;

use Zend\Form\FormInterface as Form;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\FormHandler
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class BindFormHandler implements FormHandlerInterface
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
    public function createEntity($data)
    {
        return $data;
    }

    /**
     * @param mixed $data
     * @param mixed $entity
     * @return mixed
     */
    public function updateEntity($data, $entity)
    {
        return $entity;
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
