<?php
include_once("conex.php");


 /*     * *******************************************************
     *               REPORTE PENDIENTES POR PISO             *
     *                      PARA EL KARDEX                   *
     *     				                   *
     * ******************************************************* */
//==================================================================================================================================
//PROGRAMA                   : entrega_turnos_secretaria.php
//AUTOR                      : Jonatan Lopez Aguirre.
//FECHA CREACION             : 05 Dic de 2011
//FECHA ULTIMA ACTUALIZACION :
   
//DESCRIPCION
//========================================================================================================================================\\
//========================================================================================================================================\\
//     Programa usado para verificar los pendientes procedimientos, glucometrias y nebulizaciones.                                        \\
//     ** Funcionamiento General:                                                                                                         \\
// Este script permite la visualizacion de los pacientes de cada piso dependiendo del piso que sea seleccionado, en el panel inicial
// aparece el listado acompañado de los siguientes pendientes: Procedimiento,glucometrias y nebulizaciones, los pendientes de procedimeintos
// salen de la tabla root_000050, los pendientes de glucometria salen de la tabla HCE_000036 y las nebulizaciones de la tabla movhos_000015.
// La funcion traer_sumglucometer hace el calculo de la cantidad de glucometrias que hay pendientes y hace la resta con las glucometrias
// guardadad en la tabla movhos_000119, cada vez que la secretaria selecciona el ckeckbox graba las glucometrias pendientes.
//
// En el panel inicial se encuentra el enlace ver, el cual lleva a los procedimientos pendientes con los siguientes datos:
// Examenes, realizar en la fecha, estado, ademas la secretaria puede agregar observaciones a cada examen y observar las observaciones
// a los examenes anteriores, en la parte inferior la sercretaria puede agregar observaciones generales para la historia.
/*
//========================================================================================================================================\\
  Septiembre 21 de 2020 Edwin MG: Se agrega casting a la variable $valor para que no se generen warnings
//========================================================================================================================================\\
  Febrero 19 de 2019 Arleyda I.C: Migración realizada 
//========================================================================================================================================\\
  Septiembre 26 de 2017 Jonatan: Se quita la validacion de procedimientos y examenes que necesitan autotizacion.
//========================================================================================================================================\\
//========================================================================================================================================\\
  Septiembre 22 de 2017 Jonatan: Se agrega la variable $sololectura = 'on' para que inactive las acciones de este sistema, ademas se agrega el codigo
		cups en el listado de examenes y procedimientos.
//========================================================================================================================================\\
  Septiembre 13 de 2017: Jonatan: Se valida en la funcion traer_oximetrias que el centro de costos sea de UCI/UCE, si es asi tomara el consecutivo 20
  del formulario 000112, sino tomara el consecutivo 9.
//========================================================================================================================================\\
  Agosto 18 de 2018 Jonatan: Correcciones con respecto a la grabacion de insumos UCI/UCE inactivando 
  el boton al grabar para que no se generen repetidos.
//========================================================================================================================================\\
  Julio 13 de 2017 Jonatan: Correcciones con respecto al cambio de estado de los examenes.
//========================================================================================================================================\\
  Junio 12 de 2017 Jonatan: Se corrige la eliminacion del checkbox cuando se graba.
//========================================================================================================================================\\
  Junio 6 de 2017 Jonatan: Se agrega historial de grabaciones para cada uno de las columnas, se muestra a urgencias en el 
							listado de cco y se muestran los pacientes.
//========================================================================================================================================\\
  Mayo 16 de 2017 Jonatan: Se agrega formulario hce_000349 para la lectura de insumos y procedimientos, se marcan las ordenes prioritarias con
  verde.
  //========================================================================================================================================\\
*/
//Abril 5 de 2017 Jonatan: Se agrega el formulario hce_000367 a la revision de formularios de evolucion del paciente, este reemplzara al hce_000069.
//========================================================================================================================================\\
//Marzo 30 de 2017 Jonatan: Se agrega mensaje de paciente con muerte pero sin alta definitiva en el sistema.
//========================================================================================================================================\\
//Junio 22 de 2016 Jonatan: Se agrega la cedula del medico en el detalle de las evoluciones e interconsultas.
//========================================================================================================================================\\
//Mayo 31 de 2016 Jonatan: Se agrega la columna interconsulta la cual trabaja con el formulario 000328 de hce, ese dato estraido del parametro
//en la tabla root_000051 FormularioInterconsultas.
//========================================================================================================================================\\
//Mayo 11 de 2016 Jonatan: Se comenta esta variable $cod_profesor en la funcion traer_evoluciones ya que debe traer exactamente 
//la especialidad del medico que le confirmo el formulario al alumno.
//========================================================================================================================================\\
//Enero 22 de 2016 Eimer y Jonatan: Se crean dos columnas nuevas llamadas insumos y procedimientos, la cuales pertenecen a los centros de
//costos de UCI/UCE y también permite guadar los insumos y procedimeintos de estos. La información se toma de la tabla hce_000206.
//========================================================================================================================================\\
//Julio 14 de 2015 Jonatan: Se agrega la observacion que viene de las ordenes en la columna de examanes.
//========================================================================================================================================\\
//Mayo 27 de 2015 Jonatan
//========================================================================================================================================\\
//Se cambia el filtro de lo examenes de eexpen = 'on' a eexaut = 'on', ya que las secretarias solo deben ver los examenes que tengan esa
//variable activa en la tabla 45 de movhos.
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
//Se agrega cambio de estado desde para que las secretarias cambien en examen a autorizado, este autorizado se reflejara en el kardex.
//========================================================================================================================================\\
//Agosto 27 de 2014 Jonatan
//Se agrega identificador al registro de observaciones para que no se repita si el paciente tiene el mismo examen.
//========================================================================================================================================\\
//Agosto 12 de 2014 (Jonatan Lopez)
//Se agrega el acceso al reporte de entrega y recibo de pacientes, historia clinica y consulta de turnos de cirugia.
//========================================================================================================================================\\
//Julio 17 de 2014 (Jonatan Lopez)
//Se agrega la fecha de registro de la evolucion y se muestra en el detalle de las evoluciones.
//========================================================================================================================================\\
//Mayo 15 de 2014 (Juan C. Hernández)
//========================================================================================================================================\\
//Se modifica el query ppal para que se ordene por el campo habord, de las habitaciones tabla movhos_000020
//========================================================================================================================================\\
//Julio 8 de 2013
//Se corrigen las evoluciones para que no tenga en cuenta la tabla 48 de movhos, ya que esta tabla tiene hasta 2 especialidades por medico y esto
//genera registros repetidos en el informe.
//========================================================================================================================================\\
// Mayo 27 de 2013
// Se agrega una nueva columna llamada evoluciones, la cual consulta la cantidad de evoluciones que se le han hecho al paciente, al ingresar
// se mostrara la especialidad, el o los especialistas asociados y cuantas evoluciones han hecho cada uno, al seleccionar grabar se insertaran los
// datos en la tabla 119 de movhos con el tipo E(Evoluciones) para la his e ing seleccionada, al volver a cargar la pantalla se consultaran de nuevo
// las evoluciones a partir de la esa ultima fecha y hora de grabacion de datos en adelante en la tabla hce_000069(Evoluciones), ademas se validan
// los especialista que son alumnos para que sea a su profesor al que se le carguen los honorarios.
//========================================================================================================================================\\
//Abril 1 de 2013
//Se adiciona un link para consultar los turnos de disponibilidad de las especialidades
//========================================================================================================================================\\
//Diciembre 10 de 2012
//Se agrega un union a la consulta de pacientes para que muestre los que tienen muerte y asi les puedan generar factura desde listas.php
//========================================================================================================================================\\
//Noviembre 29 de  2012
//Se agrega un mensaje recordandole a las secretarias que deben grabar en unix los insumos, ademas se organiza la codificacion de caracteres
//para los insumos.
//========================================================================================================================================\\
//Modificacion: Noviembre 07 de 2012
//========================================================================================================================================\\
//Se agrega la columna de insumos, la cual consulta la tabla 000205 de hce y extrae que insumos han utilizado para el paciente, los cuales
//pueden guardar al ingresar a la ventana modal.
///========================================================================================================================================\\
//Modificacion: Mayo 28 de 2012
//========================================================================================================================================\\
//Se modifica el estilo del encabezado de la información demográfica del paciente cuando se ingresa a la opción "VER".
//
//========================================================================================================================================\\
//Modificacion: Mayo 25 de 2012
//========================================================================================================================================\\
//Se complementa la información demográfica del paciente cuando se ingresa a la opción "VER".
//Los datos de paciente agregados son documento de identidad, Servicio de donde proviene, Entidad responsable.
///========================================================================================================================================\\
//Modificacion: Febrero 28 de 2012
//========================================================================================================================================\\
//Se le quita la negrilla a la columna de medico tratante y se le pone al paciente, ademas permite ver las observaciones generales del kardex
//aun sin tener pendientes.
//========================================================================================================================================\\
//Modificacion: Febrero 07 de 2012
//========================================================================================================================================\\
//Se modifica la funcion traer_nebulizaciones se modifica la consulta para que no sume sino que cuente para el campo aplca, se modifica la
//funcion traer_oximetrias para que consulte en el formulario 112 de hce en la posicion 9, se repara la consulta del medico tratante ya que
//no estaba tomando la fecha actual, se agrupan los medicos tratantes para que no se repitan, se modifica el ingreso de observacion por
//examen para que acepte un espacio almenos, se modifica el control de para el numero de observaciones, solo mostrara el numero titilando si
//la secretaria no ha leido ningun pendiente.
//========================================================================================================================================\\
////Modificacion: Febrero 02 de 2012
//========================================================================================================================================\\
//Se agregan los campos de control Dmoord y Dmoite para controlar los pendientes de ordenes en la tabla movhos_000121 / Jonatan Lopez
//========================================================================================================================================\\
//Modificacion: Enero 31 de 2012
//========================================================================================================================================\\
//Se agrega la consulta a la tabla temporal de los examenes del Kardex para permitir que mientras el kardex esta abierto siga mostrando los
//pendientes. / Jonatan Lopez
//========================================================================================================================================\\
//Modificacion: Enero 20 de 2012
//========================================================================================================================================\\
//Se agrega la consulta a las tablas hce_000027 y hce_0000028 para que traiga los examenes pendientes por historia e ingreso,
//esta consulta se agrega  las funciones consultarpendi y consultarPendientesPaciente para que sean impresos y mostrados en el listado
//de pendintes, ademas se agrega la funcion traer_nombre_examen para que consulte el maestro de examenes de la tabla hce_000017, esta
//funcion trae el nombre del examen con un codigo especifico. / Jonatan Lopez
///========================================================================================================================================\\
//Modificacion: Enero 18 de 2012
//========================================================================================================================================\\
//Se refina la consulta de los examenes pendientes del kardex aplicado a las funciones consultarPendi y consultarPendientesPacientes, ademas se
//agrega a la insercion de las observaciones por examen, la fecha en que aparace en el Kardex y la hora en que aparece en el Kardex, esto para permitir
//la relacion del panel del Kardex y las observaciones de los pendientes. / Jonatan Lopez
////========================================================================================================================================\\
//Modificacion: Enero 13 de 2012
//========================================================================================================================================\\
//Se crean las funciones traer_tansfusiones y traer_oximetrias para que muestren la cantidad de oximetrias y transfusiones hechas a los pacientes
//ademas se modifica el archivo matrix.css ubicado en include/root para que muestre columnas de solor azul, gris y crema cada una asociada a oximetrias
//glucometrias y transfusiones respectivamente, se incluye el enlace al kardex de cada paciente. / Jonatan Lopez
//========================================================================================================================================\\
//Modificacion: Enero 11 de 2012
//========================================================================================================================================\\
//Se repara el script para que utilice las funciones de comun.php con respecto a las aplicaciones por empresa, se cambia de color la columna
//de glucometrias, la cual estaba en rojo por gris oscuro, ademas la grabacion de las glucometrias y nebulizaciones quedan con la fecha y hora
//de la parte superior del panel, permitiendo que las consultas sean de esa hora en adelante y asi evitar lapsos de grabacion ocultos, se aumenta
//el tamaño de los texto en el panel de observaciones. / Jonatan Lopez
//
//========================================================================================================================================\\
//Modificacion: Diciembre 27
//========================================================================================================================================\\
//Se optimiza la interfaz para dar claridad a la secretaria sobre los campos editables y los no editables, esto se hace con colores resaltados
//amarillo y rojo claro. / Jonatan Lopez
//========================================================================================================================================\\
//Modificacion: Diciembre 23
//========================================================================================================================================\\
//Se optimizan las consultas para la tabla movhos_000015, en relacion con la tabla movhos_000091 para que la impresion de penientes
//sea mas optima. / Jonatan Lopez
//========================================================================================================================================\\
//Modificacion: Diciembre 14
//========================================================================================================================================\\
//Se agrega el parametro de glucometrias pendientes seleccionado de la tabla HCE_000036 todas las glucometrias, despues que la secretaria los graba,
//el numero de glucometrias y el checkbox desaparecen los datos son enviados a la tabla movhos_000119, al ingresar mas tarde apareceran las glucometrias
//nuevas, estas glucometrias seran impresas en pantalla y grabadas en la tabla movhos_000119, el numero de glucometrias nuevas pendientes sale de la reszta
//de las glucometrias de la tabla HCE_000036 y movhos_000119 respectivamente, este nuevo numero que aparece en pantalla sera regisrado por la
//secretaria en un nuevo elemento en la base de datos con la fecha, hora, y el resto de datos necesarios para el control de estos ingresos.
// / Jonatan Lopez
//========================================================================================================================================\\
//Modificacion: Diciembre 13
//========================================================================================================================================\\
//Se agragan las observaciones a cada examen con procedimiento ajax in situ utilizando la funcion grabarObservacion, los datos de estas
//observaciones se graban en la tabla movhos_000121 y se agrega el area de
//texto inferior en el cual la secretaria tambien puede agregar observaciones generales in situ con la funcion grabarObservaciongnral y se graban
//en la tabla movhos_000120. / Jonatan Lopez
//========================================================================================================================================\\
//Modificacion: Diciembre 5
//========================================================================================================================================\\
//Se crea el listado de los pacientes por piso y se imprime en pantalla los procedimientos pendientes, ademas se muestran los pendientes de
//cada paciente.
//
//Se agregan los datos de interconsultas, terapias, cirugias y observaciones para la historia e ingreso en pantalla, esta informacion se imprime
//en la parte inferior de los pendientes del paciente con la funcion query_kardex. / Jonatan Lopez
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

