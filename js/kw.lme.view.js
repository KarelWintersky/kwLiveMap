function popupEmptyInfoBox(hexgrid_coords)
{
    $("#colorboxed-view-content").html('По гексу `'+ hexgrid_coords.hexcoord +'` информации нет! Но можно добавить: <button id="actor-edit"> Редактировать </button>');
    $.colorbox({
        inline: true,
        href: '#colorboxed-view',
        width: 800,
        height: 600,
        title: 'Информации нет!'
    });
}

function popupInfoBox(hexgrid_coords)
{
    //@todo:брать title из отдельного дива или переменной, обдумать!
    var str_title =
        '&nbsp;<img src="images/cursor_drag_arrow_2.png" align="bottom" width="16" width="16">&nbsp;&nbsp;'
            + 'Координаты: </span>'
            + pad(hexgrid_coords.col, 2) + pad(hexgrid_coords.row, 2)
            + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
            + '<button id="actor-edit"> Редактировать </button>';
    // set window location hash
    setHash('#hexcoords='+hexgrid_coords.hexcoord);

    $.colorbox({
        href: 'core/action.get.content.php?'+$.param(hexgrid_coords),
        width: 800,
        height: 600,
        title: str_title
    });

}