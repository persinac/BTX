<?php
/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 10/30/2017
 * Time: 11:38 PM
 */

namespace Create;


use CRUD\BTXMaster;
use TableNames;

class BTXKeeper extends BTXMaster
{
    function __construct()
    {
        parent::__construct();
    }

    public static function CreateNewScoreKeeper() {
        return new BTXKeeper();
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