<?php

/**
 * Created by PhpStorm.
 * User: apersinger
 * Date: 10/27/17
 */

class MySQLConnector
{
    public $mys;

    public function __construct($host, $user, $pass, $database) {
        $this->NewConnection($host, $user, $pass, $database);
    }
    /**
     * Connection functions
     */
    public function NewConnection($host, $user, $pass, $database)
    {
        $this->mys = mysqli_connect($host, $user, $pass, $database);
        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }
        return $this->mys;
    }

    function CloseConnection($connection) {
        try {
            mysqli_close($connection);
            return true;
        } catch (Exception $e) {
            printf("Close connection failed: %s\n", $this->mys->error);
        }
    }

}