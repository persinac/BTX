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