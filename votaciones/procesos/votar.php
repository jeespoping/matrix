<?php
include_once("conex.php");

@session_start();

if(!$_SESSION['user'])
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina de inicio de matrix<FONT COLOR='RED'>" .
        " </FONT></H1>\n</CENTER>");

include_once("root/comun.php");


$conex = obtenerConexionBD("matrix");

$wactualiz="Febrero 28 de 2018";

if( !isset($wemp_pmla) ){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

$wentidad = $institucion->nombre;
$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wbasedato = aplicacion($conex, $wemp_pmla, "votaciones" );
$wlogoempresa = strtolower( $institucion->baseDeDatos );
$wusuario = substr($user,2,7);
$wfecha_hoy = date("Y-m-d");

$key = substr($user, 2, strlen($user)); //se eliminan los dos primeros digitos

if(is_numeric($key))
{
	if(strlen($key) == 7 AND "'".substr($key, 2)."'" !== "'".$wemp_pmla."'")
	{

		$wemp_pmla1=(substr($key, 0,2)); //el wemp_pmla son los dos primeros digitos
	    $key2 = substr( $key, -5 );
	}
	else
	{
		$wemp_pmla1=$wemp_pmla;
		$key2 = substr( $key, -5 );
	}

}
else
{
	$key2=$key;
	$wemp_pmla1=$wemp_pmla;
}


mysql_select_db("matrix") or die("No se selecciono la base de datos");


if(isset($dato_tema) and $dato_tema != ''){
	
	$filtro_tipo = " AND Votcod = '$dato_tipo' ";
}

//Configuracion de la votacion
$q_conf_vot =    " SELECT * "
				."   FROM ".$wbasedato."_000001 "
			    ."	WHERE Votapl = '".$aplicacion."' $filtro_tipo "				
			    ."	  AND Votact = 'on'";
$res_conf_vot = mysql_query($q_conf_vot,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_conf_vot." - ".mysql_error());

$array_conf_vot = array();

	while($row_conf_vot = mysql_fetch_assoc($res_conf_vot))
    {
        //Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_conf_vot['Votcod'], $array_conf_vot))
        {
            $array_conf_vot[$row_conf_vot['Votcod']] = $row_conf_vot;
        }

    }


foreach($array_conf_vot as $key => $value){
	
	$cod_vot = $key;
	
}


$wconsecutivo_votacion = $array_conf_vot[$cod_vot]['Votcod'];  //Codigo de la votacion activa

$q_nofoto =    " SELECT * "
			  ."   FROM root_000116 "
			  ."  WHERE Fotest = 'on'";
$res_nofoto  = mysql_query($q_nofoto ,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_nofoto." - ".mysql_error());
$array_foto = array();

	while($row_foto = mysql_fetch_assoc($res_nofoto))
    {
        //Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_foto['Fotced'], $array_foto))
        {			
            $array_foto[$row_foto['Fotced']] = $row_foto;
        }

    }

//Temas asociados a la votacion
$q_temas =   " SELECT * "
			."   FROM ".$wbasedato."_000002 "
			."	WHERE Temapl = '".$aplicacion."' $filtro_tipo "
			."	  AND Temcvo = '".$wconsecutivo_votacion."'"
			."	  AND Temest = 'on'";
$res_temas = mysql_query($q_temas,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_temas." - ".mysql_error());

$array_temas = array();

	while($row_temas = mysql_fetch_assoc($res_temas))
    {
        //Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_temas['id'], $array_temas))
        {			
            $array_temas[$row_temas['id']] = $row_temas;
        }

    }

$wfecha_max_ins = $array_conf_vot[$cod_vot]['Votfmi']; 
$wfecha_inicio = $array_conf_vot[$cod_vot]['Votini']; 
$whora_inicio = $array_conf_vot[$cod_vot]['Vothiv']; 

$fechaHoraActual = strtotime(date("Y-m-d H:i:s"));
$fechaHoraInicio = strtotime($wfecha_inicio." ".$whora_inicio);

// if($wfecha_inicio > $wfecha_hoy){
// if($fechaHoraActual < $fechaHoraInicio){
if($fechaHoraInicio > $fechaHoraActual){
	
	$westado_votaciones = 'off';
	
	
}else{
	
	$westado_votaciones = $array_conf_vot[$cod_vot]['Votact'];  //Verifica si la votacion estan activa.
}


$wcontrol_voto_blanco_est = $array_conf_vot[$cod_vot]['Votbla'];  //Voto en blanco.
$wcontrol_suplente_est = $array_conf_vot[$cod_vot]['Votsup']; //Suplente.
$wcon_inscripcion = $array_conf_vot[$cod_vot]['Votein'];  //Habilitado para inscripcion.
$wcopaso = $array_conf_vot[$cod_vot]['Votcop'];  //Habilitado para copaso, con esto buscara en la tabla los inscritos para este tipo de votacion.
$wtalhumas = traer_talumas($conex, "informacion_empleados", $aplicacion, $wbasedato); //Trae todas las empresas que tengan tablas de talento humano.
$numcolumnas = $array_conf_vot[$cod_vot]['Votcol'];	//Cantidad de columnas en las que se dividira el tarjeton de votacion.
$video_instructivo = $array_conf_vot[$cod_vot]['Votvin'];	//Nombre del video instructivo.
$video_instructivo_obligatorio = $array_conf_vot[$cod_vot]['Votvob'];	//Video instructivo obligatorio.
$descrip_votacion = $array_conf_vot[$cod_vot]['Votdes'];	//Cantidad de columnas en las que se dividira el tarjeton de votacion.

$wempresas_select = getOptionsEmpresas($wemp_pmla, $conex, $wbasedato, '', '', 'off', $aplicacion);
$centro_costos = getOptionsCostos($wemp_pmla, $conex, $wbasedato, '', '', 'off');
$cargos = getOptionsCargos($wemp_pmla, $conex, $wbasedato, '', '', 'off');

$cierre_votacion = cerrar_votaciones($wemp_pmla, $wbasedato, $cod_vot, $aplicacion );

function cerrar_votaciones($wemp_pmla, $wbasedato, $cod_vot, $aplicacion ){
	
	global $conex;
	global $wlogoempresa;
	global $wactualiz;
	
	$fecha_actual = time();
	
	$q =   " SELECT Votfin as fecha_final, Vothcv as hora_final 
			   FROM ".$wbasedato."_000001
			  WHERE Votcod = '".$cod_vot."'
				AND Votapl = '".$aplicacion."'";
				
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);
	$fecha_final = $row['fecha_final'];
	$hora_final = $row['hora_final'];
	
	$fechaHoraActual = strtotime(date("Y-m-d H:i:s"));
	$fechaHoraCierre = strtotime($fecha_final." ".$hora_final);
	
	// if($fecha_actual > $fecha_final){
	if($fechaHoraActual >= $fechaHoraCierre){
		
	$q = " UPDATE ".$wbasedato."_000001 "
		 ."    SET Votact = 'off' "	
		 ."  WHERE Votcod = '".$cod_vot."'"
		 ."    AND Votapl = '".$aplicacion."'";
	$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());  
	$update = mysql_affected_rows();	
		
	if($update > 0){
		encabezado("Votaciones",$wactualiz, $wlogoempresa);
		
		echo "<br>\n<br>\n".
        " <H1 align=center>No hay votaciones abiertas en este momento.</H1>";
		exit();
	}
	
	}
	
}

function getOptionsEmpresas($wemp_pmla, $conex, $wbasedato, $id_padre, $especifico = '', $add_todos = 'on', $aplicacion){
	
	$q =  " SELECT Tabtab "
		  ."   FROM ".$wbasedato."_000008"
		  ."  WHERE Tabemp = '".$aplicacion."'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);
	$empresas_votacion_a = $row['Tabtab'];
	$empresas_votacion_b = explode(",",$empresas_votacion_a);
	$empresas_votacion = implode("','", $empresas_votacion_b);	
	
	$q = "  SELECT  Empcod, Empdes
              FROM    root_000050
             WHERE   Empcod in ('".$empresas_votacion."')";
    $res = mysql_query($q,$conex);
	$array_emp = array();
	while($row = mysql_fetch_assoc($res)){
		
		$array_emp[$row['Empcod']] = $row;
		
	}
	$option_emp = "<option value=''>Seleccione..</option>";
	foreach($array_emp as $key => $value){
		
		$option_emp .= "<option value='".$key."'>".$value['Empdes']."</option>"; 
		
	}
	
	return $option_emp;
}

/**
 * Este es un buscador para cargos
 *
 * @param unknown $wemp
 * @param unknown $conex
 * @param unknown $wbasedato
 * @param string $id_padre     : Es el id o parte del nombre de lo que se está buscando
 * @param string $especifico   : Si este parámetro está chequeado se retornará un checkbox chequeado dentro de un div.
 * @return html: retorna opctions para un select o un ckeckbox dentro de un div.
 */
function getOptionsCargos($wemp_pmla, $conex, $wbasedato, $id_padre, $especifico = '', $add_todos = 'on')
{
    $optionsCar = '';
    if(trim(strtolower($id_padre)) != '*')
    {
        $buscaNombre = str_replace('*','%',str_replace(' ','%',(trim($id_padre))));
        $buscaNombre = strtoupper(strtolower($buscaNombre));

        $q = "  SELECT  Carcod AS codigo, Cardes AS nombre
                FROM    root_000079
                WHERE   Cardes LIKE '%".$buscaNombre."%'
                        OR Carcod LIKE '".$buscaNombre."'
                ORDER BY Cardes";
        $res = mysql_query($q,$conex);

        if($especifico == '')
        {
            $optionsCar = '<option value="" >Seleccione..</option>';
            if($add_todos == 'on')
            {
                $optionsCar .= '<option value="*" >[ - TODOS - ]</option>';
            }
        }

        if($especifico != '')
        {
            if(mysql_num_rows($res) > 0)
            {
                $row = mysql_fetch_array($res);
                $optionsCar = $row['codigo'].' - '.utf8_encode(strtoupper(strtolower($row['nombre'])));
                $optionsCar = "
                        <div id='div_ckc_ccg_".$row['codigo']."' class='fila2' style='border-top: 2px solid #ffffff;'>
                            <input type='checkbox' id='wcccargo_pfls_".$row['codigo']."' name='wcccargo_pfls_chk[".$row['codigo']."]' value='".$row['codigo']."' checked='checked' onClick='desmarcarRemover(\"wcccargo_pfls_".$row['codigo']."\",\"div_ckc_ccg_".$row['codigo']."\",\"div_load_chk_cargo\");' >&nbsp;".$optionsCar."
                        </div>";
            }
            else
            {
                $optionsCar = '';
            }
        }
        else
        {
            while($row = mysql_fetch_array($res))
            {
                $optionsCar .= '<option value="'.$row['codigo'].'" >'.$row['codigo'].' - '.utf8_encode(strtoupper(strtolower($row['nombre']))).'</option>';
            }
        }
    }
    else
    {
        $optionsCar = "
                <div id='div_ckc_ccg_todos' class='fila2' style='border-top: 2px solid #ffffff;'>
                    <input type='checkbox' id='wcccargo_pfls_todos' name='wcccargo_pfls_chk[todos]' value='*' checked='checked' onClick='desmarcarRemover(\"wcccargo_pfls_todos\",\"div_ckc_ccg_todos\",\"div_load_chk_cargo\");' >&nbsp;[ - TODOS - ]
                </div>";
    }
    return $optionsCar;
}

/**
 * Este es un buscador para centros de costos
 *
 * @param unknown $wemp
 * @param unknown $conex
 * @param unknown $wbasedato
 * @param string $id_padre     : Es el id o parte del nombre de lo que se está buscando
 * @param string $especifico   : Si este parámetro está chequeado se retornará un checkbox chequeado dentro de un div.
 * @return html: retorna opctions para un select o un ckeckbox dentro de un div.
 */
