<?php
require_once '_required_lme.php';

$config = new LiveMapEngine\Config();
$db     = new LiveMapEngine\DB();
$dbh    = $config->getconnection();

$authconfig = new PHPAuth\Config($dbh);
$auth       = new PHPAuth\Auth($dbh, $authconfig, $lang);

$is_can_edit = auth_CanIEdit();

$project_alias
    = isset($_GET['project_alias'])
    ? $_GET['project_alias']
    : die('No such project!');

$map_alias
    = isset($_GET['map_alias'])
    ? $_GET['map_alias']
    : die('No such map!');

$revealed = $db->getRevealedTiles($project_alias, $map_alias);

$dbh = null;

print(json_encode($revealed));