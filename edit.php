<?php
require_once 'backend/core.php';
require_once 'backend/core.auth.php';
require_once 'backend/core.pdo.php';
require_once 'backend/websun.php';

// init null values
$revisions_string = '';
$revision = array();

// check access rights
$is_can_edit = auth_CanIEdit();

$coords_col = intval($_GET['col']);
$coords_row = intval($_GET['row']);

$project_name
    = isset($_GET['project'])
    ? $_GET['project']
    : die('No such project!');

$map_name
    = isset($_GET['map'])
    ? $_GET['map']
    : die('No such map!');

// коннект с базой
$dbh = DB_Connect();

// проверяем, сколько ревизий текста у гекса
$revisions_count = DB_GetRevisionsCount($dbh, $coords_col, $coords_row);

// в зависимости от количества ревизий заполняем данные шаблона
if ($revisions_count != 0) {

    // если во входящих параметрах нет идентификатора ревизии - загружаем последнюю
    if (!isset($_GET['revision'])) {
        // загружаем последнюю ревизию
        $revision = DB_GetRevisionLast($dbh, $coords_col, $coords_row, $project_name, $map_name);
    } else {
        // загружаем нужную ревизию по идентификатору
        $revision_id = $_GET['revision'];
        $revision = DB_GetRevisionById($dbh, $revision_id);
    }

    // выгружаем список ревизий
    $revisions_string = DB_GetRevisionsList($dbh, $coords_col, $coords_row);
} else {
    // заполняем данные
    $template = array(
        'text'      =>  '',
        'title'     =>  '',
        'edit_reason'   =>  'Первое редактирование',
        'editor_name'   =>  at($_COOKIE, 'kwtrpglme_auth_editorname', ''),
    );
}

$dbh = null;


// параметр callback больше не передаем - отображение карты зависит от настроек в конфиге
$TEMPLATE_DATA = array(
    'project_name'          =>  $project_name,
    'map_name'              =>  $map_name,
    //
    'html_callback'         =>  "/{$project_name}/{$map_name}",
    'project_title'         =>  'Trollfjorden -- Троллячьи фьорды',
    'map_title'             =>  'основная карта',
    //
    'hexcoord'              =>  $_GET['hexcoord'],
    'coords_col'            =>  $coords_col,
    'coords_row'            =>  $coords_row,
    'region_name'           =>  $revision['title'],
    'region_text'           =>  $revision['text'],
    'region_edit_reason'    =>  $revision['edit_reason'],
    'region_editor_name'    =>  $revision['editor_name'],
    // revisions
    'region_revisions'      =>  $revisions_string,
    // other
    'info_message'          =>  $revision['message'],
    'copyright'             =>  '(c) Karel Wintersky, 2015, ver 0.5.1'
);

$tpl_file = 'template/edit.html';

$html = websun_parse_template_path($TEMPLATE_DATA, $tpl_file);

echo $html;