function getOptionsCostos($wemp_pmla, $conex, $wbasedato_talhuma, $id_padre, $especifico = '', $add_todos = 'on')
{
    $options = "";
    if(trim(strtolower($id_padre)) != '*')
    {
        $q = "  SELECT  Empdes,Emptcc
                FROM    root_000050
                WHERE   Empcod = '".$wemp_pmla."'";
        $res = mysql_query($q,$conex);

        if($especifico == '')
        {
            $options = '<option value="" >Seleccione..</option>';
            if($add_todos == 'on')
            {
                $options .= '<option value="*" >[ - TODOS - ]</option>';
            }
        }

        if($row = mysql_fetch_array($res))
        {
            $buscaNombre = str_replace('*','%',str_replace(' ','%',(trim($id_padre))));
            $buscaNombre = strtoupper(strtolower($buscaNombre));

            $clisur_000003 = array('inner'=>'','filtro'=>'');
            $farstore_000003 = array('inner'=>'','filtro'=>'');
            $costosyp_000005 = array('inner'=>'','filtro'=>'');
            $uvglobal_000003 = array('inner'=>'','filtro'=>'');

            if($wbasedato != '')
            {
                $clisur_000003['inner'] = "INNER JOIN
                                           ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)";
                $clisur_000003['filtro'] = "OR tb1.Ccocod LIKE '".trim($buscaNombre)."'";

                $farstore_000003['inner'] = "INNER JOIN
                                           ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)";
                $farstore_000003['filtro'] = "OR tb1.Ccocod LIKE '".trim($buscaNombre)."'";

                $costosyp_000005['inner'] = "INNER JOIN
                                            ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)";
                $costosyp_000005['filtro'] = "OR tb1.Ccocod LIKE '".trim($buscaNombre)."'";

                $uvglobal_000003['inner'] = "INNER JOIN
                                            ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)";
                $uvglobal_000003['filtro'] = "OR tb1.Ccocod LIKE '".trim($buscaNombre)."'";
            }

            $tabla_CCO = $row['Emptcc'];
            switch ($tabla_CCO)
            {
                case "clisur_000003":
                        $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                    FROM    clisur_000003 AS tb1
                                            ".$clisur_000003['inner']."
                                    WHERE   tb1.Ccodes LIKE '%".trim($buscaNombre)."%'
                                            ".$clisur_000003['filtro']."
                                    GROUP BY    tb1.Ccocod
                                    ORDER BY    tb1.Ccodes";
                        break;
                case "farstore_000003":
                        $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                    FROM    farstore_000003 AS tb1
                                            ".$farstore_000003['inner']."
                                    WHERE   tb1.Ccodes LIKE '%".trim($buscaNombre)."%'
                                            ".$farstore_000003['filtro']."
                                    GROUP BY    tb1.Ccocod
                                    ORDER BY    tb1.Ccodes";
                        break;
                case "costosyp_000005":
                        $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                                    FROM    costosyp_000005 AS tb1
                                            ".$costosyp_000005['inner']."
                                    WHERE   tb1.Cconom LIKE '%".trim($buscaNombre)."%'
                                            ".$costosyp_000005['filtro']."
                                    GROUP BY    tb1.Ccocod
                                    ORDER BY    tb1.Cconom";
                        break;
                case "uvglobal_000003":
                        $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                    FROM    uvglobal_000003 AS tb1
                                            ".$uvglobal_000003['inner']."
                                    WHERE   tb1.Ccodes LIKE '%".trim($buscaNombre)."%'
                                            ".$uvglobal_000003['filtro']."
                                    GROUP BY    tb1.Ccocod
                                    ORDER BY    tb1.Ccodes";
                        break;
                default:
                        $query="    SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                                    FROM    costosyp_000005 AS tb1
                                            ".$costosyp_000005['inner']."
                                    WHERE   tb1.Cconom LIKE '%".trim($buscaNombre)."%'
                                            ".$costosyp_000005['filtro']."
                                    GROUP BY    tb1.Ccocod
                                    ORDER BY    tb1.Cconom";
            }

            $res = mysql_query($query,$conex);

            if($especifico != '')
            {
                if(mysql_num_rows($res) > 0)
                {
                    $row = mysql_fetch_array($res);
                    $options = $row['codigo'].' - '.utf8_encode(strtoupper(strtolower($row['nombre'])));
                    $options = "
                            <div id='div_ckc_cco_".$row['codigo']."' class='fila2' style='border-top: 2px solid #ffffff;'>
                                <input type='checkbox' id='wccostos_pfls_".$row['codigo']."' name='wccostos_pfls_chk[".$row['codigo']."]' value='".$row['codigo']."' checked='checked' onClick='desmarcarRemover(\"wccostos_pfls_".$row['codigo']."\",\"div_ckc_cco_".$row['codigo']."\",\"div_adds_costos\");' >&nbsp;".$options."
                            </div>";
                }
                else
                {
                    $options = '';
                }
            }
            else
            {
                while($row = mysql_fetch_array($res))
                {
                    $options .= '<option value="'.$row['codigo'].'" >'.$row['codigo'].' - '.utf8_encode(strtoupper(strtolower($row['nombre']))).'</option>';
                }
            }
        }
    }
    else
    {
        $options = "
                <div id='div_ckc_cco_todos' class='fila2' style='border-top: 2px solid #ffffff;'>
                    <input type='checkbox' id='wccostos_pfls_todos' name='wccostos_pfls_chk[todos]' value='*' checked='checked' onClick='desmarcarRemover(\"wccostos_pfls_todos\",\"div_ckc_cco_todos\",\"div_adds_costos\");' >&nbsp;[ - TODOS - ]
                </div>";
    }
    return $options;
}

