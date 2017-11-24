<?php
/**
 * Created by PhpStorm.
 * User: apersinger
 * Date: 5/15/2017
 * Time: 2:13 PM
 */

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
//require $root . '/mongo/CRUD/classes/MongoUtility.php';
require_once $root . '/mongo/CRUD/classes/BlogData_Mongo.php';
require_once $root . '/mongo/CRUD/classes/Employee_Mongo.php';
require_once $root . '/CRUD/classes/Employee.php';
require_once $root . '/CRUD/classes/MonthDataEmployeeView.php';
require_once $root . '/CRUD/classes/HTMLBuilder.php';
require_once 'dateUtilities.php';

$collectionName = DateUtilities::GetCurrentMonth() . DateUtilities::GetCurrentYear();
$blogData_mongo = new BlogData_Mongo("test", $collectionName);
$result = $blogData_mongo->GetAllBlogData();
$listOfMonthDataVW = array();
foreach($result as $doc) {

    if( !empty($doc->empId) ) {
        //$empId, $empEmail, $date, $status, $blogTopic, $empName = ""
        $mdev = new MonthDataEmployeeView(
            (string)$doc->_id,
            $doc->empId,
            $doc->empEmail,
            $doc->date,
            $doc->status,
            $doc->topic,
            getEmployeeNameById($doc->empId)
        );

        if($mdev->GetMonthDataEmployeeId() > -1) {
            $listOfMonthDataVW[] = $mdev;
        }
    }
}
$headers = ["Employee ID", "Email", "Date", "Name", "Topic", "Status"];
$headerOrder = ["empId", "empEmail", "date", "empName", "topic", "status"];
$dataToParse = array();
$dataToParse[] = [ "headers" => $headers];
$dataToParse[] = [ "data" => $listOfMonthDataVW];
//var_dump($dataToParse);
$html = new HTMLBuilder($dataToParse);
$html->BuildTable($headerOrder, "employee", "blogOid");
echo $html->GetHTML();


function getEmployeeNameById($id) {
    $collectionName = "Employees";
    $employee_mongo = new Employee_Mongo("test", $collectionName);
    $result = $employee_mongo->GetEmployeeById($id);
    $name = "";
    foreach($result as $doc) {
        $name = $doc->employee->firstName . " " . $doc->employee->lastName;
    }
    return $name;
}
