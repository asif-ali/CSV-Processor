<?php
namespace Stock;

/**
 * Interface Storage
 * @package Stock
 */
interface Storage {

    public function saveStockCollection(Array $stockCollection);
}