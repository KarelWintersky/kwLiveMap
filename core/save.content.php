<?php

// check access rights


$content = $_POST['textdata'];

$f = fopen($_SERVER['DOCUMENT_ROOT'].'/map/data/lorem_ipsum.html', "w+");
fwrite($f, $content);
fclose($f);

?>

<html>
<head>
    <title>Redirect... </title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="refresh" content="5;URL=/map/">
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
        var delay = 5;
        var pause = step = 0.5;
        var callback = '/map/';
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
<button class="button-huge" onclick="window.location.href='/map/'">Назад
    <br><br>До перехода осталось <span id="wait">5</span> секунд
</button>
</body>
</html>
