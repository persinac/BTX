<?php
/**
 * Created by PhpStorm.
 * User: apersinger
 * Date: 5/11/2017
 * Time: 10:39 AM
 */

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
include $root . '/mongo/CRUD/classes/MongoUtility.php';

function getEmployeeIdByEmail($email) {
    $mongoObj = new MongoUtility();
    $collectionName = "Employees";
    $mongoObj->SelectDBToUse("test");
    $mongoObj->SelectCollection($collectionName);

    $filter = ['employee.id' => ['$gt' => $email]];
    $options = [
        'projection' => ['_id' => 0],
        'sort' => ['employee.id' => -1],
    ];
    $result = $mongoObj->FindSpecific($filter, $options);
    return $result;
}