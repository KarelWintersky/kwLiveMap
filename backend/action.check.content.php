<?php
require_once 'config/config.php';
require_once 'core.php';
require_once 'core.auth.php';
require_once 'core.pdo.php';

global $CONFIG;

$is_can_edit = auth_CanIEdit();

$col = intval($_GET['col']);
$row = intval($_GET['row']);

$project_name
    = isset($_GET['project_name'])
    ? $_GET['project_name']
    : die('No such project!');

$map_name
    = isset($_GET['map_name'])
    ? $_GET['map_name']
    : die('No such map!');

$dbh = DB_Connect();

$check_result = DB_GetRevisionsCount($dbh, $col, $row, $project_name, $map_name);

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