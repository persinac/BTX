<?php
/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 10/30/2017
 * Time: 11:38 PM
 */

namespace src\CRUD\create;

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require_once $root . '/vendor/autoload.php';

use src\CRUD\BTXMaster;

class BTXKeeper extends BTXMaster
{
    function __construct($connection)
    {
        parent::__construct($connection);
    }

    public static function CreateNewBTXKeeper($connection) {
        return new BTXKeeper($connection);
    }

    public function CreateMultiInsertStatement($listOfObjects, $tableArray) {
        $beginInsert = "INSERT INTO ". $tableArray[0] . " " . $tableArray[1] . " VALUES ";

        $valuesImplode = array();
        foreach ($listOfObjects as $value) {
            $valuesImplode[] = "(" . $value->createCommaDelimitedValueForInsert() . ") ";
        }
        $valuesStatements = implode(",",$valuesImplode);
        $insertStatement = $beginInsert . $valuesStatements;
        return $insertStatement;
    }

    /***
     * This function takes in the sql statement to execute as well as the num of rows
     * and the table name.
     *
     * @param $insertStatement
     * @param int $numOfRows
     * @param string $tableName
     * @return int|string
     */
    public function ExecuteInsertStatement($insertStatement, $numOfRows = 0, $tableName = "") {
        $retVal = -1;
        $query = $insertStatement;
        if ($result = pg_query($query)) {
            $retVal = "Successfully inserted $numOfRows row(s) into $tableName!";
            pg_free_result($result);
        } else {
            $retVal = "Error: " . pg_last_error();
        }
        return  $retVal;
    }

    public function InsertNewScore($obj) {
//        $this->mys
        $retVal = -1;
        $this->InsertIntoHeader($obj);
        return $retVal;
    }

    public function InsertIntoHeader($hdrObj) {
        $retVal = -1;
        $query = "INSERT INTO " . RV_TBL_HTML_HEADER . " (keyValue, overallScore, createdOn, fileName) VALUES (?,?,?,?)";
        $key = $hdrObj->key;
        $overallScore = $hdrObj->overallScore;
        $fileName = $hdrObj->fileName;
        $date = date("Y-m-d H:i:s");
        $stmt = $this->scoreMastermysql->mys->prepare($query);
        $stmt->bind_param('siss', $key, $overallScore, $date, $fileName);
        if ($result = $stmt->execute()) {
            $hdrId = $stmt->insert_id;
            $stmt->close();
            $this->scoreMastermysql->mys->commit();
            for($i = 0; $i < count($hdrObj->scoreDetails); $i++) {
                $hdrObj->scoreDetails[$i]->hdrId = $hdrId;
            }
            $retVal = $this->InsertIntoDetails($hdrObj->scoreDetails);
        } else {
            $retVal = -2;
        }
        return $retVal;
    }

    public function InsertIntoDetails($dtlObj) {
        $retVal = -1;
        $query = "INSERT INTO " . RV_TBL_HTML_DETAILS . " (headerId, tagId, score) VALUES (?,?,?)";
        $stmt = $this->scoreMastermysql->mys->prepare($query);
        foreach ($dtlObj as $sd) {
            $stmt->bind_param('iii', $sd->hdrId, $sd->tagId, $sd->score);
            if ($result = $stmt->execute()) {
                $this->scoreMastermysql->mys->commit();
                $retVal = 1;
            } else {
                $retVal = -3;
            }
        }
        $stmt->close();
        return $retVal;
    }
}