<?php
/**
 * User: Arris
 * Date: 06.09.15, time: 16:34
 */
require_once '_required_lme.php';

$config = new LiveMapEngine\Config();
$db     = new LiveMapEngine\DB();
$dbh    = $config->getconnection();

$authconfig = new PHPAuth\Config($dbh);
$auth       = new PHPAuth\Auth($dbh, $authconfig, $lang);

switch ($_POST['auth:loginaction']) {
    case 'login': {

        $auth_result = $auth->login(
            $_POST["auth:login_email"],
            $_POST["auth:login_password"],
            at($_POST, "auth:login_remember_me", 0) );

        if (!$auth_result['error']) {
            // no errors
            setcookie($authconfig->__get('cookie_name'), $auth_result['hash'], time()+$auth_result['expire'], "/");
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