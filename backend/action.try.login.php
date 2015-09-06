<?php
/**
 * User: Arris
 * Date: 06.09.15, time: 16:34
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

// не забыть сбросить куку kw_livemap_new_registred_username после логина

$dbh = DB_Connect();

$config = new PHPAuth\Config($dbh);
$auth   = new PHPAuth\Auth($dbh, $config, $lang);

// $remember_me = at($_POST, "auth:login_remember_me", 0);

$auth_result = $auth->login(
    $_POST["auth:login_email"],
    $_POST["auth:login_password"],
    at($_POST, "auth:login_remember_me", 0) );

if (!$auth_result['error']) {
    setcookie('kw_livemap_logged_in_session_hash', $auth_result['hash'],  time()+$auth_result['time'], "/");
    setcookie($config->__get('cookie_name'), $auth_result['hash'],  time()+$auth_result['time'], "/");
    unsetcookie('kw_livemap_new_registred_username');
    $html_callback = '/';
} else {
    $html_callback = '/login';
}

$template_data = array(
    'error_messages'    =>  $auth_result['message'],
    'html_callback_timeout' =>  10,
    'html_callback'     =>  $html_callback
);

$tpl_file = 'auth_callbacks/auth.callback.login.html';

$html = websun_parse_template_path($template_data, $tpl_file, '$/template');

echo $html;