<?php
/**
 * User: Arris
 * Date: 06.09.15, time: 16:36
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

$auth_result = $auth->requestReset($_POST['auth:recover_email']);

$html_callback
    = ($auth_result['error'])
    ? '/recover'
    : '/';

$template_data = array(
    'error_messages'    =>  $auth_result['message'],
    'html_callback_timeout' =>  10,
    'html_callback'     =>  $html_callback
);

$tpl_file = 'auth_callbacks/auth.callback.recover.html';

$html = websun_parse_template_path($template_data, $tpl_file, '$/template');



echo $html;