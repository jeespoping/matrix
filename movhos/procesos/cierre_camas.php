<html>
<head>
<title>MATRIX CIERRE DIARIO DE CAMAS</title>
<script type="text/javascript">
function reloj()
{
   var fObj = new Date();
   var horas = fObj.getHours() ;
   var minutos = fObj.getMinutes() ;
   var segundos = fObj.getSeconds() ;
   if (horas <= 9) horas = "0" + horas; if (minutos <= 9)
      minutos = "0" + minutos;
   if (segundos <= 9)
      segundos = "0" + segundos;

   window.status = horas+":"+minutos+":"+segundos;
   document.title = horas+":"+minutos+":"+segundos;
}


function Cerrar()
{
	var objFecha = new Date();


	//console.log("Hora: "+objFecha.getHours()+" Min: "+objFecha.getMinutes()+ " Seg: "+objFecha.getSeconds());

	if(objFecha.getHours() == '23' && objFecha.getMinutes() == '59' && parseInt(objFecha.getSeconds()) > 0 && parseInt(objFecha.getSeconds()) < 10)
	{
		setTimeout("ejecutar();",50000);//a los 50 minutos
		setTimeout("close();",51000);//a los 51 minutos
	}
	else
	{

		setTimeout("close();",30);
	}
}

function ejecutar()
{

    document.location.href = 'cierre_camas.php';
}
</script>
</head>
<?php
if (!isset($wfecha))
{
?>
<body onLoad="Cerrar();setInterval('reloj()',1000);" bgcolor="#FFFFFF" text="#000066">
<?php
}else{
?>
<body  bgcolor="#FFFFFF" text="#000066">
<?php
}

include_once("conex.php");
 /***************************************************************************
 *          ACTUALIZACIONES          										*
 * Actualizado: 2020-08-11 (Edwin MG.):  							*
 * Se cambia el calculo de dias de estancia desde la fecha y hora del servicio
 * hasta la fecha y hora de egreso del servicio, antes era desde la fecha de*
 * ingreso al servicio a media noche hasta la fecha y hora actual		    *
 * Actualizado: 03-Feb-2011 (John M. Cadavid G.):  							*
 * Se corrije error al asignar variables con mas de 10 decimales			*
 * y el campo en la base de datos es VARCHAR(10), se redondea el valor		*
 * Se valida que las variables a almacenar en la tabla 38 tomen algún valor *
 ****************************************************************************
 * Actualizado: 10-Mar-2011 (John M. Cadavid G.):  							*
 * Se adicionó el campo Habtmp para el INSERT en la tabla 67 de Movhos		*
 ****************************************************************************
 * Actualizado: 08-Nov-2012 (Frederick Aguirre Sanchez):					*
 * Se utiliza la funcion consultarAliasPorAplicacion para no "quemar"		*
 ****************************************************************************
 * Actualizado: 16-Nov-2012 (Frederick Aguirre Sanchez):					*
 * Se quitan condiciones para consulta de cieocu (camas ocupadas) y se		*
 * agregan la condicion habhis != ''										*
 ****************************************************************************
 * Actualizado: 21-Nov-2012 (Frederick Aguirre Sanchez):					*
 * Para el dato "camas ocupadas" se comprueba que los pacientes que aparecen*
 * en la tabla 20 no esten en proceso de traslado, para evitar contar la    *
 * habitacion como ocupada cuando se hace una entrega y no hay recibo       *
 ****************************************************************************
 * Actualizado: 27-Nov-2012 (Frederick Aguirre Sanchez):	      			*
 * Para el servicio 1179 se modifican los querys de egresos por traslado,	*
 * y Egresos y dias de estancia totales para que no cuente los pacientes  	*
 * de ambulatorio.
 ****************************************************************************
 * Actualizado: 21-Dic-2012 (Jonatan Lopez Aguirre):	      			    *
 * Se cambia el estado de los pacientes de proceso de alta a alta definitiva*
 * cuando la historia no tiene medicamentos pendientes, los mismo para los  *
 * pacientes con muerte.
 ****************************************************************************
 * Actualizado: 31-Enero-2013 (Jonatan Lopez Aguirre)						*
 * Se valida si el paciente tiene muerte activa y si es asi no se guardara 	*
 * el alta para el paciente. Ademas no se guarda el alta para los pacientes	*
 * muerte, pero si se dan de alta definitiva con las respectivas validaciones.
 ****************************************************************************
 *Actualizado: 25-02-2013 (Frederick Aguirre Sanchez)						*
 *Cuando se cancela una muerte o se cancela un alta definitiva, el programa *
 *restaura los valores en la tabla de indicadores.							*
 ****************************************************************************
 *Actualizado: 23-04-2013 (Frederick Aguirre Sanchez)						*
 *Cuando el paciente tiene una "entrega" y no se ha realizado el "recibo" 	*
 *cuenta como ocupada la habitacion donde estaba.							*
 ****************************************************************************
 *Actualizado: 25-04-2013 (Frederick Aguirre Sanchez)						*
 *Se agrega la funcion cancelar_pedido_alimentacion cuando se da alta 		*
 *definitiva																*
 ****************************************************************************
 * Actualizado: 05-Agosto-2013 (Jonatan Lopez) Se agrega la validacion de	*
 * evoluciones registradas, i el paciente tiene evoluciones pendientes, se 	*
 * guardara registro en las observaciones, al igual que las glucometrias, 	*
 * nebulizaiones, oximetrias, transfusiones e insumos.						*
 ****************************************************************************
 * Actualizado: 16 Octubre 2013 (Jonatan Lopez) Se agregan los campos 		*
 * habtip y habtfa a la tabla movhos_000067, los cuales se cargarán de la 	*
 * tabla movhos_000020(esta tabla tendra el nuevo campo habtfa).			*
 ***************************************************************************/
 /****************************************************************************
 * Actualizado: 21 Marzo 2014 (Jonatan Lopez) Se valida si el paciente		*
 * tiene muerte activa en el proceso automatico de alta, en caso de ser asi	*
 * no hara registros en la tabla 25, ni en la cancelacion de dietas.		*
 ***************************************************************************/ 
 /****************************************************************************
 * Actualizado: 06 Abril 2017 (Jonatan Lopez) Se agrega el formulario 		*
 * hce_000367 en la funcion traer_evoluciones.
 ***************************************************************************/

	include_once("root/comun.php");
	include_once("movhos/otros.php");
    include_once("movhos/validacion_hist.php");
    include_once("movhos/fxValidacionArticulo.php");
    include_once("movhos/registro_tablas.php");
	include_once("movhos/movhos.inc.php");

	$wactualizacion="Octubre 16 de 2013";

	$conex = obtenerConexionBD("matrix");
	if ( ! isset ( $wemp_pmla ) ){ //08-Nov-2012
		$wemp_pmla = "01";
	}
	$wBaseDato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$wbasedato = $wBaseDato;
    $whce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'HCE');

// Verifica si el paceinte tiene muerte activa.
function verficarmuerte($whis, $wing, $wmovhos)
{
    global $conex;

    $query = " SELECT Ubimue "
               ."FROM ".$wmovhos."_000018 "
              ."WHERE Ubihis = '".$whis."'
                  AND Ubiing = '".$wing."'";
    $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ". $query ." - ".mysql_error());
    $rows = mysql_fetch_array($res);
    $wmuerte = $rows['Ubimue'];

    return $wmuerte;
}

