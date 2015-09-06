<?php
namespace PHPAuth;

/**
 * PHPAuth config class
 */
class Config
{
    private $dbh;
    private $config;
    private $phpauth_config_table = 'lme_phpauth_config';

    /**
     * @param \PDO $dbh
     */
    public function __construct(\PDO $dbh)
    {
        $this->dbh = $dbh;

        $this->config = array();

        $query = $this->dbh->prepare("SELECT * FROM {$this->phpauth_config_table}");
        $query->execute();

        while($row = $query->fetch()) {
            $this->config[$row['setting']] = $row['value'];
        }
    }

    /**
     * @param $setting
     * @return mixed
     */
    public function __get($setting)
    {
        return $this->config[$setting];
    }

    /**
     * @param $setting
     * @param $value
     * @return bool
     */
    public function __set($setting, $value)
    {
        $query = $this->dbh->prepare("UPDATE {$this->phpauth_config_table} SET value = ? WHERE setting = ?");

        if($query->execute(array($value, $setting))) {
            $this->config[$setting] = $value;
            return true;
        } else {
            return false;
        }
    }
}
