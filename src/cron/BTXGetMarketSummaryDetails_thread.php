<?php
/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 11/30/2017
 * Time: 4:33 PM
 *
 * This script will call the Bittrex api and create the values for the insert statement
 *
 * @return string
 *  - (value1, value2, value3....value_x), (...), (...)
 */

/*
    Cannot autoload files when you exec(..) them, so if you
    want to include files here, you'll have to include them manually
 - 12/30/17 Refactor into function
 */
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require_once $root . '/vendor/autoload.php';

function GetMarketSummaryDetailsThread($searchFor, $btcUSDValue, $list, $searchNum) {

    /* Create connection to PGSQL DB */
    $connection = new src\connections\PGSQLConnector();
    /* Create a "Keeper" Object that keeps our data, must pass connection info */
    $btxKeeper = new src\CRUD\create\BTXKeeper($connection);
    $btxFinder = new src\CRUD\read\BTXFinder($connection);
    /* init history list */
    $listOfHistoryObjs = array();
    $historyStmnt = "history_" . $searchNum . "_" . date('Y_m_d_H:i:s');
    /* get the current file and strip the extension */
    $currFile = substr(basename(__FILE__), 0, strpos(basename(__FILE__), "."));
    /* Find the history ref value for this file / type */
    $historyRefValue = $btxFinder->GetRefValuesForHistory(
        $historyStmnt, "", HISTORY_REF_TYPE_CRON_JOB, "", $currFile
    );

    $explodeList = explode(",", $list);

    $currDateTime = new DateTime();
    date_sub($currDateTime, date_interval_create_from_date_string('1 minute'));
    $currDateTimeLow = $currDateTime->format('Y-m-d H:i:00');
    $currDateTimeHigh = $currDateTime->format('Y-m-d H:i:59');

    $listOfObjs = array();
    $retValToEcho = "";
    $dataArr = array();
    foreach($explodeList as $market) {
        /* Get Market data per coin */
        $historyDescription = "";
        $specMarketParams=['market'=>$market];
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
        $coin = substr($market, strlen($searchFor));
        $market = substr($market, 0,strlen($searchFor)-1);
        if($specMarketjson->success && !empty($specMarketjson->result)) {
            $count = 0;
            foreach ($specMarketjson->result as $row) {
                /*
                {
                "Id":21450896,
                "TimeStamp":"2017-11-30T23:06:50.86",
                "Quantity":31.91489361,
                "Price":0.00004991,
                "Total":0.00159287,
                "FillType":"PARTIAL_FILL",
                "OrderType":"BUY"
                }
                 */

                $convertDate = DateTime::createFromFormat('Y-m-d\TH:i:s+', $row->TimeStamp);
                $dateCompare = $convertDate->format('Y-m-d H:i:s');
                if($dateCompare >= $currDateTimeLow && $dateCompare <= $currDateTimeHigh) {
                    $count += 1;
                    $usdtConversion = 0.00;
                    if($searchFor == "USDT-") {
                        $usdtConversion = $row->Price;
                    } else {
                        $usdtConversion = CalculateUSDValue($btcUSDValue,$row->Price);
                    }

                    $fillType = 1;
                    $orderType = 1;
                    if($row->FillType == "PARTIAL_FILL") {
                        $fillType = 2;
                    }

                    if($row->OrderType == "SELL") {
                        $orderType = 2;
                    }
                    $tempObj = new stdClass();
                    $tempObj->Id = $row->Id;
                    $tempObj->coin = $coin;
                    $tempObj->market = $market;
                    $tempObj->Quantity = $row->Quantity;
                    $tempObj->Price = $row->Price;
                    $tempObj->usdtConversion = $usdtConversion;
                    $tempObj->Total = $row->Total;
                    $tempObj->FillType = $row->FillType;
                    $tempObj->OrderType = $row->OrderType;
                    $tempObj->TimeStamp = $row->TimeStamp;
                    $dataArr[] = $tempObj;

                    $btxMarketHistory = src\BTXMarketHistoryDetails::CreateNewBTXMarketHistoryDetailsForInsert(
                        $row->Id,
                        $coin,
                        $market,
                        number_format($row->Quantity,"9", ".", ""),
                        number_format($row->Price,"9", ".", ""),
                        number_format($usdtConversion, "2", ".", ""),
                        number_format($row->Total,"9", ".", ""),
                        $fillType,
                        $orderType,
                        $convertDate->format('U')
                    );
                    $listOfObjs[] = $btxMarketHistory;
                }
            }
            if($count > 0) {
                $historyDescription = "Insert Market Summary Details_thread - $searchNum - " . $count . " rows";
            } else {
                $historyDescription = "Insert Market Summary Details_thread - $searchNum - " . $count . " rows";
                $historyDescription .= " | Nothing found between: $currDateTimeLow and $currDateTimeHigh | " . substr(json_encode($specMarketjson), 0, 800);
            }

            /* Initialize BTXHistory obj */
            $historyObj = src\BTXHistory::CreateNewBTXHistoryForInsert(
                $coin
                , $market
                , $historyDescription
                , $historyRefValue[0]['id']
                , date('U')
            );
            $listOfHistoryObjs[] = $historyObj;
        } else {
            $historyDescription = "Insert Market Summary Details_thread - $searchNum -  FAIL";
            $historyDescription .= " | " . substr(json_encode($specMarketjson), 0, 800);
            /* Initialize BTXHistory obj */
            $historyObj = src\BTXHistory::CreateNewBTXHistoryForInsert(
                $coin
                , $market
                , $historyDescription
                , $historyRefValue[0]['id']
                , date('U')
            );
            $listOfHistoryObjs[] = $historyObj;
        }
    }

    foreach ($listOfObjs as $item) {
        $retValToEcho .= "(" . $item->createCommaDelimitedValueForInsert() . "),";
    }

    if($searchNum == 0 || $searchNum == 1 || $searchNum == 2) {
        $apiFileDataVerifyName = API_DATA_STORAGE_BASE . API_DATA_GET_MARKET_SUMMARY_DETAILS_DIRECTORY;
        $apiFileDataVerifyName .= date('Y_m_d_H:i:s') . "-" . $searchNum;
        $apiFileDataVerifyName .= ".json";
        $fileHandler = fopen($apiFileDataVerifyName, 'w') or die('Cannot open file:  ' . $apiFileDataVerifyName); //open file for writing
        $fileData = json_encode($dataArr);
        fwrite($fileHandler, $fileData);
    }

    $numOfInserts = sizeof($listOfHistoryObjs);
    $hist_retValToEcho = " File Complete - History wrote $numOfInserts to table (excluding this row)";

    /* Initialize final BTXHistory obj to capture end of file statement */
    $historyObj = src\BTXHistory::CreateNewBTXHistoryForInsert(
        "ALL"
        , "ALL"
        , $hist_retValToEcho
        , $historyRefValue[0]['id']
        , date('U')
    );
    $listOfHistoryObjs[] = $historyObj;
    /* Generate Insert statement - each object will have this method */
    $historyInsertStmnt = $btxKeeper->CreateMultiInsertStatement($listOfHistoryObjs, BTX_TBL_HISTORY);
    $historyRetVal = $btxKeeper->ExecuteInsertStatement($historyInsertStmnt, $numOfInserts, BTX_TBL_HISTORY[0]);

    return $retValToEcho;
}

