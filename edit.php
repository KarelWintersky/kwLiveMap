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

//
// $dbh = new PDO('mysql:host=localhost;dbname=kwdb', 'root', 'password');
$dbh = new PDO($CONFIG['pdo_host'], $CONFIG['username'], $CONFIG['password']);
$dbh->exec("SET NAMES utf8");

// проверяем, сколько ревизий текста у гекса
try {
    $sth = $dbh->query("SELECT COUNT(id) AS count_id FROM lme_map_tiles_data WHERE `hexcol` = {$coords_col} AND `hexrow` = {$coords_row}", PDO::FETCH_ASSOC);

    $row = $sth->fetch(PDO::FETCH_ASSOC);

    $revisions_count = $row['count_id'];
}
catch (PDOException $e) {
    die($e->getMessage());
}

if ($revisions_count != 0) {

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
</head>

<body>
<h2>Координаты: <?php echo $_GET['hexcoord']?> </h2>

<form action="core/save.content.php" method="post">
    <input type="hidden" name="hexcoords" value="<?php echo $_GET['hexcoord']?>">
    <input type="hidden" name="hexcoord_col" value="<?php echo $coords_col; ?>">
    <input type="hidden" name="hexcoord_row" value="<?php echo $coords_row; ?>">
    <input type="hidden" name="callback" value="<?php echo $_GET['frontend']; ?>">

    Название региона: <input type="text" name="title" size="60" value="<?php echo $template['title'] ?>"><br/><br/>
    <textarea name="textdata" id="edit-textarea" cols="10" tabindex="3"><?php echo $template['text'] ?></textarea>
    <br/>
    Причина редактирования:
    <input type="text" name="edit_reason" value="<?php echo $template['edit_reason'] ?>"><br/><br/>
    Редактор:
    <input type="text" name="editor_name" value="<?php echo $template['editor_name'] ?>"><br/><br/>
    <button type="submit">Сохранить</button>
    <button type="button" id="actor-back" style="float:right">Назад на карту</button>
    <br/>
</form>
<br/>
<fieldset>
    <legend>Revision history</legend>
    is empty!
</fieldset>
</body>
</html>