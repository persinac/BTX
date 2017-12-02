<?php
/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 12/2/2017
 * Time: 11:42 AM
 */

namespace src;


class BTXCoinHeader
{
    private $id;
    private $coin;
    private $market;
    private $coinname;
    private $mintradesize;
    private $txfee;
    private $minconfirmation;
    private $isactive;
    private $btxTimestamp;
    private $timestamp;
    private $logourl;

    function __construct($id = "", $coin, $market, $coinname, $mintradesize,
                         $txfee, $minconfirmation, $isactive, $btxTimestamp, $timestamp, $logourl)
    {
        $this->id = $id;
        $this->coin = $coin;
        $this->market = $market;
        $this->coinname = $coinname;
        $this->mintradesize = $mintradesize;
        $this->txfee = $txfee;
        $this->minconfirmation = $minconfirmation;
        $this->isactive = $isactive;
        $this->btxTimestamp = $btxTimestamp;
        $this->timestamp = $timestamp;
        $this->logourl = $logourl;
    }

    public static function CreateNewBTXCoinHeader($id, $coin = "", $market = "", $coinname = "", $mintradesize = "",
                                                  $txfee = "", $minconfirmation = "", $isactive = "",
                                                  $btxTimestamp = "", $timestamp = "", $logourl = "") {
        return new BTXCoinHeader($id, $coin, $market, $coinname, $mintradesize,
            $txfee, $minconfirmation, $isactive, $btxTimestamp, $timestamp, $logourl);
    }

    public static function CreateNewBTXCoinHeaderForInsert($coin = "", $market = "", $coinname = "", $mintradesize = "",
                                                  $txfee = "", $minconfirmation = "", $isactive = "",
                                                   $btxTimestamp = "", $timestamp = "", $logourl = "") {
        return new BTXCoinHeader($coin, $market, $coinname, $mintradesize,
            $txfee, $minconfirmation, $isactive, $btxTimestamp, $timestamp, $logourl);
    }
    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
    public function getCoinname()
    {
        return $this->coinname;
    }

    /**
     * @param mixed $coinname
     */
    public function setCoinname($coinname)
    {
        $this->coinname = $coinname;
    }

    /**
     * @return mixed
     */
    public function getMintradesize()
    {
        return $this->mintradesize;
    }

    /**
     * @param mixed $mintradesize
     */
    public function setMintradesize($mintradesize)
    {
        $this->mintradesize = $mintradesize;
    }

    /**
     * @return mixed
     */
    public function getTxfee()
    {
        return $this->txfee;
    }

    /**
     * @param mixed $txfee
     */
    public function setTxfee($txfee)
    {
        $this->txfee = $txfee;
    }

    /**
     * @return mixed
     */
    public function getMinconfirmation()
    {
        return $this->minconfirmation;
    }

    /**
     * @param mixed $minconfirmation
     */
    public function setMinconfirmation($minconfirmation)
    {
        $this->minconfirmation = $minconfirmation;
    }

    /**
     * @return mixed
     */
    public function getIsactive()
    {
        return $this->isactive;
    }

    /**
     * @param mixed $isactive
     */
    public function setIsactive($isactive)
    {
        $this->isactive = $isactive;
    }

    /**
     * @return mixed
     */
    public function getBtxTimestamp()
    {
        return $this->btxTimestamp;
    }

    /**
     * @param mixed $btxTimestamp
     */
    public function setBtxTimestamp($btxTimestamp)
    {
        $this->btxTimestamp = $btxTimestamp;
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

    /**
     * @return mixed
     */
    public function getLogourl()
    {
        return $this->logourl;
    }

    /**
     * @param mixed $logourl
     */
    public function setLogourl($logourl)
    {
        $this->logourl = $logourl;
    }

    public function createCommaDelimitedValueForInsert() {
        $retVal = "";
        $retVal .= "'$this->coin'";
        $retVal .= ",'$this->market'";
        $retVal .= ",". $this->coinname ."";
        $retVal .= ",". $this->mintradesize . "";
        $retVal .= ",". $this->txfee ."";
        $retVal .= ",". $this->minconfirmation ."";
        $retVal .= ",". $this->isactive ."";
        $retVal .= ",". $this->btxTimestamp ."";
        $retVal .= ",". $this->timestamp . "";

        return $retVal;
    }
}