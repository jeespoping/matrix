<?php
    namespace matrix\admisiones\presap\models;
    include_once("conex.php");
    include_once("root/comun.php");
    class Admisiones {

        private $wemp_pmla;
        private $aliasPorApp;
        private $conex;

        public function __construct($conex, $wemp_pmla, $aliasPorApp){
            $this->conex = $conex;
            $this->wemp_pmla = $wemp_pmla;
            $this->aliasPorApp = $aliasPorApp;
        }

        public function todos(){
            $query = 'SELECT * FROM matrix.'.$this->aliasPorApp.'_000101 ORDER BY Fecha_data DESC LIMIT 0, 500';
            $res = mysql_query($query,$this->conex);
            return mysql_fetch_array($res);
        }

        public function todosPorFechaIngreso($fechaInicial, $fechaFinal) {
            $query = 'SELECT * FROM matrix.'.$this->aliasPorApp.'';
        }
    }
?>