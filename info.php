<?php
/**
 * User: Arris
 * Date: 03.09.15, time: 15:20
 */

require_once 'backend/config/config.php';
require_once 'backend/core.php';
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

$project_info = DB_getProjectInfo($dbh, $project_alias);
if (count($project_info) == 0) redirect('/sandbox');

$maps_list = DB_getMapsListAtProject($dbh, $project_alias);

$tpl_file = 'info.project.html';

$template_data = array(
    'project_alias' =>  $project_alias,
    'maps_list'     =>  $maps_list,
    'project_description'   =>  $project_info['description']
);

$html = websun_parse_template_path($template_data, $tpl_file, '$/template');

echo $html;



