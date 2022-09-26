<?php
define('USE_SESSION', true);
//символы, из которых будет составляться код капчи
$chars = '123456789abcdefghjkmnpqrstuvwxyz';
//Количество символов в капче
$length = 6;
//Генерируем код
$code = substr(str_shuffle($chars), 0, $length);

session_start();
$_SESSION['captcha'] =  $code;
session_write_close();

//изображение из файла
$image = imagecreatefrompng(__DIR__ . '/images/captcha.png');
//размер шрифта в пунктах
$size = 16;
//цвет текста
$color = imagecolorallocate($image, 66, 182, 66);
//путь к шрифту
$font = __DIR__ . '/fonts/oswald.ttf';
//угол в градусах
$angle = rand(-10, 10);
//координаты точки для первого символа текста
$x = 25;
$y = 30;
//добавление текста на изображение
imagefttext($image, $size, $angle, $x, $y, $color, $font, $code);
//заголовки
header('Cache-Control: no-store, must-revalidate');
header('Expires: 0');
header('Content-Type: image/png');
//вывод изображения
imagepng($image);
//удаление изображения
imagedestroy($image);
