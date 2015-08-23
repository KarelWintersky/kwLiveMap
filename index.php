<!DOCTYPE html>
<html>
<head>
    <title>kwRPG Map Engine :: Trollfjorden</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <!-- styles -->
    <link rel="stylesheet" type="text/css" href="css/main.css" />
    <link rel="stylesheet" href="css/colorbox.css">
    <!-- core js -->
    <script type="text/javascript" src="js/html5shiv.js"></script>
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.colorbox.js"></script>
    <script type="text/javascript" src="js/jquery.tiny-draggable.js"></script>
    <!-- custom js -->
    <script type="text/javascript" src="js/kwrpgmaps.core.js"></script>
    <!-- map js -->
    <script type="text/javascript" src="js/map.trollfjorden.js"></script>

    <!-- internal js -->
    <script type="text/javascript">
        var hex_loaded_content = '';
        var hex_current_coords = '';


        $(document).ready(function(){
            $.ajaxSetup({cache: false});

            $("#themap").on('click', function(event){
                var coords = getXYCoords(this, event);
                var hex = getHex(coords.x, coords.y);
                hex_current_coords = pad(hex.col, 2) + pad(hex.row, 2);

                if (loadHexInfo(hex, "#colorboxed-view-content")) {
                    var str_title =
                            '&nbsp;<img src="images/cursor_drag_arrow_2.png" align="bottom" width="16" width="16">&nbsp;&nbsp;'
                                    + 'Координаты: </span>'
                                    + hex_current_coords
                                    + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
                                    + '<button id="actor-edit"> Редактировать </button>';

                    $.colorbox({
                        inline: true,
                        href: '#colorboxed-view',
                        width: 800,
                        height: 600,
                        title: str_title
                    });
                }
            }); // themap click

            $(document).on('click', '#actor-edit', function(){
                document.location.href = 'edit.php?id='+ hex_current_coords;
            });

            $("#colorbox").tinyDraggable({
                handle: '#cboxTitle',
                exclude: 'button'
            });
        });
    </script>
</head>
<body>
<img src="images/maps/trollfjordenmap.png" id="themap" border="1">
<br>
Нажмите на <strike>кексик</strike> гексик карты и... =^.^= <a href="leaflet.html">Leaflet version</a>
<div style="display:none">
    <div id="colorboxed-view" style="padding:10px; background:#fff;">
        <div id="colorboxed-view-content"></div>
    </div>
</div>


</body>
</html>