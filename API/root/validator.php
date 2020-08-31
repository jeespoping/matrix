<?php

class  Validator{

    public $valido    = null;
    public $faltante  = null;

    public function __construct(){

         $this->valido    = true;
         $this->$faltante = "";

    }

    public function validarParametros( $arrayParametrosRequeridos, $contenedor, $camposOpcionales = array() ){

        foreach ( $arrayParametrosRequeridos as $key => $campoRequerido ) {
            if( ( !isset( $contenedor[$campoRequerido] ) or trim( $contenedor[$campoRequerido] ) == "") and !in_array( $campoRequerido, $camposOpcionales ) ){
               $this->valido   = false;
               $this->faltante = $campoRequerido;
               break;
            }
        }
        return;
    }
}


?>