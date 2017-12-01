<?php
/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 11/30/2017
 * Time: 6:12 PM
 */

namespace src;


class BTXMarketHistoryDetails
{
    private $id;
    private $btxid;
    private $coin;
    private $market;
    private $quantity;
    private $value;
    private $usdValue;
    private $total;
    private $fillytype;
    private $ordertype;
    private $btxtimestamp;

    function __construct($id = "", $btxid, $coin, $market, $quantity, $value,
                         $usdValue, $total, $fillytype, $ordertype, $btxtimestamp)
    {
        $this->id = $id;
        $this->btxid = $btxid;
        $this->coin = $coin;
        $this->market = $market;
        $this->quantity = $quantity;
        $this->value = $value;
        $this->usdValue = $usdValue;
        $this->total = $total;
        $this->fillytype = $fillytype;
        $this->ordertype = $ordertype;
        $this->btxtimestamp = $btxtimestamp;
    }

    public static function CreateNewBTXMarketHistoryDetails(
        $id = "", $btxid = "", $coin = "", $market = ""
        , $quantity = "", $value = "", $usdValue = ""
        , $total = "", $fillytype = "", $ordertype = "", $btxtimestamp = "") {
        return new BTXMarketHistoryDetails($id, $btxid, $coin, $market, $quantity, $value,
            $usdValue, $total, $fillytype, $ordertype, $btxtimestamp);
    }

    public static function CreateNewBTXMarketHistoryDetailsForInsert($btxid = "", $coin = "", $market = ""
        , $quantity = "", $value = "", $usdValue = ""
        , $total = "", $fillytype = "", $ordertype = "", $btxtimestamp = "") {
        return new BTXMarketHistoryDetails("",$btxid, $coin, $market, $quantity, $value,
            $usdValue, $total, $fillytype, $ordertype, $btxtimestamp);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getBtxid()
    {
        return $this->btxid;
    }

    /**
     * @param mixed $btxid
     */
    public function setBtxid($btxid)
    {
        $this->btxid = $btxid;
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
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param mixed $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
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
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param mixed $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return mixed
     */
    public function getFillytype()
    {
        return $this->fillytype;
    }

    /**
     * @param mixed $fillytype
     */
    public function setFillytype($fillytype)
    {
        $this->fillytype = $fillytype;
    }

    /**
     * @return mixed
     */
    public function getOrdertype()
    {
        return $this->ordertype;
    }

    /**
     * @param mixed $ordertype
     */
    public function setOrdertype($ordertype)
    {
        $this->ordertype = $ordertype;
    }

    /**
     * @return mixed
     */
    public function getBtxtimestamp()
    {
        return $this->btxtimestamp;
    }

    /**
     * @param mixed $btxtimestamp
     */
    public function setBtxtimestamp($btxtimestamp)
    {
        $this->btxtimestamp = $btxtimestamp;
    }

    /***
     * Needs to match the constant value list order:
     * (btxid,coin,market,quantity,\"value\",\"usdValue\",total,filltype,ordertype,btxtimestamp)
     * @return string
     */
    public function createCommaDelimitedValueForInsert() {
        $retVal = "";
        $retVal .= "$this->btxid";
        $retVal .= ",'$this->coin'";
        $retVal .= ",'$this->market'";
        $retVal .= ",". $this->quantity ."";
        $retVal .= ",". $this->value . "";
        $retVal .= ",". $this->usdValue ."";
        $retVal .= ",". $this->total ."";
        $retVal .= ",". $this->fillytype ."";
        $retVal .= ",". $this->ordertype ."";
        $retVal .= ",". $this->btxtimestamp ."";

        return $retVal;
    }

}