<?php
/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 12/6/2017
 * Time: 6:52 PM
 */

namespace src\Common;


class SQLCreationUtility
{

    /***
     * Get the parameters for the function name passed in
     * Cross reference the param list to the values that were actually passed int
     * Create an array of:
     *   field: <paramname>
     *   value: <value of crossref param>
     *   operator: <calulated value>
     *
     * @param $reflectionObj
     * @param $methodNameToCheck
     * @param $fieldsToNotCheck
     * @return array
     */
    public static function ConstructFieldsForWhereStatement(
        $reflectionObj, $methodNameToCheck, $fieldsToNotCheck) {
        $arr = array();
        foreach($reflectionObj->getMethods() as $methods) {
            $name = $methods->getName();
            if($name == $methodNameToCheck) {
                $paramsList = $methods->getParameters();
                var_dump($paramsList);
                foreach ($paramsList as $param) {
                    $operator = "=";
                    $value = ${$param->name};
                    $field = $param->name;

                    if(!empty(${$param->name})
                        && !in_array($param->name, $fieldsToNotCheck)
                    ) {
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
        return $arr;
    }

    /***
     * @param $params
     * @return mixed|string
     */
    public static function ConstructWhereStatement($params, $fieldsToNotCheck) {
        $whereStatement = "";
        $whereArray = array();
        $activeItemArray = array();
        $paramIdx = 1;
        foreach ($params as $item) {
            $tempWhere = "";
            if(!empty($item['value'])
                && !in_array($item['field'], $fieldsToNotCheck)
            ) {
                $tempWhere = $item['field'] . " ";
                if($item['operator'] == "in") {
                    $tempWhere .= $item['operator'] . " ( ";

                    for ($i = 0; $i < sizeof($item['value']); $i++) {
                        $tempWhere .= ' $' . $paramIdx . '';
                        if($i >= 0 && $i < sizeof($item['value'])-1) {
                            $tempWhere .= ",";
                        }
                        $paramIdx += 1;
                    }
                    $tempWhere .=  ") ";
                } else {
                    $tempWhere .= $item['operator'] . ' $' . $paramIdx . '';
                    $paramIdx += 1;
                }
                $activeItemArray[] = $item['value'];
                $whereArray[] = $tempWhere;
            }
        }
        if(sizeof($whereArray) > 1) {
            $whereStatement = implode(" AND ", $whereArray);
        } else if (count($whereArray) == 1) {
            $whereStatement = $whereArray[0];
        }
//
        if(strlen($whereStatement) > 0) {
            $whereStatement = " WHERE " . $whereStatement;
        }
        return array($whereStatement, $activeItemArray);
    }

    public static function ConstructOrderByStatement($fields, $orderByAsc = "") {
        $orderBy = "";
        if(sizeof($fields) > 0 && !empty($fields)) {
            $orderBy .= "ORDER BY ";
            $orderBy .= implode(",", $fields);

            if($orderByAsc == 1) {
                $orderBy .= " ASC";
            } else {
                $orderBy .= " DESC";
            }
        }
        return $orderBy;
    }

    /***
     * @param string $limit (synonymous to batchsize)
     * @param string $offset (synonymous to startIndex)
     */
    public static function ConstructLimitOffsetStatement($limit = "", $offset = "") {
        $stmnt = "";
        if(strlen($limit) > 0) {
            $stmnt .= "LIMIT " . $limit;
        }
        if(strlen($offset) > 0) {
            $stmnt .= " OFFSET " . $offset;
        }
        return $stmnt;
    }

}