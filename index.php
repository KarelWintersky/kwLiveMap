<?php
require_once 'backend/websun.php';

$tpl_file = 'index.html';

$TEMPLATE_DATA = array(
    'user_status'           =>  'logged_out', // logged_in | logged_out
);

$html = websun_parse_template_path($TEMPLATE_DATA, $tpl_file, '$/template');

echo $html;

