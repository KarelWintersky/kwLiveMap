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
 * @param $dbh
 * @param $coords_col
 * @param $coords_row
 * @param $project_alias
 * @param $map_alias
 * @return mixed
 */
function DB_GetRevisionsCount(\PDO $dbh, $coords_col, $coords_row, $project_alias, $map_alias)
{
    try {
        $query = "SELECT COUNT(id)
        AS count_id
        FROM lme_map_tiles_data
        WHERE hexcol = :coords_col
        AND   hexrow = :coords_row
        AND   project_alias = :project_alias
        AND   map_alias = :map_alias ";
        $sth = $dbh->prepare($query);
        $sth->execute(array(
            'coords_col'    =>  $coords_col,
            'coords_row'    =>  $coords_row,
            'project_alias' =>  $project_alias,
            'map_alias'     =>  $map_alias
        ));

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
 * @param $project_alias
 * @param $map_alias
 * @return array
 */
function DB_GetRevisionLast(\PDO $dbh, $coords_col, $coords_row, $project_alias, $map_alias)
{
    try{
        $query = "
            SELECT title, content, editor, edit_reason
            FROM lme_map_tiles_data
            WHERE hexcol = :coords_col AND hexrow = :coords_row
            AND project_alias = :project_alias
            AND map_alias = :map_alias
            ORDER BY edit_date DESC
            LIMIT 1";

        $sth = $dbh->prepare($query);
        $sth->execute(array(
            'coords_col'    =>  $coords_col,
            'coords_row'    =>  $coords_row,
            'project_alias' =>  $project_alias,
            'map_alias'     =>  $map_alias
        ));

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
 * Возвращает информацию о карте-песочнице
 * @return array
 */
function DB_GetMapSandbox()
{
    return array(
        'id'                =>  0,
        'project_alias'     =>  'sandbox',
        'project_title'     =>  'Песочница',
        'map_alias'         =>  'map',
        'map_title'         =>  'Карта для развлечений',
        'description'       =>  '',
        'grid_edge'         =>  22,
        'grid_transversive' =>  38,
        'grid_type'         =>  'hex:x',
        'grid_max_col'      =>  20,
        'grid_max_row'      =>  20,
        'image_filename'    =>  'sandbox.png',
        'image_width'       =>  672,
        'image_height'      =>  780,
        'leaflet_filename'  =>  'sandbox.png',
        'leaflet_width'     =>  672,
        'leaflet_height'    =>  780,
        'leaflet_ief'       =>  1,
        'view_bordersize'   =>  1,
        'view_fogdensity'   =>  0.2,
        'view_style'        =>  'canvas',
        'view_minzoom'      =>  1,
        'view_maxzoom'      =>  1,
        'view_defaultzoom'  =>  1
    );
}

/**
 * Загружает всю информацию о карте
 *
 * @param PDO $dbh
 * @param $project
 * @param $map
 * @return array|mixed
 */
function DB_GetMapInfo(\PDO $dbh, $project, $map)
{
    $map_object = array();
    $map_exists = true;
    try {
        $query = "SELECT * FROM lme_map_settings WHERE project_alias = ? AND map_alias = ?";
        $sth = $dbh->prepare($query);
        $sth->execute(array($project, $map));

        if ($sth->rowCount() > 0) {
            $map_object = $sth->fetch(\PDO::FETCH_ASSOC);
        } else {
            $map_exists = false;
            $map_object = DB_GetMapSandbox();
        }
    } catch (PDOException $e) {
        die(__LINE__ . $e->getMessage());
    }
    return array(
        'map'           =>  $map_object,
        'existance'     =>  $map_exists,
        'is_sandbox'    =>  !$map_exists
    );
}

/**
 * Возвращаем ревизию информации о гексе по айди
 * @param $dbh
 * @param $revision_id
 * @return array
 */
function DB_GetRevisionById(\PDO $dbh, $revision_id)
{
    try {
        $query = "SELECT title, content, editor, edit_reason
            FROM lme_map_tiles_data
            WHERE id = :revision_id ";
        $sth = $dbh->prepare($query);
        $sth->execute(array(
            'revision_id'   =>  $revision_id
        ));

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
 * @param $dbh
 * @param $coords_col
 * @param $coords_row
 * @param $project_alias
 * @param $map_alias
 * @return string
 */
function DB_GetListRevisions(\PDO $dbh, $coords_col, $coords_row, $project_alias, $map_alias)
{
    $revisions_string = '';
    try{
        $query = "
SELECT id, hexcol, hexrow, hexcoords, DATE_FORMAT(FROM_UNIXTIME(edit_date), '%d-%m-%Y %H:%m:%s') AS edit_date, editor, edit_reason, ip
FROM lme_map_tiles_data
WHERE hexcol = :coords_col
AND hexrow = :coords_row
AND project_alias = :project_alias AND map_alias = :map_alias";

        $sth = $dbh->prepare($query);
        $sth->execute(array(
            'coords_col'    =>  $coords_col,
            'coords_row'    =>  $coords_row,
            'project_alias' =>  $project_alias,
            'map_alias'     =>  $map_alias
        ));

        while ($row = $sth->fetch(\PDO::FETCH_ASSOC)){

            $revisions_string .= sprintf(
                '<li><a href="edit.php?callback=canvas&row=%s&col=%s&hexcoord=%s&revision=%s">%s, %s</a> <em>(IP: %s)</em>: %s</li>'."\r\n",
                $row['hexrow'],
                $row['hexcol'],
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
        $sth = $dbh->prepare("INSERT INTO lme_map_tiles_data (hexcol, hexrow, hexcoords, title, content, editor, edit_date, edit_reason, ip, project_id, project_alias, map_id, map_alias)
                          VALUES (:hexcol, :hexrow, :hexcoords, :title, :content, :editor, :edit_date, :edit_reason, :ip, :project_id, :project_alias, :map_id, :map_alias)");

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
 * @param $project_alias
 * @param $map_alias
 * @return array
 */
function DB_GetRevealedTiles(\PDO $dbh, $project_alias, $map_alias)
{
    $revealed = array();
    try {
        $query = "SELECT hexcoords, hexcol, hexrow
    FROM lme_map_tiles_data
    WHERE project_alias = :project_alias
    AND map_alias = :map_alias
    GROUP BY hexcoords  ";
        $sth = $dbh->prepare($query);
        $sth->execute(array(
            'project_alias' =>  $project_alias,
            'map_alias'     =>  $map_alias
        ));

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

/**
 * Проверяет существование проекта
 * @param PDO $dbh
 * @param $project_alias
 * @param $map_alias
 * @return int
 */
function DB_checkProjectExists(\PDO $dbh, $project_alias, $map_alias)
{

    try {
        $query = "SELECT count(id) FROM lme_map_settings WHERE project_alias = ?";
        $sth = $dbh->prepare($query);
        $sth->execute(array($project_alias));

        $result['project'] = ($sth->fetchColumn()) ? true : false;

        $query = "SELECT count(id) FROM lme_map_settings WHERE map_alias = ?";
        $sth = $dbh->prepare($query);
        $sth->execute(array($map_alias));

        $result['map'] = ($sth->fetchColumn())  ? true : false;

    }catch (\PDOException $e){
        die(__LINE__ . $e->getMessage());
    }
    return $result;
}

/**
 * template function
 * @param PDO $dbh
 * @param $data
 * @return int
 */
function DB_template(\PDO $dbh, $data)
{
    try {
        $query = "";
        $sth = $dbh->prepare($query);
        $sth->execute();

        return $sth->rowCount();

    }catch (\PDOException $e){
        die(__LINE__ . $e->getMessage());
    }

}


function install(\PDO $dbh)
{
    // try add user 'root'


    // try add project 'sandbox'


    // try add map 'sandbox/map'
    $sandbox_map = array(
        'id'                =>  0,
        'project_alias'     =>  'sandbox',
        'project_title'     =>  'Песочница',
        'map_alias'         =>  'map',
        'map_title'         =>  'Карта для развлечений',
        'description'       =>  '',
        'grid_edge'         =>  22,
        'grid_transversive' =>  38,
        'grid_type'         =>  'hex:x',
        'grid_max_col'      =>  20,
        'grid_max_row'      =>  20,
        'image_filename'    =>  'sandbox.png',
        'image_width'       =>  672,
        'image_height'      =>  780,
        'leaflet_filename'  =>  'sandbox.png',
        'leaflet_width'     =>  672,
        'leaflet_height'    =>  780,
        'leaflet_ief'       =>  1,
        'view_bordersize'   =>  1,
        'view_fogdensity'   =>  0.2,
        'view_style'        =>  'canvas',
        'view_minzoom'      =>  1,
        'view_maxzoom'      =>  1,
        'view_defaultzoom'  =>  1
    );

    try {
        // $Config->table_map_settings
        $query = "
INSERT INTO lme_map_settings
(project_alias, project_title, map_alias, map_title, description, grid_edge, grid_transversive, grid_type,
grid_max_col, grid_max_row, image_filename, image_width, image_height, leaflet_filename, leaflet_width,
leaflet_height, leaflet_ief, view_bordersize, view_fogdensity, view_style, view_minzoom, view_maxzoom, view_defaultzoom)
VALUES
(:project_alias, :project_title, :map_alias, :map_title, :description, :grid_edge, :grid_transversive, :grid_type,
:grid_max_col, :grid_max_row, :image_filename, :image_width, :image_height, :leaflet_filename, :leaflet_width,
:leaflet_height, :leaflet_ief, :view_bordersize, :view_fogdensity, :view_style, :view_minzoom, :view_maxzoom, :view_defaultzoom)";
        $sth = $dbh->prepare($query);
        $sth->execute($sandbox_map);

    }catch (\PDOException $e){
        die(__LINE__ . $e->getMessage());
    }
}

/**
 * Загружает из БД информацию по проекту: описание проекта и так далее
 * @todo: надо ли возвращать количество карт в проекте и вообще хранить его?
 *
 * @param PDO $dbh
 * @param $project_alias
 * @return bool
 */
function DB_loadProjectInfo(\PDO $dbh, $project_alias)
{
    $project = array();
    // get base project_info
    try {
        $query = "
        SELECT *
        FROM lme_project_settings
        ";
        $sth = $dbh->prepare($query);
        $sth->execute();

        $row = $sth->fetch(\PDO::FETCH_ASSOC);

        if ($row) {
            $project = $row;
        }
    }catch (\PDOException $e){
        die(__LINE__ . $e->getMessage());
    }
    return $project;
}

/**
 * Возвращает список карт, имеющихся у проекта.
 * RAW-функция, не учитывает никакие права доступа, возвращает список ВСЕХ имеющихся карт
 * На самом деле надо показывать только те карты, которые или public, или к которым у текущего пользователя
 * есть права viewer и старше.
 *
 * @todo: permissions
 * @param PDO $dbh
 * @param $project_alias
 * @return array
 *
 */
function DB_getMapsListInProject(\PDO $dbh, $project_alias)
{
    $maps_list = array();

    try {
        $query = "
    SELECT id, owner_id, map_title, map_alias, view_style
    FROM lme_map_settings
    WHERE project_alias = ?";

        $sth = $dbh->prepare($query);
        $sth->execute(array($project_alias));

        while($row = $sth->fetch(\PDO::FETCH_ASSOC)){
            $maps_list[ $row['id'] ] = $row;
        }

    }catch (\PDOException $e) {
        die(__LINE__ . $e->getMessage());
    }

    return $maps_list;
}

/**
 * Возвращает список проектов, владелец которых - указанный пользователь
 * @param PDO $dbh
 * @param $userid
 * @return array
 */
function DB_getProjectsByUser(\PDO $dbh, $userid)
{
    $plist = array();

    try {
        $query = "
        SELECT id, project_alias, project_title
        FROM lme_project_settings
        WHERE owner_id = ?
        ";
        $sth = $dbh->prepare($query);
        $sth->execute(array($userid));

        while ($row = $sth->fetch(\PDO::FETCH_ASSOC)) {
            $plist[ $row['id'] ] = $row;
        }

    }catch (\PDOException $e) {
        die(__LINE__ . $e->getMessage());
    }

    return $plist;
}

