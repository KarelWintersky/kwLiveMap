<?php
require_once 'core/config/config.php';
require_once 'core/core.php';
require_once 'core/core.auth.php';
require_once 'core/core.pdo.php';
global $CONFIG;

// check access rights
$is_can_edit = auth_CanIEdit();

$html_callback = ($_GET['frontend'] == 'imagemap') ? '/map/index.html' : '/map/leaflet.html';

$coords_col = intval($_GET['col']);
$coords_row = intval($_GET['row']);

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

    try{

    }
    catch (PDOException $e) {
        die($e->getMessage());
    }

    $sth = $dbh->query("
    SELECT title, content, editor, edit_reason
    FROM lme_map_tiles_data
    WHERE `hexcol` = {$coords_col} AND `hexrow` = {$coords_row}
    ORDER BY edit_date DESC
    LIMIT 1");

    $row = $sth->fetch(PDO::FETCH_ASSOC);

    $template = array(
        'text'      =>  $row['content'],
        'title'     =>  $row['title'],
        'edit_reason'   =>  $row['edit_reason'],
        'editor_name'   =>  at($_COOKIE, 'kwtrpglme_auth_editorname', $row['editor']),
    );


} else {
    // заполняем данные
    $template = array(
        'text'      =>  '',
        'title'     =>  '',
        'edit_reason'   =>  'initial commit',
        'editor_name'   =>  at($_COOKIE, 'kwtrpglme_auth_editorname', '?????'),
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
            line-height:30px;
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
<fieldset class="fields_area" style="width:90%">
    <legend>Revision history</legend>
    <ul>
        <li>
            <a -href="edit.php?frontend=imagemap&col=1&row=1&hexcoord=0101&revision=1">Дата, Arris</a> <em>(IP)</em>: Первое добавление
        </li>
    </ul>
</fieldset>
</body>
</html>