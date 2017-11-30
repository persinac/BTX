<?php
/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 11/30/2017
 * Time: 8:16 AM
 */

namespace src;

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require_once $root . '/vendor/autoload.php';

class BTXMarketHistory
{
    private $coin;
    private $market;
    private $volume;
    private $value;
    private $usdValue;
    private $high;
    private $low;
    private $lastSell;
    private $currentBid;
    private $openBuyOrders;
    private $openSellOrders;
    private $btxTimestamp;
    private $timestamp;

    function __construct($coin, $market, $volume, $value,
                         $usdValue, $high, $low, $lastSell, $currentBid,
                         $openBuyOrders, $openSellOrders, $btxTimestamp, $timestamp)
    {
        $this->coin = $coin;
        $this->market = $market;
        $this->volume = $volume;
        $this->value = $value;
        $this->usdValue = $usdValue;
        $this->high = $high;
        $this->low = $low;
        $this->lastSell = $lastSell;
        $this->currentBid = $currentBid;
        $this->openBuyOrders = $openBuyOrders;
        $this->openSellOrders = $openSellOrders;
        $this->btxTimestamp = $btxTimestamp;
        $this->timestamp = $timestamp;
    }

    public static function CreateNewBTXMarketHistory($coin = "", $market = "", $volume = "", $value = "", $usdValue = "",
                                                     $high = "", $low = "", $lastSell = "", $currentBid = "", $openBuyOrders = "",
                                                     $openSellOrders = "", $btxTimestamp = "", $timestamp = "") {
        return new BTXMarketHistory($coin, $market, $volume, $value, $usdValue, $high, $low, $lastSell, $currentBid,
            $openBuyOrders, $openSellOrders, $btxTimestamp, $timestamp);
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
    public function getVolume()
    {
        return $this->volume;
    }

    /**
     * @param mixed $volume
     */
    public function setVolume($volume)
    {
        $this->volume = $volume;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getUsdValue()
    {
        return $this->usdValue;
    }

    /**
     * @param mixed $usdValue
     */
    public function setUsdValue($usdValue)
    {
        $this->usdValue = $usdValue;
    }

    /**
     * @return mixed
     */
    public function getHigh()
    {
        return $this->high;
    }

    /**
     * @param mixed $high
     */
    public function setHigh($high)
    {
        $this->high = $high;
    }

    /**
     * @return mixed
     */
    public function getLow()
    {
        return $this->low;
    }

    /**
     * @param mixed $low
     */
    public function setLow($low)
    {
        $this->low = $low;
    }

    /**
     * @return mixed
     */
    public function getLastSell()
    {
        return $this->lastSell;
    }

    /**
     * @param mixed $lastSell
     */
    public function setLastSell($lastSell)
    {
        $this->lastSell = $lastSell;
    }

    /**
     * @return mixed
     */
    public function getCurrentBid()
    {
        return $this->currentBid;
    }

    /**
     * @param mixed $currentBid
     */
    public function setCurrentBid($currentBid)
    {
        $this->currentBid = $currentBid;
    }

    /**
     * @return mixed
     */
    public function getOpenBuyOrders()
    {
        return $this->openBuyOrders;
    }

    /**
     * @param mixed $openBuyOrders
     */
    public function setOpenBuyOrders($openBuyOrders)
    {
        $this->openBuyOrders = $openBuyOrders;
    }

    /**
     * @return mixed
     */
    public function getOpenSellOrders()
    {
        return $this->openSellOrders;
    }

    /**
     * @param mixed $openSellOrders
     */
    public function setOpenSellOrders($openSellOrders)
    {
        $this->openSellOrders = $openSellOrders;
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


    public function createCommaDelimitedValueForInsert() {
        $retVal = "";
        $retVal .= "'$this->coin'";
        $retVal .= ",'$this->market'";
        $retVal .= ",". $this->volume ."";
        $retVal .= ",". $this->value . "";
        $retVal .= ",". $this->usdValue ."";
        $retVal .= ",". $this->high ."";
        $retVal .= ",". $this->low ."";
        $retVal .= ",". $this->lastSell ."";
        $retVal .= ",". $this->currentBid ."";
        $retVal .= ",". $this->openBuyOrders ."";
        $retVal .= ",". $this->openSellOrders ."";
        $retVal .= ",". $this->btxTimestamp ."";
        $retVal .= ",". $this->timestamp . "";

        return $retVal;
    }
}