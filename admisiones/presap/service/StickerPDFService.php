<?php

namespace Admisiones\service;

require('root/fpdf/fpdf.php');

use Exception;
use FPDF;

class StickerPDFService extends FPDF
{

    public function __construct($orientation = 'P', $unit = 'mm', $size = 'A4')
    {
        parent::__construct($orientation, $unit, $size);
        $this->AddPage();
        $this->SetMargins(1, 1, 1);
        $this->SetFont('Arial', 'B', 8);
    }


    /**
     * Generar sticker
     * @param        $nombreCompleto
     * @param        $documento
     * @param        $historia
     * @param        $ingreso
     * @param        $sexo
     * @param        $edad
     * @param        $dx
     * @param        $eps
     * @param string $empresa
     * @throws Exception
     */
    function generarSticker($nombreCompleto, $documento, $historia, $ingreso, $sexo, $edad, $dx, $eps, $empresa = "")
    {
        $this->Celda(4, 2, $nombreCompleto, 'C');
        if ($empresa) {
//            $this->LogoEmpresa($empresa);
        }

        $this->CodigoBarras(4, 4, $documento, 0.40, 10);

        $this->Escribir(4, 22, 'Historia: ', 19, $historia);
        $this->Escribir(31, 22, 'Ingreso: ', 44, $ingreso);
        $this->Escribir(4, 28, 'Sexo: ', 12, $sexo);
        $this->Escribir(16, 28, 'Edad: ', 24, $edad);
        $this->Escribir(37, 28, 'Dx: ', 42, $dx);
        $this->Escribir(4, 35, 'EPS: ', 10, $eps);
        $this->TextWithDirection(3, 37, 'Clinica de Las Americas', 'U');
        $this->Output();
    }

    /**
     * Posiciona logo de la empresa.
     * @param string $aliasEmpresa Alias de la empresa.
     */
    function LogoEmpresa(string $aliasEmpresa)
    {
        $imagePath = dirname(__FILE__) . "/../../../images/medical/root/$aliasEmpresa.jpg";
        $fileInfo = pathinfo($imagePath);

        // Logo
        $this->Image($imagePath, 1, 4, 10, 10, $fileInfo['extension']);
    }


    /**
     * Imprime una celda (de �rea rectangular) bordes opcionales, color de fondo y secuencia de car�cteres La
     * esquina superior izquierda de la celda corresponde a la posici�n actual. El texto puede ser alineado o
     * centrado. Despues de invocar, la posici�n actual se desplaza a la derecha o la siguietne l�nea. Es posible
     * poner una referencia en el texto. Si esta el salto de p�gina autom�tico habilitado y la celda esta por fuera
     * del l�mite, es realizado un salto de p�gina antes de producir la salida
     * @param int    $x      El valor de la abscisa
     * @param int    $y      El valor de la ordenada
     * @param string $txt    cadena a ser impresa. Valor por defecto: cadena vacia.
     * @param string $align  Permite centrar o alinear el texto. Los posibles valores son: L o una cadena vacia:
     *                       alineaci�n izquierda (valor por defecto) C: centro R: alineaci�n derecha
     * @param int    $border Indica si los bordes deben se dibujados alrededor de la celda. El valor puede ser un
     *                       n�mero: 0: sin borde 1: marco
     * @param int    $w      Ancho de Celda. Si es 0, la celda se extiende hasta la m�rgen derecha
     * @param int    $h      Alto de celda. Valor por defecto: 0.
     */
    function Celda($x, $y, $txt = '', $align = '', $border = 0, $w = 0, $h = 0)
    {
        $this->SetXY($x, $y);
        $this->Cell($w, $h, $txt, $border, 0, $align);
    }

    /**
     * Pocisionar y escribir texto en el documento.
     * @param int    $xLabel Posicion Horizontal de la etiqueta
     * @param int    $y      Posicion vertical
     * @param string $label  Etiqueta del texto
     * @param int    $xTexto Posicion horizontal de texto
     * @param string $texto  Texto del campo
     */
    function Escribir(int $xLabel, int $y, string $label = '', int $xTexto, string $texto = '')
    {
        $this->SetTextColor(96, 96, 96);
        $this->SetFont('Courier', 'B', 8);
        $this->Text($xLabel, $y, $label);
        if (!is_null($texto) || !$texto != '') {
            $this->SetTextColor(0, 0, 0);
            $this->SetFont('Arial', '', 9);
            $this->Text($xTexto, $y, $texto);
        }
    }

    /**
     * Este script implementa c�digos de barras Code 39. Un c�digo de barras Code 39 puede codificar una cadena
     * con los siguientes caracteres: d�gitos (0 a 9), letras may�sculas (A a Z) y 8 caracteres
     * adicionales (-.Espacio $ / +% *).
     * @param int    $xpos     abscisa del c�digo de barras
     * @param int    $ypos     ordenada del c�digo de barras
     * @param string $code     valor del c�digo de barras
     * @param float  $baseline corresponde al ancho de una barra ancha (por defecto es 0.5)
     * @param int    $height   altura de la barra (por defecto es 5)
     * @throws Exception
     */
    function CodigoBarras(int $xpos, int $ypos, string $code, float $baseline = 0.5, int $height = 5)
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

        $this->SetFont('Arial', '', 9);
        $this->Text($xpos, $ypos + $height + 3, $code);
        $this->SetXY($xpos, $ypos + $height + 1);
//        $this->Cell(0, 2, $code, 0, 0, 'C');
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

    /**
     * Genera texto en determinada orientaci�n.
     * @param        $x         Posici�n horizontal del texto
     * @param        $y         Posici�n vertical del texto
     * @param        $txt       Texto a imprimir
     * @param string $direction Direcci�n del texto: <ul><li>L = Left</li><li>U = Up</li><li>R = Right</li><li>D =
     *                          Down</li></ul>
     */
    function TextWithDirection(int $x, int $y, string $txt, string $direction = "R")
    {
        if ($direction == 'R') {
            $s = sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET', 1, 0, 0, 1, $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
        } else {
            if ($direction == 'L') {
                $s = sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET', -1, 0, 0, -1, $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
            } else {
                if ($direction == 'U') {
                    $s = sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET', 0, 1, -1, 0, $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
                } else {
                    if ($direction == 'D') {
                        $s = sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET', 0, -1, 1, 0, $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
                    } else {
                        $s = sprintf('BT %.2F %.2F Td (%s) Tj ET', $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
                    }
                }
            }
        }
        if ($this->ColorFlag) {
            $s = 'q ' . $this->TextColor . ' ' . $s . ' Q';
        }
        $this->_out($s);
    }
}
