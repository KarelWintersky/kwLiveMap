<?php
require_once 'config/config.php';
require_once 'core.php';
require_once 'core.auth.php';
require_once 'core.pdo.php';
global $CONFIG;

$is_can_edit = auth_CanIEdit();

$col = intval($_GET['col']);
$row = intval($_GET['row']);

try {
    $dbh = new PDO($CONFIG['pdo_host'], $CONFIG['username'], $CONFIG['password']);
    // $dbh = new PDO('mysql:host=localhost;dbname=kwdb', 'root', 'password' /* , array( PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8' ) */);
    $dbh->exec("SET NAMES utf8");
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    echo $e->getMessage();
}

try {
    $sth = $dbh->query("SELECT COUNT(id) AS count_id FROM lme_map_tiles_data WHERE `hexcol` = {$col} AND `hexrow` = {$row}",
        PDO::FETCH_ASSOC);

    $row = $sth->fetch(PDO::FETCH_ASSOC);

    if ($row['count_id'] == 0) {
        $ret = ($is_can_edit) ? 'empty' : 'ignore';
    } else {
        $ret = 'anydata';
    }
}
catch (PDOException $e) {
    die($e->getMessage());
}

$data = array(
    'error'     =>  0,
    'result'    =>  $ret
);
$dbh = null;

print(json_encode($data));