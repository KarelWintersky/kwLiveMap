<?php
require_once 'config/config.php';
require_once 'core.php';
require_once 'core.auth.php';
require_once 'core.pdo.php';

$is_can_edit = auth_CanIEdit();

$dbh = DB_Connect();

$project_alias
    = isset($_GET['project_alias'])
    ? $_GET['project_alias']
    : die('No such project!');

$map_alias
    = isset($_GET['map_alias'])
    ? $_GET['map_alias']
    : die('No such map!');

$revealed = DB_GetRevealedTiles($dbh, $project_alias, $map_alias);

$dbh = null;

print(json_encode($revealed));