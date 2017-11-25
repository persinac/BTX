<?php
/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 11/24/2017
 * Time: 7:45 PM
 */

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require_once $root . '/vendor/autoload.php';

$retval = sprintf('host=%s dbname=%s user=%s password=%s',
    BTX_DB_HOST, BTX_DB_NAME, BTX_DB_USERNAME, BTX_DB_PASSWORD);

$connection = new src\connections\PGSQLConnector();
$btxFinder = new src\CRUD\read\BTXFinder($connection);

$retval = $btxFinder->GetCoinsToWatch();
//$retval = BTX_DB_HOST . ' ' . BTX_DB_NAME;
var_dump($retval);