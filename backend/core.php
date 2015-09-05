<?php

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

 
