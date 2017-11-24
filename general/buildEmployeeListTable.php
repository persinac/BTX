<?php
/**
 * Created by PhpStorm.
 * User: apersinger
 * Date: 5/17/2017
 * Time: 8:34 AM
 */

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
//require $root . '/mongo/CRUD/classes/MongoUtility.php';
require_once $root . '/mongo/CRUD/classes/Employee_Mongo.php';
require_once $root . '/CRUD/classes/Employee.php';
require_once $root . '/CRUD/classes/MonthData.php';
require_once $root . '/CRUD/classes/HTMLBuilder.php';

$collectionName = "Employees";
$employee_mongo = new Employee_Mongo("test", $collectionName);
$result = $employee_mongo->GetAllEmployees();
$listOfEmployees = array();
foreach($result as $doc) {
    $employee = new Employee(
        (string)$doc->_id,
        $doc->employee->id,
        $doc->employee->firstName,
        $doc->employee->lastName,
        $doc->employee->email,
        $doc->employee->phone
    );
    if($employee->GetEmployeeId() > -1) {
        $listOfEmployees[] = $employee;
    }
}

$headers = ["ID", "First", "Last", "Email", "Phone"];
$headerOrder = ["id", "firstName", "lastName", "email", "phone"];
$dataToParse = array();
$dataToParse[] = [ "headers" => $headers];
$dataToParse[] = [ "data" => $listOfEmployees];
$html = new HTMLBuilder($dataToParse);
$html->BuildTable($headerOrder);
echo $html->GetHTML();
