<?php

// check access rights

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

            var request = $.ajax({
                url:    'core/get.content.php?hexcoord=<?php echo $_GET['id']?>',
                async:  false,
                type:   'GET',
            });
            request.done(function(data){
                $("#edit-textarea").val(data);
            });
            tinyMCE.settings = tiny_config['full'];
            tinyMCE.execCommand('mceAddEditor', true, 'edit-textarea');


            $("#actor-back").on('click', function(){
                document.location.href = '/map/';
            });
        });

    </script>
</head>

<body>
<h2>Координаты: <?php echo $_GET['id']?> </h2>
    <form action="core/save.content.php" method="post">
        <input type="hidden" name="hexcoords" value="<?php echo $_GET['id']?>">
        <textarea name="textdata" id="edit-textarea" cols="10" tabindex="3"></textarea>
        <button type="submit">Сохранить</button>
        <button type="button" id="actor-back" style="float:right">Назад на карту</button>
    </form>
</body>
</html>