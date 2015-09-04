<?php

require_once 'config/config.php';
require_once 'core/core.php';
require_once 'core/core.auth.php';
require_once 'core/core.pdo.php';
global $CONFIG;

$hex_coords = $_GET['hexcoord'];

$is_can_edit = auth_CanIEdit();

$coords_col = intval($_GET['col']);
$coords_row = intval($_GET['row']);

try {
    $dbh = new PDO($CONFIG['pdo_host'], $CONFIG['username'], $CONFIG['password']);
    $dbh->exec("SET NAMES utf8");
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    echo $e->getMessage();
}

try {
    $sth = $dbh->query("
    SELECT hexcoords, hexcol, hexrow
    FROM lme_map_tiles_data
    GROUP BY hexcoords
    ", PDO::FETCH_ASSOC);

    $revealed = array();

    while($row = $sth->fetch(PDO::FETCH_OBJ)){
        $zarea = 'z'.$row->hexcoords;
        $revealed[$zarea] = array(
            'col'   =>  $row->hexcol,
            'row'   =>  $row->hexrow
        );
    }
}
catch (PDOException $e) {
    die($e->getMessage());
}

$dbh = null;

print(json_encode($revealed));