// FUNCION PARA MOSTRAR LOS PENDIENTES POR GRABAR PARA LAS EVOLUCIONES (Jonatan 05 Agosto 2013)
function traer_evoluciones($wmovhos, $whis, $wing, $wemp_pmla)
    {

        global $conex;
        global $whce;
        $wevoluciones = 0;
        //Extrae el nombre del formulario donde se registran las evoluciones.
        $wform_evoluciones = consultarAliasPorAplicacion($conex, $wemp_pmla, 'FormularioEvoluciones');
        $formularios_evolucion = explode(",",$wform_evoluciones);		
		
		$wform_posicion_evo69 = explode("-", $formularios_evolucion[0]); // Se explota el registro que esta separado por comas, para luego usarlo en el ciclo siguiente.
		$wformulario69 = $wform_posicion_evo69[0];
		$wposicion = $wform_posicion_evo69[1];
		
		$wform_posicion_evo367 = explode("-", $formularios_evolucion[1]); // Se explota el registro que esta separado por comas, para luego usarlo en el ciclo siguiente.
		$wformulario367 = $wform_posicion_evo367[0];
		$wposicion = $wform_posicion_evo367[1];
		
        // CONSULTA PARA EXTRAER LA FECHA MAXIMA DE LAS EVOLUCIONES GUARD PARA UNA HIST E INGRESO
        $query =     "  SELECT MAX( CONCAT( Fecha_data, ' ', Hora_data ) ) AS FechaHora "
                        ."FROM ".$wmovhos."_000119 "
                        ."WHERE Glnhis = '".$whis."'
                            AND Glning = '".$wing."'
                            AND Glnind = 'E'
                            AND Glnest = 'on'";
        $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ". $query ." - ".mysql_error());
        $rows = mysql_fetch_array($res);
        $fechamax_evolucion = $rows['FechaHora'];

        //Consulta todos los especialistas que tienen el campo usures diferente de on, quiere decir los que son profesores y los que on tienen alumnos asignados,
        //hago la relacion de los codigos para extraer la especialidad, el nombre y el codigo de la especialidad.
        $query =    " SELECT usucod, usualu, u.descripcion, espmed.Medesp, nomesp.Espnom"
                    ."  FROM ".$whce."_000020 as usuhce
                        INNER JOIN
                        usuarios as u on (u.codigo = usuhce.Usucod )
                        INNER JOIN
                        ".$wmovhos."_000048 as espmed on (espmed.Meduma = usuhce.Usucod)
                        INNER JOIN
                        ".$wmovhos."_000044 as nomesp on (nomesp.Espcod = SUBSTRING_INDEX(espmed.Medesp, '-', 1))"
                    ." WHERE usures != 'on'";
        $res= mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());

        //Se crea por defecto la posicion medico de turno, para asociarlo a los alumnos que tienen mas de un profesor.
        $array_profesores = array(  'medico_turno'=> array(
                                                                'cuantos'=>0,
                                                    'nombre_especialista'=>'MEDICO DE TURNO',
                                                                'cod_esp'=>'medico_turno',
                                                    'nombre_especialidad'=>'MEDICO DE TURNO',
                                                                'alumnos'=>array()
                                                        )
                                    );

        $array_alumnos = array();
        //Al recorrer el resultado de la consulta se crea un arreglo $array_profesores[$row['usucod']][dato] y se agrega al arreglo $array_profesores[$row['usucod']]['alumnos'][],
        //todos los alumnnos asignados a el, solo se agregaran si la posicion $alumno del foreach es diferente de vacio y diferente de punto.
        while($row = mysql_fetch_array($res))
        {
            if(!array_key_exists($row['usucod'], $array_profesores))
            {
                $array_profesores[$row['usucod']] = array();
            }

            $array_profesores[$row['usucod']]['cuantos'] = 0;
            $array_profesores[$row['usucod']]['nombre_especialista'] = $row['descripcion'];
            $array_profesores[$row['usucod']]['cod_esp'] = $row['Medesp'];
            $array_profesores[$row['usucod']]['nombre_especialidad'] = $row['Espnom'];
            $explo_alum = explode(",", $row['usualu']);

            foreach ($explo_alum as $key => $alumno)
                {
                    $array_profesores[$row['usucod']]['alumnos'][] = $alumno;

                    //Solo se agregan los que tengan datos en la posicion $alumno and diferente de punto.
                    if(!empty($alumno) and $alumno != '.')
                        {
                        $array_alumnos[$alumno]['profesor'][] = $row['usucod'];
                        }
                }
        }

        //Consulta todas las  evoluciones que no se han registrado a partir de la ultima fecha y hora de registro
        //en la tabla 119 de movhos para la historia e ingreso y el parametro Glnind = 'E', se trae tambien el nombre, la especialidad y el codigo de la especialidad.
       $query =    		" SELECT * FROM (SELECT firusu, usuhce.usualu, COUNT(firusu) as cuantos, u.descripcion, usuhce.usures, ".$whce."_000036.Fecha_data as fechafir, ".$whce."_000036.Hora_data as horafir, "
                            ." ".$whce."_000036.firhis, ".$whce."_000036.firing, ".$whce."_000036.firrol "
                            ."  FROM ".$whce."_000036, ".$whce."_000020 as usuhce
                                INNER JOIN
                                usuarios as u on (u.codigo = usuhce.Usucod )"
                            ." WHERE Firhis = '".$whis."'"
                            ."   AND Firing = '".$wing."'"
                            ."   AND Firpro = '".$wformulario69."'"
                            ."   AND Firfir = 'on'"
                            ."   AND firusu = usucod "
                            ."   AND u.Activo = 'A' "
                            ."   AND CONCAT( ".$whce."_000036.Fecha_data, ' ', ".$whce."_000036.Hora_data ) > '".$fechamax_evolucion."' HAVING COUNT(*) > 0" 
							."  UNION "
							." SELECT firusu, usuhce.usualu, COUNT(firusu) as cuantos, u.descripcion, usuhce.usures, ".$whce."_000036.Fecha_data as fechafir, ".$whce."_000036.Hora_data as horafir, "
                            ." ".$whce."_000036.firhis, ".$whce."_000036.firing, ".$whce."_000036.firrol "
                            ."  FROM ".$whce."_000036, ".$whce."_000020 as usuhce
                                INNER JOIN
                                usuarios as u on (u.codigo = usuhce.Usucod )"
                            ." WHERE Firhis = '".$whis."'"
                            ."   AND Firing = '".$wing."'"
                            ."   AND Firpro = '".$wformulario367."'"
                            ."   AND Firfir = 'on'"
                            ."   AND firusu = usucod "
                            ."   AND u.Activo = 'A' "
                            ."   AND CONCAT( ".$whce."_000036.Fecha_data, ' ', ".$whce."_000036.Hora_data ) > '".$fechamax_evolucion."' HAVING COUNT(*) > 0) as t"
                            ." GROUP BY firusu";
        $res= mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());

        while($row1 = mysql_fetch_array($res))
        {
            //Aqui solo permite ingresar si el usuario es residente, osea alumno.
            if($row1['usures'] == 'on')
            {
                //Verifica que en el array_alumnos se encuentre el codigo del alumno.
                if(array_key_exists($row1['firusu'], $array_alumnos))
                {

                    //Si un alumno tiene varios profesores, pondra como especialista la palabra medico de turno.
                    if (count($array_alumnos[$row1['firusu']]['profesor'])>1)
                    {
                        $wevoluciones += $row1['cuantos']*1;
                    }
                    else
                    {
                        $wevoluciones += $row1['cuantos']*1;
                    }


                }

            }
            //Si el usuario no es residente, entonces la informacion se mantendra como viene en el arreglo de profesores.
            else
            {
                $wevoluciones += $row1['cuantos']*1;
            }

        }


        return $wevoluciones;

}


    // FUNCION PARA MOSTRAR LOS PENDIENTES DE GLUCOMETER POR COBRAR //12 DIC 2011 Jonatan Lopez
function traer_glucometer($wmovhos, $whis, $wing, $wemp_pmla)
        {

        global $conex;
        global $whce;

        $wglucometrias = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Glucometrias');

        // CONSULTA PARA EXTRAER LA FECHA MAXIMA DE LAS GLUCOMETRIAS GUARD PARA UNA HIST E INGRESO
        $query =     "SELECT MAX( CONCAT( Fecha_data, ' ', Hora_data ) ) AS FechaHora "
                        ."FROM ".$wmovhos."_000119 "
                        ."WHERE Glnhis = '".$whis."'
                                AND Glning = '".$wing."'
                                AND Glnind = 'G'";

        $res = mysql_query($query, $conex);
        $rows = mysql_fetch_array($res);
        $fechamax_glu = $rows[0];


        //CANTIDAD DE GLUCOMETRIAS SIN GUARDAR
        $query = "SELECT COUNT(Firhis) "
                        ."FROM ".$whce."_000036 "
                        ."WHERE Firhis = '".$whis."'"
                        ."  AND Firing = '".$wing."'"
                        ."  AND Firpro = '".$wglucometrias."'"
                        ."  AND Firfir = 'on'"
                        ."  AND CONCAT( Fecha_data, ' ', Hora_data ) > '".$fechamax_glu."'";

        $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
        $rows = mysql_fetch_array($res);
        $cantidadg = $rows[0];
        return $cantidadg;

        }


// FUNCION PARA MOSTRAR LOS PENDIENTES DE GLUCOMETER POR COBRAR //12 DIC 2011 Jonatan Lopez
function traer_nebulizaciones($wmovhos, $whis, $wing, $wemp_pmla, $wcco)
        {

        global $conex;

        // CONSULTA PARA EXTRAER LA FECHA MAXIMA DE LAS NEBULIZACIONES GUARD PARA UNA HIST E INGRESO
        $query =    "SELECT MAX( CONCAT( Fecha_data, ' ', Hora_data ) ) AS FechaHora "
                        ."FROM ".$wmovhos."_000119 "
                        ."WHERE Glnhis = '".$whis."'
                            AND Glning = '".$wing."'
                            AND Glnind = 'N'";
        $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
        $rows = mysql_fetch_array($res);
        $fechamax_nebus = $rows[0];

       $query =  " SELECT COUNT(".$wmovhos."_000015.Aplcan) as nebus "
                    ."   FROM ".$wmovhos."_000091, ".$wmovhos."_000015"
                    ."  WHERE Aplart = Arscod "
                    ."    AND Arstip = 'N' "
                    ."    AND Aplhis = '".$whis."'"
                    ."    AND Apling = '".$wing."'"
                    ."    AND Aplcco = '" .$wcco."' "
                    ."    AND Arscco = Aplcco "
                    ."    AND Aplest = 'on'"
                    ."    AND CONCAT( ".$wmovhos."_000015.Fecha_data, ' ', ".$wmovhos."_000015.Hora_data ) > '".$fechamax_nebus."'";
        $res = mysql_query($query, $conex);
        $rows = mysql_fetch_array($res);
        $wnebulizaciones = $rows['nebus'];

        return $wnebulizaciones;

    }

    // FUNCION PARA MOSTRAR LOS PENDIENTES POR GRABAR PARA INSUMOS //12 DIC 2011 Jonatan Lopez
