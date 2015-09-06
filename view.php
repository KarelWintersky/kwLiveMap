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

$project_name
    = isset($_GET['project'])
    ? $_GET['project']
    : die('No such project!');

$map_name
    = isset($_GET['map'])
    ? $_GET['map']
    : die('No such map!');

/*
Теперь нужно по полученным данным (project_name, map_name) извлечь из таблиц
lme_projects    : настройки проекта
lme_maps        : настройки конкретной карты

Кстати, отображение карты - leaflet или канвас - тоже описывается в таблице lme_maps


*/

$TEMPLATE_DATA = array(
    // project variables
    'project_title'     =>  "Trollfjorden -- Троллячьи фьорды",
    'project_name'      =>  "trollfjorden",

    // map variables
    'map_title'         =>  "основная карта",
    'map_name'          =>  "map",
    'map_imagefile'     =>  "/storage/trollfjorden/trollfjorden_l.png",
    'map_max_col'       =>  23,
    'map_max_row'       =>  16,
    'map_bordersize'    =>  1,
    'map_grid_edge'     =>  29.82,
    'map_grid_height'   =>  51.62,
    'map_grid_type'     =>  'hex:x_oriented',
    'map_fog_hidden'    =>  0.2
);

$tpl_file = 'view.canvas.html';

$html = websun_parse_template_path($TEMPLATE_DATA, $tpl_file, '$/template');

echo $html;