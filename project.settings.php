<?php
/**
 * User: Arris
 * Date: 03.09.15, time: 15:20
 */
require_once 'backend/_required_lme.php';

$config = new LiveMapEngine\Config();
$db     = new LiveMapEngine\DB();
$dbh    = $config->getconnection();

$authconfig = new PHPAuth\Config($dbh);
$auth       = new PHPAuth\Auth($dbh, $authconfig, $lang);

var_dump(__FILE__);
echo '<hr/>';
var_dump($_GET);
 
