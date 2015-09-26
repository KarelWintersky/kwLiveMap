<?php
namespace LiveMapEngine;
require_once('lme.config.class.php');


/**
 *
 */
class Logger extends Config
{
    private $connection;

    public function __construct()
    {
        parent::__construct();
        $this->connection = parent::getconnection();
    }

    /**
     * @param $where
     * @param $project
     * @param $map
     * @param $coords
     * @param $action
     */
    public static function logEvent($where, $project, $map, $coords, $action)
    {
        $event = array(
            'dt'        =>  time(),
            'who'       =>  at($_COOKIE, 'lme_auth_currentuserid', 0),
            'where'     =>  $where,
            'project'   =>  $project,
            'map'       =>  $map,
            'coords'    =>  $coords,
            'action'    =>  $action,
        );

        try {
            $query = "INSERT INTO lme_eventlog
            (dt, who, where, project, map, coords, action)
            VALUES
            (:dt, :who, :where, :project, :map, :coords, :action)";

            $sth = Config::getconnection()->prepare($query);
            $sth->execute($event);

        } catch (\PDOException $e) {
            die($e->getMessage());
        }

    }


    public static function getEvents($timestamp, $project, $map)
    {

    }



}
