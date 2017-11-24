<?php
/**
 * Created by PhpStorm.
 * User: apersinger
 * Date: 10/31/2017
 * Time: 10:15 AM
 */

namespace DBObjects;


class ScoreHeader
{
    var $id;
    var $key;
    var $overallScore;
    var $createdOn;
    var $fileName;
    var $scoreDetails;

    private function __construct($id, $key, $overallScore, $createdOn, $fileName) {
        $this->id = $id;
        $this->key = $key;
        $this->overallScore = $overallScore;
        $this->createdOn = $createdOn;
        $this->fileName = $fileName;
        $this->scoreDetails = array();
    }

    public static function CreateNewHeader($id, $key, $overallScore, $createdOn, $fileName) {
        return new ScoreHeader($id, $key, $overallScore, $createdOn, $fileName);
    }

    public function GetHeaderId() {
        return $this->id;
    }

    public function GetKey() {
        return $this->key;
    }

    public function GetOverallScore() {
        return $this->overallScore;
    }

    public function GetCreatedOn() {
        return $this->createdOn;
    }

    public function GetFileName() {
        return $this->fileName;
    }

    /**
     * @return array
     */
    public function getScoreDetails()
    {
        return $this->scoreDetails;
    }

    public function SetHeaderId($val) {
        $this->id = $val;
    }

    public function SetKey($val) {
        $this->key = $val;
    }

    public function SetOverallScore($val) {
        $this->overallScore = $val;
    }

    public function SetCreatedOn($val) {
        $this->createdOn = $val;
    }

    public function SetFileName($val) {
        $this->fileName = $val;
    }

    public function CalculateTotalScore() {
        $listOfCount = array();
        if(count($this->scoreDetails) > 0) {
            foreach ($this->scoreDetails as $dets) {
                $listOfCount[] = $dets->score;
            }
            $this->SetOverallScore(array_sum($listOfCount));
        }
    }

    /**
     * @param array $listOfScoreDetails
     */
    public function SetScoreDetails($listOfScoreDetails)
    {
        foreach ($listOfScoreDetails as $scoreDetails ) {
            $newDtlObj = ScoreDetails::CreateNewDetails(-1
                ,$this->GetHeaderId()
                , $scoreDetails->tag->id
                , ($scoreDetails->count * $scoreDetails->tag->scoreModifier)
            );
            $this->addScoreDetailsToScoreDetailsList($newDtlObj);
        }

    }

    public function AddScoreDetailsToScoreDetailsList($scoreDetails)
    {
        $this->scoreDetails[] = $scoreDetails;
    }
}