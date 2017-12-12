<?php
/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 10/30/2017
 * Time: 11:36 PM
 */

namespace src\CRUD\read;

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require_once $root . '/vendor/autoload.php';

use src\CRUD\BTXMaster;
//use DBObjects\ScoreHeader;

class BTXFinder extends BTXMaster
{
    function __construct($connection)
    {
        parent::__construct($connection);
    }

    public static function CreateNewScoreFinder($connection) {
        return new BTXFinder($connection);
    }

    public function GetCoinsToWatch() {
        $retVal = -1;
        $query = sprintf("select * from %s",BTX_TBL_TESTING_LEDGER);
        if ($result = pg_query($query)) {
            $retVal = pg_num_rows($result);
            pg_free_result($result);
        }
        return  $retVal;
    }
    /*
    public function GetTagReferenceData() {
        $retVal = -1;
        $listOfTags = array();
        $query = "select * from " . RV_TBL_HTML_TAGS . " WHERE isActive = 1";
        $moreResults = $this->scoreMastermysql->mys->more_results();
        if($moreResults) {
            $this->scoreMastermysql->mys->next_result();
        }
        if ($result = $this->scoreMastermysql->mys->query($query)) {
            while ($row = $result->fetch_assoc()) {
                $tagObj = new \stdClass();
                $tagObj->id = $row['id'];
                $tagObj->tag = $row['tag'];
                $tagObj->scoreModifier = $row['scoreModifier'];
                $listOfTags[] = $tagObj;
            }
            $result->free();
        }
        return  $listOfTags;
    }

    public function GetTotalNumberOfUploads() {
        $retVal = -1;
        $query = "select id from " . RV_TBL_HTML_HEADER;
        $moreResults = $this->scoreMastermysql->mys->more_results();
        if($moreResults) {
            $this->scoreMastermysql->mys->next_result();
        }
        if ($result = $this->scoreMastermysql->mys->query($query)) {
            $retVal = $result->num_rows;
            $result->free();
        }
        return  $retVal;
    }

    public function GetAllOverallScores() {
        $listOfScores = array();
        $query = "select overallScore from " . RV_TBL_HTML_HEADER;
        if($this->scoreMastermysql->mys->more_results()) {
            $this->scoreMastermysql->mys->next_result();
        }
        if ($result = $this->scoreMastermysql->mys->query($query)) {
            while ($row = $result->fetch_assoc()) {
                $listOfScores[] = $row['overallScore'];
            }
            $result->free();
        }
        return  $listOfScores;
    }

    public function GetAverageOverallScore() {
        $retVal = -1;
        $query = "select avgOverallScore from " . RV_VW_HTML_OVERALLSCORE_AVG;
        if($this->scoreMastermysql->mys->more_results()) {
            $this->scoreMastermysql->mys->next_result();
        }
        if ($result = $this->scoreMastermysql->mys->query($query)) {
            while ($row = $result->fetch_assoc()) {
                $retVal = $row['avgOverallScore'];
            }
            $result->free();
        }
        return  $retVal;
    }

    public function GetAllHeaders() {

        $listOfHeaders = array();
        $query = "select * from " . RV_TBL_HTML_HEADER;
        if($this->scoreMastermysql->mys->more_results()) {
            $this->scoreMastermysql->mys->next_result();
        }
        if ($result = $this->scoreMastermysql->mys->query($query)) {
            while ($row = $result->fetch_assoc()) {
                $header = ScoreHeader::CreateNewHeader(
                    $row['id'],
                    $row['keyValue'],
                    $row['overallScore'],
                    $row['createdOn'],
                    $row['fileName']
                );
                $listOfHeaders[] = $header;
            }
            $result->free();
        }
        return  $listOfHeaders;
    }

    public function GetUniqueKeys() {

        $listOfKeys = array();
        $query = "select keyValue from " . RV_TBL_HTML_HEADER . " group by keyValue";
        if($this->scoreMastermysql->mys->more_results()) {
            $this->scoreMastermysql->mys->next_result();
        }
        if ($result = $this->scoreMastermysql->mys->query($query)) {
            while ($row = $result->fetch_assoc()) {
                $listOfKeys[] = $row['keyValue'];
            }
            $result->free();
        }
        return  $listOfKeys;
    }

    public function GetHeaderDetailsById($id) {

        $listOfDetails = array();
        $query = "select * from " . RV_VW_HTML_HEADER_DETAILS . " where id = $id";
        if($this->scoreMastermysql->mys->more_results()) {
            $this->scoreMastermysql->mys->next_result();
        }
        if ($result = $this->scoreMastermysql->mys->query($query)) {
            while ($row = $result->fetch_assoc()) {
                $details = new \stdClass();
                $details->id = $row['id'];
                $details->keyValue = $row['keyValue'];
                $details->createdOn = $row['createdOn'];
                $details->fileName = $row['fileName'];
                $details->overallScore = $row['overallScore'];
                $details->score = $row['score'];
                $details->tag = $row['tag'];
                $details->scoreModifier = $row['scoreModifier'];
                $listOfDetails[] = $details;
            }
            $result->free();
        }
        return  $listOfDetails;
    }
    */
}