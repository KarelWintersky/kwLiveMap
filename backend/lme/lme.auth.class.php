<?php
namespace LiveMapEngine;
require_once('lme.config.class.php');


/**
 *
 */
class Auth extends Config
{
    private $connection;

    public function __construct()
    {
        parent::__construct();
        $this->connection = parent::getconnection();
    }



    public static function auth_CanIEdit()
    {
        return true;
    }

    /**
     * Возвращает права доступа пользователя к проекту и карте.
     * @param $userid
     * @param $project_alias
     * @param $map_alias (Если не указать - к самому проекту)
     */
    public function checkUserPermissions($userid, $project_alias, $map_alias='')
    {

    }

}
