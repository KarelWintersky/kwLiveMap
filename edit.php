<?php
require_once 'core/config/config.php';
require_once 'core/core.php';
require_once 'core/core.auth.php';
require_once 'core/core.pdo.php';
global $CONFIG;

// init null values
$revisions_string = '';

// check access rights
$is_can_edit = auth_CanIEdit();

$html_callback = ($_GET['frontend'] == 'imagemap') ? '/map/index.html' : '/map/leaflet.html';

$coords_col = intval($_GET['col']);
$coords_row = intval($_GET['row']);

// коннект с базой
try {
    $dbh = new PDO($CONFIG['pdo_host'], $CONFIG['username'], $CONFIG['password']);
    $dbh->exec("SET NAMES utf8");
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    echo $e->getMessage();
}


// проверяем, сколько ревизий текста у гекса
try {
    $sth = $dbh->query("SELECT COUNT(id) AS count_id FROM lme_map_tiles_data WHERE `hexcol` = {$coords_col} AND `hexrow` = {$coords_row}", PDO::FETCH_ASSOC);

    $row = $sth->fetch(PDO::FETCH_ASSOC);

    $revisions_count = $row['count_id'];
}
catch (PDOException $e) {
    die($e->getMessage());
}

// в зависимости от количества ревизий заполняем данные шаблона
if ($revisions_count != 0) {

    // если во входящих параметрах нет идентификатора ревизии - загружаем последнюю
    if (!isset($_GET['revision'])) {

        // загружаем последнюю ревизию
        try{

            $sth = $dbh->query("
            SELECT title, content, editor, edit_reason
            FROM lme_map_tiles_data
            WHERE `hexcol` = {$coords_col} AND `hexrow` = {$coords_row}
            ORDER BY edit_date DESC
            LIMIT 1");

            $row = $sth->fetch(PDO::FETCH_ASSOC);

            $template = array(
                'message'   =>  '',
                'text'      =>  $row['content'],
                'title'     =>  $row['title'],
                'edit_reason'   =>  $row['edit_reason'],
                'editor_name'   =>  at($_COOKIE, 'kwtrpglme_auth_editorname', ""),
            );

        }
        catch (PDOException $e) {
            die(__LINE__ . $e->getMessage());
        }
    } else {
        // загружаем нужную ревизию по идентификатору
        $revision = $_GET['revision'];
        try {
            $sth = $dbh->query("
            SELECT title, content, editor, edit_reason
            FROM lme_map_tiles_data
            WHERE id = {$revision}
            ");

            $row = $sth->fetch(PDO::FETCH_ASSOC);

            $template = array(
                'message'   =>  'Fork revision #'.$revision,
                'text'      =>  $row['content'],
                'title'     =>  $row['title'],
                'edit_reason'   =>  "".$row['edit_reason'],
                'editor_name'   =>  at($_COOKIE, 'kwtrpglme_auth_editorname', ""),
            );


        }
        catch (PDOException $e){
            die($e->getMessage());
        }

    }


    // выгружаем список ревизий

    try{
        $sth = $dbh->query("
SELECT id, hexcol, hexrow, hexcoords, DATE_FORMAT(FROM_UNIXTIME(edit_date), '%d-%m-%Y') AS edit_date, editor, edit_reason, ip
FROM lme_map_tiles_data
WHERE hexcol = {$coords_col} AND hexrow = {$coords_row}"
            , PDO::FETCH_OBJ);

        while($row = $sth->fetch(PDO::FETCH_OBJ)){
            $revisions_string .= sprintf(
                '<li><a href="edit.php?frontend=imagemap&col=%s&row=%s&hexcoord=%s&revision=%s">%s, %s</a> <em>(%s)</em>: %s</li>'."\r\n",
                $row->hexcol,
                $row->hexrow,
                $row->hexcoords,
                $row->id,
                $row->edit_date,
                $row->editor,
                $row->ip,
                $row->edit_reason
            );
        }
    }
    catch(PDOException $e) {
        die($e->getMessage());
    }
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
?>
<!DOCTYPE html>
<html>
<head>
    <title>kwRPG Map Engine :: Trollfjorden</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script type="text/javascript" src="js/html5shiv.js"></script>
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script src="js/tinymce/tinymce.min.js"></script>
    <script src="js/tinymce.config.js"></script>
    <!-- styles -->
    <link rel="stylesheet" type="text/css" href="css/main.css" />
    <script type="text/javascript">
        $(document).ready(function(){
            tinify(tiny_config['full'], 'edit-textarea');
            $("#actor-back").on('click', function(){
                document.location.href = '<?php echo $html_callback  ?>';
            });
        });
    </script>
    <style type="text/css">
        body {
            font-size:14pt;
        }
        fieldset {
            margin: 0;
        }
        .fields_area {
            float: left;
            clear: both;
        }
        .field {
            clear:both;
            text-align:right;
            line-height:20pt;
        }
        label {
            float:left;
            padding-right:55px;
        }
        .label_textarea {
            padding: 0;
        }
        .label_fullwidth {
            width: 100%;
        }
        .clear {
            clear: both;
        }
        ul {
            margin:0;
        }
        #revisions_fieldset {
            float: left;
            clear: both;
            width:90%;
            font-size: 10pt;
        }
    </style>
</head>

<body>
<h2>Координаты: <?php echo $_GET['hexcoord']?> </h2>

<form action="core/action.put.content.php" method="post">
    <input type="hidden" name="hexcoords" value="<?php echo $_GET['hexcoord']?>">
    <input type="hidden" name="hexcoord_col" value="<?php echo $coords_col; ?>">
    <input type="hidden" name="hexcoord_row" value="<?php echo $coords_row; ?>">
    <input type="hidden" name="callback" value="<?php echo $_GET['frontend']; ?>">

    <fieldset class="fields_area">
        <div class="field ">
            <label for="title">Название региона:</label>
            <input type="text" name="title" id="title" size="60" value="<?php echo $template['title'] ?>">
        </div>
    </fieldset>

    <label for="edit-textarea" class="label_textarea label_fullwidth">
        <textarea name="textdata" id="edit-textarea" cols="10" tabindex="3"><?php echo $template['text'] ?></textarea>
    </label>

    <fieldset  class="fields_area">
        <div class="field">
            <label for="edit_reason">Причина редактирования:</label>
            <input type="text" name="edit_reason" id="edit_reason" size="60" value="<?php echo $template['edit_reason'] ?>">
        </div>
        <div class="field">
            <label for="editor_name">Редактор:</label>
            <input type="text" name="editor_name" id="editor_name" size="60" value="<?php echo $template['editor_name'] ?>">
        </div>
        <span> <?php echo $template['message']; ?> </span>

    </fieldset>
    <div class="clear"></div>
    <fieldset>
        <div class="label_fullwidth">
            <button type="submit">Сохранить</button>
            <button type="button" id="actor-back" style="float:right">Назад на карту</button>
        </div>
    </fieldset>

</form>
<br/>
<fieldset id="revisions_fieldset">
    <legend>Revision history</legend>
    <ul>
        <?php echo $revisions_string; ?>
    </ul>
</fieldset>
<div class="clear"></div>
<hr>
<small><em>(c) Karel Wintersky, Aug 2015</em></small>

</body>
</html>