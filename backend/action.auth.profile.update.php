<?php
/**
 * User: Arris
 * Date: 11.09.15, time: 9:54
 */

require_once 'config/config.php';
require_once 'core.php';
require_once 'core.pdo.php';
require_once 'websun.php';

require_once "phpauth/languages/en_GB.php";
require_once "phpauth/config.class.php";
require_once "phpauth/auth.class.php";

global $CONFIG;

$dbh = DB_Connect();

$config = new PHPAuth\Config($dbh);
$auth   = new PHPAuth\Auth($dbh, $config, $lang);

$auth_result = array();
$html_callback = '';

if (!$auth->isLogged()) redirect('/'); // hacking attempt!

$current_uid = $auth->getSessionUID( $auth->getSessionHash() );

switch ($_POST['auth:editprofile:submit']) {
    case 'update_personal_data' : {
        // update personal data
        $post_data = array(
            'username'      =>  $_POST['auth:editprofile:name'],
            'gender'        =>  $_POST['auth:editprofile:gender'],
            'city'          =>  $_POST['auth:editprofile:reg_city'],
        );

        $password_is_correct = $auth->comparePasswords($current_uid, $_POST['auth:editprpfile:password']);

        if ($password_is_correct) {

            $result = $auth->updateUser($current_uid, $post_data);

            $auth_result['message']
                = $result['error']
                ? $result['message']
                : 'Профиль обновлён';
        } else $auth_result['message'] = 'Неправильный пароль!';

        $html_callback = '/mysettings';

        break;
    }
    case 'change_email': {

        $auth_result = $auth->changeEmail($current_uid, $_POST['auth:changeemail:new'], $_POST['auth:changeemail:password']);

        $html_callback = '/mysettings';

        break;
    }
    case 'change_password': {

        // $password_is_correct = $auth->comparePasswords($current_uid, $_POST['auth:changepassword:current']);

        $auth_result = $auth->changePassword($current_uid,
            $_POST['auth:changepassword:current'],
            $_POST['auth:changepassword:new'],
            $_POST['auth:changepassword:again']);

        $html_callback = '/mysettings';
        break;
    }
    case 'delete_account': {
        //@todo: check EMail match ( $_POST['auth:deleteaccount:email'] )

        //@todo: проверка, не удаляем ли мы OWNER'а с проектами или рута!!!

        $auth_result = $auth->deleteUser($current_uid, $_POST['auth:deleteaccount:password']);
        if (!$auth_result['error']) {
            /* Удаление аккаунта еще и вызывает логаут! */

            if ( $auth->logout( $auth->getSessionHash() ) ) {
                /* И удаление кук */

                unsetcookie($config->__get('cookie_name'));

                //@todo: Не забываем: еще нужно удалить записи из таблицы `lme_user_permissions` (которой еще нет)
                // и вообще много где подчистить мусор.


            }
        }
        $html_callback = '/';
        break;
    }
    default: {
        redirect('/');
        break;
    }
}

// $auth_result['message'] = at($auth_result, 'message', print_r($_POST, true));

$template_data = array(
    'error_messages'    =>  $auth_result['message'],
    'html_callback_timeout' =>  10,
    'html_callback'     =>  $html_callback
);

$tpl_file = 'auth_callbacks/auth.callback.updateprofile.html';

$html = websun_parse_template_path($template_data, $tpl_file, '$/template');

echo $html;
 
