<?php
/**
 * User: Arris
 * Date: 03.09.15, time: 15:20
 */
require_once 'backend/_required_lme.php';

$is_logged_in = $auth->isLogged(); // true if logged-in

$template_file = '';
$template_data = array();

/*
 * Решил оставить в блоках и заполнение инстантного коллбэк-темплейта, и переход через
 * смену хедера. А вдруг сломается (смена хедера)? Как сделать лучше - пока идей нет.
*/
switch ($_GET['action']) {
    // обработка механизма входа в систему
    case 'login': {
        if ($is_logged_in) {
            $template_file = 'auth.callback.instant_to_root.html';
            redirect('/');
        } else {
            $new_username = at($_COOKIE, 'kw_livemap_last_logged_user', '');
            $template_file = 'auth/auth.login.html';
        }
        break;
    }

    // обработка механизма регистрации
    case 'register': {
        if ($is_logged_in) {
            $template_file = 'auth.callback.instant_to_root.html';
            redirect('/');
        } else {
            $template_data['strong_password'] = $authconfig->verify_password_strong_requirements;
            $template_file = 'auth/auth.register.html';
        }
        break;
    }

    // обработка механизма восстановления пароля
    case 'recover': {
        // если мы залогинились - глупо пытаться восстановить пароль
        if ($is_logged_in) {
            $template_file = 'auth.callback.instant_to_root.html';
            redirect('/');
        } else {
            $template_file = 'auth/auth.recover.html';
        }
        break;
    }

    // мои настройки (разнообразные)
    case 'mysettings': {
        if ($is_logged_in) {
            // загрузить в переменные значения из базы и вставить их в темплейт
            $userid = $auth->getSessionUID( $auth->getSessionHash() );
            $userdata = $auth->getUser($userid);

            $template_data = array(
                'username'      =>  $userdata['username'],
                'gender'        =>  $userdata['gender'],
                'city'          =>  $userdata['city'],
                'current_email' =>  $userdata['email'],
                'strong_password'=> $authconfig->verify_password_strong_requirements
            );


            $template_file = 'auth/auth.mysettings.html';
        } else {
            $template_file = 'auth.callback.instant_to_root.html';
            redirect('/');
        }
        break;
    }

    // механизм логаута
    case 'logout': {
        if ($is_logged_in) {
            $template_file = 'auth/auth.logout.html';
        } else {
            $template_file = 'auth.callback.instant_to_root.html';
            redirect('/');
        }
        break;
    }

    // ввод ключа активации аккаунта
    case 'activateaccount': {
        if ($is_logged_in) {
            // Активация аккаунта недоступна если мы залогинились
            $template_file = 'auth.callback.instant_to_root.html';
            redirect('/');
        } else {
            $template_file = 'auth/auth.activate.html';
        }
        break;
    }

    // ввод ключа сброса пароля
    case 'resetpassword': {
        if ($is_logged_in) {
            $template_file = 'auth.callback.instant_to_root.html';
            redirect('/');
        } else {
            $template_file = 'auth/auth.resetpassword.html';
        }
        break;
    }

    // вообще непонятно как мы сюда попали
    default: {
        redirect('/');
        break;
    }
};

echo '<hr>';

$html = websun_parse_template_path($template_data, $template_file, '$/template');

echo $html;