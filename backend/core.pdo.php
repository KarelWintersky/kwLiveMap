<?php
/**
 * User: Arris
 * Date: 24.08.15, time: 23:30
 */
require_once 'config/config.php';

/** @todo: класс, инкапсулирующий работу с базой */

/**
 * Соединяемся с БД и отдаем DB Handler
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
 * @todo: ?
 * @param $dbh
 */
function DB_Close(&$dbh)
{
    $dbh = null;
}

/**
 * Возвращаем количество ревизий у гекса
 * @param $handler
 * @param $coords_col
 * @param $coords_row
 * @param $project_name
 * @param $map_name
 * @return mixed
 */
function DB_GetRevisionsCount($handler, $coords_col, $coords_row, $project_name, $map_name)
{
    //@todo: перейти на prepared statement
    try {
        $sth = $handler->query("SELECT COUNT(id)
        AS count_id
        FROM lme_map_tiles_data
        WHERE hexcol = {$coords_col}
        AND hexrow = {$coords_row}
        AND project_name = '{$project_name}'
        AND map_name = '{$map_name}'
        ", PDO::FETCH_ASSOC);

        $row = $sth->fetch(PDO::FETCH_ASSOC);

        $revisions_count = $row['count_id'];
    }
    catch (PDOException $e) {
        die($e->getMessage());
    }
    return $revisions_count;
}


function DB_GetRevisionLast($dbh, $coords_col, $coords_row, $project_name, $map_name)
{
    //@todo: перейти на prepared statement
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
            'editor_name'   =>  $row['editor'],
        );

    }
    catch (PDOException $e) {
        die(__LINE__ . $e->getMessage());
    }
    return $info;
}


function DB_GetRevisionById($dbh, $revision_id)
{
    //@todo: перейти на prepared statement
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


function DB_GetListRevisions($dbh, $coords_col, $coords_row, $project_name, $map_name)
{
    //@todo: перейти на prepared statement
    $revisions_string = '';
    try{
        $sth = $dbh->query("
SELECT id, hexcol, hexrow, hexcoords, DATE_FORMAT(FROM_UNIXTIME(edit_date), '%d-%m-%Y %H:%m:%s') AS edit_date, editor, edit_reason, ip
FROM lme_map_tiles_data
WHERE hexcol = {$coords_col}
AND hexrow = {$coords_row}
AND project_name = '{$project_name}'
AND map_name = '{$map_name}'
"
            , PDO::FETCH_ASSOC);

        while($row = $sth->fetch(PDO::FETCH_ASSOC)){
            //@todo: изменить ссылку!!!
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


function DB_UpdateHexTile($dbh, $data)
{
    try{
        $sth = $dbh->prepare("INSERT INTO lme_map_tiles_data (hexcol, hexrow, hexcoords, title, content, editor, edit_date, edit_reason, ip, project_id, project_name, map_id, map_name)
                          VALUES (:hexcol, :hexrow, :hexcoords, :title, :content, :editor, :edit_date, :edit_reason, :ip, :project_id, :project_name, :map_id, :map_name)");

        $success = $sth->execute($data);
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
    return $success;

}


function DB_GetRevealedTiles($dbh, $project_name, $map_name)
{
    //@todo: перейти на prepared statement
    $revealed = array();
    try {
        $sth = $dbh->query("
    SELECT hexcoords, hexcol, hexrow
    FROM lme_map_tiles_data
    WHERE project_name = '{$project_name}'
    AND map_name = '{$map_name}'
    GROUP BY hexcoords
    ", PDO::FETCH_ASSOC);

        while($row = $sth->fetch(PDO::FETCH_ASSOC)){
            $zarea = 'z'.$row['hexcoords'];
            $revealed[$zarea] = array(
                'col'   =>  $row['hexcol'],
                'row'   =>  $row['hexrow']
            );
        }
    }
    catch (PDOException $e) {
        die($e->getMessage());
    }
    return $revealed;
}

