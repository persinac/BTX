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
$beginInsert = "INSERT INTO ".BTX_TBL_COIN_MARKET_HISTORY_DETAILS."
 (btxid,coin,market,quantity,\"value\",total,filltype,ordertype,btxtimestamp) VALUES ";
$valuesInsert = "";
$btcUSDValue = 0.00;

$numOfInserts = count($encodedJSON->result);
$currDateTimeLow = date('Y-m-d H:i:00');
$currDateTimeHigh = date('Y-m-d H:i:59');
if($btcUSDTjson->success) {
    $btcUSDValue = $btcUSDTjson->result[0]->Last;
}
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
                $usdtConversion = 0.00;
                if($searchFor == "USDT-") {
                    $usdtConversion = $row->Last;
                } else {
                    $usdtConversion = CalculateUSDValue($btcUSDValue,$row->Last);
                }

                $datetime = DateTime::createFromFormat('Y-m-d\TH:i:s+', $row->TimeStamp);
                /* Get Market data */
                $specMarketParams=['market'=>$row->MarketName];
                $specMarketOptions = array();
                $specMarketDefaults = array(
                    CURLOPT_URL => "https://bittrex.com/api/v1.1/public/getmarkethistory",
                    CURLOPT_HEADER => 0,
                    CURLOPT_FRESH_CONNECT => 1,
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_POST => 1,
                    CURLOPT_POSTFIELDS => http_build_query($specMarketParams)
                );

                $specMarketch = curl_init();
                curl_setopt_array($specMarketch, ($specMarketOptions + $specMarketDefaults));
                if( ! $specMarketResult = curl_exec($specMarketch))
                {
                    trigger_error(curl_error($specMarketch));
                }
                curl_close($specMarketch);
                $specMarketjson = json_decode($specMarketResult);
                if($datetime > $currDateTimeLow && $datetime <= $currDateTimeHigh) {
                    echo $datetime . " is between: " . $currDateTimeLow . " and " . $currDateTimeHigh . "</br>";
                } else {
                    echo $datetime->format('') . " is NOT between: " . $currDateTimeLow . " and " . $currDateTimeHigh . "</br>";
                }
            }
        }

    }
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