function consultar_registros($wemp_pmla, $wbasedato, $wusuario, $registros_inicial, $registros_final, $aplicacion, $texto_buscar, $wccostos, $wcargo, $wempresa_asociado, $wempresas_filtro, $instructivo_visto){
	
	global $wcon_inscripcion;
	global $wconsecutivo_votacion;
	global $westado_votaciones;
	global $wcontrol_voto_blanco_est;
	global $wcontrol_suplente_est;
	global $wcon_inscripcion;
	global $numcolumnas;
	global $array_temas;
	global $key;
	global $key2;
	global $wemp_pmla1;
	global $conex;
	global $array_foto;
	
	$qaso =  " SELECT Asoced, Asoemp, Asoemr 
		         FROM ".$wbasedato."_000006
		 	    WHERE Asoest = 'on'";
	$res_aso = mysql_query($qaso,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	$array_asociados = array();

	while($row_asociados = mysql_fetch_assoc($res_aso))
    {
        //Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_asociados['Asoced'], $array_asociados))
        {
            $array_asociados[$row_asociados['Asoced']] = $row_asociados;
        }

    }
	
	$array_centros_costo = array();
	//Busco las tablas de centros de costos.
	$wtabla_cco = traer_tablas_cco($conex, "costoscer");

	//Consultar el los centros de costos de las empresas.
	$array_empresas_cco = array();
	
	$and = '';
	
	foreach($wtabla_cco as $key_tablas_cco => $value_tablas_cco){

		//Consulto en las tablas los datos de los centros de costos.
		$q_cco =   " SELECT * "
				  ."   FROM ".$value_tablas_cco['Detval']."";
		$res_ccos = mysql_query($q_cco,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_cco." - ".mysql_error());

		//Creo un arreglo inicial de empresas.
		if(!array_key_exists($value_tablas_cco['Detemp'], $array_empresas_cco))
		{
			$array_empresas_cco[$value_tablas_cco['Detemp']] = array();
		}

		//A cada empresa le relaciono sus centros de costos.
		while($row_ccos = mysql_fetch_assoc($res_ccos))
		{
			//Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
			if(!array_key_exists($row_ccos['Ccocod'], $array_empresas_cco[$value_tablas_cco['Detemp']]))
			{

				$array_empresas_cco[$value_tablas_cco['Detemp']][$row_ccos['Ccocod']] = $row_ccos;
			}

		}

	}
	
	$wtalhumas = traer_talumas($conex, "informacion_empleados", $aplicacion, $wbasedato); //Trae todas las empresas que tengan tablas de talento humano.
	$datamensaje = array('mensaje'=>'', 'error'=>0, 'html'=>'', 'total_registros'=>'', 'num_resultados'=> 0);
	
	
	if(trim($texto_buscar) != ''){
			
			$nombre_apellido = explode(" ",$texto_buscar);
			
			$filtro_buscar .= " (Ideno1 LIKE '%".$texto_buscar."%'";
			$filtro_buscar .= " OR Ideno2 LIKE '%".$texto_buscar."%' ";
			$filtro_buscar .= " OR Ideap1 LIKE '%".$texto_buscar."%' ";
			$filtro_buscar .= " OR Ideap2 LIKE '%".$texto_buscar."%' ";
			$filtro_buscar .= " OR (Ideno1 LIKE '%".$nombre_apellido[0]."%' AND Ideno2 LIKE '%".$nombre_apellido[1]."%') ";
			$filtro_buscar .= " OR (CONCAT ( Ideno1,' ', Ideap1 )) LIKE '%".$texto_buscar."%' ";
			$filtro_buscar .= " OR (CONCAT ( Ideno2,' ', Ideap1 )) LIKE '%".$texto_buscar."%' ) AND ";
			
		
	}
	
	if(trim($wccostos) != ''){
		
		$filtro_buscar .= "$and Idecco = '$wccostos' AND ";
		
	}
	
	if(trim($wcargo) != ''){
		
		$filtro_buscar .= "$and Ideccg = '$wcargo' AND ";
		
	}
	
	if(trim($wempresas_filtro) != ''){
		
		unset($wtalhumas);
		$wempresas_votacion = traer_talumas($conex, "informacion_empleados", $aplicacion, $wbasedato); //Trae todas las empresas que tengan tablas de talento humano.
		$wtalhumas[$wempresas_filtro] = array('Detval'=>$wempresas_votacion[$wempresas_filtro]['Detval']);
		
	}
	

	if($wcon_inscripcion == 'on'){
	
	foreach($wtalhumas as $key_b => $value){

		//Consulta los suplente principales y los agrega al arreglo inscritos
		$query_inscritos_ppales[] = "  SELECT Insced as cedula, Idegen, Ideced, Ideno1, Ideno2, Ideap1, Ideap2, Idecco, Ideccg, Ideuse, Inscsu, Insces, Insemp, Inscco, Insesu, Insccs, ".$key_b." as cod_emp
									   FROM ".$wbasedato."_000003, ".$value['Detval']."
									  WHERE $filtro_buscar Insced = Ideced
										AND Insest = 'on'
										AND Instip = '01'
										AND Insapr = 'on'
										AND Insemp = '".$wempresa_asociado."'								
										AND Insvot = '".$wconsecutivo_votacion."'
										AND Insapl = '".$aplicacion."'
										AND Ideest = 'on'";

		}
	
	}else{
				
		foreach($wtalhumas as $key_b => $value){
		$query_inscritos_ppales[] = "  SELECT Ideced as cedula, Idegen, Ideced, Ideno1, Ideno2, Ideap1, Ideap2, Idecco, Ideccg, Ideuse, SUBSTRING_INDEX( Ideuse, '-', -1 ) as Insemp, ".$key_b." as cod_emp
									     FROM ".$value['Detval']."
									    WHERE $filtro_buscar Ideest = 'on'										  
										  AND Ideced != '' ";
		}
	
	}
	
	//Uno el arreglo anterior con la instruccion UNION para asi tener toda la informacion.
	$query_inscritos_ppales = implode(" UNION ", $query_inscritos_ppales);		
	$query_inscritos_ppales_aux = "SELECT * FROM ( $query_inscritos_ppales ) AS t GROUP BY cedula, Ideuse ORDER BY Ideno1, Ideno2 LIMIT $registros_inicial, $registros_final;";
	$res_inscritos_ppales_aux = mysql_query($query_inscritos_ppales);
	$numero_inscritos = mysql_num_rows($res_inscritos_ppales_aux);
	
	$datamensaje['num_resultados'] = $numero_inscritos;
	
	$query_inscritos_ppales = "SELECT * FROM ( $query_inscritos_ppales ) AS t GROUP BY cedula, Ideuse ORDER BY Ideno1, Ideno2 LIMIT $registros_inicial, $registros_final;";
	$res_inscritos_ppales = mysql_query($query_inscritos_ppales);
	$array_inscritos_ppales = array();

	while($row_inscritos_ppales = mysql_fetch_assoc($res_inscritos_ppales))
		{

			if(!array_key_exists($row_inscritos_ppales['cedula'], $array_inscritos_ppales) and (int)$row_inscritos_ppales['cedula'] > 0)
			{		
				$array_inscritos_ppales[$row_inscritos_ppales['cedula']] = $row_inscritos_ppales;				
			}
		}

	$array_suplentes = array();
	//Verifico si se debe mostrar el suplente.
	if($wcontrol_suplente_est == 'on'){
	//Recorro todos los talhumas y creo la union de todos ellos, esto para buscar los suplentes.
	foreach($wtalhumas as $key_c => $value){

	$query_suplentes[] = " SELECT Insced as cedula, Insapr, Idegen, Ideced, Ideno1, Ideno2, Ideap1, Ideap2, Idecco, Ideccg, Ideuse, Inscsu, Insces, Insemp, Inscco, Insesu, Insccs
						   FROM ".$wbasedato."_000003, ".$value['Detval']."
						  WHERE Insces = Ideced
							AND Insest = 'on'
							AND Insapr = 'on'
							AND Insapl = '".$aplicacion."'
							AND Insemp = '".$wempresa_asociado."'							
							AND Insvot = '".$wconsecutivo_votacion."'
							AND Ideest = 'on'";
		}

	//Uno el arreglo anterior con la instruccion UNION para asi tener toda la informacion.
	$query_suplentes = implode(" UNION ", $query_suplentes);
	$res_suplentes = mysql_query($query_suplentes);
	

	while($row_suplentes = mysql_fetch_assoc($res_suplentes))
		{
			//Se verifica si el producto ya se encuentra en el arreglo, si no esta lo agrega.
			if(!array_key_exists($row_suplentes['Insces'], $array_suplentes))
			{
				$array_suplentes[$row_suplentes['Insces']] = $row_suplentes;
			}

		}
	}
	
	$datamensaje['html'] .= "<table width='40%' id='tabla_principales'>"; //Tabla de principales
	
	//Buscar en la tabla de usuario que cco le pertenece al usuario.
	$q_user = "SELECT Descripcion, Empresa
				 FROM usuarios
				WHERE Codigo = '".$wusuario."'			
				  AND Activo = 'A'";
	$res_user = mysql_query($q_user, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_user . " - " . mysql_error());
	$row_user = mysql_fetch_array($res_user);

	$empresa_tabla_usuarios = $row_user['Empresa'];
	
		foreach($wtalhumas as $key_a => $value){
		
		//Consulto la informacion del usuario por el codigo en la tabla talhuma_000013
		$q[] =    " SELECT Idegen, Ideced, Ideno1, Ideno2, Ideap1, Ideap2, Idecco, Ideccg, Ideuse"
				 ."	  FROM ".$value['Detval'].""
				 ."	 WHERE (Ideuse = '".$key2."-".$wemp_pmla1."'"
				 ."     OR Ideuse = '".$key."' OR Ideuse = '".$key2."-".$empresa_tabla_usuarios."')"
				 ."    AND Ideest = 'on'"
				 ."    AND Ideced != '' ";

			}

		//Uno el arreglo anterior con la instruccion UNION para asi tener toda la informacion.
		$q = implode(" UNION ", $q);
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$row = mysql_fetch_assoc($res);
		$wcedula = $row['Ideced'];
		//Deshabilita los radio button para las votaciones si el tercer parametro la root_000051 (VotacionAspirantesFe) esta en off.
	   $westado_actual_votacion = verificar_votacion($wusuario, $wcedula, $wconsecutivo_votacion, $aplicacion);
	   
	   $wempresas = empresas(); //Array de empresas

	   if (count($array_inscritos_ppales) > 0) {
		 
		 $datamensaje['html'] .= "<input type='hidden' id='total_registros' value='".$numero_inscritos."' >";
		 $datamensaje['html'] .= "<input type='hidden' id='registros_pagina' value='4' >";
		 $i = 1;
		 $j = 1;
		 $wcentro_costos_ppal= '';
		 $wcentro_costos_suple = '';
		 $texto_principal = '';
		 
		 
		 foreach($array_inscritos_ppales as $key_ppales => $valores_ppal){

			   $resto = ($i % $numcolumnas);
			   if($resto == 1){ /*si es el primer elemento creamos una nueva fila*/
				 $datamensaje['html'] .= "<tr class='find'>";
				}
			 $mensaje_foto = "";
			 $datamensaje['html'] .= "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
			 
			 //Si es sin inscripciones tomara la empresa de la tabla de talhuma.
			if($westado_votaciones == 'on'){
				$wempresa_asociado = $valores_ppal['Insemp'];
			}
			
			 $wgenero_ppal = ($valores_ppal['Idegen'] == '') ? "M" : $valores_ppal['Idegen'];
			 $wgenero_suplente = ($array_suplentes[$valores_ppal['Insces']]['Idegen'] == '') ? "M" : $array_suplentes[$valores_ppal['Insces']]['Idegen'];
			 //Trae la foto del usuario.
			 $foto_ppal = '<img class="imagen lightbox_ppal" id=fotografia_ppal_'.$j.' width=100px height=110px src="'.getFoto($conex,$wempresa_asociado,$wbasedato,$wusuario,$valores_ppal['cedula'],$wgenero_ppal).'"/>';

			 $foto_suplente = '<img class="imagen lightbox_suplente" id=fotografia_suplente_'.$j.' width=100px height=110px src="'.getFoto($conex,$wempresa_asociado,$wbasedato,$wusuario,$valores_ppal['Insces'],$wgenero_suplente).'"/>';

			//Centro de costos asociado al ppal.
			$wcentro_costos_ppal = $array_empresas_cco[$wempresa_asociado][$valores_ppal['Idecco']]['Cconom'];
			//Si no esta con nombre lo busca con descripcion.
			$wcentro_costos_ppal = ($wcentro_costos_ppal == '') ? $array_empresas_cco[$wempresa_asociado][$valores_ppal['Idecco']]['Ccodes'] : $array_empresas_cco[$wempresa_asociado][$valores_ppal['Idecco']]['Cconom'];
			//Si no esta con nombre ni con descripcion, imprime la empresa.
			 if($wcentro_costos_ppal == ''){

				$wcentro_costos_ppal = $wempresas[$valores_ppal['Insemp']]['Empdes'];
			}

			//Centro de costos asociado al suplente.
			$wcentro_costos_suple = $array_empresas_cco[$wempresa_asociado][$array_suplentes[$valores_ppal['Insces']]['Idecco']]['Cconom'];
			//Si no esta con nombre lo busca con descripcion.
			$wcentro_costos_suple = ($wcentro_costos_suple == '') ? $array_empresas_cco[$wempresa_asociado][$array_suplentes[$valores_ppal['Insces']]['Idecco']]['Ccodes'] : $array_empresas_cco[$wempresa_asociado][$array_suplentes[$valores_ppal['Insces']]['Idecco']]['Cconom'];
			//Si no esta con nombre ni con descripcion, imprime la empresa.
			if($wcentro_costos_suple == ''){

				$wcentro_costos_suple = $wempresas[$valores_ppal['Insesu']]['Empdes'];
			}


			//Si el asociado ppal tiene datos en empresa real en la tabla de asociados (fondos_000006 - Asoemr), entonces pondra esa en el reporte, sino, pondra la empresa que se encuentra en el campo Asoemp.
			if($array_asociados[$valores_ppal['Insced']]['Asoemr'] != ''){
				$wempresa_ppal = $wempresas[$array_asociados[$valores_ppal['Insced']]['Asoemr']]['Empdes'];
				$wcentro_costos_ppal = $wempresas[$array_asociados[$valores_ppal['Insced']]['Asoemr']]['Empdes']; //Esta variable se reasignara en caso de que el asociado tenga datos en empresa real.
			}else{
				$wempresa_ppal = $wempresas[$array_asociados[$valores_ppal['Insced']]['Asoemp']]['Empdes'];
			}
			
			if($wempresa_ppal == ''){
				
				$wempresa_ppal = $wempresas[$valores_ppal['Insemp']]['Empdes'];
				
			}
			
			if($wempresa_ppal == ''){
					
					$wempresa_ppal = $wempresas[$valores_ppal['cod_emp']]['Empdes'];
					
				}
				
			//Si el asociado suplente tiene datos en empresa real en la tabla de asociados (fondos_000006 - Asoemr), entonces pondra esa en el reporte, sino, pondra la empresa que se encuentra en el campo Asoemp.
			if($array_asociados[$valores_ppal['Insces']]['Asoemr'] != ''){
				$wempresa_suplente = $wempresas[$array_asociados[$valores_ppal['Insces']]['Asoemr']]['Empdes'];
				$wcentro_costos_suple = $wempresas[$array_asociados[$valores_ppal['Insces']]['Asoemr']]['Empdes'];	//Esta variable se reasignara en caso de que el asociado tenga datos en empresa real.
			}else{
				$wempresa_suplente = $wempresas[$array_asociados[$valores_ppal['Insces']]['Asoemp']]['Empdes'];
			}
			
			if($wempresa_suplente == ''){
				
				$wempresa_suplente = $wempresas[$valores_ppal['Insemp']]['Empdes'];
				
			}
			
			if($wempresa_suplente == ''){
					
				$wempresa_suplente = $wempresas[$valores_ppal['cod_emp']]['Empdes'];
					
				}
			
			
			if( $wcontrol_suplente_est == 'on'){
				
				$texto_principal = 'Principal';
				
			}
			
			if(isset($array_foto[$valores_ppal['cedula']])){
					
					$mensaje_foto = "<div style='font-size:12px; color:#000;'>&oslash;</div>";
					
				}
			
			 $datamensaje['html'] .= "<td class='bordeAbajo'><br>
						<div id='div_tabla' class='border1'>
						<div style='font-size:12px; color:#000000;'><b>$texto_principal</b></div>
						<div>&nbsp;</div>
						<div>".$foto_ppal." <img class='fotografia_ppal_".$j."' id='imagen_grande_ppal_".$j."' src='".getFoto($conex,$wempresa_asociado,$wbasedato,'',$valores_ppal['cedula'],$valores_ppal['Idegen'])."' style='display:none' /></div>
						<div><hr></div>
						<div nomPri style='font-size:18px; color:#13189F; display:block;'>".ucfirst(utf8_encode($valores_ppal['Ideno1']))." ".ucfirst(utf8_encode($valores_ppal['Ideno2']))."</div>
						<div style='font-size:12px; color:#13189F;'>".ucfirst(utf8_encode($valores_ppal['Ideap1']))." ".ucfirst(utf8_encode($valores_ppal['Ideap2']))."</div>
						<div style='font-size:10px; color:#000000;'><b>".utf8_encode($wempresa_ppal)."</b></div>
						<div style='font-size:9px; color:#D02114;'>".utf8_encode($wcentro_costos_ppal)."</div>
						".$mensaje_foto."
					</div><br></td>";
			
			//Verifico si se debe mostrar el suplente.
			if($wcontrol_suplente_est == 'on'){
			
			$datamensaje['html'] .= "<td class='bordeAbajo'><br><div id='div_tabla' class='border1'>
						<div style='font-size:12px; color:#000000;'><b>Suplente</b></div>
						<div>&nbsp;</div>
						<div>".$foto_suplente." <img class='fotografia_suplente_".$j."' id='imagen_grande_suplente_".$j."' src='".getFoto($conex,$wempresa_asociado,$wbasedato,'',$valores_ppal['Insces'],$valores_ppal['Idegen'])."' style='display:none' /></div>
						<div><hr></div>
						<div nomSup style='font-size:18px; color:#13189F;'>".ucfirst(utf8_encode($array_suplentes[$valores_ppal['Insces']]['Ideno1']))." ".ucfirst(utf8_encode($array_suplentes[$valores_ppal['Insces']]['Ideno2']))."</div>
						<div style='font-size:12px; color:#13189F; display:inline;'>".ucfirst(utf8_encode($array_suplentes[$valores_ppal['Insces']]['Ideap1'])." ".utf8_encode($array_suplentes[$valores_ppal['Insces']]['Ideap2']))."</div>
						<div style='font-size:10px; color:#000000;'><b>".utf8_encode($wempresa_suplente)."</b></div>
						<div style='font-size:9px; color:#D02114;'>".utf8_encode($wcentro_costos_suple)."</div>
					</div><br></td>";
			}else{
			
			$datamensaje['html'] .= "<td></td>";
			
			}
			
			//Radio buton para votar.
			
			$datamensaje['html'] .= "<td colspan=2 style='padding: 3px;'>";
			
			//Si tiene mas de una opcion en los temas muestra el titulo de tema.
			if(count($array_temas) > 1){
				
				$datamensaje['html'] .= "<div align=center><b>TEMA</b></div>";	
			}
							
			foreach($array_temas as $key => $value){
				
				$wradio_checked = '';
				$wcursor_votar = '';
				$color_votado = '';
				
				
			   if($westado_votaciones == 'on'){
								
					//Verifica si el asocaido no ha votado.
					if($westado_actual_votacion[$wconsecutivo_votacion][$value['id']] == ''){

						$whabilitar_votaciones = "votado";
						$vista_mensaje = 'display:none;'; //Si no ha votado, el tr del mensaje no se mostrará.
						$wcursor_votar = "pointer";											

					}else{

							//Si ya voto, lo botones se inhabilitaran.
							$whabilitar_votaciones = "inactivo";
							
							if($westado_actual_votacion[$wconsecutivo_votacion][$value['id']]['Votcod'] == $valores_ppal['Ideuse']){
							
								$color_votado = "background-color: yellow;";
								$wradio_checked = 'checked';
							}
							
							//$wmensaje = "<div align=center style='background-color:yellow; height:20px; width: 200px;'><b>Su voto fue registrado.</b></div><br>";

							//$vista_mensaje = '';
						}

					}else{
					   $whabilitar_votaciones = "inactivo"; //Si ya voto, lo botones se inhabilitaran.
					   $vista_mensaje = 'display:none;'; //Si no ha votado pero las votaciones estan cerradas, el tr del mensaje no se mostrará.
					   }
				
				
				$datamensaje['html'] .= "	<div align='left' id='div_".$valores_ppal['Ideuse']."_".$value['id']."' style='padding: 3px;font-size: 8pt; font-family: verdana;color:#2A5DB0;border: 1px solid #72A3F3;white-space: nowrap; $color_votado'>
							<b>".strtoupper(utf8_encode($value['Temdes']))."</b>
							<input type=radio class='tema".$value['id']."  radiovotar radio_".$valores_ppal['Ideuse']."_".$value['id']." ".$whabilitar_votaciones."' name='radio_button_principal".$value['id']."' value='".$value['Temcvo']."_".$value['id']."' tooltip='si' disabled style='cursor:$wcursor_votar' id='radio_ppales_".$value['id']."' ".$wradio_checked." onclick='votar(\"$wemp_pmla\",\"$wbasedato\",\"".$wcedula."\", \"$i\",\"principal\", \"".trim($wconsecutivo_votacion)."\", \"".$valores_ppal['Ideuse']."\" , \"".$valores_ppal['Ideced']."\",\"$wempresa_asociado\", this, \"".$value['id']."\", \"".utf8_encode($value['Temdes'])."\");'>
						</div>";
					
			}
			
			$datamensaje['html'] .= "	</td>";
			//}
			 /*mostramos el valor del campo especificado*/
			if($resto == 0){
			  /*cerramos la fila*/
			  $datamensaje['html'] .= "</tr>";
			}
		   $i++;
		   $j++;

		 }

	 if($resto != 0){
	  /*Si en la &uacute;ltima fila sobran columnas, creamos celdas vac&iacute;as*/
	   for ($j = 0; $j < ($numcolumnas - $resto); $j++){
		 $datamensaje['html'] .= "<td></td>";
		}
	   $datamensaje['html'] .= "</tr>";
	  }
	  $datamensaje['html'] .= "</table>";
	
	}
	
	echo json_encode($datamensaje);
	return;
}


//Trae las tablas que contienen los centros de costos de las empresas.
function traer_tablas_cco($conex, $aplicacion){


	$q =  " SELECT Detval, Detemp "
		  ."   FROM root_000051"
		  ."  WHERE Detapl = '".$aplicacion."'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	$array_ccos = array();

while($row = mysql_fetch_array($res))
    {
        //Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row['Detval'], $array_ccos))
        {
            $array_ccos[$row['Detval']] = $row;
        }

    }

	return $array_ccos;
}

function traer_talumas($conex, $informacion_empleados, $aplicacion, $wbasedato){

	
	global $conex;
	
	$q =  " SELECT Tabtab "
		  ."   FROM ".$wbasedato."_000008"
		  ."  WHERE Tabemp = '".$aplicacion."'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);
	$empresas_votacion_a = $row['Tabtab'];
	$empresas_votacion_b = explode(",",$empresas_votacion_a);
	$empresas_votacion = implode("','", $empresas_votacion_b);	
	
	$q =  " SELECT Detval, Detemp "
		  ."   FROM root_000051"
		  ."  WHERE Detemp in ('".$empresas_votacion."')"
		  ."    AND Detapl = '".$informacion_empleados."'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	$array_talhumas = array();

	while($row = mysql_fetch_array($res))
    {
        //Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row['Detemp'], $array_talhumas))
        {
            $array_talhumas[$row['Detemp']] = $row;
        }

    }

	return $array_talhumas;
}

function empresas(){


	global $conex;

	 $q =  " SELECT Empcod, Empdes "
		  ."   FROM root_000050";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	$array_empresas = array();

	while($row = mysql_fetch_array($res))
    {
        //Se verifica si ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row['Empcod'], $array_empresas))
        {
            $array_empresas[$row['Empcod']] = $row;
        }

    }

	return $array_empresas;
}


//Trae una aplicacion con el filtro de empresa.
function aplicacion($conex, $wemp_pmla, $aplicacion){


	 $q =  " SELECT Detval "
		  ."   FROM root_000051"
		  ."  WHERE Detapl = '".$aplicacion."'"
		  ."	AND Detemp = '".$wemp_pmla."'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);
	$waplicacion = $row['Detval'];

	return $waplicacion;
}



//Trae la ruta de la foto para el usuario que esta ingresando.
function getFoto($conex,$wemp_pmla,$wbasedato,$wusuario,$wcedula = 'not_foto',$sex='M')
{
    $extensiones_img = array(   '.jpg','.Jpg','.jPg','.jpG','.JPg','.JpG','.JPG','.jPG',
                                '.png','.Png','.pNg','.pnG','.PNg','.PnG','.PNG','.pNG');
    $wruta_fotos = "../../images/medical/tal_huma/";
    $wfoto = "silueta".$sex.".png";

    $wfoto_em = '';
    $ext_arch = '';
	
	$q_nofoto =   " SELECT * "
			      ."   FROM root_000116"
			      ."  WHERE Fotced = '".$wcedula."'"				
				  ."	AND Fotest = 'on'";
	$res_nofoto  = mysql_query($q_nofoto ,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_nofoto." - ".mysql_error());
	$num_nofoto = mysql_num_rows($res_nofoto);
	
	if($num_nofoto >0){
		
		$wcedula = "";
	}
	
	foreach($extensiones_img as $key => $value)
	{
		$ext_arch = $wruta_fotos.trim($wcedula).$value;
		
		if (file_exists($ext_arch))
		{
			$wfoto_em = $ext_arch;
			break;
		}
	}


    if ($wfoto_em == '')
    {
        $wfoto_em = $wruta_fotos.$wfoto;
    }

    return $wfoto_em;
}

//Funcion que registra el voto del asociado.
function votar($wemp_pmla, $wbasedato, $wcedula, $wtipo, $wconsecutivo_votacion, $wcod_votado, $wced_votado, $wempresa, $wtema, $aplicacion){

	global $conex;
	global $wbasedato;
	global $wusuario;

	$wfecha = date("Y-m-d");
    $whora  = date("H:i:s");

	$datamensaje = array('mensaje'=>'', 'error'=>0, 'actualizado'=>'');

	//Log de votacion.
    $q4 = " INSERT INTO ".$wbasedato."_000004 (   Medico  ,    Fecha_data,      Hora_data,        Rvoapl,                     Rvovot           , Rvocod      ,   Rvoced, 			 Rvoemp     , Rvoest, Rvotem, Seguridad     ) "
					  ."               VALUES ('".$wbasedato."','".$wfecha."','".$whora."', '".$aplicacion."', '".$wconsecutivo_votacion."','".$wusuario."', '".$wcedula."', '".$wempresa."', 'on', '".$wtema."', 'C-".$wusuario."')";
	$err4 = mysql_query($q4, $conex) or die (mysql_errno() . $q4 . " - " . mysql_error());

	//Registro de voto.
	$q5 = " INSERT INTO ".$wbasedato."_000005 (   Medico  ,    Fecha_data,      Hora_data,            Votapl,    Votvot           , Votcod      ,          Votced        ,      Votemp     ,Votest, Vottem, Seguridad     ) "
					  ."               VALUES ('".$wbasedato."','".$wfecha."','".$whora."', 	'".$aplicacion."','".$wconsecutivo_votacion."','".$wcod_votado."', '".$wced_votado."', '".$wempresa."',  'on', '".$wtema."', 'C-".$wbasedato."')";
	$err5 = mysql_query($q5, $conex) or die (mysql_errno() . $q5 . " - " . mysql_error());

	$datamensaje['mensaje'] = "Gracias por su voto";
	$datamensaje['mensaje_html'] = "<div align=center style='background-color:yellow; height:20px; width: 200px;'><b>Su voto fue registrado.</b></div><br>";
	$datamensaje['tipo'] = $wtipo;

	 echo json_encode($datamensaje);
     return;
}


function verificar_votacion($wusuario, $wcedula, $wconsecutivo_votacion, $aplicacion){

	global $conex;
	global $wbasedato;

	$q_reg_vot =   " SELECT * "
			      ."   FROM ".$wbasedato."_000004, ".$wbasedato."_000005 "
			      ."  WHERE Rvocod = '".$wusuario."'"
				  ."	AND Rvoced = '".$wcedula."'"				
				  ."	AND Rvovot = '".$wconsecutivo_votacion."'"
				  ."	AND Rvoapl = '".$aplicacion."'"
				  ."	AND ".$wbasedato."_000004.Fecha_data = ".$wbasedato."_000005.Fecha_data"
				  ."	AND ".$wbasedato."_000004.Hora_data = ".$wbasedato."_000005.Hora_data"
			      ."    AND Rvoest = 'on'";
	$res_reg_vot = mysql_query($q_reg_vot,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_reg_vot." - ".mysql_error());
	
	$array_reg_vot = array();	
	
	while($row_reg_vot = mysql_fetch_assoc($res_reg_vot))
    {
       $array_reg_vot[$wconsecutivo_votacion][$row_reg_vot['Rvotem']] = $row_reg_vot;       

    }
	
	// echo "<pre>";
	// echo "<div>";
	// print_r($array_reg_vot);
	// echo "</div>";
	// echo "<pre>";
	
	return $array_reg_vot;

}

if(isset($consultaAjax))
	{

	switch($consultaAjax){

		case 'votar':
					{
					echo votar($wemp_pmla, $wbasedato, $wcedula, $wtipo, $wconsecutivo_votacion, $wcod_votado, $wced_votado, $wempresa, $wtema, $aplicacion);
					}
		break;
		
		case 'consultar_registros':
					{
					echo consultar_registros($wemp_pmla, $wbasedato, $wseguridad, $registros_inicial, $registros_final, $aplicacion, $texto_buscar, $wccostos, $wcargo, $empresa_asociado, $wempresas_filtro, $instructivo_visto);
					}
		break;

		default: break;

		}
	return;
	}

?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
<meta charset="utf-8">
<title>Votaciones</title>
</head>
<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />	
<link type='text/css' href='../../../include/root/ui.core.css' rel='stylesheet' />
<link type="text/css" href="../../../include/root/smartpaginator.css" rel="stylesheet" /> <!-- Autocomplete -->
<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />

<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script src="../../../include/root/modernizr.custom.js" type="text/javascript"></script>
<script type='text/javascript' src='../../../include/root/jquery.blockUI.min.js'></script>
<link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.css" />
<link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.filter.css" />
<script type="text/javascript" src="../../../include/root/jquery.multiselect.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.multiselect.filter.js"></script>
<script type="text/javascript" src="../../../include/root/prettify.js"></script>
<script type='text/javascript' src='../../../include/root/smartpaginator.js'></script>	<!-- Autocomplete -->
<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
		

<style type="text/css">
    .border1{
	-moz-border-radius: 1em;
	-webkit-border-radius: 1em;
	border-radius: 1em;
	}

	#div_tabla{
	height: auto;
	text-align:center;
	padding: 10px;
	width: 180px;
	border: #CCCCCC solid 2px;
	background: #FFFFFF;
	}

	hr {border: 0; height: 12px; box-shadow: inset 0 12px 12px -12px #000000;}
	#tooltip{font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 8pt;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:3px;opacity:1;border-radius: 4px;}
	#tooltip div{margin:0; width:auto;}
	.bordeAbajo{
		border-bottom: 2px dotted #72A3F3;
	}
</style>
<script type="text/javascript">
			 
var mouseSobreRadio = false;


function ver_video(nombre_video, descripcion, origen){	

if(origen = 'instructivo'){
	
//Activa todos los radiobutton cuando termina la carga de la pagina
var k = 1;
jQuery(".datos_temas").each(function(i) {
		
		if($("#tema_votado_"+k).val() == undefined){
						
			$(".tema"+k).removeAttr('disabled');
			$(".tema"+k).removeAttr('title');	
			$(".tema"+k).attr('title','Click para votar');
		}else{
			
			$(".tema"+k).removeAttr('title');	
			$(".tema"+k).attr('title','Ya ha votado por este tema');
		}
	
		k++;
  
	});
	
	$( ".radiovotar").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });
	
	$("#instructivo_visto").val('on');
	
	iniciar();
}


	
$( "#dialog-modal_"+nombre_video ).dialog({
		height: 500,
		width:	700,
		modal: true,
		title:	descripcion,
		resizable: false,
		beforeClose: function() {
				//Pausar el video al cerrar la modal.
				$("#video"+nombre_video)[0].pause();
		}
		});
	
}

