<?php
require_once 'backend/_required_libs.php';

global $CONFIG;

$dbh = DB_Connect();

$config = new PHPAuth\Config($dbh);
$auth   = new PHPAuth\Auth($dbh, $config, $lang);

$is_logged_in = (int)$auth->isLogged(); // 1 if logged-in, 0 elseether

$this_user_id = $auth->getSessionUID( $auth->getSessionHash() );

if ($is_logged_in) {
    // load my projects info
    $my_projects_list = DB_getProjectsByUser($dbh, $this_user_id );
    $my_projects_count = count($my_projects_list);
}

// show template
$tpl_file = 'index.html';

$template_data = array(
    'is_logged_in'          =>  $is_logged_in,
    'my_projects_count'     =>  $my_projects_count,
    'my_projects_list'      =>  $my_projects_list
);

$html = websun_parse_template_path($template_data, $tpl_file, '$/template');

echo $html;

