<?php
/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 10/30/2017
 * Time: 11:36 PM
 */

namespace Read;

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require_once $root . '/vendor/autoload.php';
/*
require_once $root . '\markup-project-persinac\src\settings\settings.php';

require_once $root . '\markup-project-persinac\src\CRUD\ScoreMaster.php';
require_once $root . '\markup-project-persinac\src\CRUD\TableNames.php';
require_once $root . '\markup-project-persinac\src\CRUD\ViewNames.php';

require_once $root . '\markup-project-persinac\src\classes\ScoreHeader.php';
require_once $root . '\markup-project-persinac\src\classes\ScoreDetails.php';
*/
use CRUD\BTXMaster;
//use DBObjects\ScoreHeader;
use TableNames;
//use ViewNames;

class BTXFinder extends BTXMaster
{
    function __construct()
    {
        parent::__construct();
    }

    public static function CreateNewScoreFinder() {
        return new BTXFinder();
    }

    public function GetCoinsToWatch() {
        $retVal = -1;
        $query = "select btxledgerid from " . BTX_TBL_COINS_TO_WATCH;
        $moreResults = $this->btxMasterSQL->mys->more_results();
        if($moreResults) {
            $this->btxMasterSQL->mys->next_result();
        }
        if ($result = $this->btxMasterSQL->mys->query($query)) {
            $retVal = $result->num_rows;
            $result->free();
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