function validar(e){
	
	tecla = (document.all) ? e.keyCode : e.which;
	if (tecla==13){
		
		buscar_persona();
	}
}

function isNumberKey(evt)
      {
         var charCode = (evt.which) ? evt.which : event.keyCode
         if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
 
         return true;
      }


var largo = 7;

function buscar_persona(){
	
	var cantidad_resultados_mostrar =  $("#cantidad_resultados_mostrar").val();
	
	if(cantidad_resultados_mostrar == ''){
		registros_pagina = 4;
	}else{
		registros_pagina = cantidad_resultados_mostrar;
	}
	
	var registros_pagina = registros_pagina;
	var total_registros = $("#total_registros").val();
	var empresa_asociado = $("#empresa_asociado").val();
	var instructivo_visto = $("#instructivo_visto").val();
	
	var wemp_pmla = $("#wemp_pmla").val();
	var wbasedato = $("#wbasedato").val();
	var wseguridad = $("#wseguridad").val();
	var aplicacion = $("#aplicacion").val();		
	var texto_buscar = $("#id_search").val();
	var wccostos = 	$("#wccostos").val();
	var wcargo = 	$("#wcargo").val();
	var wempresas_filtro = 	$("#wempresas_filtro").val();
	var newPage = 1;	
			
	$.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
								css: 	{
											width: 	'auto',
											height: 'auto'
										}
						 });

				$.ajax({
					url: "votar.php",
					type: "POST",
					data:{
						consultaAjax: 		'consultar_registros',	
						wemp_pmla: 			wemp_pmla,						
						texto_buscar: 		texto_buscar,										
						wemp_pmla:      	wemp_pmla,
						wbasedato:      	wbasedato,							
						wseguridad:			wseguridad,
						registros_inicial:	(newPage*registros_pagina) - registros_pagina,
						registros_final:	registros_pagina,
						aplicacion:			aplicacion,
						wccostos:			wccostos,
						wcargo:				wcargo,
						empresa_asociado:	empresa_asociado,
						wempresas_filtro:	wempresas_filtro,
						instructivo_visto:	instructivo_visto
						
					},
					dataType: "json",
					async: false,
					success:function(data_json) {

						if (data_json.error == 1)
						{
							
						}
						else{
							
							$('#lista_personas_votacion').html(data_json.html);	
							$.unblockUI();
							if(data_json.num_resultados == 0){
								largo = 0;
							}else{
								largo = 7;
							}
						}
					}

				}).done(function(){	
							
							iniciar();							
							
							if(instructivo_visto == 'on'){								
								
								//Activa todos los radiobutton cuando termina la carga de la pagina
								jQuery(".radiovotar").each(function(i) {
												jQuery(this).removeAttr('disabled');
												
												});
								
								//Al terminar la carga de la pagina inactiva los que ya votaron. 
								jQuery(".inactivo").each(function(i) {								
											
											jQuery(this).attr('title', 'Ya ha votado por este tema');
											jQuery(this).attr('disabled', 'disabled');
											
											});
											
								jQuery(".votado").each(function(i) {
									
											jQuery(this).attr('title', 'Clik para votar');
											jQuery(this).removeAttr('disabled');
											
											});
											
								
							}else{
								
								//Al terminar la carga de la pagina inactiva los que ya votaron. 
								jQuery(".votado").each(function(i) {
									
											jQuery(this).attr('title', 'Debe ver el video instructivo para poder votar');
											jQuery(this).attr('disabled', 'disabled');
											
											});
								
								jQuery(".inactivo").each(function(i) {
									
											jQuery(this).attr('title', 'Ya ha votado por este tema');
											jQuery(this).attr('disabled', 'disabled');
											
											});
											
								
								
							}
							
							// --> Activar tooltip
								$("[tooltip=si]").tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
						
						});
	
}


