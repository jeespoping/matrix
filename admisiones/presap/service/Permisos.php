<?php 

namespace matrix\admisiones\presap\service;
use Exception;

class Permisos {

    private $codigoGrupo;
    private $codigoOpcion;
    private $usuario;
    private $conex;

    public function __construct($usuario, $conex) {
        $this->usuario = $usuario;
        $this->conex = $conex;
    }

    public function getCodigoGrupo(){
        return $this->codigoGrupo;
    }

    public function setCodigoGrupo($codigoGrupo){
        $this->codigoGrupo = $codigoGrupo;
    }

    public function getCodigoOpcion(){
        return $this->codigoOpcion;    
    }

    public function setCodigoOpcion($codigoOpcion){
        $this->codigoOpcion = $codigoOpcion;
    }

    public function validarGrupo($codigoGrupo)
    {
        $q = "SELECT 
            Descripcion 
            FROM matrix.root_000020
            WHERE codigo ='{$codigoGrupo}'
            AND Usuarios LIKE '%{$this->usuario}%';";

        $res = mysqli_query($this->conex, $q);
        return mysqli_num_rows($res) > 0;
    }

    public function validarOpcion($codigoGrupo,$codigoOpcion)
    {
        $q = "SELECT Descripcion 
            FROM matrix.root_000021
            WHERE Codopt ='{$codigoOpcion}'
            AND Codgru = '{$codigoGrupo}'
            AND Usuarios LIKE '%{$this->usuario}%';";
        try{
            $res = mysqli_query($this->conex, $q);
            if($res){
                return mysqli_num_rows($res) > 0;
            }else {
                throw mysqli_error($res);
            }
        } catch (Exception $e){
            throw new Exception($e);
        }
    }
}