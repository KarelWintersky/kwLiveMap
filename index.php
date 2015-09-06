<?php
require_once "backend/phpauth/languages/en_GB.php";
require_once "backend/phpauth/config.class.php";
require_once "backend/phpauth/auth.class.php";

require_once 'backend/config/config.php';
require_once 'backend/core.php';
require_once 'backend/core.pdo.php';
global $CONFIG;

$config = new PHPAuth\Config($dbh);
$auth   = new PHPAuth\Auth($dbh, $config, $lang);

$logged_in_status = '';

if(!isset($_COOKIE[$config->cookie_name]) || !$auth->checkSession($_COOKIE[$config->cookie_name])) {
    $logged_in_status = 'logged_in';
} else {
    $logged_in_status = 'logged_out';
}


// show template
$tpl_file = 'index.html';

$template_data = array(
    'user_status'           =>  $logged_in_status,
);

$html = websun_parse_template_path($template_data, $tpl_file, '$/template');

echo $html;

