<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Service\Crud
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Service\Crud;

use DkplusBase\Stdlib\Hydrator\HydrationFactoryInterface as HydrationFactory;
use Zend\Form\FormInterface as Form;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Service\Crud
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class FactoryFormStrategy implements FormStrategyInterface
{
    /** @var Form */
    private $form;

    /** @var HydrationFactory */
    private $factory;

    public function __construct(Form $form, HydrationFactory $factory)
    {
        $this->form    = $form;
        $this->factory = $factory;
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    public function createItem($data)
    {
        return $this->factory->create($data);
    }

    /**
     * @param mixed $data
     * @param mixed $item
     * @return mixed
     */
    public function updateItem($data, $item)
    {
        $this->factory->hydrate($data, $item);
        return $item;
    }

    /** @return Form */
    public function getCreationForm()
    {
        return $this->form;
    }

    /**
     * @param mixed $item
     * @return Form
     */
    public function getUpdateForm($item)
    {
        $data = $this->factory->extract($item);
        $this->form->populateValues($data);
        return $this->form;
    }
}
