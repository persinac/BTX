<?php
/**
 * Created by PhpStorm.
 * User: apersinger
 * Date: 5/9/2017
 * Time: 9:13 AM
 *
 * 5/9/17: As of now only create month collection (if not already exists)
 * and insert file name
 *
 * Data structure:
 * [ <month> : [ fileName: <filename> ] ]
 *
 */

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
//require $root . '/External Libraries/PHPExcel-1.8/Classes/PHPExcel.php';
//require 'mailer.php';
require $root . '/mongo/CRUD/classes/MongoUtility.php';
require $root . '/CRUD/classes/Employee.php';
require $root . '/CRUD/classes/MonthData.php';
require $root . '/External Libraries/PHPExcel-1.8/Classes/PHPExcel.php';
require $root . '/CRUD/general/dateUtilities.php';

/**
 * @param $fileName
 *
 * File Structure:
 *  [ [ "blogger": <field_val>, "blogDate": <field_val> ] ]
 *
 */

function InsertExcelData($data, $collectionName = "") {
    $mongoObj = new MongoUtility();

    /* Default collection name to current Month/Yr */
    if(strlen($collectionName) == 0 ) {
        $collectionName = GetCurrentMonth() . GetCurrentYear();
    }
    $mongoObj->SelectDBToUse("test");
    $listOfCollections = $mongoObj->ListAllCollections();
    if(DoesCollectionExist($listOfCollections, $collectionName)) {
        $mongoObj->SelectCollection($collectionName);
    } else {
        $mongoObj->CreateNewCollection($collectionName);
        $mongoObj->SelectCollection($collectionName);
    }
    $mongoObj->InsertIntoCollection($data);
    $result = $mongoObj->FindAll();
    return $result;
}

function DeleteAllDocuments($collection) {
    $mongoObj = new MongoUtility();
    $mongoObj->SelectDBToUse("test");
    $output[] = array();

    $listOfCollections = $mongoObj->ListAllCollections();
    if(DoesCollectionExist($listOfCollections, $collection)) {
        $mongoObj->SelectCollection($collection);
        $result = $mongoObj->DeleteAllInCollection();
        $output = "Removed " . $result->getDeletedCount() . " documents from " . $collection;
    } else {
        $output[] = "Need to input an existing collection name!</br>";
    }
    return $output;
}

function ParseExcelData($fileName, $fileType) {
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/";
    $target_file = $target_dir . $fileName;
    $inputFileType = PHPExcel_IOFactory::identify($target_file);

//    echo '<br/> File ',pathinfo($target_file,PATHINFO_BASENAME),
//    ' has been identified as an ',$inputFileType,' file<br />';
//
//    echo '<br/> Loading file ',pathinfo($target_file,PATHINFO_BASENAME),
//    ' using IOFactory with the identified reader type<br />';
//    echo '<br/><br/>';
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objPHPExcel = $objReader->load($target_file);
    $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
    $arrayToRet = array();
    if($fileType == 0) {
        foreach( $sheetData AS $emp ) {
            if(strlen($emp["A"]) > 1) {
                $monthData = new MonthData(
                    "",
                    -1,
                    $emp["A"], /* email address in xlxs */
                    $emp["B"], /* date */
                    null,
                    "Assigned"
                );
                $arrayToRet[] = $monthData;
            }
        }
    } else {
        /* only care about cells A - E */
        foreach( $sheetData AS $emp ) {
            if(strlen($emp["B"]) > 1) {
                $employee = new Employee(
                    "",
                    $emp["A"],
                    $emp["B"],
                    $emp["C"],
                    $emp["D"],
                    $emp["E"]
                );
                $arrayToRet[] = $employee;
            }
        }
    }
    return $arrayToRet;
}

function getEmployeeIdByEmail($email) {
    $mongoObj = new MongoUtility();
    $collectionName = "Employees";
    $mongoObj->SelectDBToUse("test");
    $mongoObj->SelectCollection($collectionName);

    /* $gt = greater than */
    $filter = ['employee.email' => $email];
    $options = [
        'projection' => ['_id' => 0]
    ];
    $result = $mongoObj->FindSpecific($filter, $options);
    return $result;
}

function DoesCollectionExist($collections, $collectionName) {
    $result = false;
    foreach($collections as $cName) {
        if($cName == $collectionName) {
            $result = true;
        }
    }
    return $result;
}

//get current month
//select collection based on current month
//if collection does not exist - create new collection for month and insert data
//if collection does exist and file name is empty - insert file name
