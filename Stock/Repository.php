<?php
namespace Stock;

use Stock\Storage;

/**
 * Class Repository
 * @package Stock
 */
class Repository {

    protected $storage;

    /**
     * @param Storage $storage
     */
    function __construct(Storage $storage)
    {
        $this->setStorage($storage);
    }

    public function setStorage($storage)
    {
        $this->storage = $storage;
    }

    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @param Array $stockCollection
     */
    public function saveStockCollection(Array $stockCollection)
    {
        $this->getStorage()->saveStockCollection($stockCollection);
    }
}