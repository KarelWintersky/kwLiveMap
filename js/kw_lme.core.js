var sqrt3 = Math.sqrt(3); // 1.732

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
 * Возвращает координаты точки на элементе CANVAS
 * Вызов: var mousePos = getMousePos(canvas, event); - аналогично getXYCoods()
 * @param canvas    - элемент канвас
 * @param evt       - событие
 * @return { x, y }
 */
function getCanvasXYPos(canvas, event) {
    var rect = canvas.getBoundingClientRect();
    var bordersize = $(canvas).attr('border');
    return {
        x: event.clientX - rect.left - bordersize,
        y: event.clientY - rect.top - bordersize
    };
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
 * Возвращает объект-вектор с учетом начала координат
 * @param x
 * @param y
 * @return {Object}
 * @constructor
 */
function Vector0(x,y) {
    return {
        x: Math.floor(x0 + x),
        y: Math.floor(y0 + y)
    };
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

/**
 * Загружает из базы массив с информацией о разведанных областях.
 * @return {
 *    z0101:  {col:"1", row:"1"},
 *    z0301:  {col:"3", row:"1"}
 *    etc
 * }
 */
function loadRevealedAreas()
{
    var ret = '';
    var request = $.ajax({
        url:    'core/action.get.revealed.php',
        async:  false,
        type:   'GET'
    });
    request.done(function(data){
        ret = $.parseJSON(data);
    });
    return ret;
}

/**
 * Возвращает структуру с координатами вершин гексагона, заданного координатами Col-Row
 * требует в глобальной области видимости переменные edge, shift, h, hh
 * @param C
 * @param R
 * @return {Object}
 */
function getHexPath(C, R)
{
    var xperiod = (edge+shift) * (C-1);
    var yperiod = (hh) * (R-1);
    var ybase = ((C % 2)==0) ? hh : 0;
    var hv = {
        v1: Vector(shift        +   xperiod, ybase +        h*(R-1)),
        v2: Vector(shift + edge +	xperiod, ybase +        h*(R-1)),
        v3: Vector(edge  + edge + 	xperiod, ybase + hh	+   h*(R-1)),
        v4: Vector(shift + edge	+ 	xperiod, ybase + h  +   h*(R-1)),
        v5: Vector(shift 		+ 	xperiod, ybase + h  +   h*(R-1)),
        v6: Vector(0 			+ 	xperiod, ybase + hh +   h*(R-1))
    };
    return hv;
}



/**
 * Рисует гексагон
 * @param ctx   - контекст канваса
 * @param c     - колонка (X-координана)
 * @param r     - строка (Y-координана)
 * @param alpha - альфа затемнения
 */
function drawHex(ctx, c, r, alpha)
{
    var hexpath = getHexPath(c, r);
    var local_alpha = alpha || 0.3;

    ctx.lineWidth = 0;
    ctx.strokeStyle = 'rgba(0,0,0,0)';

    ctx.beginPath();
    ctx.moveTo(hexpath.v1.x, hexpath.v1.y);
    ctx.lineTo(hexpath.v2.x, hexpath.v2.y);
    ctx.lineTo(hexpath.v3.x, hexpath.v3.y);
    ctx.lineTo(hexpath.v4.x, hexpath.v4.y);
    ctx.lineTo(hexpath.v5.x, hexpath.v5.y);
    ctx.lineTo(hexpath.v6.x, hexpath.v6.y);
    ctx.closePath();
    ctx.fillStyle = 'rgba(0, 0, 0, ' + local_alpha + ')';
    ctx.fill();
    ctx.stroke();
}