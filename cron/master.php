<?php
/**
 * Created by PhpStorm.
 * User: apersinger
 * Date: 5/11/2017
 * Time: 3:45 PM
 */


$root = realpath($_SERVER["DOCUMENT_ROOT"]);
//require $root . '/External Libraries/PHPExcel-1.8/Classes/PHPExcel.php';
//require 'mailer.php';
require $root . '/mongo/CRUD/classes/MongoUtility.php';
require $root . '/CRUD/classes/Employee.php';
//require $root . '/CRUD/classes/MonthData.php';
require $root . '/CRUD/classes/MonthDataEmployeeView.php';
require $root . '/CRUD/general/dateUtilities.php';
require $root . '/CRUD/general/mailer.php';
require $root . '/CRUD/classes/History.php';
require $root . '/mongo/CRUD/classes/History_Mongo.php';

$monthData = GetMongoData();



$historyMongo = new History_Mongo();

$emailData = array();
$listOfMonthDataVW = array();
$today = date('m/d/Y', time());
/*
 * $notifyBlogDueDate Structure: [stdClass]->notifyDate, [stdClass]->blogDueDate
 * */
$notifyBlogDueDate = BuildNotifyAndBlogDueDate($today);
$notifyDate = "";
$blogDueDate = "";

foreach($monthData as $doc) {

    if(!empty($doc->employee->id)) {

        $mdev = new MonthDataEmployeeView(
           (string)$doc->_id,
          $doc->employee->id,
          $doc->employee->email, $notifyBlogDueDate->blogDueDate,
            "",
          "",
          $doc->employee->firstName);
        /*
         * Old Functionality
        $result = GetEmployeeRecord($doc->empId);
          foreach($result as $empRecord) {
              $mdev->SetMonthDataEmployeeId($doc->empId);
              $mdev->SetMonthDataEmployeeEmail($doc->empEmail);
              $mdev->SetMonthDataBlogDate($doc->date);
              $mdev->SetMonthDataBlogStatus($doc->status);
              $mdev->SetMonthDataBlogTopic($doc->topic);
              $mdev->SetEmployeeName($empRecord->firstName);
          }
          if($mdev->GetMonthDataEmployeeId() > -1) {
              $emailData[] = $mdev;
          }*/
        $emailData[] = $mdev;
       }
}
//var_dump($emailData);
$historyArr = [];
foreach($emailData as $sendTo) {
    if(strlen($sendTo->empEmail) > 0) {
        $history = new History("","", "","");
        $history->SetEvent("1");
        $history->SetValue(sendMail($sendTo));
        $history->SetCreatedOn(date("m/d/Y h:i:s"));
        $historyArr[] = $history;
    }
}
/* Insert history in bulk */
$historyMongo->InsertIntoHistory($historyArr);

/**
 * GetMongoData()
 *
 * Returns the list of employees and their respective blog due dates
 *
 * As of June 30th 2017, client wants to return all users for
 * blog notification(s). If allEmployees == 0, return all, else
 * continue with old logic.
 *
 * @return array
 */
function GetMongoData($allEmployees = 0) {
    $lowHigh = DateUtilities::GenerateLowHighDateRange();

    $mongoObj = new MongoUtility();
    $mongoObj->SelectDBToUse("test");

    /*
    default to old logic
    Pull from employee list instead of month data
    */
    $collectionName = DateUtilities::GetCurrentMonth() . DateUtilities::GetCurrentYear();
    if($allEmployees == 0) {
        $collectionName = "Employees";
    }
    $mongoObj->SelectCollection($collectionName);

    /* $gt = greater than */
    $filter = [ /*'date' => ['$gt' => $lowHigh->low]*/ ];
    $options = [];
    $result = $mongoObj->FindSpecific($filter, $options);
    $dataInRange[] = array();
    $lowFormatted = date('m/d/Y',strtotime($lowHigh->low));
    $highFormatted = date('m/d/Y',strtotime($lowHigh->high));
    foreach($result as $var) {

        if(is_object($var)) {
            $monthData = $var;
            if($allEmployees == 0) {
                $dataInRange[] = $monthData;
            } else {
                $time = strtotime($monthData->date);
                $newformat = date('m/d/Y', $time);
                if ($newformat > $lowFormatted) {
                    $newDateGreaterThanLowRange = 1;
                } else {
                    $newDateGreaterThanLowRange = 0;
                }
                if ($highFormatted > $newformat) {
                    $newDateLessThanHighRange = 1;
                } else {
                    $newDateLessThanHighRange = 0;
                }
                if ($newDateGreaterThanLowRange == 1
                    && $newDateLessThanHighRange == 1) {
                    $dataInRange[] = $monthData;
                }
            }
        }
    }
    return $dataInRange;
}

function GetEmployeeRecord($id) {
    $mongoObj = new MongoUtility();
    $mongoObj->SelectDBToUse("test");
    $collectionName = "Employees";
    $mongoObj->SelectCollection($collectionName);

    /* $gt = greater than */
    $filter = ['employee.id' => $id];
    $options = [
        'projection' => ['_id' => 0]
    ];
    $result = $mongoObj->FindSpecific($filter, $options);
//    var_dump($result);
    return $result;
}

function GetServiceAccount($email) {
    $mongoObj = new MongoUtility();
    $mongoObj->SelectDBToUse("test");
    $collectionName = "SERVICE_ACCOUNT";
    $mongoObj->SelectCollection($collectionName);

    /* $gt = greater than */
    $filter = ['email' => $email];
    $options = [
//        'projection' => ['_id' => 0]
    ];
    $result = $mongoObj->FindSpecific($filter, $options);
//    var_dump($result);
    return $result;
}

function BuildNotifyAndBlogDueDate($dateToCompare) {
    $currentMonth = DateUtilities::GenerateCurrentMonth();
    $currentYear = DateUtilities::GenerateCurrentYear();
    $newObj = new stdClass();
    $notifyDate = "";
    $blogDueDate = "";
    /*
     * Eventually make this dynamic to handle non-hardcoded notify dates
     * Need to make user configuration
     * */
    if($dateToCompare <= DateUtilities::BuildSpecificDate($currentMonth, 10, $currentYear)) {
        $notifyDate = DateUtilities::BuildSpecificDate($currentMonth, 10, $currentYear);
        $blogDueDate = DateUtilities::BuildSpecificDate($currentMonth, 15, $currentYear);
    } else if($dateToCompare <= DateUtilities::BuildSpecificDate($currentMonth, 15, $currentYear)) {
        $notifyDate = DateUtilities::BuildSpecificDate($currentMonth, 15, $currentYear);
        $blogDueDate = DateUtilities::BuildSpecificDate($currentMonth, 15, $currentYear);
    } else if($dateToCompare <= DateUtilities::BuildSpecificDate($currentMonth, 20, $currentYear)) {
        $notifyDate = DateUtilities::BuildSpecificDate($currentMonth, 20, $currentYear);
        $blogDueDate = DateUtilities::BuildSpecificDate($currentMonth, 30, $currentYear);
    } else {
        $notifyDate = date('m/d/Y', strtotime($currentMonth . "/30/" . $currentYear));
        $blogDueDate = date('m/d/Y', strtotime($currentMonth . "/30/" . $currentYear));
    }

    $newObj->notifyDate = $notifyDate;
    $newObj->blogDueDate = $blogDueDate;
    return $newObj;
}