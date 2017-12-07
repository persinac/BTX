<?php
/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 12/6/2017
 * Time: 8:30 PM
 *
 * Query Parameters:
 *  coin    - Text / Array of text
 *  market  - Text / Array of text
 *  volumeGT  - integer / decimal
 *  volumeLT  - integer / decimal
 *  valueGT   - integer / decimal
 *  valueLT   - integer / decimal
 *  orderBy - Array of text
 *  orderASC - integer 0/1
 *  startIndex - integer
 *  batchSize - integer
 *
 *  Sample:
 *   http://159.203.122.96/src/api/get/getbtxcoinheader.php?coin=ADA&market=BTC
 *   http://159.203.122.96/src/api/get/getbtxmarkethistory.php?coin[]=EMC2&coin[]=ADA&market=BTC&orderBy[]=btxtimestamp&orderASC=1&batchSize=100
 *   http://159.203.122.96/src/api/get/getbtxmarkethistory.php?coin[]=EMC2&coin[]=ADA&market=BTC&orderBy[]=btxtimestamp&orderASC=1&batchSize=100&volumeGT=14000&valueGT=.00005
 */

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require_once $root . '/vendor/autoload.php';

/* Create connection to PGSQL DB */
$connection = new src\connections\PGSQLConnector();
$btxFinder = new src\CRUD\read\BTXFinder($connection);
$urlParams = parse_str($_SERVER['QUERY_STRING'], $output);

//create a unique statement name for the pg_query
$stmntName = "getbtxmarkethistory_".date('U');

$apiRetObject = \src\APIReturnObject::CreateNewAPIReturnObject(
    false, -1, "", ""
);

if(is_null($output['coin']) || is_null($output['market'])) {
    $apiRetObject->setSuccess(false);
    $apiRetObject->setResponse("Must provide a coin and market to query on.");
} else {
    $testing = $btxFinder->GetMarketHistory(
        $stmntName, "", $output['coin'], $output['market']
        , $output['volumeGT'], $output['volumeLT']
        , $output['valueGT'], $output['valueLT']
        , "", "", ""
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