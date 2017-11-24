<?php
/**
 * Created by PhpStorm.
 * User: apersinger
 * Date: 7/19/2017
 * Time: 9:16 PM
 */

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
//require $root . '/External Libraries/PHPExcel-1.8/Classes/PHPExcel.php';
//require 'mailer.php';
require $root . '/mongo/CRUD/classes/MongoUtility.php';
require $root . '/CRUD/general/dateUtilities.php';
require $root . '/CRUD/classes/History.php';
require $root . '/mongo/CRUD/classes/History_Mongo.php';
require_once $root . '/CRUD/classes/HTMLBuilder.php';

$history = new History_Mongo();

$historyDisplay = $history->GetAllHistory();
$updatedDisplay = ConvertEventID($historyDisplay);
$orderedList = OrderListAscending($updatedDisplay);
$headers = ["Date", "Event", "Value", "Actor"];
$headerOrder = ["createdOn", "event", "value", "actor"];
$dataToParse = array();
$dataToParse[] = [ "headers" => $headers];
$dataToParse[] = [ "data" => $orderedList];
$html = new HTMLBuilder($dataToParse);
$html->BuildTable($headerOrder);
echo $html->GetHTML();

function ConvertEventID($list) {
    $newArr = [];
    foreach($list as $var) {
        $eid = $var->event;
        if($eid == 1) {
            $var->event = "Sent Email";
        } else {
            $var->event = "Other";
        }
        $newArr[] = $var;
    }
    return $newArr;
}

function OrderListByCreatedOnDate($a, $b) {
    $retVal = 0;
    $aVal = date_create_from_format('m/d/Y H:i:s', $a->createdOn);
    $bVal = date_create_from_format('m/d/Y H:i:s', $b->createdOn);
//    var_dump($date);
//    echo "AVAL: " . $aVal->getTimestamp() . " == ";
//    echo "BVAL: " . $bVal->getTimestamp() . "?";
//    echo "</br>";

    if($aVal->getTimestamp() == $bVal->getTimestamp()){
        $retVal = 0 ;
    } else if($aVal->getTimestamp() > $bVal->getTimestamp()) {
        $retVal = -1;
    } else {
        $retVal = 1;
    }
    return $retVal;
}

function OrderListAscending($list) {
    usort($list, 'OrderListByCreatedOnDate');
    return $list;

}

