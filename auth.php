<?php
/**
 * User: Arris
 * Date: 03.09.15, time: 15:20
 */
require_once 'backend/config/config.php';
require_once 'backend/core.php';
require_once 'backend/core.auth.php';
require_once 'backend/core.pdo.php';
require_once 'backend/websun.php';

require_once "backend/phpauth/languages/en_GB.php";
require_once "backend/phpauth/config.class.php";
require_once "backend/phpauth/auth.class.php";
global $CONFIG;

$dbh = DB_Connect();

$config = new PHPAuth\Config($dbh);
$auth   = new PHPAuth\Auth($dbh, $config, $lang);

if(!isset($_COOKIE[$config->cookie_name]) || !$auth->checkSession($_COOKIE[$config->cookie_name])) {
    $logged_in_status = 'logged_out';
} else {
    $logged_in_status = 'logged_in';
}

$template_file = '';
$template_data = array();

//@todo: оптимизировать флаг до boolean

/*
 * Решил оставить в блоках и заполнение инстантного коллбэк-темплейта, и переход через
 * смену хедера. А вдруг сломается? Как сделать лучше - не знаю.
*/
switch ($_GET['action']) {
    case 'login': {
        if ($logged_in_status == 'logged_in') {
            $template_file = 'auth.callback.instant_to_root.html';
            redirect('/');
        } else {
            $new_username = at($_COOKIE, 'kw_livemap_last_logged_user', '');
            $template_file = 'auth/auth.login.html';
        }
        break;
    }
    case 'register': {
        if ($logged_in_status == 'logged_in') {
            $template_file = 'auth.callback.instant_to_root.html';
            redirect('/');
        } else {
            $template_file = 'auth/auth.register.html';
        }
        break;
    }
    case 'recover': {
        // если мы залогинились - глупо пытаться восстановить пароль
        if ($logged_in_status == 'logged_in') {
            $template_file = 'auth.callback.instant_to_root.html';
            redirect('/');
        } else {
            $template_file = 'auth/auth.recover.html';
        }
        break;
    }
    // user logged in (вставить дополнительную проверку по сессии)
    case 'mysettings': {
        //@todo: mysettings and debug values
        var_dump($_COOKIE);

        if ($logged_in_status == 'logged_out') {
            $template_file = 'auth.callback.instant_to_root.html';
            redirect('/');
        } else {
            $template_file = 'auth/auth.mysettings.html';
        }
        break;
    }
    case 'logout': {
        if ($logged_in_status == 'logged_out') {
            $template_file = 'auth.callback.instant_to_root.html';
            redirect('/');
        } else {
            $template_file = 'auth/auth.logout.html';
        }
        break;
    }

    case 'activateaccount': {
        if ($logged_in_status == 'logged_in') {
            // Активация аккаунта недоступна если мы залогинились
            $template_file = 'auth.callback.instant_to_root.html';
            redirect('/');
        } else {
            $template_file = 'auth/auth.activate.html';
        }
        break;
    }
    case 'resetpassword': {
        if ($logged_in_status == 'logged_in') {
            // Сброс недоступен если мы залогинились
            $template_file = 'auth.callback.instant_to_root.html';
            redirect('/');
        } else {
            $template_file = 'auth/auth.resetpassword.html';
        }
        break;
    }

    // error
    default: {
        redirect('/');
        break;
    }
};

$html = websun_parse_template_path($template_data, $template_file, '$/template');

echo $html;