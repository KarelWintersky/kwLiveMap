var sqrt3 = Math.sqrt(3);

function pad(a,b){return(1e15+a+"").slice(-b)}

function floor(x) { return Math.floor(x); }

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
function Vector(X,Y)
{
    return {
        x: X,
        y: Y
    }
}

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
        col     :   ++Y
    }
}