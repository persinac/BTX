<?php
/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 11/25/2017
 * Time: 3:58 PM
 */

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require_once $root . '/vendor/autoload.php';

$options = array();
/* get all market summaries */
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
// set URL and other appropriate options
curl_setopt_array($btcUSDTCH, ($btcUSDTOptions + $btcUSDTDefaults));
// grab URL and pass it to the browser
if( ! $btcUSDTResult = curl_exec($btcUSDTCH))
{
    trigger_error(curl_error($btcUSDTCH));
}
curl_close($btcUSDTCH);

/* encoded values for USDT-BTC data */
$btcUSDTjson = json_decode($btcUSDTResult);

/* encoded values for all market data */
$encodedJSON = json_decode($result);

/* set default btcUSDT value */
$btcUSDValue = 0.00;

$numOfInserts = count($encodedJSON->result);

if($btcUSDTjson->success) {
    $btcUSDValue = $btcUSDTjson->result[0]->Last;
}
if($encodedJSON->success) {
    $searchParty = array('BTC-', 'USDT-');
    $listOfMarketHistory = array();
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
                $coin = substr($row->MarketName, strlen($searchFor));
                $market = substr($row->MarketName, 0,strlen($searchFor)-1);
                /* Convert date format to Epoch */
                $date = date('U');
                $datetime = DateTime::createFromFormat('Y-m-d\TH:i:s+', $row->TimeStamp);
                $convertBTXToEpoch = $datetime->format('U');

                /* Create the object */
                $btxMarketHistory = new src\BTXMarketHistory(
                    $coin, $market, $row->BaseVolume, number_format($row->Last, "9", ".", "")
                    , number_format($usdtConversion, "2", ".", "")
                    , number_format($row->High, "9", ".", "")
                    , number_format($row->Low, "9", ".", "")
                    , number_format($row->Last, "9", ".", "")
                    , number_format($row->Bid, "9", ".", "")
                    , $row->OpenBuyOrders, $row->OpenSellOrders, $convertBTXToEpoch, $date
                );
                /* store the object in a list */
                $listOfMarketHistory[] = $btxMarketHistory;
            }
        }
    }
    /* Create connection to PGSQL DB */
    $connection = new src\connections\PGSQLConnector();
    /* Create a "Keeper" Object that keeps our data, must pass connection info */
    $btxKeeper = new src\CRUD\create\BTXKeeper($connection);

    /* Generate Insert statement - each object will have this method */
    $insertStmnt = $btxKeeper->CreateMultiInsertStatement($listOfMarketHistory, BTX_TBL_MARKET_HISTORY);
    /* Run the insert statement */
    $retVal = $btxKeeper->ExecuteInsertStatement($insertStmnt, $numOfInserts, BTX_TBL_MARKET_HISTORY[0]);
    echo $retVal;
} else {
    echo 'CURL Call to bittrex API failed';
}