function traer_insumos($wmovhos, $whis, $wing, $wemp_pmla)
        {

        global $conex;
        global $whce;
        $wcant_insumos = 0;

        $wforminsumos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'FormInsumos'); //Extrae el nombre del formulario para extraer los valores a cobrar.
        $wconfinsumos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ConfInsumos'); //Extrae el arreglo con dos numeros, el primero sirve para mostrar el nombre del
                                                                                        //articulo de la tabla hce_000002 y el segundo sirve para extraer la cantidad
                                                                                        //del campo movdat de la tabla hce_000205.
        $wcampos_desc = explode(";", $wconfinsumos); // Se explota el registro que esta separado por comas, para luego usarlo en el ciclo siguiente.
        $wcuantos = count($wcampos_desc);

        // CONSULTA PARA EXTRAER LA FECHA MAXIMA DE LAS GLUCOMETRIAS GUARD PARA UNA HIST E INGRESO
        $query =     "  SELECT MAX( CONCAT( Fecha_data, ' ', Hora_data ) ) AS FechaHora "
                        ."FROM ".$wmovhos."_000119 "
                        ."WHERE Glnhis = '".$whis."'
                            AND Glning = '".$wing."'
                            AND Glnind = 'I'";
        $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ". $query ." - ".mysql_error());
        $rows = mysql_fetch_array($res);
        $fechamax_insumos = $rows[0];

        for($i = 0; $i <= ($wcuantos-1); $i++)
        {

            $wnombres_posicion = explode("-", $wcampos_desc[$i]); //Esta posicion se refiere a la cantidad y nombre del insumo, en el formulario 000205 de hce.

            //CANTIDAD DE INSUMOS SIN GUARDAR
            $query =    "SELECT SUM(".$whce."_".$wforminsumos.".movdat)  "
                        ."FROM ".$whce."_000036, ".$whce."_".$wforminsumos
                        ." WHERE Firhis = '".$whis."'"
                        ."  AND Firing = '".$wing."'"
                        ."  AND Firhis = Movhis"
                        ."  AND Firing = Moving"
                        ."  AND Firpro = '".$wforminsumos."'"
                        ."  AND Firfir = 'on'"
                        ."  AND ".$whce."_000036.Fecha_data = ".$whce."_".$wforminsumos.".Fecha_data"
                        ."  AND ".$whce."_000036.Hora_data = ".$whce."_".$wforminsumos.".Hora_data"
                        ."  AND movcon = '".$wnombres_posicion[1]."'" //Esta posicion se refiere a la cantidad, en el formulario 000205 de hce.
                        ."  AND CONCAT( ".$whce."_000036.Fecha_data, ' ', ".$whce."_000036.Hora_data ) > '".$fechamax_insumos."'";
        $res= mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
        $rows = mysql_fetch_array($res);
        $wcant_insumos += $rows[0];

        }

        return $wcant_insumos;

        }


// FUNCION PARA MOSTRAR LOS PENDIENTES POR GRABAR PARA LOS SOPORTES RESPIRATORIOS //Diciembre 12/2012 Jonatan Lopez
function traer_oximetrias($wmovhos, $whis, $wing, $wemp_pmla)
        {

        global $conex;
        global $whce;

        $woximetrias = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Soporterespiratorio');
        $wcampos = explode("-", $woximetrias);
        $wtablat = $wcampos[0];
        $wcampot = $wcampos[1];

        // CONSULTA PARA EXTRAER LA FECHA MAXIMA DE LAS TRANSFUSIONES GUARD PARA UNA HIST E INGRESO
        $query =     "SELECT MAX(CONCAT( Fecha_data, ' ', Hora_data ) ) AS FechaHora "
                        ."FROM ".$wmovhos."_000119 "
                        ."WHERE Glnhis = '".$whis."'
							AND Glning = '".$wing."'
							AND Glnind = 'O'";
        $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ". $query ." - ".mysql_error());
        $rows = mysql_fetch_array($res);
        $fechamax_transf = $rows[0];

        //CANTIDAD DE OXIMETRIAS SIN GUARDAR
      $query =    "SELECT SUM(".$whce."_".$wtablat.".movdat)  "
                    ."FROM ".$whce."_000036, ".$whce."_".$wtablat
                    ." WHERE Firhis = '".$whis."'"
                    ."  AND Firing = '".$wing."'"
                    ."  AND Firhis = Movhis"
                    ."  AND Firing = Moving"
                    ."  AND Firpro = '".$wtablat."'"
                    ."  AND Firfir = 'on'"
                    ."  AND ".$whce."_000036.Fecha_data = ".$whce."_".$wtablat.".Fecha_data"
                    ."  AND ".$whce."_000036.Hora_data = ".$whce."_".$wtablat.".Hora_data"
                    ."  AND movcon = '".$wcampot."'"
                    ."  AND CONCAT( ".$whce."_000036.Fecha_data, ' ', ".$whce."_000036.Hora_data ) > '".$fechamax_transf."'";
        $res= mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
        $rows = mysql_fetch_array($res);
        $cantidadoxi = $rows[0];

        if ($cantidadoxi == '')
        {
            $cantidadoxi = 0;
        }

        return $cantidadoxi;

        }

// FUNCION PARA MOSTRAR LOS PENDIENTES POR GRABAR PARA LAS TRANSFUSIONES //Diciembre 12/2012 Jonatan Lopez
function traer_transfusiones($wmovhos, $whis, $wing, $wemp_pmla)
        {

        global $conex;
        global $whce;

        $wtransfusiones = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Transfusiones');
        $wcampos = explode("-", $wtransfusiones);
        $wtablat = $wcampos[0];
        $wcampot = $wcampos[1];

        // CONSULTA PARA EXTRAER LA FECHA MAXIMA DE LAS TRANSFUSIONES GUARD PARA UNA HIST E INGRESO
        $query =    "SELECT MAX(CONCAT( Fecha_data, ' ', Hora_data ) ) AS FechaHora "
                     ."FROM ".$wmovhos."_000119 "
                    ."WHERE Glnhis = '".$whis."'
                        AND Glning = '".$wing."'
                        AND Glnind = 'T'";
        $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ". $query ." - ".mysql_error());
        $rows = mysql_fetch_array($res);
        $fechamax_transf = $rows[0];


        //CANTIDAD DE TRANSFUSIONES SIN GUARDAR
        $query =    "SELECT COUNT(DISTINCT (movdat))  "
                     ."FROM ".$whce."_000036, ".$whce."_".$wtablat
                   ." WHERE Firhis = '".$whis."'"
                    ."  AND Firing = '".$wing."'"
                    ."  AND Firhis = Movhis"
                    ."  AND Firing = Moving"
                    ."  AND Firpro = '".$wtablat."'"
                    ."  AND Firfir = 'on'"
                    ."  AND ".$whce."_000036.Fecha_data = ".$whce."_".$wtablat.".Fecha_data"
                    ."  AND ".$whce."_000036.Hora_data = ".$whce."_".$wtablat.".Hora_data"
                    ."  AND movcon = '".$wcampot."'"
                    ."  AND CONCAT( ".$whce."_000036.Fecha_data, ' ', ".$whce."_000036.Hora_data ) > '".$fechamax_transf."'";
        $res= mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
        $rows = mysql_fetch_array($res);
        $cantidadtransf = $rows[0];
        return $cantidadtransf;

        }

//Esta funcion valida si el paciente tiene medicamentos pendientes.
function validar_medins($conex,$whis,$wnin) //12 DIC 2011 Jonatan Lopez
    {
    global $wBaseDato;
    global $conex_o;
    global $bd;

    $pac=array();
    $pac['his']=$whis;
    $pac['ing']=$wnin;
    $pac['permisoAlta']=false;
    $array=array();
    $conex_o=0;
    $bd=$wBaseDato;
    connectOdbc($conex_o, "inventarios");
    actualizacionDetalleRegistros ($pac, $array);
	
	$suma = 0;

    $query = "select sum((spamen + spauen ) - (spamsa + spausa )) ";
    $query .= " from ".$wBaseDato."_000004 ";
    $query .= " where spahis = '".$whis."'";
    $query .= "   and spaing = '".$wnin."'";
    $err = mysql_query($query,$conex);
    $nums = mysql_num_rows($err);

	if ($nums > 0)
    {
       $row = mysql_fetch_array($err);
       $suma = round( $row[0], 3 );        //Abril 8 de 2013
    }


    if($suma < 0.0001)
		$suma = 0;

	$suma2 = 0;

	$query = "select sum( carcca - carcap - carcde ) ";
    $query .= " from ".$wBaseDato."_000227 ";
    $query .= " where carhis = '".$whis."'";
    $query .= "   and caring = '".$wnin."'";
    $query .= "   and cartra = 'on'";
    $query .= "   and carest = 'on'";
    $err2 = mysql_query($query,$conex);
    $nums2 = mysql_num_rows($err2);

	if ($nums2 > 0)
    {
       $row = mysql_fetch_array($err2);
       $suma2 = round( $row[0], 3 );        //Abril 8 de 2013
    }

    if($suma2 < 0.0001)
		$suma2 = 0;


    $query  = "select inghis, inging ";
    $query .= "  from ".$wBaseDato."_000016,".$wBaseDato."_000018 ";
    $query .= " where inghis = '".$whis."'";
    $query .= "   and inging = '".$wnin."'";
    $query .= "   and inghis = ubihis";
    $query .= "   and inging = ubiing  ";
    $err = mysql_query($query,$conex);
    $num = mysql_num_rows($err);

    $alta="false";

    if($pac['permisoAlta'])
        $alta="true";

    if($pac['permisoAlta'] and $num > 0 and $nums > 0 and $suma == 0 and $suma2 == 0)
        {
            $validar_medins=0;
        }
        else
        {
            $validar_medins=1;

        }

	liberarConexionOdbc($conex_o);
	odbc_close_all();

    return $validar_medins;

    }

