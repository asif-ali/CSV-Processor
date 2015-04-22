<?php
namespace Stock\Storage;

use Stock\Storage;
/**
 * Class Database
 * @implements Stock\Storage
 * @package Stock\Storage
 */
class Database implements Storage{

    protected $dbConnection;
    protected $dbHost;
    protected $dbUsername;
    protected $dbPassword;
    protected $dbDatabase;

    /**
     * @param $dbDatabase
     * @param $dbHost
     * @param $dbPassword
     * @param $dbUsername
     */
    public function __construct($dbDatabase, $dbHost, $dbPassword, $dbUsername)
    {
        $this->dbConnection = null;
        $this->setDbDatabase($dbDatabase);
        $this->setDbHost($dbHost);
        $this->setDbPassword($dbPassword);
        $this->setDbUsername($dbUsername);
    }

    public function setDbDatabase($dbDatabase)
    {
        $this->dbDatabase = $dbDatabase;
    }

    public function getDbDatabase()
    {
        return $this->dbDatabase;
    }

    public function setDbHost($dbHost)
    {
        $this->dbHost = $dbHost;
    }

    public function getDbHost()
    {
        return $this->dbHost;
    }

    public function setDbPassword($dbPassword)
    {
        $this->dbPassword = $dbPassword;
    }

    public function getDbPassword()
    {
        return $this->dbPassword;
    }

    public function setDbUsername($dbUsername)
    {
        $this->dbUsername = $dbUsername;
    }

    public function getDbUsername()
    {
        return $this->dbUsername;
    }

    public function setDbConnection($dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    /**
     * Get Database Connection, If not connected, reconnect
     * @throws \Exception
     * @return $dbConnection
     */
    public function getDbConnection()
    {
        if (!isset($this->dbConnection)) {
            $this->dbConnection = mysql_connect($this->getDbHost(), $this->getDbUsername(), $this->getDbPassword());
            if (!isset($this->dbConnection)) {
                throw new \Exception('Cannot connect to database');
            }
            mysql_select_db($this->getDbDatabase(), $this->dbConnection);
        }
        return $this->dbConnection;
    }

    /**
     * @param $stockCollection
     */
    public function saveStockCollection(Array $stockCollection)
    {
        /*
         * REPLACE INTO used because of unique key on stProductCode
         * This also helps to avoid Race Condition between SELECT/UPDATE queries
         */
        $sql = 'REPLACE INTO tblProductData (strProductName, strProductDesc, strProductCode, dtmAdded,
                    dtmDiscontinued, intStockLevel, decPrice) VALUES';

        $currentDateTime = new \DateTime('now');

        $values = array();
        foreach ($stockCollection as $stock) {
            $values[] = "("
                . "'" . mysql_real_escape_string($stock->getProductName()) . "',"
                . "'" . mysql_real_escape_string($stock->getProductDescription()) . "',"
                . "'" .mysql_real_escape_string($stock->getProductCode()) . "',"
                . "'" .$currentDateTime->format('Y-m-d H:i:s') . "',"
                . "'" . (($stock->getDiscontinued() == 'yes') ? $currentDateTime->format('Y-m-d H:i:s') : NULL) . "',"
                . "'" . $stock->getStock() ."',"
                . "'" . $stock->getCostInGBP() . "'"
                . ")";
        }
print_r($values);
        $sql .= implode(',', $values);

        mysql_query($sql, $this->getDbConnection());
    }
}