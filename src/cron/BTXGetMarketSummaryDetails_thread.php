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
 */
$root = realpath(dirname(__FILE__));
include '/var/www/html/src/BTXMarketHistoryDetails.php';
include '/var/www/html/src/Common/Utilities.php';
include '/var/www/html/src/constants.php';


$searchFor = $argv[1];
$btcUSDValue = $argv[2];
$explodeList = explode(",", $argv[3]);
$searchNum = $argv[4];
$currDateTimeLow = date('Y-m-d H:i:00');
$currDateTimeHigh = date('Y-m-d H:i:59');
$listOfObjs = array();
$retValToEcho = "";
$dataArr = array();

foreach($explodeList as $market) {
    /* Get Market data per coin */
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
    if($specMarketjson->success) {
        $coin = substr($market, strlen($searchFor));
        $market = substr($market, 0,strlen($searchFor)-1);
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
                /* Create the object */
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
    }
}
foreach ($listOfObjs as $item) {
    $retValToEcho .= "(" . $item->createCommaDelimitedValueForInsert() . "),";
}
$apiFileDataVerifyName = API_DATA_STORAGE_BASE . API_DATA_GET_MARKET_SUMMARY_DETAILS_DIRECTORY;
$apiFileDataVerifyName .= date('Y_m_d_H:i:s') . "-". $searchNum;
$apiFileDataVerifyName .= ".json";
$fileHandler = fopen($apiFileDataVerifyName, 'w') or die('Cannot open file:  '.$apiFileDataVerifyName); //open file for writing
$fileData = json_encode($dataArr);
fwrite($fileHandler, $fileData);

echo $retValToEcho;