function votar(wemp_pmla, wbasedato, cedula, i, tipo, consecutivo_votacion, cod_votado, ced_votado, empresa, Elemento, tema, desc_tema){
	
	var nomSuplente 	= $(Elemento).parent().parent().prev().find("[nomSup]").text()+" "+$(Elemento).parent().parent().prev().find("[nomSup]").next().text();
	var nomPrincipal	= $(Elemento).parent().parent().prev().prev().find("[nomPri]").text()+" "+$(Elemento).parent().parent().prev().prev().find("[nomPri]").next().text();
	
	var aplicacion = $("#aplicacion").val();
	
	if( mouseSobreRadio == false )
		return;
		
	if( nomSuplente.trim() == "" && nomPrincipal.trim() == "" ){
		nomSuplente = "EN BLANCO";
		nomPrincipal = "EN BLANCO";
	}
	
	if(nomSuplente.trim() != ""){
		
		nomSuplente = "\n- "+nomSuplente;
		
	}
	
	var cantidad_temas = $("#cantidad_temas").val();
	
	if(cantidad_temas >= 2){
		var mensaje = 'Su voto ser&aacute por '+desc_tema+' para :\n\n - '+nomPrincipal+nomSuplente+' \n\nEst&aacute seguro?';
	}else{
		
		
		var mensaje = 'Votar&aacute por :\n\n - '+nomPrincipal+nomSuplente+' \n\nEst&aacute seguro?';
	}
	
	jConfirm(mensaje, 'Votar', function(r) {
	//if(jConfirm('Su voto sera por '+desc_tema+' para:\n\n - '+nomPrincipal+nomSuplente))
	if(r){
			$.post("votar.php",
						{
							consultaAjax:			'votar',
							wemp_pmla:				wemp_pmla,
							wbasedato:				wbasedato,
							wcedula:				cedula,
							wtipo:					tipo,					
							wconsecutivo_votacion: 	consecutivo_votacion,
							wcod_votado:			cod_votado,
							wced_votado:			ced_votado,
							wempresa:				empresa,
							wtema:					tema,
							aplicacion:				aplicacion

						}
						,function(data_json) {

							if (data_json.error == 1)
							{
								jAlert("Error: "+data_json);
								return;
							}
							else
							{
								
							$('#radio_ppales_'+tema).prop('checked', false);						
							$('#radio_suplentes_'+tema).prop('checked', false);
							$("#div_"+cod_votado+"_"+tema).css('background-color', 'yellow');
							$(".radio_"+cod_votado+"_"+tema).prop('checked', true);
							
							//Inactiva el radio buton de voto en blanco.
							if($("#radio_button_voto_blanco").length > 0){
																
								$("#radio_button_voto_blanco").attr('disabled', 'disabled');
							}
							
							jQuery("input[name='radio_button_"+data_json.tipo+tema+"']").each(function(i) {
								jQuery(this).attr('disabled', 'disabled');
								jQuery(this).attr('title', ''); //Se inicia en vacio el title
								jQuery(this).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
								});
												

							jAlert(data_json.mensaje);


							}

					},
					"json"
				);
		}
		else
		{
			$(Elemento).removeAttr("checked");
		}
	});
}


function limpiarbusqueda(){

 $.blockUI({ message:	'Espere...',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });

$('input#id_search').val('');


location.reload();


}

function cerrarVentana(){
  window.close();
 }

 
 function iniciar(){
	 
			var total_registros = $("#total_registros").val();
			var empresa_asociado = $("#empresa_asociado").val();
			var wempresas_filtro = $("#wempresas_filtro").val();
			var cantidad_resultados_mostrar =  $("#cantidad_resultados_mostrar").val();
			if(cantidad_resultados_mostrar == ''){
				registros_pagina = 4;
			}else{
				registros_pagina = cantidad_resultados_mostrar;
			}
			
			var registros_pagina = registros_pagina;
			//var registros_pagina = $("#registros_pagina").val();
			var wemp_pmla = $("#wemp_pmla").val();
			var wbasedato = $("#wbasedato").val();
			var wseguridad = $("#wseguridad").val();
			var aplicacion = $("#aplicacion").val();			
			var texto_buscar = $("#id_search").val();
			var wccostos = 	$("#wccostos").val();
			var wcargo = 	$("#wcargo").val();
			var instructivo_visto = $("#instructivo_visto").val();
			
			//Si ponen el mouse sobre los radio de clase "radiovotar" se pone la vble mouseSobreRadio en true
			//para que permita realizar el voto
			$(".radiovotar").mouseover(function(){
				mouseSobreRadio = true;				
			});
			
			// --> Activar tooltip
			$("[tooltip=si]").tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
			//Permite que al escribir en el campo buscar, se filtre la informacion del grid
			//$('input#id_search').quicksearch('div#div_tabla_asociados table tbody .find');

			$('#paginador').smartpaginator({
						
				totalrecords: total_registros, 
				recordsperpage: registros_pagina, 
				length: largo, 
				next: 'Sig', 
				prev: 'Atras', 
				first: 'Inicio', 
				last: 'Ulti',
				go: 'Ir',
				theme: 'black',
				controlsalways: true, 
				onchange: 
				
				function (newPage) {
						
						$.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
								css: 	{
											width: 	'auto',
											height: 'auto'
										}
						 });
												
						$.ajax({
							url: "votar.php",
							type: "POST",
							data:{
							
								consultaAjax: 		'consultar_registros',					
								wemp_pmla:      	wemp_pmla,
								wbasedato:      	wbasedato,							
								wseguridad:			wseguridad,
								registros_inicial:	(newPage*registros_pagina) - registros_pagina,
								registros_final:	registros_pagina,
								aplicacion:			aplicacion,
								texto_buscar:		texto_buscar,
								wccostos:			wccostos,
								wcargo:				wcargo,
								empresa_asociado:	empresa_asociado,
								wempresas_filtro:	wempresas_filtro,
								instructivo_visto: 	instructivo_visto

							},			
							async: false,
							dataType: "json",
							success:function(data_json) {
						
								if (data_json.error == 1)
								{
									jAlert(data_json.mensaje);
									return;
								}
								else{
																			
									$('#lista_personas_votacion').html(data_json.html);
									$.unblockUI();
								}
							}
						}
					).done(function(){	
							
							if(instructivo_visto == 'on'){								
								
								//Activa todos los radiobutton cuando termina la carga de la pagina
								jQuery(".radiovotar").each(function(i) {
												jQuery(this).removeAttr('disabled');
												
												});
								
								//Al terminar la carga de la pagina inactiva los que ya votaron. 
								jQuery(".inactivo").each(function(i) {								
											
											jQuery(this).attr('title', 'Ya ha votado por este tema');
											jQuery(this).attr('disabled', 'disabled');
											
											});
											
								jQuery(".votado").each(function(i) {
											
											jQuery(this).attr('title', 'Clik para votar');
											jQuery(this).removeAttr('disabled');
											
											});
											
								
							}else{
								
								//Al terminar la carga de la pagina inactiva los que ya votaron. 
								jQuery(".votado").each(function(i) {
									
											jQuery(this).attr('title', 'Debe ver el video instructivo para poder votar');
											jQuery(this).attr('disabled', 'disabled');
											
											});
								
								jQuery(".inactivo").each(function(i) {
									
											jQuery(this).attr('title', 'Ya ha votado por este tema');
											jQuery(this).attr('disabled', 'disabled');
											
											});
											
								
								
							}
							
							// --> Activar tooltip
								$("[tooltip=si]").tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
							
							//Si ponen el mouse sobre los radio de clase "radiovotar" se pone la vble mouseSobreRadio en true
							//para que permita realizar el voto
							jQuery(".radiovotar").mouseover(function(){
								mouseSobreRadio = true;								
							});
						});				
				}
            });
	 
	 
 }

 
 
 
 
