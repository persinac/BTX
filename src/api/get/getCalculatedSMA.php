<?php
/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 12/13/2017
 * Time: 9:21 AM
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
    $timePeriod = 14;
    if(!empty($output['timePeriod'])) {
        $timePeriod = $output['timePeriod'];
    }
    $output = "";
    exec("sh /var/www/html/runGetCalculatedSMA.sh $market $coin $limit $interval $timePeriod 2>&1", $output);
    //echo gettype($output);
    foreach($output as $var) {
        $json_decode = json_decode($var);
        echo $var;
    }
}

