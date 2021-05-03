<?php


class PacienteEgreso
{
    public $documento;
    public $tipo_documento;
    public $paciente;
    public $historia;
    public $ingreso;
    public $ccoEgreso;
    public $fechaAltDefinitiva;
    public $horaAltDefinitiva;

    public $data;

    public function __construct(Array $properties=array()){
        foreach($properties as $key => $value){
            $this->{$key} = $value;
        }
    }
}