<?php
/**
 * User: Arris
 * Date: 03.09.15, time: 15:20
 */

require_once 'backend/_required_lme.php';

$config = new LiveMapEngine\Config();
$db     = new LiveMapEngine\DB();
$dbh    = $config->getconnection();

$authconfig = new PHPAuth\Config($dbh);
$auth       = new PHPAuth\Auth($dbh, $authconfig, $lang);

$project_alias
    = isset($_GET['project'])
    ? $_GET['project']
    : die('No such project!');

$project_info = $db->getProjectInfo($project_alias);
if (count($project_info) == 0) redirect('/sandbox');

$maps_list = $db->getMapsListAtProject($project_alias);

$tpl_file = 'project.html';

$template_data = array(
    'project_alias'         =>  $project_alias,
    'maps_list'             =>  $maps_list,
    'project_description'   =>  $project_info['description']
);

$html = websun_parse_template_path($template_data, $tpl_file, '$/template');

echo $html;



