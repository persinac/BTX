<?php
/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 12/13/2017
 * Time: 9:21 AM
 */

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require_once $root . '/vendor/autoload.php';

$output = "";
exec('sh /var/www/html/runGetCalculatedSMA.sh 2>&1', $output);
//echo gettype($output);
foreach($output as $var) {
    $json_decode = json_decode($var);
    echo $var;
}