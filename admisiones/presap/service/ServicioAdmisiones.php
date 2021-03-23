<?php 
    namespace matrix\admisiones\presap\service;

    class ServicioAdmisiones {

        private $modelo;
        private $datosDemograficos;

        public function __construct($modeloAdmisiones)
        {
            $this->modelo = $modeloAdmisiones;
        }

        public function calcularEdad(){
            $info = $this->modelo->todas();
            return $info;
        }
    }
?>