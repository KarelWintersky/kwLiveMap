<?php
require_once 'backend/config/config.php';
require_once 'backend/core.php';
require_once 'backend/core.pdo.php';
require_once 'backend/websun.php';

require_once "backend/phpauth/languages/en_GB.php";
require_once "backend/phpauth/config.class.php";
require_once "backend/phpauth/auth.class.php";
global $CONFIG;

$dbh = DB_Connect();

$config = new PHPAuth\Config($dbh);
$auth   = new PHPAuth\Auth($dbh, $config, $lang);

$is_logged_in = (int)$auth->isLogged(); // 1 if logged-in, 0 elseether

// show template
$tpl_file = 'index.html';

$template_data = array(
    'is_logged_in'          =>  $is_logged_in
);

$html = websun_parse_template_path($template_data, $tpl_file, '$/template');

echo $html;

