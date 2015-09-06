<?php
/**
 * User: Arris
 * Date: 06.09.15, time: 16:34
 */
require_once 'config/config.php';
require_once 'core.php';
require_once 'core.auth.php';
require_once 'core.pdo.php';
require_once 'websun.php';

require_once "phpauth/languages/en_GB.php";
require_once "phpauth/config.class.php";
require_once "phpauth/auth.class.php";

global $CONFIG;

$dbh = DB_Connect();

$config = new PHPAuth\Config($dbh);
$auth   = new PHPAuth\Auth($dbh, $config, $lang);

if(isset($_COOKIE[$config->cookie_name]) || $auth->checkSession($_COOKIE[$config->cookie_name])) {
    $session_hash = $_COOKIE[$config->cookie_name];
    $auth_result = $auth->logout($session_hash);

    if ($auth_result) {
        unsetcookie('kw_livemap_logged_in_session_hash');
        unsetcookie($config->__get('cookie_name'));
        $template_data['error_messages'] = 'Мы успешно вышли из системы.';
    } else {
        $template_data['error_messages'] = 'UNKNOWN Error while logging out!';
    }
} else {
    // we are not logged!
    $template_data['error_messages'] = 'We are not logged in!!!';
}
$html_callback = '/';

$template_data = array(
    'html_callback_timeout' =>  10,
    'html_callback'     =>  $html_callback
);

$tpl_file = 'auth_callbacks/auth.callback.logout.html';

$html = websun_parse_template_path($template_data, $tpl_file, '$/template');

echo $html;