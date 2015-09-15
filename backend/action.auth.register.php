<?php
/**
 * User: Arris
 * Date: 06.09.15, time: 16:35
 */
require_once 'config/config.php';
require_once 'core.php';
require_once 'core.pdo.php';
require_once 'websun.php';

require_once "phpauth/languages/en_GB.php";
require_once "phpauth/config.class.php";
require_once "phpauth/auth.class.php";

global $CONFIG;

$dbh = DB_Connect();

$config = new PHPAuth\Config($dbh);
$auth   = new PHPAuth\Auth($dbh, $config, $lang);

$additional_fields = array(
    'username'      =>  at($_POST, 'auth:reg_username', "Anonymous" ),
    'gender'        =>  at($_POST, 'auth:reg_gender', 'N'),
    'city'          =>  at($_POST, 'auth:reg_city', '')
);

$auth_result = $auth->register(
    $_POST['auth:reg_email'],
    $_POST['auth:reg_password'],
    $_POST['auth:reg_password_again'],
    $additional_fields
);

if (!$auth_result['error']) {
    // no errors
    setcookie('kw_livemap_last_logged_user', $_POST['auth:reg_email'],  time()+60*60*5, "/");
    $html_callback = '/activateaccount';
} else {
    $html_callback = '/register';
}

$template_data = array(
    'error_messages'    =>  $auth_result['message'],
    'html_callback_timeout' =>  10,
    'html_callback'     =>  $html_callback
);

$tpl_file = 'auth_callbacks/auth.callback.register.html';

$html = websun_parse_template_path($template_data, $tpl_file, '$/template');

echo $html;