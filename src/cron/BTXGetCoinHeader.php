<?php
/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 12/2/2017
 * Time: 11:55 AM
 * Bittrex API Call: /public/getmarketsummaries
 */

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require_once $root . '/vendor/autoload.php';

$options = array();
/* get all market summaries */
$defaults = array(
    CURLOPT_URL => "https://bittrex.com/api/v1.1/public/getmarkets",
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

/* encoded values for all market data */
$encodedJSON = json_decode($result);
$apiFileDataVerifyName = API_DATA_STORAGE_BASE . API_DATA_GET_COIN_HEADER_DIRECTORY;
$apiFileDataVerifyName .= date('Y_m_d_H:i:s');
if($encodedJSON->success) {
    $apiFileDataVerifyName .= "___SUCCESS";
    $listOfHeaders = array();
    $listOfHeadersToUpdate = array();
    /*
     * "MarketCurrency":"LTC",
     * "BaseCurrency":"BTC",
     * "MarketCurrencyLong":"Litecoin",
     * "BaseCurrencyLong":"Bitcoin",
     * "MinTradeSize":0.02879846,
     * "MarketName":"BTC-LTC",
     * "IsActive":true,
     * "Created":"2014-02-13T00:00:00",
     * "Notice":null,
     * "IsSponsored":null,
     * "LogoUrl":"https://bittrexblobstorage.blob.core.windows.net/public/6defbc41-582d-47a6-bb2e-d0fa88663524.png"
     * */

    /* Create connection to PGSQL DB */
    $connection = new src\connections\PGSQLConnector();
    /* Create a "Keeper" Object that keeps our data, must pass connection info */
    $btxKeeper = new src\CRUD\create\BTXKeeper($connection);
    $btxFinder = new src\CRUD\read\BTXFinder($connection);
    foreach ($encodedJSON->result as $row) {
        /* Convert date format to Epoch */
        $date = date('U');
        $datetime = DateTime::createFromFormat('Y-m-d\TH:i:s+', $row->Created);
        $convertBTXToEpoch = $datetime->format('U');
        $isActive = 0;
        if($row->IsActive == true) {
            $isActive = 1;
        }
        /* Create the object */
        $btxCoinHeader = src\BTXCoinHeader::CreateNewBTXCoinHeaderForInsert(
            $row->MarketCurrency
            , $row->BaseCurrency
            , $row->MarketCurrencyLong
            , $row->MinTradeSize
            , 0
            , 0
            , $isActive
            , $convertBTXToEpoch
            , $date
            , $row->LogoUrl
        );
        $stmntName = $row->MarketCurrency . $row->BaseCurrency . $date;
        $testing = $btxFinder->GetCoinHeader($stmntName, "", $row->MarketCurrency, $row->BaseCurrency);
        if($testing === false) {
//            echo "Coin does not exist in DB - Insert</br>";
            /* store the object in a list */
            $listOfHeaders[] = $btxCoinHeader;
        }
        else {
            $coinRow = $testing[0];
            if(
                $btxCoinHeader->getCoinname() == $coinRow['coinname']
                && $btxCoinHeader->getMintradesize()== $coinRow['mintradesize']
                && $btxCoinHeader->getTxfee()== $coinRow['txfee']
                && $btxCoinHeader->getMinconfirmation()== $coinRow['minconfirmation']
                && $btxCoinHeader->getIsactive()== $coinRow['isactive']
            ) {
//                echo "No Update </br>";
            }
            else {
//                echo "Should Update </br>";
                /* store the object in a list */
                $listOfHeadersToUpdate[] = $btxCoinHeader;
            }
        }

    }
    $numOfInserts = sizeof($listOfHeaders);
    $numOfUpdates = sizeof($listOfHeadersToUpdate);
    /* Generate Insert statement - each object will have this method */
    $insertStmnt = $btxKeeper->CreateMultiInsertStatement($listOfHeaders, BTX_TBL_COIN_HEADER);
    if($numOfInserts > 0) {
        $retVal = $btxKeeper->ExecuteInsertStatement($insertStmnt, $numOfInserts, BTX_TBL_COIN_HEADER[0]);
        $retValToEcho = date('Y-m-d H:i:s') . " | " . $retVal . "\n";
    } else if ($numOfUpdates > 0) {
        /* TODO
            Create an update
         */
        $retValToEcho = date('Y-m-d H:i:s') . " | Need to update $numOfUpdates row(s)\n";
    } else {
        $retValToEcho = date('Y-m-d H:i:s') . " | No inserts or updates executed\n";
    }
} else {
    $apiFileDataVerifyName .= "___FAIL";
    $retValToEcho = date('Y-m-d H:i:s') . " | CURL Call to bittrex API: /public/getmarkets failed\n";
}

$apiFileDataVerifyName .= ".json";
$fileHandler = fopen($apiFileDataVerifyName, 'w') or die('Cannot open file:  '.$apiFileDataVerifyName); //open file for writing
$fileData = json_encode($encodedJSON->result) . "\n" . $retValToEcho;
fwrite($fileHandler, $fileData);
echo $retValToEcho;