<?php
require_once 'config/config.php';
require_once 'core.php';
require_once 'core.auth.php';
require_once 'core.pdo.php';

$is_can_edit = auth_CanIEdit();

$dbh = DB_Connect();

$project_name
    = isset($_GET['project_name'])
    ? $_GET['project_name']
    : die('No such project!');

$map_name
    = isset($_GET['map_name'])
    ? $_GET['map_name']
    : die('No such map!');

$revealed = DB_GetRevealedTiles($dbh, $project_name, $map_name);

$dbh = null;

print(json_encode($revealed));