<?php
namespace LiveMapEngine;

require_once('config/config.db.php');

/**
 *
 */
class Config extends DBConfig
{
    private $connection;
    public  $revision_url = '<li><a href="edit.region.php?callback=canvas&row=%s&col=%s&hexcoord=%s&revision=%s">%s, %s</a> <em>(IP: %s)</em>: %s</li>\r\n';

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

    /**
     * @return array
     */
    public function getSandboxMap()
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

}
