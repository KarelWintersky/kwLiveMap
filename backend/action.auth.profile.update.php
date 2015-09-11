<?php
/**
 * User: Arris
 * Date: 11.09.15, time: 9:54
 */


require_once 'config/config.php';
require_once 'core.php';
require_once 'core.auth.php';
require_once 'core.pdo.php';
require_once 'websun.php';

require_once "phpauth/languages/en_GB.php";
require_once "phpauth/config.class.php";
require_once "phpauth/auth.class.php";

global $CONFIG;

$dbh = DB_Connect();

$config = new PHPAuth\Config($dbh);
$auth   = new PHPAuth\Auth($dbh, $config, $lang);

$auth_result = array();
$html_callback = '';


switch ($_POST['auth:editprofile:submit']) {
    case 'update_personal_data' : {
        // update personal data


        break;
    }
    case 'change_email': {
        // Change Password: $auth->changeEmail($uid, $email, $password)
        break;
    }
    case 'change_password': {
        // Change Password: $auth->changePassword($uid, $curr, $new, $new2)
        break;
    }
    case 'delete_account': {
        // $auth->deleteUser($id, $password)
        break;
    }
    default: {
        break;
    }
}




$template_data = array(
    'error_messages'    =>  $auth_result['message'],
    'html_callback_timeout' =>  1000,
    'html_callback'     =>  $html_callback
);
// --------
$tpl_file = 'auth_callbacks/auth.callback.activate.html';

$html = websun_parse_template_path($template_data, $tpl_file, '$/template');

echo $html;
 