function registrarAuditoriaKardex($conex,$wbasedato, $auditoria){

	$q = "INSERT INTO ".$wbasedato."_000055
				(Medico, Fecha_data, Hora_data, Kauhis, Kauing, Kaudes, Kaufec, Kaumen, Kauido, Seguridad)
			VALUES
				('movhos','".date("Y-m-d")."','".date("H:i:s")."','$auditoria->historia','$auditoria->ingreso','$auditoria->descripcion','$auditoria->fechaKardex','$auditoria->mensaje','$auditoria->idOriginal','A-$auditoria->seguridad')";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

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



        /**
         * Función buscar_centro_costo($wemp_pmla, $wcco), esta función busca el nombre de un centro de costo a partir de su código.
         *
         * @param string $wemp_pmla :   Nombre de la empresa-tabla en la que se hace la búsqueda del centro de costo.
         * @param unknown $wcco     :   Código del centro de costos para el cual se va a buscar la descripción.
         * @return string           :   Retorna el nombre o descripción del centro de costos consultado.
         */

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


    function mostrar_detalle($wemp_pmla, $whis, $wing, $wfecha, $whora, $sololectura)
    {

		global $conex;
		global $wemp_pmla;
        global $whce;
        global $wmovhos;
		global $sololectura;
		
		if($sololectura == 'on'){
			
			$disabled = "disabled";
		}
		
        $wcant_insumostotal = '';

        $wforminsumos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'FormInsumos'); //Extrae el nombre del formulario para extraer los valores a cobrar.
        $wconfinsumos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ConfInsumos'); //Extrae el arreglo con dos numeros, el primero sirve para mostrar el nombre del
                                                                                        //articulo de la tabla hce_000002 y el segundo sirve para extraer la cantidad
                                                                                        //del campo movdat de la tabla hce_000205.

        $wcampos_desc = explode(";", $wconfinsumos); // Se explota el registro que esta separado por comas, para luego usarlo en el ciclo siguiente.
        $wcuantos = count($wcampos_desc);


		echo "<div align='center' style='cursor:default;background:none repeat scroll 0 0; position:relative;width:100%;height:500px;overflow:auto;'><center><br>";
		echo "<br><INPUT TYPE='button' value='Cerrar' onClick=' cerraremergente();' style='width:100'><br><br>";
		
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

		//CANTIDAD DE INSUMOS PARA EL PACIENTE
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
        $num = mysql_num_rows($res);
		
		if($num > 0){
			
			echo "<table style='text-align: left; width: 100px;' border='0' rowspan='2' colspan='1'>
                <tbody>
                <tr class='fondoAmarillo'>
                <td colspan=2 align=center><b>Insumo pendientes por grabar</b></td>                
                </tr>
				<tr class='encabezadoTabla'>
                <td align=center>Insumo</td>
                <td>Cantidad</td>
                </tr>";
			
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
					echo "<tr class=$wclass><td nowrap=nowrap>".($wdescripcion)."</td><td>".$wcant_insumosxart."</td></tr>";
				}
				$i++;

			}

			echo "<tr class=encabezadoTabla><td>Total</td><td>".$wcant_insumostotal."</td></tr>";
			echo "</table>	";
			echo "<p class='blink'><font color='red' size='4'><b>RECUERDE</b></font><br><b>Primero debe grabar en la cuenta del paciente en Unix</b></p>";
			$td_id = $whis."-".$wing;
			echo "<INPUT TYPE='button' value='Grabar' id='insumos' ".$disabled." onClick='grabarinsumos(\"$wmovhos\",\"$whis\",\"$wing\",\"I\",\"$wcant_insumostotal\",\"$td_id\")'><br>";
			echo "<br>";
			echo "<div id='div_resultado' style='text-align:left;'></div>";
			
		}
		
		//Historico
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
					."  AND CONCAT( ".$whce."_000036.Fecha_data, ' ', ".$whce."_000036.Hora_data ) < '".$fechamax_insumos."'"
					." GROUP BY ".$whce."_".$wforminsumos.".movcon "
					."   HAVING SUM(".$whce."_".$wforminsumos.".movdat) > 0 ";
		$res= mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
        $num_historico = mysql_num_rows($res);
		
		$i = 0;
		
		if($num_historico > 0){
			
			echo "<table style='text-align: left; width: 100px;' border='0' rowspan='2' colspan='1'>
			<tbody>
			<tr class='fondoAmarillo'>
			<td colspan=2 align=center><b>Insumos ya grabados</b></td>
			</tr>
			<tr class='encabezadoTabla'>
			<td align=center>Insumo</td>
			<td>Cantidad</td>
			</tr>";
		
		while ($rows = mysql_fetch_array($res))
        {

			$wdescripcion = traer_descripcion_insumos($wforminsumos, $arts[ $rows[1] ] ); //Trae la descripcion del articulo de la tabla hce_000002

			//Estilo de las filas
            if (is_integer($i / 2))
				$wclass = "fila1";
            else
                $wclass = "fila2";

			$wcant_insumosxart_historico   = $rows[0]; //Cantidad por insumo
            $wcant_insumostotal_historico += $rows[0]; //Cantidad total

            if($wcant_insumosxart_historico > 0)
            {
				echo "<tr class=$wclass><td nowrap=nowrap>".($wdescripcion)."</td><td>".$wcant_insumosxart_historico."</td></tr>";
            }
            $i++;

        }

        echo "<tr class=encabezadoTabla><td>Total</td><td>".$wcant_insumostotal_historico."</td></tr>";
        echo "</table>	";
		
		}
		
		
		echo "<br><INPUT TYPE='button' value='Cerrar' onClick=' cerraremergente();' style='width:100'><br><br>";
		echo "</center></div>";



    }

	function mostrar_detalleUCIUCE($wemp_pmla, $whis, $wing, $wfecha, $whora, $sololectura)
    {
		global $conex;
		global $wemp_pmla;
        global $whce;
        global $wmovhos;
        $wcant_insumostotal = '';
		$datos = array();
		
		if($sololectura == 'on'){
			
			$disabled = "disabled";
		}
		
        $wforminsumosuciuce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'FormulariosInsumosUCIUCE'); //Extrae el nombre del formulario para extraer los valores a cobrar.
		$datos_formularios = explode(",",$wforminsumosuciuce);
		
		$wcampo_form_insuUCIUCE = consultarAliasPorAplicacion($conex, $wemp_pmla, 'campoFormUCIUCEinsumos');

		echo "<div align='center' style='cursor:default;background:none repeat scroll 0 0; position:relative;width:100%;height:500px;overflow:auto;'><center><br>";
		
		echo "<br><INPUT TYPE='button' value='Cerrar' onClick=' cerraremergente();' style='width:100'><br><br>";
		
        $query ="SELECT MAX( CONCAT( Fecha_data, ' ', Hora_data ) ) AS FechaHora "
                 ."FROM ".$wmovhos."_000119 "
                ."WHERE Glnhis = '".$whis."'
                    AND Glning = '".$wing."'
                    AND Glnind = 'IUCIUCE'
                    AND Glnest = 'on'";
        $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ". $query ." - ".mysql_error());
        $rows = mysql_fetch_array($res);
        $fechamax_insumos = $rows[0];

		
		foreach($datos_formularios as $key => $value){
				
				$form_campo = explode("-",$value);
				$wforminsumosuciuce = $form_campo[0];
				$wcampo_form_insuUCIUCE = $form_campo[1];
		
				$query =          " SELECT movcon,movdat, firhis, firing, CONCAT( ".$whce."_000036.Fecha_data, ' ', ".$whce."_000036.Hora_data ) "
									."FROM ".$whce."_000036, ".$whce."_".$wforminsumosuciuce
								  ." WHERE Firhis = '".$whis."'"
								   ."  AND Firing = '".$wing."'"
								   ."  AND Firhis = Movhis"
								   ."  AND Firing = Moving"
								   ."  AND Firpro = '".$wforminsumosuciuce."'"
								   ."  AND movcon = '".$wcampo_form_insuUCIUCE."'"
								   ."  AND Firfir = 'on'"
								   ."  AND ".$whce."_000036.Fecha_data = ".$whce."_".$wforminsumosuciuce.".Fecha_data"
								   ."  AND ".$whce."_000036.Hora_data = ".$whce."_".$wforminsumosuciuce.".Hora_data"
								   ."  AND '".$fechamax_insumos."' <= CONCAT( ".$whce."_000036.Fecha_data, ' ', ".$whce."_000036.Hora_data ) ";
				$res= mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
				//echo $query."<br>";
				while($info = mysql_fetch_assoc($res))
				{

						$informacion[] = $info['movdat'];


				}
				
				// echo "<pre align='left'>";
				// echo print_r($informacion);
				// echo "</pre>";

				$array_final_aux = array();
				
				if (is_array($informacion)){
				
					foreach($informacion as $key2 => $value2){

						$datos_arreglo = explode("*",$value2);
						unset($datos_arreglo[0]);

						$i = 0;
						
						// echo "<pre align='left'>";
				// echo print_r($datos_arreglo);
				// echo "</pre>";
						
						foreach($datos_arreglo as $key1 => $value1){

								$datos_arreglo_aux = explode("|",$value1);
								
								if(trim($datos_arreglo_aux[0]) != '' and $datos_arreglo_aux[1] > 0){
									
									if(!array_key_exists($datos_arreglo_aux[0],$array_final_aux )){

										$array_final_aux[$datos_arreglo_aux[0]] = array(  'nombre'   => $datos_arreglo_aux[0],
																						  'cantidad' => $datos_arreglo_aux[1]);

									}else{

										$array_final_aux[$datos_arreglo_aux[0]]['cantidad'] += $datos_arreglo_aux[1];

									}
								}

							}

					}
				}
		}
		// echo "<pre align='left'>";
		// echo print_r($array_final_aux);
		// echo "</pre>";

		// echo "<pre align='left'>";
		// echo print_r($array_final_aux);
		// echo "</pre>";
		if(count($array_final_aux) > 0 ){
		
			echo "<table style='text-align: left; width: 100px;' border='0' rowspan='2' colspan='1'>
					<tbody>
					<tr class='fondoAmarillo'>
					<td colspan=2 align=center><b>Insumo pendientes por grabar</b></td>                
					</tr>
					<tr class='encabezadoTabla'>
					<td>Insumo</td>
					<td>Cantidad</td>
					</tr>";	

			foreach($array_final_aux as $key1 => $value1){

				if($value1['nombre'] !=''){
					$suma = $suma + $value1['cantidad'];
					//Estilo de las filas
					if (is_integer($i / 2))
					{
						$wclass = "fila1";
					}
					else{
						$wclass = "fila2";
					}
					echo "<tr class=$wclass><td nowrap=nowrap>".($value1['nombre'])."</td><td>".$value1['cantidad']."</td></tr>";
					$i++;
				}
			}

			// echo "<pre align='left'>";
			// echo print_r($datos, true);
			// echo "</pre>";

			echo "<tr class=encabezadoTabla><td>Total</td><td>".$suma."</td></tr>";
			echo "</table>	";
			echo "<p class='blink'><font color='red' size='4'><b>RECUERDE</b></font><br><b>Primero debe grabar en la cuenta del paciente en Unix</b></p>";
			$td_id = $whis."-".$wing;
			echo "<INPUT TYPE='button' value='Grabar' ".$disabled." id='boton_insumos' onClick='grabarinsumos(\"$wmovhos\",\"$whis\",\"$wing\",\"IUCIUCE\",\"$suma\",\"$td_id\")'><br>";
			
		}
		
		echo "<div id='div_resultado' style='text-align:left;'></div>";
		
		echo "<br>";
		//----- Historico ------
		
		foreach($datos_formularios as $key => $value){
				
				$form_campo = explode("-",$value);
				$wforminsumosuciuce = $form_campo[0];
				$wcampo_form_insuUCIUCE = $form_campo[1];
		
				$query =          " SELECT movcon,movdat, firhis, firing, CONCAT( ".$whce."_000036.Fecha_data, ' ', ".$whce."_000036.Hora_data ) "
									."FROM ".$whce."_000036, ".$whce."_".$wforminsumosuciuce
								  ." WHERE Firhis = '".$whis."'"
								   ."  AND Firing = '".$wing."'"
								   ."  AND Firhis = Movhis"
								   ."  AND Firing = Moving"
								   ."  AND Firpro = '".$wforminsumosuciuce."'"
								   ."  AND movcon = '".$wcampo_form_insuUCIUCE."'"
								   ."  AND Firfir = 'on'"
								   ."  AND ".$whce."_000036.Fecha_data = ".$whce."_".$wforminsumosuciuce.".Fecha_data"
								   ."  AND ".$whce."_000036.Hora_data = ".$whce."_".$wforminsumosuciuce.".Hora_data"
								   ."  AND '".$fechamax_insumos."' >= CONCAT( ".$whce."_000036.Fecha_data, ' ', ".$whce."_000036.Hora_data ) ";
				$res= mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
				//echo $query."<br>";
				while($info = mysql_fetch_assoc($res))
				{

						$informacion_his[] = $info['movdat'];


				}
				
				// echo "<pre align='left'>";
				// echo print_r($informacion_his);
				// echo "</pre>";

				$array_final_aux1 = array();
				
				if (is_array($informacion_his)){
				
					foreach($informacion_his as $key2 => $value2){

						$datos_arreglo = explode("*",$value2);
						unset($datos_arreglo[0]);

						$i = 0;
						
						// echo "<pre align='left'>";
				// echo print_r($datos_arreglo);
				// echo "</pre>";
						
						foreach($datos_arreglo as $key1 => $value1){

								$datos_arreglo_aux = explode("|",$value1);
								
								if(trim($datos_arreglo_aux[0]) != '' and $datos_arreglo_aux[1] > 0){
									
									if(!array_key_exists($datos_arreglo_aux[0],$array_final_aux1 )){

										$array_final_aux1[$datos_arreglo_aux[0]] = array(  'nombre'   => $datos_arreglo_aux[0],
																						  'cantidad' => $datos_arreglo_aux[1]);

									}else{

										$array_final_aux1[$datos_arreglo_aux[0]]['cantidad'] += $datos_arreglo_aux[1];

									}
								}

							}

					}
				}
		}
		
		// echo "<pre align='left'>";
		// echo print_r($array_final_aux1);
		// echo "</pre>";

		if(count($array_final_aux1) > 0){
			
			echo "<table style='text-align: left; width: 100px;' border='0' rowspan='2' colspan='1'>
                <tbody>
                <tr class='fondoAmarillo'><td colspan=2 align=center><b>Insumos ya grabados</b></td></tr>
                <tr class='encabezadoTabla'>
                <td>Insumo</td>
                <td>Cantidad</td>
                </tr>";
				
			foreach($array_final_aux1 as $key1 => $value1){

				if($value1['nombre'] !=''){
					$suma_his = $suma_his + $value1['cantidad'];
					//Estilo de las filas
					if (is_integer($i / 2))
					{
						$wclass = "fila1";
					}
					else{
						$wclass = "fila2";
					}
					echo "<tr class=$wclass><td nowrap=nowrap>".($value1['nombre'])."</td><td>".$value1['cantidad']."</td></tr>";
					$i++;
				}
			}

			// echo "<pre align='left'>";
			// echo print_r($datos, true);
			// echo "</pre>";

			echo "<tr class=encabezadoTabla><td>Total</td><td>".$suma_his."</td></tr>";
			echo "</table>	";
		
		}
		
		echo "<br><INPUT TYPE='button' value='Cerrar' onClick=' cerraremergente();' style='width:100'><br><br>";
		echo "</center></div>";

    }
	
	
	function strstr_replica($haystack, $needle, $beforeNeedle = false) {
        $needlePosition = strpos($haystack, $needle);

        if ($position === false) {
            return false;
        }

        if ($beforeNeedle) {
            return substr($haystack, 0, $needlePosition);
        } else {
            return substr($haystack, $needlePosition);
        }
    }

	function mostrar_detalleProcUCIUCE($wemp_pmla, $whis, $wing, $wfecha, $whora, $sololectura)
    {
		global $conex;
		global $wemp_pmla;
        global $whce;
        global $wmovhos;
        $wcant_insumostotal = '';
		$informacion = array();
		
		if($sololectura == 'on'){
			
			$disabled = "disabled";
		}
		
        $wforminsumosuciuce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'FormulariosInsumosUCIUCE'); //Extrae el nombre del formulario para extraer los valores a cobrar.
		$datos_formularios = explode(",",$wforminsumosuciuce);

		echo "<div align='center' style='cursor:default;background:none repeat scroll 0 0; position:relative;width:100%;height:500px;overflow:auto;'><center><br>";
		
		echo "<br><INPUT TYPE='button' value='Cerrar' onClick=' cerraremergente();' style='width:100'><br><br>";
		
        $query ="SELECT MAX( CONCAT( Fecha_data, ' ', Hora_data ) ) AS FechaHora "
                 ."FROM ".$wmovhos."_000119 "
                ."WHERE Glnhis = '".$whis."'
                    AND Glning = '".$wing."'
                    AND Glnind = 'PUCIUCE'
                    AND Glnest = 'on'";
        $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ". $query ." - ".mysql_error());
        $rows = mysql_fetch_array($res);
        $fechamax_insumosuciuce = $rows[0];

		$datos = array();

		foreach($datos_formularios as $key => $value){
				
				$form_campo = explode("-",$value);
				$wforminsumosuciuce = $form_campo[0];
				$wcampo_form_procUCIUCE = $form_campo[2];
				$wcampo_form_procUCIUCE_aux = $form_campo[2];
				
				$wcampo_form_procUCIUCE = explode("*",$wcampo_form_procUCIUCE);
				$wcampo_form_procUCIUCE = implode("','",$wcampo_form_procUCIUCE);
		
				$query =    "SELECT movcon,movdat, firhis, firing "
									."FROM ".$whce."_000036, ".$whce."_".$wforminsumosuciuce
								  ." WHERE Firhis = '".$whis."'"
								   ."  AND Firing = '".$wing."'"
								   ."  AND Firhis = Movhis"
								   ."  AND Firing = Moving"
								   ."  AND Firpro = '".$wforminsumosuciuce."'"
								   ."  AND movcon in ('".$wcampo_form_procUCIUCE."')"
								   ."  AND Firfir = 'on'"
								   ."  AND ".$whce."_000036.Fecha_data = ".$whce."_".$wforminsumosuciuce.".Fecha_data"
								   ."  AND ".$whce."_000036.Hora_data = ".$whce."_".$wforminsumosuciuce.".Hora_data"
								   ."  AND '".$fechamax_insumosuciuce."' < CONCAT( ".$whce."_000036.Fecha_data, ' ', ".$whce."_000036.Hora_data )";
				$res= mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
				
				while($info = mysql_fetch_assoc($res))
				{

					$informacion[] = $info['movdat'];

				}
		}
		// echo "<pre align='left'>";
		// echo print_r($informacion);
		// echo "</pre>";

		$datos = array();
		if(count($informacion) > 0){
			
		foreach($informacion as $key => $value){

				$aux_datos = explode("*",$value);
				unset($aux_datos[0]);
				
				foreach($aux_datos as $key1 => $value1){
					
					//Cuando es el consecutivo 9 del formulario
					if(substr_count($value1, '|') > 1){
						
						$procedimiento = explode("|", $value1);
						
						// echo "<div align=left>";
						// echo "<pre>";
						// print_r($procedimiento);
						// echo "</pre>";
						// echo "</div>";
						
						$nom_procedimiento = $procedimiento[0];
						$cantidad_datos = $procedimiento[1];				
						
						if(!array_key_exists($nom_procedimiento, $datos))
						{
							$datos[$nom_procedimiento] = array("descripcion"=>$nom_procedimiento, "cantidad"=>$cantidad_datos);
							
						}else{
							
							$datos[$nom_procedimiento]["cantidad"] = $datos[$nom_procedimiento]["cantidad"]+$cantidad_datos;
							
						}
						
						//print_r($datos);
						
					}else{
						
						$procedimiento = explode("|", $value1);
						$nom_procedimiento = $procedimiento[0];
					
						
						$cantidad_datos = 1;
						if(!array_key_exists($nom_procedimiento, $datos))
						{
							$datos[$nom_procedimiento] = array("descripcion"=>$nom_procedimiento, "cantidad"=>$cantidad_datos);
						}
						else
						{
							$datos[$nom_procedimiento]["cantidad"]++;
						}
						
					}				

				}

			}
		}
		
		if(count($datos) > 0){
			
				echo "<table style='text-align: left; width: 100px;' border='0' rowspan='2' colspan='1'>
					<tbody>	
					<tr class='fondoAmarillo'>
					<td colspan=2 align=center><b>Procedimientos pendientes por grabar</b></td>                
					</tr>					
					<tr class='encabezadoTabla'>
					<td>Procedimiento</td>
					<td>Cantidad</td>
					</tr>";

			foreach($datos as $key1 => $value1){

				$dato_descripcion = str_replace("\n", "", $value1['descripcion']);

				if(trim($dato_descripcion != '')){

					$suma = $suma + $value1['cantidad'];
					//Estilo de las filas
					if (is_integer($i / 2))
					{
						$wclass = "fila1";
					}
					else{
						$wclass = "fila2";
					}
					echo "<tr class=$wclass><td nowrap=nowrap>".($value1['descripcion'])."</td><td>".$value1['cantidad']."</td></tr>";
					$i++;

				}
			}
			
		echo "<tr class=encabezadoTabla><td>Total</td><td>".$suma."</td></tr>";
        echo "</table>	";
        echo "<p class='blink'><font color='red' size='4'><b>RECUERDE</b></font><br><b>Primero debe grabar en la cuenta del paciente en Unix</b></p>";
		$td_id = $whis."-".$wing;
        echo "<INPUT TYPE='button' value='Grabar' ".$disabled." id='insumos' onClick='grabarinsumos(\"$wmovhos\",\"$whis\",\"$wing\",\"PUCIUCE\",\"$suma\",\"$td_id\")'><br>";
        echo "<div id='div_resultado' style='text-align:left;'></div>";
			
			
		}
		
		echo "<br>";	
		
		//---------------------**-----------
		//Historial
						
		foreach($datos_formularios as $key => $value){
				
				$form_campo = explode("-",$value);
				$wforminsumosuciuce = $form_campo[0];
				$wcampo_form_procUCIUCE = $form_campo[2];
				$wcampo_form_procUCIUCE_aux = $form_campo[2];
				
				$wcampo_form_procUCIUCE = explode("*",$wcampo_form_procUCIUCE);
				$wcampo_form_procUCIUCE = implode("','",$wcampo_form_procUCIUCE);
		
				$query =    "SELECT movcon,movdat, firhis, firing "
									."FROM ".$whce."_000036, ".$whce."_".$wforminsumosuciuce
								  ." WHERE Firhis = '".$whis."'"
								   ."  AND Firing = '".$wing."'"
								   ."  AND Firhis = Movhis"
								   ."  AND Firing = Moving"
								   ."  AND Firpro = '".$wforminsumosuciuce."'"
								   ."  AND movcon in ('".$wcampo_form_procUCIUCE."')"
								   ."  AND Firfir = 'on'"
								   ."  AND ".$whce."_000036.Fecha_data = ".$whce."_".$wforminsumosuciuce.".Fecha_data"
								   ."  AND ".$whce."_000036.Hora_data = ".$whce."_".$wforminsumosuciuce.".Hora_data"
								   ."  AND '".$fechamax_insumosuciuce."' > CONCAT( ".$whce."_000036.Fecha_data, ' ', ".$whce."_000036.Hora_data )";
				$res= mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
				
				while($info = mysql_fetch_assoc($res))
				{

					$informacion[] = $info['movdat'];

				}
		}
		// echo "<pre align='left'>";
		// echo print_r($informacion);
		// echo "</pre>";

		$datos = array();
		foreach($informacion as $key => $value){

			$aux_datos = explode("*",$value);
			unset($aux_datos[0]);
			
			foreach($aux_datos as $key1 => $value1){
				
				//Cuando es el consecutivo 9 del formulario
				if(substr_count($value1, '|') > 1){
					
					$procedimiento = explode("|", $value1);
					
					// echo "<div align=left>";
					// echo "<pre>";
					// print_r($procedimiento);
					// echo "</pre>";
					// echo "</div>";
					
					$nom_procedimiento = $procedimiento[0];
					$cantidad_datos = $procedimiento[1];				
					
					if(!array_key_exists($nom_procedimiento, $datos))
					{
						$datos[$nom_procedimiento] = array("descripcion"=>$nom_procedimiento, "cantidad"=>$cantidad_datos);
						
					}else{
						
						$datos[$nom_procedimiento]["cantidad"] = $datos[$nom_procedimiento]["cantidad"]+$cantidad_datos;
						
					}
					
					//print_r($datos);
					
				}else{
					
					$procedimiento = explode("|", $value1);
					$nom_procedimiento = $procedimiento[0];
				
					
					$cantidad_datos = 1;
					if(!array_key_exists($nom_procedimiento, $datos))
					{
						$datos[$nom_procedimiento] = array("descripcion"=>$nom_procedimiento, "cantidad"=>$cantidad_datos);
					}
					else
					{
						$datos[$nom_procedimiento]["cantidad"]++;
					}
					
				}				

			}

		}

		if(count($datos) > 0){	
		
		echo "<table style='text-align: left; width: 100px;' border='0' rowspan='2' colspan='1'>
                <tbody>
				<tr class='fondoAmarillo' align=center><td colspan=2><b>Procedimientos ya grabados</b></td></tr>
                <tr class='encabezadoTabla'>
                <td>Procedimiento</td>
                <td>Cantidad</td>
                </tr>";
			
		foreach($datos as $key1 => $value1){

			$dato_descripcion = str_replace("\n", "", $value1['descripcion']);

			if(trim($dato_descripcion != '')){

				$suma = $suma + $value1['cantidad'];
				//Estilo de las filas
				if (is_integer($i / 2))
				{
					$wclass = "fila1";
				}
				else{
					$wclass = "fila2";
				}
				echo "<tr class=$wclass><td nowrap=nowrap>".($value1['descripcion'])."</td><td>".$value1['cantidad']."</td></tr>";
				$i++;

			}
		}

		// echo "<pre align='left'>";
		// echo print_r($datos, true);
		// echo "</pre>";

        echo "<tr class=encabezadoTabla><td>Total</td><td>".$suma."</td></tr>";
        echo "</table>	";
       	}
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

   function mostrar_detalle_especialista($wemp_pmla, $whis, $wing, $wfecha, $whora, $array_profesores, $evoluciones, $tipo, $sololectura)
    {

		global $conex;
		global $wemp_pmla;
        global $whce;
        global $wmovhos;
		
		if($sololectura == 'on'){
			
			$disabled = "disabled";
		}
		
        $array_profesores = unserialize(base64_decode($array_profesores));
				
		if($tipo == 'E'){
			$texto_tipo = "Evoluci&oacute;n";
			$texto_tipo1 = "Evoluciones";
		}
		
		if($tipo == 'INT'){
			$texto_tipo = "Interconsulta";
			$texto_tipo1 = "Interconsultas";
		}
		
		echo "<div align='center' style='cursor:default;background:none repeat scroll 0 0; position:relative;width:100%;height:500px;overflow:auto;'><center><br>";
		echo "<br><INPUT TYPE='button' value='Cerrar' onClick=' cerraremergente();' style='width:100'><br><br>";
		if(count($array_profesores['sin_grabar']) > 0){		
		
			echo "<table style='text-align: left; width: auto;' border='0' rowspan='2' colspan='1'>
					<tbody>
					<tr class='fondoAmarillo'>	
					<td colspan=4 align=center><b>".$texto_tipo1." pendientes por grabar</b></td>
					</tr>
					<tr class='encabezadoTabla'>
					<td>Especialidad</td>
					<td>Profesional</td>
					<td>Fecha $texto_tipo</td>
					<td>$texto_tipo1</td>               
					</tr>";

			$arr_resp = array();
			$winfo = '';

			foreach($array_profesores['sin_grabar'] as $firusu => $value)
			{

				$wnombre_esp = $value['nombre_especialidad']; //Nombre de la especialidad
				$wcod_esp = $value['cod_esp']; //Codigo de la especialidad
				$wespecialista = $value['nombre_especialista']; //Trae el nombre del especialista
				$wcedula_medico = $value['cedula_medico']; //Trae la cedula del especialista

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
				$arr_resp[$wcod_esp]["especialistas"][] = array('especialidad'=> $wcod_esp,'especialista'=> $wespecialista, 'cedula_medico'=> $wcedula_medico,'evoluciones'=> $value['cuantos'], 'cantidad'=> count($value['cuantos']));
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
							$winfo .="<td class=$wclass rowspan='".$valueP['cantidad']."' >".($valueP['especialista'])." <br>(Doc. ".$valueP['cedula_medico'].")</td>";
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
			//En este caso se utiliza la misma funcion de grabarinsumo pero son el parametro INT, el cual diferencia el registro de interconsultas.
			$td_id = $whis."-".$wing;
			echo "<INPUT TYPE='button' value='Grabar' ".$disabled." id='insumos' onClick='grabarinsumos(\"$wmovhos\",\"$whis\",\"$wing\",\"$tipo\",\"$evoluciones\", \"$td_id\" )'><br>";
			echo "<div id='div_resultado' style='text-align:left;'></div>";
		
		}
		
		echo "<br>";
				
		if($array_profesores['historico'] > 0){
		
		
		echo "<table style='text-align: left; width: auto;' border='0' rowspan='2' colspan='1'>
                <tbody>
                <tr class='fondoAmarillo'>
				<td colspan=4 align=center><b>".$texto_tipo1." ya grabadas</b></td>
				</tr>
                <tr class='encabezadoTabla'>
                <td>Especialidad</td>
                <td>Profesional</td>
				<td>Fecha $texto_tipo</td>
                <td>$texto_tipo1</td>               
                </tr>";

        $arr_resp = array();
        $winfo = '';
		
        foreach($array_profesores['historico'] as $firusu => $value)
        {

            $wnombre_esp = $value['nombre_especialidad']; //Nombre de la especialidad
            $wcod_esp = $value['cod_esp']; //Codigo de la especialidad
            $wespecialista = $value['nombre_especialista']; //Trae el nombre del especialista
            $wcedula_medico = $value['cedula_medico']; //Trae la cedula del especialista

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
            $arr_resp[$wcod_esp]["especialistas"][] = array('especialidad'=> $wcod_esp,'especialista'=> $wespecialista, 'cedula_medico'=> $wcedula_medico,'evoluciones'=> $value['cuantos'], 'cantidad'=> count($value['cuantos']));
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
			$sumatoria_hist = 0;
			$sumatoria_esp = 0;
            foreach($value['especialistas'] as $keyP => $valueP)
                {
                    if($esps != 0) { $winfo .= "<tr>"; } //Se declara un tr para el inicio de la columan que acompaña a la especialidad
				
					//Recorro el array de fechas de evoluciones que esta dentro de este arreglo.
					foreach($valueP['evoluciones'] as $clave => $valor){
	
						if(!array_key_exists($keyP, $a)){
						$winfo .="<td class=$wclass rowspan='".$valueP['cantidad']."' >".($valueP['especialista'])." <br>(Doc. ".$valueP['cedula_medico'].")</td>";
						}


						$winfo .= "<td class=$wclass align=center>".$valor."</td>";

						if(!array_key_exists($keyP, $a)){
						$winfo .="<td class=$wclass align='center' rowspan='".$valueP['cantidad']."'>".$valueP['cantidad']."</td>";
						$a[$keyP] = $keyP;
						}

						$winfo .= "</tr>";
						
						$sumatoria_esp++;
					}
					
					$sumatoria_hist = $sumatoria_hist + $sumatoria_esp;
                    //Especialistas y cantidad de evoluciones de cada uno

                    $esps++;

                    //Cuenta cuantas evoluciones hay para luego imprimirlas en el total.
                    $wcuantos = $valueP['evoluciones'];
					
					
                }

                $i++;
            }

            //Cuanto esta listo el arreglo se imprime la informacion.
            echo $winfo;

        echo "<tr class=encabezadoTabla><td>Total</td><td></td><td></td><td align=center>".$sumatoria_hist."</td></tr>";
        echo "</table>	";
		
		}
		
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
   function grabarObservacion($wemp_pmla, $wmovhos, $wfec, $wexam, $wing, $wfechadataexamen, $whoradataexamen, $wfechagk, $whis, $wordennro, $wordite, $wtexto, $wid)
       {

          global $conex;
          global $key;

          $whora = (string) date("H:i:s");
		  $wtexto = utf8_decode($wtexto);

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


				switch($wtipo){
					case "E" : $datamensaje['mensaje'] = 'Evoluciones guardadas';
					break;

					case "I": $datamensaje['mensaje'] = 'Insumos guardados';
					break;

					case "IUCIUCE": $datamensaje['mensaje'] = 'Insumos de UCI/UCE guardados';
					break;

					case "PUCIUCE": $datamensaje['mensaje'] = 'Procedimientos de UCI/UCE guardados';
					break;
					
					case "INT": $datamensaje['mensaje'] = 'Interconsultas guardados';
					break;

					default:
					break;

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

                if ($wnum > 0)
                    {
                        $wmed = "";				
				
						while($row = mysql_fetch_array($res)){
							
							$wmed .= "<li>".$row['Medno1']." ".$row['Medno2']." ".$row['Medap1']." ".$row['Medap2']."</li>";
							
						}
						
						return $wmed;
                    }
                    else
                        return "Sin Médico";
            }


        // FUNCION PARA MOSTRAR LOS PENDIENTES DE GLUCOMETER POR COBRAR //09 DIC 2011 Jonatan Lopez
        function traer_glucometer($wmovhos, $whis, $wing, $wemp_pmla)
             {

                global $conex;
                global $whce;

                $wglucometrias = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Glucometrias');
				        $array_datos = array('historialg'=>0,'cantidadg'=>0);

                // CONSULTA PARA EXTRAER LA FECHA MAXIMA DE LAS GLUCOMETRIAS GUARD PARA UNA HIST E INGRESO
                $query =     "SELECT SUM( Glncan ) AS sumatoria "
                                ."FROM ".$wmovhos."_000119 "
                               ."WHERE Glnhis = '".$whis."'
                                     AND Glning = '".$wing."'
                                     AND Glnind = 'G'
                                     AND Glnest = 'on'";
                $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ". $query ." - ".mysql_error());
                $rows = mysql_fetch_array($res);
                $historial = $rows['sumatoria'];				
				
				        $query =     "SELECT MAX( CONCAT( Fecha_data, ' ', Hora_data ) ) AS FechaHora "
            							  ."FROM ".$wmovhos."_000119 "
            						     ."WHERE Glnhis = '".$whis."'
            								 AND Glning = '".$wing."'
            								 AND Glnind = 'G'
            								 AND Glnest = 'on'";
                $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ". $query ." - ".mysql_error());
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
				
				$array_datos['historialg'] = $historial;
				
				if($cantidadg > 0){
				
					$array_datos['cantidadg'] = $cantidadg;
				
				}
				
				
        return $array_datos;

        }


        // FUNCION PARA MOSTRAR LOS PENDIENTES DE GLUCOMETER POR COBRAR //09 DIC 2011 Jonatan Lopez
        function traer_nebulizaciones($wmovhos, $whis, $wing, $wemp_pmla)
              {

                global $conex;
                global $wcco;
				
				        $array_datos = array('historialg'=>0,'cantidadg'=>0);
						
                $pos_str = explode(" - ",$wcco);
                $wcco = $pos_str[0];

               
                $query =    "SELECT SUM( Glncan ) AS sumatoria "
                               ."FROM ".$wmovhos."_000119 "
                              ."WHERE Glnhis = '".$whis."'
                                    AND Glning = '".$wing."'
                                    AND Glnind = 'N'
                                    AND Glnest = 'on'";
                $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
                $rows = mysql_fetch_array($res);
                $historial = $rows['sumatoria']; 
				
				// CONSULTA PARA EXTRAER LA FECHA MAXIMA DE LAS NEBULIZACIONES GUARD PARA UNA HIST E INGRESO
				$query =    "SELECT MAX( CONCAT( Fecha_data, ' ', Hora_data ) ) AS FechaHora "
                               ."FROM ".$wmovhos."_000119 "
                              ."WHERE Glnhis = '".$whis."'
                                    AND Glning = '".$wing."'
                                    AND Glnind = 'N'
                                    AND Glnest = 'on'";
                $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
                $rows = mysql_fetch_array($res);
                $fechamax_nebus = $rows[0];

                //Cuenta la cantidad de nebulizaciones no guardadas a partir de la ultima fecha y hora de grabacion en el tabla 119 de movhos para la his e ing.
                $query =  " SELECT COUNT(".$wmovhos."_000015.Aplcan) "
                           ."   FROM ".$wmovhos."_000091, ".$wmovhos."_000015"
                           ."  WHERE Aplart = Arscod "
                           ."    AND Arstip = 'N' "
                           ."    AND Aplhis = '".$whis."'"
                           ."    AND Apling = '".$wing."'"
                           ."    AND Aplcco = '" .$wcco."'"
                           ."    AND Arscco = Aplcco "
                           ."    AND Aplest = 'on'"
                           ."    AND CONCAT( ".$wmovhos."_000015.Fecha_data, ' ', ".$wmovhos."_000015.Hora_data ) > '".$fechamax_nebus."'";
               $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$datos_nebus." - ".mysql_error());
               $rows = mysql_fetch_array($res);
			   $cantidadg = $rows[0];	
				
				$array_datos['historialn'] = $historial;
				
				if($cantidadg > 0){
				
					$array_datos['cantidadn'] = $cantidadg;
				
				}
			
				
               return $array_datos;

            }
			//------------------------------------------------------------------------
			// Funcion que crea un string de articulos para compararlo en el in
			//-------------------------------------------------------------------------
			function crear_string_in ( $wcampos_desc )
			{
				//$wcampos_desc = explode(";", $wconfinsumos); // Se explota el registro que esta separado por comas, para luego usarlo en el ciclo siguiente.
                $wcuantos = count($wcampos_desc);

				$cadena_in = "";
				for($i = 0; $i <= ($wcuantos-1); $i++)
                {
					$wnombres_posicion = explode("-", $wcampos_desc[$i]);
					if ($cadena_in == "")
						$cadena_in .= "( '".$wnombres_posicion[1]."'";
					else
						$cadena_in .= ", '".$wnombres_posicion[1]."'";
				}
				return $cadena_in.")";
			}

          // FUNCION PARA MOSTRAR LOS PENDIENTES POR GRABAR PARA LOS SOPORTES RESPIRATORIOS //Enero 12/2012 Jonatan Lopez
        function traer_insumos($wmovhos, $whis, $wing, $wemp_pmla)
             {

                global $conex;
                global $whce;
                $wcant_insumos = array('sin_grabar'=>0, 'grabados'=>0);

                $wforminsumos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'FormInsumos'); //Extrae el nombre del formulario para extraer los valores a cobrar.
                $wconfinsumos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ConfInsumos'); //Extrae el arreglo con dos numeros, el primero sirve para mostrar el nombre del
                                                                                                //articulo de la tabla hce_000002 y el segundo sirve para extraer la cantidad
                                                                                                //del campo movdat de la tabla hce_000205.
                $wcampos_desc = explode(";", $wconfinsumos); // Se explota el registro que esta separado por comas, para luego usarlo en el ciclo siguiente.


                // CONSULTA PARA EXTRAER LA FECHA MAXIMA DE LOS INSUMOS GUARD PARA UNA HIST E INGRESO
                $query =     "  SELECT MAX( CONCAT( Fecha_data, ' ', Hora_data ) ) AS FechaHora "
                                ."FROM ".$wmovhos."_000119 "
                               ."WHERE Glnhis = '".$whis."'
                                   AND Glning = '".$wing."'
                                   AND Glnind = 'I'
                                   AND Glnest = 'on'";
                $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ". $query ." - ".mysql_error());
                $rows = mysql_fetch_array($res);
                $fechamax_insumos = $rows[0];

				$strin_articulos_in = crear_string_in ( $wcampos_desc );

                 //CANTIDAD DE INSUMOS SIN GUARDAR A PARTIR DE LA ULTIMA FECHA Y HORA DE GRABACION DE INSUMOS EN LA 119 DE MOVHOS
                $query =    "SELECT SUM(".$whce."_".$wforminsumos.".movdat) as suma  "
                                ."FROM ".$whce."_000036, ".$whce."_".$wforminsumos
                                ." WHERE Firhis = '".$whis."'"
                               ."  AND Firing = '".$wing."'"
                               ."  AND Firhis = Movhis"
                               ."  AND Firing = Moving"
                               ."  AND Firpro = '".$wforminsumos."'"
                               ."  AND Firfir = 'on'"
                               ."  AND ".$whce."_000036.Fecha_data = ".$whce."_".$wforminsumos.".Fecha_data"
                               ."  AND ".$whce."_000036.Hora_data = ".$whce."_".$wforminsumos.".Hora_data"
                               ."  AND movcon in ".$strin_articulos_in." " //Esta posicion se refiere a la cantidad, en el formulario 000205 de hce.
                               ."  AND CONCAT( ".$whce."_000036.Fecha_data, ' ', ".$whce."_000036.Hora_data ) > '".$fechamax_insumos."'";
                $res= mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
                $rows = mysql_fetch_array($res);
                $wcant_insumos_suma = $rows['suma'];
				
				$query_hist =    "SELECT SUM(".$whce."_".$wforminsumos.".movdat) as historico "
                                ."FROM ".$whce."_000036, ".$whce."_".$wforminsumos
                                ." WHERE Firhis = '".$whis."'"
                               ."  AND Firing = '".$wing."'"
                               ."  AND Firhis = Movhis"
                               ."  AND Firing = Moving"
                               ."  AND Firpro = '".$wforminsumos."'"
                               ."  AND Firfir = 'on'"
                               ."  AND ".$whce."_000036.Fecha_data = ".$whce."_".$wforminsumos.".Fecha_data"
                               ."  AND ".$whce."_000036.Hora_data = ".$whce."_".$wforminsumos.".Hora_data"
                               ."  AND movcon in ".$strin_articulos_in." " //Esta posicion se refiere a la cantidad, en el formulario 000205 de hce.
                               ."  AND CONCAT( ".$whce."_000036.Fecha_data, ' ', ".$whce."_000036.Hora_data ) < '".$fechamax_insumos."'";
                $res_hist = mysql_query($query_hist, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query_hist." - ".mysql_error());
                $rows_hist = mysql_fetch_array($res_hist);
                $wcant_insumos_hist = $rows_hist['historico'];
				
				$wcant_insumos['sin_grabar'] = $wcant_insumos_suma;
				$wcant_insumos['grabados'] = $wcant_insumos_hist;
								
                return $wcant_insumos;

             }

    // FUNCION PARA MOSTRAR LOS PENDIENTES POR GRABAR PARA EL CENTRO DE COSTOS DE UCI/UCE //Enero 13/2016 Eimer Castro
    function traer_insumos_uci_uce($wmovhos, $whis, $wing, $wemp_pmla)
    {

        global $conex;
        global $whce;
        $wcant_insumos = 0;
	      $datos = array();
	      $datos1 = array();


        $wforminsumosuciuce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'FormulariosInsumosUCIUCE'); //Extrae el nombre del formulario para extraer los valores a cobrar.
  			$datos_formularios = explode(",",$wforminsumosuciuce);          

  			$query1 ="   SELECT SUM(Glncan) as sumIUCIUCE "
        						 ."FROM ".$wmovhos."_000119 "
        						."WHERE Glnhis = '".$whis."'
        							AND Glning = '".$wing."'
        							AND Glnind = 'IUCIUCE'
        							AND Glnest = 'on'";
  			$res1 = mysql_query($query1, $conex) or die("Error: ".mysql_errno()." - en el query: ". $query1 ." - ".mysql_error());
  			$rows1 = mysql_fetch_array($res1);
        $sum_insumosUCIUCE = $rows1['sumIUCIUCE'];

			$query =     "  SELECT MAX( CONCAT( Fecha_data, ' ', Hora_data ) ) AS FechaHora "
      							."FROM ".$wmovhos."_000119 "
      						   ."WHERE Glnhis = '".$whis."'
      							   AND Glning = '".$wing."'
      							   AND Glnind = 'IUCIUCE'
      							   AND Glnest = 'on'";
			$res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ". $query ." - ".mysql_error());
			$rows = mysql_fetch_array($res);
			$fechamax_insumos = $rows[0];
			
			
			foreach($datos_formularios as $key => $value){
				
				$form_campo = explode("-",$value);
				$wforminsumosuciuce = $form_campo[0];
				$wcampo_form_insuUCIUCE = $form_campo[1];
				
				$query_fm =    "SELECT MAX( CONCAT( ".$whce."_000036.Fecha_data, ' ', ".$whce."_000036.Hora_data ) ) as fecha_max "
								."FROM ".$whce."_000036, ".$whce."_".$wforminsumosuciuce
							  ." WHERE Firhis = '".$whis."'"
							   ."  AND Firing = '".$wing."'"
							   ."  AND Firhis = Movhis"
							   ."  AND Firing = Moving"
							   ."  AND Firpro = '".$wforminsumosuciuce."'"
							   ."  AND movcon = '".$wcampo_form_insuUCIUCE."'"
							   ."  AND Firfir = 'on'"
							   ."  AND ".$whce."_000036.Fecha_data = ".$whce."_".$wforminsumosuciuce.".Fecha_data"
							   ."  AND ".$whce."_000036.Hora_data = ".$whce."_".$wforminsumosuciuce.".Hora_data";
				$res_fm = mysql_query($query_fm, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query_fm." - ".mysql_error());
				$row_fm = mysql_fetch_assoc($res_fm);

				$wfecha_max_insumo = $row_fm['fecha_max'];

				 //CANTIDAD DE INSUMOS SIN GUARDAR A PARTIR DE LA ULTIMA FECHA Y HORA DE GRABACION DE INSUMOS EN LA 119 DE MOVHOS
			    $query =    "SELECT movcon,movdat, firhis, firing "
							."FROM ".$whce."_000036, ".$whce."_".$wforminsumosuciuce
						  ." WHERE Firhis = '".$whis."'"
						   ."  AND Firing = '".$wing."'"
						   ."  AND Firhis = Movhis"
						   ."  AND Firing = Moving"
						   ."  AND Firpro = '".$wforminsumosuciuce."'"
						   ."  AND movcon = '".$wcampo_form_insuUCIUCE."'"
						   ."  AND Firfir = 'on'"
						   ."  AND ".$whce."_000036.Fecha_data = ".$whce."_".$wforminsumosuciuce.".Fecha_data"
						   ."  AND ".$whce."_000036.Hora_data = ".$whce."_".$wforminsumosuciuce.".Hora_data"
						   ."  AND CONCAT( ".$whce."_000036.Fecha_data, ' ', ".$whce."_000036.Hora_data ) > '".$fechamax_insumosuciuce."'";
				$res= mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
				
				while($info = mysql_fetch_assoc($res))
				{

					$datos[$info['movcon']][] = $info;

				}

				// echo "<pre>";
				// echo print_r($datos, true);
				// echo "</pre>";


				$suma = 0;
				foreach($datos as $key_aux => $value_aux){

						$suma = 0;
						$suma_proc = 0;
						foreach($value_aux as $key => $value){

							$aux = explode("|", $value['movdat']);

							foreach($aux as $pos => $valor) {

								if($pos %2 != 0)
								{
									$suma += (int) $valor;	//Septiembre 21 de 2020. Se agrega casting a entero (int) para que no se genere warnings
								}
								
								
								//Si la cantidad grabada en la 119 de movhos es igual a la cantidad de insumos de la tabla 206 de hce y la fecha maxima en la tabla 119 es mayor a la fecha maxima de
								//la tabla 36 de  hce para la tabla 206, entonces el total sera cero, sino la suma total de insumos se le restara a la suma total de la tabla 119 de movhos.
								if(($datos1[$key_aux]['total_insumos'] == $sum_insumosUCIUCE) and ($fechamax_insumos < $wfecha_max_insumo)){

									$datos1[$key_aux]['total_insumos'] = 0;
									
										
								}else{
									
									$datos1[$key_aux]['total_insumos'] = $suma - $sum_insumosUCIUCE;
									$datos1[$key_aux]['grabados'] = $sum_insumosUCIUCE;

									}
					
							}

						}

					}

			}


			// echo "<pre align='left'>";
			// echo print_r($datos1, true);
			// echo "</pre>";

            return $datos1;
         }


	function traer_proc_uci_uce($wmovhos, $whis, $wing, $wemp_pmla)
         {

        global $conex;
		    global $wemp_pmla;
        global $whce;
        global $wmovhos;
        $wcant_insumostotal = '';

        $wforminsumosuciuce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'FormulariosInsumosUCIUCE'); //Extrae el nombre del formulario para extraer los valores a cobrar.
		$datos_formularios = explode(",",$wforminsumosuciuce);    


        $query1 ="SELECT SUM( Glncan ) AS historial "
                 ."FROM ".$wmovhos."_000119 "
                ."WHERE Glnhis = '".$whis."'
                    AND Glning = '".$wing."'
                    AND Glnind = 'PUCIUCE'
                    AND Glnest = 'on'";
        $res1 = mysql_query($query1, $conex) or die("Error: ".mysql_errno()." - en el query: ". $query1 ." - ".mysql_error());
        $rows1 = mysql_fetch_array($res1);
        $grabados = $rows1['historial']; 
		
		$query ="SELECT MAX( CONCAT( Fecha_data, ' ', Hora_data ) ) AS FechaHora "
                 ."FROM ".$wmovhos."_000119 "
                ."WHERE Glnhis = '".$whis."'
                    AND Glning = '".$wing."'
                    AND Glnind = 'PUCIUCE'
                    AND Glnest = 'on'";
        $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ". $query ." - ".mysql_error());
        $rows = mysql_fetch_array($res);
        $fechamax_insumosuciuce = $rows[0];

		$datos = array();
		$informacion = array();
		
		foreach($datos_formularios as $key => $value){
				
			$form_campo = explode("-",$value);
			$wforminsumosuciuce = $form_campo[0];
			$wcampo_form_procUCIUCE = $form_campo[2];
			$wcampo_form_procUCIUCE_aux = $form_campo[2];
			
			$wcampo_form_procUCIUCE = explode("*",$wcampo_form_procUCIUCE);
			$wcampo_form_procUCIUCE = implode("','",$wcampo_form_procUCIUCE);
			
			$query =   "SELECT movcon,movdat, firhis, firing "
						."FROM ".$whce."_000036, ".$whce."_".$wforminsumosuciuce
					  ." WHERE Firhis = '".$whis."'"
					   ."  AND Firing = '".$wing."'"
					   ."  AND Firhis = Movhis"
					   ."  AND Firing = Moving"
					   ."  AND Firpro = '".$wforminsumosuciuce."'"
					   ."  AND movcon in ('".$wcampo_form_procUCIUCE."')"
					   ."  AND Firfir = 'on'"
					   ."  AND ".$whce."_000036.Fecha_data = ".$whce."_".$wforminsumosuciuce.".Fecha_data"
					   ."  AND ".$whce."_000036.Hora_data = ".$whce."_".$wforminsumosuciuce.".Hora_data"
					   ."  AND '".$fechamax_insumosuciuce."' < CONCAT( ".$whce."_000036.Fecha_data, ' ', ".$whce."_000036.Hora_data )";
			$res= mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
						
			while($info = mysql_fetch_assoc($res))
			{

				$informacion[] = $info['movdat'];

			}
		}
		
		// echo "<pre>";
		// print_r($informacion);
		// echo "</pre>";
		
		// $datos = array();
		foreach($informacion as $key => $value){

			$aux_datos = explode("*",$value);
			unset($aux_datos[0]);
			
			foreach($aux_datos as $key1 => $value1){
				
				//Cuando es el consecutivo 9 del formulario
				if(substr_count($value1, '|') > 1){
					
					$procedimiento = explode("|", $value1);
					
					// echo "<div align=left>";
					// echo "<pre>";
					// print_r($procedimiento);
					// echo "</pre>";
					// echo "</div>";
					
					$nom_procedimiento = $procedimiento[0];
					$cantidad_datos = $procedimiento[1];
					
				
					
					if(!array_key_exists($nom_procedimiento, $datos))
					{
						$datos[$nom_procedimiento] = array("descripcion"=>$nom_procedimiento, "cantidad"=>$cantidad_datos);
						
					}else{
						
						$datos[$nom_procedimiento]["cantidad"] = $datos[$nom_procedimiento]["cantidad"]+$cantidad_datos;
						
					}
					
					
				}else{
					
					//Cuando es el consecutivo 8
					if(strpos($value1,"|")){
					
						$procedimiento = strstr($value1, '|');

					}else{

						$procedimiento = $value1;

					}
					
					
					$cantidad_datos = 1;
					if(!array_key_exists($procedimiento, $datos))
					{
						$datos[$procedimiento] = array("descripcion"=>$procedimiento, "cantidad"=>$cantidad_datos);
					}
					else
					{
						$datos[$procedimiento]["cantidad"]++;
					}
					
				}				

			}

		}


		/* foreach($datos as $key1 => $value1){

			if($value1['descripcion'] !=''){

				$suma = $suma + $value1['cantidad'];


			}
		} */
		
		
		
		
		foreach($datos as $key1 => $value1){

			$dato_descripcion = str_replace("\n", "", $value1['descripcion']);

			if(trim($dato_descripcion) != ''){

				$suma = $suma + $value1['cantidad'];
			}

		}
	

		$datos1[$wcampo_form_procUCIUCE_aux]['total_procedimientos'] = $suma;
		$datos1[$wcampo_form_procUCIUCE_aux]['grabados'] = $grabados;
		
				
         return $datos1;


	}

     //Aqui se consulta quien es el profesor que confirma el formulario para un medico residente.
     function consultar_profe_confirma($whis, $wing, $wfecha_registro, $whora_registro, $wformulario, $wposicion)
     {

        global $conex;
        global $whce;
        global $wemp_pmla;
        global $wmovhos;

        $wdatos_profesor = array();        

        //Consulto en la tabla hce_000068 (".$whce."_".$wformulario.") con la fecha, hora, historia, ingreso y posicion (movcon = '".$wposicion."'"),
        //para extraer el codigo del profesor que firmo el formulario.
        $query =         "SELECT movusu, u.descripcion, espmed.Medesp, nomesp.Espnom, Meddoc "
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
        $wcedula_medico = $rows['Meddoc']; // Nombre de la especialidad.

        $wdatos_profesor = array('codigo_profesor'=>$wprofe_firma,'nombre'=>$wnombre_profe, 'codigo_especialidad'=> $wcodigo_especialidad, 'descrip_especialidad'=> $wnombre_especialidad, 'fecha_firma'=> $wfecha_registro, 'cedula_medico'=> $wcedula_medico );

        return $wdatos_profesor;
     }


