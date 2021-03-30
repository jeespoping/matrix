<?php


namespace Admisiones\Service;


class StickerPDFService
{
    public function crearArchivo()
    {

        $f = fopen('php://output', 'w');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');
        fclose($f);
    }
}