<?php
require_once 'config/config.php';
require_once 'core.php';
require_once 'core.auth.php';
require_once 'core.pdo.php';
global $CONFIG;

// check access rights
$is_can_edit = auth_CanIEdit();

if (!$is_can_edit) die('Hacking attempt!');

// $callback = at($_POST, 'callback', '/map/index.html');
$callback
    = ($_POST['callback'] == 'leaflet')
    ? '/map/leaflet.html'
    : '/map/index.html';

$coords_col = intval($_POST['hexcoord_col']);
$coords_row = intval($_POST['hexcoord_row']);


// $dbh = new PDO('mysql:host=localhost;dbname=kwdb', 'root', 'password');
$dbh = new PDO($CONFIG['pdo_host'], $CONFIG['username'], $CONFIG['password']);
$dbh->exec("SET NAMES utf8");

$data = array(
    'hexcol'       =>  $coords_col,
    'hexrow'       =>  $coords_row,
    'coords'    =>  $_POST['hexcoords'],
    'title'     =>  $_POST['title'],
    'content'   =>  $_POST['textdata'],
    'editor'    =>  $_POST['editor_name'],
    'edit_date' =>  time(),
    'edit_reason'=> $_POST['edit_reason'],
    'ip'        =>  $_SERVER['REMOTE_ADDR']
);

$sth = $dbh->prepare("
    INSERT INTO lme_map_tiles_data (`col`, `row`, `coords`, `title`, `content`, `editor`, `edit_date`, `edit_reason`, `ip`)
     VALUES (:hexcol, :hexrow, :coords, :title, :content, :editor, :edit_date, :edit_reason, :ip)");


try {
    $flag = $sth->execute($data);
    // $last_insert_id = $dbh->lastInsertId();

}
catch (PDOException $e) {
    die($e->getMessage());
}


$dbh = null;

$timeout = 5;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Redirect... </title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="refresh" content="<?php echo $timeout; ?>;URL=<?php echo $callback; ?>">
    <style type="text/css">
        #wait {
            color: red;
            font-weight: bold;
        }
        .button-huge {
            height: 120px;
            width:400px;
        }
        .info {
            color: #7b68ee;
            font-weight: bold;
        }
    </style>

    <script type="text/javascript">
        var delay = <?php echo $timeout; ?>;
        var pause = step = 0.5;
        var callback = '<?php echo $callback; ?>';
        var dtf;
        function CountDown()
        {
            if (delay > 0) {
                dtf = delay.toFixed(1);
                document.getElementById("wait").innerHTML = dtf;
                document.title = '...осталось '+ dtf + ' секунд...';
                delay -= step;
                setTimeout("CountDown('wait')", pause*1000);
            } else {
                document.location.href = callback;
            }
        }
    </script>

</head>
<body onLoad="CountDown()">
<div class="info">Собираемся назад на карту</div>
<hr>
<button class="button-huge" onclick="window.location.href='<?php echo $callback; ?>'">Назад
    <br><br>До перехода осталось <span id="wait">5</span> секунд
</button>
</body>
</html>
