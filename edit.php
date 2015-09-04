<?php
require_once 'backend/config/config.php';
require_once 'backend/core/core.php';
require_once 'backend/core/core.auth.php';
require_once 'backend/core/core.pdo.php';
global $CONFIG;

var_dump($_GET);

// init null values
$revisions_string = '';

// check access rights
$is_can_edit = auth_CanIEdit();

// check callback
$html_callback = ($_GET['frontend'] == 'canvas') ? '/map/index.html' : '/map/leaflet.html';

$coords_col = intval($_GET['col']);
$coords_row = intval($_GET['row']);

// коннект с базой
try {
    $dbh = new PDO($CONFIG['pdo_host'], $CONFIG['username'], $CONFIG['password']);
    $dbh->exec("SET NAMES utf8");
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    echo $e->getMessage();
}


// проверяем, сколько ревизий текста у гекса
try {
    $sth = $dbh->query("SELECT COUNT(id) AS count_id FROM lme_map_tiles_data WHERE `hexcol` = {$coords_col} AND `hexrow` = {$coords_row}", PDO::FETCH_ASSOC);

    $row = $sth->fetch(PDO::FETCH_ASSOC);

    $revisions_count = $row['count_id'];
}
catch (PDOException $e) {
    die($e->getMessage());
}

// в зависимости от количества ревизий заполняем данные шаблона
if ($revisions_count != 0) {

    // если во входящих параметрах нет идентификатора ревизии - загружаем последнюю
    if (!isset($_GET['revision'])) {

        // загружаем последнюю ревизию
        try{

            $sth = $dbh->query("
            SELECT title, content, editor, edit_reason
            FROM lme_map_tiles_data
            WHERE hexcol = {$coords_col} AND hexrow = {$coords_row}
            ORDER BY edit_date DESC
            LIMIT 1");

            $row = $sth->fetch(PDO::FETCH_ASSOC);

            $template = array(
                'message'   =>  '',
                'text'      =>  $row['content'],
                'title'     =>  $row['title'],
                'edit_reason'   =>  $row['edit_reason'],
                'editor_name'   =>  at($_COOKIE, 'kw_trpg_lme_auth_editorname', ""),
            );

        }
        catch (PDOException $e) {
            die(__LINE__ . $e->getMessage());
        }
    } else {
        // загружаем нужную ревизию по идентификатору
        $revision = $_GET['revision'];
        try {
            $sth = $dbh->query("
            SELECT title, content, editor, edit_reason
            FROM lme_map_tiles_data
            WHERE id = {$revision}
            ");

            $row = $sth->fetch(PDO::FETCH_ASSOC);

            $template = array(
                'message'   =>  'Fork revision #'.$revision,
                'text'      =>  $row['content'],
                'title'     =>  $row['title'],
                'edit_reason'   =>  "".$row['edit_reason'],
                'editor_name'   =>  at($_COOKIE, 'kw_trpg_lme_auth_editorname', ""),
            );


        }
        catch (PDOException $e){
            die($e->getMessage());
        }

    }


    // выгружаем список ревизий

    try{
        $sth = $dbh->query("
SELECT id, hexcol, hexrow, hexcoords, DATE_FORMAT(FROM_UNIXTIME(edit_date), '%d-%m-%Y %H:%m:%s') AS edit_date, editor, edit_reason, ip
FROM lme_map_tiles_data
WHERE hexcol = {$coords_col} AND hexrow = {$coords_row}"
            , PDO::FETCH_OBJ);

        while($row = $sth->fetch(PDO::FETCH_OBJ)){
            $revisions_string .= sprintf(
                '<li><a href="edit.php?frontend=imagemap&col=%s&row=%s&hexcoord=%s&revision=%s">%s, %s</a> <em>(IP: %s)</em>: %s</li>'."\r\n",
                $row->hexcol,
                $row->hexrow,
                $row->hexcoords,
                $row->id,
                $row->edit_date,
                $row->editor,
                $row->ip,
                $row->edit_reason
            );
        }
        if ($revisions_string == '')
            $revisions_string = 'Это будет первая версия статьи!';

    }
    catch(PDOException $e) {
        die($e->getMessage());
    }
} else {
    // заполняем данные
    $template = array(
        'text'      =>  '',
        'title'     =>  '',
        'edit_reason'   =>  'Первое редактирование',
        'editor_name'   =>  at($_COOKIE, 'kwtrpglme_auth_editorname', ''),
    );
}

$dbh = null;
