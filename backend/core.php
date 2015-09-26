<?php

/**
 * заглушка
 * @return bool
 */
function auth_CanIEdit()
{
    return true;
}

/**
 * Эквивалент isset( array[ key ] ) ? array[ key ] : default ;
 * at PHP 7 useless, z = a ?? b;
 * А точнее z = $array[ $key ] ?? $default;
 * @param $array    - массив, в котором ищем значение
 * @param $key      - ключ
 * @param $default  - значение по умолчанию
 */
function at($array, $key, $default)
{
    return isset($array[$key]) ? $array[$key] : $default;
}

/**
 * Аналог at(), только если не находит - умирает с сообщением.
 * @param $array    - массив, в котором ищем значение
 * @param $key      - ключ
 * @param string $message   - сообщение при смерти скрипта
 * @return mixed
 */
function atordie($array, $key, $message = '')
{
    if (!isset( $array[ $key ])) {
        die($message);
    } else {
        return $array[ $key ];
    }
    /*
    // эквивалентно:
    return
        (isset($array[ $key ]))
        ? $array[ $key ]
        : die($message);
    */
}

/**
 * Удаляет куку
 * @param $cookie_name
 */
function unsetcookie($cookie_name)
{
    unset($_COOKIE[$cookie_name]);
    setcookie($cookie_name, null, -1, '/');
}

/**
 * Instant redirect по указанному URL
 * @param $url
 */
function redirect($url)
{
    if (headers_sent() === false) header('Location: '.$url);
    die();
}

/**
 * @return string
 */
function getCopyright()
{
    global $CONFIG;
    return '(c) Karel Wintersky, 2015, ver '.$CONFIG['version'];
}