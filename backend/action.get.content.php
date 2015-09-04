<?php
require_once 'config/config.php';
require_once 'core.php';
require_once 'core.auth.php';
require_once 'core.pdo.php';
require_once 'websun.php';
global $CONFIG;

$is_can_edit = auth_CanIEdit();

$coords_col = intval($_GET['col']);
$coords_row = intval($_GET['row']);
$hex_coords = $_GET['hexcoord'];

$project_name
    = isset($_GET['project_name'])
    ? $_GET['project_name']
    : die('No such project!');

$map_name
    = isset($_GET['map_name'])
    ? $_GET['map_name']
    : die('No such map!');

$dbh = DB_Connect();

$revision = DB_GetRevisionLast($dbh, $coords_col, $coords_row, $project_name, $map_name);

$dbh = null;

$TEMPLATE_DATA = array(
    'tileinfo_title'        =>  $revision['title'],
    'tileinfo_text'         =>  $revision['text']
);

$tpl_file = 'get_content.ajaxed.html';

$html = websun_parse_template_path($TEMPLATE_DATA, $tpl_file, '$/template');

echo $html;
