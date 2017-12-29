<?php
/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 12/18/2017
 * Time: 10:52 AM
 */

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require_once $root . '/vendor/autoload.php';

/* Create connection to PGSQL DB */
$connection = new src\connections\PGSQLConnector();
$btxFinder = new src\CRUD\read\BTXFinder($connection);

//create a unique statement name for the pg_query
$stmntName = "getbtxcoinheader_".date('U');

$orderBy = array("coin");

$btcCoins = $btxFinder->GetCoinHeaderForDropdown(
    $stmntName, "", "", "BTC"
    , "", "", ""
    , "", "", ""
    , "", "", $orderBy, "1"
    , "", ""
);

$usdtCoins = $btxFinder->GetCoinHeaderForDropdown(
    $stmntName, "", "", "USDT"
    , "", "", ""
    , "", "", ""
    , "", "", $orderBy, "1"
    , "", ""
);
//var_dump($usdtCoins);



$marketDropdown = "";
$marketDropdown .= "<select>";
$marketDropdown .= '<option value="BTC">BTC</option>';
$marketDropdown .= '<option value="USDT">USDT</option>';
$marketDropdown .= "</select>";

$finalJsonObj = new stdClass();
$finalJsonObj->markets = $marketDropdown;
$finalJsonObj->coinMarkets = array();

$usdtCoinsClass = new stdClass();
$usdtCoinsClass->market = "USDT";
$usdtCoinsClass->coins = "<select>";
foreach ($usdtCoins  as $coin) {
    $currCoin = $coin['coin'];
    $usdtCoinsClass->coins .= '<option value="'.$currCoin.'">'.$currCoin.'</option>';
}
$usdtCoinsClass->coins .= "</select>";

$btcCoinsClass = new stdClass();
$btcCoinsClass->market = "BTC";
$btcCoinsClass->coins = "<select>";
foreach ($btcCoins  as $coin) {
    $currCoin = $coin['coin'];
    $btcCoinsClass->coins .= '<option value="'.$currCoin.'">'.$currCoin.'</option>';
}
$btcCoinsClass->coins .= "</select>";

$finalJsonObj->coinMarkets[] = $usdtCoinsClass;
$finalJsonObj->coinMarkets[] = $btcCoinsClass;

echo json_encode($finalJsonObj);