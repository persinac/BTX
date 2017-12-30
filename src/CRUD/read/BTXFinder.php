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

use \RecursiveIteratorIterator;
use src\Common\SQLCreationUtility;
use src\CRUD\BTXMaster;
//use DBObjects\ScoreHeader;

class BTXFinder extends BTXMaster
{
    private $fieldsToNotCheck;
    function __construct($connection)
    {
        $this->fieldsToNotCheck = array("orderBy", "orderASC"
        , "startIndex", "batchSize", "stmntName");
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
//            pg_free_result($result);
        }
        return  $retVal;
    }

    /* This will be primarily used to get main header data
     - No fancy queries
     */
    public function GetCoinHeader($stmntName,
        $id = "", $coin = "", $market = ""
        , $coinname = "", $mintTradeSize = "", $txFee = ""
        , $minConfirmation = "", $isActive = "", $btxTimestamp = ""
        , $timestamp = "", $logourl = "", $orderBy = "", $orderASC = ""
        , $startIndex = "", $batchSize = ""
    ) {
        $retVal = -1;
        $query = sprintf("select * from %s",
            BTX_TBL_COIN_HEADER[0]);

        /*
        Get the parameters for this function
        Cross reference the param list to the values
        that were actually passed int
        Create an array of:
            field: <paramname>
            value: <value of crossref param>
            operator: = (for now)
         */
        $arr = array();
        $ref = new \ReflectionClass(BTXFinder::CreateNewScoreFinder($this->GetSQLObj()));
        foreach($ref->getMethods() as $methods) {
            $name = $methods->getName();
            if($name == __FUNCTION__) {
                $paramsList = $methods->getParameters();
                foreach ($paramsList as $param) {
                    /*
                     * $param->name: id
                     * ${<value>} -> $<value> -> $value
                     * ${<id>} -> $<id> -> $id
                     * */
                    if(!empty(${$param->name})
                        && !in_array($param->name, $this->fieldsToNotCheck)
                    ) {
                        $operator = "=";
                        if(gettype(${$param->name}) == "array") {
                            $operator = "in";
                        }
                        $arr[] = array(
                            "field" => $param->name
                            , "value"=>${$param->name}
                            , "operator"=>$operator
                        );
                    }
                }
            }
        }
        $tempArray = $arr;
        /* First construct the where statement */
        $retQuery = SQLCreationUtility::ConstructWhereStatement($arr, $this->fieldsToNotCheck);
        /* Second - construct the Order by */
        $orderBy = SQLCreationUtility::ConstructOrderByStatement($orderBy, $orderASC);
        /* Third - construct the paging */
        $limitOffset = SQLCreationUtility::ConstructLimitOffsetStatement($batchSize, $startIndex);
        /* put it all together */
        $result = pg_prepare($this->GetSQLObj(), $stmntName, $query . $retQuery[0] . " " . $orderBy ." ". $limitOffset);

        /* Flatten the array(s) */
        $it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($tempArray));
        $valuesForPrepStmnt = array();
        foreach($it as $k => $v) {
            /* find the value(s) and search for integers for the indexes in the arrays */
            if($k == "value" || gettype($k) == "integer") {
                $valuesForPrepStmnt[] = $v;
            }
        }
        /**
         * select * from BLAH where F1 = $1 and F2 = $2 and F3 in ($3, $4)
         * $valuesForPrepStmnt = array(1,2,6,9)
         */
        $result = pg_execute($this->GetSQLObj(), $stmntName, $valuesForPrepStmnt);
        /**
         * select * from BLAH where F1 = 1 and F2 = 2 and F3 in (6, 9)
         */
        if ($result === false) {}
        else {
            $arr = pg_fetch_all($result);
            $result = $arr;
        }
        return  $result;
    }


    public function GetMarketHistory(
        $stmntName, $id = "", $coin = "", $market = ""
        , $volumeGT = "", $volumeLT = ""
        , $valueGT = "", $valueLT = "", $openBuyOrders = ""
        , $openSellOrders = "", $btxtimestamp = "", $orderBy = "", $orderASC = ""
        , $startIndex = "", $batchSize = ""
    ) {
        $retVal = -1;
        $query = sprintf("select * from %s ",
            BTX_TBL_MARKET_HISTORY[0]);

        /*
        Get the parameters for this function
        Cross reference the param list to the values
        that were actually passed int
        Create an array of:
            field: <paramname>
            value: <value of crossref param>
            operator: = (for now)
         */
        $arr = array();
        $ref = new \ReflectionClass(BTXFinder::CreateNewScoreFinder($this->GetSQLObj()));
        foreach($ref->getMethods() as $methods) {
            $name = $methods->getName();
            if($name == __FUNCTION__) {
                $paramsList = $methods->getParameters();
                foreach ($paramsList as $param) {
                    if(!empty(${$param->name})
                        && !in_array($param->name, $this->fieldsToNotCheck)
                    ) {
                        $operator = "=";
                        $value = ${$param->name};
                        $field = $param->name;
                        if(gettype(${$param->name}) == "array") {
                            $operator = "in";
                        }

                        if($field == "volumeGT") {
                            $field = "volume";
                            $operator = ">";
                        } else if($field == "volumeLT") {
                            $field = "volume";
                            $operator = "<";
                        } else if($field == "valueGT") {
                            $field = "value";
                            $operator = ">";
                        } else if($field == "valueLT") {
                            $field = "value";
                            $operator = "<";
                        }

                        $arr[] = array(
                            "field" => $field
                        , "value"=> $value
                        , "operator"=>$operator
                        );
                    }
                }
            }
        }
        $tempArray = $arr;
        /* First construct the where statement */
        $retQuery = SQLCreationUtility::ConstructWhereStatement($arr, $this->fieldsToNotCheck);
        /* Second - construct the Order by */
        $orderBy = SQLCreationUtility::ConstructOrderByStatement($orderBy, $orderASC);
        /* Third - construct the paging */
        $limitOffset = SQLCreationUtility::ConstructLimitOffsetStatement($batchSize, $startIndex);
        /* put it all together */
        $fullSQL = $query . $retQuery[0] ." ". $orderBy ." ". $limitOffset;
        $result = pg_prepare($this->GetSQLObj(), $stmntName, $fullSQL);

        /* Flatten the array(s) */
        $it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($tempArray));
        $valuesForPrepStmnt = array();
        foreach($it as $k => $v) {
            /* find the value(s) and search for integers for the indexes in the arrays */
            if($k == "value" || gettype($k) == "integer") {
                $valuesForPrepStmnt[] = $v;
            }
        }
        $result = pg_execute($this->GetSQLObj(), $stmntName, $valuesForPrepStmnt);
        if ($result === false) {}
        else {
            $arr = pg_fetch_all($result);
            $result = $arr;
        }
        return  $result;
    }

    public function GetMarketHistoryDetails(
        $stmntName, $id = "", $btxid = "", $coin = "", $market = ""
        , $quantityGT = "", $quantityLT = ""
        , $valueGT = "", $valueLT = ""
        , $totalGT = "", $totalLT = ""
        , $filltype = "", $ordertype = ""
        , $orderBy = "", $orderASC = ""
        , $startIndex = "", $batchSize = ""
    ) {
        $retVal = -1;
        $query = sprintf("select * from %s ",
            BTX_TBL_COIN_MARKET_HISTORY_DETAILS[0]);

        /*
        Get the parameters for this function
        Cross reference the param list to the values
        that were actually passed int
        Create an array of:
            field: <paramname>
            value: <value of crossref param>
            operator: = (for now)
         */
        $arr = array();
        $ref = new \ReflectionClass(BTXFinder::CreateNewScoreFinder($this->GetSQLObj()));
        foreach($ref->getMethods() as $methods) {
            $name = $methods->getName();
            if($name == __FUNCTION__) {
                $paramsList = $methods->getParameters();
                foreach ($paramsList as $param) {
                    $operator = "=";
                    $value = ${$param->name};
                    $field = $param->name;

                    if(!empty(${$param->name})
                        && !in_array($param->name, $this->fieldsToNotCheck)
                    ) {
                        if(gettype(${$param->name}) == "array") {
                            $operator = "in";
                        }

                        if($field == "quantityGT") {
                            $field = "quantity";
                            $operator = ">";
                        } else if($field == "quantityLT") {
                            $field = "quantity";
                            $operator = "<";
                        } else if($field == "valueGT") {
                            $field = "value";
                            $operator = ">";
                        } else if($field == "valueLT") {
                            $field = "value";
                            $operator = "<";
                        } else if($field == "totalGT") {
                            $field = "total";
                            $operator = ">";
                        } else if($field == "totalLT") {
                            $field = "total";
                            $operator = "<";
                        }

                        $arr[] = array(
                            "field" => $field
                        , "value"=> $value
                        , "operator"=>$operator
                        );
                    }
                }
            }
        }
        $tempArray = $arr;
        /* First construct the where statement */
        $retQuery = SQLCreationUtility::ConstructWhereStatement($arr, $this->fieldsToNotCheck);
        /* Second - construct the Order by */
        $orderBy = SQLCreationUtility::ConstructOrderByStatement($orderBy, $orderASC);
        /* Third - construct the paging */
        $limitOffset = SQLCreationUtility::ConstructLimitOffsetStatement($batchSize, $startIndex);
        /* put it all together */
        $fullSQL = $query . $retQuery[0] ." ". $orderBy ." ". $limitOffset;
//        var_dump($fullSQL);
        $result = pg_prepare($this->GetSQLObj(), $stmntName, $fullSQL);

        /* Flatten the array(s) */
        $it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($tempArray));
        $valuesForPrepStmnt = array();
        foreach($it as $k => $v) {
            /* find the value(s) and search for integers for the indexes in the arrays */
            if($k == "value" || gettype($k) == "integer") {
                $valuesForPrepStmnt[] = $v;
            }
        }
        $result = pg_execute($this->GetSQLObj(), $stmntName, $valuesForPrepStmnt);
        if ($result === false) {}
        else {
            $arr = pg_fetch_all($result);
            $result = $arr;
        }
        return  $result;
    }

    /* This will be primarily used to get main header data
    - No fancy queries
    */
    public function GetCoinHeaderForDropdown($stmntName,
                                  $id = "", $coin = "", $market = ""
        , $coinname = "", $mintTradeSize = "", $txFee = ""
        , $minConfirmation = "", $isActive = "", $btxTimestamp = ""
        , $timestamp = "", $logourl = "", $orderBy = "", $orderASC = ""
        , $startIndex = "", $batchSize = ""
    ) {
        $retVal = -1;
        $query = sprintf("select coin, market from %s",
            BTX_TBL_COIN_HEADER[0]);

        /*
        Get the parameters for this function
        Cross reference the param list to the values
        that were actually passed int
        Create an array of:
            field: <paramname>
            value: <value of crossref param>
            operator: = (for now)
         */
        $arr = array();
        $ref = new \ReflectionClass(BTXFinder::CreateNewScoreFinder($this->GetSQLObj()));
        foreach($ref->getMethods() as $methods) {
            $name = $methods->getName();
            if($name == __FUNCTION__) {
                $paramsList = $methods->getParameters();
                foreach ($paramsList as $param) {
                    /*
                     * $param->name: id
                     * ${<value>} -> $<value> -> $value
                     * ${<id>} -> $<id> -> $id
                     * */
                    if(!empty(${$param->name})
                        && !in_array($param->name, $this->fieldsToNotCheck)
                    ) {
                        $operator = "=";
                        if(gettype(${$param->name}) == "array") {
                            $operator = "in";
                        }
                        $arr[] = array(
                            "field" => $param->name
                        , "value"=>${$param->name}
                        , "operator"=>$operator
                        );
                    }
                }
            }
        }
        $tempArray = $arr;
        /* First construct the where statement */
        $retQuery = SQLCreationUtility::ConstructWhereStatement($arr, $this->fieldsToNotCheck);
        /* Second - construct the Order by */
        $orderBy = SQLCreationUtility::ConstructOrderByStatement($orderBy, $orderASC);
        /* Third - construct the paging */
        $limitOffset = SQLCreationUtility::ConstructLimitOffsetStatement($batchSize, $startIndex);
        /* Fourth - should be temporary - construct group by */
        $groupBy = "GROUP BY coin, market";
        /* put it all together */
        $result = pg_prepare($this->GetSQLObj(), $stmntName, $query . $retQuery[0] . " " . $groupBy ." ". $orderBy ." ". $limitOffset);

        /* Flatten the array(s) */
        $it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($tempArray));
        $valuesForPrepStmnt = array();
        foreach($it as $k => $v) {
            /* find the value(s) and search for integers for the indexes in the arrays */
            if($k == "value" || gettype($k) == "integer") {
                $valuesForPrepStmnt[] = $v;
            }
        }
        /**
         * select * from BLAH where F1 = $1 and F2 = $2 and F3 in ($3, $4)
         * $valuesForPrepStmnt = array(1,2,6,9)
         */
        $result = pg_execute($this->GetSQLObj(), $stmntName, $valuesForPrepStmnt);
        /**
         * select * from BLAH where F1 = 1 and F2 = 2 and F3 in (6, 9)
         */
        if ($result === false) {}
        else {
            $arr = pg_fetch_all($result);
            $result = $arr;
        }
        return  $result;
    }

    public function GetDataFromHistory($stmntName,
          $id = "", $coin = "", $market = ""
        , $description = "", $historyRefKey = "", $timestamp = ""
        , $orderBy = "", $orderASC = ""
        , $startIndex = "", $batchSize = ""
    ) {
        $retVal = -1;
        $query = sprintf("select * from %s",
            BTX_TBL_HISTORY[0]);

        /*
        Get the parameters for this function
        Cross reference the param list to the values
        that were actually passed int
        Create an array of:
            field: <paramname>
            value: <value of crossref param>
            operator: = (for now)
         */
        $arr = array();
        $ref = new \ReflectionClass(BTXFinder::CreateNewScoreFinder($this->GetSQLObj()));
        foreach($ref->getMethods() as $methods) {
            $name = $methods->getName();
            if($name == __FUNCTION__) {
                $paramsList = $methods->getParameters();
                foreach ($paramsList as $param) {
                    /*
                     * $param->name: id
                     * ${<value>} -> $<value> -> $value
                     * ${<id>} -> $<id> -> $id
                     * */
                    if(!empty(${$param->name})
                        && !in_array($param->name, $this->fieldsToNotCheck)
                    ) {
                        $operator = "=";
                        if(gettype(${$param->name}) == "array") {
                            $operator = "in";
                        }
                        $arr[] = array(
                            "field" => $param->name
                        , "value"=>${$param->name}
                        , "operator"=>$operator
                        );
                    }
                }
            }
        }
        $tempArray = $arr;
        /* First construct the where statement */
        $retQuery = SQLCreationUtility::ConstructWhereStatement($arr, $this->fieldsToNotCheck);
        /* Second - construct the Order by */
        $orderBy = SQLCreationUtility::ConstructOrderByStatement($orderBy, $orderASC);
        /* Third - construct the paging */
        $limitOffset = SQLCreationUtility::ConstructLimitOffsetStatement($batchSize, $startIndex);
        /* put it all together */

        $result = pg_prepare($this->GetSQLObj(), $stmntName, $query . $retQuery[0] . " " . $orderBy ." ". $limitOffset);

        /* Flatten the array(s) */
        $it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($tempArray));
        $valuesForPrepStmnt = array();
        foreach($it as $k => $v) {
            /* find the value(s) and search for integers for the indexes in the arrays */
            if($k == "value" || gettype($k) == "integer") {
                $valuesForPrepStmnt[] = $v;
            }
        }
        /**
         * select * from BLAH where F1 = $1 and F2 = $2 and F3 in ($3, $4)
         * $valuesForPrepStmnt = array(1,2,6,9)
         */
        $result = pg_execute($this->GetSQLObj(), $stmntName, $valuesForPrepStmnt);
        /**
         * select * from BLAH where F1 = 1 and F2 = 2 and F3 in (6, 9)
         */
        if ($result === false) {}
        else {
            $arr = pg_fetch_all($result);
            $result = $arr;
        }
        return  $result;
    }

    public function GetRefValuesForHistory($stmntName,
          $id = "", $type = "", $subtype = ""
            , $name = "", $isActive = ""
        , $orderBy = "", $orderASC = ""
        , $startIndex = "", $batchSize = ""
    ) {
        $retVal = -1;
        $query = sprintf("select * from %s",
            BTX_TBL_HISTORY_REF[0]);

        /*
        Get the parameters for this function
        Cross reference the param list to the values
        that were actually passed int
        Create an array of:
            field: <paramname>
            value: <value of crossref param>
            operator: = (for now)
         */
        $arr = array();
        $ref = new \ReflectionClass(BTXFinder::CreateNewScoreFinder($this->GetSQLObj()));
        foreach($ref->getMethods() as $methods) {
            $methodName = $methods->getName();
            if($methodName == __FUNCTION__) {
                $paramsList = $methods->getParameters();
                foreach ($paramsList as $param) {
                    /*
                     * $param->name: id
                     * ${<value>} -> $<value> -> $value
                     * ${<id>} -> $<id> -> $id
                     * */
                    if(!empty(${$param->name})
                        && !in_array($param->name, $this->fieldsToNotCheck)
                    ) {
                        $operator = "=";
                        if(gettype(${$param->name}) == "array") {
                            $operator = "in";
                        }
                        $arr[] = array(
                            "field" => $param->name
                        , "value"=>${$param->name}
                        , "operator"=>$operator
                        );
                    }
                }
            }
        }
        $tempArray = $arr;
        /* First construct the where statement */
        $retQuery = SQLCreationUtility::ConstructWhereStatement($arr, $this->fieldsToNotCheck);
        /* Second - construct the Order by */
        $orderBy = SQLCreationUtility::ConstructOrderByStatement($orderBy, $orderASC);
        /* Third - construct the paging */
        $limitOffset = SQLCreationUtility::ConstructLimitOffsetStatement($batchSize, $startIndex);
        /* put it all together */
        $result = pg_prepare($this->GetSQLObj(), $stmntName, $query . $retQuery[0] . " " . $orderBy ." ". $limitOffset);

        /* Flatten the array(s) */
        $it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($tempArray));
        $valuesForPrepStmnt = array();
        foreach($it as $k => $v) {
            /* find the value(s) and search for integers for the indexes in the arrays */
            if($k == "value" || gettype($k) == "integer") {
                $valuesForPrepStmnt[] = $v;
            }
        }
        /**
         * select * from BLAH where F1 = $1 and F2 = $2 and F3 in ($3, $4)
         * $valuesForPrepStmnt = array(1,2,6,9)
         */
        $result = pg_execute($this->GetSQLObj(), $stmntName, $valuesForPrepStmnt);
        /**
         * select * from BLAH where F1 = 1 and F2 = 2 and F3 in (6, 9)
         */
        if ($result === false) {}
        else {
            $arr = pg_fetch_all($result);
            $result = $arr;
        }
        return  $result;
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