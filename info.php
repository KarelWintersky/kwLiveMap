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

$is_this_exists = DB_checkProjectExists($dbh, $project_alias, 'map');

// если проект не существует - идем в песочницу
if (!$is_this_exists['project']) redirect('/sandbox');

$project_info = DB_loadProjectInfo($dbh, $project_alias);
// объединить две функции в одну. Если проекта нет - из базы придет пустой массив!
// Т.е. нет смысла проверять наличие проекта, а потом еще раз дергать данные
// к тому же устаревшее поведение - инфа о проекте лежит в lme_map_settings!


$maps_list = DB_getMapsListInProject($dbh, $project_alias);

$tpl_file = 'info.project.html';

$template_data = array(
    'project_alias' =>  $project_alias,
    'maps_list'     =>  $maps_list,
    'project_description'   =>  $project_info['description']
);

$html = websun_parse_template_path($template_data, $tpl_file, '$/template');

echo $html;



