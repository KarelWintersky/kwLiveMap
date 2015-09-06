<?php
/**
 * User: Arris
 * Date: 06.09.15, time: 16:35
 */
require_once 'config/config.php';
require_once 'core.php';
require_once 'core.auth.php';
require_once 'core.pdo.php';
require_once 'websun.php';
global $CONFIG;

$html_callback = '/';

$errors = array();

if (!$_POST['auth:reg_email'])
    $errors[] = 'E-Mail required';
if (!$_POST['auth:reg_password'])
    $errors[] = 'Password required';
if (!$_POST['auth:reg_password_again'])
    $errors[] = 'Please, retype password';
if (trim($_POST['auth:reg_password']) !== trim($_POST['auth:reg_password_again']))
    $errors[] = 'Password not match!';
if (!$_POST['auth:reg_username'])
    $errors[] = 'Username required';
if (!$_POST['auth:reg_eula_checked'])
    $errors[] = 'Eula must be checked!';

if (!$errors) {

    $userdata = array(
        'email'     =>  $_POST['auth:reg_email'],
        'password'  =>  $_POST['auth:reg_password'],
        'username'  =>  $_POST['auth:reg_username'],
        'regip'     =>  $_SERVER['REMOTE_ADDR'],
        'regdate'   =>  time()
    );

    $dbh = DB_Connect();
    $usercount = auth_getUsersByEmail($dbh, $userdata[':email']);

    if ($usercount > 0) {
        $errors[] = "Пользователь с таким email'ом уже есть в системе.";
        $html_callback = '/register';
    } else {
        $register_result = auth_tryRegisterUser($dbh, $userdata);
        $errors[] = "Пользователь зарегистрирован.";
        $html_callback = '/login';
        setcookie('kw_livemap_new_registred_username', $userdata['email'],  time()+60*5, "/");
    }

}

$template_data = array(
    'error_messages'    => implode($errors, "<br/>\r\n"),
    'html_callback_timeout' =>  10,
    'html_callback'     =>  $html_callback
);

$tpl_file = 'auth_callbacks/auth.callback.register.html';

$html = websun_parse_template_path($template_data, $tpl_file, '$/template');

echo $html;




