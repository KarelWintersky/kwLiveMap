<?php
$hex_coords = $_GET['hexcoord'];

if (file_exists($_SERVER['DOCUMENT_ROOT'].'/map/data/lorem_ipsum.html'))
{
    $file = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/map/data/lorem_ipsum.html', null, null);
} else {
    $file = 'Not found!';
}
echo $file;

