<?php

namespace matrix\admisiones\presap\models;

include_once("conex.php");
include_once("root/comun.php");

use Exception;
use DateTime;

/**
 * Modelo para obtener información de las admisiones
 */
class Admisiones
{
    /**
     * codigo de la empresa
     */
    private $wemp_pmla;
    /**
     * alias de la aplicación en matrix.
     */
    private $aliasPorApp;
    /**
     * conexión a la base de datos matrix.
     */
    private $conex;
    /**
     * 
     */
    public function __construct($conex, $wemp_pmla, $aliasPorApp)
    {
        $this->conex = $conex;
        $this->wemp_pmla = $wemp_pmla;
        $this->aliasPorApp = $aliasPorApp;
    }
    /**
     * Obtiene 500 regisros de los ingresos por empresa
     * @param void
     * @return array $res
     */
    public function todas()
    {
        $query = 'SELECT * FROM matrix.' . $this->aliasPorApp . '_000101 ORDER BY Fecha_data DESC LIMIT 0, 500';
        $res = mysql_query($query, $this->conex);
        return mysql_fetch_array($res);
    }
    /**
     * Obtiene los ingresos filtrandolos por fecha de ingreso.
     * @param string $fechaInicial
     * @return array $res
     */
    public function todasPorFechaIngreso($fechaInicial)
    {
        $q = 'SELECT
            ing.Ingfei as fecha_ingreso,
            pac.Pacno1 AS nombre, pac.Pacno2 AS segundo_nombre, pac.Pacap1 AS primer_apellido,
            pac.Pacap2 AS segund_apellido, ing.Inghis AS historia,
            est.Seldes AS estado_civil, pac.Pacdoc AS documento, pac.Pacfna AS fecha_nacimiento,
            "" AS edad, pac.Pacsex as sexo, concat(pac.Pacdir," - ",pac.Pacddr) AS direccion,
            pac.Pactel AS telefono_domicilio, mun.nombre AS municipio, pac.Pacnoa AS nombre_acompanante,
            pac.Pactea AS telefono_acompanante, pac.Pacnru AS nombre_responsable, pac.Pactru AS telefono_responsable,
            pac.Pacpru AS parentesco_responsable, aseg.Empnom AS aseguradora, afi.Seldes AS tipo_afiliacion
            FROM matrix.' . $this->aliasPorApp . '_000101 AS ing
        INNER JOIN matrix.' . $this->aliasPorApp . '_000100 AS pac ON pac.Pachis = ing.inghis
        INNER JOIN matrix.' . $this->aliasPorApp . '_000105 AS est ON pac.Pacest = est.Selcod AND est.seltip = 25
        INNER JOIN matrix.' . $this->aliasPorApp . '_000105 AS afi ON pac.Pactaf = afi.Selcod AND afi.seltip = 16
        INNER JOIN matrix.root_000006 AS mun ON pac.Paciu = mun.codigo
        INNER JOIN matrix.' . $this->aliasPorApp . '_000024 AS aseg ON ing.Ingcem = aseg.Empcod
        WHERE ing.Ingfei = ' . $fechaInicial . ';';
        try {
            //mysqli_set_charset($this->conex, "utf8");
            $res = mysqli_query($this->conex, $q);
            mysqli_data_seek($res, 0);
            $collecionReporte = [];
            while ($fila = mysqli_fetch_assoc($res)) {
                if ($fila['fecha_nacimiento'] != '') {
                    $fila['edad'] = $this->calcularEdad($fila['fecha_nacimiento']);
                }
                array_push($collecionReporte, $fila);
            }
            return $collecionReporte;
        } catch (Exception $err) {
            throw new Exception($err);
        }
    }
    /**
     * 
     */
    private function calcularEdad($edad)
    {
        $ahora = new DateTime();
        $objetoEdad = new DateTime($edad);
        $edad = $ahora->diff($objetoEdad)->y;
        $unidad = " anos";
        if ($edad < 1) {
            $edad = $ahora->diff($objetoEdad)->m;
            $unidad = " meses";
        }
        return $edad . $unidad;
    }
}
