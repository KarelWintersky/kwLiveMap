<?php
/**
 * User: Arris
 * Date: 06.09.15, time: 16:34
 */
require_once '_required_libs.php';

global $CONFIG;

$dbh = DB_Connect();
$auth_result = array();

$config = new PHPAuth\Config($dbh);
$auth   = new PHPAuth\Auth($dbh, $config, $lang);

switch ($_POST['auth:loginaction']) {
    case 'login': {

        $auth_result = $auth->login(
            $_POST["auth:login_email"],
            $_POST["auth:login_password"],
            at($_POST, "auth:login_remember_me", 0) );

        if (!$auth_result['error']) {
            // no errors
            setcookie($config->__get('cookie_name'), $auth_result['hash'], time()+$auth_result['expire'], "/");
            unsetcookie('kw_livemap_new_registred_username');

            $html_callback = '/';
        } else {
            $html_callback = '/login';
        }
        break;
    }
    case 'resendactivation' : {
        $auth_result = $auth->resendActivation( $_POST["auth:login_email"]);
        $html_callback
            = $auth_result['error']
            ? '/login'
            : '/activateaccount';
        break;
    }
    default: {
        $html_callback = '/';
        break;
    }
}

$template_data = array(
    'error_messages'    =>  $auth_result['message'],
    'html_callback_timeout' =>  5,
    'html_callback'     =>  $html_callback
);

$tpl_file = 'auth_callbacks/auth.callback.login.html';

$html = websun_parse_template_path($template_data, $tpl_file, '$/template');

echo $html;