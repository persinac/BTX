<?php
/**
 * Created by PhpStorm.
 * User: apersinger
 * Date: 7/20/2017
 * Time: 8:30 AM
 */

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require $root . '/CRUD/classes/History.php';
require $root . '/CRUD/classes/CronTab.php';
require $root . '/CRUD/classes/CronTabCreator.php';

$raw_cronTabs = CronTabCreator::ListCurrentCronJobs();
/* create an array based on new line */
$rows = explode("\n", $raw_cronTabs);

foreach($rows as $row => $data) {
    if(substr($data, 0, 1) != "#" && strlen($data) > 0) {
        echo $data;
        echo "\n";
    }
}