function traer_interconsultas($wmovhos, $whis, $wing, $wemp_pmla, &$winterconsultas_pendientes, &$winterconsultas_grabadas){

		global $conex;
		global $whce;

		//Extrae el nombre del formulario donde se registran las evoluciones.
		$wform_interc = consultarAliasPorAplicacion($conex, $wemp_pmla, 'FormularioInterconsultas');
		$wform_posicion_int = explode("-", $wform_interc); // Se explota el registro que esta separado por comas, para luego usarlo en el ciclo siguiente.
		$wformulario = $wform_posicion_int[0];
		$wposicion = $wform_posicion_int[1];
		
		// CONSULTA PARA EXTRAER LA FECHA MAXIMA DE LAS EVOLUCIONES GUARD PARA UNA HIST E INGRESO
		$query =     "  SELECT MAX( CONCAT( Fecha_data, ' ', Hora_data ) ) AS FechaHora "
						."FROM ".$wmovhos."_000119 "
					   ."WHERE Glnhis = '".$whis."'
						   AND Glning = '".$wing."'
						   AND Glnind = 'INT'
						   AND Glnest = 'on'";
		$res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ". $query ." - ".mysql_error());
		$rows = mysql_fetch_array($res);
		$fechamax_inter = $rows['FechaHora'];


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
		$res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
		$res1 = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());

		//Se crea por defecto la posicion medico de turno, para asociarlo a los alumnos que tienen mas de un profesor.
		$array_profesores['sin_grabar'] = array(  'medico_turno'=> array(
															   'cuantos'=>array(),
												   'nombre_especialista'=>'MEDICO DE TURNO',
															   'cod_esp'=>'medico_turno',
												   'nombre_especialidad'=>'MEDICO DE TURNO',
																'alumnos'=>array()
													   )
									);

		// echo "<div align=left>";
		// echo "<pre>";
		// print_r($array_profesores['sin_grabar']);
		// echo "<pre>";
		// echo "</div>";

		$array_alumnos = array();
		//Al recorrer el resultado de la consulta se crea un arreglo $array_profesores['sin_grabar'][$row['usucod']][dato] y se agrega al arreglo $array_profesores['sin_grabar'][$row['usucod']]['alumnos'][],
		//todos los alumnnos asignados a el, solo se agregaran si la posicion $alumno del foreach es diferente de vacio y diferente de punto.
		while($row = mysql_fetch_array($res))
		{
			if(!array_key_exists($row['usucod'], $array_profesores['sin_grabar']))
			{
				$array_profesores['sin_grabar'][$row['usucod']] = array();
			}

			$array_profesores['sin_grabar'][$row['usucod']]['cuantos'] = array();
			$array_profesores['sin_grabar'][$row['usucod']]['nombre_especialista'] = $row['descripcion'];
			$array_profesores['sin_grabar'][$row['usucod']]['cod_esp'] = $row['Medesp'];
			$array_profesores['sin_grabar'][$row['usucod']]['nombre_especialidad'] = $row['Espnom'];
			$array_profesores['sin_grabar'][$row['usucod']]['cedula_medico'] = $row['Meddoc'];
			$explo_alum = explode(",", $row['usualu']);

			foreach ($explo_alum as $key => $alumno)
				{
					$array_profesores['sin_grabar'][$row['usucod']]['alumnos'][] = $alumno;

					//Solo se agregan los que tengan datos en la posicion $alumno and diferente de punto.
					if(!empty($alumno) and $alumno != '.')
						{
						$array_alumnos[$alumno]['profesor'][] = $row['usucod'];
						}
				}
		}

		//Consulta todas las  evoluciones que no se han registrado a partir de la ultima fecha y hora de registro
		//en la tabla 119 de movhos para la historia e ingreso y el parametro Glnind = 'INT', se trae tambien el nombre, la especialidad y el codigo de la especialidad.
		$query =    " SELECT firusu, usuhce.usualu, u.descripcion, usuhce.usures, ".$whce."_000036.Fecha_data as fechafir, ".$whce."_000036.Hora_data as horafir, "
					." ".$whce."_000036.firhis, ".$whce."_000036.firing, ".$whce."_000036.firrol "
					."  FROM ".$whce."_000036, ".$whce."_000020 as usuhce
						INNER JOIN
						usuarios as u on (u.codigo = usuhce.Usucod )"
					." WHERE Firhis = '".$whis."'"
					."   AND Firing = '".$wing."'"
					."   AND Firpro = '".$wformulario."'"
					."   AND Firfir = 'on'"
					."   AND firusu = usucod "
					."   AND u.Activo = 'A' "
					."   AND CONCAT( ".$whce."_000036.Fecha_data, ' ', ".$whce."_000036.Hora_data ) > '".$fechamax_inter."'";
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
							$wprofe_confirma = consultar_profe_confirma($row1['firhis'],$row1['firing'],$row1['fechafir'], $row1['horafir'], $wformulario, $wposicion );
							$el_profe = $wprofe_confirma['codigo_profesor'];

							//Si ese profesor no esta en el arreglo de profesores entonces lo agregara.
							if (!array_key_exists($el_profe, $array_profesores['sin_grabar']))
							{
								$array_profesores['sin_grabar'][$el_profe]['cuantos'] = array();
								$array_profesores['sin_grabar'][$el_profe]['nombre_especialista'] = $wprofe_confirma['nombre'];
								$array_profesores['sin_grabar'][$el_profe]['cod_esp'] = $wprofe_confirma['codigo_especialidad'];
								$array_profesores['sin_grabar'][$el_profe]['nombre_especialidad'] = $wprofe_confirma['descrip_especialidad'];
								$array_profesores['sin_grabar'][$el_profe]['cedula_medico'] = $wprofe_confirma['cedula_medico'];

							}

							$array_profesores['sin_grabar'][$el_profe]['cuantos'][] = $row1['fechafir'];


							//Se declara esta variable para que se le asigne al medico de turno la especialidad de uno de los profesores que tiene asignado, la especialidad
							//del profesor siempre sera la misma en donde aparezca el alumno.
							
							//Se comenta esta variable ya que debe traer exactamente la especialidad del medico que le confirmo el formulario al alumno.
							//$cod_profesor = $array_alumnos[$row1['firusu']]['profesor'][0];

							//Al array profesores en la posicion cod_esp se le asigna la primera aparicion de codigo de su profesor.
							$array_profesores['sin_grabar'][$el_profe]['cod_esp'] = $array_profesores['sin_grabar'][$el_profe]['cod_esp'];

							//Al array profesores en la posicion nombre_especialidad se le asigna la primera aparicion de especialidad de su profesor.
							$array_profesores['sin_grabar'][$el_profe]['nombre_especialidad'] = $array_profesores['sin_grabar'][$el_profe]['nombre_especialidad'];
					}
				 else
					{
							//Si el especialista solo aparece una vez y no es alumno de nadie entonces deja los datos como vienen en el arreglo.
							$el_profe = $array_alumnos[$row1['firusu']]['profesor'][0];
							//Codigo del profesor y cuantas apariciones tiene.
							$array_profesores['sin_grabar'][$el_profe]['cuantos'][] = $row1['fechafir'];

					}

				}

			}
			//Si el usuario no es residente, entonces la informacion se mantendra como viene en el arreglo de profesores.
			else
			{

				if(!array_key_exists($row1['firusu'], $array_profesores['sin_grabar'])){

				   $array_profesores['sin_grabar'][$row1['firusu']]['cuantos'] = array();
				}

			   $array_profesores['sin_grabar'][$row1['firusu']]['cuantos'][] = $row1['fechafir'];

			}

		}

		$array_aux = $array_profesores['sin_grabar']; // Auxiliar para recorrer array_profesores['sin_grabar'] y eliminar los que tengan cero.

		//Elimino los elementos del arreglo de profesores que tienen la posicion cuantos en cero.
		foreach ($array_aux as $key => $value)
			{
				if(count($value['cuantos']) == '0')
				{
					unset($array_profesores['sin_grabar'][$key]);
				}else{

				//Cuento las evoluciones por medico, el arreglo en la posicion $array_profesores['sin_grabar'][$key]['cuantos'] contiene las fechas de las evoluciones firmadas.
				$winterconsultas_pendientes += count($array_profesores['sin_grabar'][$key]['cuantos']);

				}

			}
		
		//-----------------------------------------------------**----------------------------
		//Historico
		
		//Consulta todas las  evoluciones que no se han registrado a partir de la ultima fecha y hora de registro
		//en la tabla 119 de movhos para la historia e ingreso y el parametro Glnind = 'INT', se trae tambien el nombre, la especialidad y el codigo de la especialidad.
		$query_hist =    " SELECT firusu, usuhce.usualu, u.descripcion, usuhce.usures, ".$whce."_000036.Fecha_data as fechafir, ".$whce."_000036.Hora_data as horafir, "
					." ".$whce."_000036.firhis, ".$whce."_000036.firing, ".$whce."_000036.firrol "
					."  FROM ".$whce."_000036, ".$whce."_000020 as usuhce
						INNER JOIN
						usuarios as u on (u.codigo = usuhce.Usucod )"
					." WHERE Firhis = '".$whis."'"
					."   AND Firing = '".$wing."'"
					."   AND Firpro = '".$wformulario."'"
					."   AND Firfir = 'on'"
					."   AND firusu = usucod "
					."   AND u.Activo = 'A' "
					."   AND CONCAT( ".$whce."_000036.Fecha_data, ' ', ".$whce."_000036.Hora_data ) < '".$fechamax_inter."'";
		$res_hist = mysql_query($query_hist, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query_hist." - ".mysql_error());
		$num_hist = mysql_num_rows($res_hist);
		
		if($num_hist > 0){			
			
			$array_profesores['historico'] = array(  'medico_turno'=> array(
															   'cuantos'=>array(),
												   'nombre_especialista'=>'MEDICO DE TURNO',
															   'cod_esp'=>'medico_turno',
												   'nombre_especialidad'=>'MEDICO DE TURNO',
																'alumnos'=>array()
													   )
									);
		
		
			while($row1 = mysql_fetch_array($res1))
			{
				if(!array_key_exists($row1['usucod'], $array_profesores['historico']))
				{
					$array_profesores['historico'][$row1['usucod']] = array();
				}

				$array_profesores['historico'][$row1['usucod']]['cuantos'] = array();
				$array_profesores['historico'][$row1['usucod']]['nombre_especialista'] = $row1['descripcion'];
				$array_profesores['historico'][$row1['usucod']]['cod_esp'] = $row1['Medesp'];
				$array_profesores['historico'][$row1['usucod']]['nombre_especialidad'] = $row1['Espnom'];
				$array_profesores['historico'][$row1['usucod']]['cedula_medico'] = $row1['Meddoc'];
				$explo_alum = explode(",", $row1['usualu']);

				foreach ($explo_alum as $key => $alumno)
					{
						$array_profesores['historico'][$row1['usucod']]['alumnos'][] = $alumno;

						//Solo se agregan los que tengan datos en la posicion $alumno and diferente de punto.
						if(!empty($alumno) and $alumno != '.')
							{
							$array_alumnos[$alumno]['profesor'][] = $row1['usucod'];
							}
					}
			}

			
			$array_alumnos_historico = array();

			
			while($row1 = mysql_fetch_array($res_hist))
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
								$wprofe_confirma = consultar_profe_confirma($row1['firhis'],$row1['firing'],$row1['fechafir'], $row1['horafir'], $wformulario, $wposicion );
								$el_profe = $wprofe_confirma['codigo_profesor'];

								//Si ese profesor no esta en el arreglo de profesores entonces lo agregara.
								if (!array_key_exists($el_profe, $array_profesores['historico']))
								{
									$array_profesores['historico'][$el_profe]['cuantos'] = array();
									$array_profesores['historico'][$el_profe]['nombre_especialista'] = $wprofe_confirma['nombre'];
									$array_profesores['historico'][$el_profe]['cod_esp'] = $wprofe_confirma['codigo_especialidad'];
									$array_profesores['historico'][$el_profe]['nombre_especialidad'] = $wprofe_confirma['descrip_especialidad'];
									$array_profesores['historico'][$el_profe]['cedula_medico'] = $wprofe_confirma['cedula_medico'];

								}

								$array_profesores['historico'][$el_profe]['cuantos'][] = $row1['fechafir'];


								//Se declara esta variable para que se le asigne al medico de turno la especialidad de uno de los profesores que tiene asignado, la especialidad
								//del profesor siempre sera la misma en donde aparezca el alumno.
								
								//Se comenta esta variable ya que debe traer exactamente la especialidad del medico que le confirmo el formulario al alumno.
								//$cod_profesor = $array_alumnos[$row1['firusu']]['profesor'][0];

								//Al array profesores en la posicion cod_esp se le asigna la primera aparicion de codigo de su profesor.
								$array_profesores['historico'][$el_profe]['cod_esp'] = $array_profesores['historico'][$el_profe]['cod_esp'];

								//Al array profesores en la posicion nombre_especialidad se le asigna la primera aparicion de especialidad de su profesor.
								$array_profesores['historico'][$el_profe]['nombre_especialidad'] = $array_profesores['historico'][$el_profe]['nombre_especialidad'];
						}
					 else
						{
								//Si el especialista solo aparece una vez y no es alumno de nadie entonces deja los datos como vienen en el arreglo.
								$el_profe = $array_alumnos[$row1['firusu']]['profesor'][0];
								//Codigo del profesor y cuantas apariciones tiene.
								$array_profesores['historico'][$el_profe]['cuantos'][] = $row1['fechafir'];

						}

					}

				}
				//Si el usuario no es residente, entonces la informacion se mantendra como viene en el arreglo de profesores.
				else
				{

					if(!array_key_exists($row1['firusu'], $array_profesores['historico'])){

					   $array_profesores['historico'][$row1['firusu']]['cuantos'] = array();
					}

				   $array_profesores['historico'][$row1['firusu']]['cuantos'][] = $row1['fechafir'];

				}

			}

			$array_aux = $array_profesores['historico']; // Auxiliar para recorrer array_profesores['historico'] y eliminar los que tengan cero.

			//Elimino los elementos del arreglo de profesores que tienen la posicion cuantos en cero.
			foreach ($array_aux as $key => $value)
				{
					if(count($value['cuantos']) == '0')
					{
						unset($array_profesores['historico'][$key]);
					}else{

					//Cuento las evoluciones por medico, el arreglo en la posicion $array_profesores['historico'][$key]['cuantos'] contiene las fechas de las evoluciones firmadas.
					$winterconsultas_grabadas += count($array_profesores['historico'][$key]['cuantos']);

					}

				}
		}
		
	   	
	   // echo "<div align=left>";
	   // echo "<pre>";
	   // print_r($array_profesores['historico']);
	   // echo "<pre>";
	   // echo "</div>";

	  return $array_profesores;


}

