<?php
/**
 * API Call: /public/getmarkethistory
 * Per coin
 *
 * Created by PhpStorm.
 * User: apfba
 * Date: 11/26/2017
 * Time: 3:13 PM
 */

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require_once $root . '/vendor/autoload.php';

$options = array();
/* get all market summary details */
$defaults = array(
    CURLOPT_URL => "https://bittrex.com/api/v1.1/public/getmarketsummaries",
    CURLOPT_HEADER => 0,
    CURLOPT_FRESH_CONNECT => 1,
    CURLOPT_RETURNTRANSFER => 1
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

/* Get USDT-BTC data for dollar value conversion */
$btcUSDTParams=['market'=>'usdt-btc'];
$btcUSDTOptions = array();
$btcUSDTDefaults = array(
    CURLOPT_URL => "https://bittrex.com/api/v1.1/public/getmarketsummary",
    CURLOPT_HEADER => 0,
    CURLOPT_FRESH_CONNECT => 1,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => http_build_query($btcUSDTParams)
);

$btcUSDTCH = curl_init();
curl_setopt_array($btcUSDTCH, ($btcUSDTOptions + $btcUSDTDefaults));
if( ! $btcUSDTResult = curl_exec($btcUSDTCH))
{
    trigger_error(curl_error($btcUSDTCH));
}
curl_close($btcUSDTCH);

$btcUSDTjson = json_decode($btcUSDTResult);
$encodedJSON = json_decode($result);

// begin the insert statement - this will eventually be in its own class
$beginInsert = "INSERT INTO ".BTX_TBL_COIN_MARKET_HISTORY_DETAILS[0]." " . BTX_TBL_COIN_MARKET_HISTORY_DETAILS[1] . " VALUES ";
$valuesInsert = "";
$btcUSDValue = 0.00;

$numOfInserts = count($encodedJSON->result);
$currDateTimeLow = date('Y-m-d H:i:00');
$currDateTimeHigh = date('Y-m-d H:i:59');
if($btcUSDTjson->success) {
    $btcUSDValue = $btcUSDTjson->result[0]->Last;
}

$listOfBTCMarketCoins = array();
$listOfUSDTMarketCoins = array();

if($encodedJSON->success) {
    $searchParty = array('BTC-', 'USDT-');
    foreach ($searchParty as $searchFor) {
        /*
         * "MarketName":"BTC-1ST",
         * "High":0.00004796,
         * "Low":0.00004427,
         * "Volume":897254.45583711,
         * "Last":0.00004547,
         * "BaseVolume":41.06630843,
         * "TimeStamp":"2017-11-25T20:57:23.85",
         * "Bid":0.00004546,
         * "Ask":0.00004590,
         * "OpenBuyOrders":247,
         * "OpenSellOrders":2883,
         * "PrevDay":0.00004497,
         * "Created":"2017-06-06T01:22:35.727"
         *
         * */
        foreach ($encodedJSON->result as $row) {
            $pos = strpos($row->MarketName, $searchFor);
            if($pos === false) {}
            else {
                /* build a list of coins based on searchFor */
                if($searchFor == "USDT-") {
                    $listOfUSDTMarketCoins[] = $row->MarketName;
                } else {
                    $listOfBTCMarketCoins[] = $row->MarketName;
                }
            }
        }

    }

    /* execute USDT market data script */
    $usdtRetVal = "";
    $btcListOneRetVal = "";
    $btcListTwoRetVal = "";
    $btcListThreeRetVal = "";
    $implodedUSDTMarketCoins = implode(",", $listOfUSDTMarketCoins);
    exec("/usr/bin/php /var/www/html/src/cron/BTXGetMarketSummaryDetails_thread.php USDT- $btcUSDValue $implodedUSDTMarketCoins", $usdtRetVal);
    $triSplit = 3;
    $btcListOne = array();
    $btcListTwo = array();
    $btcListThree = array();
    for($i = 0; $i < count($listOfBTCMarketCoins); $i++) {
        if($i % $triSplit == 0) {
            $btcListOne[] = $listOfBTCMarketCoins[$i];
        } else if($i % $triSplit == 1) {
            $btcListTwo[] = $listOfBTCMarketCoins[$i];
        } else {
            $btcListThree[] = $listOfBTCMarketCoins[$i];
        }
    }
    $implodedbtcListOne = implode(",", $btcListOne);
    $implodedbtcListTwo = implode(",", $btcListTwo);
    $implodedbtcListThree = implode(",", $btcListThree);
    exec("/usr/bin/php /var/www/html/src/cron/BTXGetMarketSummaryDetails_thread.php USDT- $btcUSDValue $implodedbtcListOne", $btcListOneRetVal);
    exec("/usr/bin/php /var/www/html/src/cron/BTXGetMarketSummaryDetails_thread.php USDT- $btcUSDValue $implodedbtcListTwo", $btcListTwoRetVal);
    exec("/usr/bin/php /var/www/html/src/cron/BTXGetMarketSummaryDetails_thread.php USDT- $btcUSDValue $implodedbtcListThree", $btcListThreeRetVal);
//    echo $usdtRetVal[0];
//    echo $btcListOneRetVal[0];
//    echo $btcListTwoRetVal[0];
//    echo $btcListThreeRetVal[0];
    $valuesInsert = $usdtRetVal[0];
    $valuesInsert .= $btcListOneRetVal[0];
    $valuesInsert .= $btcListTwoRetVal[0];
    $valuesInsert .= $btcListThreeRetVal[0];
    $valuesInsert = substr($valuesInsert, 0, strlen($valuesInsert)-1);
    $numOfInserts = substr_count($valuesInsert,"),");
    $insertStmnt = $beginInsert . $valuesInsert;
    /* Create connection to PGSQL DB */
    $connection = new src\connections\PGSQLConnector();
    /* Create a "Keeper" Object that keeps our data, must pass connection info */
    $btxKeeper = new src\CRUD\create\BTXKeeper($connection);
    /* Run the insert statement */
    $retVal = $btxKeeper->ExecuteInsertStatement($insertStmnt, $numOfInserts, BTX_TBL_COIN_MARKET_HISTORY_DETAILS[0]);
    echo $retVal;
} else {
    echo "world";
}

//$insertStmnt = $beginInsert . substr($valuesInsert, 0, strlen($valuesInsert)-1);
//
//$connection = new src\connections\PGSQLConnector();
//$btxKeeper = new src\CRUD\create\BTXKeeper($connection);
//
//$retval = $btxKeeper->ExecuteInsertStatement($insertStmnt, $numOfInserts, BTX_TBL_MARKET_HISTORY);
//var_dump($retval);