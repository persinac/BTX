<?php
/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 12/17/2017
 * Time: 6:53 PM
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
    $interval = 1;
    if(!empty($output['interval'])) {
        $interval = $output['interval'];
    }

    $limit = 100;
    if($interval == 1) {
        $limit = $output['limit'];
    } else if($interval == 2) {
        $limit = 200;
    } else if($interval == 5) {
        $limit = 500;
    } else if($interval == 15) {
        $limit = 1500;
    } else if($interval == 30) {
        $limit = 3000;
    } else if($interval == 60) {
        $limit = 6000;
    } else if(!empty($output['limit'])) {
        $limit = $output['limit'];
    }
    $timePeriod = 14;
    if(!empty($output['timePeriod'])) {
        $timePeriod = $output['timePeriod'];
    }
    $output = "";
    exec("sh /var/www/html/runGetCalculatedStochastic.sh $market $coin $limit $interval $timePeriod 2>&1", $output);
    //echo gettype($output);
    foreach($output as $var) {
        $json_decode = json_decode($var);
        $cleanStr = str_replace("'","\"", $var);
        echo $cleanStr;
    }
}

