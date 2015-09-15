<?php
require_once 'config/config.php';
require_once 'core.php';
require_once 'core.pdo.php';

global $CONFIG;

$is_can_edit = auth_CanIEdit();

$col = intval($_GET['col']);
$row = intval($_GET['row']);

$project_alias
    = isset($_GET['project_alias'])
    ? $_GET['project_alias']
    : die('No such project!');

$map_alias
    = isset($_GET['map_alias'])
    ? $_GET['map_alias']
    : die('No such map!');

$dbh = DB_Connect();

$check_result = DB_GetRevisionsCount($dbh, $col, $row, $project_alias, $map_alias);

if ($check_result == 0) {
    $ret = ($is_can_edit) ? 'empty' : 'ignore';
} else {
    $ret = 'anydata';
}

$data = array(
    'error'     =>  0,
    'result'    =>  $ret
);
$dbh = null;

print(json_encode($data));