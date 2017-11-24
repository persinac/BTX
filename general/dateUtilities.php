<?php
/**
 * Created by PhpStorm.
 * User: apersinger
 * Date: 5/11/2017
 * Time: 4:00 PM
 */
class DateUtilities
{

    private function __construct () { }

    /**
     * @return bool|string
     */
    public static function GenerateCurrentMonth() {
        return date('m', time());
    }

    public static function GenerateCurrentYear() {
        return date('Y', time());
    }

    public static function BuildSpecificDate($mth, $day, $year) {
        return date('m/d/Y', strtotime($mth . '/'. $day . '/' . $year));
    }

    /***
     * Returns the string value of the current month in all caps
     * @return string
     */
    public static function GetCurrentMonth()
    {
        $now = new \DateTime('now');
        $rawMonth = $now->format('m');
        $month = 'NA';
        if ($rawMonth == '01' || $rawMonth == '1') {
            $month = 'JANUARY';
        } elseif ($rawMonth == '02' || $rawMonth == '2') {
            $month = 'FEBRUARY';
        } elseif ($rawMonth == '03' || $rawMonth == '3') {
            $month = 'MARCH';
        } elseif ($rawMonth == '04' || $rawMonth == '4') {
            $month = 'APRIL';
        } elseif ($rawMonth == '05' || $rawMonth == '5') {
            $month = 'MAY';
        } elseif ($rawMonth == '06' || $rawMonth == '6') {
            $month = 'JUNE';
        } elseif ($rawMonth == '07' || $rawMonth == '7') {
            $month = 'JULY';
        } elseif ($rawMonth == '08' || $rawMonth == '8') {
            $month = 'AUGUST';
        } elseif ($rawMonth == '09' || $rawMonth == '9') {
            $month = 'SEPTEMBER';
        } elseif ($rawMonth = '10') {
            $month = 'OCTOBER';
        } elseif ($rawMonth == '11') {
            $month = 'AUGUST';
        } else {
            $month = 'DECEMBER';
        }

        return $month;
    }

    public static function GetCurrentYear()
    {
        $now = new \DateTime('now');
        $rawYear = $now->format('y');
        return $rawYear;
    }

    public static function GenerateLowHighDateRange()
    {
        $details = new stdClass();
        $d = strtotime("tomorrow");
        $details->low = date("m/d/Y", $d);
        $d = strtotime("+9 Days");
        $details->high = date("m/d/Y", $d);

        return $details;
    }
}