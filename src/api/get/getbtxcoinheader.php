<?php
/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 12/5/2017
 * Time: 11:05 PM
 *
 * Return JSON structure:
 *  success
 *  errorCode
 *  errorMessage
 *  response
 *      <data>
 *
 * Query Parameters:
 *  coin    - Text / Array of text
 *  market  - Text / Array of text
 *  orderBy - Array of text
 *  orderASC - integer 0/1
 *  startIndex - integer
 *  batchSize - integer
 *
 *  Sample:
 *   http://159.203.122.96/src/api/get/getbtxcoinheader.php?coin=ADA&market=BTC
 *   http://159.203.122.96/src/api/get/getbtxcoinheader.php?coin[]=EMC2&coin[]=ADA&coin[]=DOGE&market[]=BTC&market[]=ETH&orderBy[]=coin&orderBy[]=market&orderASC=1
 */

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require_once $root . '/vendor/autoload.php';

/* Create connection to PGSQL DB */
$connection = new src\connections\PGSQLConnector();
$btxFinder = new src\CRUD\read\BTXFinder($connection);
parse_str($_SERVER['QUERY_STRING'], $output);

//create a unique statement name for the pg_query
$stmntName = "getbtxcoinheader_".date('U');

$apiRetObject = \src\APIReturnObject::CreateNewAPIReturnObject(
    false, -1, "", ""
);

$testing = $btxFinder->GetCoinHeader(
    $stmntName, "", $output['coin'], $output['market']
    , "", "", ""
    , "", "", ""
    , "", "", $output['orderBy'], $output['orderASC']
    , $output['startIndex'], $output['batchSize']
);
if($testing === false) {
    $apiRetObject->setSuccess(true);
    $apiRetObject->setResponse("Coin does not exist in DB");
}
else {
    $apiRetObject->setSuccess(true);
    $apiRetObject->setResponse($testing);
}

//var_dump();

echo $apiRetObject->returnJSONEncodedAPIObject();