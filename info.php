<?php
/**
 * User: Arris
 * Date: 03.09.15, time: 15:20
 */

require_once 'backend/config/config.php';
require_once 'backend/core.php';
require_once 'backend/core.auth.php';
require_once 'backend/core.pdo.php';
require_once 'backend/websun.php';

require_once "backend/phpauth/languages/en_GB.php";
require_once "backend/phpauth/config.class.php";
require_once "backend/phpauth/auth.class.php";

$tpl_file = 'info.project.html';

$html = websun_parse_template_path($TEMPLATE_DATA, $tpl_file, '$/template');

echo $html;



