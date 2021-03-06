<?php
/**
 * User: Arris
 * Date: 08.09.15, time: 01:15
 */
require_once '_required_lme.php';

$auth_result = $auth->resetPass(
    $_POST['auth:reset_key'],
    $_POST['auth:new_password'],
    $_POST['auth:new_password_again']
);

if ($auth_result['error']) {
    // error occured
    $html_callback = '/resetpassword';
} else {
    // reset password correct, set cookie
    $html_callback = '/login';
    setcookie('kw_livemap_last_logged_user', $_POST['auth:reg_email'],  time()+60*60*5, "/");
}


$template_data = array(
    'error_messages'    =>  $auth_result['message'],
    'html_callback_timeout' =>  10,
    'html_callback'     =>  $html_callback
);

$tpl_file = 'auth_callbacks/auth.callback.reset.html';

$html = websun_parse_template_path($template_data, $tpl_file, '$/template');

echo $html;