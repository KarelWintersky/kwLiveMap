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
     * @param string $config_table
     */
    public function __construct(\PDO $dbh, $config_table = 'config')
    {
        $this->dbh = $dbh;

        if (func_num_args() > 1)
            $this->phpauth_config_table = $config_table;

        $this->config = array();

        $query = $this->dbh->prepare("SELECT * FROM {$this->phpauth_config_table}");
        $query->execute();

        while($row = $query->fetch()) {
            $this->config[$row['setting']] = $row['value'];
        }

        $this->setVerifyDefaults();
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

    private function setVerifyDefaults()
    {
        if (! isset($this->config['verify_password_min_length']) )
            $this->config['verify_password_min_length'] = 3;

        if (! isset($this->config['verify_password_max_length']) )
            $this->config['verify_password_max_length'] = 150;

        if (! isset($this->config['verify_password_strong_requirements']) )
            $this->config['verify_password_strong_requirements'] = 1;

        if (! isset($this->config['verify_email_min_length']) )
            $this->config['verify_email_min_length'] = 5;

        if (! isset($this->config['verify_email_max_length']) )
            $this->config['verify_email_max_length'] = 100;

        if (! isset($this->config['verify_email_use_banlist']) )
            $this->config['verify_email_use_banlist'] = 1;
    }
}
