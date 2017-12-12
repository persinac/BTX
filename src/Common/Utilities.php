<?php
/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 11/26/2017
 * Time: 2:30 PM
 */

function CalculateUSDValue($usdtBTCVal, $value) {
    $retVal = 0.00;
    $retVal = $usdtBTCVal * $value;
    return $retVal;
}