<?php
namespace Stock;

/**
 * Class Entity
 * @package Stock
 */
class Entity {

    protected $productCode;
    protected $productName;
    protected $productDescription;
    protected $stock;
    protected $costInGBP;
    protected $discontinued;

    /**
     * @param array $entityData
     */
    public function __construct(Array $entityData)
    {
        $this->setCostInGBP($entityData['Cost in GBP']);
        $this->setDiscontinued($entityData['Discontinued']);
        $this->setProductCode($entityData['Product Code']);
        $this->setProductDescription($entityData['Product Description']);
        $this->setProductName($entityData['Product Name']);
        $this->setStock($entityData['Stock']);
    }

    public function setCostInGBP($costInGBP)
    {
        $this->costInGBP = $costInGBP;
    }

    public function getCostInGBP()
    {
        return $this->costInGBP;
    }

    public function setDiscontinued($discontinued)
    {
        $this->discontinued = $discontinued;
    }

    public function getDiscontinued()
    {
        return $this->discontinued;
    }

    public function setProductCode($productCode)
    {
        $this->productCode = $productCode;
    }

    public function getProductCode()
    {
        return $this->productCode;
    }

    public function setProductDescription($productDescription)
    {
        $this->productDescription = $productDescription;
    }

    public function getProductDescription()
    {
        return $this->productDescription;
    }

    public function setProductName($productName)
    {
        $this->productName = $productName;
    }

    public function getProductName()
    {
        return $this->productName;
    }

    public function setStock($stock)
    {
        $this->stock = $stock;
    }

    public function getStock()
    {
        return $this->stock;
    }
}