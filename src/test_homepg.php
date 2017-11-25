<?php
/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 11/24/2017
 * Time: 7:45 PM
 */

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require_once $root . '/vendor/autoload.php';

$retval = "";
//$mystring = system('python3 ../../python_bittrex_master/myTestScript.py', $retval);
//$mystring = system('../../py_bittrex/new_script_3.sh', $retval);


$btxFinder = new \Read\BTXFinder();

$retval = $btxFinder->GetCoinsToWatch();

var_dump($retval);