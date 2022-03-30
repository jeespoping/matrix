<?php
include_once("conex.php");


 /*     * *******************************************************
     *               REPORTE AUTORIZACIONES PENDIENTES            *
     *     				                   						*
     * ******************************************************* */
//==================================================================================================================================
//PROGRAMA                   : autorizaciones_pendientes5.php
//AUTOR                      : Jonatan Lopez Aguirre.
//FECHA CREACION             : 16 de Julio de 2014
//FECHA ULTIMA ACTUALIZACION :
/*
//DESCRIPCION
//========================================================================================================================================\\
// Programa similar a entrega de turno secretaria, pero solo con las columnas transfusiones, evoluciones y procedimientos.
//========================================================================================================================================\\
//Julio 9 de 2018 Juan Felipe Balcero: Se corrige el botón de ir a órdenes en el caso de que se filtre por todos los centros de costo. Cuando se 
                                        selecciona esta opción, la variable wir_a_ordenes se consulta por cada paciente.
//================================================================================================================================================
//Julio 4 de 2018 Jonatan: Se reestructura el formato con el que se inserta informacion en la bitacora, se elimina la unidad que realiza y
							la fecha de realización.
//========================================================================================================================================\\
//Mayo 31 de 2018 Jonatan: Se hace registro en la bitacora de pacientes cuando se agrega una observacion en algun procedimiento(ordenes).
//========================================================================================================================================\\
//Mayo 24 de 2018 Jonatan: Se corrigen los datos del paciente en el encabezado de los procedimientos y se agrega el codigo de autorizacion
							registrado en la admisión.
//========================================================================================================================================\\
//Abril 5 de 2018 Jonatan:  Se agrega columna de entidades cuando selecciona todas las entidades en el filtro, ademas se cambia el estilo de 
							fondoAmarillo a fondoGris en el cajon de pacientes con mas de 24 horas en alta definitiva, ademas se agrega filtro
							de zonas.
//========================================================================================================================================\\
//Marzo 2 de 2018 Jonatan: Se agrega el filtro de tarifa en la funcion generarQueryCombinado.
//========================================================================================================================================\\
//Septiembre 26 de 2017 Jonatan: Correcion en la consulta que muestra los pacientes con muerte.
//========================================================================================================================================\\
//========================================================================================================================================\\
//Septiembre 22 de 2017 Jonatan: Se agrega el codigo cups en el listado de examanes y procedimientos.
//========================================================================================================================================\\
//Septiembre 1 de 2017 Jonatan: Se corrige el calculo de procedimientos sin autorizacion.
//========================================================================================================================================\\
//Julio 13 de 2017 Jonatan: Correcciones con respecto al cambio de estado de los examenes.
//========================================================================================================================================\\
//Junio 8 de 2017 Jonatan: Se agregan los pacientes que sean marcados con alta definitiva en las ultimas 24 hooras.
//========================================================================================================================================\\
//Junio 6 de 2017 Jonatan: Se marca en verde la orden prioritaria, se muestra a urgencias en el listado de cco y se muestran los pacientes.
//========================================================================================================================================\\
//Mayo 16 de 2017 Jonatan: Se agrega formulario hce_000349 para la lectura de insumos y procedimientos, se marcan las ordenes prioritarias con
//verde.
//========================================================================================================================================\\
//Abril 5 de 2017 Jonatan: Se agrega el formulario hce_000367 a la revision de formularios de evolucion del paciente, este reemplzara al hce_000069.
//========================================================================================================================================\\
//Junio 22 de 2016 Jonatan: Se agrega la cedula del medico en el detalle de las evoluciones e interconsultas.
//========================================================================================================================================\\
//Julio 14 de 2015 Jonatan: Se agrega la observacion que viene de las ordenes en la columna de examanes.
//========================================================================================================================================\\
//Mayo 27 de 2015 Jonatan
//Se cambia el filtro de lo examenes de eexpen = 'on' a eexaut = 'on', ya que las secretarias solo deben ver los examenes que tengan esa
//variable activa en la tabla 45 de movhos.
//========================================================================================================================================\\
//Abril 04 de 2015 Jerson trujillo: Se agrega select para asignarle un plan al paciente y se le coloca la fecha y hora de creacion de la orden   
//========================================================================================================================================\\
//Febrero 3 de 2015 Jonatan: Se agrega el filtro Ordest = 'on' a las ordenes de los pacientes para que muestre los datos correctamente.
//========================================================================================================================================\\
//Enero 21 de 2015 Jonatan
//Si el nombre del examen no se encuentra en la tabla 47 de hce, buscara en la tabla 17 de hce (lenguaje americas).
//========================================================================================================================================\\
//Octubre 2 de 2014
//Se muestran las observaciones en la columna de observaciones anteriores, en la que se incluyen las del dia actual.
//========================================================================================================================================\\
//Septiembre 26 de 2014 Jonatan
//Se controla el listado de examenes dependiendo de la variable Eexmeh de la tabla movhos_000045, si esta en on se mostraran
//las ordenes que tengan ese estado.
//========================================================================================================================================\\
//Septiembre 8 de 2014 Jonatan
// Se agrega cambio de estado desde para que las secretarias cambien en examen a autorizado, este autorizado se reflejara en el kardex.
//========================================================================================================================================\\
//Agosto 27 de 2014 Jonatan
//  Se agrega identificador al registro de observaciones para que no se repita si el paciente tiene el mismo examen.
//========================================================================================================================================\\
// Agosto 12 de 2014 Jonatan
/* 	Se corrige el filtro por nit de la entidad, ya que estaba siendo separado por el guion, esto mostraba todas las entidades relacionadas con este nit,
	la necesidad es separar los pacientes.
*/
//========================================================================================================================================\\
    if (!isset($_SESSION['user']))
        echo "error";
    else {

	    include_once( "root/comun.php" );
        include_once("movhos/movhos.inc.php");
		include_once("ips/funciones_facturacionERP.php");
		
        $pos = strpos($user, "-");
        $key = substr($user, $pos + 1, strlen($user));
        $wfecha1 = date("Y-m-d");
        $fecha = time ();
        $whora1 = date ( "H:i:s" , $fecha );

        $wtabcco = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tabcco');
        $wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
        $whce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'HCE');

//*******************************************************************************************************************************************
//F U N C I O N E S
//===========================================================================================================================================

class AuditoriaDTO{
	var $fechaRegistro = "";
	var $horaRegistro = "";
	var $historia = "";
	var $ingreso = "";
	var $fechaKardex = "";
	var $descripcion = "";
	var $mensaje = "";
	var $seguridad = "";

	//Anexo para reporte de cambios por tiempo
	var $servicio = "";
	var $confirmadoKardex = "";

	var $idOriginal = 0;
}

//Texto para el mensaje de la auditoria.
function obtenerMensaje($clave){

	$texto = 'No encontrado';

	switch ($clave) {

		case 'MSJ_EXAMEN_ACTUALIZADO':
			$texto = "Examen de laboratorio actualizado";
			break;

		default:
			$texto = "Mensaje no especificado";
			break;
	}

	return $texto;
}


function registrar_bitacora($whis, $wing, $observaciones, $user){
	
	 global $conex;
     global $wmovhos;	
			
	$sqlUltId = "  SELECT MAX(Bitnum) AS id
					 FROM ".$wmovhos."_000021 ";
	$resUltId = mysql_query($sqlUltId, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUltId):</b><br>".mysql_error());
	
	if($rowUltId = mysql_fetch_array($resUltId)){
		
		$nuevoId = ($rowUltId['id']*1)+1;		

		$sqlInserBit = " INSERT INTO ".$wmovhos."_000021
								(      Medico  ,	Fecha_data     ,      Hora_data    ,   Bithis  ,  Biting   ,     Bitnum   ,	       Bitobs       ,	 Bitusr  ,	Bittem , Seguridad	)
						 VALUES ('".$wmovhos."','".date("Y-m-d")."','".date("H:i:s")."','".$whis."','".$wing."','".$nuevoId."', '".$observaciones."', '".$user."',    'AD' , 'C-".$user."' )";
		mysql_query($sqlInserBit, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInserBit):</b><br>".mysql_error());

	}	
}

 /**
 * Función buscar_centro_costo($wemp_pmla, $wcco), esta función busca el nombre de un centro de costo a partir de su código.
 *
 * @param string $wemp_pmla :   Nombre de la empresa-tabla en la que se hace la búsqueda del centro de costo.
 * @param unknown $wcco     :   Código del centro de costos para el cual se va a buscar la descripción.
 * @return string           :   Retorna el nombre o descripción del centro de costos consultado.
 */

