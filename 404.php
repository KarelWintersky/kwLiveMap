<?php
/**
 * User: Arris
 * Date: 26.09.15, time: 13:37
 */

require_once 'backend/_required_lme.php';

$template_data = array();

$tpl_file = '404.html';

$html = websun_parse_template_path($template_data, $tpl_file, '$/template');

echo $html;