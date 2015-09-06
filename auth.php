<?php
/**
 * User: Arris
 * Date: 03.09.15, time: 15:20
 */

require_once 'backend/websun.php';

var_dump(__FILE__);
echo '<hr/>';
var_dump($_GET);

$tpl_file = '';

$html = websun_parse_template_path($TEMPLATE_DATA, $tpl_file, '$/template');

echo $html;

