<?php
require_once 'config/config.php';
require_once 'core.php';
require_once 'core.auth.php';
require_once 'core.pdo.php';
require_once 'websun.php';

global $CONFIG;

// var_dump($_POST);

// check access rights
$is_can_edit = auth_CanIEdit();
if (!$is_can_edit) die('Hacking attempt!');

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
    'project_name'  =>  'trollfjorden',
    'map_id'        =>  0,
    'map_name'      =>  'map'
);

if ($data['editor'] != '')
    setcookie('kw_trpg_lme_auth_editorname', $data['editor'],  time()+60*60*24*7, '/trollfjorden/');

try {
    $dbh = new PDO($CONFIG['pdo_host'], $CONFIG['username'], $CONFIG['password']);
    $dbh->exec("SET NAMES utf8");
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    echo $e->getMessage();
}

try{
    $sth = $dbh->prepare("INSERT INTO lme_map_tiles_data (hexcol, hexrow, hexcoords, title, content, editor, edit_date, edit_reason, ip, project_id, project_name, map_id, map_name)
                          VALUES (:hexcol, :hexrow, :hexcoords, :title, :content, :editor, :edit_date, :edit_reason, :ip, :project_id, :project_name, :map_id, :map_name)");

    $success = $sth->execute($data);
}
catch (PDOException $e) {
    echo $e->getMessage();
}

$dbh = null;

$TEMPLATE_DATA = array(
    'html_callback'         =>  '/trollfjorden/map',
    'html_callback_timeout' =>  500,
);

$tpl_file = '../template/put_content.callback.html';

$html = websun_parse_template_path($TEMPLATE_DATA, $tpl_file);

echo $html;
