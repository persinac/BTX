<?php
/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 12/25/2017
 * Time: 10:03 PM
 */

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require_once $root . '/vendor/autoload.php';

parse_str($_SERVER['QUERY_STRING'], $output);

$httpQueryData = array(
    'market' => $output['market'] . "-" . $output['coin']
);

$options = array();
/* get all market summaries */
$defaults = array(
    CURLOPT_URL => "https://bittrex.com/api/v1.1/public/getmarkethistory",
    CURLOPT_HEADER => 0,
    CURLOPT_FRESH_CONNECT => 1,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_POSTFIELDS => http_build_query($httpQueryData)
);

$ch = curl_init();
// set URL and other appropriate options
curl_setopt_array($ch, ($options + $defaults));
// grab URL and pass it to the browser
if( ! $result = curl_exec($ch))
{
    trigger_error(curl_error($ch));
}
curl_close($ch);

$encodedJSON = json_decode($result);

$apiRetObject = \src\APIReturnObject::CreateNewAPIReturnObject(
    false, -1, "", ""
);

/* Create connection to PGSQL DB */
$connection = new src\connections\PGSQLConnector();
$btxFinder = new src\CRUD\read\BTXFinder($connection);
$stmntName = "client_getbtxcoinheader_".date('U');
$stmntName2 = "client_getbtxmarkethistory_".date('U');

if($encodedJSON->success) {
    $row = $encodedJSON->result[0];
    $testing = $btxFinder->GetCoinHeader(
        $stmntName, "", $output['coin'], $output['market']
        , "", "", ""
        , "", 1, ""
        , "", "", array("id"), ""
        , 0, 1
    );
    $apiResponse = array($row);
    if($testing === false) {
        $apiRetObject->setSuccess(true);
        $apiResponse[] = "Coin does not exist in DB";
    }
    else {
        $apiRetObject->setSuccess(true);
        $apiResponse[] = $testing[0];
    }
    $testing2 = $btxFinder->GetMarketHistory(
        $stmntName2, "", $output['coin'], $output['market']
        , "", ""
        , "", ""
        , "", "", ""
        , array("id"), ""
        , 0, 1
    );
    if($testing2 === false) {
        $apiRetObject->setSuccess(true);
        $apiResponse[] = "Coin does not exist in DB";
    }
    else {
        $apiRetObject->setSuccess(true);
        $apiResponse[] = $testing2[0];
    }
    $apiRetObject->setResponse($apiResponse);
    //var_dump();

    echo $apiRetObject->returnJSONEncodedAPIObject();
}

//var_dump($btxCoinHeader);
//echo http_build_query($httpQueryData);