<?php
/**
 * User: Arris
 * Date: 07.09.15, time: 1:37
 */

require_once '_required_lme.php';

$auth_result = $auth->activate($_POST['auth:activate_key']);

$html_callback
    = ($auth_result['error'])
    ? '/'
    : '/login';

$template_data = array(
    'error_messages'    =>  $auth_result['message'],
    'html_callback_timeout' =>  10,
    'html_callback'     =>  $html_callback
);

$tpl_file = 'auth_callbacks/auth.callback.activate.html';

$html = websun_parse_template_path($template_data, $tpl_file, '$/template');

echo $html;