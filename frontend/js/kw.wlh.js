/**
 Window Location Hash library
 */

/**
 *
 * @param haystack
 * @param needle
 * @param offset
 * @return {Boolean}
 */
function strpos (haystack, needle, offset) {
    var i = (haystack+'').indexOf(needle, (offset || 0));
    return i === -1 ? false : i;
}

/**
 * Разделить строку по параметрам © http://a2x.ru/?p=140
 * @param queryString
 * @param limiter
 * @return {Array} возвращает массив вида 'valuename' => 'valuedata'
 */
function getQuery( queryString , limiter)
{
    var parts = queryString.split((limiter || '&')); //делим строку по & - parama1=1
    var arr = {};
    for (var i=0, vl=parts.length; i < vl; i++)
    {
        var pair = parts[i].split("="); //делим параметр со значением по =, и пишем в ассоциативный массив arr['param1'] = 1
        arr[pair[0]] = pair[1];
    }
    return arr;
}

/**
 *
 */
function clearHash()
{
    if ('pushState' in history) {
        window.history.pushState('', window.title, window.location.pathname + window.location.search);
    } else {
        window.location.hash = '';
    }
}

/**
 *
 * @param hashstr
 */
function setHash(hashstr)
{
    window.location.hash = hashstr;
}

/**
 *
 * @return {String}
 */
function getHash()
{
    return window.location.hash;
}

function getHashArray()
{
    var hash = window.location.hash;
    hash = hash.substr(hash.indexOf('#', 0) + 1);
    var hashes_arr
        = (hash.length > 0)
        ? getQuery(hash)
        : {};
    return hashes_arr;
}