function registrarAuditoriaKardex($conex,$wbasedato, $auditoria){

	$q = "INSERT INTO ".$wbasedato."_000055
				(Medico, Fecha_data, Hora_data, Kauhis, Kauing, Kaudes, Kaufec, Kaumen, Kauido, Seguridad)
			VALUES
				('movhos','".date("Y-m-d")."','".date("H:i:s")."','$auditoria->historia','$auditoria->ingreso','$auditoria->descripcion','$auditoria->fechaKardex','$auditoria->mensaje','$auditoria->idOriginal','A-$auditoria->seguridad')";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	}

	//Verifica si el centro de costos debe ir a ordenes.
	function ir_a_ordenes($wemp_pmla, $wcco, $waplicacion){


			global $wmovhos;

			$q = "  SELECT Ccoior
					  FROM ".$wmovhos."_000011
					 WHERE Ccocod = '".$wcco."'" ;
			$res = mysql_query($q);
			$row = mysql_fetch_array($res);

			return $row['Ccoior'];

        }
    //Verifica si el centro de costos debe ir a ordenes si se selecciona todos los centros de costos.
    function ir_a_ordenes_his_ing($his, $ing)
    {
        global $wmovhos;

        $sql = "SELECT Ccoior 
                FROM ".$wmovhos."_000018 A, ".$wmovhos."_000011 B
                WHERE Ubisac = Ccocod
                AND Ubihis = '".$his."' AND Ubiing = '".$ing."'";
        
        $res = mysql_query($sql);
		$row = mysql_fetch_array($res);

		return $row['Ccoior'];
    }

      // FUNCION QUE TRAE LAS OBSERVACIONES GENERALES DEL DIA DE HOY POR HISTORIA
    function traer_descripcion_insumos($wformulario, $wposicion) {

        global $conex;
        global $whce;

        $query =     " SELECT Detdes"
                    ."   FROM ".$whce."_000002"
                    ."  WHERE Detpro = '".$wformulario."'"
                    ."    AND Detcon = '".$wposicion."'";
        $res = mysql_query($query, $conex) or die("Error: ".mysql_error()." - en el query: ".$query." - ".mysql_error());
        $num = mysql_num_rows($res);

        if ($num > 0)
            {
            $row = mysql_fetch_array($res);
            return $row['Detdes'];
            }
        else
            return "";
    }

    //Trae la especialidad del medico.
    function traer_especialidad($wespecialista)
    {

        global $conex;
        global $wmovhos;


        //Tabla de medicos
        $query =     " SELECT Medesp"
                    ."   FROM ".$wmovhos."_000048"
                    ."  WHERE Meduma = '".$wespecialista."'";
        $res = mysql_query($query, $conex) or die("Error: ".mysql_error()." - en el query: ".$query." - ".mysql_error());
        $row = mysql_fetch_array($res);
        $wcod_esp = explode("-", $row['Medesp']);

        //Tabla de especialidades
        $query =     " SELECT Espnom"
                    ."   FROM ".$wmovhos."_000044"
                    ."  WHERE Espcod = '".$wcod_esp[0]."'";
        $res = mysql_query($query, $conex) or die("Error: ".mysql_error()." - en el query: ".$query." - ".mysql_error());
        $row = mysql_fetch_array($res);

        return $row['Espnom']."-".$wcod_esp[0];

    }


    function mostrar_detalle($wemp_pmla, $whis, $wing)
    {

		global $conex;
		global $wemp_pmla;
        global $whce;
        global $wmovhos;
        $wcant_insumostotal = '';

        $wforminsumos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'FormInsumos'); //Extrae el nombre del formulario para extraer los valores a cobrar.
        $wconfinsumos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ConfInsumos'); //Extrae el arreglo con dos numeros, el primero sirve para mostrar el nombre del
                                                                                        //articulo de la tabla hce_000002 y el segundo sirve para extraer la cantidad
                                                                                        //del campo movdat de la tabla hce_000205.

        $wcampos_desc = explode(";", $wconfinsumos); // Se explota el registro que esta separado por comas, para luego usarlo en el ciclo siguiente.
        $wcuantos = count($wcampos_desc);


		echo "<div align='center' style='cursor:default;background:none repeat scroll 0 0; position:relative;width:100%;height:500px;overflow:auto;'><center><br>";
		echo "<table style='text-align: left; width: 100px;' border='0' rowspan='2' colspan='1'>
                <tbody>
                <tr class='encabezadoTabla'>
                <td>Insumo</td>
                <td>Cantidad</td>
                </tr>";

        $query ="SELECT MAX( CONCAT( Fecha_data, ' ', Hora_data ) ) AS FechaHora "
                 ."FROM ".$wmovhos."_000119 "
                ."WHERE Glnhis = '".$whis."'
                    AND Glning = '".$wing."'
                    AND Glnind = 'I'
                    AND Glnest = 'on'";
        $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ". $query ." - ".mysql_error());
        $rows = mysql_fetch_array($res);
        $fechamax_insumos = $rows[0];

		foreach( $wcampos_desc as $key => $value )
		{
			 $posiciones = explode("-", $value ); //Segun la posicion del arreglo, se extraen los datos.

			$arts[ $posiciones[1] ] = $posiciones[0];
		}

		$strin_articulos_in = crear_string_in ( $wcampos_desc );

		//CANTIDAD DE INSUMOS PARA EL PACIENTE UNO POR UNO SEGUN EL CICLO
	   $query =     "SELECT SUM(".$whce."_".$wforminsumos.".movdat), ".$whce."_".$wforminsumos.".movcon "
					 ."FROM ".$whce."_000036, ".$whce."_".$wforminsumos
				   ." WHERE Firhis = '".$whis."'"
					."  AND Firing = '".$wing."'"
					."  AND Firhis = Movhis"
					."  AND Firing = Moving"
					."  AND Firpro = '".$wforminsumos."'"
					."  AND Firfir = 'on'"
					."  AND ".$whce."_000036.Fecha_data = ".$whce."_".$wforminsumos.".Fecha_data"
					."  AND ".$whce."_000036.Hora_data = ".$whce."_".$wforminsumos.".Hora_data"
					."  AND movcon IN ".$strin_articulos_in." "
					."  AND CONCAT( ".$whce."_000036.Fecha_data, ' ', ".$whce."_000036.Hora_data ) > '".$fechamax_insumos."'"
					." GROUP BY ".$whce."_".$wforminsumos.".movcon "
					."   HAVING SUM(".$whce."_".$wforminsumos.".movdat) > 0 ";
		$res= mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
        $i = 0;
		while ($rows = mysql_fetch_array($res))
        {

			$wdescripcion = traer_descripcion_insumos($wforminsumos, $arts[ $rows[1] ] ); //Trae la descripcion del articulo de la tabla hce_000002

			//Estilo de las filas
            if (is_integer($i / 2))
				$wclass = "fila1";
            else
                $wclass = "fila2";

			$wcant_insumosxart   = $rows[0]; //Cantidad por insumo
            $wcant_insumostotal += $rows[0]; //Cantidad total

            if($wcant_insumosxart > 0)
            {
				echo "<tr class=$wclass><td nowrap=nowrap>".utf8_encode($wdescripcion)."</td><td>".$wcant_insumosxart."</td></tr>";
            }
            $i++;

        }

        echo "<tr class=encabezadoTabla><td>Total</td><td>".$wcant_insumostotal."</td></tr>";
        echo "</table>	";
        echo "<p class='blink'><font color='red' size='4'><b>RECUERDE</b></font><br><b>Primero debe grabar en la cuenta del paciente en Unix</b></p>";
        echo "<INPUT TYPE='button' value='Grabar' id='insumos' onClick='grabarinsumos(\"$wmovhos\",\"$whis\",\"$wing\",\"I\",\"$wcant_insumostotal\")'><br>";
        echo "<div id='div_resultado' style='text-align:left;'></div>";
		echo "<br><INPUT TYPE='button' value='Cerrar' onClick=' cerraremergente();' style='width:100'><br><br>";
		echo "</center></div>";



    }

    //Devuelve el nonmbre de un usuario en Matrix
    function traer_nombre_especialista($wcodigo)
    {

        global $conex;

        //Nombre del usuario
        $q_usuario = " SELECT descripcion "
                    ."   FROM usuarios "
                    ."  WHERE codigo = '".$wcodigo."'";
        $res_usuario = mysql_query($q_usuario,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_usuario." - ".mysql_error());
        $row_usuario = mysql_fetch_array($res_usuario);
        $wnombre = $row_usuario['descripcion'];

        return $wnombre;

    }

   function mostrar_detalle_especialista($wemp_pmla, $whis, $wing, $wfecha, $whora, $array_profesores, $evoluciones)
    {

		global $conex;
		global $wemp_pmla;
        global $whce;
        global $wmovhos;

        $array_profesores = unserialize(base64_decode($array_profesores));

		$id_td = $whis."-".$wing;

		echo "<div align='center' style='cursor:default;background:none repeat scroll 0 0; position:relative;width:100%;height:500px;overflow:auto;'><center><br>";
		echo "<table style='text-align: left; width: auto;' border='0' rowspan='2' colspan='1'>
                <tbody>
                <tr class='encabezadoTabla'>
                <td>Especialidad</td>
                <td>Profesional</td>
				<td>Fecha Evoluci&oacute;n</td>
                <td>Evoluciones</td>
                </tr>";

        $arr_resp = array();
        $winfo = '';

        foreach($array_profesores as $firusu => $value)
        {

            $wnombre_esp = $value['nombre_especialidad']; //Nombre de la especialidad
            $wcod_esp = $value['cod_esp']; //Codigo de la especialidad
            $wespecialista = $value['nombre_especialista']; //Trae el nombre del especialista
            $wcedula_medico = $value['cedula_medico']; //Trae el nombre del especialista

            //Si el usuario no tiene especialidad, se le asigna SIN_ESPECALIDAD ya que esa es la clave primaria del arreglo, y asi evitar claves vacias.
            if(trim($wcod_esp) == '')
                {
                    $wcod_esp = 'SIN_ESPECIALIDAD';
                    $wnombre_esp = 'SIN ESPECIALIDAD';
                    $wespecialista = traer_nombre_especialista($firusu);
                }

            if(!array_key_exists($wcod_esp, $arr_resp))
                {
                    $arr_resp[$wcod_esp] = array("nombre_esp"=>$wnombre_esp,"especialistas"=>array());
                }

            //Se lee el arreglo y crea un modelo de texto con el nombre de la especialidad, los especialistas y las evoluciones
            $arr_resp[$wcod_esp]["especialistas"][] = array('especialidad'=> $wcod_esp,'especialista'=> $wespecialista, 'cedula_medico'=>$wcedula_medico,'evoluciones'=> $value['cuantos'], 'cantidad'=> count($value['cuantos']));
            $arr_resp[$wcod_esp]["evoluciones"] += count($value['cuantos']);

        }

        $i = 2;
        $wcuantos = 0;

		// echo "<div align=left>";
               // echo "<pre>";
               // print_r($arr_resp);
               // echo "<pre>";
               // echo "</div>";
        //Se lee el arreglo y crea un modelo de texto con el nombre de la especialidad y los especialistas
        foreach($arr_resp as $key => $value)
        {

            if (is_integer($i / 2))
                $wclass = "fila1";
            else
                $wclass = "fila2";

            //Si hay respuesta en la posicion especialista imprime la linea html.
            if(count($value['especialistas']) > 0)
                {
                    $winfo .= "<tr class=$wclass><td nowrap=nowrap rowspan='".$value['evoluciones']."'>".utf8_encode($value['nombre_esp'])."</td>"; //Especialidad
                }

            $esps = 0;
			$a = array();
            foreach($value['especialistas'] as $keyP => $valueP)
                {
                    if($esps != 0) { $winfo .= "<tr>"; } //Se declara un tr para el inicio de la columan que acompaña a la especialidad




					//Recorro el array de fechas de evoluciones que esta dentro de este arreglo.
					foreach($valueP['evoluciones'] as $clave => $valor){

						if(!array_key_exists($keyP, $a)){
						$winfo .="<td class=$wclass rowspan='".$valueP['cantidad']."' >".utf8_encode($valueP['especialista'])."<br>(Doc. ".$valueP['cedula_medico'].") </td>";
						}


						$winfo .= "<td class=$wclass align=center>".$valor."</td>";

						if(!array_key_exists($keyP, $a)){
						$winfo .="<td class=$wclass align='center' rowspan='".$valueP['cantidad']."'>".$valueP['cantidad']."</td>";
						$a[$keyP] = $keyP;
						}

						$winfo .= "</tr>";
					}

                    //Especialistas y cantidad de evoluciones de cada uno

                    $esps++;

                    //Cuenta cuantas evoluciones hay para luego imprimirlas en el total.
                    $wcuantos = $valueP['evoluciones'];
                }

                $i++;
            }

            //Cuanto esta listo el arreglo se imprime la informacion.
            echo $winfo;

        echo "<tr class=encabezadoTabla><td>Total</td><td></td><td></td><td align=center>".$evoluciones."</td></tr>";
        echo "</table>	";



        echo "<p class='blink'><font color='red' size='4'><b>RECUERDE</b></font><br><b>Primero debe grabar en la cuenta del paciente en Unix</b></p>";
        //En este caso se utiliza la misma funcion de grabarinsumo pero son el parametro E, el cual diferencia el registro de evoluciones.
        echo "<INPUT TYPE='button' value='Grabar' id='insumos' onClick='grabarinsumos(\"$wmovhos\",\"$whis\",\"$wing\",\"E\",\"$wcuantos\",\"$id_td\")'><br>";
        echo "<div id='div_resultado' style='text-align:left;'></div>";
		echo "<br><INPUT TYPE='button' value='Cerrar' onClick=' cerraremergente();' style='width:100'><br><br>";
		echo "</center></div>";



    }

    function buscar_centro_costo($wemp_pmla, $wcco)
        {
            global $conex;

            $q = " SELECT detapl, detval, empdes "
                ."   FROM root_000050, root_000051 "
                ."  WHERE empcod = '".$wemp_pmla."'"
                ."    AND empest = 'on' "
                ."    AND empcod = detemp ";
            $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
            $num = mysql_num_rows($res);

            if ($num > 0)
            {
                for ($i = 1;$i <= $num;$i++)
                {
                    $row = mysql_fetch_array($res);

                    if ($row[0] == "cenmez")
                    $wcenmez = $row[1];

                    if ($row[0] == "afinidad")
                    $wafinidad = $row[1];

                    if ($row[0] == "movhos")
                    $wbasedato = $row[1];

                    if ($row[0] == "tabcco")
                    $wtabcco = $row[1];

                    $winstitucion=$row[2];
                }

                $query = "  SELECT  Ccocod AS cco, Cconom
                        FROM   ".$wbasedato."_000011
                        WHERE   Ccocod = '$wcco'";
                $res = mysql_query($query, $conex) or die ("Error: ".mysql_errno()." - en el query - Buscar Centro costo: ".$query." - ".mysql_error());
                $row = mysql_fetch_array($res);
                return $row['Cconom'];
            }
            return $wcco;
        }

    // FUNCION QUE PERMITE LA GRABACION DE LAS OSERVACIONES DE CADA EXAMEN TRAIDOS DEL AJAX (grabarObservacion)
   function grabarObservacion($wemp_pmla, $wmovhos, $wfec, $wexam, $wing, $wfechadataexamen, $whoradataexamen, $wfechagk, $whis, $wordennro, $wordite, $wtexto, $wid, $texto_bitacora)
       {

          global $conex;
          global $key;

          $whora = (string) date("H:i:s");
		  $wtexto = utf8_decode($wtexto);
		  $texto_bitacora = explode("*",$texto_bitacora);

          $query =   "SELECT COUNT(id) AS contador
                        FROM ".$wmovhos."_000121
                       WHERE Dmoexa = '$wexam'
                         AND Dmohis = '$whis'
                         AND Dmoing = '$wing'
                         AND Dmofka = '$wfechadataexamen'
                         AND Dmohka = '$whoradataexamen'
                         AND Dmofeo = '$wfechagk'
                         AND Dmoord = '$wordennro'
                         AND Dmoite = '$wordite'
                         AND Dmoido = '$wid'
                         AND Fecha_data = '$wfec';";
          $res = mysql_query($query, $conex) or die(mysql_error()." - Error en el query: $query - ".mysql_error());
          $row = mysql_fetch_array($res);
          $contador = $row['contador'];

          if ($contador == 0)
              {
                if ($wtexto != "")
                    {

                     $query =       "INSERT INTO ".$wmovhos."_000121 ( medico, Fecha_data , Hora_data, Dmohis, Dmoing, Dmoexa, Dmofka, Dmohka, Dmofeo, Dmolei, Dmoobs, Dmousu, Dmoord, Dmoite, Dmoest, Dmoido, Seguridad) "
                                        ."VALUES ('".$wmovhos."',
                                                  '".$wfec."' ,
                                                  '".$whora."',
                                                  '".$whis."',
                                                  '".$wing."',
                                                  '".$wexam."',
                                                  '".$wfechadataexamen."',
                                                  '".$whoradataexamen."',
                                                  '".$wfechagk."',
                                                  'off',
                                                  '".$wtexto."',
                                                  '".$key."',
                                                  '".$wordennro."',
                                                  '".$wordite."',
                                                  'on',
												  '".$wid."',
                                                  'C-".$key."') ";
                    $res = mysql_query( $query, $conex ) or die( mysql_errno()." - Error en el query $query - ".mysql_error());					
					
					$texto_bitacora = "Procedimiento: ".$texto_bitacora[1]."\nOrigen: Autorizaciones Pendientes";					
					$texto_bitacora = $texto_bitacora."\n\nObservación: ".$wtexto;
					
					registrar_bitacora($whis, $wing, $texto_bitacora, $key);
					
                    }
              }

          elseif ($wtexto != "" or $wtexto == NULL)
            {

			  $query =       "INSERT INTO ".$wmovhos."_000121 ( medico, Fecha_data , Hora_data, Dmohis, Dmoing, Dmoexa, Dmofka, Dmohka, Dmofeo, Dmolei, Dmoobs, Dmousu, Dmoord, Dmoite, Dmoest, Dmoido, Seguridad) "
                                        ."VALUES ('".$wmovhos."',
                                                  '".$wfec."' ,
                                                  '".$whora."',
                                                  '".$whis."',
                                                  '".$wing."',
                                                  '".$wexam."',
                                                  '".$wfechadataexamen."',
                                                  '".$whoradataexamen."',
                                                  '".$wfechagk."',
                                                  'off',
                                                  '".$wtexto."',
                                                  '".$key."',
                                                  '".$wordennro."',
                                                  '".$wordite."',
                                                  'on',
												  '".$wid."',
                                                  'C-".$key."') ";
             $res = mysql_query( $query, $conex ) or die( mysql_errno()." - Error en el query $query - ".mysql_error());
			

			 $query =  "UPDATE ".$wmovhos."_000121 "
                          ."SET Dmolei = 'off'"
                       ." WHERE Dmoexa = '".$wexam."'
                            AND Dmohis = '".$whis."'
                            AND Dmoing = '".$wing."'
                            AND Dmofka = '".$wfechadataexamen."'
                            AND Dmohka = '".$whoradataexamen."'
                            AND Dmofeo = '".$wfechagk."'
                            AND Dmoord = '".$wordennro."'
                            AND Dmoite = '".$wordite."'
							AND Dmoido = '".$wid."'
                            AND Fecha_data= '".$wfec."'";
              $res = mysql_query($query,$conex) or die (mysql_errno()." - ".mysql_error());
			  
			  $texto_bitacora = "\nProcediento: ".$texto_bitacora[1]."\nOrigen: Autorizaciones Pendientes";
			  $texto_bitacora = $texto_bitacora."\n\nObservación: ".$wtexto;	
			  
			  registrar_bitacora($whis, $wing, $texto_bitacora, $key);


            }
        }

	//Funcion que cambia el estado del examen por parte de la secretaria.
	function cambiar_estado_examen($wemp_pmla, $wmovhos, $wfec, $wexam, $wing, $wfechadataexamen, $whoradataexamen, $wfechagk, $whis, $wordennro, $wordite, $westado, $wid, $wcco, $whce, $wcontrol_ordenes, $wtexto_examen, $westado_registro)
       {

          global $conex;
          global $key;

		  $wir_a_ordenes = ir_a_ordenes($wemp_pmla, $wcco, 'ir_a_ordenes');

          $whora = (string) date("H:i:s");
		  $wtexto = utf8_decode($wtexto);
		  $nombreExamen = $wexam;
		  $audNuevo = "N:".$wexam.",".$wordennro.",".$wordite.",".str_replace( "_", " ", trim($wtexto_examen) ).",".$westado.",,".$wfechadataexamen;
		  $audAnterior = "A:".$wexam.",".$wordennro.",".$wordite.",".str_replace( "_", " ", trim($wtexto_examen) ).",".$westado_registro.",,".$wfechadataexamen;
		  //Verifica si debe actualizar registros de ordenes o de kardex.
		  if($wcontrol_ordenes != 'on'){

		  //Modifica la tabla ppal.
          $query =   "UPDATE ".$wmovhos."_000050
						 SET Ekaest = '".$westado."'
                       WHERE id = '$wid'
                         AND Ekafec = '$wfec'
                         AND Ekahis = '$whis'
                         AND Ekaing = '$wing'";
          $res = mysql_query($query, $conex) or die(mysql_error()." - Error en el query: $query - ".mysql_error());

		  //Modifica la tabla temporal.
		  $query =   "UPDATE ".$wmovhos."_000061
						 SET Ekaest = '".$westado."'
                       WHERE Ekaido = '$wid'
					     AND Ekafec = '$wfec'
                         AND Ekahis = '$whis'
                         AND Ekaing = '$wing'";
          $res = mysql_query($query, $conex) or die(mysql_error()." - Error en el query: $query - ".mysql_error());


		  }else{

		  $query1 ="  	UPDATE ".$whce."_000027 A, ".$whce."_000028 B
						   SET Detesi = '".$westado."'
                         WHERE Ordtor = Dettor
                           AND Ordnro = Detnro
                           AND A.Ordhis = '".$whis."'
                           AND A.Ording = '".$wing."'
                           AND B.Detnro = '".$wordennro."'
                           AND B.Detite = '".$wordite."'
                           AND B.Detest = 'on'";
          $res1 = mysql_query($query1, $conex) or die(mysql_errno()." - Error en el query $sql - ".mysql_error());

		  $query1 ="  	UPDATE ".$whce."_000027 A, ".$wmovhos."_000159 B
						   SET Detesi = '".$westado."'
                         WHERE Ordtor = Dettor
                           AND Ordnro = Detnro
                           AND A.Ordhis = '".$whis."'
                           AND A.Ording = '".$wing."'
                           AND B.Detnro = '".$wordennro."'
                           AND B.Detite = '".$wordite."'
                           AND B.Detest = 'on'";
          $res1 = mysql_query($query1, $conex) or die(mysql_errno()." - Error en el query $sql - ".mysql_error());


		  }

			$mensajeAuditoria = obtenerMensaje('MSJ_EXAMEN_ACTUALIZADO');

		    //Registro de auditoria
			$auditoria = new AuditoriaDTO();

			$auditoria->historia = $whis;
			$auditoria->ingreso = $wing;
			$auditoria->descripcion = "$audAnterior $audNuevo";
			$auditoria->fechaKardex = $wfec;
			$auditoria->mensaje = $mensajeAuditoria;
			$auditoria->seguridad = $key;

			registrarAuditoriaKardex($conex,$wmovhos,$auditoria);


        }

        // FUNCION QUE PERMITE LA GRABACION DE OBSERVACIONES GENERALES TRAIDAS DEL AJAX
     function grabarObservaciongnral($wmovhos, $wfec, $wing, $whis, $wemp_pmla, $wtexto)
         {

          global $conex;
          global $key;

          $wfecha = date("Y-m-d");

          $whora = (string) date("H:i:s");
          $wtexto = utf8_decode($wtexto);

          $query =    "SELECT COUNT(*) AS contador
                                 FROM ".$wmovhos."_000120
                                WHERE Monhis = '$whis'
                                  AND Moning = '$wing'
                                  AND Fecha_data = '$wfec';";
          $res = mysql_query($query, $conex) or die(mysql_error()." - Error en el query $query - ".mysql_error());
          $row = mysql_fetch_array($res);
          $contador = $row['contador'];

          if ($contador == 0)
              {
                if ($wtexto == "")
                {

                }else{
                      $query = "INSERT INTO ".$wmovhos."_000120 ( medico, Fecha_data , Hora_data, Monhis, Moning, Monobs, Monusu, Monest, seguridad ) "
                                   ."VALUES ('".$wmovhos."',
                                             '".$wfecha."' ,
                                             '".$whora."',
                                             '".$whis."',
                                             '".$wing."',
                                             '".$wtexto."',
                                             '".$key."',
                                             'on',
                                             '".$wmovhos."') ";

                      $res = mysql_query( $query, $conex ) or die( mysql_errno()." - Error en el query $query - ".mysql_error());
                      }
             }
             elseif ($wtexto != "")
                    {
                       $query = "UPDATE ".$wmovhos."_000120 "
                                  ."SET Monobs = '".$wtexto."'"
                                ."WHERE Monhis = ".$whis."
                                    AND Moning = ".$wing."
                                    AND Fecha_data= '".$wfecha."'";
                       $res = mysql_query( $query, $conex ) or die( mysql_errno()." - Error en el query $query - ".mysql_error());

                    }
        }

        // FUNCION QUE PERMITE LA GRABACION GLUCOMETRIAS DEL PANEL INICIAL
     function grabargluco($wmovhos, $whis, $wing, $wtipo, $wvalor, $wfechapantalla, $whorapantalla)
            {

                global $conex;
                global $key;

                //$fecha_actual = date("Y-m-d");

               // INSERCION DE NUMERACION PARA EL CONSECUTIVO DE LA TABLA
                $query = "LOCK TABLE numeracion LOW_PRIORITY WRITE,
                            ".$wmovhos."_000119 LOW_PRIORITY WRITE ";
                $err1 = mysql_query($query,$conex);

                $query =  " UPDATE numeracion
                                    SET secuencia = secuencia + 1
                                    WHERE medico='".$wmovhos."'
                                    AND formulario='000119'
                                    AND campo='0006' ";
                $err2 = mysql_query($query,$conex);

                $query = "SELECT *
                                    FROM numeracion
                                WHERE medico='".$wmovhos."'
                                    AND formulario='000119'
                                    AND campo='0006' ";
                $err3 = mysql_query($query,$conex);
                $row = mysql_fetch_array($err3);
                $con=$row[3];

                $query = "INSERT INTO ".$wmovhos."_000119 ( medico, Fecha_data , Hora_data, Glnhis, Glning, Glnind, Glncan, Glnusu, Glncon, Glnest, Seguridad ) "
                             ."VALUES ('".$wmovhos."',
                                       '".$wfechapantalla."' ,
                                       '".$whorapantalla."',
                                       '".$whis."',
                                       '".$wing."',
                                       '".$wtipo."',
                                       '".$wvalor."',
                                       '".$key."',
                                       '".$con."',
                                       'on',
                                       '".$wmovhos."') ";
                $res = mysql_query( $query, $conex ) or die( mysql_errno()." - Error en el query: $query - ".mysql_error());

                $query = " UNLOCK TABLES";
                $res = mysql_query( $query, $conex ) or die( mysql_errno()." - Error en el query: $query - ".mysql_error());
            }

        // FUNCION QUE PERMITE LA GRABACION NEBULIZACIONES DEL PANEL INICIAL
        function grabarnebus($wmovhos, $whis, $wing, $wtipo, $wvalor, $wfechapantalla, $whorapantalla)
            {

                global $conex;
                global $key;

                // INSERCION DE NUMERACION PARA EL CONSECUTIVO DE LA TABLA
               // INSERCION DE NUMERACION PARA EL CONSECUTIVO DE LA TABLA
                $query = "LOCK TABLE numeracion LOW_PRIORITY WRITE,
                            ".$wmovhos."_000119 LOW_PRIORITY WRITE ";
                $err1 = mysql_query($query,$conex);

                $query =  " UPDATE numeracion
                                    SET secuencia = secuencia + 1
                                    WHERE medico='".$wmovhos."'
                                    AND formulario='000119'
                                    AND campo='0006' ";
                $err2 = mysql_query($query,$conex);

                $query = "SELECT *
                                    FROM numeracion
                                WHERE medico='".$wmovhos."'
                                    AND formulario='000119'
                                    AND campo='0006' ";
                $err3 = mysql_query($query,$conex);
                $row = mysql_fetch_array($err3);
                $con=$row[3];

                $fecha_actual = date("Y-m-d");
                $whora = (string) date("H:i:s");

                $query = "INSERT INTO ".$wmovhos."_000119 ( medico, Fecha_data , Hora_data, Glnhis, Glning, Glnind, Glncan, Glnusu, Glncon, Glnest, Seguridad ) "
                             ."VALUES ('".$wmovhos."',
                                       '".$wfechapantalla."' ,
                                       '".$whorapantalla."',
                                       '".$whis."',
                                       '".$wing."',
                                       '".$wtipo."',
                                       '".$wvalor."',
                                       '".$key."',
                                       '".$con."', 'on',
                                       '".$wmovhos."') ";
                $res = mysql_query( $query, $conex ) or die( mysql_errno()." - Error en el query $query - ".mysql_error());

                $query = " UNLOCK TABLES";
                $res = mysql_query( $query, $conex ) or die( mysql_errno()." - Error en el query: $query - ".mysql_error());

            }

            // FUNCION QUE PERMITE LA GRABACION DE OXIMETRIAS DEL PANEL INICIAL
        function grabaroxi($wmovhos, $whis, $wing, $wtipo, $wvalor, $wfechapantalla, $whorapantalla)
            {

                global $conex;
                global $key;


               // INSERCION DE NUMERACION PARA EL CONSECUTIVO DE LA TABLA
                $query = "LOCK TABLE numeracion LOW_PRIORITY WRITE,
                            ".$wmovhos."_000119 LOW_PRIORITY WRITE ";
                $err1 = mysql_query($query,$conex);

                $query =  " UPDATE numeracion
                                    SET secuencia = secuencia + 1
                                    WHERE medico='".$wmovhos."'
                                    AND formulario='000119'
                                    AND campo='0006' ";
                $err2 = mysql_query($query,$conex);

                $query = "SELECT *
                                    FROM numeracion
                                WHERE medico='".$wmovhos."'
                                    AND formulario='000119'
                                    AND campo='0006' ";
                $err3 = mysql_query($query,$conex);
                $row = mysql_fetch_array($err3);
                $con=$row[3];

                $fecha_actual = date("Y-m-d");
                $whora = (string) date("H:i:s");

                $query = "INSERT INTO ".$wmovhos."_000119 ( medico, Fecha_data , Hora_data, Glnhis, Glning, Glnind, Glncan, Glnusu, Glncon, Glnest, Seguridad ) "
                             ."VALUES ('".$wmovhos."',
                                       '".$wfechapantalla."' ,
                                       '".$whorapantalla."',
                                       '".$whis."',
                                       '".$wing."',
                                       '".$wtipo."',
                                       '".$wvalor."',
                                       '".$key."',
                                       '".$con."', 'on',
                                       '".$wmovhos."') ";
                $res = mysql_query( $query, $conex ) or die( mysql_errno()." - Error en el query $query - ".mysql_error());

                $query = " UNLOCK TABLES";
                $res = mysql_query( $query, $conex ) or die( mysql_errno()." - Error en el query: $query - ".mysql_error());

            }

         // FUNCION QUE PERMITE LA GRABACION TRANSFUSIONES DEL PANEL INICIAL
        function grabartransf($wmovhos, $whis, $wing, $wtipo, $wvalor, $wfechapantalla, $whorapantalla)
            {

                global $conex;
                global $key;

                // INSERCION DE NUMERACION PARA EL CONSECUTIVO DE LA TABLA
                $query = "LOCK TABLE numeracion LOW_PRIORITY WRITE,
                            ".$wmovhos."_000119 LOW_PRIORITY WRITE ";
                $err1 = mysql_query($query,$conex);

                $query =  " UPDATE numeracion
                               SET secuencia = secuencia + 1
                             WHERE medico='".$wmovhos."'
                               AND formulario='000119'
                               AND campo='0006' ";
                $err2 = mysql_query($query,$conex);

                $query = "SELECT *
                            FROM numeracion
                           WHERE medico='".$wmovhos."'
                             AND formulario='000119'
                             AND campo='0006' ";
                $err3 = mysql_query($query,$conex);
                $row = mysql_fetch_array($err3);
                $con=$row[3];

                $query = "INSERT INTO ".$wmovhos."_000119 ( medico, Fecha_data , Hora_data, Glnhis, Glning, Glnind, Glncan, Glnusu, Glncon, Glnest, Seguridad ) "
                             ."VALUES ('".$wmovhos."',
                                       '".$wfechapantalla."' ,
                                       '".$whorapantalla."',
                                       '".$whis."',
                                       '".$wing."',
                                       '".$wtipo."',
                                       '".$wvalor."',
                                       '".$key."',
                                       '".$con."', 'on',
                                       '".$wmovhos."') ";
                $res = mysql_query( $query, $conex ) or die( mysql_errno()." - Error en el query $query - ".mysql_error());

                $query = " UNLOCK TABLES";
                $res = mysql_query( $query, $conex ) or die( mysql_errno()." - Error en el query: $query - ".mysql_error());

            }


          // FUNCION QUE PERMITE LA GRABACION DE INSUMOS DEL PANEL INICIAL
        function grabarinsumos($wmovhos, $whis, $wing, $wtipo, $wvalor, $wfechapantalla, $whorapantalla)
            {

                global $conex;
                global $key;

                $datamensaje = array('mensaje'=>'', 'error'=>0);

                // INSERCION DE NUMERACION PARA EL CONSECUTIVO DE LA TABLA
                $query = "LOCK TABLE numeracion LOW_PRIORITY WRITE,
                            ".$wmovhos."_000119 LOW_PRIORITY WRITE ";
                $err1 = mysql_query($query,$conex);

                $query =   " UPDATE numeracion
                                SET secuencia = secuencia + 1
                              WHERE medico='".$wmovhos."'
                                AND formulario='000119'
                                AND campo='0006' ";
                $err2 = mysql_query($query,$conex);

                $query = "SELECT *
                            FROM numeracion
                           WHERE medico='".$wmovhos."'
                             AND formulario='000119'
                             AND campo='0006' ";
                $err3 = mysql_query($query,$conex);
                $row = mysql_fetch_array($err3);
                $con=$row[3];

                $fecha_actual = date("Y-m-d");
                $whora = (string) date("H:i:s");

                $query = "INSERT INTO ".$wmovhos."_000119 ( medico, Fecha_data , Hora_data, Glnhis, Glning, Glnind, Glncan, Glnusu, Glncon, Glnest, Seguridad ) "
                             ."VALUES ('".$wmovhos."',
                                       '".$wfechapantalla."' ,
                                       '".$whorapantalla."',
                                       '".$whis."',
                                       '".$wing."',
                                       '".$wtipo."',
                                       '".$wvalor."',
                                       '".$key."',
                                       '".$con."',
                                       'on',
                                       '".$wmovhos."') ";
                $res = mysql_query( $query, $conex ) or die( mysql_errno()." - Error en el query $query - ".mysql_error());

                $query = " UNLOCK TABLES";
                $res = mysql_query( $query, $conex ) or die( mysql_errno()." - Error en el query: $query - ".mysql_error());

                if($wtipo == "E")
                    {
                     $datamensaje['mensaje'] = 'Evoluciones guardadas';
                    }
                else
                    {
                    $datamensaje['mensaje'] = 'Insumos guardados';
                    }

                echo json_encode($datamensaje);
            }


        function traer_medico_tte($whis, $wing, $wfecha, &$i)
        {
                global $conex;
                global $wmovhos;

                $query = " SELECT Medno1, Medno2, Medap1, Medap2  "
                        ."   FROM ".$wmovhos."_000047, ".$wmovhos."_000048 "
                        ."  WHERE methis = '".$whis."'"
                        ."    AND meting = '".$wing."'"
                        ."    AND metest = 'on' "
                        ."    AND metfek = '".$wfecha."'"
                        ."    AND mettdo = medtdo "
                        ."    AND metdoc = meddoc "
                        ."    GROUP BY meddoc ";
                $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                $wnum = mysql_num_rows($res);
				
				if($wnum > 0){
					while($row = mysql_fetch_array($res)){
						
						$wmed .= "<li>".$row[0]." ".$row[1]."<br>".$row[2]." ".$row[3]."</li>";
					}
				}
				else{
                       $wmed = "Sin Médico";
				}
				
				return $wmed;
        }


     //Aqui se consulta quien es el profesor que confirma el formulario para un medico residente.
     function consultar_profe_confirma($whis, $wing, $wfecha_registro, $whora_registro)
     {

        global $conex;
        global $whce;
        global $wemp_pmla;
        global $wmovhos;

        $wdatos_profesor = array();

        $wform_evoluciones = consultarAliasPorAplicacion($conex, $wemp_pmla, 'FormularioEvoluciones');
        // Se explota el registro que esta separado por guion para extraer el codigo del formulario y la posicion donde esta el codigo del profesor.
        $wform_posicion_evo = explode("-", $wform_evoluciones);
        $wformulario = $wform_posicion_evo[0]; // Formulario
        $wposicion = $wform_posicion_evo[1]; // Codigo del profesor

        //Consulto en la tabla hce_000068 (".$whce."_".$wformulario.") con la fecha, hora, historia, ingreso y posicion (movcon = '".$wposicion."'"),
        //para extraer el codigo del profesor que firmo el formulario.
        $query =         "SELECT movusu, u.descripcion, espmed.Medesp, nomesp.Espnom  "
                        ."  FROM ".$whce."_".$wformulario." as formulario "
                        ."INNER JOIN
                            usuarios as u on (u.codigo = formulario.movusu )
                          INNER JOIN
                          ".$wmovhos."_000048 as espmed on (espmed.Meduma = formulario.movusu)
                          INNER JOIN
                          ".$wmovhos."_000044 as nomesp on (nomesp.Espcod = SUBSTRING_INDEX(espmed.Medesp, '-', 1))"
                        ." WHERE movhis = '".$whis."'"
                        ."  AND moving = '".$wing."'"
                        ."  AND movpro = '".$wformulario."'"
                        ."  AND formulario.Fecha_data = '".$wfecha_registro."'"
                        ."  AND formulario.Hora_data = '".$whora_registro."'"
                        ."  AND movcon = '".$wposicion."'"; //Esta posicion se refiere a al especialista que confirmo el formulario;
        $res= mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
        $rows = mysql_fetch_array($res);


        $wprofe_firma = $rows['movusu']; //Codigo del profesor que confirma el formulario.
        $wnombre_profe = $rows['descripcion']; //Nombre del profesor.
        $wcodigo_especialidad = $rows['Medesp']; // Codigo de la especialidad.
        $wnombre_especialidad = $rows['Espnom']; // Nombre de la especialidad.

        $wdatos_profesor = array('codigo_profesor'=>$wprofe_firma,'nombre'=>$wnombre_profe, 'codigo_especialidad'=> $wcodigo_especialidad, 'descrip_especialidad'=> $wnombre_especialidad, 'fecha_firma'=> $wfecha_registro );

        return $wdatos_profesor;
     }

