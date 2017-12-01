<?php
/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 11/30/2017
 * Time: 4:33 PM
 */

/* Cannot autoload files when you exec(..) them, so if you
    want to include files here, you'll have to include them manually

This script will call the Bittrex api and create the values for the insert statement
 */

$root = realpath(dirname(__FILE__));
include '/var/www/html/src/BTXMarketHistoryDetails.php';
include '/var/www/html/src/Common/Utilities.php';


$searchFor = $argv[1];
$btcUSDValue = $argv[2];
$explodeList = explode(",", $argv[3]);
$currDateTimeLow = date('Y-m-d H:i:00');
$currDateTimeHigh = date('Y-m-d H:i:59');
//var_dump($explodeList);
$listOfObjs = array();
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
        foreach ($specMarketjson->result as $row) {
            $coin = substr($market, strlen($searchFor));
            $market = substr($market, 0,strlen($searchFor)-1);
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

                if($row->OrderType == "ORDER_TYPE_SELL") {
                    $orderType = 2;
                }

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
    echo "(" . $item->createCommaDelimitedValueForInsert() . "),";
}