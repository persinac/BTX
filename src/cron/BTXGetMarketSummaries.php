<?php
/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 11/25/2017
 * Time: 3:58 PM
 */

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require_once $root . '/vendor/autoload.php';

$params=['name'=>'John', 'surname'=>'Doe', 'age'=>36];
$options = array();
$defaults = array(
    CURLOPT_URL => "https://bittrex.com/api/v1.1/public/getmarketsummaries",
    CURLOPT_HEADER => 0,
    CURLOPT_FRESH_CONNECT => 1,
    CURLOPT_RETURNTRANSFER => 1
//    CURLOPT_POST => 1,
//    CURLOPT_POSTFIELDS => http_build_query($params),
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
$encodedJSON = json_decode($result);
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
                $coin = substr($row->MarketName, strlen($searchFor));
                $market = substr($row->MarketName, 0,strlen($searchFor)-1);
                $date = date("Y-m-d H:i:s");

                //get rid of the timezone character
                $datetime = DateTime::createFromFormat('Y-m-d\TH:i:s+', $row->TimeStamp);
                echo "Coin: $coin | Market: $market | ". $datetime->format('Y-m-d H:i:s') . "</br>";
            }
        }
    }
} else {
    echo "world";
}

// close cURL resource, and free up system resources
//