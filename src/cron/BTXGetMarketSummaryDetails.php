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
$startTime = date('Y-m-d H:i:s');
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require_once $root . '/vendor/autoload.php';

parse_str($_SERVER['QUERY_STRING'], $output);
$searchInterval = $output['searchFor'];
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

/*
 * Need to call https://bittrex.com/api/v1.1/public/getmarkets
 * to get the isActive status...
 * If coin is active, continue,
 * Else: write to history table why it's inactive. Should have a
 * "notice" field in the ret val of curl.
 *
 * */


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


/* Create connection to PGSQL DB */
$connection = new src\connections\PGSQLConnector();
/* Create a "Keeper" Object that keeps our data, must pass connection info */
$btxKeeper = new src\CRUD\create\BTXKeeper($connection);
$btxFinder = new src\CRUD\read\BTXFinder($connection);
/* init history list */
$listOfHistoryObjs = array();
$historyStmnt = "history_" . date('Y_m_d_H:i:s');
/* get the current file and strip the extension */
$currFile = substr(basename(__FILE__), 0, strpos(basename(__FILE__), "."));
/* Find the history ref value for this file / type */
$historyRefValue = $btxFinder->GetRefValuesForHistory(
    $historyStmnt, "", HISTORY_REF_TYPE_CRON_JOB, "", $currFile
);



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
    $btcListRetVal = "";
    $implodedUSDTMarketCoins = implode(",", $listOfUSDTMarketCoins);
    $valuesInsert = "";
    /* execute USDT market data script and let the cpu work on the BTC list */
    if($searchInterval < 0) {
        $usdtRetVal = GetMarketSummaryDetailsThread(
            "USDT-"
            , $btcUSDValue
            , $implodedUSDTMarketCoins
            , "USDT"
        );
//        exec("/usr/bin/php /var/www/html/src/cron/BTXGetMarketSummaryDetails_thread.php USDT- $btcUSDValue $implodedUSDTMarketCoins USDT", $usdtRetVal);
        $valuesInsert .= $usdtRetVal[0];
        $historyDescription = "Insert Market Summary Details: USDT Interval: " . $searchInterval;
    } else {
        $splitMod = 3;
        $btcList = array();
        for ($i = 0; $i < count($listOfBTCMarketCoins); $i++) {
            if ($i % $splitMod == $searchInterval) {
                $btcList[] = $listOfBTCMarketCoins[$i];
            }
        }
        /* turn the btc split lists in strings */
        $implodedbtcList = implode(",", $btcList);
        /* Run the split lists asynchronously
         * exec is read as follows:
         * /usr/bin/php - Path to PHP to run the script
         * /var/www/html/src/cron/BTXGetMarketSummaryDetails_thread.php - relative path to the script
         * BTC- > Search parameter
         * $btcUSDValue > the current dollar value of BTC so that we don't have to query for it again
         * $implodedbtcList___ > the list to parse and gather data from
         */
        $btcListRetVal = GetMarketSummaryDetailsThread(
            "BTC-"
            , $btcUSDValue
            , $implodedbtcList
            , $searchInterval
        );

//        exec("/usr/bin/php /var/www/html/src/cron/BTXGetMarketSummaryDetails_thread.php BTC- $btcUSDValue $implodedbtcList $searchInterval", $btcListRetVal);
        $valuesInsert .= $btcListRetVal[0];
        $historyDescription = "Insert Market Summary Details: BTC Interval: " . $searchInterval;
    }

    $valuesInsert = substr($valuesInsert, 0, strlen($valuesInsert)-1);
    $numOfInserts = substr_count($valuesInsert,"),");

    $market = "BTC";
    if($searchInterval < 0) {
        $market = "USDT";
    }

    /* Initialize BTXHistory obj */
    $historyObj = src\BTXHistory::CreateNewBTXHistoryForInsert(
        "SearchInterval-".$searchInterval
        , $market
        , $historyDescription
        , $historyRefValue[0]['id']
        , date('U')
    );
    $listOfHistoryObjs[] = $historyObj;

    $insertStmnt = $beginInsert . $valuesInsert;
    if(strlen($valuesInsert) > 5) {
        /* Run the insert statement */
        $retVal = $btxKeeper->ExecuteInsertStatement($insertStmnt, $numOfInserts, BTX_TBL_COIN_MARKET_HISTORY_DETAILS[0]);
    } else {
        $retVal = "Inserted 0 rows into btxcoinmarkethistorydetails";
    }
    $endTime = date('Y-m-d H:i:s');
    $dteStart = new DateTime($startTime);
    $dteEnd   = new DateTime($endTime);
    $dteDiff  = $dteStart->diff($dteEnd);
    $retValToEcho = "Start-End Time: $startTime - " . date('Y-m-d H:i:s') . " | Time (seconds): " . $dteDiff->format("%S") . " | " . $retVal;
} else {
    $retValToEcho =  "Start-End Time: $startTime - " . date('Y-m-d H:i:s') . " | Time (seconds): " . $dteDiff->format("%S") . " | CURL Call to bittrex API: public/getmarketsummary failed";
}

$numOfInserts = sizeof($listOfHistoryObjs);
$retValToEcho .= " | File Complete - History wrote $numOfInserts to table (excluding this row)";

/* Initialize final BTXHistory obj to capture end of file statement */
$historyObj = src\BTXHistory::CreateNewBTXHistoryForInsert(
    "ALL"
    , "ALL"
    , $retValToEcho
    , $historyRefValue[0]['id']
    , date('U')
);
$listOfHistoryObjs[] = $historyObj;
/* Generate Insert statement - each object will have this method */
$historyInsertStmnt = $btxKeeper->CreateMultiInsertStatement($listOfHistoryObjs, BTX_TBL_HISTORY);
//var_dump($historyInsertStmnt);
$historyRetVal = $btxKeeper->ExecuteInsertStatement($historyInsertStmnt, $numOfInserts, BTX_TBL_HISTORY[0]);
