<?php
require_once '_required_lme.php';

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

$check_result = $db->getRevisionsCount($col, $row, $project_alias, $map_alias);

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