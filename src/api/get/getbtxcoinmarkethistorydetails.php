<?php
/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 12/6/2017
 * Time: 9:41 PM
 *
 * Query Parameters:
 *  btxid   - Text / Array of text
 *  coin    - Text / Array of text
 *  market  - Text / Array of text
 *  quantityGT  - integer / decimal
 *  quantityLT  - integer / decimal
 *  valueGT   - integer / decimal
 *  valueLT   - integer / decimal
 *  totalGT   - integer / decimal
 *  totalLT   - integer / decimal
 *  orderType   - integer 0/1
 *  fillType   - integer 0/1
 *  orderBy - Array of text
 *  orderASC - integer 0/1
 *  startIndex - integer
 *  batchSize - integer
 *
 *  Sample:
 *      http://159.203.122.96/src/api/get/getbtxcoinmarkethistorydetails.php?coin[]=EMC2&market=BTC&orderBy[]=btxtimestamp&orderASC=1&startIndex=100&batchSize=100&orderType=1&fillType=2
 */

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require_once $root . '/vendor/autoload.php';

/* Create connection to PGSQL DB */
$connection = new src\connections\PGSQLConnector();
$btxFinder = new src\CRUD\read\BTXFinder($connection);
$urlParams = parse_str($_SERVER['QUERY_STRING'], $output);

//create a unique statement name for the pg_query
$stmntName = "getbtxcoinmarkethistorydetails_".date('U');

$apiRetObject = \src\APIReturnObject::CreateNewAPIReturnObject(
    false, -1, "", ""
);

if(is_null($output['coin']) || is_null($output['market'])) {
    $apiRetObject->setSuccess(false);
    $apiRetObject->setResponse("Must provide a coin and market to query on.");
} else {
    $testing = $btxFinder->GetMarketHistoryDetails(
        $stmntName, "", "", $output['coin'], $output['market']
        , $output['quantityGT'], $output['quantityLT']
        , $output['valueGT'], $output['valueLT']
        , $output['totalGT'], $output['totalGT']
        , $output['fillType'], $output['orderType']
        , $output['orderBy'], $output['orderASC']
        , $output['startIndex'], $output['batchSize']
    );
    if($testing === false) {
        $apiRetObject->setSuccess(true);
        $apiRetObject->setResponse("Coin does not exist in DB.");
    }
    else {
        $apiRetObject->setSuccess(true);
        $apiRetObject->setResponse($testing);
    }

}

echo $apiRetObject->returnJSONEncodedAPIObject();