<?php
/**
 * Created by PhpStorm.
 * User: mike.cokingtin
 * Date: 12/14/17
 * Time: 6:27 PM
 */

namespace src;

class BTXHistory
{

  "timestamp" integer,
    private $id;
    private $coin;
    private $market;
    private $description;
    private $history_ref_key;
    private $timestamp;

    function __construct($id = "", $coin, $market, $description, $value,
                         $history_ref_key, $timestamp)

    {
        $this->coin = $coin;
        $this->market = $market;
        $this->description = $description;
        $this->history_ref_key = $history_ref_key;
        $this->timestamp = $timestamp;
    }

    public static function CreateNewBTXHistory($id, $coin = "", $market = "", $description = "",  $history_ref_key = "", $timestamp = "") {
        return new BTXHistory($id, $coin, $market, $description, $history_ref_key, $timestamp);
    }

    public static function CreateNewBTXHistoryForInsert($coin = "", $market = "", $description = "", $history_ref_key = "", $timestamp = "") {
        return new BTXMarketHistory("", $coin, $market, $description, $history_ref_key, $timestamp);
    }

    /**
     * @return mixed
     */
    public function getCoin()
    {
        return $this->coin;
    }

    /**
     * @param mixed $coin
     */
    public function setCoin($coin)
    {
        $this->coin = $coin;
    }

    /**
     * @return mixed
     */
    public function getMarket()
    {
        return $this->market;
    }

    /**
     * @param mixed $market
     */
    public function setMarket($market)
    {
        $this->market = $market;
    }

    /**
     * @return mixed
     */
    public function getdescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setdescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function gethistory_ref_key()
    {
        return $this->history_ref_key;
    }

    /**
     * @param mixed $history_ref_key
     */
    public function sethistory_ref_key($history_ref_key)
    {
        $this->history_ref_key = $history_ref_key;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param mixed $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    public function createCommaDelimitedValueForInsert() {
        $retVal = "";
        $retVal .= "'$this->coin'";
        $retVal .= ",'$this->market'";
        $retVal .= ",". $this->description ."";
        $retVal .= ",". $this->history_ref_key ."";
        $retVal .= ",". $this->timestamp . "";

        return $retVal;
}