$(document).ready( function () {
	
			iniciar();
			
			 $("#wcargo").val("");
			 $("#wccostos").val("");
			 $("#wempresas_filtro").val("");
			 $("#id_search").val("");			 
			
			$('#wcargo').multiselect({
						   multiple: false,						   
						   selectedList: 1													   
									}).multiselectfilter();
			
			$('#wccostos').multiselect({
						   multiple: false,						   
						   selectedList: 1													   
									}).multiselectfilter();
									
			$('#wempresas_filtro').multiselect({
						   multiple: false,						   
						   selectedList: 1													   
									}).multiselectfilter();
			
			
			 
			$("#esperar").hide();
			$("#tabla_principales").show();
			
			//Activa todos los radiobutton cuando termina la carga de la pagina
			jQuery(".radiovotar").each(function(i) {
							//jQuery(this).removeAttr('disabled');
							
							});
			
			//Al terminar la carga de la pagina inactiva los que ya votaron. 
			jQuery(".inactivo").each(function(i) {
				
						jQuery(this).attr('disabled', 'disabled');
						
						});
						
			var ver_instructivo = $('#instructivo_visto').val();
			
			if(ver_instructivo == 'on'){
				
				$('#tooltip').remove();				
				
			}
							
        });


</script>
<Body>
<?php

//==========================================================================================================================================
//PROGRAMA				      :Pograma donde se listan los asociados aprobados para que sean votados.
//AUTOR				          :Jonatan Lopez Aguirre.
//FECHA CREACION			  :Enero 31 de 2014
//FECHA ULTIMA ACTUALIZACION  :Enero 31 de 2014

$texto_aplicacion = str_replace("_"," ",$aplicacion);
encabezado("Votaciones $texto_aplicacion",$wactualiz, $wlogoempresa);

//=========================================================================================================================================\\
// 2018-02-28	Jessica		- Se agrega a la validación de fecha la hora de inicio y cierre de votación
//Julio 21 de 2014 (Jonatan Lopez)
//Se controla si se muestra la informacion del suplente con un parametro en la root_000051 ControlSuplenteVotaciones.
//=========================================================================================================================================\\

