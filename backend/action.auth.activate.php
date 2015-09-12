<?php
/**
 * User: Arris
 * Date: 07.09.15, time: 1:37
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

$auth_result = $auth->activate($_POST['auth:activate_key']);

$html_callback
    = ($auth_result['error'])
    ? '/'
    : '/login';

$template_data = array(
    'error_messages'    =>  $auth_result['message'],
    'html_callback_timeout' =>  10,
    'html_callback'     =>  $html_callback
);

$tpl_file = 'auth_callbacks/auth.callback.activate.html';

$html = websun_parse_template_path($template_data, $tpl_file, '$/template');

echo $html;