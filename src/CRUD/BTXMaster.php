<?php
/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 10/30/2017
 * Time: 9:02 PM
 */

namespace CRUD;

//use \HTMLTagCounter as HTMLTagCounter

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require_once $root . '/vendor/autoload.php';

class BTXMaster
{
    var $htmlTagCounter;
    var $btxMasterSQL;
    function __construct()
    {
        $this->btxMasterSQL = new \PGSQLConnector ();
    }

    public function GetMySQLObj() {
        return $this->btxMasterSQL->mys;
    }
}