<?php

/**
 * Эквивалент isset( array[ key ] ) ? array[ key ] : default ;
 * at PHP 7 useless, z = a ?? b;
 * @param $array
 * @param $key
 * @param $default
 */
function at($array, $key, $default)
{
    return isset($array[$key]) ? $array[$key] : $default;
}

 
