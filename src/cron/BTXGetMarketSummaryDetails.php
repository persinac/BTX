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
    $usdtRetVal = "";
    $btcListOneRetVal = "";
    $btcListTwoRetVal = "";
    $btcListThreeRetVal = "";
    $implodedUSDTMarketCoins = implode(",", $listOfUSDTMarketCoins);

    /* execute USDT market data script and let the cpu work on the BTC list */
    exec("/usr/bin/php /var/www/html/src/cron/BTXGetMarketSummaryDetails_thread.php USDT- $btcUSDValue $implodedUSDTMarketCoins", $usdtRetVal);
    $splitBTCList = 3;
    $btcListOne = array();
    $btcListTwo = array();
    $btcListThree = array();
    for($i = 0; $i < count($listOfBTCMarketCoins); $i++) {
        if($i % $splitBTCList == 0) {
            $btcListOne[] = $listOfBTCMarketCoins[$i];
        } else if($i % $splitBTCList == 1) {
            $btcListTwo[] = $listOfBTCMarketCoins[$i];
        } else {
            $btcListThree[] = $listOfBTCMarketCoins[$i];
        }
    }
    /* turn the btc split lists in strings */
    $implodedbtcListOne = implode(",", $btcListOne);
    $implodedbtcListTwo = implode(",", $btcListTwo);
    $implodedbtcListThree = implode(",", $btcListThree);

    /* Run the split lists asynchronously
     * exec is read as follows:
     * /usr/bin/php - Path to PHP to run the script
     * /var/www/html/src/cron/BTXGetMarketSummaryDetails_thread.php - relative path to the script
     * BTC- > Search parameter
     * $btcUSDValue > the current dollar value of BTC so that we don't have to query for it again
     * $implodedbtcList___ > the list to parse and gather data from
     */
    exec("/usr/bin/php /var/www/html/src/cron/BTXGetMarketSummaryDetails_thread.php BTC- $btcUSDValue $implodedbtcListOne 1", $btcListOneRetVal);
    exec("/usr/bin/php /var/www/html/src/cron/BTXGetMarketSummaryDetails_thread.php BTC- $btcUSDValue $implodedbtcListTwo 2", $btcListTwoRetVal);
    exec("/usr/bin/php /var/www/html/src/cron/BTXGetMarketSummaryDetails_thread.php BTC- $btcUSDValue $implodedbtcListThree 3", $btcListThreeRetVal);
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
    echo date('Y-m-d H:i:s') . " | " . $retVal . "\n";
} else {
    echo date('Y-m-d H:i:s') . " | CURL Call to bittrex API: public/getmarketsummary failed\n";
}
