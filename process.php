<?php
require_once __DIR__ . '/include/autoload.php';

use Stock\Storage\Database as StockDatabase;
use Stock\Repository as StockRepository;
use Stock\Mapper as StockMapper;
use CSV\Processor;
use Exception\FileNotFound;
use Exception\FileCannotBeOpened;

//Database connection details
$dbName = 'csvTest';
$dbHost = 'localhost';
$dbPassword = 'xxx';
$dbUser = 'xxx';

$database = new StockDatabase($dbName, $dbHost, $dbUser, $dbPassword);
$repository = new StockRepository($database);
$mapper = new StockMapper();


//Test mode off by default
$isTest = false;

//To avoid getting PHP Notice: Undefined offset, check isset first
if(isset($argv[1])) {
    if ($argv[1] == 'test') {
        $isTest = true;
    }
}

$csvProcessor = new Processor($repository, $mapper, $isTest);

try {
    $results = $csvProcessor->processCsv();

    //Display the results
    echo PHP_EOL;
    echo 'Total Rows Found in CSV: ' . $results['Total'] . PHP_EOL;
    echo 'Total Rows Stored in DB: ' . $results['Successful'] . PHP_EOL;
    echo 'Total Rows Failed : ' . count($results['Failed']) . PHP_EOL;
    echo 'Failed Product Codes:' . PHP_EOL;

    foreach ($results['Failed'] as $failed) {
        echo $failed->getProductCode() . PHP_EOL;
    }

} catch (FileNotFound $e) {
    echo $e->getMessage();
} catch (FileCannotBeOpened $e) {
    echo $e->getMessage();
} catch (Exception $e) {
    echo $e->getMessage();
}