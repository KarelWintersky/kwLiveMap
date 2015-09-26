<?php
require_once 'backend/_required_libs.php';

// init null values
$revisions_string = '';
$revision = array();

// check access rights
$is_can_edit = auth_CanIEdit();

// эти конструкции выглядят прозрачно и понятно, но их можно заменить на вызовы atordie()
$project_alias
    = isset($_GET['project'])
    ? $_GET['project']
    : die('No such project!');

$map_alias
    = isset($_GET['map'])
    ? $_GET['map']
    : die('No such map!');

setcookie('kwlme_filemanager_storagepath', $project_alias, 0, "/"); // cookie for Responsive Manager

$coords_col = intval( atordie($_GET, 'col', 'X-Coordinate required!'));

$coords_row = intval( atordie($_GET, 'row', 'Y-Coordinate required!'));


// коннект с базой
$dbh = DB_Connect();

// проверяем, сколько ревизий текста у гекса
$revisions_count = DB_GetRevisionsCount($dbh, $coords_col, $coords_row, $project_alias, $map_alias);

// в зависимости от количества ревизий заполняем данные шаблона
if ($revisions_count != 0) {

    // если во входящих параметрах нет идентификатора ревизии - загружаем последнюю
    if (!isset($_GET['revision'])) {
        // загружаем последнюю ревизию
        $revision = DB_GetRevisionLast($dbh, $coords_col, $coords_row, $project_alias, $map_alias);
    } else {
        // загружаем нужную ревизию по идентификатору
        $revision_id = $_GET['revision'];
        $revision = DB_GetRevisionById($dbh, $revision_id);
    }

    // выгружаем список ревизий
    $revisions_string = DB_GetListRevisions($dbh, $coords_col, $coords_row, $project_alias, $map_alias);
} else {
    // заполняем данные
    $revision = array(
        'text'      =>  '',
        'title'     =>  '',
        'edit_reason'   =>  'Первое редактирование',
        'editor_name'   =>  at($_COOKIE, 'kwtrpglme_auth_editorname', ''),
    );
}

$dbh = null;


// параметр callback больше не передаем - отображение карты зависит от настроек в конфиге
$template_data = array(
    'project_alias'          =>  $project_alias,
    'map_alias'              =>  $map_alias,
    //
    'html_callback'         =>  "/{$project_alias}/{$map_alias}",
    'project_title'         =>  'Trollfjorden -- Троллячьи фьорды',
    'map_title'             =>  'основная карта',
    //
    'hexcoord'              =>  $_GET['hexcoord'],
    'coords_col'            =>  $coords_col,
    'coords_row'            =>  $coords_row,
    'region_title'          =>  $revision['title'],
    'region_text'           =>  $revision['text'],
    'region_edit_reason'    =>  $revision['edit_reason'],

    // имя куки возможно стоит перенести в глобальные настройки
    'region_editor_name'    =>  at($_COOKIE, 'kw_trpg_lme_auth_editorname', ""),
    // revisions
    'region_revisions'      =>  $revisions_string,
    // other
    'info_message'          =>  at($revision, 'message', ''),
    'copyright'             =>  '(c) Karel Wintersky, 2015, ver 0.6.+'
);

$tpl_file = 'edit.html';

$html = websun_parse_template_path($template_data, $tpl_file, '$/template');

echo $html;