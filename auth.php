<?php
/**
 * User: Arris
 * Date: 03.09.15, time: 15:20
 */
require_once 'backend/websun.php';

$template_file = '';
$template_data = array();

switch ($_GET['action']) {
    // user not logged in (вставить дополнительную проверку по сессии)
    case 'login': {
        $template_file = 'auth/auth.login.html';
        break;
    }
    case 'register': {
        $template_file = 'auth/auth.register.html';
        break;
    }
    case 'remember': {
        $template_file = 'auth/auth.recover.html';
        break;
    }
    // user logged in (вставить дополнительную проверку по сессии)
    case 'mysettings': {
        $template_file = 'auth/auth.mysettings.html';
        break;
    }
    case 'logout': {
        $template_file = 'auth/auth.logout.html';
        break;
    }
    // error
    default: {
        break;
    }
};

$html = websun_parse_template_path($template_data, $template_file, '$/template');

echo $html;

