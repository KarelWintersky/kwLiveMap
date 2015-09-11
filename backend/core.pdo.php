<?php
/**
 * User: Arris
 * Date: 24.08.15, time: 23:30
 */
require_once 'config/config.php';

/**
 * Соединяемся с БД и отдаем DB Handler
 * @return PDO
 */
function DB_Connect()
{
    global $CONFIG;
    try {
        $dbh = new \PDO($CONFIG['pdo_host'], $CONFIG['username'], $CONFIG['password']);
        $dbh->exec("SET NAMES utf8");
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
    return $dbh;
}

/**
 * Закрываем соединение с базой
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
function DB_GetRevisionsCount(\PDO $handler, $coords_col, $coords_row, $project_name, $map_name)
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

/**
 * Возвращаем последнюю ревизию информации о гексе
 * @param $dbh
 * @param $coords_col
 * @param $coords_row
 * @param $project_name
 * @param $map_name
 * @return array
 */
function DB_GetRevisionLast(\PDO $dbh, $coords_col, $coords_row, $project_name, $map_name)
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

/**
 * Возвращаем ревизию информации о гексе по айди
 * @param $dbh
 * @param $revision_id
 * @return array
 */
function DB_GetRevisionById(\PDO $dbh, $revision_id)
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

/**
 * Возвращаем список ревизий в виде строки из <LI>ссылок</LI>
 * @todo: изменить ссылку!!!
 * @param $dbh
 * @param $coords_col
 * @param $coords_row
 * @param $project_name
 * @param $map_name
 * @return string
 */
function DB_GetListRevisions(\PDO $dbh, $coords_col, $coords_row, $project_name, $map_name)
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

/**
 * Апдейтит в базу информацию по гексику
 * @param $dbh
 * @param $data
 * @return mixed
 */
function DB_UpdateHexTile(\PDO $dbh, $data)
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

/**
 * Возвращает массив из открытых гексов (для тумана войны)
 * @param $dbh
 * @param $project_name
 * @param $map_name
 * @return array
 */
function DB_GetRevealedTiles(\PDO $dbh, $project_name, $map_name)
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
