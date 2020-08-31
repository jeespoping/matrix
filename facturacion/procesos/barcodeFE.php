<?php
$dato = $_GET['factura'];   $parametro = $_GET['parametro'];
//incluir los archivos necesarios de la libreria Barcode PHP:
require_once('barcodegen.1d-php5.v5.2.1/class/BCGFontFile.php');
require_once('barcodegen.1d-php5.v5.2.1/class/BCGColor.php');
require_once('barcodegen.1d-php5.v5.2.1/class/BCGDrawing.php');
require_once('barcodegen.1d-php5.v5.2.1/class/BCGcode128.barcode.php');

if($parametro == 1)
{
    header('Content-Type: image/png');
//Definir los colores de frente(blanco) y fondo(negro):
    $colorFront = new BCGColor(0, 0, 0);
    $colorBack = new BCGColor(255, 255, 255);
//Crear una instancia de la clase Barcode 128:
    $code = new BCGcode128();
//Asignar la escala o tamaño de las barras
    $code->setScale(6);
//Asignar Alto de las barras:
    $code->setThickness(35);
//Asignar Color de frente:
    $code->setForegroundColor($colorFront);
//Asignar color de fondo:
    $code->setBackgroundColor($colorBack);
//Asignar valor al codigo:
    $code->parse($dato);
//Crear un dibujador y asignar la instancia del código creado:
    $drawing = new BCGDrawing('', $colorBack);
    $drawing->setBarcode($code);
//Dibujar el código:
    $drawing->draw();
    $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
}
else
{
    header('Content-Type: image/png');
//Definir los colores de frente(blanco) y fondo(negro):
    $colorFront = new BCGColor(0, 0, 0);
    $colorBack = new BCGColor(255, 255, 255);
//Crear una instancia de la clase Barcode 128:
    $code = new BCGcode128();
//Asignar la escala o tamaño de las barras
    $code->setScale(4);
//Asignar Alto de las barras:
    $code->setThickness(30);
//Asignar Color de frente:
    $code->setForegroundColor($colorFront);
//Asignar color de fondo:
    $code->setBackgroundColor($colorBack);
//Asignar valor al codigo:
    $code->parse($dato);
//Crear un dibujador y asignar la instancia del código creado:
    $drawing = new BCGDrawing('', $colorBack);
    $drawing->setBarcode($code);
//Dibujar el código:
    $drawing->draw();
    $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
}