// FUNCION PARA MOSTRAR LOS PENDIENTES POR GRABAR PARA LAS EVOLUCIONES //Enero 12/2012 Jonatan Lopez
function traer_evoluciones($wmovhos, $whis, $wing, $wemp_pmla, &$wevoluciones)
	{

		global $conex;
		global $whce;

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


		//Consulta todos los especialistas que tienen el campo usures diferente de on, quiere decir los que son profesores,
		//hago la relacion de los codigos para extraer la especialidad, el nombre y el codigo de la especialidad.
		$query =    " SELECT usucod, usualu, u.descripcion, espmed.Medesp, nomesp.Espnom, Meddoc"
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
															   'cuantos'=>array(),
												   'nombre_especialista'=>'MEDICO DE TURNO',
															   'cod_esp'=>'medico_turno',
												   'nombre_especialidad'=>'MEDICO DE TURNO',
																'alumnos'=>array()
													   )
									);

//                echo "<div align=left>";
//                echo "<pre>";
//                print_r($array_profesores);
//                echo "<pre>";
//                echo "</div>";

		$array_alumnos = array();
		//Al recorrer el resultado de la consulta se crea un arreglo $array_profesores[$row['usucod']][dato] y se agrega al arreglo $array_profesores[$row['usucod']]['alumnos'][],
		//todos los alumnnos asignados a el, solo se agregaran si la posicion $alumno del foreach es diferente de vacio y diferente de punto.
		while($row = mysql_fetch_array($res))
		{
			if(!array_key_exists($row['usucod'], $array_profesores))
			{
				$array_profesores[$row['usucod']] = array();
			}

			$array_profesores[$row['usucod']]['cuantos'] = array();
			$array_profesores[$row['usucod']]['nombre_especialista'] = $row['descripcion'];
			$array_profesores[$row['usucod']]['cod_esp'] = $row['Medesp'];
			$array_profesores[$row['usucod']]['nombre_especialidad'] = $row['Espnom'];
			$array_profesores[$row['usucod']]['cedula_medico'] = $row['Meddoc'];
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
		$query =    " SELECT * FROM(
						SELECT firusu, usuhce.usualu, u.descripcion, usuhce.usures, ".$whce."_000036.Fecha_data as fechafir, ".$whce."_000036.Hora_data as horafir, "
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
						."   AND CONCAT( ".$whce."_000036.Fecha_data, ' ', ".$whce."_000036.Hora_data ) > '".$fechamax_evolucion."'					
						UNION
						SELECT firusu, usuhce.usualu, u.descripcion, usuhce.usures, ".$whce."_000036.Fecha_data as fechafir, ".$whce."_000036.Hora_data as horafir, "
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
						."   AND CONCAT( ".$whce."_000036.Fecha_data, ' ', ".$whce."_000036.Hora_data ) > '".$fechamax_evolucion."') as t";
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

							//Si el especialista que firma es residente entonces traera el profesor que le confirmo el formulario.
							$wprofe_confirma69 = consultar_profe_confirma($row1['firhis'],$row1['firing'],$row1['fechafir'], $row1['horafir'], $wformulario69, $wposicion);
							$wprofe_confirma367 = consultar_profe_confirma($row1['firhis'],$row1['firing'],$row1['fechafir'], $row1['horafir'], $wformulario367, $wposicion);
							
							if($wprofe_confirma69['codigo_profesor'] != ''){							
								$el_profe = $wprofe_confirma['codigo_profesor'];
							}
							
							if($wprofe_confirma367['codigo_profesor'] != ''){						
								$el_profe = $wprofe_confirma['codigo_profesor'];
							}

							//Si ese profesor no esta en el arreglo de profesores entonces lo agregara.
							if (!array_key_exists($el_profe, $array_profesores))
							{
								$array_profesores[$el_profe]['cuantos'] = array();
								$array_profesores[$el_profe]['nombre_especialista'] = $wprofe_confirma['nombre'];
								$array_profesores[$el_profe]['cod_esp'] = $wprofe_confirma['codigo_especialidad'];
								$array_profesores[$el_profe]['nombre_especialidad'] = $wprofe_confirma['descrip_especialidad'];
								$array_profesores[$el_profe]['cedula_medico'] = $wprofe_confirma['cedula_medico'];

							}

							$array_profesores[$el_profe]['cuantos'][] = $row1['fechafir'];


							//Se declara esta variable para que se le asigne al medico de turno la especialidad de uno de los profesores que tiene asignado, la especialidad
							//del profesor siempre sera la misma en donde aparezca el alumno.
							$cod_profesor = $array_alumnos[$row1['firusu']]['profesor'][0];

							//Al array profesores en la posicion cod_esp se le asigna la primera aparicion de codigo de su profesor.
							$array_profesores[$el_profe]['cod_esp'] = $array_profesores[$cod_profesor]['cod_esp'];

							//Al array profesores en la posicion nombre_especialidad se le asigna la primera aparicion de especialidad de su profesor.
							$array_profesores[$el_profe]['nombre_especialidad'] = $array_profesores[$cod_profesor]['nombre_especialidad'];
					}
				 else
					{
							//Si el especialista solo aparece una vez y no es alumno de nadie entonces deja los datos como vienen en el arreglo.
							$el_profe = $array_alumnos[$row1['firusu']]['profesor'][0];
							//Codigo del profesor y cuantas apariciones tiene.
							$array_profesores[$el_profe]['cuantos'][] = $row1['fechafir'];

					}

				}

			}
			//Si el usuario no es residente, entonces la informacion se mantendra como viene en el arreglo de profesores.
			else
			{

				if(!array_key_exists($row1['firusu'], $array_profesores)){

				   $array_profesores[$row1['firusu']]['cuantos'] = array();
				}

			   $array_profesores[$row1['firusu']]['cuantos'][] = $row1['fechafir'];

			}

		}

		$array_aux = $array_profesores; // Auxiliar para recorrer array_profesores y eliminar los que tengan cero.

		//Elimino los elementos del arreglo de profesores que tienen la posicion cuantos en cero.
		foreach ($array_aux as $key => $value)
			{
				if(count($value['cuantos']) == '0')
				{
					unset($array_profesores[$key]);
				}else{

				//Cuento las evoluciones por medico, el arreglo en la posicion $array_profesores[$key]['cuantos'] contiene las fechas de las evoluciones firmadas.
				$wevoluciones += count($array_profesores[$key]['cuantos']);

				}

			}

	   // echo "<div align=left>";
	   // echo "<pre>";
	   // print_r($array_profesores);
	   // echo "<pre>";
	   // echo "</div>";

	  return $array_profesores;


}

