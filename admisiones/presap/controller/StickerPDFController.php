<?php

namespace Admisiones\Controller;

//define('FPDF_FONTPATH', 'root/font/');
require('root/fpdf/fpdf.php');
require('root/phpqrcode/qrlib.php');

use Exception;
use FPDF;
use Tools\{QRcode, QRimage};


//use Tools\QRcode;


/**
 * Description of StickerPDFController
 *
 * @author Edier Andrés Villaneda Navarro
 */
class StickerPDFController extends FPDF
{

    /**
     * Encabeczado de la pagina
     */
    function LogoEmpresa()
    {
        // Logo
        $this->Image('logo.png', 10, 8, 33);
        // Arial bold 15
        $this->SetFont('Arial', 'B', 15);
        // Movernos a la derecha
        $this->Cell(80);
        // Título
        $this->Cell(30, 10, 'Title', 1, 0, 'C');
        // Salto de línea
        $this->Ln(20);
    }


    /**
     * Imprime una celda (de área rectangular) bordes opcionales, color de fondo y secuencia de carácteres La esquina
     * superior izquierda de la celda corresponde a la posición actual. El texto puede ser alineado o centrado.
     * Despues de invocar, la posición actual se desplaza a la derecha o la siguietne línea. Es posible poner una
     * referencia en el texto.
     * Si esta el salto de página automático habilitado y la celda esta por fuera del límite, es realizado un salto de
     * página antes de producir la salida
     * @param int $x El valor de la abscisa
     * @param int $y El valor de la ordenada
     * @param string $txt cadena a ser impresa. Valor por defecto: cadena vacia.
     * @param string $align Permite centrar o alinear el texto. Los posibles valores son: L o una cadena vacia: alineación izquierda (valor por defecto) C: centro R: alineación derecha
     * @param int $border Indica si los bordes deben se dibujados alrededor de la celda. El valor puede ser un número: 0: sin borde 1: marco
     * @param int $w Ancho de Celda. Si es 0, la celda se extiende hasta la márgen derecha
     * @param int $h Alto de celda. Valor por defecto: 0.
     */
    function Celda($x, $y, $txt = '', $align = '', $border = 0, $w = 0, $h = 0)
    {
        $this->SetXY($x, $y);
        $this->Cell($w, $h, $txt, $border, 0, $align);
    }
    function Escribir($x, $y, $txt = '', $h = 0)
    {
        $this->Text($x, $y, $txt);
    }

    /**
     * Este script implementa códigos de barras Code 39. Un código de barras Code 39 puede codificar una cadena
     * con los siguientes caracteres: dígitos (0 a 9), letras mayúsculas (A a Z) y 8 caracteres
     * adicionales (-.Espacio $ / +% *).
     * @param $xpos abscisa del código de barras
     * @param $ypos ordenada del código de barras
     * @param $code valor del código de barras
     * @param float $baseline corresponde al ancho de una barra ancha (por defecto es 0.5)
     * @param int $height altura de la barra (por defecto es 5)
     * @throws Exception
     */
    function Code39($xpos, $ypos, $code, $baseline = 0.5, $height = 5)
    {

        $wide = $baseline;
        $narrow = $baseline / 3;
        $gap = $narrow;

        $barChar['0'] = 'nnnwwnwnn';
        $barChar['1'] = 'wnnwnnnnw';
        $barChar['2'] = 'nnwwnnnnw';
        $barChar['3'] = 'wnwwnnnnn';
        $barChar['4'] = 'nnnwwnnnw';
        $barChar['5'] = 'wnnwwnnnn';
        $barChar['6'] = 'nnwwwnnnn';
        $barChar['7'] = 'nnnwnnwnw';
        $barChar['8'] = 'wnnwnnwnn';
        $barChar['9'] = 'nnwwnnwnn';
        $barChar['A'] = 'wnnnnwnnw';
        $barChar['B'] = 'nnwnnwnnw';
        $barChar['C'] = 'wnwnnwnnn';
        $barChar['D'] = 'nnnnwwnnw';
        $barChar['E'] = 'wnnnwwnnn';
        $barChar['F'] = 'nnwnwwnnn';
        $barChar['G'] = 'nnnnnwwnw';
        $barChar['H'] = 'wnnnnwwnn';
        $barChar['I'] = 'nnwnnwwnn';
        $barChar['J'] = 'nnnnwwwnn';
        $barChar['K'] = 'wnnnnnnww';
        $barChar['L'] = 'nnwnnnnww';
        $barChar['M'] = 'wnwnnnnwn';
        $barChar['N'] = 'nnnnwnnww';
        $barChar['O'] = 'wnnnwnnwn';
        $barChar['P'] = 'nnwnwnnwn';
        $barChar['Q'] = 'nnnnnnwww';
        $barChar['R'] = 'wnnnnnwwn';
        $barChar['S'] = 'nnwnnnwwn';
        $barChar['T'] = 'nnnnwnwwn';
        $barChar['U'] = 'wwnnnnnnw';
        $barChar['V'] = 'nwwnnnnnw';
        $barChar['W'] = 'wwwnnnnnn';
        $barChar['X'] = 'nwnnwnnnw';
        $barChar['Y'] = 'wwnnwnnnn';
        $barChar['Z'] = 'nwwnwnnnn';
        $barChar['-'] = 'nwnnnnwnw';
        $barChar['.'] = 'wwnnnnwnn';
        $barChar[' '] = 'nwwnnnwnn';
        $barChar['*'] = 'nwnnwnwnn';
        $barChar['$'] = 'nwnwnwnnn';
        $barChar['/'] = 'nwnwnnnwn';
        $barChar['+'] = 'nwnnnwnwn';
        $barChar['%'] = 'nnnwnwnwn';

        $this->SetFont('Courier', '', 5);
        //$this->Text($xpos, $ypos + $height + 2, $code);
        $this->SetXY($xpos, $ypos + $height);
        $this->Cell(0, 2, $code, 0, 0, 'C');
        $this->SetFillColor(0);

        $code = '*' . strtoupper($code) . '*';
        for ($i = 0; $i < strlen($code); $i++) {
            $char = $code[$i];
            if (!isset($barChar[$char])) {
                $this->Error('Invalid character in barcode: ' . $char);
            }
            $seq = $barChar[$char];
            for ($bar = 0; $bar < 9; $bar++) {
                if ($seq[$bar] == 'n') {
                    $lineWidth = $narrow;
                } else {
                    $lineWidth = $wide;
                }
                if ($bar % 2 == 0) {
                    $this->Rect($xpos, $ypos, $lineWidth, $height, 'F');
                }
                $xpos += $lineWidth;
            }
            $xpos += $gap;
        }
    }

    function QrCode($code, $file = "temp.png")
    {
//        $qr = new QRimage();
//        $qr->png($code, $file);
        QRcode::png($code, $file);
        return $file;
    }

}
