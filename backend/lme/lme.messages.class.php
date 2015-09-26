<?php
namespace LiveMapEngine;
require_once('lme.config.class.php');

/**
 *
 */
class LMEMessages extends Config
{
    public function __construct()
    {
        parent::__construct();
        $this->connection = parent::getconnection();
    }


}
