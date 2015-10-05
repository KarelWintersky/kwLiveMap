<?php
/**
 * User: Arris
 * Date: 15.09.15, time: 12:08
 */
namespace LiveMapEngine;
require_once('lme.config.class.php');

/**
 *
 */
class DB extends Config {
    private $connection;

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->connection = parent::getconnection();
    }

    /**
     * Работа с ревизиями гексов
     **/

    /**
     * Возвращаем количество ревизий у гекса
     * @param $coords_col
     * @param $coords_row
     * @param $project_alias
     * @param $map_alias
     * @return mixed
     */
    public function getRevisionsCount($coords_col, $coords_row, $project_alias, $map_alias)
    {
        try {
            $query = "SELECT COUNT(id)
        AS count_id
        FROM lme_map_tiles_data
        WHERE hexcol = :coords_col
        AND   hexrow = :coords_row
        AND   project_alias = :project_alias
        AND   map_alias = :map_alias ";
            $sth = $this->connection->prepare($query);
            $sth->execute(array(
                'coords_col'    =>  $coords_col,
                'coords_row'    =>  $coords_row,
                'project_alias' =>  $project_alias,
                'map_alias'     =>  $map_alias
            ));

            $row = $sth->fetch(\PDO::FETCH_ASSOC);

            $revisions_count = $row['count_id'];
        }
        catch (\PDOException $e) {
            die($e->getMessage());
        }
        return $revisions_count;
    }

    /**
     * Возвращаем последнюю версию данных о регионе карты (гексе)
     * @param $coords_col
     * @param $coords_row
     * @param $project_alias
     * @param $map_alias
     * @return array
     */
    public function getRevisionLast($coords_col, $coords_row, $project_alias, $map_alias)
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

            $sth = $this->connection->prepare($query);
            $sth->execute(array(
                'coords_col'    =>  $coords_col,
                'coords_row'    =>  $coords_row,
                'project_alias' =>  $project_alias,
                'map_alias'     =>  $map_alias
            ));

            $row = $sth->fetch(\PDO::FETCH_ASSOC);

            $info = array(
                'message'   =>  '',
                'text'      =>  $row['content'],
                'title'     =>  $row['title'],
                'edit_reason'   =>  $row['edit_reason'],
                'editor_name'   =>  $row['editor'],
            );

        }
        catch (\PDOException $e) {
            die(__LINE__ . $e->getMessage());
        }
        return $info;
    }

    /**
     * Возвращаем ревизию информации о гексе по айди
     * @param $revision_id
     * @return array
     */
    public function getRevisionById($revision_id)
    {
        try {
            $query = "SELECT title, content, editor, edit_reason
            FROM lme_map_tiles_data
            WHERE id = :revision_id ";
            $sth = $this->connection->prepare($query);
            $sth->execute(array(
                'revision_id'   =>  $revision_id
            ));

            $row = $sth->fetch(\PDO::FETCH_ASSOC);

            $info = array(
                'message'   =>  'Fork revision #'.$revision_id,
                'text'      =>  $row['content'],
                'title'     =>  $row['title'],
                'edit_reason'   =>  "".$row['edit_reason'],
                'editor_name'   =>  at($_COOKIE, 'kw_trpg_lme_auth_editorname', ""),
            );
        }
        catch (\PDOException $e){
            die($e->getMessage());
        }
        return $info;
    }

    /**
     * Возвращаем список ревизий в виде строки из <LI>ссылок</LI>
     * @todo: изменить ссылку и адресацию на неё
     * @param $coords_col
     * @param $coords_row
     * @param $project_alias
     * @param $map_alias
     * @return string
     */
    function getListRevisions($coords_col, $coords_row, $project_alias, $map_alias)
    {
        $revisions_string = '';
        try{
            $query = "
SELECT id, hexcol, hexrow, hexcoords, DATE_FORMAT(FROM_UNIXTIME(edit_date), '%d-%m-%Y %H:%m:%s') AS edit_date, editor, edit_reason, ip
FROM lme_map_tiles_data
WHERE hexcol = :coords_col
AND hexrow = :coords_row
AND project_alias = :project_alias AND map_alias = :map_alias";

            $sth = $this->connection->prepare($query);
            $sth->execute(array(
                'coords_col'    =>  $coords_col,
                'coords_row'    =>  $coords_row,
                'project_alias' =>  $project_alias,
                'map_alias'     =>  $map_alias
            ));

            while($row = $sth->fetch(\PDO::FETCH_ASSOC)){

                $revisions_string .= sprintf(
                    $this->revision_url."\r\n",
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
        catch(\PDOException $e) {
            die($e->getMessage());
        }
        return $revisions_string;
    }

    /**
     * Апдейтит в базу информацию по гексику
     * @param $data
     * @return bool
     */
    function updateHexTile($data)
    {
        $success = false;

        try{
            $sth = $this->connection->prepare("INSERT INTO lme_map_tiles_data (hexcol, hexrow, hexcoords, title, content, editor, edit_date, edit_reason, ip, project_id, project_alias, map_id, map_alias)
                          VALUES (:hexcol, :hexrow, :hexcoords, :title, :content, :editor, :edit_date, :edit_reason, :ip, :project_id, :project_alias, :map_id, :map_alias)");

            $success = $sth->execute($data);
        }
        catch (\PDOException $e) {
            echo $e->getMessage();
        }
        return $success;
    }

    /**
     * Возвращает массив из открытых гексов (для тумана войны)
     * @param $project_alias
     * @param $map_alias
     * @return array
     */
    public function getRevealedTiles($project_alias, $map_alias)
    {
        $revealed = array();
        try {
            $query = "SELECT hexcoords, hexcol, hexrow
    FROM lme_map_tiles_data
    WHERE project_alias = :project_alias
    AND map_alias = :map_alias
    GROUP BY hexcoords  ";
            $sth = $this->connection->prepare($query);
            $sth->execute(array(
                'project_alias' =>  $project_alias,
                'map_alias'     =>  $map_alias
            ));

            while($row = $sth->fetch(\PDO::FETCH_ASSOC)){
                $zarea = 'z'.$row['hexcoords'];
                $revealed[$zarea] = array(
                    'col'   =>  $row['hexcol'],
                    'row'   =>  $row['hexrow']
                );
            }
        }
        catch (\PDOException $e) {
            die($e->getMessage());
        }
        return $revealed;
    }

    /**
     * DEPRECATED: Проверяет существование проекта и карты
     * @param $project_alias
     * @param $map_alias
     * @return mixed
     */
    public function checkProjectExists($project_alias, $map_alias)
    {

        try {
            $query = "SELECT count(id) FROM lme_map_settings WHERE project_alias = ?";
            $sth = $this->connection->prepare($query);
            $sth->execute(array($project_alias));

            $result['project'] = ($sth->fetchColumn()) ? true : false;

            $query = "SELECT count(id) FROM lme_map_settings WHERE map_alias = ?";
            $sth = $this->connection->prepare($query);
            $sth->execute(array($map_alias));

            $result['map'] = ($sth->fetchColumn())  ? true : false;

        }catch (\PDOException $e){
            die(__LINE__ . $e->getMessage());
        }
        return $result;
    }



    /**
     * Загружает всю информацию о карте
     * @param $project
     * @param $map
     * @return array
     */
    public function getMapInfo($project, $map)
    {
        $map_object = array();
        $map_exists = true;
        try {
            $query = "SELECT * FROM lme_map_settings WHERE project_alias = ? AND map_alias = ?";
            $sth = $this->connection->prepare($query);
            $sth->execute(array($project, $map));

            if ($sth->rowCount() > 0) {
                $map_object = $sth->fetch(\PDO::FETCH_ASSOC);
            } else {
                $map_exists = false;
            }
        } catch (\PDOException $e) {
            die(__LINE__ . $e->getMessage());
        }
        return array(
            'map'           =>  $map_object,
            'existance'     =>  $map_exists,
        );
    }


    /**
     * Возвращает список проектов, владелец которых - указанный (по id) пользователь
     * @param $userid
     * @return array
     */
    public function getProjectsByUser($userid)
    {
        $plist = array();

        try {
            $query = "
        SELECT id, project_alias, project_title
        FROM lme_project_settings
        WHERE owner_id = ?
        ";
            $sth = $this->connection->prepare($query);
            $sth->execute(array($userid));

            while ($row = $sth->fetch(\PDO::FETCH_ASSOC)) {
                $plist[ $row['id'] ] = $row;
            }

        }catch (\PDOException $e) {
            die(__LINE__ . $e->getMessage());
        }

        return $plist;
    }

    /**
     * Загружает из БД информацию по проекту: описание проекта и так далее
     *
     * @param $project_alias
     * @return array - инфорация о проекте или пустой массив, если проекта нет.
     */
    function getProjectInfo($project_alias)
    {
        $project = array();
        try {
            $query = "
        SELECT *
        FROM lme_project_settings
        WHERE project_alias LIKE ?
        ";
            $sth = $this->connection->prepare($query);
            $sth->execute(array($project_alias));

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
     * @param $project_alias
     * @return array
     *
     */
    function getMapsListAtProject($project_alias)
    {
        $maps_list = array();

        try {
            $query = "
    SELECT id, owner_id, map_title, map_alias, view_style
    FROM lme_map_settings
    WHERE project_alias = ?";

            $sth = $this->connection->prepare($query);
            $sth->execute(array($project_alias));

            while($row = $sth->fetch(\PDO::FETCH_ASSOC)){
                $maps_list[ $row['id'] ] = $row;
            }

        }catch (\PDOException $e) {
            die(__LINE__ . $e->getMessage());
        }

        return $maps_list;
    }


}
 
