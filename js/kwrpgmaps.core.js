var sqrt3 = Math.sqrt(3);

/**
 * Дополняет value нулями слева до полной длины строкового представления длиной stringsize.
 * @param value
 * @param stringsize
 * @return {*|Blob}
 */
function pad(value,stringsize){return(1e15+value+"").slice(-stringsize)}

/**
 * Возвращает координаты точки, на которую кликнули по картинке.
 * Вызов: getXYCoords('#imageid', event);
 * @param target - картинка
 * @param event - событие, вызвавшее клик
 * @return {Object}
 */
function getXYCoords(target, event)
{
    var $target = $(target);
    var offset = $target.offset();
    var bordersize = $target.attr('border');
    return {
        x:  (event.pageX - offset.left - bordersize) | 0,
        y:  (event.pageY - offset.top - bordersize) | 0
    }
}

/**
 * Возвращает объект-вектор
 * @param X
 * @param Y
 * @return { x: X, y: Y}
 */
function Vector(X,Y)
{
    return {
        x: X,
        y: Y
    }
}


/**
 *
 * @param mx
 * @param my
 * @return { col, row, hexcoord (col+row)}
 */
function getHex(mx, my)
{
    var r = map_object.hexgrid_size;
    var width = r * sqrt3;
    var rowheight = 1.5 * r;
    var height = 2.0 * r;
    var halfwidth = r * sqrt3 * 0.5;


    var rise = height - rowheight;
    var slope = rise / halfwidth;
    // поворачиваем оси
    var X = Math.floor(my / width);
    var Y = Math.floor(mx / rowheight);

    var ox = my - X * width;
    var oy = mx - Y * rowheight;

    if (Y % 2 == 0)
    {
        if ( oy < (-slope * ox + rise )) {
            X--; Y--;
        } else
        if ( oy < (slope * ox - rise) ) {
            Y--;
        }
    } else {
        if (ox >= halfwidth)
        {
            if (oy < (-slope * ox + rise * 2.0)) {
                Y--;
            }
        } else {
            if (oy < (slope * ox)) {
                Y--;
            } else {
                X--;
            }
        }
    }

    // снова инвертирование, только вывода - X отвечает за высоту, Y за ширину
    return {
        row     :   ++X,
        col     :   ++Y,
        hexcoord:   pad(Y, 2) + pad(X, 2)
    }
}

/**
 *
 * @param hexcoord
 * @param target
 * @return {Boolean}
 */
function loadHexInfo(hexcoord, target)
{
    if (hexcoord.col && hexcoord.row) {
        var request = $.ajax({
            url:    'core/action.get.content.php?'+$.param(hexcoord),
            async:  false,
            type:   'GET',
        });
        request.done(function(data){
            hex_loaded_content = data;
            $(target).html(data)
        });
        return true;
    } else {
        return false;
    }
}

/**
 * Проверяет в базе наличие информации о гексе с координатами, описанными в hexcoord.
 * Возвращает:
 * -    anydata - есть какая-то инфа для просмотра
 * -    ignore  - инфы нет, но мы не можем редактировать гекс и клик игнорируем
 * -    empty   - инфы нет, но мы можем редактировать гекс
 * @param hexcoord
 * @return {ignore|empty|anydata}
 */
function checkHexContent(hexcoord)
{
    var ret;
    if (hexcoord.col && hexcoord.row) {
        var request = $.ajax({
            url:    'core/action.check.content.php?'+ $.param(hexcoord),
            async:  false,
            type:   'GET'
        });
        request.done(function(data){
            ret = $.parseJSON(data);
        });
    } else {
        ret['result'] = 'ignore';
    }
    return ret;
}