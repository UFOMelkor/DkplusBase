<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Authentication
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Authentication\Storage;

use Zend\Authentication\Storage\StorageInterface;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Authentication
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class Chain implements StorageInterface
{
    /** @var StorageInterface[] */
    private $storages = array();

    public function addStorage(StorageInterface $storage)
    {
        $this->storages[] = $storage;
    }

    public function clear()
    {
        foreach ($this->storages as $storage) {
            $storage->clear();
        }
    }

    public function isEmpty()
    {
        foreach ($this->storages as $storage) {
            if (!$storage->isEmpty()) {
                return false;
            }
        }

        return true;
    }

    public function read()
    {
        foreach ($this->storages as $storage) {
            if (!$storage->isEmpty()) {
                return $storage->read();
            }
        }
    }

    public function write($contents)
    {
        foreach ($this->storages as $storage) {
            $storage->write($contents);
        }
    }
}
