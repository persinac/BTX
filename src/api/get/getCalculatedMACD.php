<?php
/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 12/17/2017
 * Time: 6:54 PM
 */

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require_once $root . '/vendor/autoload.php';

$urlParams = parse_str($_SERVER['QUERY_STRING'], $output);

if(is_null($output['coin']) || is_null($output['market'])) {
    $apiRetObject->setSuccess(false);
    $apiRetObject->setResponse("Must provide a coin and market to query on.");
} else {


    $market = $output['market'];
    $coin = $output['coin'];
    $limit = 100;
    if(!empty($output['limit'])) {
        $limit = $output['limit'];
    }
    $interval = 1;
    if(!empty($output['interval'])) {
        $interval = $output['interval'];
    }
    $fastPeriod = 12;
    if(!empty($output['fastPeriod'])) {
        $timePeriod = $output['fastPeriod'];
    }
    $slowPeriod = 26;
    if(!empty($output['slowPeriod'])) {
        $timePeriod = $output['slowPeriod'];
    }
    $signalPeriod = 9;
    if(!empty($output['signalPeriod'])) {
        $timePeriod = $output['signalPeriod'];
    }
    $output = "";
    exec("sh /var/www/html/runGetCalculatedMACD.sh $market $coin $limit $interval $fastPeriod $slowPeriod $signalPeriod 2>&1", $output);
    //echo gettype($output);
    foreach($output as $var) {
        $json_decode = json_decode($var);
        $cleanStr = str_replace("'","\"", $var);
        echo $cleanStr;
    }
}