//Funcion que cambia el estado de los pacientes de alta en proceso a alta definitiva si no tiene
//medicamentos pendientes. //12 DIC 2011 Jonatan Lopez
function pasaraltaenprocesoaaltadefinitiva($whistoria, $wingreso, $wcco, $whab)
    {

      global $conex;
      global $wemp_pmla;
      global $wBaseDato;
	  global $wbasedato;
      $wcontrol = '';
      $wglucometrias_msg = '';
      $winsumos_msg = '';
      $wnebulizaciones_msg = '';
      $woximetrias_msg = '';
      $wtrasfusiones_msg = '';
      $wpendientes = '';
      $wfecha=date("Y-m-d");
      $whora =(string)date("H:i:s");

      //Verifica si hay medicamentos o insumos pendientes para el paciente.
      $resultado=@validar_medins($conex,$whistoria,$wingreso);

      switch ($resultado)
            {
                case 0:
                        $wcontrolmedicamentos = 'sinpendientes';
                break;

                case 1:

                        $wcontrolmedicamentos = 'pendientes';
                break;

            default :
                break;
            }

        //Validaciones para cada uno de los pendientes por facturar que se encuentran en la entrega de turnos secretaria.
        $wglucometrias = traer_glucometer($wBaseDato, $whistoria, $wingreso, $wemp_pmla); //Trae las glucometrias pendientes por facturar.
        $winsumos = traer_insumos($wBaseDato, $whistoria, $wingreso, $wemp_pmla); //Trae los insumos pendientes por facturar.
        $wnebulizaciones = traer_nebulizaciones($wBaseDato, $whistoria, $wingreso, $wemp_pmla, $wcco); //Trae las nebulizaciones pendientes por facturar.
        $woximetrias = traer_oximetrias($wBaseDato, $whistoria, $wingreso, $wemp_pmla); //Trae los oxigenos pendientes por facturar.
        $wtrasfusiones = traer_transfusiones($wBaseDato, $whistoria, $wingreso, $wemp_pmla); //Trae las transfusiones pendientes por facturar.
		$wevoluciones = traer_evoluciones($wBaseDato, $whistoria, $wingreso, $wemp_pmla);//Trae las evoluciones pendientes por registrar en unix.

        if ($wglucometrias > 0 )
        {
            $wglucometrias_msg = "-GLUCOMETRIAS";
            $wcontrol = 'G';
        }

        if ($winsumos > 0 )
        {

            $winsumos_msg = "-INSUMOS";
            $wcontrol = 'I';

        }

        if ($wnebulizaciones > 0 )
        {
            $wnebulizaciones_msg = "-NEBULIZACIONES";
            $wcontrol = 'N';
        }

        if($woximetrias > 0 )
        {
            $woximetrias_msg = "-OXIGENOS";
            $wcontrol = 'O';
        }

        if ($wtrasfusiones > 0)
        {
            $wtrasfusiones_msg = "-TRANSFUSIONES";
            $wcontrol = 'T';
        }

		if ($wevoluciones > 0)
        {
            $wevoluciones_msg = "-EVOLUCIONES";
            $wcontrol = 'E';
        }

      if ($wcontrolmedicamentos == 'sinpendientes')
            {
                if ($wcontrol != '')
                {
                    $wpendientes .= "Tiene pendientes:".$wglucometrias_msg.$winsumos_msg.$wnebulizaciones_msg.$woximetrias_msg.$wtrasfusiones_msg.$wevoluciones_msg;
                }
                $wpendientes .= '';
                $wpendientes .= ' Fue dado de alta automaticamente por el sistema desde el programa cierre de camas.';

                //Verificar si el registro ya existe
                $query = "SELECT cuehis, cueing ";
                $query .= " FROM ".$wBaseDato."_000022 ";
                $query .= "WHERE cuehis = '".$whistoria."'";
                $query .= "  AND cueing = '".$wingreso."'";
                $err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
                $num = mysql_num_rows($err);

               if ($num == 0 ) //aqui se evalua si el registro ya existe en la tabla 22 de movhos, si ya existe no permite realizar mas inserciones
                {
                $query = "insert ".$wBaseDato."_000022 (medico,fecha_data,hora_data, Cuehis, Cueing, Cuefac, Cuegen, Cuepag, Cuefpa, Cuehpa, Cuecok, Cueobs, Cueffa, Cuehfa, Cuepgr, Seguridad) values ('";
                $query .=  $wBaseDato."','";
                $query .=  $wfecha."','";
                $query .=  $whora."','";
                $query .=  $whistoria."','";
                $query .=  $wingreso."',";
                $query .=  "'.','off','off','0000-00-00','00:00:00','on','".$wpendientes."','0000-00-00','00:00:00','off'";
                $query .=  ",'C-".$wBaseDato."')";
                $err1 = mysql_query($query,$conex) or die("ARCHIVO DE CUENTAS A CAJA : ".mysql_errno().":".mysql_error());
                }
                //Se actualiza a alta definitiva la historia e ingreso.
                $sql = " UPDATE {$wBaseDato}_000018
                            SET  ubiald = 'on',
                                 ubiuad = '".$wBaseDato."',
                                 ubifad = '".$wfecha."',
                                 ubihad = '".$whora."'
                          WHERE  ubihis = '".$whistoria."'
                            AND  ubiing = '".$wingreso."'";
                $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

                //Se limpia el registro de la tabla 20 de movhos con respecto a la historia e ingreso.
                 $q1 = " UPDATE ".$wBaseDato."_000020 "
                             ."    SET Habali = 'on', "
                             ."        Habdis = 'off', "
                             ."        Habhis = '', "
                             ."        Habing = '', "
                             ."        Habfal = '".$wfecha."', "
                             ."        Habhal = '".$whora."'"
                             ."  WHERE Habhis = '".$whistoria."'"
                             ."    AND Habing = '".$wingreso."'"
                             ."    AND Habcod = '".$whab."'";
                 $err = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());

				 $wmuerte = verficarmuerte($whistoria, $wingreso, $wBaseDato); // Verifica si el paciente tiene muerte activa, en este caso no guardara nada en la 33 de movhos.

                // Aca calculo los dias de estancia en el servicio  ************************
                $q_est =  " SELECT ROUND(TIMESTAMPDIFF(MINUTE,CONCAT( Fecha_ing, ' ', Hora_ing ),now())/(24*60),2) "
                    . "   FROM ".$wBaseDato."_000032 	"
                    . "  WHERE Historia_clinica = '".$whistoria."'"
                    . "    AND Num_ingreso      = '".$wingreso."'"
                    . "    AND Servicio         = '".$wcco."'"
                    ."GROUP BY Num_ing_Serv DESC";
                $err_est = mysql_query($q_est, $conex) or die (mysql_errno() . $q_est . " - " . mysql_error());
                $row_est = mysql_fetch_array($err_est);
                $wdiastan = $row_est[0];

                if ($wdiastan == "" or $wdiastan == 0)
                   $wdiastan = 0;

                // Si el paciente a estado antes en el servicio para el mismo ingreso, traigo cuantas veces para sumarle una
                $q_ing = " SELECT COUNT(*) "
                    ."   FROM ".$wBaseDato."_000032 "
                    ."  WHERE Historia_clinica = '".$whistoria."'"
                    ."    AND Num_ingreso      = '".$wingreso."'"
                    ."    AND Servicio         = '".$wcco."'";
                $err_ing = mysql_query($q_ing, $conex) or die (mysql_errno().$q_ing." - ".mysql_error());
                $row_ing = mysql_fetch_array($err_ing);
                $wingser = $row_ing[0] + 1; //Sumo un ingreso a lo que traigo el query


                if ($wmuerte != 'on'){

						@BorrarAltasMuertesAntesDeAgregarNueva($conex, $wBaseDato, $whistoria, $wingreso, 'Nueva alta');

                        $q =  " INSERT INTO ".$wBaseDato."_000033(   Medico       ,   Fecha_data,   Hora_data,   Historia_clinica,   Num_ingreso,   Servicio ,  Num_ing_Serv,   Fecha_Egre_Serv ,   Hora_egr_Serv ,   Tipo_Egre_Serv ,  Dias_estan_Serv, Seguridad     ) "
                            . "                            VALUES('".$wBaseDato."','".$wfecha."','".$whora."','".$whistoria."'   ,'".$wingreso."'   ,'".$wcco."' ,".$wingser."  ,'".$wfecha."'      ,'".$whora."'     ,'ALTA',".$wdiastan."    , 'C-" . $wBaseDato . "')";
                        $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

						//Registro en central de habitaciones
						$q_ali = " INSERT INTO ".$wBaseDato."_000025 (medico,fecha_data,hora_data, movhab, movfal, movhal, Seguridad) "
                                               ." VALUES ('".$wBaseDato."','".$wfecha."','".$whora."','".$whab."','".$wfecha."','".$whora."','C-".$wBaseDato."') ";
						$err = mysql_query($q_ali,$conex) or die (mysql_errno().$q_ali." - ".mysql_error());

						cancelar_pedido_alimentacion($whistoria, $wingreso, $wcco, 'Cancelar', 'movhos');

						//Temporal
						$qqq = " INSERT INTO log_agenda (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
									  VALUES ('".date('Y-m-d')."', '".date('H:i:s')."', '".$whistoria."', '".$wingreso."', 'ALTA DEFINITIVA SIN MEDICAMENTOS PENDIENTES', 'XMOVHOS', '', '')";
						$resl2 = mysql_query($qqq, $conex);

                }
            }
    }

	// separando cada campo por el caracter |
	function obtenerRegistrosFila($qlog)
	{
		global $conex;

		$reslog = mysql_query($qlog, $conex);
		$rowlog = mysql_fetch_row($reslog);
		$datosFila = implode("|", $rowlog);
		return $datosFila;
	}

	//ANTES DE INSERTAR UNA ALTA O UNA MUERTE PARA UN PACIENTE SE CONSULTA SI YA TUVO ALTA O MUERTE Y SE ELIMINAN
	function BorrarAltasMuertesAntesDeAgregarNueva($conex, $wbasedato, $whis, $wing, $bandera)
{

	$user_session = explode('-',$_SESSION['user']);
	$seguridad = $user_session[1];

	if( !isset( $bandera ) ){
		$bandera = "";
	}

	$q = "    SELECT *
			FROM ".$wbasedato."_000033
			WHERE Historia_clinica = '".$whis."'
			AND Num_ingreso = '".$wing."'
			AND Tipo_egre_serv REGEXP 'MUERTE MAYOR A 48 HORAS|MUERTE MENOR A 48 HORAS|ALTA' ";

	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);

	$arregloDatos = array();

	if ($num > 0)
	{
		while($row = mysql_fetch_assoc($res))
		{
			$result = array();
			$result['fecha'] = $row['Fecha_data'];
			$result['cco'] = $row['Servicio'];
			$result['egreso'] = $row['Tipo_egre_serv'];
			array_push( $arregloDatos, $result );
		}
	}

	if( count( $arregloDatos )  > 0 )
	{
		foreach( $arregloDatos as $dato )
		{

			$wfecha = $dato['fecha'];
			$wcco = $dato['cco'];
			$wtipoEgresoABorrar = $dato['egreso'];

			$q = " SELECT * "
				."   FROM ".$wbasedato."_000038 "
				."  WHERE Fecha_data = '".$wfecha."'"
				."    AND Cieser = '".$wcco."'";

			$res = mysql_query($q,$conex);
			$num = mysql_num_rows($res);
			$row = mysql_fetch_assoc($res);


			$existe_en_la_67 = false;
			$q67 = " SELECT * "
				."   FROM ".$wbasedato."_000067 "
				."  WHERE Fecha_data = '".$wfecha."'"
				."    AND Habhis = '".$whis."'"
				."    AND Habing = '".$wing."'";

			$res67 = mysql_query($q67,$conex);
			$num67 = mysql_num_rows($res67);
			if( $num67 > 0 ){
				$existe_en_la_67 = true;
			}

			$cant_egresos = $row['Cieegr'];
			$cant_camas_ocupadas = $row['Cieocu'];
			$cant_camas_disponibles = $row['Ciedis'];
			$muerteMayor = $row['Ciemmay'];
			$muerteMenor = $row['Ciemmen'];
			$egresosAlta = $row['Cieeal'];
			//Restamos uno al motivo de egreso que tenia el paciente

			if(preg_match('/ALTA/i',$wtipoEgresoABorrar))
			{
				$egresosAlta--;
				$cant_egresos--;
				if( $existe_en_la_67 === false )
				{
					$cant_camas_ocupadas++;
					$cant_camas_disponibles--;
				}
			}
			else if(preg_match('/MAYOR/i',$wtipoEgresoABorrar)) //Muerte mayor
			{
				$muerteMayor--;
				$cant_egresos--;
				if( $existe_en_la_67 === false )
				{
					$cant_camas_ocupadas++;
					$cant_camas_disponibles--;
				}
			}
			else if(preg_match('/MENOR/i',$wtipoEgresoABorrar))
			{ // Muerte menor
				$muerteMenor--;
				$cant_egresos--;
				if( $existe_en_la_67 === false )
				{
					$cant_camas_ocupadas++;
					$cant_camas_disponibles--;
				}
			}

			$query_para_log = "    SELECT *
				FROM ".$wbasedato."_000033
				WHERE Historia_clinica = '".$whis."'
				AND Num_ingreso = '".$wing."'
				AND Tipo_egre_serv = '".$wtipoEgresoABorrar."'";
			$registrosFila = @obtenerRegistrosFila($query_para_log);

			$q ="    DELETE FROM ".$wbasedato."_000033
					 WHERE Historia_clinica = '".$whis."'
					   AND Num_ingreso = '".$wing."'
					   AND Tipo_egre_serv = '".$wtipoEgresoABorrar."'";
			$res = mysql_query($q,$conex);

			$num_affect = mysql_affected_rows();
			if($num_affect>0)
			{
				$q = "   UPDATE ".$wbasedato."_000038 "
					."  SET Ciemmay = '".$muerteMayor."',"
					."      Ciemmen = '".$muerteMenor."',"
					."      Cieeal = '".$egresosAlta."',"
					."      Cieegr = '".$cant_egresos."',"
					."      Cieocu = '".$cant_camas_ocupadas."',"
					."      Ciedis = '".$cant_camas_disponibles."'"
					." WHERE Fecha_data = '".$wfecha."'"
					."  AND Cieser = '".$wcco."'"
					." LIMIT 1 ";

				$res = mysql_query($q,$conex);

				//Guardo LOG de borrado en tabla movhos 33 - Activacion paciente
				$q = "    INSERT INTO log_agenda
										  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
								   VALUES
										  ('".date('Y-m-d')."', '".date('H:i:s')."', '".$whis."', '".$wing."', 'Borrado tabla movhos_000033', '".$seguridad."', '".$bandera."', '".$registrosFila."')";
				$resl2 = mysql_query($q, $conex);
			}
		}
	}
}


	function redondear_dos_decimal($valor) {
	   $float_redondeado=round($valor * 100) / 100;
	   return $float_redondeado;
	}

	echo "<form action='cierre_camas.php' method=post>";
	echo "<center><table>";
	echo "<tr><td align=center colspan=2><IMG SRC='/matrix/images/medical/movhos/logo_movhos.png'></td></tr>";
	echo "<tr><td align=center bgcolor=#999999 colspan=2><font size=6 face='tahoma'><b>CIERRE DIARIO DE CAMAS</font></b></font></td></tr>";
	echo "</table>";

	//ESPERAR A EJECUTAR HASTA EL MINUTO 59 SEGUNDO 50
	// Hora actual
	$hora_actual = date('h:i:s');
	$explo = explode(":", $hora_actual);
	$diff = 50 - $explo[2];

	if( $diff > 0 && !isset($wfecha))
	{
		// esperar $diff segundos
		usleep( $diff * 1000000 );
	}
	//FIN DE LA ESPERA
	echo "Ultima actualizacion: ".$wactualizacion;
	echo "<BR>EJECUTO A LAS : ".date('h:i:s') . "<br>";

	if(!isset($wfecha))
	{
		$wfecha = date("Y-m-d");
	}else{
		
		$wfecha = strtoupper($wfecha);
		if ($wfecha == "AYER")
		{
			$wfecha = date('Y-m-d',strtotime("-1 days"));
		}
		echo "<BR>Se procesará la fecha por parametro: ".$wfecha. "<br>";
	}
	$whora = (string)date("H:i:s");
    $wusuario = 'movhos';
    $horaEjecucion = (string)date("H");
    $minutosEjecucion = (string)date("i");
	
    //echo $horaEjecucion;
    //echo $minutosEjecucion;

    $debug = false;

    if(($horaEjecucion == '23' && $minutosEjecucion == '59') || ($horaEjecucion == '00' && $minutosEjecucion == '59') || $debug || isset($wfecha))
	{
		//Temporal
		if($horaEjecucion == '23' && $minutosEjecucion == '59'){
			$q = "    INSERT INTO log_agenda
					(Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
					VALUES
					('".date('Y-m-d')."', '".date('H:i:s')."', '123', '1', 'EJECUTO A LAS 11PM', 'XMOVHOS', 'PRUEBA', 'EJECUTO A LAS 11PM escribir archivo')";
					$resl2 = mysql_query($q, $conex);
		}

    	//Creacion de un archivo plano para tomar una imagen de la informacion de las camas en ese momento
    	$nombreArchivo = "camasOcupadas.dat";

    	//Apuntador en modo de adicion si no existe el archivo se intenta crear...
    	$archivo = fopen($nombreArchivo, "a");
    	if(!$archivo)
		{
    		$archivo = fopen($nombreArchivo, "w");
    	}

    	if($archivo)
		{
    		//Consulta de la informacion de camas ocupadas en este instante
    		$q = " SELECT Habcod, Habcco, Habhis, Habing, Habali, Habdis, Habest, habpro, habfal, habhal, habprg "
				." FROM ".$wBaseDato."_000020 "
				." WHERE habest = 'on' "
				." AND habali != 'on' "
				." AND habdis != 'on' ";
    		$resultSet = mysql_query($q,$conex);

    		$contenido = "HAB ; CCO ; HIS ; ING ; ALI ; DIS ; EST ; PRO ; FAL ; HAL ; PRG ; ..::::$wfecha::::..$horaEjecucion:$minutosEjecucion \r\n";
    		for ($i=1;$i<=mysql_num_rows($resultSet);$i++)
    		{
    			$row3 = mysql_fetch_array($resultSet);
    			$contenido = $contenido.$row3[0].";".$row3[1].";".$row3[2].";".$row3[3].";".$row3[4].";".$row3[5].";".$row3[6].";".$row3[7].";".$row3[8].";".$row3[9].";".$row3[10]." \r\n";
    		}

    		// Asegurarse primero de que el archivo existe y puede escribirse sobre él.
    		if (is_writable($nombreArchivo))
			{

    			// En nuestro ejemplo estamos abriendo $nombreArchivo en modo de adición.
    			// El apuntador de archivo se encuentra al final del archivo, así que
    			// allí es donde irá $contenido cuando llamemos fwrite().
    			if (!$archivo)
				{
    				echo "No se puede abrir el archivo ($nombreArchivo)";
    				//    			exit;
    			}

    			// Escribir $contenido a nuestro arcivo abierto.
    			if (fwrite($archivo, $contenido) === FALSE)
				{
    				echo "No se puede escribir al archivo ($nombreArchivo)";
    				//    			exit;
    			}

    			echo "Éxito, se escribió ($contenido) al archivo ($nombreArchivo)";

    			fclose($archivo);

    		}
			else
			{
    			echo "No se puede escribir sobre el archivo $nombreArchivo";
    		}
    	}
    }


    //Esta seccion actualiza los pacientes de alta en proceso a alta definitiva, en caso de tener saldos pendientes no le da alta definitiva,
    //si esta marcada la muerte y el paciente no tiene saldos pendientes le dara alta definitiva.
     if(($horaEjecucion == '23' && $minutosEjecucion == '59') || $debug)
    {
		//Temporal
		$q = "    INSERT INTO log_agenda
				(Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
				VALUES
				('".date('Y-m-d')."', '".date('H:i:s')."', '123', '1', 'EJECUTO A LAS 11:59 PM', 'XMOVHOS', 'PRUEBA', '2 EJECUTO A LAS 11:59 PM')";
				$resl2 = mysql_query($q, $conex);
        //===================================================================================================================================================
        // ACA TRAIGO LOS PACIENTES QUE TIENEN MUERTE //17 DE DICIEMBRE JONATAN LOPEZ
        //===================================================================================================================================================
     $q_muerte = "  SELECT ubihis, ubiing, ubisac, ubihac "
                ."    FROM ".$wBaseDato."_000018, root_000036, root_000037, ".$wBaseDato."_000011 "
                ."   WHERE ubihis = orihis "
                ."     AND Ubiing = Oriing "
                ."     AND oriori  = '".$wemp_pmla."'"
                ."     AND oriced  = pacced "
                ."     AND oritid  = pactid "
                ."     AND ubisac  = ccocod "
                ."     AND ccohos  = 'on' "
                ."     AND ubimue  = 'on' "
                ."     AND ccourg != 'on' "
                ."     AND ubiald != 'on' "
                ."     AND ubiptr != 'on' "
                ."   ORDER BY 2 ";
        $res_muerte = mysql_query($q_muerte,$conex);

        while($row_muerte = mysql_fetch_assoc($res_muerte))
        {
			//Esta funcion cambia los pacientes de proceso de alta a alta definitiva, si la historia tiene no tiene medicamentos pendientes.
			@pasaraltaenprocesoaaltadefinitiva($row_muerte['ubihis'], $row_muerte['ubiing'], $row_muerte['ubisac'], $row_muerte['ubihac']);
        }


        //===================================================================================================================================================
        // ACA TRAIGO LAS HABITACIONES QUE ESTEN EN PROCESO DE ALTA PARA CAMBIARLAS A A ALTA DEFINITIVA SI NO TIENEN MEDICAMENTOS PENDIENTES //12 DIC 2011 Jonatan Lopez
        //===================================================================================================================================================
       $q_alta = "  SELECT Habcco, Habcod, Habhis, Habing, pacno1, pacno2, pacap1, pacap2 "
                ."    FROM ".$wBaseDato."_000018, ".$wBaseDato."_000020, root_000036, root_000037, ".$wBaseDato."_000011 "
                ."   WHERE habest  = 'on' "
                ."     AND habhis  = ubihis "
                ."     AND habing  = ubiing "
                ."     AND habhis  = orihis "
                ."     AND oriori  = '".$wemp_pmla."'"
                ."     AND oriced  = pacced "
                ."     AND oritid  = pactid "
                ."     AND habcco  = ccocod "
                ."     AND ccohos  = 'on' "
                ."     AND ccourg != 'on' "
                ."     AND ubialp  = 'on' "
                ."     AND ubiald != 'on' "
                ."   ORDER BY 2 ";
        $res_alta = mysql_query($q_alta,$conex);

        while($row_alta = mysql_fetch_assoc($res_alta))
        {
			//Esta funcion cambia los pacientes de proceso de alta a alta definitiva, si la historia tiene no tiene medicamentos pendientes.
			@pasaraltaenprocesoaaltadefinitiva($row_alta['Habhis'], $row_alta['Habing'], $row_alta['Habcco'], $row_alta['Habcod']);
        }

    }

    //Por si ya habia generado el cierre, borro lo que exista en la fecha
	$q = " DELETE FROM ".$wBaseDato."_000038 "
     ." WHERE fecha_data = '".$wfecha."'";
    $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	 $q = " SELECT habcco, COUNT(*) "
	    ." FROM ".$wBaseDato."_000020 "
	    ." WHERE habest = 'on' "
	    ." GROUP BY 1 ";

	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		//En cada posicion hay un arreglo asociativo con cuatro claves:  historia, ingreso, cco, hab
		$pacientes_limpiar = array(); //Contiene los pacientes que se deben quitar del piso-habitacion porque no estan fisicamente ahi, estan en proceso de traslado
		$pacientes_agregar = array(); //Contiene los pacientes que se deben agregar al piso-habitacion porque no han llegado al piso destino, estan en proceso de traslado
        //Este for recorre todas las habitaciones
		for ($i=1;$i<=$num;$i++)
		{
			 $row = mysql_fetch_array($res);

			 //Camas Disponibles por Servicio
			 $q = " SELECT  COUNT(*) "
	             ."   FROM   ".$wBaseDato."_000020 "
	             ."  WHERE  habest = 'on' "
				 ."    AND  habhis = ''"
	             //."  AND (habali = 'on' " //16-nov-2012
	             //."   OR  habdis = 'on') " //16-nov-2012
	             ."    AND  habcco = '".$row[0]."'";
	         $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			 $row1 = mysql_fetch_array($res1);
			 $wdis = 0;
			 if($row1 && $row1[0]>0)
				$wdis=$row1[0];    //Camas Desocupadas o Disponibles

			//Camas Ocupadas por Servicio
			$wocu = 0;

			//Quienes ESTAN DESTINADOS al piso y estan en proceso de traslado, no cuentan como ocupado
			$q =  " SELECT  Habcco, Habcod, Ubihis, Ubiing, Ubiptr as proceso_traslado"
				 ."   FROM  ".$wBaseDato."_000020, ".$wBaseDato."_000018 "
				 ."  WHERE  Ubihis = Habhis "
				 ."    AND  Ubiing = Habing "
				 ."	   AND  Ubisac = Habcco "
				 ."    AND  Habcco = '".$row[0]."'"
				 ."    AND  Ubiald != 'on' "
				 ."    AND	Habest = 'on' "
				 ."    AND  Habhis != '' ";

			$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

			//Comprobar que no este en proceso de traslado
			while($row_eh = mysql_fetch_assoc($res1))
			{
				if($row_eh && $row_eh['proceso_traslado'] != 'on'){
					$wocu++; //Si no esta en proceso de traslado la habitacion esta ocupada fisicamente
				}else{
					$wdis++; // Si esta en proceso de traslado la habitacion esta planeada para ser ocupada, se cuenta como "disponible" aunque este reservada
					//echo "<br>1. ----->Quitarla del piso:".$row[0]."-habitacion  La historia: ".$row_eh['Ubihis']."  No esta en la hab: ".$row_eh['Habcco']."-".$row_eh['Habcod']." porque estan en proceso de traslado";
					$paciente = array();
					$paciente['his'] = $row_eh['Ubihis'];
					$paciente['ing'] = $row_eh['Ubiing'];
					$paciente['cco'] = $row_eh['Habcco'];
					$paciente['hab'] = $row_eh['Habcod'];
					array_push($pacientes_limpiar, $paciente );

				}
			}


			//23-04-2013  Quienes ESTABAN en el piso y estan en proceso de traslado, cuenta como ocupado
			$q = "	  SELECT  Habcco, Habcod, Ubihis, Ubiing, Ubihan, Ubiptr as proceso_traslado "
				."  	FROM  ".$wBaseDato."_000018, ".$wBaseDato."_000020 "
				."     WHERE  Ubihis = Habhis "
				."       AND  Ubiing = Habing "
				."		 AND  Ubisan='".$row[0]."' "
				."       AND  Ubiald != 'on' "
				."       AND  Ubiptr = 'on'"
				."       AND  Habhis != '' "
				."       AND  Habest = 'on' ";
			$res3 = mysql_query($q,$conex);
			while($row3 = mysql_fetch_assoc($res3))
			{
				//echo "<br>2. ----->Ponerla en el piso:".$row[0]."-habitacion:".$row3['Ubihan']."  La historia: ".$row3['Ubihis']."  No esta en la hab: ".$row3['Habcco']."-".$row3['Habcod']." porque estan en proceso de traslado";
				$wocu++; //Esta en proceso de traslado, la habitacion esta ocupada fisicamente
				$wdis--; //No esta disponible esa habitacion

				$paciente = array();
				$paciente['his'] = $row3['Ubihis'];
				$paciente['ing'] = $row3['Ubiing'];
				$paciente['cco'] = $row[0];
				$paciente['hab'] = $row3['Ubihan'];
				array_push($pacientes_agregar, $paciente );
			}

			/*$row1 = mysql_fetch_array($res1);
			 $wocu = 0;
			 if($row1 && $row1[0]>0)
				$wocu=$row1[0];   //Camas Ocupadas
			*/
			 //Ingresos por Servicio
			 $q = " SELECT COUNT(*) "
	             ."   FROM ".$wBaseDato."_000032, ".$wBaseDato."_000011 "
	             ."  WHERE ".$wBaseDato."_000032.fecha_data = '".$wfecha."'"
	             ."    AND Servicio = '".$row[0]."'"
	             ."    AND Ccocod = Servicio "
	             ."    AND Ccohos = 'on' "
	             ."    AND Servicio != Procedencia ";
	         $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			 $row1 = mysql_fetch_array($res1);

			 $wing = 0;
			 if($row1 && $row1[0]>0)
				$wing = $row1[0];   //Ingresos

			 /*
			  * Egresos y dias de estancia totales
			  */

			$q = " SELECT "
			 	." COUNT(*), IFNULL(SUM(Dias_estan_serv),0) "
			 	." FROM ".$wBaseDato."_000033, ".$wBaseDato."_000011 "
	            ." WHERE ".$wBaseDato."_000033.fecha_data = '".$wfecha."'"
	            ." AND Servicio = '".$row[0]."'"
	            ." AND Ccocod = Servicio "
	            ." AND Ccohos = 'on' "
	            ." AND Servicio != Tipo_egre_serv ";
			 $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			 $row1 = mysql_fetch_array($res1);

			 $wegr = 0;
			 $wdiaest = 0;
			 if($row1 && $row1[0]>0)
				$wegr=$row1[0];   //Cantidad de egresos (MODIFICADO), este valor incluye TODOS los egresos contando altas, muertes mayores y menores
			 if($row1 && $row1[1]>0)
				$wdiaest=redondear_dos_decimal($row1[1]);  //Dias de estancia egresos

			if(trim($row[0])=='1179'){
				 /*
				 * SI EL SERVICIO ES 1179 - MEDICINA NUCLEAR
				 * SOLO DEBE MOSTRAR LOS DE HOSPITALIZACION
				 */
				$wegr = 0;
				$wdiaest = 0;

				 $q = "   SELECT  COUNT(*), IFNULL(SUM(EG.Dias_estan_serv),0)"
						."   FROM  ".$wBaseDato."_000033 EG, ".$wBaseDato."_000018 UB, ".$wBaseDato."_000011 CCO"
						."  WHERE  EG.Fecha_data = '".$wfecha."'"
						."    AND  EG.servicio = '".trim($row[0])."'"
						."    AND  EG.Historia_clinica = UB.Ubihis "
						."    AND  EG.Num_ingreso = UB.Ubiing "
						."    AND  UB.Ubihac != '' "
						."    AND  CCO.Ccocod = EG.Servicio "
						."    AND  CCO.Ccohos = 'on' "
						."    AND  EG.Servicio != EG.Tipo_egre_serv ";

				$res1 = mysql_query($q,$conex);
				$row1 = mysql_fetch_array($res1);

				$wegr = 0;
				$wdiaest = 0;
				if($row1 && $row1[0]>0)
				$wegr=$row1[0];   //Cantidad de egresos (MODIFICADO), este valor incluye TODOS los egresos contando altas, muertes mayores y menores
				if($row1 && $row1[1]>0)
				$wdiaest=redondear_dos_decimal($row1[1]);  //Dias de estancia egresos
				 /*
				 * FIN CALCULANDO PARA 1179
				 */
			}

			 /*
			  * Cantidad de muertes menores a 48 horas
			  */
			 $wmen48 = 0;
			 $diasMen48 = 0;
			 $q2 = " SELECT COUNT(*), IFNULL(SUM(Dias_estan_serv),0) "
				  ." FROM ".$wBaseDato."_000018, ".$wBaseDato."_000033 "
				  ." WHERE ".$wBaseDato."_000033.Fecha_data = '".$wfecha."'"
				  ." AND ubisac = '".trim($row[0])."'"
				  ." AND ubimue = 'on' "
				  ." AND ubihis = historia_clinica "
				  ." AND ubiing = num_ingreso "
				  ." AND Tipo_egre_serv = 'MUERTE MENOR A 48 HORAS' ";

			 $rs = mysql_query($q2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());
			 $num2 = mysql_num_rows($rs);

			 if ($num2 > 0)
			 {
			 	$consulta = mysql_fetch_array($rs);
			 	$wmen48=$consulta[0];
			 	$diasMen48=$consulta[1];
			 }

			 /*
			  * Cantidad de muertes mayores a 48 horas
			  */
			 $wmay48 = 0;
			 $diasMay48 = 0;

			 $q2 = " SELECT COUNT(*), IFNULL(SUM(Dias_estan_serv),0) "
				  ." FROM ".$wBaseDato."_000018, ".$wBaseDato."_000033 "
				  ." WHERE ".$wBaseDato."_000033.Fecha_data = '".$wfecha."'"
				  ." AND ubisac = '".trim($row[0])."'"
				  ." AND ubimue = 'on' "
				  ." AND ubihis = historia_clinica "
				  ." AND ubiing = num_ingreso "
				  ." AND Tipo_egre_serv = 'MUERTE MAYOR A 48 HORAS' ";

			 $rs = mysql_query($q2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());
			 $num2 = mysql_num_rows($rs);

			 if ($num2 > 0)
			 {
			 	$consulta = mysql_fetch_array($rs);
			 	$wmay48=$consulta[0];
			 	$diasMay48=$consulta[1];
			 }

		     /*
			  * Egresos y dias de estancia altas
			  */
			 $diasAlta = 0;
			 $wegal = 0;

			 $q2 = " SELECT IFNULL(SUM(Dias_estan_serv),0), COUNT(*) "
				  ." FROM ".$wBaseDato."_000033 "
				  ." WHERE Fecha_data = '".$wfecha."'"
				  ." AND servicio = '".trim($row[0])."'"
				  ." AND Tipo_egre_serv = 'ALTA' ";

			 $rs = mysql_query($q2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());

			 if (mysql_num_rows($rs) > 0)
			 {
			 	$consulta = mysql_fetch_array($rs);
			 	$diasAlta=redondear_dos_decimal($consulta[0]);
			 	$wegal = $consulta[1];
			 }

			if(trim($row[0])=='1179'){
					 /*
					 * SI EL SERVICIO ES 1179 - MEDICINA NUCLEAR
					 * SOLO DEBE MOSTRAR LOS DE HOSPITALIZACION
					 */
					 $diasAlta = 0;
					 $wegal = 0;

					 $q2 = "   SELECT  IFNULL(SUM(EG.Dias_estan_serv),0), COUNT(*) "
							."   FROM  ".$wBaseDato."_000033 EG, ".$wBaseDato."_000018 UB "
							."  WHERE  EG.Fecha_data = '".$wfecha."'"
							."    AND  EG.servicio = '".trim($row[0])."'"
							."    AND  EG.Tipo_egre_serv = 'ALTA' "
							."    AND  EG.Historia_clinica = UB.Ubihis "
							."    AND  EG.Num_ingreso = UB.Ubiing "
							."    AND  UB.Ubihac != '' ";

					 $rs = mysql_query($q2,$conex);

					 if (mysql_num_rows($rs) > 0)
					 {
						$consulta = mysql_fetch_array($rs);
						$diasAlta=redondear_dos_decimal($consulta[0]);
						$wegal = $consulta[1];
					 }
					 /*
					 * FIN CALCULANDO PARA 1179
					 */
			}


			 /*
			  * Ingresos y egresos del mismo dia
			  */
			 $q = "  SELECT COUNT(*)
					   FROM ".$wBaseDato."_000032 ing, ".$wBaseDato."_000033 egr
					  WHERE	ing.Fecha_data = egr.Fecha_data
						AND egr.Historia_clinica = ing.Historia_clinica
						AND ing.Servicio = egr.Servicio
						AND ing.Fecha_data = '".$wfecha."'
						AND ing.Servicio = '".$row[0]."'";
			 $res1 = mysql_query($q,$conex);
			 $resultSet = mysql_fetch_array($res1);

			 $wiye = 0;
			 if($resultSet && $resultSet[0]>0)
				$wiye = $resultSet[0];

			 /**
			  * Ingresos por urgencias
			  */
			 $ingU = 0;
		     $q = "SELECT COUNT(*)
					 FROM ".$wBaseDato."_000032
					WHERE Fecha_data = '".$wfecha."'
					  AND Procedencia='1130'
					  AND Servicio = '".$row[0]."'";
			 $rs = mysql_query($q,$conex);
			 if(mysql_num_rows($rs) > 0)
			 {
		 		$fila = mysql_fetch_row($rs);

		 		$ingU = $fila[0];
			 }

		    /**
			  * Ingresos por admisiones
			  */
			 $ingA = 0;
		     $q = "SELECT
		     			COUNT(*)
					FROM
						".$wBaseDato."_000032
					WHERE
						Fecha_data = '".$wfecha."'
					    AND Procedencia='1800'
					    AND Servicio = '".$row[0]."'";

			 $rs = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			 if(mysql_num_rows($rs) > 0)
			 {
		 		$fila = mysql_fetch_row($rs);

		 		$ingA = $fila[0];
			 }

		    /**
			  * Ingresos por cirugía
			  */
			 $ingC = 0;
		     $q = "SELECT
		     			COUNT(*)
					FROM
						".$wBaseDato."_000032
					WHERE
						Fecha_data = '".$wfecha."'
					    AND Procedencia='1016'
					    AND Servicio = '".$row[0]."'";

			 $rs = mysql_query($q,$conex);
			 if(mysql_num_rows($rs) > 0)
			 {
		 		$fila = mysql_fetch_row($rs);
		 		$ingC = $fila[0];
			 }

		    /**
			  * Ingresos por traslado
			  */
			 $ingT = 0;
		     $q = "SELECT
		     			COUNT(*)
					FROM
						".$wBaseDato."_000032
					WHERE
						Fecha_data = '".$wfecha."'
					 	AND Procedencia in (SELECT Ccocod
											FROM ".$wBaseDato."_000011
					   						WHERE Ccoest = 'on' AND Ccohos = 'on' AND ( Ccoing != 'on' OR Ccohib = 'on' )
					   						ORDER by 1)
					    AND Servicio = '".$row[0]."'";

			 $rs = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			 if(mysql_num_rows($rs) > 0)
			 {
		 		$fila = mysql_fetch_row($rs);

		 		$ingT = $fila[0];
			 }

			 /*
			  * Egresos por traslado
			  */
		    $egrT = 0;
		    $diasTraslado = 0;

		    $q = "SELECT
		     			COUNT(*), IFNULL(SUM(Dias_estan_serv),0)
					FROM
						".$wBaseDato."_000033
					WHERE
						Fecha_egre_serv = '".$wfecha."'
					 	AND Tipo_egre_serv in (SELECT Ccocod
											FROM ".$wBaseDato."_000011
					   						WHERE Ccoest = 'on' AND Ccohos = 'on' AND ( Ccoing != 'on' OR Ccohib = 'on' )
					   						ORDER by 1)
					    AND Servicio = '".$row[0]."'";

			 $rs = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			 if(mysql_num_rows($rs) > 0)
			 {
		 		$fila = mysql_fetch_row($rs);
		 		$egrT = $fila[0];
		 		$diasTraslado = redondear_dos_decimal($fila[1]);
			 }

			 $q = " INSERT ".$wBaseDato."_000038 ( medico ,   fecha_data,   hora_data,   cieser    ,   ciedis  ,   cieocu  ,   cieing  ,   cieegr  ,   cieiye  ,   ciedes     ,   Ciemmen     ,		Ciemmay, 	Cieinu, Cieinc, Cieina, Cieint, Ciegrt, Ciedit, Ciediam, Cieeal, Seguridad) "

		         ." VALUES ('".$wBaseDato."','".$wfecha."','".$whora."','".$row[0]."','".$wdis."','".$wocu."','".$wing."','".$wegr."','".$wiye."','".$wdiaest."','".$wmen48."','".$wmay48."','".$ingU."', '".$ingC."','".$ingA."','".$ingT."','".$egrT."','".$diasTraslado."','".($diasAlta+$diasMay48+$diasMen48)."','".$wegal."','".$wusuario."')";
			 $res1 = mysql_query($q,$conex) or die("ERROR GRABANDO CIERRE DIARIO DE CAMAS : ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		}

		echo "<br><br><br>";
		echo "<BR>TERMINO A LAS : ".date('h:i:s') . "\n";
		 //Se borra lo que exista en la fecha en la tabla de historial de ocupacion de habitaciones para todos los servicios
		$q = "DELETE FROM ".$wBaseDato."_000067 WHERE Fecha_data = '".$wfecha."'";
    	$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		 //Realiza insert masivo sobre la tabla historial de habitaciones
		 $q = "INSERT INTO ".$wBaseDato."_000067
		 			(Medico,Fecha_data,Hora_data,Habcod,Habcco,Habhis,Habing,Habali,Habdis,Habest,habpro,habfal,habhal,habtmp,habprg,habtip,habtfa,Seguridad)
		 		SELECT
		 			Medico,'$wfecha','$whora',Habcod,Habcco,Habhis,Habing,Habali,Habdis,Habest,habpro,habfal,habhal,habtmp,habprg,habtip,habtfa,Seguridad
		 		FROM
		 			".$wBaseDato."_000020";
		 $res1 = mysql_query($q,$conex) or die("ERROR GRABANDO CIERRE DIARIO DE CAMAS : ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		 //Actualizar la foto de acuerdo a las entrega-recibo que no han sido efectuadas.
		 //Si solo tiene la entrega, la cama ocupada es donde estaba, desocupada la cama destino
		 foreach($pacientes_limpiar as $pos=>$paciente){
			$qq= " UPDATE ".$wBaseDato."_000067 SET Habhis='', Habing='' "
			  ."   WHERE Habhis= '".$paciente['his']."' AND Habing ='".$paciente['ing']."' AND Habcod='".$paciente['hab']."' AND Habcco='".$paciente['cco']."'"
			  ."     AND Fecha_data = '".$wfecha."'";
			$resq = mysql_query($qq,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qq." - ".mysql_error());
		 }
		 foreach($pacientes_agregar as $pos=>$paciente){
			$qq= " UPDATE ".$wBaseDato."_000067 SET Habhis= '".$paciente['his']."', Habing ='".$paciente['ing']."'"
			  ."   WHERE  Habcod='".$paciente['hab']."' AND Habcco='".$paciente['cco']."' "
			  ."     AND Fecha_data = '".$wfecha."'";
			$resq = mysql_query($qq,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qq." - ".mysql_error());
		 }
         echo "<center><table>";
		 echo "<tr><td align=center bgcolor=#999999 colspan=2><font size=6 face='tahoma'><b>TERMINO DE GENERAR EL CIERRE DIARIO DE CAMAS</font></b></font></td></tr>";
		 echo "</table></center>";

		 //Temporal
		if(($horaEjecucion == '23' && $minutosEjecucion == '59') || $debug)
		{
			$q = "    INSERT INTO log_agenda
					(Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
					VALUES
					('".date('Y-m-d')."', '".date('H:i:s')."', '123', '1', '4 EJECUTO A LAS 11PM', 'XMOVHOS', 'PRUEBA', '4 EJECUTO A LAS 11PM')";
					$resl2 = mysql_query($q, $conex);
		}
	}

		liberarConexionBD($conex);

?>
</body>
</html>