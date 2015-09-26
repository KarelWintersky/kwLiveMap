<?php
require_once '_required_libs.php';

global $CONFIG;

// check access rights
$is_can_edit = auth_CanIEdit();
if (!$is_can_edit) die('Hacking attempt!');

$project_alias
    = isset($_POST['project_alias'])
    ? $_POST['project_alias']
    : die('No such project!');

$map_alias
    = isset($_POST['map_alias'])
    ? $_POST['map_alias']
    : die('No such map!');

// получить project_id & map_id по алиасам!

$data = array(
    'hexcol'        =>  intval($_POST['hexcoord_col']),
    'hexrow'        =>  intval($_POST['hexcoord_row']),
    'hexcoords'     =>  $_POST['hexcoords'],
    'title'         =>  $_POST['title'],
    'content'       =>  $_POST['textdata'],
    'editor'        =>  $_POST['editor_name'],
    'edit_date'     =>  time(),
    'edit_reason'   =>  $_POST['edit_reason'],
    'ip'            =>  $_SERVER['REMOTE_ADDR'],
    'project_id'    =>  1,
    'project_alias' =>  $project_alias,
    'map_id'        =>  1,
    'map_alias'     =>  $map_alias
);


$dbh = DB_Connect();

$put_result = DB_UpdateHexTile($dbh, $data);

$dbh = null;

if ($data['editor'] != '')
    setcookie('kw_trpg_lme_auth_editorname', $data['editor'],  time()+60*60*24*7, "/{$project_alias}/");

unsetcookie('kwlme_filemanager_storagepath');

$TEMPLATE_DATA = array(
    'html_callback'         =>  "/{$project_alias}/{$map_alias}",
    'html_callback_timeout' =>  10,
);

$tpl_file = 'put_content.callback.html';

$html = websun_parse_template_path($TEMPLATE_DATA, $tpl_file, '$/template');

echo $html;
