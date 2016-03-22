<?php
/**
 * User: Arris
 * Date: 06.09.15, time: 16:34
 */
require_once '_required_lme.php';

if ($auth->isLogged()) {

    $session_hash = $auth->getSessionHash();

    $auth_result = $auth->logout($session_hash);

    if ($auth_result) {
        unsetcookie($authconfig->__get('cookie_name'));
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