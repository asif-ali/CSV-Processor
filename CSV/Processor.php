<?php
namespace CSV;

use Exception\FileCannotBeOpened;
use Exception\FileNotFound;
use Stock\Repository as StockRepository;
use Stock\Entity as StockEntity;
use Stock\Mapper as StockMapper;
/**
 * Class Processor
 * @package CSV
 */
class Processor {

    const CSV_TO_PROCESS = 'stock.csv';
    const MIN_COST = 5;
    const MAX_COST = 1000;
    const MIN_STOCK = 10;
    const RULE_1 = 'MinCostMinStock';
    const RULE_2 = 'MaxCost';

    protected $isTest;
    protected $stocksToSave;
    protected $stocksFailed;
    protected $stockRepository;
    protected $stockMapper;

    /**
     * @param StockRepository $stockRepository
     * @param StockMapper $stockMapper
     * @param Boolean $isTest
     */
    public function __construct(StockRepository $stockRepository, StockMapper $stockMapper, $isTest)
    {
        $this->setIsTest($isTest);
        $this->setStockRepository($stockRepository);
        $this->setStockMapper($stockMapper);
        $this->stocksFailed = array();
        $this->stocksToSave = array();
    }

    public function setStockMapper($stockMapper)
    {
        $this->stockMapper = $stockMapper;
    }

    public function getStockMapper()
    {
        return $this->stockMapper;
    }

    public function setStockRepository(StockRepository $stockRepository)
    {
        $this->stockRepository = $stockRepository;
    }

    public function getStockRepository()
    {
        return $this->stockRepository;
    }

    public function getStocksFailed()
    {
        return $this->stocksFailed;
    }

    public function getStocksToSave()
    {
        return $this->stocksToSave;
    }

    /**
     * Add failed stock rows
     * @param StockEntity $stock
     */
    protected function addStockFailed(StockEntity $stock)
    {
        $this->stocksFailed[] = $stock;
    }

    /**
     * Add stock rows to be saved
     * @param StockEntity $stock
     */
    protected function addStockToSave(StockEntity $stock)
    {
        $this->stocksToSave[] = $stock;
    }

    public function setIsTest($isTest)
    {
        $this->isTest = $isTest;
    }

    public function getIsTest()
    {
        return $this->isTest;
    }

    /**
     * Process the CSV file and return results
     * @return Array results
     */
    public function processCsv()
    {
        $stockCollection = $this->convertCsvToStockCollection();

        //Apply rules and separate successful and failed stock rows
        foreach ($stockCollection as $stock) {
            if ($this->applyMinCostMinStockRule($stock) || $this->applyMaxCostRule($stock)) {
                $this->addStockFailed($stock);
                continue;
            }
            $this->addStockToSave($stock);
        }

        //Store results in an array
        $results = array();
        $results['Total'] = count($stockCollection);
        $results['Successful'] = count($this->getStocksToSave());
        $results['Failed'] = $this->getStocksFailed();

        //If it was a test, then return without saving, else save
        if ($this->getIsTest()) {
            return $results;
        }
        $this->getStockRepository()->saveStockCollection($this->getStocksToSave());
        return $results;
    }

    /**
     * Apply Rule 1
     * Stock item which costs less than £5
     *                   AND
     * Has less than 10 stock will not be imported.
     * @param StockEntity $stock
     * @return Boolean TRUE/FALSE
     */
    protected function applyMinCostMinStockRule(StockEntity $stock)
    {
        if ($stock->getCostInGBP() < static::MIN_COST && $stock->getStock() < static::MIN_STOCK) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Apply Rule 2
     * Any stock items which cost over £1000 will not be imported.
     * @param StockEntity $stock
     * @return Boolean TRUE/FALSE
     */
    protected function applyMaxCostRule(StockEntity $stock)
    {
        if ($stock->getCostInGBP() > static::MAX_COST) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Read CSV file and convert it into a Stock Collection
     * @throws FileNotFound
     * @throws FileCannotBeOpened
     * @return Array $stock
     */
    protected function convertCsvToStockCollection()
    {

        //Check if file exists
        if(!file_exists(static::CSV_TO_PROCESS)) {
            throw new FileNotFound('File not found. Please check if the file exists and its named as stock.csv');
        }

        //Open file to process
        $fp = fopen(static::CSV_TO_PROCESS, 'r');

        //Check if file has been opened, otherwise throw exception
        if (!$fp) {
            throw new FileCannotBeOpened('File cannot be opened. Please check if the file exists and its named as stock.csv');
        }

        //First read headers
        $headers = array_map('trim', fgetcsv($fp));

        //Now process rest of the data into a collection of stock objects
        $stockCollection = array();
        while ($row = fgetcsv($fp)) {
            $stockData = array();
            foreach ($row as $index => $value)  {
                if (isset($headers[$index])) {
                    $stockData[$headers[$index]] = $value;
                }
            }
            //Ask mapper to convert array to object
            $stockCollection[] = $this->getStockMapper()->arrayToEntity($stockData);
        }
        return $stockCollection;
    }
}