// FUNCION PARA MOSTRAR LOS PENDIENTES POR GRABAR PARA LAS TRANSFUSIONES //Enero 12/2012 Jonatan Lopez
function traer_transfusiones($wmovhos, $whis, $wing, $wemp_pmla)
 {

	global $conex;
	global $whce;

	$wtransfusiones = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Transfusiones');
	$wcampos = explode("-", $wtransfusiones);
	$wtablat = $wcampos[0];
	$wcampot = $wcampos[1];

	// CONSULTA PARA EXTRAER LA FECHA MAXIMA DE LAS TRANSFUSIONES GUARD PARA UNA HIST E INGRESO
	$query =     "SELECT MAX(CONCAT( Fecha_data, ' ', Hora_data ) ) AS FechaHora "
					."FROM ".$wmovhos."_000119 "
				   ."WHERE Glnhis = '".$whis."'
						 AND Glning = '".$wing."'
						 AND Glnind = 'T'
						 AND Glnest = 'on'";
	$res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ". $query ." - ".mysql_error());
	$rows = mysql_fetch_array($res);

	$fechamax_transf = $rows[0];

	//CANTIDAD DE TRANSFUSIONES SIN GUARDAR
   $query =  " SELECT COUNT(DISTINCT (movdat))  "
			."  FROM ".$whce."_000036, ".$whce."_".$wtablat
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

// FUNCION QUE TRAE LAS OBSERVACIONES DE DIAS ANTERIORES Y LOS IMPRIME EN EL TEXT AREA DE CADA EXAMEN
function traer_observaciones_anteriores_exam($whis, $wing, $wexam, $wfechadataexamen, $whoradataexamen, $wfechagk, $wordennro, $wordite, $wid_a) {

	global $conex;
	global $wmovhos;
	global $wfecha1;
	$dato = '';

	$query = " SELECT * "
			 ."  FROM ".$wmovhos."_000121 "
			 ." WHERE Dmohis = '".$whis."'"
			."  AND Dmoing = '".$wing."'"
			."  AND Dmoexa = '".$wexam."'"
			."  AND Dmofka = '".$wfechadataexamen."'"
			."  AND Dmohka = '".$whoradataexamen."'"
			."  AND Dmoido = '".$wid_a."'"
			."  AND Dmoite = '".$wordite."'"
			."  AND Fecha_data <= '" .$wfecha1. "'"
			."  AND Dmoest = 'on'"
			." UNION "
			." SELECT * "
			."  FROM ".$wmovhos."_000121 "
			." WHERE Dmohis = '".$whis."'"
			."  AND Dmoing = '".$wing."'"
			."  AND Dmoexa = '".$wexam."'"
			."  AND Dmofka = '".$wfechadataexamen."'"
			."  AND Dmohka = '".$whoradataexamen."'"
			."  AND Dmoido = ''"
			."  AND Fecha_data <= '" .$wfecha1. "'"
			."  AND Dmoest = 'on' "
			."ORDER BY Fecha_data DESC, Hora_data DESC ";

	$res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
	$wnum = mysql_num_rows($res);

	$dato .= "<table>";
	if ($wnum > 0)
		{

			while ($row = mysql_fetch_array($res))
					{
						if ($row['Dmoobs'] != ' ' and $row['Dmoobs'] != ''){

						//Nombre del usuario
						$q_usuario = " SELECT descripcion "
									."   FROM usuarios "
									."  WHERE codigo = '".$row['Dmousu']."'";
						$res_usuario = mysql_query($q_usuario,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_usuario." - ".mysql_error());
						$row_usuario = mysql_fetch_array($res_usuario);
						$wnombre = $row_usuario['descripcion'];
						$dato .= "<tr>";
						$dato .= "<td>";
						$dato .= "<font size='2'><b>".$row['Dmoobs']."</b></font><br><font size='1'>".$row['Fecha_data']." ".$row['Hora_data']." por ".$wnombre."</font><hr>";
						$dato .= "</td>";
						$dato .= "</tr>";

						}
					}

		}
	$dato .= "</table>";

	return $dato;

}

// FUNCION QUE TRAE LAS OBSERVACIONES DEL DIA DE HOY Y LOS IMPRIME EN EL TEXTAREA DE CADA EXAMEN
function traer_observaciones_examen($whis, $wing, $wexam, $wfechadataexamen, $whoradataexamen, $wfechagk, $wordennro, $wordite, $wid_a) {

	global $conex;
	global $wmovhos;
	global $wfecha1;


	$query =    " SELECT Dmoobs "
			   ."   FROM ".$wmovhos."_000121 "
			   ."  WHERE Dmohis = '".$whis."'"
			   ."    AND Dmoing = '".$wing."'"
			   ."    AND Dmoexa = '".$wexam."'"
			   ."    AND Dmofka = '".$wfechadataexamen."'"
			   ."    AND Dmohka = '".$whoradataexamen."'"
			   ."    AND Dmofeo = '".$wfechagk."'"
			   ."    AND Dmoord = '".$wordennro."'"
			   ."    AND Dmoite = '".$wordite."'"
			   ."    AND Fecha_data = '".$wfecha1."'"
			   ."    AND Dmoido = '".$wid_a."'"
			   ."    AND Dmoest = 'on'"
			   ."  UNION "
			   ." SELECT Dmoobs "
			   ."   FROM ".$wmovhos."_000121 "
			   ."  WHERE Dmohis = '".$whis."'"
			   ."    AND Dmoing = '".$wing."'"
			   ."    AND Dmoexa = '".$wexam."'"
			   ."    AND Dmofka = '".$wfechadataexamen."'"
			   ."    AND Dmohka = '".$whoradataexamen."'"
			   ."    AND Dmofeo = '".$wfechagk."'"
			   ."    AND Dmoord = '".$wordennro."'"
			   ."    AND Dmoite = '".$wordite."'"
			   ."    AND Fecha_data = '".$wfecha1."'"
			   ."    AND Dmoido = ''"
			   ."    AND Dmoest = 'on'";
	$res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$wnum = mysql_num_rows($res);

   if ($wnum > 0)
	   {
		$row = mysql_fetch_array($res);
		return $row['Dmoobs'];
	   }
   else
		return "";
}

	// FUNCION QUE TRAE LAS OBSERVACIONES DE DIAS ANTERIORES PARA LAS HISTORIAS Y LOS IMPRIME EN EL TEXTAREA INFERIOR
	function traer_obser_anterior_hist($whis, $wing) {

            global $conex;
            global $wmovhos;
            global $wfecha1;
            $dato = '';

            $query =      " SELECT * "
                       ."   FROM ".$wmovhos."_000120 "
                       ."  WHERE Monhis = '".$whis."'"
                       ."    AND Moning = '".$wing."'"
                       ."    AND Fecha_data < '" .$wfecha1. "'"
                       ."    AND Monest = 'on'  ORDER BY Fecha_data DESC";

            $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $wnum = mysql_num_rows($res);

            if ($wnum > 0)
                {
                    while ($row = mysql_fetch_array($res))
                            {
                                $dato .= $row['Monobs']." \n".$row['Monusu'].' / '.$row['Fecha_data'].' '.$row['Hora_data']."\n\n";
                            }
                            return $dato;

                }
            else
                return "";
        }

        // FUNCION QUE TRAE LAS OBSERVACIONES GENERALES DEL DIA DE HOY POR HISTORIA
        function traer_observaciones_historia($whis, $wing, $wfecha1) {

            global $conex;
            global $wmovhos;

           $query =   " SELECT Fecha_data, Hora_data, Monhis, Moning, Monobs"
                   ."   FROM ".$wmovhos."_000120"
                   ."  WHERE Monhis = '".$whis."'"
                   ."    AND Moning = '".$wing."'"
                   ."    AND Fecha_data = '".$wfecha1."' ORDER BY Fecha_data DESC";

            $res = mysql_query($query, $conex) or die("Error: ".mysql_error()." - en el query: ".$query." - ".mysql_error());
            $num = mysql_num_rows($res);

           if ($num > 0)
               {
                $row = mysql_fetch_array($res);
                return $row['Monobs'];
               }
            else
                return "";
        }

         // FUNCION QUE IMPRIME UN AREA DE TEXTO Y CREA O ACTUALIZA LAS OBSERVACIONES GENERALES
        function observa_general($whis, $wing, $wfecha1)

         {

            global $wemp_pmla;


             //IMPRIME LOS DATOS DEL KARDEX DEL DIA ACTUAL SI EXISTEN
            query_kardex($whis, $wing, $wfecha1, $res);

            echo '<br><table cellpadding="2" cellspacing="2" align="center">
                  <tbody>
                    <tr align="center" class="encabezadoTabla">
                      <td colspan="2" rowspan="1">OBSERVACIONES GENERALES</td>
                    </tr>
                    <tr>
                      <td class="encabezadoTabla" align="center">&nbsp;HOY</td>
                      <td class="encabezadoTabla" align="center">&nbsp;ANTERIORES</td>
                    </tr>
                    <tr>';
            echo  "<td colspan=1 rowspan=1><textarea cols=100 rows=5 name='' class=fondoAmarillo onmouseout='grabarObservaciongnral( \"$wfecha1\",\"$wing\",\"$whis\",\"$wemp_pmla\",this)'>".traer_observaciones_historia($whis, $wing, $wfecha1)."</textarea></td>";
            echo  '<td><textarea cols="30" rows="5" readonly = "readonly">'.traer_obser_anterior_hist($whis, $wing).'</textarea></td>
                    </tr>
                  </tbody>

                </table>';
            echo '<center><span class="textoMedio">Guardar observaciones&nbsp;&nbsp;</span><input id="wconfdisp" type="checkbox" name="wconfdisp" </center>';

        }

        // FUNCION QUE TRAE LAS OBSERVACIONES DEL DIA DE HOY Y LOS IMPRIME EN EL TEXTAREA DE CADA EXAMEN
        function traer_nombre_examen($wcodexam) {

            global $conex;
            global $whce;


            $query =   "SELECT Codigo, Descripcion "
                   ."   FROM ".$whce."_000047"
                   ."  WHERE Codigo = '".$wcodexam."'"
                   ."    AND Estado = 'on'";

            $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $num = mysql_num_rows($res);

			if($num > 0){

				$row = mysql_fetch_array($res);
				$nombre_examen = $row['Descripcion'];

			}else{

				$query =    " SELECT Codigo, Descripcion "
						   ."   FROM ".$whce."_000017"
						   ."  WHERE Codigo = '".$wcodexam."'"
						   ."    AND Estado = 'on'";
				$res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				$row = mysql_fetch_array($res);
				$nombre_examen = $row['Descripcion'];


			}

            return $nombre_examen;


        }
		
		//Consulta si un centro de costos es de urgencias
		function ccoUrgencias($conex, $wbasedato, $servicio){
		
			$es = false;

			$q = "SELECT Ccourg
					FROM ".$wbasedato."_000011
				   WHERE Ccocod = '".$servicio."' ";

			$err = mysql_query($q,$conex);
			$num = mysql_num_rows($err);

			if($num>0)
			{
				$rs = mysql_fetch_array($err);

				($rs['Ccourg'] == 'on') ? $es = true : $es = false;
			}

			return $es;
		}
		
		function consultarUbicacionUrg($conex, $wmovhos, $whis, $wing){
			
			$cod_hab = "Urg";
			
			$q = "  SELECT Habcpa, Aredes  
				      FROM ".$wmovhos."_000020, ".$wmovhos."_000169 
				 	 WHERE Habzon = Arecod 
					   AND Habhis = '".$whis."'
					   AND Habing = '".$wing."'";			
			$err = mysql_query($q,$conex);
			$rs = mysql_fetch_array($err);
			
			if($rs['Habcpa'] != ''){
				$cod_hab = $rs['Aredes']."<br>".$rs['Habcpa'];
			}
			
			return $cod_hab;
			
		}
		
        function elegir_historia($wturno)
        {

            global $conex;
            global $wmovhos;
            global $wemp_pmla;
            global $key;
            global $wtabcco;
            global $wcco;
            global $wresp;
            global $whab;
            global $whis;
            global $wing;
            global $wpac;
            global $wdpa;
            global $weda;
            global $wfec;
            global $wfecha1;
            global $whora1;
            global $wmed;
            global $wbasedato;
			global $whce;
			global $sala;

            $wcco1 = explode("-", $wcco);
			$wir_a_ordenes = ir_a_ordenes($wemp_pmla, $wcco, 'ir_a_ordenes');
			
			$tiempo_alta_definitiva = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tiempoReactivacionAltas');
			

			if($wcco1[0] == ""){ $wcco1[0] = "%";}
            //Seleccionar CENTRO DE COSTOS
            echo "<center><table>";

			$query =  " SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom "
                   ."   FROM ".$wtabcco.", ".$wmovhos."_000011 "
                   ."  WHERE ".$wtabcco.".ccocod = ".$wmovhos."_000011.ccocod "
                   ."    AND (ccohos = 'on' or ccourg = 'on')
				         AND Ccoemp = '".$wemp_pmla."'
					ORDER BY Ccocod";
            $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
            $num = mysql_num_rows($res);

			echo "<tr class=fila1>";
			echo "<td class=fila1><font size=4>Seleccione la Unidad:</font></td>";			
			echo "<td align=left class=fila1><select name='wcco' id='wcco' onchange='buscar_zonas();'>";
			echo "<option value='%'>Todos</option>";

             for($i = 1; $i <= $num; $i++)
               {
                $row = mysql_fetch_array($res);
                if(isset($wcco) && $row[0]==$wcco)
                        echo "<option selected value='".$row[0]."'>".$row[0]."-".$row[1]."</option>";
                         else
                                 echo "<option value='".$row[0]."'>".$row[0]."-".$row[1]."</option>";
               }
            echo "</select>";			
            echo "<div id='wfecha' style='display:none'>".$wfecha1."</div><div id='whora' style='display:none;'>".$whora1."</div></td>";
			echo "</tr>";			
			
			if($sala == ''){
				$ocultar_zonas = "display:none;";
			}
			
			echo "<tr>";
			echo "<td class='fila1 td_zonas' style='".$ocultar_zonas."'><font size=4>Seleccionar zona:</font></td><input type='hidden' id='validar_zonas' value='".$sala."'>";
			echo "<td class='fila1 td_zonas' style='".$ocultar_zonas."'><span id='select_zonas'></td>";
			echo "</tr>";
			
			
			$wurgencias = ccoUrgencias($conex,$wmovhos, $wcco);
				
			switch(1){
			
				case ($wurgencias):					
					
					$q_res = "SELECT Ingres AS nit_responsable, Ingnre AS ent_responsable
							   FROM root_000036, root_000037, ".$wmovhos."_000018, ".$wmovhos."_000016,
									".$wmovhos."_000011, ".$whce."_000022
							  WHERE ubihis = orihis
								AND ubiing = oriing
								AND oriori = '".$wemp_pmla."'
								AND oriced = pacced
								AND oritid = pactid
								AND ubiald != 'on'
								AND ubisac = '".trim($wcco1[0])."'
								AND ubihis = inghis
								AND ubiing = inging
								AND ccocod = ubisac
								AND ccoest = 'on'
								AND mtrhis = ubihis
								AND mtring = ubiing
								$filtro_zonas
						  GROUP BY nit_responsable
						  ORDER BY ent_responsable ";	
			
					
					break;
					
					default:					
					
					$q_res = " SELECT * FROM (SELECT habcod, habhis, habing, pacno1, pacno2, pacap1, pacap2, pacnac, pactid, pacced, t16.ingres AS nit_responsable, t16.Ingnre AS ent_responsable, t17.Eyrsor AS cco_origen, ubiptr, ubialp, habord"
						   ."   FROM ".$wmovhos."_000020,".$wmovhos."_000018
						   LEFT JOIN ".$wmovhos."_000016 AS t16 ON ( ubihis = t16.Inghis AND  ubiing = t16.Inging)
						   LEFT JOIN ".$wmovhos."_000017 AS t17 ON ( ubihis = t17.Eyrhis AND  ubiing = t17.Eyring), root_000036, root_000037 "
						   ."  WHERE habali != 'on' "            //Que no este para alistar
						   ."    AND habdis != 'on' "            //Que no este disponible, osea que este ocupada
						   ."    AND habcod  = ubihac "
						   ."    AND ubisac  LIKE '%".$wcco1[0]."%' "
						   ."    AND ubihis  = orihis "
						   ."    AND ubiing  = oriing "
						   ."    AND ubiald != 'on' "
						   ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
						   ."    AND oriced  = pacced "
						   ."    AND oritid  = pactid "
						   ."    AND habhis  = ubihis "
						   ."    AND habing  = ubiing "
						   ."	$filtro_zonas		  "
						   ." UNION"
							//Este union agrega los pacientes que tienen muerte en on.
						   ." SELECT ubihan, ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, pacnac, pactid, pacced, t16.ingres AS nit_responsable, t16.Ingnre AS ent_responsable, t17.Eyrsor AS cco_origen, ubiptr, ubialp, 0 habord"
						   ."   FROM ".$wmovhos."_000018
						   LEFT JOIN ".$wmovhos."_000016 AS t16 ON ( ubihis = t16.Inghis AND  ubiing = t16.Inging)
						   LEFT JOIN ".$wmovhos."_000017 AS t17 ON ( ubihis = t17.Eyrhis AND  ubiing = t17.Eyring), root_000036, root_000037 "
						   ."  WHERE ubihis  = orihis "
						   ."    AND ubiing  = oriing "
						   ."    AND ubisac  LIKE '%".$wcco1[0]."%' "
						   ."    AND ubimue  = 'on' "
						   ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
						   ."    AND oriced  = pacced "
						   ."    AND oritid  = pactid "
						   ."    AND Ubiptr != 'on' "             //Solo los pacientes que no esten siendo trasladados
						   ."    AND Ubiald != 'on' "             //Que no este en Alta Definitiva
						   ."  ) as t "
						   ."  GROUP BY nit_responsable"
						   ."  ORDER BY ent_responsable";	
					
					break;
			}

				   
			$res_resp = mysql_query($q_res, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
			$num_resp = mysql_num_rows($res_resp);

            echo "<tr class=fila1>
                  <td align=center colspan='1' rowspan='2'><font size=4>Empresa Responsable: </font></td>";
            echo "<td align=left colspan='1' rowspan='2'><select name='wresp' id='wresp'>";
            echo "<option value='%'>Todos</option>";

             for($j = 1; $j <= $num_resp; $j++)
               {
                $row_resp = mysql_fetch_array($res_resp);

                if(isset($wresp) and $row_resp['nit_responsable']==$wresp){
                        echo "<option selected value='".$row_resp['nit_responsable']."'>".$row_resp['ent_responsable']."</option>";
						}
                         else{
                              echo "<option value='".$row_resp['nit_responsable']."'>".$row_resp['ent_responsable']."</option>";
							  }

               }
            echo "</select>";
            echo "</td></tr>";
            echo "</table>";

			echo "<table>";
			echo "<tr><td ><input type=button value='Consultar' onclick='enter1()'></td></tr>";
			echo "</table>";

			if($wresp == "%"){

			$filtro_responsable = "    AND ingres  LIKE '%%%'";

			}else{

			$filtro_responsable = "    AND ingres  = '".$wresp."'";

			}
			
			$fecha_actual = date('Y-m-d H:i:s');
			$fecha_inicial = strtotime ( '-'.$tiempo_alta_definitiva.' hour' , strtotime ( $fecha_actual ) ) ;
			$fecha_inicial = date ( 'Y-m-d H:i:s' , $fecha_inicial );
			
			switch(1){
			
				case ($wurgencias):
					
					if($sala != '%' and $sala != ''){
							$filtro_zonas = "	AND mtrsal = '".$sala."'";							
						}					
					
					
					if($sala == '%' and $sala != ''){
						
						$pacientes_urgencias_24horas = "	UNION
															SELECT 'Urg' as habcod, ubihis as 'habhis', ubiing as 'habing', pacno1, pacno2, pacap1, pacap2, pacnac, pactid, pacced, Ingnre AS ent_responsable, ubiptr, ubialp, ingres, ubiald, ubifad, ubihad, 'on' as Altadef, ubisac
															   FROM root_000036, root_000037, ".$wmovhos."_000018, ".$wmovhos."_000016,
																	".$wmovhos."_000011, ".$whce."_000022
															  WHERE ubihis = orihis
																AND ubiing = oriing
																AND oriori = '".$wemp_pmla."'
																AND oriced = pacced
																AND oritid = pactid
																AND ubiald = 'on'
																AND ubisac = '".trim($wcco1[0])."'
																AND ubihis = inghis
																AND ubiing = inging
																AND ccocod = ubisac
																AND ccoest = 'on'
																AND mtrhis = ubihis
																AND mtring = ubiing
																$filtro_responsable 									
																AND concat(".$wmovhos."_000018.Ubifad,' ',".$wmovhos."_000018.Ubihad ) between '$fecha_inicial' and  '$fecha_actual'";
							
					}
					
					$query = "SELECT * FROM (
								SELECT 'Urg' as habcod, ubihis as 'habhis', ubiing as 'habing', pacno1, pacno2, pacap1, pacap2, pacnac, pactid, pacced, Ingnre AS ent_responsable, ubiptr, ubialp, ingres, ubiald, ubifad, ubihad, '' as AltaDef, ubisac
								   FROM root_000036, root_000037, ".$wmovhos."_000018, ".$wmovhos."_000016,
										".$wmovhos."_000011, ".$whce."_000022
								  WHERE ubihis = orihis
									AND ubiing = oriing
									AND oriori = '".$wemp_pmla."'
									AND oriced = pacced
									AND oritid = pactid
									AND ubiald != 'on'
									AND ubisac = '".trim($wcco1[0])."'
									AND ubihis = inghis
									AND ubiing = inging
									AND ccocod = ubisac
									AND ccoest = 'on'
									AND mtrhis = ubihis
									AND mtring = ubiing
									$filtro_responsable 
									$filtro_zonas
									$pacientes_urgencias_24horas
									) as t ";	
					
					break;	
					
					default:
					
					if($sala != '%' and $sala != ''){

						$filtro_zonas = "	AND habzon = '".$sala."'";
					}
					
					//Selecciono todos los pacientes del servicio seleccionado
				     $query= "SELECT * FROM ( SELECT habcod, habhis, habing, pacno1, pacno2, pacap1, pacap2, pacnac, pactid, pacced, t16.Ingnre AS ent_responsable, t17.Eyrsor AS cco_origen, ubiptr, ubialp, habord, ingres, ubifad, ubihad, ubiald, ubimue, ubisac"
						   ."   FROM ".$wmovhos."_000020,".$wmovhos."_000018
						   LEFT JOIN ".$wmovhos."_000016 AS t16 ON ( ubihis = t16.Inghis AND  ubiing = t16.Inging)
						   LEFT JOIN ".$wmovhos."_000017 AS t17 ON ( ubihis = t17.Eyrhis AND  ubiing = t17.Eyring), root_000036, root_000037 "
						   ."  WHERE habcco LIKE '%".$wcco1[0]."%'"
						   ."    AND habali != 'on' "            //Que no este para alistar
						   ."    AND habdis != 'on' "            //Que no este disponible, osea que este ocupada
						   ."    AND habcod  = ubihac "
						   ."    AND ubihis  = orihis "
						   ."    AND ubiing  = oriing "
						   ."    AND ubiald != 'on' "
						   ."    $filtro_responsable "
						   ."    $filtro_zonas "
						   ."    AND ubisac  LIKE '%".$wcco1[0]."%'"
						   ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
						   ."    AND oriced  = pacced "
						   ."    AND oritid  = pactid "
						   ."    AND habhis  = ubihis "
						   ."    AND habing  = ubiing "
						   ."  GROUP BY 1,2,3,4,5,6,7 "
						   ." UNION"
							//Este union agrega los pacientes que tienen muerte en on.
						   ." SELECT ubihac, ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, pacnac, pactid, pacced, t16.Ingnre AS ent_responsable, t17.Eyrsor AS cco_origen, ubiptr, ubialp, 0 habord, ingres, ubifad, ubihad, ubiald, ubimue, ubisac"
						   ."   FROM ".$wmovhos."_000018
						   LEFT JOIN ".$wmovhos."_000016 AS t16 ON ( ubihis = t16.Inghis AND  ubiing = t16.Inging)
						   LEFT JOIN ".$wmovhos."_000017 AS t17 ON ( ubihis = t17.Eyrhis AND  ubiing = t17.Eyring), root_000036, root_000037 "
						   ."  WHERE ubihis  = orihis "
						   ."    AND ubiing  = oriing "
						   ."    AND ubimue  = 'on' "
						   ."    $filtro_responsable "
						   ."    AND ubisac  = '".$wcco1[0]."'"
						   ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
						   ."    AND oriced  = pacced "
						   ."    AND oritid  = pactid "
						   ."    AND Ubiptr != 'on' "             //Solo los pacientes que no esten siendo trasladados
						   ."    AND Ubiald != 'on' "             //Que no este en Alta Definitiva
						   ."  UNION "
						   ." SELECT ubihac, ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, pacnac, pactid, pacced, t16.Ingnre AS ent_responsable, t17.Eyrsor AS cco_origen, ubiptr, ubialp, 1000 habord, ingres, ubifad, ubihad, ubiald, ubimue, ubisac"
						   ."   FROM ".$wmovhos."_000018 
						   LEFT JOIN ".$wmovhos."_000016 AS t16 ON ( ubihis = t16.Inghis AND  ubiing = t16.Inging)
						   LEFT JOIN ".$wmovhos."_000017 AS t17 ON ( ubihis = t17.Eyrhis AND  ubiing = t17.Eyring), root_000036, root_000037 "
						   ."  WHERE ubihis  = orihis "
						   ."    AND ubiing  = oriing "
						   ."    $filtro_responsable "
						   ."    AND ubisac  = '".$wcco1[0]."'"
						   ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
						   ."    AND oriced  = pacced "
						   ."    AND oritid  = pactid "
						   ."    AND Ubiptr != 'on' "             //Solo los pacientes que no esten siendo trasladados
						   ."    AND Ubiald = 'on' "             //Que no este en Alta Definitiva
						   ."    AND concat(".$wmovhos."_000018.Ubifad,' ',".$wmovhos."_000018.Ubihad ) between '$fecha_inicial' and  '$fecha_actual') AS t"
						   ."  GROUP BY 1,2,3,4,5,6,7 "
						   ."  ORDER BY 15, 1 ";
					
					break;
					
			}
			
			$res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
            $num = mysql_num_rows($res);

            if ($num > 0 and $wresp != "")
            {
                
			    echo "";
                echo "<div id='msjEspere' style='display:none;'><img src='../../images/medical/ajax-loader5.gif'/>Por favor espere un momento...</div><center><table>";

				$path_disp_espec = "http://mx.lasamericas.com.co/matrix/movhos/procesos/Consul_disponibilidad_especialidad.php?wemp_pmla=01";
				$path_cirugia = "http://mx.lasamericas.com.co/matrix/tcx/reportes/ListaG.php?wemp_pmla=".$wemp_pmla."&empresa=tcx&TIP=0";
			    echo "<tr>";
				echo "<td></td>";
				echo "<td style='cursor: pointer;' class='fila1' onclick='ejecutar(".chr(34).$path_cirugia.chr(34).")'><font size=2>Consultar Turnos <br> de Cirugia</td>";
				echo "<td style='cursor: pointer;' class='fila1' colspan=2 onclick='ejecutar(".chr(34).$path_disp_espec.chr(34).")'><font size=2>Consultar Disponibilidad Especialidades</td>";
				echo "<td colspan=2><table><tr><td class='articuloNuevoPerfil' align='center'><p class='msg textoNomenclatura'>Ordenes Autorizadas</p></td><td class='fondoNaranja' align='center'><p class='msg textoNomenclatura'>Ordenes Prioritarias sin autorizar</p></td><td class='fondoGris' align='center'><p><b>Alta definitiva <br> últ ".$tiempo_alta_definitiva." horas</b></p></td></tr></table></td>";
				echo "</tr>";

                echo "<tr class=encabezadoTabla>";
				echo "<th colspan=1 rowspan=2 style='empty-cells: hide;'><font size=4>Ordenes</font></th>";               
                echo "<th colspan=1 rowspan=2><font size=4>Hab.</font></th>";
                echo "<th colspan=1 rowspan=2><font size=4>Historia</font></th>";
                echo "<th colspan=1 rowspan=2><font size=4>Paciente</font></th>";
				if($wresp == '%'){
					echo "<th colspan=1 rowspan=2><font size=4>Empresa Responsable</font></th>";
				}
                echo "<th colspan=1 rowspan=2><font size=4>Médico(s) Tratante(s)</font></th>";
                echo "<th colspan=5 rowspan=1><font size=4>Pendientes</font></th>";
                echo "</tr>";
                echo "<tr class=encabezadoTabla>";
                echo "<td colspan='2' rowspan='1'><font size=3>Transfusiones</font></td>";
                echo "<td colspan='1' rowspan='1'><font size=3>Evoluciones</font></td>";
                echo "<td colspan='2' rowspan='1'><font size=3>Procedimientos</font></td>";
                echo "</tr>";

                for ($i = 1; $i <= $num; $i++)
                {
                    $row = mysql_fetch_array($res);
                    
                    if ($wcco1[0] == '%')
                    {
                        $wir_a_ordenes = ir_a_ordenes_his_ing($row[1], $row[2]);
                    }                    

                    if (is_integer($i / 2))
                        $wclass = "fila1";
                    else
                        $wclass = "fila2";

                    $whab = $row[0];
                    $whis = $row[1];
                    $wing = $row[2];
                    $wpac = $row[3]." ".$row[4]."<br>".$row[5]." ".$row[6];
                    $wentidad_responsable = $row['ent_responsable'];
                    $ingres = $row['ingres'];
                    $fecha_ald = $row['ubifad'];
                    $hora_ald = $row['ubihad'];
                    $alta_def = $row['ubiald'];

                    $wnac = $row[7];
                    //Calculo la edad
                    $wfnac = (integer) substr($wnac, 0, 4) * 365 + (integer) substr($wnac, 5, 2) * 30 + (integer) substr($wnac, 8, 2);
                    $wfhoy = (integer) date("Y") * 365 + (integer) date("m") * 30 + (integer) date("d");
                    $weda = (($wfhoy - $wfnac) / 365);
                    if ($weda < 1)
                        $weda = number_format(($weda * 12), 0, '.', ',')."<b> Meses</b>";
                    else
                        $weda = number_format($weda, 0, '.', ',')." Años";
					
					$mensaje_muerte = "";
                    $wtid = $row[8];                                      //Tipo documento paciente
                    $wdpa = $row[9];                                      //Documento del paciente
                    $wptr = $row['ubiptr'];                                      //Proceso de traslado
                    $walp = $row['ubialp'];
					$wcedula = $row['pacced'];
					$wtip_doc = $row['pactid'];
					$wald = $row['ubiald'];
					$control_alta_def = $row['AltaDef'];

                    if ($wptr=="on")    //Si la historia esta en proceso de traslado
                        {
                        $wclass="colorAzul4";
                        }

                    if ($walp=="on")    //Si la historia esta en proceso de traslado
                        {
                        $wclass="fondoAmarillo";
                        }
						
					if($control_alta_def == 'on'){
						$wclass="fondoGris";
					}
					
                    //==========================================================================================================
					$path_hce = "../../hce/procesos/HCE_iFrames.php?empresa=".$whce."&origen=".$wemp_pmla."&wdbmhos=".$wmovhos."&whis=".$whis."&wing=".$wing."&accion=F&ok=0&wcedula=".$wdpa."&wtipodoc=".$wtid."";
					$origen = "Ver";
                    echo "<tr class=".$wclass.">";

					if($wir_a_ordenes != 'on'){

					$path_destino = "/matrix/movhos/procesos/generarKardex.php?wemp_pmla=".$wemp_pmla."&waccion=b&whistoria=".$whis."&wingreso=".$wing."&wfecha=".$wfecha1."&editable=off&et=on";

					}else{

					$origen = "Ver";
					$path_destino = "/matrix/hce/procesos/ordenes.php?wemp_pmla=".$wemp_pmla."&wcedula=".$wcedula."&wtipodoc=".$wtip_doc."&hce=on&programa=autorizacionesPendientes&editable=off&et=on";

					}

                    $path_ent_y_rec_pac = "/matrix/movhos/reportes/rep_ent_y_rec_pac.php?wemp_pmla=".$wemp_pmla."&whis=".$whis."&wing=".$wing."";
                    echo "<td align=center nowrap><a href=# onclick='ejecutar(".chr(34).$path_destino.chr(34).")'><b>$origen</b></a></td>";
					
					if($wurgencias){
						
						$whab = consultarUbicacionUrg($conex, $wmovhos, $whis, $wing);
						
					}
					
					if($alta_def == 'on'){
						
						$whab = 'Alta definitiva <font size=1>'.$fecha_ald.' '.$hora_ald.'</font>';
					}
					
					
					if($row['ubimue'] == 'on'){
						
							$mensaje_muerte = "<font color='red'>Muerte sin alta definitiva en el sistema.</font>";
					}
					
                    echo "<td align=center style='cursor: pointer;' onclick='ejecutar(".chr(34).$path_ent_y_rec_pac.chr(34).")'><b>".$whab."<br>".$mensaje_muerte."</b></td>";
                    echo "<td align=center style='cursor: pointer;' onclick='ejecutar(".chr(34).$path_hce.chr(34).")'>".$whis."-".$wing."</td>";
                    echo "<td align=left nowrap><b>".$wpac."</b></td>";
					
					if($wresp == '%'){
						echo "<td align=left nowrap><b>".$wentidad_responsable."</b></td>";
					}
					
                    $wmed = traer_medico_tte($whis, $wing, $wfecha1, $j);
                    if ($wmed == "Sin Médico")
                        {         //Si no lo encontro en el Kardex actual, lo busco en el Kardex de día anterior
                            $dia = time() - (1 * 24 * 60 * 60);   //Resta un dia (2*24*60*60) Resta dos y //asi...
                            $wayer = date('Y-m-d', $dia); //Formatea dia

                            $wmed = traer_medico_tte($whis, $wing, $wayer, $j);
                        }
                    echo "<td align=left  >".$wmed."</td>";

                    // IMPRIME LA CANTIDAD DE TRANSFUSIONES PENDIENTES POR GRABAR
                    $transfusiones = traer_transfusiones($wmovhos, $whis, $wing, $wemp_pmla);

                    if ($transfusiones>0)
                        {

                        if ($wptr=="on")    //Si la historia esta en proceso de traslado
                            {
                            $wclass="colorAzul4";
                            }
                            else
                            {
                                $wclass="fondoCrema";
                            }

                             if ($walp=="on")    //Si la historia esta en proceso de traslado
                                {
                                $wclass="fondoAmarillo";
                                }
                            echo "<td align=center id='trans_dato_".$i."' class = ".$wclass."><b class='blink'>&nbsp;&nbsp;&nbsp;".$transfusiones."</b></td>";
                            echo "<td align=center class = ".$wclass.">";

                            // ASIGNA AL CHECKBOX EL VALOR DE LAS GLUCOMETRIAS PENDIENTES POR GRABAR
                            echo "<div id='bloquet_".$i."' class = ".$wclass." style='display: block'><input id='$i' value=".$transfusiones." type='checkbox' onClick ='grabartransf(\"$i\",\"$wmovhos\",\"$wemp_pmla\",\"$whis\",\"$wing\",\"T\",this)'></div></td>";
                        }
                        else
                        {
                             if ($wptr=="on")    //Si la historia esta en proceso de traslado
                                {
                                $wclass="colorAzul4";
                                }
                                else
                                {
                                    $wclass="fondoCrema";
                                }

                               if ($walp=="on")    //Si la historia esta en proceso de traslado
                                {
                                $wclass="fondoAmarillo";
                                }
                             echo "<td align=center class = ".$wclass."><b></b></td>";
                             echo "<td align=center class = ".$wclass."><b></b></td>";
                        }



                    //TRAE LAS EVOLUCIONES HECHAS A LOS PACIENTES
                    $wevoluciones = 0;
                    $array_evoluciones = traer_evoluciones($wmovhos, $whis, $wing, $wemp_pmla, $wevoluciones);
                    $warreglo_evoluciones = base64_encode(serialize($array_evoluciones));
                    $wid = $whis."-".$wing; //Identificador del td hidden
                    if ($wevoluciones == 0)
                        {
                        echo "<td></td>";
                        }
                    else
                        {
                        echo "<td align='center' style='cursor:pointer;' id='E_".$whis."-".$wing."' onClick='fnMostrar_especialista(\"".$wemp_pmla."\", \"".$whis."\", \"".$wing."\", \"".$wid."\", \"".$wevoluciones."\", \"".$wid_td."\");'><input type='HIDDEN' id='arreglo_".$wid."' VALUE='".$warreglo_evoluciones."'><b class='blink'>".$wevoluciones."</b>";
                        }

                    // IMPRIME LA CANTIDAD DE PROCEDIMIENTOS PENDIENTES POR PACIENTE
                    list($totalpendientes, $grabadosnumber, $clase_estado_examenes) = consultarPendientesPaciente($conex, $wmovhos, $whis, $wing, $ingres);

                    //Verifica si la secretaria leyó una observacion, en caso de haber leido almenos uno el numero dejara de titilar
                    if ($totalpendientes > 0 and $grabadosnumber == '')
                        {
                        echo "<td align=center width=80px class='".$clase_estado_examenes."'><b class='blink'>".$totalpendientes."</b></td>";
                        }
                       elseif($totalpendientes > 0 and $grabadosnumber > 0)
                       {
                        echo "<td align=center width=80px class='".$clase_estado_examenes."'><b>".$totalpendientes."</b></td>";
                       }
                    else
                    {
                        echo "<td align=left><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td>";
                    }

					$path_procedimientos = "/matrix/movhos/procesos/autorizaciones_pendientes5.php?wemp_pmla=".$wemp_pmla."&key=".$key."&whis=".$whis."&wing=".$wing."&wcco=".$wcco."&wfec=".$wfecha1."";
                    // ENLACE VER PARA ACCEDER A LA INFORMACION DE LOS PENDIENTES POR PACIENTE Y SUS EXAMENES
                    echo "<td align=center onclick='ejecutar(".chr(34).$path_procedimientos.chr(34).")' style='cursor:pointer;'><b><u>Ver</u></b></td>";

                    echo "</tr>";

                }
            }
            else
            {
				if($num == 0){
                echo "<br><b>NO HAY HABITACIONES OCUPADAS</b><br>";
				}

            }
            echo "</table>";


        }
	
		function consultarEstadosExamenesRol(){
			
			global $wmovhos;
			global $conex;

			$coleccion = array();

			$q = "SELECT Eexcod,Eexdes,Eexapa,Eexcan,Eexrea,Eexpen,Eexaut,Eexenf,Eexeau
					FROM ".$wmovhos."_000045
				   WHERE Eexord = 'on'
					 AND Eexest = 'on'";
			$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$num = mysql_num_rows($res);

			$cont1 = 0;
			while($cont1 < $num)
			{
				$info = mysql_fetch_array($res);

				$reg = new RegistroGenericoDTO();

				$reg->codigo = $info['Eexcod'];
				$reg->descripcion = $info['Eexdes'];
				$reg->accion_med_proc_agrup = $info['Eexapa'];
				$reg->est_cancelada = $info['Eexcan'];
				$reg->est_realizado = $info['Eexrea'];
				$reg->est_pendiente = $info['Eexpen'];
				$reg->est_autorizado = $info['Eexaut'];
				$reg->enfermeria = $info['Eexenf'];
				$reg->estado_autorizado = $info['Eexeau'];
				
				$cont1++;

				$coleccion[$info['Eexcod']] = $reg;
			}

			return $coleccion;
		}

        // FUNCION QUE PERMITE SABER CUANTOS PROCEDIMIENTOS SE ENCUENTRAN PENDIENTES
        function consultarPendientesPaciente($conex, $wmovhos, $whis, $wing, $ingres )
            {

                global $whce;
                global $wfecha1;
				global $wemp_pmla;
				global $wcco;
				
				$estados_examenes = consultarEstadosExamenesRol();				
				
				$wbasedatoCliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
				
				$wir_a_ordenes = ir_a_ordenes($wemp_pmla, $wcco, 'ir_a_ordenes');
				
				$sqlInfoSegRes = "SELECT Empcod, Empnit, Emptem, Empnom, Tardes, Placod, Plades, Emptar
									FROM ".$wbasedatoCliame."_000205 AS A INNER JOIN ".$wbasedatoCliame."_000024 AS B ON(A.Resnit = B.Empcod)
											INNER JOIN ".$wbasedatoCliame."_000025 AS C ON(Emptar = Tarcod) LEFT JOIN ".$wbasedatoCliame."_000153 AS D ON (Respla = Placod)
									 WHERE Reshis = '".$whis."'
									   AND Resing = '".$wing."'
									   AND Resnit = '".$ingres."'";
				$resInfoSegRes = mysql_query($sqlInfoSegRes, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfoSegRes):</b><br>".mysql_error());
				if($rowInfoSegRes = mysql_fetch_array($resInfoSegRes))
				{
					$infoSegRes['codEntidad'] 	= $rowInfoSegRes['Empcod'];
					$infoSegRes['entidad']		= $rowInfoSegRes['Empnom'];
					$infoSegRes['nitEntidad'] 	= $rowInfoSegRes['Empnit'];
					$infoSegRes['tarifa'] 		= $rowInfoSegRes['Tardes'];
					$infoSegRes['tipoEmp']		= $rowInfoSegRes['Emptem'];
					$infoSegRes['plan']			= $rowInfoSegRes['Placod'];
					$infoSegRes['descripPlan']	= $rowInfoSegRes['Plades'];

					// --> Variables para obtener si un insumo o procedimiento requiere autorizacion
					$codEnt 	= $infoSegRes['codEntidad'];
					$nitEnt 	= $infoSegRes['nitEntidad'];
					$tipEnt 	= $infoSegRes['tipoEmp'];
					$planEmp 	= $infoSegRes['plan'];
					$tarifaEmp 	= $rowInfoSegRes['Emptar'];
				}				
				
                $query= " SELECT A.Fecha_data, A.Hora_data, Ekahis, Ekaing, Ekaest, Ekafec, Ekaobs, Ekafes, Ekacod, A.id
                            FROM ". $wmovhos ."_000050 A, ". $wmovhos ."_000045 B
                           WHERE Ekahis = '".$whis."'
                             AND Ekaing = '".$wing."'
                             AND Ekaest = B.Eexcod
                             AND Eexaut = 'on'
                             AND Ekafec = '".$wfecha1."'
                           UNION
                          SELECT A.Fecha_data, A.Hora_data, Ekahis, Ekaing, Ekaest, Ekafec, Ekaobs, Ekafes, Ekacod, Ekaido as id
                            FROM ". $wmovhos ."_000061 A, ". $wmovhos ."_000045
                           WHERE Ekahis = '".$whis."'
                             AND Ekaing = '".$wing."'
                             AND Ekaest = Eexcod
                             AND Eexaut = 'on'
                             AND Ekafec = '".$wfecha1."'
                        ORDER BY Ekafes DESC";
                $res = mysql_query($query, $conex) or die(mysql_errno()." - Error en el query $sql - ".mysql_error());
				$numK = mysql_num_rows($res);

			  $query1 ="  SELECT A.Medico, A.Fecha_data, A.Hora_data, A.Ordfec, A.Ordhor, A.Ordhis, A.Ording, A.Ordtor, A.Ordnro, A.Ordobs, A.Ordesp, A.Ordest, A.Ordusu, A.Ordfir, A.Seguridad, B.id as id_encabezado, B.Medico, B.Fecha_data, B.Hora_data, B.Dettor,
										 B.Detnro, B.Detcod, B.Detesi, B.Detrdo, B.Detfec, B.Detjus, B.Detest, B.Detite, B.Detusu, B.Detfir, B.Deture, B.Seguridad, B.id as id_detalle, Detpri
						  FROM ".$whce."_000027 A, ".$whce."_000028 B, ".$wmovhos."_000045 C
						 WHERE Ordtor = Dettor
						   AND Ordnro = Detnro
						   AND A.Ordhis = '".$whis."'
						   AND A.Ording = '".$wing."'
						   AND B.Detesi = C.Eexcod
						   AND C.Eexaut = 'on'
						   AND A.Ordest = 'on'
						   AND B.Detest = 'on'
					  ORDER BY B.Detfec DESC";
			  $res1 = mysql_query($query1, $conex) or die(mysql_errno()." - Error en el query $sql - ".mysql_error());
			  $roword = mysql_num_rows($res1);
			  
			  $sinaut = 0;
			  
			  while($row_proc = mysql_fetch_array($res1)){
				  
				  // --> Obtener clasificacion del procedimiento
					$sqlCups = "SELECT * FROM 
								( SELECT Codcups
									  FROM ".$whce."_000017
									 WHERE Codigo = '".$row_proc['Detcod']."'
									   AND Nuevo = 'on'
									 UNION
									SELECT Codcups
									  FROM ".$whce."_000047
									 WHERE Codigo = '".$row_proc['Detcod']."') as t ";
					$resCups = mysql_query($sqlCups, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlClasPro):</b><br>".mysql_error());
					if($rowCups = mysql_fetch_array($resCups))
						$codCups = $rowCups['Codcups'];
					
					// --> Obtener clasificacion del procedimiento
					$sqlClasPro = " SELECT Procpg
									  FROM ".$wbasedatoCliame."_000103
									 WHERE Procod = '".$codCups."'";
					$resClasPro = mysql_query($sqlClasPro, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlClasPro):</b><br>".mysql_error());
					if($rowClasPro = mysql_fetch_array($resClasPro))
						$clasifiPro = $rowClasPro['Procpg'];
					else
						$clasifiPro = '*';
					
					$pideAutorizacion 	= procedimientoSeDebeAutorizar($codEnt, $nitEnt, $tipEnt, $planEmp, $tarifaEmp, $clasifiPro, $codCups);
					
					if(!$pideAutorizacion){
						
						$sinaut++;
					}
						
					if( $estados_examenes[ $row_proc['Detesi'] ]->est_pendiente == 'on' and $estados_examenes[ $row_proc['Detesi'] ]->enfermeria == 'on' and $row_proc['Detpri'] == 'on' ){
						
						$pendiente_prioritario++;	
					
					}
					
					if( $estados_examenes[ $row_proc['Detesi'] ]->est_autorizado == 'on' and $estados_examenes[ $row_proc['Detesi'] ]->enfermeria == 'off' ){
						
						$autorizado++;	
						
					}
				  
			  }
							
               //CONSULTA QUE PERMITE IDENTIFICAR CUANTOS PENDIENTES YA TIENE OBSERVACIONES EL DIA DE HOY
                $querypendg =  "  SELECT COUNT(Dmohis) AS nroobserva"
                                   ."   FROM ".$wmovhos."_000121 "
                                   ."  WHERE Fecha_data = '".$wfecha1."'"
                                   ."    AND Dmohis = '".$whis."'"
                                   ."    AND Dmoing = '".$wing."'";
                $res_pendgrab = mysql_query($querypendg, $conex) or die("Error: ".mysql_errno()." - en el query: ".$querypendg." - ".mysql_error());
                $row_pendgrab = mysql_fetch_array($res_pendgrab);
                $grabados= $row_pendgrab['nroobserva'];
                $grabadosnumber = (int)$grabados;

                $totalpendientes = ($numK + $roword) - $sinaut;
				
				if($pendiente_prioritario > 0){
					
					$clase_estado_examenes = "fondoNaranja";
					
				}else{		
					
					if($autorizado == $totalpendientes ){
						
						$clase_estado_examenes = "articuloNuevoPerfil";
						
					}
				}
				
				
                return array ($totalpendientes, $grabadosnumber, $clase_estado_examenes);
            }

		//---------------------------------------------------------------------------------------------------------------------
		//	--> Funcion que valida si un procedimiento requiere autorizacion
		//		Jerson trujillo, 2016-03-30
		//---------------------------------------------------------------------------------------------------------------------
		// function procedimientoSeDebeAutorizar($codEnt, $nitEnt, $tipEnt, $planEmp, $clasifProc, $procedimiento, $tarifaEmp)
		// {
			// global $conex;
			// global $wemp_pmla;
						
			// $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
			
			// // --> Generar query combinado para saber si alguna regla le aplica al articulo, para que este deba ser autorizado
			// $variables = array();
			// // --> Procedimiento
			// $variables['Paucop']['combinar'] 	= true;
			// $variables['Paucop']['valor'] 		= $procedimiento;
			// // --> Clasificacion
			// $variables['Paucla']['combinar'] 	= true;
			// $variables['Paucla']['valor'] 		= $clasifProc;
			// // --> Plan de empresa
			// $variables['Paupla']['combinar'] 	= true;
			// $variables['Paupla']['valor'] 		= $planEmp;
			// // --> Tarifa
			// $variables['Pautar']['combinar'] 	= true;
			// $variables['Pautar']['valor'] 		= $tarifaEmp;
			// // --> Entidad
			// $variables['Paucem']['combinar'] 	= true;
			// $variables['Paucem']['valor'] 		= $codEnt;
			// // --> Nit Entidad
			// $variables['Paunit']['combinar'] 	= true;
			// $variables['Paunit']['valor'] 		= $nitEnt;
			// // --> Tipo de empresa
			// $variables['Pautem']['combinar'] 	= true;
			// $variables['Pautem']['valor'] 		= $tipEnt;
			// // --> Estado
			// $variables['Pauest']['combinar'] 	= false;
			// $variables['Pauest']['valor'] 		= 'on';			
						
			// // --> Obtener query
			// $sqlDebeAuto = generarQueryCombinado($variables, $wbasedato."_000260");
			// $resDebeAuto = mysql_query($sqlDebeAuto, $conex) or die("ERROR EN QUERY MATRIX (sqlDebeAuto): ".mysql_error());

			// if($rowDebeAuto = mysql_fetch_array($resDebeAuto))
			// {
				// $sqlAut = "SELECT Paupau
							  // FROM ".$wbasedato."_000260
							 // WHERE id = '".$rowDebeAuto['id']."'";
				// $resAut = mysql_query($sqlAut, $conex) or die("ERROR EN QUERY MATRIX (sqlAut): ".mysql_error());
				// $rowAut = mysql_fetch_array($resAut);
				// if($rowAut['Paupau'] == 'on')
					// return true;
				// else
					// return false;
			// }
			// else
				// return false;
		// }
		
		
        // FUNCION QUE IMPRIME LOS PENDIENTES DE LOS PACIENTES, ADEMAS DE IMPRIMIR LOS TEXTAREA PARA QUE LA SECRETARIA
        // INGRESE LAS OBSERVACIONES DE CADA EXAMEN Y LAS GENERALES
        function consultarPendi($whis, $wing, $orden, $orden2)
         {
            global $conex;
            global $wmovhos;
            global $wpac;
            global $wing;
            global $whab;
            global $wfecha1;
            global $wemp_pmla;
            global $whce;
            global $wcco;
						
			$wir_a_ordenes = ir_a_ordenes($wemp_pmla, $wcco, 'ir_a_ordenes');
			$wbasedatoCliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
			$estados_examenes = consultarEstadosExamenesRol();
			
           $wcolor = "CCCCFF";
           
		   $wmed = traer_medico_tte($whis, $wing, $wfecha1, $j);
		   
		   if ($wmed == "Sin Médico")
			{         //Si no lo encontro en el Kardex actual, lo busco en el Kardex de día anterior
				$dia = time() - (1 * 24 * 60 * 60);   //Resta un dia (2*24*60*60) Resta dos y //asi...
				$wayer = date('Y-m-d', $dia); //Formatea dia
				$wmed = traer_medico_tte($whis, $wing, $wayer, $j);
			}

           // Traer datos del paciente
           $q = "SELECT pacno1, pacno2, pacap1, pacap2, pactid, pacced, Ubisac, Ubihac, Ubisan, Ubihan, Ingnre, Ingres
                   FROM root_000036, root_000037, ".$wmovhos."_000018, ".$wmovhos."_000016
                  WHERE oriced = pacced
                    AND Ubihis = Orihis
                    AND Ubiing = Oriing
                    AND Inghis = Ubihis
                    AND Inging = Ubiing
                    AND orihis = '".$whis."'
                    AND oriing = '".$wing."'
                    AND oriori = '".$wemp_pmla."'";
           $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
           $winfo = mysql_fetch_array($res);
			
		   $doc = $winfo['pacced'];
           $wpac = $winfo['pacno1']." ".$winfo['pacno2']." ".$winfo['pacap1']." ".$winfo['pacap2'];
           $whab = $winfo['Ubihac'];
		   $ent_responsable = $winfo['Ingnre'];
           $codEntRes = $winfo['Ingres'];
		   
		   if($whab == ''){
			   
			   $q =    "  SELECT Aredes, Habcpa 
					        FROM ".$wmovhos."_000020, ".$wmovhos."_000169 
					 	   WHERE habhis = '".$whis."'
					 		 AND habing = '".$wing."'
							 AND habzon = Arecod ";
			   $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			   $winfo = mysql_fetch_array($res);
			   
			   $whab = $winfo['Aredes']."<br>".$winfo['Habcpa'];
			   
		   }

           $query= "      SELECT A.Fecha_data, A.Hora_data, Ekahis, Ekaing, Ekaest, Ekafec, Ekaobs, Ekafes, Ekacod, A.id
                            FROM ". $wmovhos ."_000050 A, ". $wmovhos ."_000045 B
                           WHERE Ekahis = '".$whis."'
                             AND Ekaing = '".$wing."'
                             AND Ekaest = B.Eexcod
                             AND Eexaut = 'on'
                             AND Ekafec = '".$wfecha1."'
                           UNION
                          SELECT A.Fecha_data, A.Hora_data, Ekahis, Ekaing, Ekaest, Ekafec, Ekaobs, Ekafes, Ekacod, Ekaido as id
                            FROM ". $wmovhos ."_000061 A, ". $wmovhos ."_000045
                           WHERE Ekahis = '".$whis."'
                             AND Ekaing = '".$wing."'
                             AND Ekaest = Eexcod
                             AND Eexaut = 'on'
                             AND Ekafec = '".$wfecha1."'
                        ORDER BY Ekafes DESC";
            $res = mysql_query($query, $conex);
            $num = mysql_num_rows($res);

            //CONSULTA QUE PERMITE IDENTIFICAR SI EL KARDEX PARA LA HISTORIA E INGRESO SE ENCUENTRAN ABIERTOS (Off = Abierto, On = Cerrado)
            $querygk =  " SELECT Fecha_data, Karhis, Karing, Kargra "
                               ."   FROM ".$wmovhos."_000053 "
                               ."  WHERE Fecha_data = '".$wfecha1."'"
                               ."    AND Karhis = '".$whis."'"
                               ."    AND Karing = '".$wing."'";
            $res_grab = mysql_query($querygk, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
            $row_grab = mysql_fetch_array($res_grab);			
			 
             //datos del paciente
             $query_p =  "    SELECT  m16.Ingres AS cod_responsable, m16.Ingnre AS ent_responsable, m17.Eyrsor AS cco_origen, r37.Oriced AS doc
                                FROM  ".$wmovhos."_000017 AS m17
                           LEFT JOIN  root_000037 AS r37 ON (r37.Orihis = m17.Eyrhis AND r37.Oriing = m17.Eyring)
                           LEFT JOIN  ".$wmovhos."_000016 AS m16 ON (m16.Inghis = m17.Eyrhis AND m16.Inging = m17.Eyring)
                               WHERE  Orihis = '".$whis."'
                                 AND  Oriing = '".$wing."'";
            $res_p = mysql_query($query_p, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query_p." - ".mysql_error());
            $num_p = mysql_num_rows($res_p);
           
            if ($num_p > 0)
            {
                $row = mysql_fetch_array($res_p);               
                $cco_origen = $row['cco_origen'];
            }			
			
			$querygk =  " SELECT * "
					   ."   FROM ".$wmovhos."_000053 "
					   ."  WHERE Fecha_data = '".$wfecha1."'"
					   ."    AND Karhis = '".$whis."'"
					   ."    AND Karing = '".$wing."'";
            $res_grab = mysql_query($querygk, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
            $row_grab = mysql_fetch_array($res_grab);
			
			
			$query_aut =  " SELECT Ingord 
					          FROM ".$wbasedatoCliame."_000101 
					         WHERE Inghis = '".$whis."'
					           AND Ingnin = '".$wing."'";
            $res_aut = mysql_query($query_aut, $conex) or die("Error: ".mysql_errno()." - en el query_aut: ".$query_aut." - ".mysql_error());
            $row_aut = mysql_fetch_array($res_aut);			
			$cod_aut = $row_aut['Ingord'];
			
            $query1 ="  SELECT A.Medico, A.Fecha_data, A.Hora_data, A.Ordfec, A.Ordhor, A.Ordhis, A.Ording, A.Ordtor, A.Ordnro, A.Ordobs, A.Ordesp, A.Ordest, A.Ordusu, A.Ordfir, A.Seguridad, A.id as id_encabezado, B.Medico, B.Fecha_data, B.Hora_data, B.Dettor,
									     B.Detnro, B.Detcod, B.Detesi, B.Detrdo, B.Detfec, B.Detjus, B.Detest, B.Detite, B.Detusu, B.Detfir, B.Deture, B.Seguridad, B.id as id_detalle, Detpri
                          FROM ".$whce."_000027 A, ".$whce."_000028 B, ".$wmovhos."_000045 C
                         WHERE Ordtor = Dettor
                           AND Ordnro = Detnro
                           AND A.Ordhis = '".$whis."'
                           AND A.Ording = '".$wing."'
                           AND B.Detesi = C.Eexcod
                           AND C.Eexaut = 'on'
						   AND A.Ordest = 'on'
                           AND B.Detest = 'on'
                      ORDER BY Detpri DESC, A.Fecha_data DESC, A.Hora_data DESC ";
             $res1 = mysql_query($query1, $conex) or die(mysql_errno()." - Error en el query $sql - ".mysql_error());
             $numord=mysql_num_rows($res1);
			
			
			// --> 	Obtener el plan actual del paciente
			// 		Jerson trujillo, 2015-04-16.
			
			$sqlInfoSegRes = "SELECT Empcod, Empnit, Emptem, Empnom, Tardes, Placod, Plades, Emptar
								  FROM ".$wbasedatoCliame."_000205 AS A INNER JOIN ".$wbasedatoCliame."_000024 AS B ON(A.Resnit = B.Empcod)
										INNER JOIN ".$wbasedatoCliame."_000025 AS C ON(Emptar = Tarcod) LEFT JOIN ".$wbasedatoCliame."_000153 AS D ON (Respla = Placod)
								 WHERE Reshis = '".$whis."'
								   AND Resing = '".$wing."'
								   AND Resnit = '".$codEntRes."'";
			$resInfoSegRes = mysql_query($sqlInfoSegRes, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfoSegRes):</b><br>".mysql_error());
			if($rowInfoSegRes = mysql_fetch_array($resInfoSegRes))
			{
				$infoSegRes['codEntidad'] 	= $rowInfoSegRes['Empcod'];
				$infoSegRes['entidad']		= $rowInfoSegRes['Empnom'];
				$infoSegRes['nitEntidad'] 	= $rowInfoSegRes['Empnit'];
				$infoSegRes['tarifa'] 		= $rowInfoSegRes['Tardes'];
				$infoSegRes['tipoEmp']		= $rowInfoSegRes['Emptem'];
				$infoSegRes['plan']			= $rowInfoSegRes['Placod'];
				$infoSegRes['descripPlan']	= $rowInfoSegRes['Plades'];

				// --> Variables para obtener si un insumo o procedimiento requiere autorizacion
				$codEnt 	= $infoSegRes['codEntidad'];
				$nitEnt 	= $infoSegRes['nitEntidad'];
				$tipEnt 	= $infoSegRes['tipoEmp'];
				$planEmp 	= $infoSegRes['plan'];
				$tarifaEmp  = $rowInfoSegRes['Emptar'];
				
			}
			
			
			$sqlPlanAct = "SELECT Respla
							 FROM ".$wbasedatoCliame."_000205
							WHERE Reshis = '".$whis."'
							  AND Resing = '".$wing."'
							  AND Resnit = '".$codEntRes."'
							  AND Resest = 'on'";
			$resPlanAct = mysql_query($sqlPlanAct, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlPlanAct):</b><br>".mysql_error());
			if($rowPlanAct = mysql_fetch_array($resPlanAct))
				$planActualPac = $rowPlanAct['Respla'];
			else
				$planActualPac = '*';
			
			// --> 	Obtener los planes relacionados a la entidad responsable
			// 		Jerson trujillo, 2015-04-16.
			$optionsSelect = "";
			$sqlPlanes = "SELECT Placod, Plades
							FROM ".$wbasedatoCliame."_000153
						   WHERE (Plaemp = '".$codEntRes."' OR Plaemp = '*')
							 AND Plaest = 'on'
			";
			$resPlanes = mysql_query($sqlPlanes, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlPlanes):</b><br>".mysql_error());
			while($rowPlanes = mysql_fetch_array($resPlanes))
			{
				$optionsSelect.= "<option ".(($planActualPac == $rowPlanes['Placod']) ? "selected=selected" : "")." value='".$rowPlanes['Placod']."'>".$rowPlanes['Plades']."</option>";
			}

            echo "  <div id='encbz' align='center'>
                    <table>
                        <tr class='fila1'>
                            <th><font size='3'>Habitación</font></th>
                            <th><font size='3'>Documento</font></th>
                            <th><font size='3'>Historia</font></th>
                            <th><font size='3'>Nombre</font></th>
                            <th><font size='3'>Servicio origen</font></th>
                            <th><font size='3'>Médico tratante</font></th>
                            <th><font size='3'>Código Autorización</font></th>
                            <th><font size='3'>Entidad responsable</font></th>
                            <th><font size='3'>Plan</font></th>
                        </tr>
                        <tr class='fila2'>
                            <td align='center'><font size='3'><b>".$whab."</b></font></td>
                            <td align='center'><font size='3'>".$doc."</font></td>
                            <td align='center'><font size='3'>".$whis."-".$wing."</font></td>
                            <td align='center'><font size='3'><b>".$wpac."&nbsp&nbsp</b></font></td>
                            <td align='center'><font size='3'>".buscar_centro_costo($wemp_pmla,$cco_origen)."</font></td>
                            <td align='center'><font size='3'><b>".$wmed."&nbsp&nbsp</b></font></td>
                            <td align='center'><font size='3'><b>".$cod_aut."&nbsp&nbsp</b></font></td>
                            <td align='center'><font size='3'>".$ent_responsable."</font></td>
                            <td align='center'>
								<select id='selectPlanPaciente' style='font-size:12px' onChange='actualizarPlanPaciente(\"".$whis."\", \"".$wing."\", \"".$codEntRes."\")'>
									".$optionsSelect."
								</select>
							</td>
                        </tr>
                    </table>
                    <br/>
                    </div>";

          if ($num > 0 or $numord > 0)
             {
                $i=1;
                echo "<table border=0 ALIGN=CENTER>";
                echo "<tr class='encabezadoTabla'>";
                echo "<td align='center'><b>FECHA<br>HORA</b></td>";
                echo "<td align='center' width = 150><b>UNIDAD QUE REALIZA</b></td>";
                echo "<td align='center' width = 300><b>EXAMENES</b></td>";
                echo "<td align='center' width = 100><b>REALIZAR EN LA FECHA</b></td>";
                echo "<td align='center'><b>ESTADO</b></td>";
                echo "<td align='center'>OBSERVACIONES</td>";
                echo "<td align='center'>OBSERVACIONES ANTERIORES (incluye hoy)</td>";
                echo "</tr>";

				$estadokardex= $row_grab['Kargra'];
				if($estadokardex=="off")
				  {
				  $edicion = 'readonly';
				  $mensaje = '<font size="-3" style="color: red;">El Kardex esta siendo editado</font>';
				  }
				 else
				 {
				  $edicion = '';
				  $mensaje = '';
				 }


				while ($row = mysql_fetch_array($res))
					{

						if (is_int($i / 2))
						{
						 $class = 'fila1';
						}
						else
						{
						 $class = 'fila2';
						}
						echo "<tr>";

						//Consulta el centro de costos.
						$sql_condi =   " SELECT Cconom "
									  ."   FROM ".$wmovhos."_000011 "
									  ."  WHERE Ccocod = '".$row['Ekacod']."'";
						$res_condi = mysql_query( $sql_condi, $conex ) or die( mysql_errno()." - Error en el query $sql_condi - ".mysql_error() );
						$row_condi = mysql_fetch_array( $res_condi );

						$wser_relacionado = $row_condi['Cconom'];

						if($row_condi['Cconom'] == ''){

						//Consulta el tipo de orden.
						$sql_tip_ord = "SELECT Codigo, Descripcion
										  FROM {$whce}_000015
										 WHERE estado = 'on'
										   AND Codigo = '".$row['Ekacod']."'";
						$res_tip_ord = mysql_query( $sql_tip_ord, $conex ) or die( mysql_errno()." - Error en el query $sql_tip_ord - ".mysql_error() );
						$row_tip_ord = mysql_fetch_array( $res_tip_ord );

						$wser_relacionado = $row_tip_ord['Descripcion'];

						}
						$westado_registro = $row['Ekaest'];
						$wfechadataexamen = $row['Fecha_data'];
						$whoradataexamen = $row['Hora_data'];

						echo "<td class =".$class." align=center>".$wfechadataexamen."<br>".$whoradataexamen."</td>";
						echo "<td class =".$class." align=center>".$wser_relacionado."</td>";
						$wobservac = $row['Ekaobs'];
						echo "<td class =".$class.">".$wobservac."<br>".$mensaje."</td>";
						echo "<td class =".$class." align='center'>".$row['Ekafes']."</td>";
						
						$texto_bitacora = $row_tip_ord['Descripcion']."*".$datos_examen."*".$roword['Detfec'];
						
						$wexam = $row['Ekacod'];
						$wfechagk = $row['Ekafec'];
						$wid_a = $row['id'];
						$wcontrol_ordenes = "";

						$sql_est = "SELECT Eexcod, Eexdes
							          FROM ".$wmovhos."_000045
							         WHERE Eexaut = 'on'";
						$res_est = mysql_query( $sql_est, $conex ) or die( mysql_errno()." - Error en el query $sql_est - ".mysql_error() );
						$estado = "";
						$westado_actual = $row['Ekaest'];

						$estado1 = "<select id='estado_$wid_a' onchange='cambiar_estado_examen(\"$wemp_pmla\",\"$wfecha1\",\"$wexam\",\"$wing\",\"$whis\",\"$wfechadataexamen\",\"$whoradataexamen\", \"$wfechagk\", \" \", \" \",this, \"$wid_a\", \"$wcco\", \"$whce\",\"$wcontrol_ordenes\", \"$wobservac\", \"$westado_actual\", \"$wid_a\")'>";
						while($row_est = mysql_fetch_array( $res_est )){

							$seleccionar = "";

							if($row_est['Eexcod'] == $westado_registro){

							$seleccionar = "selected";

							}

							$estado.= "<option value='".$row_est['Eexcod']."' $seleccionar>".$row_est['Eexdes']."</option>";

						}
						$estado2 = "</select>";

						echo "<td class =".$class." align='center'>".$estado1.$estado.$estado2."</td>";
						echo "<td align=left><textarea ID='wobscc[".$i."]' rows=3 cols=30 ".$edicion." class=fondoAmarillo onChange='grabarObservacion(\"$wemp_pmla\",\"$wfecha1\",\"$wexam\",\"$wing\",\"$whis\",\"$wfechadataexamen\",\"$whoradataexamen\", \"$wfechagk\", \" \", \" \",this, \"$wid_a\", \"$texto_bitacora\")'></textarea></td>";
						echo "<td align=left bgcolor=".$wcolor."><div style='overflow:auto; height:80px;'>".traer_observaciones_anteriores_exam($whis, $wing, $wexam, $wfechadataexamen, $whoradataexamen, $wfechagk, $wordennro, $wordite, $wid_a)."</div></td>";
						echo "</tr>";


						$i++;
					}

                //DATOS DE LA CONSULTA A LAS TABLAS HCE_000027 y HCE_000028 PARA EXTRAER LOS PENDIENTES DE LA HISTORIA E INGRESO EN ORDENES
                while ($roword = mysql_fetch_array($res1))
                {
					$texto_prioritario = "";
					$westado_registro = "";
					
					$sqlCups = "  SELECT * FROM 
								( SELECT Codcups
									  FROM ".$whce."_000017
									 WHERE Codigo = '".$roword['Detcod']."'
									   AND Nuevo = 'on'
									 UNION
									SELECT Codcups
									  FROM ".$whce."_000047
									 WHERE Codigo = '".$roword['Detcod']."') as t ";
					$resCups = mysql_query($sqlCups, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlClasPro):</b><br>".mysql_error());
					if($rowCups = mysql_fetch_array($resCups))
						$codCups = $rowCups['Codcups'];
					
					// --> Obtener clasificacion del procedimiento
					$sqlClasPro = " SELECT Procpg
									  FROM ".$wbasedatoCliame."_000103
									 WHERE Procod = '".$codCups."'";
					$resClasPro = mysql_query($sqlClasPro, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlClasPro):</b><br>".mysql_error());
					if($rowClasPro = mysql_fetch_array($resClasPro))
						$clasifiPro = $rowClasPro['Procpg'];
					else
						$clasifiPro = '*';					
					
					$pideAutorizacion 	= procedimientoSeDebeAutorizar($codEnt, $nitEnt, $tipEnt, $planEmp, $tarifaEmp, $clasifiPro, $codCups);
					$pideAutorizacion	= ($pideAutorizacion) ? '' : 'style="display:none;"';					
					
                    if (is_int($i / 2))
                    {
                     $class = 'fila1';
                    }
                    else
                    {
                     $class = 'fila2';
                    }
					
					
					//Si el procedimiento es prioritario se marca naranja.
					if($roword['Detpri'] == 'on'){	
						
						if($estados_examenes[$roword['Detesi']]->estado_autorizado == 'on'){
							$class = "articuloNuevoPerfil";
						}else{
							$class = "fondoNaranja";
						}
													
						$texto_prioritario = "Prioritario";
					}					
					
                    echo "<tr $pideAutorizacion class =".$class.">";
					//Consulta el tipo de orden.
					$sql_tip_ord = "SELECT Codigo, Descripcion
								      FROM {$whce}_000015
							         WHERE estado = 'on'
									   AND Codigo = '".$roword['Dettor']."'";
					$res_tip_ord = mysql_query( $sql_tip_ord, $conex ) or die( mysql_errno()." - Error en el query $sql_tip_ord - ".mysql_error() );
					$row_tip_ord = mysql_fetch_array( $res_tip_ord );

					$wnombre_examen = traer_nombre_examen($roword['Detcod']);
                    $wfechadataexamen = $roword['Fecha_data'];
                    $whoradataexamen = $roword['Hora_data'];
                    $wjustificacion = $roword['Detjus'];
					
					$observacion = "";
					
					if(trim($wjustificacion) != ''){
						
						$observacion = "<br><hr><b>Justificaci&oacute;n: </b>".$wjustificacion;
					}

					echo "<td align=center>".$wfechadataexamen."<br>".$whoradataexamen."</td>";
					echo "<td align='center'>".$row_tip_ord['Descripcion']."</td>";
                    echo "<td>".$wnombre_examen." (".$codCups.")".$observacion."<br>".$mensaje."</td>";
                    echo "<td align='center'>".$roword['Detfec']."</td>";

                    $westado_registro = $roword['Detesi'];

					$wexam = $roword['Dettor'];
                    $wfechagk = $roword['Detfec'];
                    $wordennro = $roword['Detnro'];
                    $wordite = $roword['Detite'];
                    $wordite = $roword['Detite'];
                    $wordid_detalle = $roword['id_detalle'];
					$wcontrol_ordenes = 'on';
					
					$datos_examen = $wnombre_examen." (".$codCups.")";
					
					$texto_bitacora = $row_tip_ord['Descripcion']."*".$datos_examen."*".$roword['Detfec'];
					
					$sql_est = "SELECT Eexcod, Eexdes
							      FROM ".$wmovhos."_000045
							     WHERE Eexaut = 'on'";
					$res_est = mysql_query( $sql_est, $conex ) or die( mysql_errno()." - Error en el query $sql_est - ".mysql_error() );

					$estado = "";

					$wid_encabezado = $roword['id_encabezado'];

					$estado1 = "<select id='estado_$wordid_detalle$wordite' onchange='cambiar_estado_examen(\"$wemp_pmla\",\"$wfecha1\",\"$wexam\",\"$wing\",\"$whis\",\"$wfechadataexamen\",\"$whoradataexamen\", \"$wfechagk\", \"$wordennro\", \"$wordite\",this, \"$wordid_detalle\", \"$wcco\", \"$whce\", \"$wcontrol_ordenes\",\"$wnombre_examen\",\"$westado_registro\",\"$wordid_detalle$wordite\")'>";

					while($row_est = mysql_fetch_array( $res_est )){

						$seleccionar = "";

						if($row_est['Eexcod'] == $westado_registro){

						$seleccionar = "selected";

						}

						$estado .= "<option value='".$row_est['Eexcod']."' $seleccionar>".$row_est['Eexdes']."</option>";

					}

					$estado2 = "</select>";

                    echo "<td align='center'>".$estado1.$estado.$estado2."<br><b>".$texto_prioritario."</b></td>";

                    echo "<td align=left><textarea ID='wobscc[".$i."]' rows=3 cols=30 class=fondoAmarillo onchange='grabarObservacion(\"$wemp_pmla\",\"$wfecha1\",\"$wexam\",\"$wing\",\"$whis\",\"$wfechadataexamen\",\"$whoradataexamen\", \"$wfechagk\", \"$wordennro\", \"$wordite\",this, \"$wid_encabezado\", \"$texto_bitacora\")'></textarea></td>";

                    echo "<td align=left bgcolor=".$wcolor."><div style='overflow:auto; height:80px;'>".traer_observaciones_anteriores_exam($whis, $wing, $wexam, $wfechadataexamen, $whoradataexamen, $wfechagk, $wordennro, $wordite, $wid_encabezado)."</div></td>";

                    echo "</tr>";


                    $i++;
                }


                echo "</table><br>";

                observa_general($whis, $wing, $wfecha1);

            }else
                {
                 //FUNCION QUE IMPRIME LOS TEXTAREA PARA LAS OBSERVACIONES GENERALES Y LAS OBSERVACIONES ANTERIORES

                observa_general($whis, $wing, $wfecha1);
                }
        }

        // FUNCION QUE EXTRAE LA INFORMACION DEL KARDEX EN LA TABLA 000053 CON LA HISTORIA
        function query_kardex($whis, $wing, $wfec, &$res)
         {
            global $conex;
            global $wmovhos;

           $query = " SELECT Fecha_data, karobs, karter, karint, karcip "
                   ."   FROM ".$wmovhos."_000053"
                   ."  WHERE karhis = '".$whis."'"
                   ."    AND karing = '".$wing."'"
                   ."    AND Fecha_data = '".$wfec."'"
                   ."    AND karest = 'on'"
                   ."    AND karcco = '*'"
                 ." ORDER BY Fecha_data DESC";

            $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $num = mysql_num_rows($res);

            if ($num > 0)
             {

                $i=1;

                echo "<table align=center>
                      <tbody>
                      <tr class='fila1'>
                      <td align='center'>
                      <font size='4' textaling= center>PENDIENTES: ".$wfec."</font>
                      </td>
                      </tbody>
                      </table>";

                echo "<table border=0 ALIGN=CENTER>";
                echo "<tr class='encabezadoTabla'>";
                echo "<td align='center'>&nbsp;OBSERVACIONES DEL KARDEX</b></td>";
                echo "</tr>";
                while ($row = mysql_fetch_array($res))
                    {

                        if (is_int($i / 2))
                        {
                         $class = 'fila1';
                        }
                        else
                        {
                         $class = 'fila2';
                        }
                        echo "<tr>";

                        echo "<td class =".$class." align='center'><textarea readonly='' rows=4 cols='100'>".$row['karobs']."</textarea></td>";
                        echo "</tr>";
                        $i++;
                    }
                        echo "</table>";
              }

         }


 if (isset($consultaAjax))
            {
            switch($consultaAjax)
                {
					// -->	Actualiza el plan asociado al paciente
					// 		Jerson trujillo, 2015-04-16
					case 'actualizarPlanPaciente':
					{
						$wbasedatoCliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');

						$sqlActPlan = "UPDATE ".$wbasedatoCliame."_000205
										  SET Respla = '".$plan."'
										WHERE Reshis = '".$historia."'
										  AND Resing = '".$ingreso."'
										  AND Resnit = '".$responsable."'
						";
						mysql_query($sqlActPlan, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlActPlan):</b><br>".mysql_error());

						break;
					}

            case 'observacion':
                {
                    echo grabarObservacion($wemp_pmla, $wmovhos, $wfec, $wexam, $wing, $wfechadataexamen, $whoradataexamen, $wfechagk, $whis, $wordennro, $wordite, $wtexto, $wid, $texto_bitacora);
                }
                    break;
            case 'observageneral':
                {
                    echo grabarObservaciongnral($wmovhos, $wfec, $wing, $whis, $wemp_pmla, $wtexto);
                }
                break;
            case 'grabargluco':
                {
                    echo grabargluco($wmovhos, $whis, $wing, $wtipo, $wvalor, $wfechapantalla, $whorapantalla);
                }
                break;
            case 'grabarnebus':
                {
                    echo grabarnebus($wmovhos, $whis, $wing, $wtipo, $wvalor, $wfechapantalla, $whorapantalla);
                }
            case 'grabaroxi':
                {
                    echo grabaroxi($wmovhos, $whis, $wing, $wtipo, $wvalor, $wfechapantalla, $whorapantalla);
                }
            case 'grabartransf':
                {
                    echo grabartransf($wmovhos, $whis, $wing, $wtipo, $wvalor, $wfechapantalla, $whorapantalla);
                }
                break;

            case 'mostrar_detalle':
			{
				mostrar_detalle($wemp_pmla, $whis, $wing, $wfecha, $whora);
				break;
			}

            case 'mostrar_detalle_especialista':

			{
				mostrar_detalle_especialista($wemp_pmla, $whis, $wing, $wfecha, $whora, $array_profesores, $evoluciones);
				break;
			}

             case 'grabarinsumos':
			{
				grabarinsumos($wmovhos, $whis, $wing, $wtipo, $wvalor, $wfechapantalla, $whorapantalla );
				break;
			}

			case 'cambiar_estado_examen':
                {
                    echo cambiar_estado_examen($wemp_pmla, $wmovhos, $wfec, $wexam, $wing, $wfechadataexamen, $whoradataexamen, $wfechagk, $whis, $wordennro, $wordite, $westado, $wid, $wcco, $whce, $wcontrol_ordenes, $wtexto_examen, $westado_registro);
                }
                    break;
                default : break;
                }
            return;
            }

?>
<head>
    <title>Autorizaciones pendientes</title>

    <style type="text/css">
        A   {text-decoration: none;color: #000066;}
        .tipo3V{color:#000066;background:#dddddd;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo3V:hover {color: #000066; background: #999999;}
		
	.textoNomenclatura
	{		
		 font-size: 11px;
		 font-weight: bold;
	}
    </style>

</head>
<script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.js"></script>
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
<script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>

<script type="text/javascript">
	
	function buscar_zonas(){

		var wcco = $('#wcco').val();

		$.ajax({
			url: "gestionEnfermeria.php",
			type: "POST",
			data:{
				wemp_pmla		: $("#wemp_pmla").val(),
				operacion 		: 'filtrarzonas',
				consultaAjax 	: '',
				wcco			: wcco
			},
			dataType: "json",
			async: false,
			success:function(data_json) {

				if (data_json.error == 1)
				{
					jAlert( data_json.mensaje, 'ALERTA' );
					return;
				}
				else{

					if(data_json.nro_zonas > 0){
					$(".td_zonas").show();
					$("#select_zonas").html(data_json.html);
					}else{

					$(".td_zonas").hide();
					$("#select_zonas").html("");
					}

				}
			}

		});

	}
	
	//-----------------------------------------------------
	//	-->	Actualiza el plan de la entidad del paciente
	//		Jerson Trujillo, 2015-04-16
	//-----------------------------------------------------
	function actualizarPlanPaciente(historia, ingreso, responsable)
    {
        $.post("autorizaciones_pendientes5.php",
		{
			consultaAjax    :	'actualizarPlanPaciente',
			wemp_pmla       : 	$("#wemp_pmla").val(),
			historia		: 	historia,
			ingreso			: 	ingreso,
			responsable		: 	responsable,
			plan			: 	$("#selectPlanPaciente").val()
		}
		,function(data){

		});
    }

	function grabarinsumos(basedato, historia, ingreso, tipo, valor, id_td )
    {
    	$('#div_resultado').html('<div align="center"><img src="../../images/medical/ajax-loader5.gif"/></div>');

        var wfechapantalla = document.getElementById('wfecha');
        var datofecha = wfechapantalla.innerHTML;

        var whorapantalla = document.getElementById('whora');
        var datohora = whorapantalla.innerHTML;


        $.post("autorizaciones_pendientes5.php",
            {
                consultaAjax    :'grabarinsumos',
                wemp_pmla       : $("#wemp_pmla").val(),
                wmovhos         : basedato,
                whis         	: historia,
                wing         	: ingreso,
                wtipo         	: tipo,
                wvalor         	: valor,
                wfechapantalla	: datofecha,
                whorapantalla	: datohora
            }
            ,function(data_json) {
            	if (data_json.error == 1)
            	{
            		alert(data_json.mensaje);
            		enter();
            	}
            	else
            	{
                    alert(data_json.mensaje);
					$("#E_"+id_td).html(" ");
                    cerraremergente();
            	}

            },
            "json"
        );
    }


    //ventanana emergente
    function fnMostrar_especialista(wemp_pmla, historia, ingreso, id, evoluciones)
    {
        $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });

        var whorapantalla = document.getElementById('whora');
        var datohora = whorapantalla.innerHTML;

        var wfechapantalla = document.getElementById('wfecha');
        var datofecha = wfechapantalla.innerHTML;

		$.post("autorizaciones_pendientes5.php",
				{

                    consultaAjax:   	'mostrar_detalle_especialista',
					wemp_pmla:      	wemp_pmla,
					whis:           	historia,
					wing:           	ingreso,
					wfecha:             datofecha,
                    whora:              datohora,
                    array_profesores:   $("#arreglo_"+id).val(),
					evoluciones:		evoluciones

				}
				,function(data) {
					$.blockUI({ message: data,
							css: {  left: 	'30%',
								    top: 	'10%',
								    width: 	'40%',
                                    height: 'auto'
								 }
					  });
				}
			);
	}

    //ventanana emergente
    function fnMostrar(wemp_pmla, historia, ingreso)
    {
        $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });

        var whorapantalla = document.getElementById('whora');
        var datohora = whorapantalla.innerHTML;

        var wfechapantalla = document.getElementById('wfecha');
        var datofecha = wfechapantalla.innerHTML;

		$.post("autorizaciones_pendientes5.php",
				{

                    consultaAjax:   	'mostrar_detalle',
					wemp_pmla:      	wemp_pmla,
					whis:           	historia,
					wing:           	ingreso,
					wfecha:             datofecha,
                    whora:              datohora

				}
				,function(data) {
					$.blockUI({ message: data,
							css: {  left: 	'30%',
								    top: 	'10%',
								    width: 	'40%',
                                    height: 'auto'
								 }
					  });
				}
			);
	}

    function cerraremergente()
    {

       $.unblockUI();
      //$('#pendientes').submit();
    }


    //FUNCION QUE PERMITE GENERAR UNA VENTANA EMERGENTE CON UN PATH ESPECIFICO
    function ejecutar(path)
    {
    window.open(path,'','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
    }

    function deshabilitar_teclas()
    //document.onkeydown = function()
    {
        if(window.event && window.event.keyCode == 116 )
        {
            window.event.keyCode = 505;
        }
        if(window.event && window.event.keyCode == 505)
        {
            return false;
        }
    }


    function enter(val)
   {
        document.pendientes.orden.value='desc';
    document.pendientes.orden2.value=val;
    document.pendientes.submit();
   }

   function enter1()
    {

	 var responsable = $("#wresp").val();

	 if(responsable == ""){
	 alert("Debe seleccionar un responsable");
	 return;
	 }

     document.pendientes.submit();
    }

   function enter2(val)
   {
    document.pendientes.orden.value='asc';
    document.pendientes.orden2.value=val;
    document.pendientes.submit();
   }

   function cerrarVentana()
    {
        window.close()
    }



      // FUNCION AJAX QUE PERMITE LA GRABACION IN SITU DE LAS OBSERVACIONES GENERALES DE LA SECRETARIA
   function grabarObservaciongnral(wfec, wing, whis, wemp_pmla, campo)
      {

            var wmovhos = document.getElementById( "wmovhos" ).value;

            var texto = campo.value;
        var parametros = "consultaAjax=observageneral&wmovhos="+wmovhos+"&wfec="+wfec+"&wing="+wing+"&whis="+whis+"&wemp_pmla="+wemp_pmla+"&wtexto="+texto;
        //alert(parametros);

                try
          {
            var ajax = nuevoAjax();
            ajax.open("POST", "autorizaciones_pendientes5.php",false);
            ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            ajax.send(parametros);
            //alert(ajax.responseText);
            }catch(e){ alert(e) }
      }


     // FUNCION AJAX QUE PERMITE LA GRABACION IN SITU DE LAS OBSERVACIONES DE CADA EXAMEN POR PARTE DE LA SECRETARIA
     function grabarObservacion(wemp_pmla, wfec, wexam, wing, whis, wfechadataexamen, whoradataexamen, wfechagk, wordennro, wordite, campo, wid, texto_bitacora)
      {

        var wmovhos = document.getElementById( "wmovhos" ).value;
        var texto = campo.value;

		$.post("autorizaciones_pendientes5.php",
				{
					consultaAjax:   	'observacion',
					wemp_pmla:      	wemp_pmla,
					wmovhos:           	wmovhos,
					wexam:           	wexam,
					wfec:           	wfec,
					wing:    			wing,
					whis:         		whis,
					wfechadataexamen:   wfechadataexamen,
					whoradataexamen:	whoradataexamen,
					wfechagk : 			wfechagk,
					wordennro : 		wordennro,
					wordite : 			wordite,
					wtexto : 			texto,
					wid : 				wid,
					texto_bitacora : 	texto_bitacora
				}
				,function(data) {}
			);
		}

	function cambiar_estado_examen(wemp_pmla, wfec, wexam, wing, whis, wfechadataexamen, whoradataexamen, wfechagk, wordennro, wordite, campo, wid, wcco, whce, wcontrol_ordenes, wtexto_examen, westado_registro, wid_select)
      {

        var wmovhos = document.getElementById( "wmovhos" ).value;
        var westado = $("#estado_"+wid_select).val();

		$.post("autorizaciones_pendientes5.php",
				{
					consultaAjax:   	'cambiar_estado_examen',
					wemp_pmla:      	wemp_pmla,
					wmovhos:           	wmovhos,
					wexam:           	wexam,
					wfec:           	wfec,
					wing:    			wing,
					whis:         		whis,
					wfechadataexamen:   wfechadataexamen,
					whoradataexamen:	whoradataexamen,
					wfechagk : 			wfechagk,
					wordennro : 		wordennro,
					wordite : 			wordite,
					westado : 			westado,
					wid : 				wid,
					wcco:				wcco,
					wcontrol_ordenes:	wcontrol_ordenes,
					wtexto_examen:		wtexto_examen,
					westado_registro:	westado_registro
				}
				,function(data) {}
			);
		}

       // FUNCION AJAX QUE PERMITE LA GRABACION DE LAS GLUCOMETRRIAS EN LA TABLA movhos_000119
     function grabargluco(id, wmovhos, wemp_pmla, whis, wing, wtipo, campo)
      {

            var fila = campo.parentNode.parentNode.parentNode;
            //alert( fila );
            fila.cells[5].innerHTML = "";
            campo.disable=true;

            var wfechapantalla = document.getElementById('wfecha');
            var datofecha = wfechapantalla.innerHTML;

            var whorapantalla = document.getElementById('whora');
            var datohora = whorapantalla.innerHTML;


            var valor = campo.value;
        var parametros = "consultaAjax=grabargluco&wmovhos="+wmovhos+"&wemp_pmla="+wemp_pmla+"&wing="+wing+"&whis="+whis+"&wtipo="+wtipo+"&wfechapantalla="+datofecha+"&whorapantalla="+datohora+"&wvalor="+valor;
                //alert(parametros);

                try
          {
            var ajax = nuevoAjax();
            ajax.open("POST", "autorizaciones_pendientes5.php",false);
            ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            ajax.send(parametros);
            //alert(ajax.responseText);

                    var nombre = "bloque_"+id
                    var ele = document.getElementById(nombre);
                    if (ele.style.display == "block") { ele.style.display = "none"; }
                       else { ele.style.display = "block"; }
                                            }catch(e){ alert(e) }
            }


         // FUNCION AJAX QUE PERMITE LA GRABACION DE LAS NEBULIZACIONES EN LA TABLA movhos_000119
     function grabarnebus(id, wmovhos, wemp_pmla, whis, wing, wtipo, campo)
      {

           var fila = campo.parentNode.parentNode.parentNode;
           //alert( fila );
           fila.cells[7].innerHTML = "";
           campo.disable=true;

            var valor = campo.value;

            var wfechapantalla = document.getElementById('wfecha');
            var datofecha = wfechapantalla.innerHTML;

            var whorapantalla = document.getElementById('whora');
            var datohora = whorapantalla.innerHTML;

        var parametros = "consultaAjax=grabargluco&wmovhos="+wmovhos+"&wemp_pmla="+wemp_pmla+"&wing="+wing+"&whis="+whis+"&wtipo="+wtipo+"&wfechapantalla="+datofecha+"&whorapantalla="+datohora+"&wvalor="+valor;
               //alert(parametros);

                try
          {
            var ajax = nuevoAjax();
            ajax.open("POST", "autorizaciones_pendientes5.php",false);
            ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            ajax.send(parametros);
            //alert(ajax.responseText);

                    var nombre = "bloquen_"+id
                    var ele = document.getElementById(nombre);
                    if (ele.style.display == "block") { ele.style.display = "none"; }
                       else { ele.style.display = "block"; }
                                            }catch(e){ alert(e) }
            }

        // FUNCION AJAX QUE PERMITE LA GRABACION DE LAS OXIMETRIAS EN LA TABLA movhos_000119
     function grabaroxi(id, wmovhos, wemp_pmla, whis, wing, wtipo, campo)
      {

           var fila = campo.parentNode.parentNode.parentNode;
           //alert( fila );
           fila.cells[9].innerHTML = "";
           campo.disable=true;

            var valor = campo.value;

            var wfechapantalla = document.getElementById('wfecha');
            var datofecha = wfechapantalla.innerHTML;

            var whorapantalla = document.getElementById('whora');
            var datohora = whorapantalla.innerHTML;

        var parametros = "consultaAjax=grabargluco&wmovhos="+wmovhos+"&wemp_pmla="+wemp_pmla+"&wing="+wing+"&whis="+whis+"&wtipo="+wtipo+"&wfechapantalla="+datofecha+"&whorapantalla="+datohora+"&wvalor="+valor;
               //alert(parametros);

                try
          {
            var ajax = nuevoAjax();
            ajax.open("POST", "autorizaciones_pendientes5.php",false);
            ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            ajax.send(parametros);
            //alert(ajax.responseText);

                    var nombre = "bloqueo_"+id
                    var ele = document.getElementById(nombre);
                    if (ele.style.display == "block") { ele.style.display = "none"; }
                       else { ele.style.display = "block"; }
                                            }catch(e){ alert(e) }
            }

    // FUNCION AJAX QUE PERMITE LA GRABACION DE LAS OXIMETRIAS EN LA TABLA movhos_000119
     function grabartransf(id, wmovhos, wemp_pmla, whis, wing, wtipo, campo)
      {


			$("#trans_dato_"+id).html("");

            var valor = campo.value;

            var wfechapantalla = document.getElementById('wfecha');
            var datofecha = wfechapantalla.innerHTML;

            var whorapantalla = document.getElementById('whora');
            var datohora = whorapantalla.innerHTML;

        var parametros = "consultaAjax=grabartransf&wmovhos="+wmovhos+"&wemp_pmla="+wemp_pmla+"&wing="+wing+"&whis="+whis+"&wtipo="+wtipo+"&wfechapantalla="+datofecha+"&whorapantalla="+datohora+"&wvalor="+valor;
               //alert(parametros);

                try
          {
            var ajax = nuevoAjax();
            ajax.open("POST", "autorizaciones_pendientes5.php",false);
            ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            ajax.send(parametros);
            //alert(ajax.responseText);

                    var nombre = "bloquet_"+id
                    var ele = document.getElementById(nombre);
                    if (ele.style.display == "block") { ele.style.display = "none"; }
                       else { ele.style.display = "block"; }
                                            }catch(e){ alert(e) }
            }


            window.onload = function() {
        if (browser=="Microsoft Internet Explorer"){
               setInterval( "parpadear()", 500 );
            }
        }

        function recargar(){
            document.forms[0].submit();
        }

        setTimeout( "recargar()", 1000*60*30 );

        //Vuelve a poner la pagina en el ultimo lugar antes de ser recargada
        window.onload=function(){
			
			var pos=window.name || 0;
			window.scrollTo(0,pos);

			//Se reemplaza la accion blink por esta jquery.
			setInterval(function() {

			$('.blink').effect("pulsate", {}, 5000);

			}, 1000);
			
			if($("#validar_zonas").val() != ''){				
				buscar_zonas();
				$("#sala").val($("#validar_zonas").val());
			}

        }
        window.onunload=function(){
        window.name=self.pageYOffset || (document.documentElement.scrollTop+document.body.scrollTop);
        }




</script>

<body>

        <?php

        //===========================================================================================================================================
        //*******************************************************************************************************************************************
        //===========================================================================================================================================
        //===========================================================================================================================================
        // P R I N C I P A L
        //===========================================================================================================================================
        //===========================================================================================================================================
        echo "<form name='pendientes' id='pendientes' action='autorizaciones_pendientes5.php' method=post>";

        ?>
        <script>
            deshabilitar_teclas();
        </script>
        <?php
        if (!isset($wfecha))
        $wfecha = date("Y-m-d");
        $whora = (string) date("H:i:s");
        global $wemp_pmla;

        echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
		$wactualiz = "Julio 4 de 2018";
        encabezado("Autorizaciones pendientes", $wactualiz, "clinica");

        if ( date( "H" ) > "7" and date( "H" ) < "19" )
        $wtur_grabar="MAÑANA";
        else
        $wtur_grabar="NOCHE";

            if (isset($whis) and isset($wcco))
             {
                echo "<input type='HIDDEN' name='wcco' VALUE='".$wcco."'>";
                echo "<input type='HIDDEN' name='wfec' VALUE='".$wfec."'>";
                if (isset($wdiag))
                    echo "<input type='HIDDEN' name='wdiag' VALUE='".$wdiag."'>";
                if (isset($wmed))
                    echo "<input type='HIDDEN' name='wmed' VALUE='".$wmed."'>";

                if (isset($wturno))
                    echo "<input type='HIDDEN' name='wturno' value='".$wturno."'>";
                    echo "<input type='HIDDEN' id='wmovhos' name='wmovhos' value='".$wmovhos."'>";
                    echo "<input type='HIDDEN' name='whis' value='".$whis."'>";
                    echo "<input type='HIDDEN' name='wing' value='".$wing."'>";
                    echo "<input type='HIDDEN' name='whab' value='".$whab."'>";
                    echo "<input type='HIDDEN' name='wpac' value='".$wpac."'>";
                    echo "<input type='HIDDEN' name='wtid' value='".$wtid."'>";
                    echo "<input type='HIDDEN' name='wdpa' value='".$wdpa."'>";


                if (!isset($orden))
                    {
                    $orden = 'desc';
                    }

                if (!isset($orden2))
                     {
                     $orden2 = 8;
                     }


                //IMRPIME LOS EXAMENES PENDIENTES CON SU RESPECTIVO TEXTAREA PARA AGREGAR LAS OBSERVACIONES, SOLO SERAN EDITABLES
                //LOS DEL DIA ACTUAL, ADEMAS DE IMPRIMIR LAS OBSERVACIONES ANTERIORES EN SOLO LECTURA
                consultarPendi($whis, $wing, $orden, $orden2);

                    echo "<br><br>";

                    echo "<table align=center>";

                    echo "</table>";
              }
                else
                    {
                        // LLAMADO A LA FUNCION QUE PERMITE OBSERVAR EL LISTADO DE PACIENTES SELECCIONANDO EL CENTRO DE COSTOS,
                        // ADEMAS DE LOS PENDIENTES DE PROCEDIMIENTOS, GLUCOMETRIAS Y NEBULIZACIONES
                        elegir_historia($wtur_grabar);

                    echo "<br>";
                    echo "<table align=center>";
                    echo "</table>";
                    }


        echo "<br><br>";
        echo "<table align=center>";
        echo "<tr><td align=center colspan=9><input type=button value='RETORNAR' onclick='cerrarVentana()'></td></tr>";
        echo "</table>";

        } //IF DEL REGISTER APROX LINEA 258
    ?>