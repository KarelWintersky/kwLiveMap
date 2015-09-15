<?php
/**
 * User: Arris
 * Date: 03.09.15, time: 15:20
 */

require_once 'backend/config/config.php';
require_once 'backend/core.php';
require_once 'backend/core.auth.php';
require_once 'backend/core.pdo.php';
require_once 'backend/websun.php';

require_once "backend/phpauth/languages/en_GB.php";
require_once "backend/phpauth/config.class.php";
require_once "backend/phpauth/auth.class.php";

$project_alias
    = isset($_GET['project'])
    ? $_GET['project']
    : die('No such project!');

$dbh = DB_Connect();

$is_this_exists = DB_checkProjectExists($dbh, $project_alias, 'map');
if (!$is_this_exists['project'] && $project_alias != 'sandbox')     redirect('/sandbox/map');


$tpl_file = 'info.project.html';

$template_data = array();

$html = websun_parse_template_path($template_data, $tpl_file, '$/template');

echo $html;



