<?php
namespace Stock;

use Stock\Entity as StockEntity;

/**
 * Class Mapper
 * @package Stock
 */
class Mapper {

    public function arrayToEntity(Array $stockData)
    {
        return new StockEntity($stockData);
    }
}