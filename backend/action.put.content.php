<?php
require_once 'config/config.php';
require_once 'core.php';
require_once 'core.auth.php';
require_once 'core.pdo.php';
require_once 'websun.php';

global $CONFIG;

// check access rights
$is_can_edit = auth_CanIEdit();
if (!$is_can_edit) die('Hacking attempt!');

$project_name
    = isset($_POST['project_name'])
    ? $_POST['project_name']
    : die('No such project!');

$map_name
    = isset($_POST['map_name'])
    ? $_POST['map_name']
    : die('No such map!');

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
    'project_name'  =>  $project_name,
    'map_id'        =>  0,
    'map_name'      =>  $map_name
);

if ($data['editor'] != '')
    setcookie('kw_trpg_lme_auth_editorname', $data['editor'],  time()+60*60*24*7, "/{$project_name}/");

$dbh = DB_Connect();

DB_UpdateHexTile($dbh, $data);

$dbh = null;

$TEMPLATE_DATA = array(
    'html_callback'         =>  "/{$project_name}/{$map_name}",
    'html_callback_timeout' =>  500,
);

$tpl_file = '../template/put_content.callback.html';

$html = websun_parse_template_path($TEMPLATE_DATA, $tpl_file);

echo $html;
