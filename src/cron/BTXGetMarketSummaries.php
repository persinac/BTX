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

$btcUSDTjson = json_decode($btcUSDTResult);
$encodedJSON = json_decode($result);

// begin the insert statement - this will eventually be in its own class
$beginInsert = "INSERT INTO ".BTX_TBL_MARKET_HISTORY."
 (coin,market,volume,\"value\",\"usdValue\",high,low,\"lastSell\",\"currentBid\",\"openBuyOrders\",\"openSellOrders\",btxtimestamp,\"timestamp\") VALUES ";
$valuesInsert = "";
$btcUSDValue = 0.00;

$numOfInserts = count($encodedJSON->result);

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
                $coin = substr($row->MarketName, strlen($searchFor));
                $market = substr($row->MarketName, 0,strlen($searchFor)-1);
                $date = date('U');
                $datetime = DateTime::createFromFormat('Y-m-d\TH:i:s+', $row->TimeStamp);
                $convertBTXToEpoch = $datetime->format('U');
                $valuesInsert .= "(";
                $valuesInsert .= "'$coin'";
                $valuesInsert .= ",'$market'";
                $valuesInsert .= ",".$row->BaseVolume."";
                $valuesInsert .= "," .number_format($row->Last, "9", ".", "") . "";
                $valuesInsert .= ",". number_format($usdtConversion, "2", ".", "") ."";
                $valuesInsert .= ",".number_format($row->High, "9", ".", "")."";
                $valuesInsert .= ",".number_format($row->Low, "9", ".", "")."";
                $valuesInsert .= ",".number_format($row->Last, "9", ".", "")."";
                $valuesInsert .= ",".number_format($row->Bid, "9", ".", "")."";
                $valuesInsert .= ",".$row->OpenBuyOrders."";
                $valuesInsert .= ",".$row->OpenSellOrders."";
                $valuesInsert .= ",".$convertBTXToEpoch."";
                $valuesInsert .= ",".$date."";
                $valuesInsert .= "),";
            }
        }
    }
} else {
    echo "world";
}
$insertStmnt = $beginInsert . substr($valuesInsert, 0, strlen($valuesInsert)-1);

$connection = new src\connections\PGSQLConnector();
$btxKeeper = new src\CRUD\create\BTXKeeper($connection);

$retval = $btxKeeper->ExecuteInsertStatement($insertStmnt, $numOfInserts, BTX_TBL_MARKET_HISTORY);
var_dump($retval);