//Verifica si hay votaciones abiertas, en caso no haber ninguna muestra un mensaje diciendo que no estan abiertas, ademas las inscripciones deben estar cerradas.
if($westado_votaciones == 'on'){
	
	
//Buscar en la tabla de usuario que cco le pertenece al usuario.
$q_user = "SELECT Descripcion, Empresa
			 FROM usuarios
			WHERE Codigo = '".$wusuario."'			
			  AND Activo = 'A'";
$res_user = mysql_query($q_user, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_user . " - " . mysql_error());
$row_user = mysql_fetch_array($res_user);

$empresa_tabla_usuarios = $row_user['Empresa'];

foreach($wtalhumas as $key_a => $value){
	
//Consulto la informacion del usuario por el codigo en la tabla talhuma_000013
$q[] =    " SELECT Idegen, Ideced, Ideno1, Ideno2, Ideap1, Ideap2, Idecco, Ideccg, Ideuse, SUBSTRING_INDEX( Ideuse, '-', -1 ) as empresa"
		 ."	  FROM ".$value['Detval'].""
		 ."	 WHERE (Ideuse = '".$key2."-".$wemp_pmla1."'"
		 ."     OR Ideuse = '".$wemp_pmla1.$key2."' OR Ideuse = '".$key2."-".$empresa_tabla_usuarios."')"
		 ."    AND Ideest = 'on'"
		 ."    AND Ideced != '' ";

	}

//Uno el arreglo anterior con la instruccion UNION para asi tener toda la informacion.
$q = implode(" UNION ", $q);
$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
$row = mysql_fetch_assoc($res);
$num = mysql_num_rows($res);

if($wcon_inscripcion != 'on'){
	
	$num = 1; // Se aumenta en 1 si no esta habilitada la inscripcion.
	$num_asoc = 1;
	
}

if($num > 0){
	
	$genero = $row['Idegen'];
	$wcedula = $row['Ideced'];
	$wnombre1 = strtolower($row['Ideno1']);
	$wnombre2 = strtolower($row['Ideno1']);
	$wapeliido1 = strtolower($row['Ideap1']);
	$wapellido2 = strtolower($row['Ideap2']);
	$wcentro_costos = $row['Idecco'];
	
	$wtemas_votados = verificar_votacion($wusuario, $wcedula, $wconsecutivo_votacion, $aplicacion);
	
	if(count($wtemas_votados) > 0){ 
		foreach($wtemas_votados[$wconsecutivo_votacion] as $key_votados => $value_votados){
			
			echo "<input type='hidden' class='temas_votados' id='tema_votado_".$key_votados."' value='".$value_votados."'>";
			
		}
		
	}
	
	foreach($array_temas as $key_tema => $value_tema){
		
		echo "<input type='hidden' class='datos_temas' value='".$key_tema."'>";
		
	}
	
	echo "<input type='hidden' class='cantidad_temas' value='".count($array_temas)."'>";	
		
	if(count($wtemas_votados[$wconsecutivo_votacion]) >= count($array_temas)){

		echo "<br>\n<br>\n".
			" <H1 align=center>Su votaci&oacuten esta cerrada.</H1>";
		exit();
	} 
		

	$array_inscritos_ppales = array();
	$array_cco_empresa = array();
	
	//Si las votaciones son con inscripcion hace la relacion con la tabla de inscritos para la votacion.
	if($wcon_inscripcion == 'on'){
		
		if($wcopaso != 'on'){
		
			//***************************** //OJO CON ESTO VALIDAR QUE NO NECESITE INSCRIPCION ***************************
			//Traigo la informacion de la tabla de empleados ppal.
			$q_asoc =   " SELECT Asoemp "
					   ."   FROM ".$wbasedato."_000006 "
					   ."  WHERE Asoced = '".$wcedula."'"
					   ."    AND Asoest = 'on'";
			$res_asoc = mysql_query($q_asoc,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_asoc." - ".mysql_error());
			$num_asoc = mysql_num_rows($res_asoc);
	
		}else{
			
			$q_asoc =   " SELECT SUBSTRING_INDEX( Ideuse, '-', -1 ) as Asoemp "
					   ."   FROM talhuma_000013 "
					   ."  WHERE Ideced = '".$wcedula."'"
					   ."    AND Ideest = 'on'";
			$res_asoc = mysql_query($q_asoc,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_asoc." - ".mysql_error());
			$num_asoc = mysql_num_rows($res_asoc);
		}
		
	echo '<input type="hidden" id="query" value="'.$q_asoc.'">';
	//Verificar si el usuario se encuentra asociado al fondo de empleados.
	if($num_asoc == 0)
		{
		echo "<br>\n<br>\n".
			" <H1 align=center>Usted no esta asociado este fondo.<FONT COLOR='RED'>" .
			" </FONT></H1>\n</CENTER>";
		return;
		}

	$row_asoc = mysql_fetch_array($res_asoc);
	$wempresa_asociado = $row_asoc['Asoemp']; //Empresa del asociado de la tabla de asociados.
	
	echo "<input type=hidden id='empresa_asociado' value='".$wempresa_asociado."' >";
	
	foreach($wtalhumas as $key_b => $value){

		//Consulta los suplente principales y los agrega al arreglo inscritos
		$query_inscritos_ppales[] = "  SELECT Insced as cedula, Idegen, Ideced, Ideno1, Ideno2, Ideap1, Ideap2, Idecco, Ideccg, Ideuse, Inscsu, Insces, Insemp, Inscco, Insesu, Insccs, ".$key_b." as cod_emp
									   FROM ".$wbasedato."_000003, ".$value['Detval']."
									  WHERE Insced = Ideced
										AND Insest = 'on'
										AND Instip = '01'
										AND Insapr = 'on'
										AND Insapl = '".$aplicacion."'
										AND Insemp = '".$wempresa_asociado."'								
										AND Insvot = '".$wconsecutivo_votacion."'
										AND Ideest = 'on'";

		}
	
	}else{
				
		foreach($wtalhumas as $key_b => $value){
		$query_inscritos_ppales[] = "  SELECT Ideced as cedula, Idegen, Ideced, Ideno1, Ideno2, Ideap1, Ideap2, Idecco, Ideccg, Ideuse, SUBSTRING_INDEX( Ideuse, '-', -1 ) as Insemp, ".$key_b." as cod_emp
									     FROM ".$value['Detval']."
									    WHERE Ideest = 'on'
										  AND Ideced != ''";
		}
	}

	//Uno el arreglo anterior con la instruccion UNION para asi tener toda la informacion.
	$query_inscritos_ppales = implode(" UNION ", $query_inscritos_ppales);	
	$query_inscritos_ppales_aux = "SELECT * FROM ( $query_inscritos_ppales ) AS t GROUP BY cedula, Ideuse ORDER BY Ideno1, Ideno2;";
	$res_inscritos_ppales_aux = mysql_query($query_inscritos_ppales);
	$numero_inscritos = mysql_num_rows($res_inscritos_ppales_aux);
	
	$query_inscritos_ppales = "SELECT * FROM ( $query_inscritos_ppales ) AS t GROUP BY cedula, Ideuse ORDER BY Ideno1, Ideno2 LIMIT 4;";
	// echo "<pre>";
	// echo $query_inscritos_ppales;
	// echo "</pre>";
	$res_inscritos_ppales = mysql_query($query_inscritos_ppales);
	$array_inscritos_ppales = array();

	while($row_inscritos_ppales = mysql_fetch_assoc($res_inscritos_ppales))
		{

			if(!array_key_exists($row_inscritos_ppales['cedula'], $array_inscritos_ppales) and (int)$row_inscritos_ppales['cedula'] > 0)
			{
				
				$array_inscritos_ppales[$row_inscritos_ppales['cedula']] = $row_inscritos_ppales;
				$array_inscritos_ppales_aux[$row_inscritos_ppales['cedula']] = $row_inscritos_ppales;
			}

			if(!array_key_exists($row_inscritos_ppales['Idecco'], $array_cco_empresa))
			{
				$array_cco_empresa[$row_inscritos_ppales['Idecco']] = $row_inscritos_ppales['Idecco'];
			}

		}

	$array_suplentes = array();
	//Verifico si se debe mostrar el suplente.
	if($wcontrol_suplente_est == 'on'){
	//Recorro todos los talhumas y creo la union de todos ellos, esto para buscar los suplentes.
	foreach($wtalhumas as $key_c => $value){

	$query_suplentes[] = " SELECT Insced as cedula, Insapr, Idegen, Ideced, Ideno1, Ideno2, Ideap1, Ideap2, Idecco, Ideccg, Ideuse, Inscsu, Insces, Insemp, Inscco, Insesu, Insccs
						   FROM ".$wbasedato."_000003, ".$value['Detval']."
						  WHERE Insces = Ideced
							AND Insest = 'on'
							AND Insapr = 'on'
							AND Insapl = '".$aplicacion."'
							AND Insemp = '".$wempresa_asociado."'							
							AND Insvot = '".$wconsecutivo_votacion."'
							AND Ideest = 'on'";
		}

	//Uno el arreglo anterior con la instruccion UNION para asi tener toda la informacion.
	$query_suplentes = implode(" UNION ", $query_suplentes);
	$res_suplentes = mysql_query($query_suplentes);
	

	while($row_suplentes = mysql_fetch_assoc($res_suplentes))
		{
			//Se verifica si el producto ya se encuentra en el arreglo, si no esta lo agrega.
			if(!array_key_exists($row_suplentes['Insces'], $array_suplentes))
			{
				$array_suplentes[$row_suplentes['Insces']] = $row_suplentes;
			}

		}
	}
	// echo "<pre>";
	// echo "<div>";
	// print_r($array_inscritos_ppales);
	// echo "</div>";
	// echo "<pre>";
	
	$array_centros_costo = array();
	//Busco las tablas de centros de costos.
	$wtabla_cco = traer_tablas_cco($conex, "costoscer");

	//Consultar el los centros de costos de las empresas.
	$array_empresas_cco = array();

	foreach($wtabla_cco as $key_tablas_cco => $value_tablas_cco){

		//Consulto en las tablas los datos de los centros de costos.
		$q_cco =   " SELECT * "
				  ."   FROM ".$value_tablas_cco['Detval']."";
		$res_ccos = mysql_query($q_cco,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_cco." - ".mysql_error());

		//Creo un arreglo inicial de empresas.
		if(!array_key_exists($value_tablas_cco['Detemp'], $array_empresas_cco))
		{
			$array_empresas_cco[$value_tablas_cco['Detemp']] = array();
		}

		//A cada empresa le relaciono sus centros de costos.
		while($row_ccos = mysql_fetch_assoc($res_ccos))
		{
			//Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
			if(!array_key_exists($row_ccos['Ccocod'], $array_empresas_cco[$value_tablas_cco['Detemp']]))
			{

				$array_empresas_cco[$value_tablas_cco['Detemp']][$row_ccos['Ccocod']] = $row_ccos;
			}

		}

	}

		// echo "<pre>";
		// print_r($array_empresas_cco);
		// echo "</pre>";
	///==================== Asociados al fondo con registro activo ==================

	$q =  " SELECT Asoced, Asoemp, Asoemr "
		 ."   FROM ".$wbasedato."_000006"
		 ."	 WHERE Asoest = 'on'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	$array_asociados = array();

	while($row_asociados = mysql_fetch_assoc($res))
    {
        //Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_asociados['Asoced'], $array_asociados))
        {
            $array_asociados[$row_asociados['Asoced']] = $row_asociados;
        }

    }
	

	// echo "<pre>";
 // print_r($array_temas);
 // echo "</pre>";

//=====================

	$whabilitar_votaciones = '';
	
	//---------------- Videos asociados ------------------						
	if($video_instructivo_obligatorio == 'on'){		
		echo "<input type='hidden' value='' id='instructivo_visto'>";		
	}else{
		echo "<input type='hidden' value='on' id='instructivo_visto'>";
	}
	if(count($array_temas) > 1){
		
		$array_video_instructivo = explode(".",$video_instructivo);
		$video_ins_nombre = $array_video_instructivo[0];
		$video_ins_ext = $array_video_instructivo[1];
		echo "<center><u><b>Video Instructivo <br> ".utf8_encode($descrip_votacion)." </u> <a href=# onclick='ver_video(\"".$video_ins_nombre."\", \"".utf8_encode($descrip_votacion)."\", \"instructivo\");'>click aqu&iacute</a></b></center>";
		echo '<div id="dialog-modal_'.$video_ins_nombre.'" style="display:none">
				<video width="640" height="360" controls preload id="video'.$video_ins_nombre.'"> 
				  <source src="../../images/medical/tal_huma/'.$video_ins_nombre.'.'.$video_ins_ext.'" type="video/'.$video_ins_ext.'" />
				  Tu navegador no implementa el elemento <code>video</code>.
				</video>
				</div><br>';
			
		echo "<center>";
		echo "<fieldset style='height:auto;width:600px'><legend>Videos: </legend>";
		foreach($array_temas as $key => $value){
			
			$array_video = explode(".",$value['Temvid']);
			$nombre_video = $array_video[0];
			$ext_video = $array_video[1];
			
			if($value['Temvid'] != ''){
				
				echo '<div id="dialog-modal_'.$nombre_video.'" style="display:none">
						<video width="640" height="360" controls preload id="video'.$nombre_video.'"> 
						  <source src="../../images/medical/tal_huma/'.$nombre_video.'.'.$ext_video.'" type="video/'.$ext_video.'" />
						  Tu navegador no implementa el elemento <code>video</code>.
						</video>
						</div>';
						
				echo "<center><u><b>".utf8_encode($value['Temdes'])." </u> <a href=# onclick='ver_video(\"".$nombre_video."\", \"".utf8_encode($value['Temdes'])."\",\"temas\");'>click aqu&iacute</a></b></center>";
				echo "<div align='center' id='ver_video_".$nombre_video."' style='display:none'></div>";
			}
			
		}
		echo " </fieldset>";
		echo "</center>";
	}
	//-----------------------------------------------------
	echo "<br><br>";
	echo "<form id='form_inscritos_ppales'>";
	echo "<input id='aplicacion' type='hidden' value='$aplicacion'>
		  <input id='wemp_pmla' type='hidden' value='$wemp_pmla'>
		  <input id='wbasedato' type='hidden' value='$wbasedato'>
		  <input id='wseguridad' type='hidden' value='$wusuario'>
		  <input id='cedula' type='hidden' value='$cedula'>";
	echo "<center>";	
	echo '<div id="buscar_nombre"><input id="id_search" type="text" value="" style="height: 30px;width: 300px;" name="search" onkeypress="validar(event)" placeholder="Buscar por nombre"></div>';
	echo '<table style="text-align: left; width: 470px; height: 45px;"
			 border="0" cellpadding="2" cellspacing="2">
			  <tbody>
				<tr>
				  <td class=encabezadoTabla align=center><b>Empresa</b></td>
				  <td class=encabezadoTabla align=center><b>Por centro de costos</b></td>
				  <td class=encabezadoTabla align=center><b>Por cargo</b></td>			 
				</tr>
				<tr>
				  <td><select style="width:345px;" id="wempresas_filtro" name="wempresas_filtro" onchange="buscar_persona();">'.$wempresas_select.'</select></td>				  
				  <td><select style="width:345px;" id="wccostos" name="wccostos" onchange="buscar_persona();">'.$centro_costos.'</select></td>
				  <td><select style="width:345px;" id="wcargo" name="wcargo" onchange="buscar_persona();">'.$cargos.'</select></td>				 
				</tr>
			  </tbody>
			</table>';
	echo '<div><input type=button onclick="buscar_persona();" value="Buscar"><input type=button value=Limpiar onclick="limpiarbusqueda();" title="Reiniciar Busqueda" style="cursor:pointer" src="../../images/medical/sgc/Refresh-128.png"></div>';
	echo "<center>";
	echo "<br><br>";
	echo "<div align=center id='div_tabla_asociados' style='width: auto;'>";	
	echo "<div id='esperar'><br><br><b>Cargando, porfavor espere...</b><br><img src='../../images/medical/ajax-loader12.gif' ></div>";
	
	echo "<div id='lista_personas_votacion'>";
	echo "<table width='40%' id='tabla_principales'>"; //Tabla de principales
			
			//Deshabilita los radio button para las votaciones si el tercer parametro la root_000051 (VotacionAspirantesFe) esta en off.
		   $westado_actual_votacion = verificar_votacion($wusuario, $wcedula, $wconsecutivo_votacion, $aplicacion);
		   
		   $wempresas = empresas(); //Array de empresas

		   if (count($array_inscritos_ppales) > 0) {
			 
			 echo "<input type='hidden' id='total_registros' value='".$numero_inscritos."' >";
			 echo "<input type='hidden' id='registros_pagina' value='4' >";
			 $i = 1;
			 $j = 1;
			 $wcentro_costos_ppal= '';
			 $wcentro_costos_suple = '';
			 $texto_principal = '';
			 
			 
			 foreach($array_inscritos_ppales as $key_ppales => $valores_ppal){
				 $mensaje_foto = "";
				 $foto_ppal = "";
				 $foto_suplente = "";
				
			   $resto = ($i % $numcolumnas);
			   if($resto == 1){ /*si es el primer elemento creamos una nueva fila*/
				 echo "<tr class='find'>";
				}
				
				//Si es sin inscripciones tomara la empresa de la tabla de talhuma.
				if($westado_votaciones == 'on'){
					$wempresa_asociado = $valores_ppal['Insemp'];
				}
				
				 echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";

				 $wgenero_ppal = ($valores_ppal['Idegen'] == '') ? "M" : $valores_ppal['Idegen'];
				 $wgenero_suplente = ($array_suplentes[$valores_ppal['Insces']]['Idegen'] == '') ? "M" : $array_suplentes[$valores_ppal['Insces']]['Idegen'];
				 //Trae la foto del usuario.
				 $foto_ppal = '<img class="imagen lightbox_ppal" id=fotografia_ppal_'.$j.' width=100px height=120px src="'.getFoto($conex,$wempresa_asociado,$wbasedato,$wusuario,$valores_ppal['Ideced'],$wgenero_ppal).'"/>';

				 $foto_suplente = '<img class="imagen lightbox_suplente" id=fotografia_suplente_'.$j.' width=100px height=110px src="'.getFoto($conex,$wempresa_asociado,$wbasedato,$wusuario,$valores_ppal['Insces'],$wgenero_suplente).'"/>';

				//Centro de costos asociado al ppal.
				$wcentro_costos_ppal = $array_empresas_cco[$wempresa_asociado][$valores_ppal['Idecco']]['Cconom'];
				//Si no esta con nombre lo busca con descripcion.
				$wcentro_costos_ppal = ($wcentro_costos_ppal == '') ? $array_empresas_cco[$wempresa_asociado][$valores_ppal['Idecco']]['Ccodes'] : $array_empresas_cco[$wempresa_asociado][$valores_ppal['Idecco']]['Cconom'];
				//Si no esta con nombre ni con descripcion, imprime la empresa.
				 if($wcentro_costos_ppal == ''){

					$wcentro_costos_ppal = $wempresas[$valores_ppal['Insemp']]['Empdes'];
				}

				//Centro de costos asociado al suplente.
				$wcentro_costos_suple = $array_empresas_cco[$wempresa_asociado][$array_suplentes[$valores_ppal['Insces']]['Idecco']]['Cconom'];
				//Si no esta con nombre lo busca con descripcion.
				$wcentro_costos_suple = ($wcentro_costos_suple == '') ? $array_empresas_cco[$wempresa_asociado][$array_suplentes[$valores_ppal['Insces']]['Idecco']]['Ccodes'] : $array_empresas_cco[$wempresa_asociado][$array_suplentes[$valores_ppal['Insces']]['Idecco']]['Cconom'];
				//Si no esta con nombre ni con descripcion, imprime la empresa.
				if($wcentro_costos_suple == ''){

					$wcentro_costos_suple = $wempresas[$valores_ppal['Insesu']]['Empdes'];
				}


				//Si el asociado ppal tiene datos en empresa real en la tabla de asociados (fondos_000006 - Asoemr), entonces pondra esa en el reporte, sino, pondra la empresa que se encuentra en el campo Asoemp.
				if($array_asociados[$valores_ppal['Insced']]['Asoemr'] != ''){
					$wempresa_ppal = $wempresas[$array_asociados[$valores_ppal['Insced']]['Asoemr']]['Empdes'];
					$wcentro_costos_ppal = $wempresas[$array_asociados[$valores_ppal['Insced']]['Asoemr']]['Empdes']; //Esta variable se reasignara en caso de que el asociado tenga datos en empresa real.
				}else{
					$wempresa_ppal = $wempresas[$array_asociados[$valores_ppal['Insced']]['Asoemp']]['Empdes'];
				}
				
				if($wempresa_ppal == ''){
					
					$wempresa_ppal = $wempresas[$valores_ppal['Insemp']]['Empdes'];
					
				}
								
				if($wempresa_ppal == ''){
					
					$wempresa_ppal = $wempresas[$valores_ppal['cod_emp']]['Empdes'];
					
				}

				//Si el asociado suplente tiene datos en empresa real en la tabla de asociados (fondos_000006 - Asoemr), entonces pondra esa en el reporte, sino, pondra la empresa que se encuentra en el campo Asoemp.
				if($array_asociados[$valores_ppal['Insces']]['Asoemr'] != ''){
					$wempresa_suplente = $wempresas[$array_asociados[$valores_ppal['Insces']]['Asoemr']]['Empdes'];
					$wcentro_costos_suple = $wempresas[$array_asociados[$valores_ppal['Insces']]['Asoemr']]['Empdes'];	//Esta variable se reasignara en caso de que el asociado tenga datos en empresa real.
				}else{
					$wempresa_suplente = $wempresas[$array_asociados[$valores_ppal['Insces']]['Asoemp']]['Empdes'];
				}

				if( $wcontrol_suplente_est == 'on'){
					
					$texto_principal = 'Principal';
					
				}
				
				if(isset($array_foto[$valores_ppal['cedula']])){
					
					$mensaje_foto = "<div style='font-size:12px; color:#000;'>&oslash;</div>";
					
				}
				
				 echo "<td class='bordeAbajo'><br>
							<div id='div_tabla' class='border1'>
							<div style='font-size:12px; color:#000000;'><b>$texto_principal</b></div>
							<div>&nbsp;</div>
							<div>".$foto_ppal."</div>
							<div><hr></div>
							<div nomPri style='font-size:18px; color:#13189F; display:block;'>".ucfirst($valores_ppal['Ideno1'])." ".ucfirst($valores_ppal['Ideno2'])."</div>
							<div style='font-size:12px; color:#13189F;'>".ucfirst(utf8_encode($valores_ppal['Ideap1']))." ".ucfirst($valores_ppal['Ideap2'])."</div>
							<div style='font-size:10px; color:#000000;'><b>".@utf8_encode($wempresa_ppal)."</b></div>
							<div style='font-size:9px; color:#D02114;'>".utf8_encode($wcentro_costos_ppal)."</div>
							".$mensaje_foto."
						</div><br></td>";
				
				//Verifico si se debe mostrar el suplente.
				if($wcontrol_suplente_est == 'on'){
				
				if(isset($array_foto[$valores_ppal['Insces']])){
					
					$mensaje_foto = "<div style='font-size:12px; color:#000;'>&oslash;</div>";
					
				}
				
				echo "<td class='bordeAbajo'><br><div id='div_tabla' class='border1'>
							<div style='font-size:12px; color:#000000;'><b>Suplente</b></div>
							<div>&nbsp;</div>
							<div>".$foto_suplente."</div>
							<div><hr></div>
							<div nomSup style='font-size:18px; color:#13189F;'>".ucfirst($array_suplentes[$valores_ppal['Insces']]['Ideno1'])." ".ucfirst($array_suplentes[$valores_ppal['Insces']]['Ideno2'])."</div>
							<div style='font-size:12px; color:#13189F; display:inline;'>".ucfirst($array_suplentes[$valores_ppal['Insces']]['Ideap1']." ".$array_suplentes[$valores_ppal['Insces']]['Ideap2'])."</div>
							<div style='font-size:10px; color:#000000;'><b>".utf8_encode($wempresa_suplente)."</b></div>
							<div style='font-size:9px; color:#D02114;'>".utf8_encode($wcentro_costos_suple)."</div>
							".$mensaje_foto."
						</div><br></td>";
				}else{
				
				echo "<td></td>";
				
				}
				
				//Radio buton para votar.
				
				echo "<td colspan=2 style='padding: 3px;'>";
				
				//Si tiene mas de una opcion en los temas muestra el titulo de tema.
				if(count($array_temas) > 1){
					
					echo "<div align=center><b>TEMA</b></div>";	
				}
								
				foreach($array_temas as $key => $value){
					
					$wradio_checked = '';
					$wcursor_votar = '';				
					$color_votado = '';
					$wactivo = '';
					
				   if($westado_votaciones == 'on'){
						
						//Verifica si el asociado no ha votado.
						if($westado_actual_votacion[$wconsecutivo_votacion][$value['id']] == ''){
							
							$whabilitar_votaciones = "";
							$vista_mensaje = 'display:none;'; //Si no ha votado, el tr del mensaje no se mostrará.
							$wcursor_votar = "pointer";
							
							if($wcon_inscripcion == 'off'){
																
								$wactivo = "disabled";
								
							}else{
								
								$wtexto_tooltip = utf8_encode($value['Temdes']);	
								$wactivo = "";
							}

						}else{

								//Si ya voto, lo botones se inhabilitaran.
								$whabilitar_votaciones = "inactivo";
								
								if($westado_actual_votacion[$wconsecutivo_votacion][$value['id']]['Votcod'] == $valores_ppal['Ideuse']){
								
									$color_votado = "background-color: yellow;";
									$wradio_checked = 'checked';
								}
								
								//$wmensaje = "<div align=center style='background-color:yellow; height:20px; width: 200px;'><b>Su voto fue registrado.</b></div><br>";

								//$vista_mensaje = '';
							}

						}else{
						   $whabilitar_votaciones = "inactivo"; //Si ya voto, lo botones se inhabilitaran.
						   $vista_mensaje = 'display:none;'; //Si no ha votado pero las votaciones estan cerradas, el tr del mensaje no se mostrará.
						   }
					
					
					echo "	<div align='left' id='div_".$valores_ppal['Ideuse']."_".$value['id']."' style='padding: 3px;font-size: 8pt; font-family: verdana;color:#2A5DB0;border: 1px solid #72A3F3;white-space: nowrap; $color_votado'>
								<b>".strtoupper(utf8_encode($value['Temdes']))."</b>
								<input type=radio class='tema".$value['id']." radiovotar radio_".$valores_ppal['Ideuse']."_".$value['id']." ".$whabilitar_votaciones."' name='radio_button_principal".$value['id']."' value='".$value['Temcvo']."_".$value['id']."' tooltip='si' $wactivo title='Debe ver el video instructivo para poder votar' style='cursor:$wcursor_votar' id='radio_ppales_".$value['id']."' ".$wradio_checked." onclick='votar(\"$wemp_pmla\",\"$wbasedato\",\"".$wcedula."\", \"$i\",\"principal\", \"".trim($wconsecutivo_votacion)."\", \"".$valores_ppal['Ideuse']."\" , \"".$valores_ppal['Ideced']."\",\"$wempresa_asociado\", this, \"".$value['id']."\", \"".utf8_encode($value['Temdes'])."\");'>
							</div>";
						
				}
				
				echo "	</td>";
				//}
				 /*mostramos el valor del campo especificado*/
				if($resto == 0){
				  /*cerramos la fila*/
				  echo "</tr>";
				}
			   $i++;
			   $j++;

			 }

		 if($resto != 0){
		  /*Si en la &uacute;ltima fila sobran columnas, creamos celdas vac&iacute;as*/
		   for ($j = 0; $j < ($numcolumnas - $resto); $j++){
			 echo "<td></td>";
			}
		   echo "</tr>";
		  }
		  echo "</table>";
		  
		  echo "</div>";
		  
		  echo "<br><br>";
		
		//Verifico si se debe mostrar el voto en blanco.
		if($wcontrol_voto_blanco_est == 'on'){	
		
		  $foto_blanco = '<img class="imagen lightbox_ppal" id=fotografia_ppal_'.$j.' width=100px height=110px src="'.getFoto($conex,$wempresa_asociado,$wbasedato,$wusuario,'','M').'"/>';
		  $foto_blanco_suplente = '<img class="imagen lightbox_suplente" id=fotografia_suplente_'.$j.' width=100px height=110px src="'.getFoto($conex,$wempresa_asociado,$wbasedato,$wusuario,'','M').'"/>';
				
		  echo "<center>";
		  echo "<table>";
		  echo "<tr>";
		  echo "<td><div id='div_tabla' class='border1'>
					<div style='font-size:12px; color:#000000;'><b>Principal</b></div>
					<div>&nbsp;</div>
					<div>".$foto_blanco." <img class='fotografia_ppal_".$j."' id='imagen_grande_ppal_".$j."' src='".getFoto($conex,$wempresa_asociado,$wbasedato,'','','M')."' style='display:none' /></div>
					<div><hr></div>
					<div style='font-size:18px; color:#13189F; display:block;'>Voto en blanco</div>
					<div style='font-size:12px; color:#13189F;'></div>
					<div style='font-size:10px; color:#000000;'></div>
					<div style='font-size:9px; color:#D02114;'></div>
				</div></td>";
		  echo "<td><div id='div_tabla' class='border1'>
					<div style='font-size:12px; color:#000000;'><b>Suplente</b></div>
					<div>&nbsp;</div>
					<div>".$foto_blanco_suplente." <img class='fotografia_suplente_".$i."' id='imagen_grande_suplente_".$i."' src='".getFoto($conex,$wempresa_asociado,$wbasedato,'','','M')."' style='display:none' /></div>
					<div><hr></div>
					<div style='font-size:18px; color:#13189F;'>Voto en blanco</div>
					<div style='font-size:12px; color:#13189F; display:inline;'></div>
					<div style='font-size:10px; color:#000000;'></div>
					<div style='font-size:9px; color:#D02114;'></div>
				</div></td>";
		  echo "
				<td colspan=2 style='padding: 3px;'>
					<div align='center' style='padding: 3px;font-size: 8pt; font-family: verdana;color:#2A5DB0;border: 1px solid #72A3F3;'>
						<b>VOTAR</b><br>
						<input type=radio class='radiovotar' name='radio_button_voto_blanco' tooltip='si' title='Click para votar' style='cursor:pointer' id='radio_button_voto_blanco' ".$whabilitar_votaciones." onclick='votar(\"$wemp_pmla\",\"$wbasedato\",\"".$wcedula."\", \"$i\",\"principal\", \"".trim($wconsecutivo_votacion)."\", \"00000\" , \"00000000\",\"$wempresa_asociado\", this, \"".$value['id']."\", \"".utf8_encode($value['Temdes'])."\");'>
					</div>
				</td>";
		  echo "</tr>";
		  echo "</table>";
		  echo "</center>";
		}else{
				
				echo "<td></td>";
				
				}
		echo "<div id='paginador' style='margin: auto; display: inline-block;'></div> 
			  <div><select id='cantidad_resultados_mostrar' onchange='buscar_persona();'><option disabled selected value='4'>Fotos a mostrar</option><option value='10'>10</option><option value='15'>15</option><option value='20'>20</option><option value='25'>25</option><option value='30'>30</option></select></div>";

		}
	echo "";
	echo "</div>";
	echo "</form>";
	}else{

	echo "<br>\n<br>\n".
			" <H1 align=center>Solamente puede votar por los inscritos de su empresa.<FONT COLOR='RED'>" .
			" </FONT></H1>\n</CENTER>";

	}

}else{

	echo "<br>\n<br>\n".
        " <H1 align=center>No hay votaciones abiertas en este momento.</H1>";

}

echo "<br><br>";
echo "<center>
	<div><input type=button onclick='cerrarVentana();' value='Cerrar Ventana'></div>
	</center>";

?>
</BODY>
</html>
