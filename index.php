<?php
require_once 'backend/websun.php';

$tpl_file = 'index.html';

$html = websun_parse_template_path($TEMPLATE_DATA, $tpl_file, '$/template');

echo $html;

