;var sqrt3 = Math.sqrt(3); // 1.732

/**
 * Test for an empty Javascript object?
 * @param obj
 * @return {Boolean}
 */
function isEmpty(obj) {
    return Object.keys(obj).length === 0;
}

/**
 * Дополняет value нулями слева до полной длины строкового представления длиной stringsize.
 * @param value
 * @param stringsize
 * @return {*|Blob}
 */
function pad(value,stringsize){return(1e15+value+"").slice(-stringsize)}

/**
 * Возвращает координаты точки, на которую кликнули по картинке.
 * Вызов: getImageXYCoords('#imageid', event);
 * @param target - картинка
 * @param event - событие, вызвавшее клик
 * @return {Object}
 */
function getImageXYCoords(target, event)
{
    var $target = $(target);
    var offset = $target.offset();
    var bordersize = $target.attr('border');
    return {
        x:  (event.pageX - offset.left - bordersize) | 0,
        y:  (event.pageY - offset.top  - bordersize) | 0
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
    var bordersize = map_object.border_size || 0;
    return {
        x: event.clientX - rect.left - bordersize,
        y: event.clientY - rect.top  - bordersize
    };
}

/**
 * Возвращает объект-вектор с учетом
 * @param X
 * @param Y
 * @return { x: X, y: Y}
 */
function Vector(X,Y)
{
    var local_x = map_object.x0 || 0;
    var local_y = map_object.y0 || 0;
    return {
        x: local_x + X,
        y: local_y + Y
    }
}

/**
 * Возвращает объект-вектор с учетом начала координат, использует глобальный объект map_object
 * @param x
 * @param y
 * @return {Object}
 * @constructor
 */
function Vector0(x,y) {
    return {
        x: Math.floor(map_object.x0 + x),
        y: Math.floor(map_object.y0 + y)
    };
}


/**
 * Вычисляет координаты гекса по указанным пиксельным координатам.
 * Требуется доработка под произвольный размер гекса (описанный в map_object) и его ориентацию.
 * @param mx
 * @param my
 * @return { col, row, hexcoord (col+row)}
 */
function getHex(mx, my)
{
    var r = map_object.grid_edge;
    // внимание, все следующие значения даны для OY-ориентированной сетки!!!
    // Аррис, не забудь!
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
        row     :   ++X,    // ++, потому что отсчет начинается от нуля
        col     :   ++Y,
        hexcoord:   pad(Y, 2) + pad(X, 2)
    }
}

/**
 * @todo: ИСПОЛЬЗУЕТСЯ ТОЛЬКО В LEAFLET, обеспечивает устаревшую механику
 * @param hexcoord
 * @param target
 * @return {Boolean}
 */
function loadHexInfo(hexcoord, target)
{
    if (hexcoord.col && hexcoord.row) {
        var request = $.ajax({
            url:    '/backend/action.get.content.php?'+$.param(hexcoord),
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
function checkHexContent(hexcoord, projectdata)
{
    var ret = [];
    if ((hexcoord.col && hexcoord.row)
        && (hexcoord.col != (map_object.max_col + 1))
        ) {
        var request = $.ajax({
            url:    '/backend/action.check.content.php?'+ $.param(hexcoord) + '&' + $.param(projectdata),
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
 * @param projectdata   - массив с информацией о карте и проекте {project_name, map_name }
 * @return array, пример: {
 *
 *    z0101:  {col:"1", row:"1"},
 *    z0301:  {col:"3", row:"1"}
 * }
 * @return {String}
 */
function loadRevealedAreas(projectdata)
{
    var ret = '';
    var request = $.ajax({
        url:    '/backend/action.get.revealed.php?'+$.param(projectdata),
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
 * требует в глобальной области видимости объект map_object
 * @param C
 * @param R
 * @return {Object}
 */
function getHexPath(C, R)
{
    var xperiod = (map_object.grid_edge + map_object.grid_shift) * (C-1);
    var yperiod = (map_object.grid_halftransversive) * (R-1);
    var ybase = ((C % 2)==0) ? map_object.grid_halftransversive : 0;

    var shift = map_object.grid_shift;
    var edge = map_object.grid_edge;
    var hh = map_object.grid_halftransversive;
    var h = map_object.grid_transversive;

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

/**
 * Рисует туман войны в областях, не указанных в объекте revealed_areas
 * @param canvas_context - контекст канваса
 * @param revealed_areas - объект данными об исследованных зонах
 * @param alpha          - альфа
 */
function drawFogOfWar(canvas_context, revealed_areas, alpha)
{

    var zkey,
        maxcol  = map_object.max_col,
        maxrow  = map_object.max_row;
    var localalpha  = alpha || map_object.areas_hidden;
    var fullfog = isEmpty(revealed_areas);

    for (var col=1; col <= maxcol; col++) {
        for (var row=1; row <= maxrow; row++) {
            zkey = 'z'+pad(col,2)+pad(row,2);

            if (!fullfog) {
                if (zkey in revealed_areas) continue;
                if (row == maxrow && (col % 2) == 0) continue;
            }

            drawHex(canvas_context, col, row, localalpha);
        }
    }
}

/**
 * Проводит доинициализацию объекта map_object
 */
function initHexGrid()
{
    map_object.grid_shift = map_object.grid_edge / 2;
    map_object.grid_halftransversive = map_object.grid_transversive / 2;
    map_object.x0   =   map_object.border_size;
    map_object.y0   =   map_object.border_size;
}


