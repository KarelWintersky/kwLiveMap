<?php
namespace LiveMapEngine;

require_once('config/config.db.php');

/**
 *
 */
class Config extends DBConfig
{
    private $connection;
    public  $revision_url = '<li><a href="edit.region.php?callback=canvas&row=%s&col=%s&hexcoord=%s&revision=%s">%s, %s</a> <em>(IP: %s)</em>: %s</li>';

    /**
     *
     */
    public function __construct()
    {
        try {
            $dbh = new \PDO($this->gethost(), $this->get('username'), $this->get('password'));
            $dbh->exec("SET NAMES utf8");
            $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $this->connection = $dbh;
        }
        catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @return \PDO
     */
    public function getconnection()
    {
        return $this->connection;
    }

    public function getCopyright()
    {
        return '(c) Karel Wintersky, 2015, ver '.$this->getKey('version');
    }

}
