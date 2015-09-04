<?php
/**
 * User: Arris
 * Date: 24.08.15, time: 23:30
 */
require_once 'config/config.php';

/**
 * Соединяемся с БД и отдаем DB Handler
 * @todo: класс, инкапсулирующий работу с базой
 * @return PDO
 */
function DB_Connect()
{
    global $CONFIG;
    try {
        $dbh = new PDO($CONFIG['pdo_host'], $CONFIG['username'], $CONFIG['password']);
        $dbh->exec("SET NAMES utf8");
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
    return $dbh;
}

/**
 * Возвращаем количество ревизий у гекса
 * @param $handler
 * @param $coords_col
 * @param $coords_row
 */
function DB_GetRevisionsCount($handler, $coords_col, $coords_row)
{
    try {
        $sth = $handler->query("SELECT COUNT(id) AS count_id FROM lme_map_tiles_data WHERE hexcol = {$coords_col} AND hexrow = {$coords_row}", PDO::FETCH_ASSOC);

        $row = $sth->fetch(PDO::FETCH_ASSOC);

        $revisions_count = $row['count_id'];
    }
    catch (PDOException $e) {
        die($e->getMessage());
    }
    return $revisions_count;
}

/**
 * @param $dbh
 * @param $coords_col
 * @param $coords_row
 * @param $project_name
 * @param $map_name
 * @return array
 */
function DB_GetRevisionLast($dbh, $coords_col, $coords_row, $project_name, $map_name)
{
    try{

        $sth = $dbh->query("
            SELECT title, content, editor, edit_reason
            FROM lme_map_tiles_data
            WHERE hexcol = {$coords_col} AND hexrow = {$coords_row}
            AND project_name = '{$project_name}'
            AND map_name = '{$map_name}'
            ORDER BY edit_date DESC
            LIMIT 1");

        $row = $sth->fetch(PDO::FETCH_ASSOC);

        $info = array(
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
    return $info;
}

/**
 * @param $dbh
 * @param $revision_id
 * @return array
 */
function DB_GetRevisionById($dbh, $revision_id)
{
    try {
        $sth = $dbh->query("
            SELECT title, content, editor, edit_reason
            FROM lme_map_tiles_data
            WHERE id = {$revision_id}
            ");

        $row = $sth->fetch(PDO::FETCH_ASSOC);

        $info = array(
            'message'   =>  'Fork revision #'.$revision_id,
            'text'      =>  $row['content'],
            'title'     =>  $row['title'],
            'edit_reason'   =>  "".$row['edit_reason'],
            'editor_name'   =>  at($_COOKIE, 'kw_trpg_lme_auth_editorname', ""),
        );
    }
    catch (PDOException $e){
        die($e->getMessage());
    }
    return $info;
}

/**
 * @param $dbh
 * @param $coords_col
 * @param $coords_row
 * @return string
 */
function DB_GetRevisionsList($dbh, $coords_col, $coords_row)
{
    $revisions_string = '';
    try{
        $sth = $dbh->query("
SELECT id, hexcol, hexrow, hexcoords, DATE_FORMAT(FROM_UNIXTIME(edit_date), '%d-%m-%Y %H:%m:%s') AS edit_date, editor, edit_reason, ip
FROM lme_map_tiles_data
WHERE hexcol = {$coords_col} AND hexrow = {$coords_row}"
            , PDO::FETCH_ASSOC);

        while($row = $sth->fetch(PDO::FETCH_ASSOC)){
            $revisions_string .= sprintf(
                '<li><a href="edit.php?frontend=imagemap&col=%s&row=%s&hexcoord=%s&revision=%s">%s, %s</a> <em>(IP: %s)</em>: %s</li>'."\r\n",
                $row['hexcol'],
                $row['hexrow'],
                $row['hexcoords'],
                $row['id'],
                $row['edit_date'],
                $row['editor'],
                $row['ip'],
                $row['edit_reason']
            );
        }
        if ($revisions_string == '')
            $revisions_string = 'Это будет первая версия статьи!';

    }
    catch(PDOException $e) {
        die($e->getMessage());
    }
    return $revisions_string;
}