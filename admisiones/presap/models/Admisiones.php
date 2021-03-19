<?php
    include_once("conex.php");
    include_once("root/comun.php");
    class Admisiones {

        private $wemp_pmla;
        private $wbasedato;
        private $conex;

        public function __construct($conex, $wemp_pmla, $wbasedato){
            $this->conex = $conex;
            $this->wemp_pmla = $wemp_pmla;
            $this->wbasedato = $wbasedato;
        }

        public function todas(){
            $query = 'SELECT * FROM matrix.cliame_000101 ORDER BY Fecha_data DESC LIMIT 0, 500';
            $res = mysql_query($query,$this->conex);
            return mysql_fetch_array($res);
        }
    }
?>