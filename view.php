<?php
/**
 * User: Arris
 * Date: 03.09.15, time: 21:18
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

$map_alias
    = isset($_GET['map'])
    ? $_GET['map']
    : die('No such map!');

/*
Теперь нужно по полученным данным (project_name, map_name) извлечь из таблиц
lme_projects    : настройки проекта
lme_maps        : настройки конкретной карты
Кстати, отображение карты - leaflet или канвас - тоже описывается в таблице lme_maps
*/

$dbh = DB_Connect();

// redirect to sandbox or project folder if map not exists
$is_this_exists = DB_checkProjectExists($dbh, $project_alias, $map_alias);
if (!$is_this_exists['project'] && $project_alias != 'sandbox')     redirect('/sandbox/map');
if (!$is_this_exists['map'] && $map_alias != 'map') redirect("/{$project_alias}");


$map_info = DB_GetMapInfo($dbh, $project_alias, $map_alias);
$map = $map_info['map'];

$template_data = $map;

$template_data['image_filename'] = "/storage/{$project_alias}/{$map['image_filename']}";
$template_data['leaflet_filename'] = "/storage/{$project_alias}/{$map['leaflet_filename']}";

if ($map_info['is_sandbox']) {
    $template_data['map_header'] = "Такой карты нет, но вы можете поиграться в песочнице!";
    $template_data['image_filename'] = "/storage/sandbox/{$map['image_filename']}";
    $template_data['leaflet_filename'] = "/storage/sandbox/{$map['leaflet_filename']}";
    $template_data['project_alias'] = 'sandbox';
    $template_data['map_alias'] = 'map';
    $template_data['mapis'] = 'sandbox';
} else {
    $template_data['map_header'] = $map['project_title'] . " -- " . $map['map_title'];
}

$tpl_file = 'view.canvas.html';

$html = websun_parse_template_path($template_data, $tpl_file, '$/template');

echo $html;