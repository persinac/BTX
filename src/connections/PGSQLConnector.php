<?php
/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 11/24/2017
 * Time: 5:00 PM
 */

namespace src\connections;

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require_once $root . '/vendor/autoload.php';

class PGSQLConnector
{
    public $pg;

    public function __construct() {
        $this->NewConnection();
    }
    /**
     * Connection functions
     */
    public function NewConnection()
    {
        $connectionString = sprintf('host=%s dbname=%s user=%s password=%s',
            BTX_DB_HOST, BTX_DB_NAME, BTX_DB_USERNAME, BTX_DB_PASSWORD);
        $this->pg = pg_connect($connectionString) or die('Connection failed: ' . pg_last_error());
        return $this->pg;
    }

    function CloseConnection($connection) {
        try {
            pg_close($connection);
            return true;
        } catch (Exception $e) {
            printf("Close connection failed: %s\n", $this->pg->error);
        }
    }
}