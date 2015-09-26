<?php
/**
 * User: Arris
 * Date: 03.09.15, time: 21:18
 */
require_once 'backend/_required_libs.php';

$project_alias
    = isset($_GET['project'])
    ? $_GET['project']
    : die('No such project!');

$map_alias
    = isset($_GET['map'])
    ? $_GET['map']
    : die('No such map!');

$dbh = DB_Connect();

// загружаем информацию по карте и если такой карты нет - идем в корень текущего проекта
// ну а если окажется, что и проекта нет - мы пойдем в песочницу
$map_info = DB_GetMapInfo($dbh, $project_alias, $map_alias);
if ( !$map_info['existance'] ) redirect("/{$project_alias}");

$project_info = DB_getProjectInfo($dbh, $project_alias);

$map = $map_info['map'];

$template_data = $map;

$template_data = array_merge($template_data, array(
    'project_title'     =>  $project_info['project_title'],
    'image_filename'    =>  "/storage/{$project_alias}/{$map['image_filename']}",
    'leaflet_filename'  =>  "/storage/{$project_alias}/{$map['leaflet_filename']}",
    'map_header'        =>  $project_info['project_title'] . " -- " . $map['map_title'],
    'copyright'         =>  getCopyright()
));

$tpl_file = "view.{$map['view_style']}.html";

$html = websun_parse_template_path($template_data, $tpl_file, '$/template');

echo $html;