<?php
header("Content-Type: image/jpeg");
$im = @imagecreate(500, 20)
    or die("Cannot Initialize new GD image stream");
$color_fondo = imagecolorallocate($im, 0, 0, 0);
$color_texto = imagecolorallocate($im, 233, 14, 91);
imagestring($im, 1, 5, 5,  "A Simple Text String---------------", $color_texto);
imagepng($im);
imagedestroy($im);
?>