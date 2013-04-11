<?php
/**
 * @license MIT
 * @link    https://github.com/UFOMelkor/DkplusCrud canonical source repository
 */

namespace DkplusBase\Authentication\Storage;

use Zend\Authentication\Storage\StorageInterface;

/**
 * @author Oskar Bley <oskar@programming-php.net>
 * @since  0.1.0
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