// FUNCION PARA MOSTRAR LOS PENDIENTES POR GRABAR PARA LAS EVOLUCIONES //Enero 12/2012 Jonatan Lopez
function traer_evoluciones($wmovhos, $whis, $wing, $wemp_pmla, &$wevoluciones_pendientes, &$wevoluciones_grabadas){

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
		$res1 = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());

		//Se crea por defecto la posicion medico de turno, para asociarlo a los alumnos que tienen mas de un profesor.
		$array_profesores['sin_grabar'] = array(  'medico_turno'=> array(
															   'cuantos'=>array(),
												   'nombre_especialista'=>'MEDICO DE TURNO',
															   'cod_esp'=>'medico_turno',
												   'nombre_especialidad'=>'MEDICO DE TURNO',
																'alumnos'=>array()
													   )
									);
		

		// echo "<div align=left>";
		// echo "<pre>";
		// print_r($array_profesores['sin_grabar']);
		// echo "<pre>";
		// echo "</div>";

		$array_alumnos = array();
		//Al recorrer el resultado de la consulta se crea un arreglo $array_profesores['sin_grabar'][$row['usucod']][dato] y se agrega al arreglo $array_profesores['sin_grabar'][$row['usucod']]['alumnos'][],
		//todos los alumnnos asignados a el, solo se agregaran si la posicion $alumno del foreach es diferente de vacio y diferente de punto.
		while($row = mysql_fetch_array($res))
		{
			if(!array_key_exists($row['usucod'], $array_profesores['sin_grabar']))
			{
				$array_profesores['sin_grabar'][$row['usucod']] = array();
			}

			$array_profesores['sin_grabar'][$row['usucod']]['cuantos'] = array();
			$array_profesores['sin_grabar'][$row['usucod']]['nombre_especialista'] = $row['descripcion'];
			$array_profesores['sin_grabar'][$row['usucod']]['cod_esp'] = $row['Medesp'];
			$array_profesores['sin_grabar'][$row['usucod']]['nombre_especialidad'] = $row['Espnom'];
			$array_profesores['sin_grabar'][$row['usucod']]['cedula_medico'] = $row['Meddoc'];
			$explo_alum = explode(",", $row['usualu']);

			foreach ($explo_alum as $key => $alumno)
				{
					$array_profesores['sin_grabar'][$row['usucod']]['alumnos'][] = $alumno;

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
							if (!array_key_exists($el_profe, $array_profesores['sin_grabar']))
							{
								$array_profesores['sin_grabar'][$el_profe]['cuantos'] = array();
								$array_profesores['sin_grabar'][$el_profe]['nombre_especialista'] = $wprofe_confirma['nombre'];
								$array_profesores['sin_grabar'][$el_profe]['cod_esp'] = $wprofe_confirma['codigo_especialidad'];
								$array_profesores['sin_grabar'][$el_profe]['nombre_especialidad'] = $wprofe_confirma['descrip_especialidad'];
								$array_profesores['sin_grabar'][$el_profe]['cedula_medico'] = $wprofe_confirma['cedula_medico'];

							}

							$array_profesores['sin_grabar'][$el_profe]['cuantos'][] = $row1['fechafir'];


							//Se declara esta variable para que se le asigne al medico de turno la especialidad de uno de los profesores que tiene asignado, la especialidad
							//del profesor siempre sera la misma en donde aparezca el alumno.
							
							//Se comenta esta variable ya que debe traer exactamente la especialidad del medico que le confirmo el formulario al alumno.
							//$cod_profesor = $array_alumnos[$row1['firusu']]['profesor'][0];

							//Al array profesores en la posicion cod_esp se le asigna la primera aparicion de codigo de su profesor.
							$array_profesores['sin_grabar'][$el_profe]['cod_esp'] = $array_profesores['sin_grabar'][$el_profe]['cod_esp'];

							//Al array profesores en la posicion nombre_especialidad se le asigna la primera aparicion de especialidad de su profesor.
							$array_profesores['sin_grabar'][$el_profe]['nombre_especialidad'] = $array_profesores['sin_grabar'][$el_profe]['nombre_especialidad'];
					}
				 else
					{
							//Si el especialista solo aparece una vez y no es alumno de nadie entonces deja los datos como vienen en el arreglo.
							$el_profe = $array_alumnos[$row1['firusu']]['profesor'][0];
							//Codigo del profesor y cuantas apariciones tiene.
							$array_profesores['sin_grabar'][$el_profe]['cuantos'][] = $row1['fechafir'];

					}

				}

			}
			//Si el usuario no es residente, entonces la informacion se mantendra como viene en el arreglo de profesores.
			else
			{

				if(!array_key_exists($row1['firusu'], $array_profesores['sin_grabar'])){

				   $array_profesores['sin_grabar'][$row1['firusu']]['cuantos'] = array();
				}

			   $array_profesores['sin_grabar'][$row1['firusu']]['cuantos'][] = $row1['fechafir'];

			}

		}

		$array_aux = $array_profesores['sin_grabar']; // Auxiliar para recorrer array_profesores['sin_grabar'] y eliminar los que tengan cero.

		//Elimino los elementos del arreglo de profesores que tienen la posicion cuantos en cero.
		foreach ($array_aux as $key => $value)
			{
				if(count($value['cuantos']) == '0')
				{
					unset($array_profesores['sin_grabar'][$key]);
				}else{

				//Cuento las evoluciones por medico, el arreglo en la posicion $array_profesores['sin_grabar'][$key]['cuantos'] contiene las fechas de las evoluciones firmadas.
				$wevoluciones_pendientes += count($array_profesores['sin_grabar'][$key]['cuantos']);

				}

			}
		

		
		//--------------------------------------------------------------//------------------------------
		//Historico grabado
		$query_hist =    " SELECT * FROM(
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
						."   AND CONCAT( ".$whce."_000036.Fecha_data, ' ', ".$whce."_000036.Hora_data ) < '".$fechamax_evolucion."'					
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
						."   AND CONCAT( ".$whce."_000036.Fecha_data, ' ', ".$whce."_000036.Hora_data ) < '".$fechamax_evolucion."') as t";
		$res_hist = mysql_query($query_hist, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query_hist." - ".mysql_error());
		$num_hist = mysql_num_rows($res_hist);	
		
		//echo $query_hist."<br>";
		if($num_hist > 0){
		
		$array_profesores['historico'] = array(  'medico_turno'=> array(
															   'cuantos'=>array(),
												   'nombre_especialista'=>'MEDICO DE TURNO',
															   'cod_esp'=>'medico_turno',
												   'nombre_especialidad'=>'MEDICO DE TURNO',
																'alumnos'=>array()
													   )
									);
		
		
		while($row1 = mysql_fetch_array($res1))
		{
			if(!array_key_exists($row1['usucod'], $array_profesores['historico']))
			{
				$array_profesores['historico'][$row1['usucod']] = array();
			}

			$array_profesores['historico'][$row1['usucod']]['cuantos'] = array();
			$array_profesores['historico'][$row1['usucod']]['nombre_especialista'] = $row1['descripcion'];
			$array_profesores['historico'][$row1['usucod']]['cod_esp'] = $row1['Medesp'];
			$array_profesores['historico'][$row1['usucod']]['nombre_especialidad'] = $row1['Espnom'];
			$array_profesores['historico'][$row1['usucod']]['cedula_medico'] = $row1['Meddoc'];
			$explo_alum = explode(",", $row1['usualu']);

			foreach ($explo_alum as $key => $alumno)
				{
					$array_profesores['historico'][$row1['usucod']]['alumnos'][] = $alumno;

					//Solo se agregan los que tengan datos en la posicion $alumno and diferente de punto.
					if(!empty($alumno) and $alumno != '.')
						{
						$array_alumnos[$alumno]['profesor'][] = $row1['usucod'];
						}
				}
		}

		
		$array_alumnos_historico = array();
		
		while($row1 = mysql_fetch_array($res_hist))
		{
		
			//Aqui solo permite ingresar si el usuario es residente, osea alumno.
			if($row1['usures'] == 'on')
			{

			  //Verifica que en el array_alumnos_historico se encuentre el codigo del alumno.
			  if(array_key_exists($row1['firusu'], $array_alumnos_historico))
				{

				  //Si un alumno tiene varios profesores, pondra como especialista la palabra medico de turno.
				  if (count($array_alumnos_historico[$row1['firusu']]['profesor'])>1)
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
							if (!array_key_exists($el_profe, $array_profesores['historico']))
							{
								$array_profesores['historico'][$el_profe]['cuantos'] = array();
								$array_profesores['historico'][$el_profe]['nombre_especialista'] = $wprofe_confirma['nombre'];
								$array_profesores['historico'][$el_profe]['cod_esp'] = $wprofe_confirma['codigo_especialidad'];
								$array_profesores['historico'][$el_profe]['nombre_especialidad'] = $wprofe_confirma['descrip_especialidad'];
								$array_profesores['historico'][$el_profe]['cedula_medico'] = $wprofe_confirma['cedula_medico'];

							}

							$array_profesores['historico'][$el_profe]['cuantos'][] = $row1['fechafir'];


							//Se declara esta variable para que se le asigne al medico de turno la especialidad de uno de los profesores que tiene asignado, la especialidad
							//del profesor siempre sera la misma en donde aparezca el alumno.
							
							//Se comenta esta variable ya que debe traer exactamente la especialidad del medico que le confirmo el formulario al alumno.
							//$cod_profesor = $array_alumnos_historico[$row1['firusu']]['profesor'][0];

							//Al array profesores en la posicion cod_esp se le asigna la primera aparicion de codigo de su profesor.
							$array_profesores['historico'][$el_profe]['cod_esp'] = $array_profesores['historico'][$el_profe]['cod_esp'];

							//Al array profesores en la posicion nombre_especialidad se le asigna la primera aparicion de especialidad de su profesor.
							$array_profesores['historico'][$el_profe]['nombre_especialidad'] = $array_profesores['historico'][$el_profe]['nombre_especialidad'];
					}
				 else
					{
							//Si el especialista solo aparece una vez y no es alumno de nadie entonces deja los datos como vienen en el arreglo.
							$el_profe = $array_alumnos_historico[$row1['firusu']]['profesor'][0];
							//Codigo del profesor y cuantas apariciones tiene.
							$array_profesores['historico'][$el_profe]['cuantos'][] = $row1['fechafir'];

					}

				}

			}
			//Si el usuario no es residente, entonces la informacion se mantendra como viene en el arreglo de profesores.
			else
			{	
				
				if(!array_key_exists($row1['firusu'], $array_profesores['historico'])){
				  
				   $array_profesores['historico'][$row1['firusu']]['cuantos'] = array();
				}

			   $array_profesores['historico'][$row1['firusu']]['cuantos'][] = $row1['fechafir'];

			}

		}		
		
		$array_aux = $array_profesores['historico']; // Auxiliar para recorrer array_profesores['historico'] y eliminar los que tengan cero.

		//Elimino los elementos del arreglo de profesores que tienen la posicion cuantos en cero.
		foreach ($array_aux as $key => $value)
			{

				if(count($value['cuantos']) == '0')
				{
					unset($array_profesores['historico'][$key]);
					
				}else{

				//Cuento las evoluciones por medico, el arreglo en la posicion $array_profesores['historico'][$key]['cuantos'] contiene las fechas de las evoluciones firmadas.
				    
				    $wevoluciones_grabadas += count($array_profesores['historico'][$key]['cuantos']);

				}

			}	
		
		
		}
		
	   // echo "<div align=left>";
	   // echo "<pre>";
	   // print_r($array_profesores);
	   // echo "<pre>";
	   // echo "</div>";

	  return $array_profesores;

}

        // FUNCION PARA MOSTRAR LOS PENDIENTES POR GRABAR PARA LOS SOPORTES RESPIRATORIOS //Enero 12/2012 Jonatan Lopez
        function traer_oximetrias($wmovhos, $whis, $wing, $wemp_pmla)
        {

                global $conex;
                global $whce;
				        global $wcco;
				
        				$wccouciuce = consultar_cco_uciuce($wcco);
        				$array_datos = array('historialo'=>0,'cantidado'=>0);	
					
                $woximetrias = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Soporterespiratorio');
                $wcampos = explode("*", $woximetrias);
                $wtablat = $wcampos[0];
				
        				$wcampot = explode("-",$wcampos[1]);	//Si es UCI/UCE el consecutivo para los oxigenos es el 20.
        				$consecutivos = implode("','",$wcampot);

                // CONSULTA PARA EXTRAER LA FECHA MAXIMA DE LOS OXIGENOS GUARD PARA UNA HIST E INGRESO
                $query =     "SELECT SUM( Glncan ) AS sumatoria "
                                ."FROM ".$wmovhos."_000119 "
                               ."WHERE Glnhis = '".$whis."'
                                     AND Glning = '".$wing."'
                                     AND Glnind = 'O'
                                     AND Glnest = 'on'";
                $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ". $query ." - ".mysql_error());
                $rows = mysql_fetch_array($res);
                $historial = $rows['sumatoria'];
				
        				// CONSULTA PARA EXTRAER LA FECHA MAXIMA DE LOS OXIGENOS GUARD PARA UNA HIST E INGRESO
        				$query =     "SELECT MAX(CONCAT( Fecha_data, ' ', Hora_data ) ) AS FechaHora "
                                        ."FROM ".$wmovhos."_000119 "
                                       ."WHERE Glnhis = '".$whis."'
                                             AND Glning = '".$wing."'
                                             AND Glnind = 'O'
                                             AND Glnest = 'on'";
                $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ". $query ." - ".mysql_error());
                $rows = mysql_fetch_array($res);
                $fechamax_transf = $rows[0];				
				
                //CANTIDAD DE OXIMETRIAS SIN GUARDAR A PARTIR DE LA ULTIMA FECHA Y HORA DE REGISTRO DE OXIGENOS EN LA TABLA 119 DE MOVHOS.
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
                               ."  AND movcon in ('".$consecutivos."')"
                               ."  AND CONCAT( ".$whce."_000036.Fecha_data, ' ', ".$whce."_000036.Hora_data ) > '".$fechamax_transf."'";
                $res= mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
                $rows = mysql_fetch_array($res);
                $cantidadoxi = $rows[0];
				
				
        				$array_datos['historialo'] = $historial;
        				
        				if($cantidadoxi > 0){
        				
        					$array_datos['cantidado'] = $cantidadoxi;
        				
        				}
								
                return $array_datos;	

             }

        // FUNCION PARA MOSTRAR LOS PENDIENTES POR GRABAR PARA LAS TRANSFUSIONES //Enero 12/2012 Jonatan Lopez
        function traer_transfusiones($wmovhos, $whis, $wing, $wemp_pmla)
             {

                global $conex;
                global $whce;
				
				$array_datos = array('historialt'=>0,'cantidadt'=>0);	
				
                $wtransfusiones = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Transfusiones');
                $wcampos = explode("-", $wtransfusiones);
                $wtablat = $wcampos[0];
                $wcampot = $wcampos[1];

                
                $query =     "SELECT SUM( Glncan ) AS sumatoria "
                                ."FROM ".$wmovhos."_000119 "
                               ."WHERE Glnhis = '".$whis."'
                                   AND Glning = '".$wing."'
                                   AND Glnind = 'T'
                                   AND Glnest = 'on'";
                $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ". $query ." - ".mysql_error());
                $rows = mysql_fetch_array($res);
                $historial = $rows['sumatoria'];
				
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

                $array_datos['historialt'] = $historial;
				
				if($cantidadtransf > 0){
				
					$array_datos['cantidadt'] = $cantidadtransf;
				
				}
								
                return $array_datos;

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


           $query =     " SELECT Dmoobs "
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
			global $sololectura;

			$wir_a_ordenes = ir_a_ordenes($wemp_pmla, $wcco, 'ir_a_ordenes');
            $wcco1 = explode("-", $wcco);

            //Seleccionar CENTRO DE COSTOS
            echo "<center><table>";
            $query =  " SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom "
                   ."   FROM ".$wtabcco.", ".$wmovhos."_000011 "
                   ."  WHERE ".$wtabcco.".ccocod = ".$wmovhos."_000011.ccocod "
                   ."    AND (ccohos = 'on' or ccourg = 'on')"
                   ."    AND Ccoemp = '".$wemp_pmla."' ORDER BY Ccocod";
            $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
            $num = mysql_num_rows($res);


            echo "<tr class=fila1>

                  <td style=display:none><b><b></td>
                  <td ><b>Hora:<b></td>
                  <td align=center colspan='1' rowspan='2'><font size=4>Seleccione la Unidad : </font></td>";
            echo "<td align=center colspan='1' rowspan='2'><select name='wcco' onchange='enter1()'>";
            echo "<option > </option>";

             for($i = 1; $i <= $num; $i++)
               {
                $row = mysql_fetch_array($res);
                if(isset($wcco) && $row[0]==$wcco)
                        echo "<option selected value='".$row[0]."'>".$row[0]."-".$row[1]."</option>";
                         else
                                 echo "<option value='".$row[0]."'>".$row[0]."-".$row[1]."</option>";
               }
            echo "</select>";
            echo "</td></tr>";
            echo "<tr class=fila1>
                      <td id='wfechapantalla' style=display:none><b class='blink'><div id='wfecha' style = 'display:none'>".$wfecha1."</div></b></td>
                      <td id='whorapantalla'><b><div id='whora'>".$whora1."</div></b></td>
                  </tr>";
            echo "</table>";
			
			$wurgencias = ccoUrgencias($conex,$wmovhos, $wcco);
			
			switch(1){
				
				case ($wurgencias):
	
					$query = "SELECT 'Urg' as habcod, ubihis as 'habhis', ubiing as 'habing', pacno1, pacno2, pacap1, pacap2, pacnac, pactid, pacced, Ingnre AS ent_responsable, ubiptr, ubialp, ingres
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
								AND mtring = ubiing";	
					
					break;	
					
					default:
					
					//Selecciono todos los pacientes del servicio seleccionado
					$query= " SELECT habcod, habhis, habing, pacno1, pacno2, pacap1, pacap2, pacnac, pactid, pacced, t16.Ingnre AS ent_responsable, t17.Eyrsor AS cco_origen, ubiptr, ubialp, habord, 'off' as muerte, ingres"
						   ."   FROM ".$wmovhos."_000020,".$wmovhos."_000018
						   LEFT JOIN ".$wmovhos."_000016 AS t16 ON ( ubihis = t16.Inghis AND  ubiing = t16.Inging)
						   LEFT JOIN ".$wmovhos."_000017 AS t17 ON ( ubihis = t17.Eyrhis AND  ubiing = t17.Eyring), root_000036, root_000037 "
						   ."  WHERE habcco  = '".$wcco1[0]."'"
						   ."    AND habali != 'on' "            //Que no este para alistar
						   ."    AND habdis != 'on' "            //Que no este disponible, osea que este ocupada
						   ."    AND habcod  = ubihac "
						   ."    AND ubihis  = orihis "
						   ."    AND ubiing  = oriing "
						   ."    AND ubiald != 'on' "
						   ."    AND ubisac  = '".$wcco1[0]."'"
						   ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
						   ."    AND oriced  = pacced "
						   ."    AND oritid  = pactid "
						   ."    AND habhis  = ubihis "
						   ."    AND habing  = ubiing "
						   ."  GROUP BY 1,2,3,4,5,6,7 "
						   ." UNION"
							//Este union agrega los pacientes que tienen muerte en on.
						   ." SELECT ubihan, ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, pacnac, pactid, pacced, t16.Ingnre AS ent_responsable, t17.Eyrsor AS cco_origen, ubiptr, ubialp, 0 habord, 'on' as muerte, ingres"
						   ."   FROM ".$wmovhos."_000018
						   LEFT JOIN ".$wmovhos."_000016 AS t16 ON ( ubihis = t16.Inghis AND  ubiing = t16.Inging)
						   LEFT JOIN ".$wmovhos."_000017 AS t17 ON ( ubihis = t17.Eyrhis AND  ubiing = t17.Eyring), root_000036, root_000037 "
						   ."  WHERE ubihis  = orihis "
						   ."    AND ubiing  = oriing "
						   ."    AND ubimue  = 'on' "
						   ."    AND ubisac  = '".$wcco1[0]."'"
						   ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
						   ."    AND oriced  = pacced "
						   ."    AND oritid  = pactid "
						   ."    AND Ubiptr != 'on' "             //Solo los pacientes que no esten siendo trasladados
						   ."    AND Ubiald != 'on' "             //Que no este en Alta Definitiva
						   ."  GROUP BY 1,2,3,4,5,6,7 "
						   ."  ORDER BY 15, 1";
					
					break;
				
				
			}
			
			
			$res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
            $num = mysql_num_rows($res);

            if ($num > 0)
            {
			    echo "";
                echo "<div id='msjEspere' style='display:none;'><img src='../../images/medical/ajax-loader5.gif'/>Por favor espere un momento...</div><center><table>";

				$path_disp_espec = "/matrix/movhos/procesos/Consul_disponibilidad_especialidad.php?wemp_pmla=01";
				$path_cirugia = "/matrix/tcx/reportes/ListaG.php?wemp_pmla=".$wemp_pmla."&empresa=tcx&TIP=0";

			    echo "<tr class='fila1'>";
				echo "<td colspan=3 style='cursor: pointer' onclick='ejecutar(".chr(34).$path_cirugia.chr(34).")'><font size=2>Consultar Turnos de Cirugia</td>";
				echo "<td colspan=2 style='cursor: pointer' onclick='ejecutar(".chr(34).$path_disp_espec.chr(34).")'><font size=2>Consultar Disponibilidad de Especialidades</td>";
				echo "</tr>";

                echo "<tr class=encabezadoTabla>";
                echo "<th colspan=1 rowspan=2 style='empty-cells: hide;'></th>";              
                echo "<th colspan=1 rowspan=2><font size=4>Hab.</font></th>";
                echo "<th colspan=1 rowspan=2><font size=4>Historia</font></th>";
                echo "<th colspan=1 rowspan=2><font size=4>Paciente</font></th>";
                echo "<th colspan=1 rowspan=2><font size=4>Médico(s) Tratante(s)</font></th>";
                echo "<th colspan=17 rowspan=1><font size=4>Pendientes</font></th>";
				$wccouciuce = consultar_cco_uciuce($wcco);
				$uciuce = 5;
				if($wccouciuce)
				{
					echo "<th colspan=2 rowspan=1><font size=4>UCI / UCE</font></th>";
				}
                echo "</tr>";
                echo "<tr class=encabezadoTabla>";

                echo "<td colspan='3' rowspan='1'><font size=3>Glucometrías</font></td>";
                echo "<td colspan='3' rowspan='1'><font size=3>Nebulizaciones</font></td>";
                echo "<td colspan='3' rowspan='1' align=center><font size=3>Oxigeno</font></td>";
                echo "<td colspan='3' rowspan='1'><font size=3>Transfusiones</font></td>";
                echo "<td colspan='1' rowspan='1'><font size=3>Insumos</font></td>";
                echo "<td colspan='1' rowspan='1'><font size=3>Evoluciones</font></td>";
                echo "<td colspan='1' rowspan='1'><font size=3>Interconsultas</font></td>";
                echo "<td colspan='2' rowspan='1'><font size=3>Procedimientos</font></td>";
				if($wccouciuce)
				{
					echo "<td colspan='1' rowspan='1'><font size=3>Insumos</font></td>";
					echo "<td colspan='1' rowspan='1'><font size=3>Procedimientos</font></td>";
					$uciuce = 7;
				}
                echo "</tr>";
				echo "<tr align=center>";
				echo "<td colspan=5 class='encabezadoTabla'>&nbsp;</td>";
				echo "<td class='encabezadoTabla msg' title='Grabadas'>Gra</td>";
				echo "<td class='encabezadoTabla msg' title='Pendientes'>Pen</td>";
				echo "<td class='encabezadoTabla'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
				echo "<td class='encabezadoTabla msg' title='Grabadas'>Gra</td>";
				echo "<td class='encabezadoTabla msg' title='Pendientes'>Pen</td>";
				echo "<td class='encabezadoTabla'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
				echo "<td class='encabezadoTabla msg' title='Grabadas'>Gra</td>";
				echo "<td class='encabezadoTabla msg' title='Pendientes'>Pen</td>";
				echo "<td class='encabezadoTabla'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
				echo "<td class='encabezadoTabla msg' title='Grabadas'>Gra</td>";
				echo "<td class='encabezadoTabla msg' title='Pendientes'>Pen</td>";
				echo "<td class='encabezadoTabla'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
				echo "<td colspan=".$uciuce." class='encabezadoTabla'></td>";
				
				echo "</tr>";
				
				$array_datos = array();
				
				while($datos = mysql_fetch_assoc($res)){
					
					$array_datos[$datos['habhis']."-".$datos['habing']] = $datos;
					
				}							
				
                foreach($array_datos as $key => $row )
                {
                    
			
                    if (is_integer($i / 2))
                        $wclass = "fila1";
                    else
                        $wclass = "fila2";
					
					$mensaje_muerte = "";
                    $whab = $row['habcod'];
                    $whis = $row['habhis'];
                    $wing = $row['habing'];
                    $wpac = $row['pacno1']." ".$row['pacno2']." ".$row['pacap1']." ".$row['pacap2'];

                    $wnac = $row[7];
                    //Calculo la edad
                    $wfnac = (integer) substr($wnac, 0, 4) * 365 + (integer) substr($wnac, 5, 2) * 30 + (integer) substr($wnac, 8, 2);
                    $wfhoy = (integer) date("Y") * 365 + (integer) date("m") * 30 + (integer) date("d");
                    $weda = (($wfhoy - $wfnac) / 365);
                    if ($weda < 1)
                        $weda = number_format(($weda * 12), 0, '.', ',')."<b> Meses</b>";
                    else
                        $weda = number_format($weda, 0, '.', ',')." Años";

                    $wtid = $row['pactid'];                                      //Tipo documento paciente
                    $wdpa = $row['pacced'];                                      //Documento del paciente
                    $wptr = $row['ubiptr'];                                      //Proceso de traslado
                    $walp = $row['ubialp'];
					$wcedula = $row['pacced'];
					$wtip_doc = $row['pactid'];
					$ingres = $row['ingres'];

                    if ($wptr=="on")    //Si la historia esta en proceso de traslado
                        {
                        $wclass="colorAzul4";
                        }

                    if ($walp=="on")    //Si la historia esta en proceso de traslado
                        {
                        $wclass="fondoAmarillo";
                        }
                    //==========================================================================================================

					$path_hce = "../../hce/procesos/HCE_iFrames.php?empresa=".$whce."&origen=".$wemp_pmla."&wdbmhos=".$wmovhos."&whis=".$whis."&wing=".$wing."&accion=F&ok=0&wcedula=".$wdpa."&wtipodoc=".$wtid."";
                    $origen = "Kardex";
                    echo "<tr class=".$wclass.">";

					if($wir_a_ordenes != 'on'){

					$path_destino = "/matrix/movhos/procesos/generarKardex.php?wemp_pmla=".$wemp_pmla."&waccion=b&whistoria=".$whis."&wingreso=".$wing."&wfecha=".$wfecha1."&editable=off&et=on";

					}else{

					$origen = "Ordenes";
					$path_destino = "/matrix/hce/procesos/ordenes.php?wemp_pmla=".$wemp_pmla."&wcedula=".$wcedula."&wtipodoc=".$wtip_doc."&hce=on&programa=autorizacionesPendientes&editable=off";

					}

                    $path_ent_y_rec_pac = "/matrix/movhos/reportes/rep_ent_y_rec_pac.php?wemp_pmla=".$wemp_pmla."&whis=".$whis."&wing=".$wing."";

                    echo "<tr class=".$wclass.">";
                    echo "<td align=center nowrap><A HREF=# onclick='ejecutar(".chr(34).$path_destino.chr(34).")'>Ir a $origen</A></td>";
                    //echo "<td align=center><A HREF='generarKardex.php?wemp_pmla=".$wemp_pmla."&waccion=b&whistoria=".$whis."&wingreso=".$wing."&wfecha=".$wfecha1."&editable=off&et=on' target=_blank class=tipo3V>Kardex</A></td>";
                    
					if($row['muerte'] == 'on'){
						
							$mensaje_muerte = "<font color='red'>Muerte sin alta definitiva en el sistema.</font>";
					}
					
					if($wurgencias){
						
						$whab = consultarUbicacionUrg($conex, $wmovhos, $whis, $wing);
					}
					
					echo "<td align=center style='cursor: pointer;' onclick='ejecutar(".chr(34).$path_ent_y_rec_pac.chr(34).")'><b>".$whab."<br>".$mensaje_muerte."</b></td>";
                    echo "<td align=center style='cursor: pointer;' onclick='ejecutar(".chr(34).$path_hce.chr(34).")'>".$whis."-".$wing."</td>";
                    echo "<td align=justify><b>".$wpac."</b></td>";

                    $wmed = traer_medico_tte($whis, $wing, $wfecha1, $j);
                    if ($wmed == "Sin Médico")
                        {         //Si no lo encontro en el Kardex actual, lo busco en el Kardex de día anterior
                            $dia = time() - (1 * 24 * 60 * 60);   //Resta un dia (2*24*60*60) Resta dos y //asi...
                            $wayer = date('Y-m-d', $dia); //Formatea dia

                            $wmed = traer_medico_tte($whis, $wing, $wayer, $j);
                        }
                    echo "<td align=left  >".$wmed."</td>";

                    // TRAE LA CANTIDAD DE GLUCOMETRIAS PENDIENTES POR GRABAR
                    $glucometrias = traer_glucometer($wmovhos, $whis, $wing, $wemp_pmla);

                    if ($glucometrias['cantidadg'] > 0)
                        {
                            if ($wptr=="on")    //Si la historia esta en proceso de traslado
                            {
                               $wclass="colorAzul4";
                            }
                            else
                            {
                               $wclass="fondoGris";
                            }

                             if ($walp=="on")    //Si la historia esta en proceso de traslado
                                {
                                $wclass="fondoAmarillo";
                                }

                            echo "<td align=center class = ".$wclass."><input type='hidden' id='Ggrabadas_".$whis."_".$wing."' value='".$glucometrias['historialg']."'><b id='Ggrabadastext_".$whis."_".$wing."' class='msg' title='Glucometrías grabadas'>".$glucometrias['historialg']."</b>";
                            echo "<td align=center class = ".$wclass."><b class='blink G_".$whis."_".$wing." msg' title='Glucometrías pendientes por grabar'>".$glucometrias['cantidadg']."</b></td>";
                            echo "<td align=center class = ".$wclass.">";

                            // ASIGNA AL CHECKBOX EL VALOR DE LAS GLUCOMETRIAS PENDIENTES POR GRABAR
                            echo "<div id='bloque_".$i."' class = ".$wclass." style='display: block'><input id='gluco_$i' value='".$glucometrias['cantidadg']."' type='checkbox' onClick ='grabargluco(\"$i\",\"$wmovhos\",\"$wemp_pmla\",\"$whis\",\"$wing\",\"G\",this)'></div></td>";
                        }else
                            {

                             if ($wptr=="on")    //Si la historia esta en proceso de traslado
                                {
                                $wclass="colorAzul4";
                                }
                                else
                                {
                                    $wclass="fondoGris";
                                }

                           if ($walp=="on")    //Si la historia esta en proceso de traslado
                                {
                                $wclass="fondoAmarillo";
                                }
                             echo "<td align=center class = ".$wclass."><b><span><b class='msg' title='Glucometrías grabadas'>".$glucometrias['historialg']."</b></span></b></td>";
                             echo "<td align=center class = ".$wclass."></td>";
                             echo "<td align=center class = ".$wclass."></td>";
                            }
                     // TRAE LA CANTIDAD DE NEBULIZACIONES POR GRABAR
                    $nebulizaciones = traer_nebulizaciones($wmovhos, $whis, $wing, $wemp_pmla);

                    if ($nebulizaciones['cantidadn']>0)
                        {
                         if ($wptr=="on")    //Si la historia esta en proceso de traslado
                            {
                                $wclass="colorAzul4";
                            }
                            else
                            {
                                $wclass="fondoAmarillo";
                            }

                             if ($walp=="on")    //Si la historia esta en proceso de traslado
                                {
                                $wclass="fondoAmarillo";
                                }
                            echo "<td align=center class = ".$wclass."><input type='hidden' id='Ngrabadas_".$whis."_".$wing."' value='".$nebulizaciones['historialn']."'><b id='Ngrabadastext_".$whis."_".$wing."' class='msg' title='Nebulizaciones grabadas'>".$nebulizaciones['historialn']."</b></td>";
                            echo "<td align=center class = ".$wclass."><input type='hidden' id='Ngrabadas_".$whis."_".$wing."' value='".$nebulizaciones['historialn']."'><b class='blink N_".$whis."_".$wing." msg' title='Nebulizaciones pendientes por grabar' >".$nebulizaciones['cantidadn']."</b></td>";
                            echo "<td align=center class = ".$wclass." >";

                            // ASGINA AL CHECKBOX EL VALOR DE LAS NEBULIZACIONES PENDIENTES POR GRABAR
                            echo "<div id='bloquen_".$i."' class = ".$wclass." style='display: block'><input id='nebus_$i' value=".$nebulizaciones['cantidadn']." type='checkbox' onClick ='grabarnebus(\"$i\",\"$wmovhos\",\"$wemp_pmla\",\"$whis\",\"$wing\",\"N\",this)'></div></td>";
                        }
                        else
                        {
                             if ($wptr=="on")    //Si la historia esta en proceso de traslado
                                {
                                $wclass="colorAzul4";
                                }
                                else
                                {
                                    $wclass="fondoAmarillo";
                                }

                              if ($walp=="on")    //Si la historia esta en proceso de traslado
                                {
                                $wclass="fondoAmarillo";
                                }
                             echo "<td align=center class = ".$wclass."><b class='msg' title='Nebulizaciones grabadas'>".$nebulizaciones['historialn']."</b></td>";
                             echo "<td align=center class = ".$wclass."></td>";
                             echo "<td align=center class = ".$wclass."></td>";
                        }

                    // IMPRIME LA CANTIDAD DE OXIMETRIAS PENDIENTES POR GRABAR
                    $oximetrias = traer_oximetrias($wmovhos, $whis, $wing, $wemp_pmla);

                    if ($oximetrias['cantidado']>0)
                        {
                         if ($wptr=="on")    //Si la historia esta en proceso de traslado
                            {
                            $wclass="colorAzul4";
                            }
                            else
                            {
                                $wclass="fondoAzul";
                            }

                             if ($walp=="on")    //Si la historia esta en proceso de traslado
                                {
                                $wclass="fondoAmarillo";
                                }
                            echo "<td align=center class = ".$wclass."><input type='hidden' id='Ograbadas_".$whis."_".$wing."' value='".$oximetrias['historialo']."'><b id='Ograbadastext_".$whis."_".$wing."' class='msg' title='Oxigenos grabados'>".$oximetrias['historialo']."</b></td>";
                            echo "<td align=center class = ".$wclass."><b class='blink O_".$whis."_".$wing." msg' title='Oxigenos pendientes por grabar'>".$oximetrias['cantidado']."</b></td>";
                            echo "<td align=center class = ".$wclass.">";

                            // ASIGNA AL CHECKBOX EL VALOR DE LAS OXIMETRIAS PENDIENTES POR GRABAR
                            echo "<div id='bloqueo_".$i."' class = ".$wclass." style='display: block'><input id='oxi_$i' value=".$oximetrias['cantidado']." type='checkbox' onClick ='grabaroxi(\"$i\",\"$wmovhos\",\"$wemp_pmla\",\"$whis\",\"$wing\",\"O\",this)'></div></td>";
                        }
                        else
                        {
                            if ($wptr=="on")    //Si la historia esta en proceso de traslado
                                {
                                $wclass="colorAzul4";
                                }
                                else
                                {
                                    $wclass="fondoAzul";
                                }

                              if ($walp=="on")    //Si la historia esta en proceso de traslado
                                {
                                $wclass="fondoAmarillo";
                                }
                             echo "<td align=center class = ".$wclass."><b class='msg' title='Oxigenos grabados'>".$oximetrias['historialo']."</b></td>";
                             echo "<td align=center class = ".$wclass."></td>";
                             echo "<td align=center class = ".$wclass."><b></b></td>";
                        }

                    // IMPRIME LA CANTIDAD DE TRANSFUSIONES PENDIENTES POR GRABAR
                    $transfusiones = traer_transfusiones($wmovhos, $whis, $wing, $wemp_pmla);

                    if ((int)$transfusiones['cantidadt']>0)
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
                            echo "<td align=center class = ".$wclass."><input type='hidden' id='Tgrabadas_".$whis."_".$wing."' value='".$transfusiones['historialt']."'><b id='Tgrabadastext_".$whis."_".$wing."' class='msg' title='Transfusiones grabadas'>".$transfusiones['historialt']."</b></td>";
                            echo "<td align=center class = ".$wclass."><b class='blink T_".$whis."_".$wing." msg' title='Transfusiones pendientes por grabar'>".$transfusiones['cantidadt']."</b></td>";
                            echo "<td align=center class = ".$wclass.">";

                            // ASIGNA AL CHECKBOX EL VALOR DE LAS GLUCOMETRIAS PENDIENTES POR GRABAR
                            echo "<div id='bloquet_".$i."' class = ".$wclass." style='display: block'><input id='trans_$i' value=".$transfusiones['cantidadt']." type='checkbox' onClick ='grabartransf(\"$i\",\"$wmovhos\",\"$wemp_pmla\",\"$whis\",\"$wing\",\"T\",this)'></div></td>";
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
                             echo "<td align=center class = ".$wclass."><b class='msg' title='Transfusiones grabadas'>".$transfusiones['historialt']."</b></td>";
                             echo "<td align=center class = ".$wclass."></td>";
                             echo "<td align=center class = ".$wclass."></td>";
                        }

                    //TRAE LA CANTIDAD DE INSUMOS PENDIENTES POR FACTURAR
                    $winsumos = traer_insumos($wmovhos, $whis, $wing, $wemp_pmla);
					
                    if ((int)$winsumos['sin_grabar'] == 0 and (int)$winsumos['grabados'] == 0)
                        {
                         echo "<td></td>";
                        }
                    else
                        {
                         echo "<td align='center' id='I_".$whis."-".$wing."' style='cursor:pointer;' onClick='fnMostrar(\"".$wemp_pmla."\", \"".$whis."\", \"".$wing."\")'><b class='blink'>".$winsumos['sin_grabar']."</b>";
                        }

                    //TRAE LAS EVOLUCIONES HECHAS A LOS PACIENTES
                    $wevoluciones_pendientes = 0;
                    $wevoluciones_grabadas   = 0;
                    $array_evoluciones = traer_evoluciones($wmovhos, $whis, $wing, $wemp_pmla, $wevoluciones_pendientes, $wevoluciones_grabadas);
                    $warreglo_evoluciones = base64_encode(serialize($array_evoluciones));
                    $wid = $whis."-".$wing; //Identificador del td hidden
					
		                if ((int)$wevoluciones_pendientes == 0 and (int)$wevoluciones_grabadas == 0)
                        {
                          echo "<td></td>";
                        }
                    else
                        {
                          if ($wevoluciones_pendientes==0)
                             echo "<td align='center' id='E_".$whis."-".$wing."' style='cursor:pointer;' onClick='fnMostrar_especialista(\"".$wemp_pmla."\", \"".$whis."\", \"".$wing."\", \"".$wid."\", \"".$wevoluciones_pendientes."\", \"E\");'><input type='HIDDEN' id='arreglo_".$wid."' VALUE='".$warreglo_evoluciones."'>";
                          else
                              echo "<td align='center' id='E_".$whis."-".$wing."' style='cursor:pointer;' onClick='fnMostrar_especialista(\"".$wemp_pmla."\", \"".$whis."\", \"".$wing."\", \"".$wid."\", \"".$wevoluciones_pendientes."\", \"E\");'><input type='HIDDEN' id='arreglo_".$wid."' VALUE='".$warreglo_evoluciones."'><b class='blink'>".$wevoluciones_pendientes."</b>";
                        }
						
					          //TRAE LAS INTERCONSULTAS HECHAS A LOS PACIENTES
                    $winterconsultas_grabadas   = 0;
                    $winterconsultas_pendientes = 0;
                    $array_interconsultas = traer_interconsultas($wmovhos, $whis, $wing, $wemp_pmla, $winterconsultas_pendientes, $winterconsultas_grabadas);
                    $warreglo_interconsultas = base64_encode(serialize($array_interconsultas));
					
                    $wid = $whis."-".$wing; //Identificador del td hidden
                    if ((int)$winterconsultas_grabadas == 0 and (int)$winterconsultas_pendientes == 0)
                        {
                          echo "<td></td>";
                        }
                    else
                        {
                        if ($winterconsultas_pendientes==0)
                            echo "<td align='center' id='INT_".$whis."-".$wing."' style='cursor:pointer;' onClick='fnMostrar_especialista(\"".$wemp_pmla."\", \"".$whis."\", \"".$wing."\", \"".$wid."\", \"".$winterconsultas_pendientes."\", \"INT\");'><input type='HIDDEN' id='arreglo_int_".$wid."' VALUE='".$warreglo_interconsultas."'>";
                        else
                            echo "<td align='center' id='INT_".$whis."-".$wing."' style='cursor:pointer;' onClick='fnMostrar_especialista(\"".$wemp_pmla."\", \"".$whis."\", \"".$wing."\", \"".$wid."\", \"".$winterconsultas_pendientes."\", \"INT\");'><input type='HIDDEN' id='arreglo_int_".$wid."' VALUE='".$warreglo_interconsultas."'><b class='blink'>".$winterconsultas_pendientes."</b>";
                        }

                    // IMPRIME LA CANTIDAD DE PROCEDIMIENTOS PENDIENTES POR PACIENTE
                    list($totalpendientes, $grabadosnumber) = consultarPendientesPaciente($conex, $wmovhos, $whis, $wing, $ingres);

                    //Verifica si la secretaria leyó una observacion, en caso de haber leido almenos uno el numero dejara de titilar
                    if ((int)$totalpendientes > 0 and (int)$grabadosnumber == '')
                        {
                        echo "<td align=center width=80px class=blink><b>".$totalpendientes."</b></td>";
                        }
                       elseif((int)$totalpendientes > 0 and (int)$grabadosnumber > 0)
                       {
                        echo "<td align=center width=80px><b>".$totalpendientes."</b></td>";
                       }
                    else
                    {
                        echo "<td align=left><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td>";
                    }

                    // ENLACE VER PARA ACCEDER A LA INFORMACION DE LOS PENDIENTES POR PACIENTE Y SUS EXAMENES
					$path_procedimientos = "/matrix/movhos/procesos/entrega_turnos_secretaria.php?wemp_pmla=".$wemp_pmla."&key=".$key."&whis=".$whis."&wing=".$wing."&wcco=".$wcco."&wfec=".$wfecha1."&sololectura=".$sololectura."";
                    echo "<td align=center onclick='ejecutar(".chr(34).$path_procedimientos.chr(34).")' style='cursor:pointer;'><b><u>Ver</u></b></td>";


                    //TRAE LA CANTIDAD DE INSUMOS PENDIENTES POR FACTURAR PARA UCI / UCE
					
					$wforminsumosuciuce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'FormulariosInsumosUCIUCE'); //Extrae el nombre del formulario para extraer los valores a cobrar.
					$datos_formularios = explode(",",$wforminsumosuciuce);
					
					
					foreach($datos_formularios as $key =>$value){
						
						$form_campo = explode("-",$value);
						$wforminsumosuciuce = $form_campo[0];
						$wcampo_form_insuUCIUCE = $form_campo[1];
						$wcampo_form_procUCIUCE = $form_campo[2];
						
					}
										
                    $winsumosuciuce = traer_insumos_uci_uce($wmovhos, $whis, $wing, $wemp_pmla);
					$wproc_uciuce = traer_proc_uci_uce($wmovhos, $whis, $wing, $wemp_pmla);
					
					
                    //if ($winsumosuciuce == 0)
                    $wccouciuce = consultar_cco_uciuce($wcco);
					// echo "<br>".$wcco . "<=>" . $wccouciuce;
					if(!$wccouciuce)
					{
						//echo "<td></td>";
						echo "";
                    }
                    else
                    {
						
						if ($winsumosuciuce[$wcampo_form_insuUCIUCE]['total_insumos'] <= 0 )
						{
							echo "<td></td>";
						}
						else
						{
							if($winsumosuciuce[$wcampo_form_insuUCIUCE]['total_insumos'] == 0){
								
								$winsumosuciuce[$wcampo_form_insuUCIUCE]['total_insumos'] = "";
							}
							
							echo "<td align='center' id='IUCIUCE_".$whis."-".$wing."' style='cursor:pointer;' onClick='fnMostrarUCIUCE(\"".$wemp_pmla."\", \"".$whis."\", \"".$wing."\")'><b class='blink'>".$winsumosuciuce[$wcampo_form_insuUCIUCE]['total_insumos']."</b>";

						}

						if ($wproc_uciuce[$wcampo_form_procUCIUCE]['total_procedimientos'] <= 0 )
						{
							echo "<td></td>";
						}
						else
						{
							echo "<td align='center' id='PUCIUCE_".$whis."-".$wing."' style='cursor:pointer;' onClick='fnMostrarPROCUCIUCE(\"".$wemp_pmla."\", \"".$whis."\", \"".$wing."\")'><b class='blink'><b>".$wproc_uciuce[$wcampo_form_procUCIUCE]['total_procedimientos']."</b></td>";

						}
					}

                        // IMPRIME LA CANTIDAD DE PROCEDIMIENTOS PENDIENTES POR PACIENTE
                    /*list($totalpendientes, $grabadosnumber) = consultarPendientesPaciente($conex, $wmovhos, $whis, $wing);

                    //Verifica si la secretaria leyó una observacion, en caso de haber leido almenos uno el numero dejara de titilar
                    if ($totalpendientes > 0 and $grabadosnumber == '')
                        {
                        echo "<td align=center width=80px class=blink><b>".$totalpendientes."</b></td>";
                        }
                       elseif($totalpendientes > 0 and $grabadosnumber > 0)
                       {
                        echo "<td align=center width=80px><b>".$totalpendientes."</b></td>";
                       }
                    else
                    {
                        echo "<td align=left><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td>";
                    }*/

                    // ENLACE VER PARA ACCEDER A LA INFORMACION DE LOS PENDIENTES POR PACIENTE Y SUS EXAMENES
                    // $path_procedimientos = "/matrix/movhos/procesos/entrega_turnos_secretaria.php?wemp_pmla=".$wemp_pmla."&key=".$key."&whis=".$whis."&wing=".$wing."&wcco=".$wcco."&wfec=".$wfecha1."";
                    // echo "<td align=center onclick='ejecutar(".chr(34).$path_procedimientos.chr(34).")' style='cursor:pointer;'><b><u>Ver</u></b></td>";

                    echo "</tr>";

                }
            }
            else
            {

                echo "<br><b>NO HAY HABITACIONES OCUPADAS</b><br>";

            }
            echo "</table>";


        }


        // FUNCION QUE PERMITE SABER CUANTOS PROCEDIMIENTOS SE ENCUENTRAN PENDIENTES
        function consultarPendientesPaciente($conex, $wmovhos, $whis, $wing, $ingres )
            {

                global $whce;
                global $wfecha1;
				global $wcco;
				global $wemp_pmla;
				
				$wbasedatoCliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
				
				$wir_a_ordenes = ir_a_ordenes($wemp_pmla, $wcco, 'ir_a_ordenes');
				
				$sqlInfoSegRes = "SELECT Empcod, Empnit, Emptem, Empnom, Tardes, Placod, Plades
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
						

               $query1="SELECT A.Medico, A.Fecha_data, A.Hora_data, A.Ordfec, A.Ordhor, A.Ordhis, A.Ording, A.Ordtor, A.Ordnro, A.Ordobs, A.Ordesp, A.Ordest, A.Ordusu, A.Ordfir, A.Seguridad, A.id, B.Medico, B.Fecha_data, B.Hora_data, B.Dettor, B.Detnro, B.Detcod, B.Detesi, B.Detrdo, B.Detfec, B.Detjus, B.Detest, B.Detite, B.Detusu, B.Detfir, B.Deture, B.Seguridad, B.id
                          FROM ".$whce."_000027 A, ".$whce."_000028 B, ".$wmovhos."_000045 C
                         WHERE Ordtor = Dettor
                           AND Ordnro = Detnro
                           AND A.Ordhis = '".$whis."'
                           AND A.Ording = '".$wing."'
                           AND B.Detesi = C.Eexcod
                           AND C.Eexaut = 'on'
						   AND A.Ordest = 'on'
                           AND B.Detest = 'on'";
               $res1 = mysql_query($query1, $conex) or die(mysql_errno()." - Error en el query $sql - ".mysql_error());
               $roword = mysql_num_rows($res1);
			   
			   $sinaut = 0;
			  
			  while($row_proc = mysql_fetch_array($res1)){
					
					$sqlCups = "  SELECT * FROM 
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
					
					// $pideAutorizacion 	= procedimientoSeDebeAutorizar($codEnt, $nitEnt, $tipEnt, $planEmp, $clasifiPro, $row_proc['Detcod']);
					
					// if(!$pideAutorizacion){
						
						// $sinaut++;
					// }
				  
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

                return array ($totalpendientes, $grabadosnumber);
            }
		
		//---------------------------------------------------------------------------------------------------------------------
		//	--> Funcion que valida si un procedimiento requiere autorizacion
		//		Jerson trujillo, 2016-03-30
		//---------------------------------------------------------------------------------------------------------------------
		// function procedimientoSeDebeAutorizar($codEnt, $nitEnt, $tipEnt, $planEmp, $clasifProc, $procedimiento)
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

           $wcolor = "CCCCFF";

           $wmed = traer_medico_tte($whis, $wing, $wfecha1, $j);

           // Traer datos del paciente
           $q = "SELECT pacno1, pacno2, pacap1, pacap2, pactid, pacced, Ubisac, Ubihac, Ubisan, Ubihan
                   FROM root_000036, root_000037, movhos_000018
                  WHERE oriced = pacced
                    AND Ubihis = Orihis
                    AND Ubiing = Oriing
                    AND orihis = '".$whis."'
                    AND oriing = '".$wing."'
                    AND oriori = '".$wemp_pmla."'";
           $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
           $winfo = mysql_fetch_array($res);

           $wpac = $winfo[0]." ".$winfo[1]." ".$winfo[2]." ".$winfo[3];
           $whab = $winfo[7];

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

            $query1 ="  SELECT A.Medico, A.Fecha_data, A.Hora_data, A.Ordfec, A.Ordhor, A.Ordhis, A.Ording, A.Ordtor, A.Ordnro, A.Ordobs, A.Ordesp, A.Ordest, A.Ordusu, A.Ordfir, A.Seguridad, A.id as id_encabezado, B.Medico, B.Fecha_data, B.Hora_data, B.Dettor,
									     B.Detnro, B.Detcod, B.Detesi, B.Detrdo, B.Detfec, B.Detjus, B.Detest, B.Detite, B.Detusu, B.Detfir, B.Deture, B.Seguridad, B.id as id_detalle, Detpri
                          FROM ".$whce."_000027 A, ".$whce."_000028 B, ".$wmovhos."_000045 C
                         WHERE Ordtor = Dettor
                           AND Ordnro = Detnro
                           AND A.Ordhis = '".$whis."'
                           AND A.Ording = '".$wing."'
                           AND B.Detesi = C.Eexcod
						   AND A.Ordest = 'on'
                           AND C.Eexaut = 'on'
                           AND B.Detest = 'on'
                      ORDER BY Detpri DESC, A.Fecha_data DESC, A.Hora_data DESC ";
             $res1 = mysql_query($query1, $conex) or die(mysql_errno()." - Error en el query $sql - ".mysql_error());
             $numord=mysql_num_rows($res1);
		
             //datos del paciente
             $query_p =  "    SELECT   m16.Ingres AS cod_responsable, m16.Ingnre AS ent_responsable, m17.Eyrsor AS cco_origen, r37.Oriced AS doc
                                FROM  ".$wmovhos."_000017 AS m17
                           LEFT JOIN  root_000037 AS r37 ON (r37.Orihis = m17.Eyrhis AND r37.Oriing = m17.Eyring)
                           LEFT JOIN  ".$wmovhos."_000016 AS m16 ON (m16.Inghis = m17.Eyrhis AND m16.Inging = m17.Eyring)
                               WHERE  Orihis = '".$whis."'
                                 AND  Oriing = '".$wing."'";
            $res_p = mysql_query($query_p, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query_p." - ".mysql_error());
            $num_p = mysql_num_rows($res_p);

            $doc = '';
            $cco_origen = '';
            $ent_responsable = '';

            if ($num_p > 0)
            {
                $row = mysql_fetch_array($res_p);
                $doc = $row['doc'];
                $cco_origen = $row['cco_origen'];
                $ent_responsable = $row['ent_responsable'];
				$codEntRes = $row['cod_responsable'];
            }
			
				 // --> 	Obtener el plan actual del paciente
			// 		Jerson trujillo, 2015-04-16.
			
			$sqlInfoSegRes = "SELECT Empcod, Empnit, Emptem, Empnom, Tardes, Placod, Plades
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
                            <th><font size='3'>Entidad responsable</font></th>
                        </tr>
                        <tr class='fila2'>
                            <td align='center'><font size='3'><b>".$whab."</b></font></td>
                            <td align='center'><font size='3'>".$doc."</font></td>
                            <td align='center'><font size='3'>".$whis."-".$wing."</font></td>
                            <td align='center'><font size='3'><b>".$wpac."&nbsp&nbsp</b></font></td>
                            <td align='center'><font size='3'>".buscar_centro_costo($wemp_pmla,$cco_origen)."</font></td>
                            <td align='center'><font size='3'><b>".$wmed."&nbsp&nbsp</b></font></td>
                            <td align='center'><font size='3'>".$ent_responsable."</font></td>
                        </tr>
                    </table>
                    <br/>
                    </div>";

          if ($num > 0 or $numord > 0)
             {
                $i=1;
                echo "<table border=0 ALIGN=CENTER>";
                echo "<tr class='encabezadoTabla'>";
				echo "<td align='center' width = 150><b>UNIDAD QUE REALIZA</b></td>";
                echo "<td align='center' width = 300><b>EXAMENES</b></td>";
                echo "<td align='center' width = 100><b>REALIZAR EN LA FECHA</b></td>";
                echo "<td align='center' ><b>ESTADO</b></td>";
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
						
						$observa_procedi = ""; 
						
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
						$wobservac = $row['Ekaobs'];
						echo "<td class =".$class." align=center>".$wser_relacionado."</td>";
						echo "<td class =".$class.">".$wobservac."<br>".$mensaje."</td>";
						echo "<td class =".$class." align='center'>".$row['Ekafes']."</td>";

						$westado_registro = $row['Ekaest'];

						$sql_est = "SELECT Eexcod, Eexdes
							          FROM ".$wmovhos."_000045
							         WHERE Eexaut = 'on'";
						$res_est = mysql_query( $sql_est, $conex ) or die( mysql_errno()." - Error en el query $sql_est - ".mysql_error() );
						$estado = "";

						$wexam = $row['Ekacod'];
						$wfechadataexamen = $row['Fecha_data'];
						$whoradataexamen = $row['Hora_data'];
						$wfechagk = $row['Ekafec'];
						$wid_a = $row['id'];
						$wcontrol_ordenes = "";
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

						$observa_procedi = traer_observaciones_anteriores_exam($whis, $wing, $wexam, $wfechadataexamen, $whoradataexamen, $wfechagk, $wordennro, $wordite, $wid_a);

						echo "<td align=left><textarea ID='wobscc[".$i."]' rows=3 cols=30 ".$edicion." class=fondoAmarillo onChange='grabarObservacion(\"$wemp_pmla\",\"$wfecha1\",\"$wexam\",\"$wing\",\"$whis\",\"$wfechadataexamen\",\"$whoradataexamen\", \"$wfechagk\", \" \", \" \",this, \"$wid_a\")'></textarea></td>";
						echo "<td align=left bgcolor=".$wcolor."><div style='overflow:auto; height:80px;'>".$observa_procedi."</div></td>";
						echo "</tr>";
						$i++;
					}

                //DATOS DE LA CONSULTA A LAS TABLAS HCE_000027 y HCE_000028 PARA EXTRAER LOS PENDIENTES DE LA HISTORIA E INGRESO EN ORDENES
                while ($roword = mysql_fetch_array($res1))
                {
					$texto_prioritario = "";
					$obser_ord = "";
					
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
					
					// $pideAutorizacion 	= procedimientoSeDebeAutorizar($codEnt, $nitEnt, $tipEnt, $planEmp, $clasifiPro, $roword['Detcod']);
					// $pideAutorizacion	= ($pideAutorizacion) ? '' : 'style="display:none;"';						
					
                    if (is_int($i / 2))
                    {
                     $class = 'fila1';
                    }
                    else
                    {
                     $class = 'fila2';
                    }
					
					if($roword['Detpri'] == 'on' ){
						
						$class = "articuloNuevoPerfil";
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
					$wjustificacion = $roword['Detjus'];
					$observacion = "";
					
					if(trim($wjustificacion) != ''){

						$observacion = "<br><hr><b>Justificaci&oacute;n: </b>".$wjustificacion;
					}

					echo "<td align='center'>".$row_tip_ord['Descripcion']."</td>";
                     echo "<td>".$wnombre_examen." (".$codCups.")".$observacion."<br>".$mensaje."</td>";
                    echo "<td align='center'>".$roword['Detfec']."</td>";

					$westado_registro = $roword['Detesi'];
					$wexam = $roword['Dettor'];
                    $wfechadataexamen = $roword['Fecha_data'];
                    $whoradataexamen = $roword['Hora_data'];
                    $wfechagk = $roword['Detfec'];
                    $wordennro = $roword['Detnro'];
                    $wordite = $roword['Detite'];
                    $wordid_detalle = $roword['id_encabezado'];
					$wcontrol_ordenes = 'on';

					$sql_est = "SELECT Eexcod, Eexdes
								  FROM ".$wmovhos."_000045
								 WHERE Eexaut = 'on'";
					$res_est = mysql_query( $sql_est, $conex ) or die( mysql_errno()." - Error en el query $sql_est - ".mysql_error() );

					$estado = "";

					$wid_encabezado = $roword['id_encabezado'];

					$estado1 = "<select id='estado_$wordid_detalle$wordite' class='select_listado' onchange='cambiar_estado_examen(\"$wemp_pmla\",\"$wfecha1\",\"$wexam\",\"$wing\",\"$whis\",\"$wfechadataexamen\",\"$whoradataexamen\", \"$wfechagk\", \"$wordennro\", \"$wordite\",this, \"$wordid_detalle\", \"$wcco\", \"$whce\", \"$wcontrol_ordenes\",\"$wnombre_examen\",\"$westado_registro\", \"$wordid_detalle$wordite\")'>";

					while($row_est = mysql_fetch_array( $res_est )){

						$seleccionar = "";

						if($row_est['Eexcod'] == $westado_registro){

						$seleccionar = "selected";

						}

						$estado.= "<option value='".$row_est['Eexcod']."' $seleccionar>".$row_est['Eexdes']."</option>";

					}
					$estado2 = "</select>";

                    echo "<td align='center'>".$estado1.$estado.$estado2."<br><b>".$texto_prioritario."</b></td>";

                    $wexam = $roword['Dettor'];
                    $wfechadataexamen = $roword['Fecha_data'];
                    $whoradataexamen = $roword['Hora_data'];
                    $wfechagk = $roword['Detfec'];
                    $wordennro = $roword['Detnro'];
                    $wordite = $roword['Detite'];
                    $wid_detalle = $roword['id_detalle'];
					
					$obser_ord = traer_observaciones_anteriores_exam($whis, $wing, $wexam, $wfechadataexamen, $whoradataexamen, $wfechagk, $wordennro, $wordite, $wid_encabezado);
					
                    echo "<td align=left><textarea ID='wobscc[".$i."]' rows=3 cols=30 class=fondoAmarillo onchange='grabarObservacion(\"$wemp_pmla\",\"$wfecha1\",\"$wexam\",\"$wing\",\"$whis\",\"$wfechadataexamen\",\"$whoradataexamen\", \"$wfechagk\", \"$wordennro\", \"$wordite\",this, \"$wid_encabezado\")'></textarea></td>";

                    echo "<td align=left bgcolor=".$wcolor."><div style='overflow:auto; height:80px;'>".$obser_ord."</div></td>";

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

                echo "<td align='center'>&nbsp;INTERCONSULTAS</b></td>";
                echo "<td align='center'>&nbsp;TERAPIAS</b></td>";
                echo "<td align='center'>&nbsp;CIRUGIAS</b></td>";
                echo "<td align='center'>&nbsp;OBSERVACIONES</b></td>";
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

                        echo "<td class =".$class." align='center'><textarea readonly='' rows=4 cols=30/'>".$row['karint']."</textarea></td>";
                        echo "<td class =".$class." align='center'><textarea readonly='' rows=4 cols=30/'>".$row['karter']."</textarea></td>";
                        echo "<td class =".$class." align='center'><textarea readonly='' rows=4 cols=30/'>".$row['karcip']."</textarea></td>";
                        echo "<td class =".$class." align='center'><textarea readonly='' rows=4 cols=40/'>".$row['karobs']."</textarea></td>";
                        echo "</tr>";
                        $i++;
                    }
                        echo "</table>";
              }

         }

		//Valida si el centro de costos seleccionado es de uci o uce
        function consultar_cco_uciuce($wcco)
        {
            global $conex;
            global $wemp_pmla;

            $queryccouciuce = "    SELECT Detval
                                     FROM root_000051
                                    WHERE Detemp='" . $wemp_pmla . "'
                                      AND Detapl='ccouciuce'";
            $res = mysql_query($queryccouciuce, $conex) or die("Error: ".mysql_error()." - en el query: ".$query." - ".mysql_error());
			$row = mysql_fetch_array($res);

			$ccouciuce = explode(",",$row['Detval']);

			$cco_ok = false;
            foreach($ccouciuce as $key => $value){

				if(trim($value) == trim($wcco) ){
					$cco_ok = true;
				}
			}

			return $cco_ok;

        }


 if (isset($consultaAjax))
            {
            switch($consultaAjax)
                {

            case 'observacion':
                {
                    echo grabarObservacion($wemp_pmla, $wmovhos, $wfec, $wexam, $wing, $wfechadataexamen, $whoradataexamen, $wfechagk, $whis, $wordennro, $wordite, $wtexto, $wid);
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
				mostrar_detalle($wemp_pmla, $whis, $wing, $wfecha, $whora, $sololectura);
				break;
			}
			case 'mostrar_detalleUCIUCE':
			{
				mostrar_detalleUCIUCE($wemp_pmla, $whis, $wing, $wfecha, $whora, $sololectura);
				break;
			}
			case 'mostrar_detalleProcUCIUCE':
			{
				mostrar_detalleProcUCIUCE($wemp_pmla, $whis, $wing, $wfecha, $whora, $sololectura);
				break;
			}

            case 'mostrar_detalle_especialista':

			{
				mostrar_detalle_especialista($wemp_pmla, $whis, $wing, $wfecha, $whora, $array_profesores, $evoluciones, $tipo, $sololectura);
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
    <title>Procedimientos e insumos por grabar</title>

    <style type="text/css">
        A   {text-decoration: none;color: #000066;}
        .tipo3V{color:#000066;background:#dddddd;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo3V:hover {color: #000066; background: #999999;}
    </style>

</head>
<script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/jqueryui_1_9_2/jquery-ui.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>

<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />

<script type="text/javascript">

	function cambiar_estado_examen(wemp_pmla, wfec, wexam, wing, whis, wfechadataexamen, whoradataexamen, wfechagk, wordennro, wordite, campo, wid, wcco, whce, wcontrol_ordenes, wtexto_examen, westado_registro, wid_select)
      {

        var wmovhos = document.getElementById( "wmovhos" ).value;
        var westado = $("#estado_"+wid_select).val();

		$.post("entrega_turnos_secretaria.php",
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

	function grabarinsumos(basedato, historia, ingreso, tipo, valor, td_id )
    {
    	$('#div_resultado').html('<div align="center"><img src="../../images/medical/ajax-loader5.gif"/></div>');
		
		$("#boton_insumos").attr("disabled", true);
		
        var wfechapantalla = document.getElementById('wfecha');
        var datofecha = wfechapantalla.innerHTML;

        var whorapantalla = document.getElementById('whora');
        var datohora = whorapantalla.innerHTML;


        $.post("entrega_turnos_secretaria.php",
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
            		//enter();
            	}
            	else
            	{
					//$("#insumos").attr("disabled", "disabled");
                    alert(data_json.mensaje);
					$("#"+tipo+"_"+td_id).html(" ");
					
					switch(tipo) {
						case 'E':							
						case 'I':							
						case 'INT':							
								$("#"+tipo+"_"+td_id).prop('onclick',null).off('click');
								$("#"+tipo+"_"+td_id).css('cursor','default');
						break;
						
					}
										
                    cerraremergente();
            	}

            },
            "json"
        );
    }


    //ventanana emergente
    function fnMostrar_especialista(wemp_pmla, historia, ingreso, id, evoluciones, tipo)
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
		
		if(tipo == 'E'){			
			var info_array_profesores =   $("#arreglo_"+id).val();			
		}
		
		if(tipo == 'INT'){			
			var info_array_profesores =  $("#arreglo_int_"+id).val();			
		}

		$.post("entrega_turnos_secretaria.php",
				{

                    consultaAjax:   	'mostrar_detalle_especialista',
					wemp_pmla:      	wemp_pmla,
					whis:           	historia,
					wing:           	ingreso,
					wfecha:             datofecha,
                    whora:              datohora,
                    array_profesores:   info_array_profesores,
					evoluciones:		evoluciones,
					tipo:				tipo,
					sololectura:        $("#sololectura").val()

				}
				,function(data) {
					$.blockUI({ message: data,
							css: {  left: 	'25%',
								    top: 	'10%',
								    width: 	'50%',
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

		$.post("entrega_turnos_secretaria.php",
				{

                    consultaAjax:   	'mostrar_detalle',
					wemp_pmla:      	wemp_pmla,
					whis:           	historia,
					wing:           	ingreso,
					wfecha:             datofecha,
                    whora:              datohora,
                    sololectura:        $("#sololectura").val()

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
  function fnMostrarUCIUCE(wemp_pmla, historia, ingreso)
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

		$.post("entrega_turnos_secretaria.php",
				{

                    consultaAjax:   	'mostrar_detalleUCIUCE',
					wemp_pmla:      	wemp_pmla,
					whis:           	historia,
					wing:           	ingreso,
					wfecha:             datofecha,
                    whora:              datohora,
					sololectura:        $("#sololectura").val()

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

	//Función para mostrar la ventana modal de los procedimientos de los centros de costo de UCI/UCE.
	function fnMostrarPROCUCIUCE(wemp_pmla, historia, ingreso)
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

		$.post("entrega_turnos_secretaria.php",
				{

                    consultaAjax:   	'mostrar_detalleProcUCIUCE',
					wemp_pmla:      	wemp_pmla,
					whis:           	historia,
					wing:           	ingreso,
					wfecha:             datofecha,
                    whora:              datohora,
					sololectura:        $("#sololectura").val()

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
      // $('#pendientes').submit();
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
            ajax.open("POST", "entrega_turnos_secretaria.php",false);
            ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            ajax.send(parametros);
            //alert(ajax.responseText);
            }catch(e){ alert(e) }
      }


     // FUNCION AJAX QUE PERMITE LA GRABACION IN SITU DE LAS OBSERVACIONES DE CADA EXAMEN POR PARTE DE LA SECRETARIA
     function grabarObservacion(wemp_pmla, wfec, wexam, wing, whis, wfechadataexamen, whoradataexamen, wfechagk, wordennro, wordite, campo, wid)
      {

        var wmovhos = document.getElementById( "wmovhos" ).value;
        var texto = campo.value;

		$.post("entrega_turnos_secretaria.php",
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
					wid : 				wid
				}
				,function(data) {}
			);
		}

       // FUNCION AJAX QUE PERMITE LA GRABACION DE LAS GLUCOMETRRIAS EN LA TABLA movhos_000119
     function grabargluco(id, wmovhos, wemp_pmla, whis, wing, wtipo, campo)
      {

            var fila = campo.parentNode.parentNode.parentNode;
            
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
						ajax.open("POST", "entrega_turnos_secretaria.php",false);
						ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
						ajax.send(parametros);						
						
						var grabadas = 0;
						if($("#Ggrabadas_"+whis+"_"+wing).val() != ''){
							
							var grabadas = parseInt($("#Ggrabadas_"+whis+"_"+wing).val());
							
						}	
						
						var Ggrabadas = grabadas + parseInt(valor);		
						$("#Ggrabadastext_"+whis+"_"+wing).html(Ggrabadas);
						$(".G_"+whis+"_"+wing).html("");
						$("#gluco_"+id).remove();
					
					}catch(e){ alert(e) }
	}


         // FUNCION AJAX QUE PERMITE LA GRABACION DE LAS NEBULIZACIONES EN LA TABLA movhos_000119
     function grabarnebus(id, wmovhos, wemp_pmla, whis, wing, wtipo, campo)
      {

           var fila = campo.parentNode.parentNode.parentNode;
           //alert( fila );
          
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
            ajax.open("POST", "entrega_turnos_secretaria.php",false);
            ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            ajax.send(parametros);
					
					var grabadas = 0;
					if($("#Ngrabadas_"+whis+"_"+wing).val() != ''){
						
						var grabadas = parseInt($("#Ngrabadas_"+whis+"_"+wing).val());
						
					}
					
					var Ngrabadas = grabadas + parseInt(valor);	
					
					$("#Ngrabadastext_"+whis+"_"+wing).html(Ngrabadas);
					$(".N_"+whis+"_"+wing).html("");
                   $("#nebus_"+id).remove();
           }catch(e){ alert(e) }
    }

        // FUNCION AJAX QUE PERMITE LA GRABACION DE LAS OXIMETRIAS EN LA TABLA movhos_000119
     function grabaroxi(id, wmovhos, wemp_pmla, whis, wing, wtipo, campo)
      {

           var fila = campo.parentNode.parentNode.parentNode;
          
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
            ajax.open("POST", "entrega_turnos_secretaria.php",false);
            ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            ajax.send(parametros);
					
					var grabadas = 0;
					if($("#Ograbadas_"+whis+"_"+wing).val() != ''){
						
						var grabadas = parseInt($("#Ograbadas_"+whis+"_"+wing).val());
						
					}
					
					var Ograbadas = grabadas + parseInt(valor);	
					
					$("#Ograbadastext_"+whis+"_"+wing).html(Ograbadas);
					
					$(".O_"+whis+"_"+wing).html("");
                    $("#oxi_"+id).remove();
            }catch(e){ alert(e) }
      }

    // FUNCION AJAX QUE PERMITE LA GRABACION DE LAS OXIMETRIAS EN LA TABLA movhos_000119
     function grabartransf(id, wmovhos, wemp_pmla, whis, wing, wtipo, campo)
      {

           var fila = campo.parentNode.parentNode.parentNode;
           //alert( fila );
        
           campo.disable=true;

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
            ajax.open("POST", "entrega_turnos_secretaria.php",false);
            ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            ajax.send(parametros);
					
					var grabadas = 0;
					if($("#Tgrabadas_"+whis+"_"+wing).val() != ''){
						
						var grabadas = parseInt($("#Tgrabadas_"+whis+"_"+wing).val());
						
					}
					
					var Tgrabadas = grabadas + parseInt(valor);	
					
					$("#Tgrabadastext_"+whis+"_"+wing).html(Tgrabadas);
					
					$(".T_"+whis+"_"+wing).html("");
                    $("#trans_"+id).remove();
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

        setTimeout( "recargar()", 1000*60*5 );

        //Vuelve a poner la pagina en el ultimo lugar antes de ser recargada
        window.onload=function(){
        var pos=window.name || 0;
        window.scrollTo(0,pos);

		//Se reemplaza la accion blink por esta jquery.
		setInterval(function() {
     
		$('.blink').effect("pulsate", {}, 5000);

		}, 1000);

        }
        window.onunload=function(){
        window.name=self.pageYOffset || (document.documentElement.scrollTop+document.body.scrollTop);
        }
	
	
	$(document).ready(function()
	{
		
		$(".msg").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });		
		
		if($("#sololectura").val() == 'on'){
			
			$("#pendientes :input[type='checkbox']").prop("disabled", true);
			$("#pendientes").find('textarea').prop("disabled", true);
			$(".select_listado").prop("disabled", true);
		
		}
		 
	 });



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
        echo "<form name='pendientes' id='pendientes' action='entrega_turnos_secretaria.php' method=post>";
		
		
        ?>
        <script>
            deshabilitar_teclas();
        </script>
        <?php
        if (!isset($wfecha))
        $wfecha = date("Y-m-d");
        $whora = (string) date("H:i:s");
        global $wemp_pmla;
        global $sololectura;

        echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
        echo "<input type='hidden' name='sololectura' id='sololectura' value=".$sololectura.">";
		
		$wactualiz = "Septiembre 26 de 2017";		 
        encabezado("PROCEDIMIENTOS E INSUMOS POR GRABAR", $wactualiz, "clinica");

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