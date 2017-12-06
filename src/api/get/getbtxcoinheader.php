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
 *  Sample:
 *   http://159.203.122.96/src/api/get/getbtxcoinheader.php?coin=ADA&market=BTC
 */

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require_once $root . '/vendor/autoload.php';

/* Create connection to PGSQL DB */
$connection = new src\connections\PGSQLConnector();
$btxFinder = new src\CRUD\read\BTXFinder($connection);
$urlParams = parse_str($_SERVER['QUERY_STRING'], $output);

//create a unique statement name for the pg_query
$stmntName = "getbtxcoinheader_".date('U');

$apiRetObject = \src\APIReturnObject::CreateNewAPIReturnObject(
    false, -1, "", ""
);

$testing = $btxFinder->GetCoinHeader($stmntName, "", $output['coin'], $output['market']);
if($testing === false) {
    $apiRetObject->setSuccess(true);
    $apiRetObject->setResponse("Coin does not exist in DB");
}
else {
    $apiRetObject->setSuccess(true);
    $apiRetObject->setResponse($testing[0]);
}

//var_dump();

echo $apiRetObject->returnJSONEncodedAPIObject();