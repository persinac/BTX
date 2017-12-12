<?php
/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 10/30/2017
 * Time: 9:02 PM
 */

namespace src\CRUD;

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require_once $root . '/vendor/autoload.php';

//use BTX\Connections\PGSQLConnector;

class BTXMaster
{
    var $htmlTagCounter;
    var $btxMasterSQL;
    function __construct($pgconn)
    {
        $this->btxMasterSQL = $pgconn;
    }

    public function GetMySQLObj() {
        return $this->btxMasterSQL->btxMasterSQL;
    }
}