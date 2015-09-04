<?php
require_once 'config/config.php';
require_once 'core.php';
require_once 'core.auth.php';
require_once 'core.pdo.php';
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
    SELECT title, content
    FROM lme_map_tiles_data
    WHERE hexcol = {$coords_col} AND hexrow = {$coords_row}
    ORDER BY edit_date DESC
    LIMIT 1", PDO::FETCH_ASSOC);

    $row = $sth->fetch(PDO::FETCH_ASSOC);

    $template = array(
        'text'      =>  $row['content'],
        'title'     =>  $row['title'],
        'edit_reason'   =>  $row['edit_reason'],
        'editor_name'   =>  at($_COOKIE, 'kwtrpglme_auth_editorname', $row['editor']),
    );

}
catch (PDOException $e) {
    die($e->getMessage());
}

$dbh = null;

?>
<hr>
<fieldset class="region-content">
    <legend> <?php echo $template['title']; ?> </legend>

    <?php echo $template['text']; ?>
</fieldset>


