<?php
require_once '_required_lme.php';

$is_can_edit = auth_CanIEdit();

$coords_col = intval($_GET['col']);
$coords_row = intval($_GET['row']);
$hex_coords = $_GET['hexcoord'];

$project_alias
    = isset($_GET['project_alias'])
    ? $_GET['project_alias']
    : die('No such project!');

$map_alias
    = isset($_GET['map_alias'])
    ? $_GET['map_alias']
    : die('No such map!');

//
/* @todo: вероятно сейчас надо проверить настройку карты "показывать ли туман войны"
 * посетителю с данной ролью. Кроме того, вероятно следует объединить checkContent и
 * getContent в 1 функцию! *
 */


$revision = $db->getRevisionLast($coords_col, $coords_row, $project_alias, $map_alias);

$dbh = null;

$TEMPLATE_DATA = array(
    'tileinfo_title'        =>  $revision['title'],
    'tileinfo_text'         =>  $revision['text']
);

$tpl_file = 'get_content.ajaxed.html';

$html = websun_parse_template_path($TEMPLATE_DATA, $tpl_file, '$/template');

echo $html;
