<?php
namespace LiveMapEngine;
require_once('lme.config.class.php');


/**
 *
 */
class Auth extends Config
{
    private $connection;

    public function __construct()
    {
        parent::__construct();
        $this->connection = parent::getconnection();
    }



    public static function auth_CanIEdit()
    {
        return true;
    }

}
