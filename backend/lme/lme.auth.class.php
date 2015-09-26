<?php
namespace LiveMapEngine;
require_once('lme.config.class.php');


/**
 *
 */
class Auth extends Config
{




    public static function auth_CanIEdit()
    {
        return true;
    }

}
