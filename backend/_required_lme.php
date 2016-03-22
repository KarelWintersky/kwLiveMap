<?php
// LiveMap Engine
require_once 'lme/lme.core.php';
require_once 'lme/lme.pdo.class.php';
require_once 'lme/lme.auth.class.php';

// require_once 'lme/lme.eventlog.class.php';
// require_once 'lme/lme.messages.class.php';

// WebSun Template Engine
require_once 'websun/websun.php';

// PHPAuth Engine
// require_once "phpauth/languages/ru_RU.php";
require_once "phpauth/config.class.php";
require_once "phpauth/auth.class.php";

$config = new LiveMapEngine\Config();
$db     = new LiveMapEngine\DB();
$dbh    = $config->getconnection();

$authconfig = new PHPAuth\Config();
$auth       = new PHPAuth\Auth($dbh, $authconfig);

 
