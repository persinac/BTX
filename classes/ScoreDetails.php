<?php
/**
 * Created by PhpStorm.
 * User: apersinger
 * Date: 10/31/2017
 * Time: 10:14 AM
 */

namespace DBObjects;


class ScoreDetails
{
    var $id;
    var $hdrId;
    var $tagId;
    var $score;

    private function __construct($id, $hdrId, $tagId, $score) {
        $this->id = $id;
        $this->hdrId = $hdrId;
        $this->tagId = $tagId;
        $this->score = $score;
    }

    public static function CreateNewDetails($id, $hdrId, $tagId, $score) {
        return new ScoreDetails($id, $hdrId, $tagId, $score);
    }

    public function GetId() {
        return $this->id;
    }

    public function GetHeaderId() {
        return $this->hdrId;
    }

    public function GetTag() {
        return $this->tagId;
    }

    public function GetScore() {
        return $this->score;
    }

    public function SetId($val) {
        $this->id = $val;
    }

    public function SetHeaderId($val) {
        $this->hdrId = $val;
    }

    public function SetTag($val) {
        $this->tagId = $val;
    }

    public function SetScore($val) {
        $this->score = $val;
    }
}