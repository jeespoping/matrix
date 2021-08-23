<?php
include_once("conex.php");
if (!isset($consultaAjax))
	{

?>
<html>
<head>
  <title>PACIENTES EN PROCESO DE ALTA</title>
</head>
<body>
<script type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.core.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.tabs.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.draggable.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>

<script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.simple.tree.js"></script>
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
<script type="text/javascript">
	function enter()
	{	 
	  document.forms.hoteleria.submit();
	}

    function cambiarusuario(wemp_pmla,basedato,historia,ingreso,fecha,id, habitacion, centro_costos, usuario)
    {

	var nuevousuario = document.getElementById(id).value; //posicion

    var parametros = "consultaAjax=cambiarusuario&wemp_pmla="+wemp_pmla+"&wbasedato="+basedato+"&whis="+historia+"&wing="+ingreso+"&wfecha="+fecha+"&wnuevousuario="+nuevousuario+"&whab="+habitacion+"&wcco="+centro_costos+"&wuser="+usuario;

		  try
		  {
		    var ajax = nuevoAjax();
			ajax.open("POST", "Hoteleria.php",false);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);
			//alert(ajax.responseText);
			}catch(e){ alert(e) }
            enter();
    }

	function agregarJusti()
	{
		var obj = document.getElementById('mjust');
		a = obj.options[obj.selectedIndex].value;
		if(a=="--")
		{
			alert("Debe seleccionar una justificaci\xf3n");
		}else
			{
				$.unblockUI();
				b = document.createElement("input");
				b.type ="text";
				b.name = "wjust";
				b.id = "wjust";
				b.value = a;
				document.hoteleria.appendChild(b);
				var f = b.name;
				//document.forms.hoteleria.submit();

			}
			//document.forms.hoteleria.submit();
	}


	function fnMostrar(){

			$.blockUI({ message: $("#menuJusti"),
							css: { left: ( $(window).width() - 800 )/2 +'px',
								    top: ( $(window).height() - $("#menuJusti").height() )/2 +'px',
								  width: '800px'
								 }
					  });
	}

	function cerrarVentana()
	 {
      window.close();
     }

  $(document).ready( function ()
	{
		
		$(".msg").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });
		
		//Si se encuentra este valor en on entonces inactivara todos los campos tipo input menos el boton cerrar.
        var wconsulta = $('#wconsulta').val();
        if(wconsulta == 'on')
            {
                $("#hoteleria :input").each(function(){

                 if($(this).attr("id") != "cerrarventana")
                    {
                    $(this).attr("disabled",true);
                    }


                });
            }

	});

 /*    }
	 window.onload = function(){
		document.getElementById('thjus').click();
	 }*/
</script>
<?php

    }
  /***********************************************
   *                  HOTELERIA                  *
   *     		  CONEX, FREE => OK		         *
   ***********************************************/
@session_start();

if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");

if(!isset($_SESSION['user']))
	echo "error";
else
{
  include_once("root/magenta.php");
  include_once("root/comun.php");
  include_once("movhos/movhos.inc.php");

  $conex = obtenerConexionBD("matrix");

  $wactualiz="(Noviembre 7 de 2017)";                      // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                                   // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
													   //=========================================================================================================================================\\
//ACTUALIZACIONES
//=========================================================================================================================================
// - DESCRIPCION Julio 13 de 2021 Joel Payares Hernández
//	* Se comenta lineas de código que colocan en modo limpieza la habitación que estaba habitada por el paciente.
//	* Estos cambios están en las lineas 902 a 915
//=========================================================================================================================================
//DESCRIPCION Agosto 11 de 2020 Edwin MG
//Se cambia el calculo de dias de estancia desde la fecha y hora de ingreso al servicio hasta la fecha y hora actual, antes el calculo se
//se realizaba desde la fecha de ingreso al servicio a media noche hasta la fecha y hora de egreso del servicio
//=========================================================================================================================================
//DESCRIPCION Noviembre 7 de 2017 Jonatan Lopez
//Se agrega la funcion cancelarPedidoInsumos para que al momento de dar alta definitiva al paciente se cancele el pedido de Insumos.
//=========================================================================================================================================
//DESCRIPCION  Diciembre 29 de 2016 Jonatan Lopez
//Se agrega a la consulta principal la relacion con la tabla movhos_000020 para que solo muestre los pacientes que estan hospitalizados y
//que aun ocupan cama.
//=========================================================================================================================================\\
//DESCRIPCION  Junio 13 de 2013 Jonatan Lopez
//Se agrega la variable $wconsulta, si esta variable esta en on inactiva todos lo elementos tipo input del formulario hoteleria.
//=========================================================================================================================================\\
//DESCRIPCION  Marzo 01 de 2013
//=========================================================================================================================================\\
//Cuando se cancela una muerte o se cancela un alta definitiva, el programa restaura los valores en la tabla de indicadores.
////=========================================================================================================================================\\
//DESCRIPCION  Septiembre 28 de 2012                                                                                                           \\
//=========================================================================================================================================\\
//Se modifico para permitir que el hotelero pueda cambiar el caso para otro hotelero en caso de terminar su turno, ademas el usuario solo
//podra cambiar sus casos.
//=========================================================================================================================================\\                                                                                                                     \\
//=========================================================================================================================================\\
//DESCRIPCION  Mayo 17 de 2012                                                                                                           \\
//=========================================================================================================================================\\
//Se agrega la validacion de que a aquellos centros de costos que no requieran promediar las altas, no se les pida la justificacion cuando  \\
// superan el tiempo de alta meta. este cambio se hizo en la funcion, require_justificacion().
//=========================================================================================================================================\\
//DESCRIPCION  Mayo 02 de 2012                                                                                                           \\
//=========================================================================================================================================\\
//Se cambió la lógica de la validación de necesitar o no justificación, lo que se hizo fue que en el momento de listar los pacientes, el sistema \\
//verifica si este necesita justificación de retraso, en el caso de que se le quiera dar alta definitiva, cuando esto es verdadero el campo       \\
//se va a presentar con fondo rojo para que el encargado sepa que se le va a pedir justificación.                                                 \\
//Cuando el encargado seleccione la opcion alta definitiva estando esta en color rojo se desplegara el menú de selección de justificación.         \\
//=========================================================================================================================================\\
//DESCRIPCION  Abril 25 de 2012                                                                                                           \\
//=========================================================================================================================================\\
//Se agregó la validación de si se necesita o no la justificación antes de guardar el alta definitiva, en caso de que sea necesaria, el programa
//pide la justificación, desplegando en un menú todas las justificaciones que están habilitadas para el retraso.
//Básicamente lo que se hace es preguntar si se requiere justificación utilizando la función "requiere_justificacion()", en caso de que el
//resultado sea falso, permite realizar el guardado como se venia haciendo anteriormente, en caso contrario se despliegua un iframe que contiene
//un dropdown el que se cargan las opciones de justificación, este enviá a la página principal la justificación y continua el proceso.
//
//=========================================================================================================================================\\
//DESCRIPCION  Abril 24 de 2012                                                                                                           \\
//=========================================================================================================================================\\
//Se adiciona la función "requiere_justificación()" y lo que hace es validar si el alta definitiva necesita una justificación por retraso,
//es decir, cuando a un paciente se le da el alta en proceso la clínica espera que este tenga su alta definitiva en aproximadamente 1 hora y
//15 minutos, en caso de que esto no suceda se tiene una holgura en esta meta de un 20%(15 minutos), cuando definitivamente no se logró dar el
//alta definitiva al paciente en este tiempo está función brinda un parámetro para que el sistema sepa que debe solicitar una justificación para
//dicho retraso.
//=========================================================================================================================================\\
//=========================================================================================================================================\\
//DESCRIPCION  Marzo 14 de 2012                                                                                                           \\
//=========================================================================================================================================\\
//Se adiciona la validacion para traer la Central correspondiente al motivo de la solicitud, según el centro de costo y el tipo de central
//asociado al motivo de solicitud configurados en la tabla _000004 y 000009 de cencam.
//=========================================================================================================================================\\
//DESCRIPCION  Agosto 13 de 2010                                                                                                           \\
//=========================================================================================================================================\\
//Se adiciona en el campo de la habitacion el nombre del paciente                                                                          \\
//=========================================================================================================================================\\
//DESCRIPCION  Marzo 9 de 2012                                                                                                             \\
//=========================================================================================================================================\\
//Se modifica el llamado a la variable $wcentral asociada a CAMILLEROS.
//=========================================================================================================================================\\
//DESCRIPCION  Marzo 1 de 2010                                                                                                             \\
//=========================================================================================================================================\\
//Se adiciona la funcionalidad de anular o cancelar los servicios de alimentacion pedidos y cuyo paciente salga de alta definitiva, para   \\
//que no se queden los servicios de alimentacion de los pacientes dados de alta sin cancelar, este funcionalidad tambien se coloco en el   \\
//programa de 'registro_de_altas.php'.                                                                                                               \\
//La cancelacion se coloca dentro de for porque puede ser que hagan los pedidos por adelantado y se de alta al paciente con uno o varios   \\
//servicios de alimentacion pedidos.                                                                                                       \\
//                                                                                                                                         \\
//=========================================================================================================================================\\
//DESCRIPCION  Abril 7 de 2009                                                                                                             \\
//=========================================================================================================================================\\
//Este programa trae todas las historias que esten en proceso de ALTA, condicionado por: Que el Servicio sea hospitalario, que no sea de   \\
//Urgencias ni de Cirugia y que el servicio tenga atencion por parte del personal de hoteleria, el programa puede ser manejado por varias  \\
//personas al mismo tiempo, pero solo una puede definir o tomar un ALTA, es decir, quien toma un ALTA 'X' queda asignado y esta ALTA no    \\
//puede ser tomada por otra persona, ademas, el ALTA DEFINITIVA solo podra ser dada por quien toma el ALTA o por el personal que maneja el \\
//programa de 'registro_de_altas.php' (esto porque van a ver momentos en que no este el personal de hoteleria).                            \\
//Para dar un alta definitiva tiene que estar asignada(o) la persona de hoteleria, si no, no deja dar el ALTA; no va a tener el radiobutton\\
//activo para darle click.                                                                                                                 \\
//                                                                                                                                         \\
//=========================================================================================================================================\\


//=========================================================================================================================================\\
//=========================================================================================================================================\\
//ACTUALIZACIONES                                                                                                                          \\
//=========================================================================================================================================\\
//                                                                                                                                         \\
//Febrero 10 de 2010                                                                                                                       \\
//Se modifica el programa para que cuando se de clic en el radio boton de ALTA DEFINITIVA, Cancele el pedido de alimentacion del paciente  \\
//si es que lo tiene, la funcion busca si hay algun servicio que se pueda cancelar en ese instante y si el paciente tiene algun pedido de  \\
//ese Servivio y si es asi lo CANCELA, dejando tambien un registro en el LOG o Auditoria en la tabla _000078.                              \\
//                                                                                                                                         \\
//=========================================================================================================================================\\
//=========================================================================================================================================\\

  $wfecha=date("Y-m-d");
  $whora = (string)date("H:i:s");


  $q = " SELECT Empdes "
      ."   FROM root_000050 "
      ."  WHERE Empcod = '".$wemp_pmla."'"
      ."    AND Empest = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $row = mysql_fetch_array($res);

  $wnominst=$row[0];

  //Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos
  $q = " SELECT Detapl, Detval, Empdes "
      ."   FROM root_000050, root_000051 "
      ."  WHERE Empcod = '".$wemp_pmla."'"
      ."    AND Empest = 'on' "
      ."    AND Empcod = Detemp ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res);

  if ($num > 0 )
     {
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res);

	      if ($row[0] == "cenmez")
	         $wcenmez=$row[1];

	      if ($row[0] == "afinidad")
	         $wafinidad=$row[1];

	      if ($row[0] == "movhos")
	         $wbasedato=$row[1];

	      if ($row[0] == "tabcco")
	         $wtabcco=$row[1];

	      if ($row[0] == "camilleros")
	         $wcencam=$row[1];

	      $winstitucion=$row[2];
         }
     }
    else
       echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";


  //=====================================================================================================================================================================
  // F U N C I O N E S
  //=====================================================================================================================================================================

	/**
	 * * Función que permite calcular los días de estancia de un servicio basado en dos (2) fechas,
	 * * fecha ingreso y egreso de servicio
	 * 
	 * @param	String	fecha_ingreso_servicio	[Fecha de ingreso al servicio del cual egresa]
	 * @param	String	fecha_egreso_servicio	[Fecha de egreso del servicio]
	 * 
	 * @return	Float	dias_estancia			[Cantidad de días de estancia]
	 * 
	 * @author Joel Payares Hernández <joel.payares@lasamericas.com.co>
	 */
	function dias_estancia_servicio( $fecha_ingreso_servicio, $fecha_egreso_servicio )
	{
		$dias_estancia = ( strtotime($fecha_ingreso_servicio) - strtotime($fecha_egreso_servicio) ) / 86400;
		$dias_estancia = abs( $dias_estancia );
		$dias_estancia = round( $dias_estancia, 2 );

		return $dias_estancia;
	}

    // Consultar si el paciento es POS
    function consultartipopos($whistoria, $wingreso)
     {
         global $conex;
         global $wbasedato;

         //Se consulta el tipo de empresa responsable del paciente
         $q_resp = "SELECT ingtip
                      FROM ".$wbasedato."_000016
                     WHERE Inghis = '".$whistoria."'
                       AND Inging = '".$wingreso."'";
         $resresp = mysql_query($q_resp,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_resp." - ".mysql_error());
         $rowresp =mysql_fetch_array($resresp);
         $wtpo = $rowresp['ingtip'];

         //Se cosulta si el tipo de empresa se encuentra en el listado de empresas tipo POS.
         $q = "   SELECT COUNT(*)
                    FROM ".$wbasedato."_000076
                   WHERE Sertpo LIKE '%".$wtpo."%'
                     AND Serest = 'on' ";
         $restpo = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
         $rowtpo=mysql_fetch_array($restpo);

        if ($rowtpo[0] > 0)
        {
            $wtipopos = "(P)";
        }


        return $wtipopos;


     }


  function requiere_justificacion($widreg) //el id del registro en la tabla 18 que tiene la información actual del paciente.
    {
		global $wbasedato;
		global $conex;
		global $wemp_pmla;
		//preguntamos si el centro de costos está en promediar alta

	    //acá traemos la hora de alta en proceso y el centro de costos de la ubicacion actual del paciente.
		$q = "SELECT Ubifap, Ubihap, Ubisac"
			."  FROM ".$wbasedato."_000018"
			." WHERE id = '".$widreg."'";

		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$row = mysql_fetch_array($res);

		//verificamos si el cco necesita promediación de alta
		$q2 = "SELECT Ccopal"
			."  FROM ".$wbasedato."_000011"
			." WHERE Ccocod = '".$row[2]."'";

		$res2 = mysql_query($q2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());
		$row2 = mysql_fetch_array($res2);

		if($row2[0]=="off")
			return false;


		if(($row[0]=="0000-00-00")or($row[1]=="00:00:00"))
			return false;


		$htiap = strtotime($row[0]." ".$row[1]); //tiempo alta en proceso en segundos por UNIX


		//acá tengo que consultar la meta de la empresa.

		$q = "SELECT Empmsa "
			."  FROM root_000050 "
			." WHERE Empcod = '".$wemp_pmla."'";

		$res = $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$row = mysql_fetch_array($res);


		$segholgura = explode(":", $row[0]);
		$segholgura = ((integer)$segholgura[0]*60*60) + ((integer)$segholgura[1]*60) + ((integer)$segholgura[2]);


		//acá se consulta el porcentaje de holgura en la meta de la empresa
		$q = "SELECT Detval"
			."  FROM root_000051 "
			." WHERE Detemp = '".$wemp_pmla."'"
			."   AND Detapl = 'HolguraMetaAltas'";

		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$row = mysql_fetch_array($res);


		//convertimos a un valor decimal la holgura en la meta de la empresa.
		$aux = explode("%",$row[0]);
		$wholgura = (integer)$aux[0];
		$wholgura = $wholgura/100; //ya está como decimal.
		$holgtotal = $segholgura + ($segholgura*$wholgura); // tiempo máximo esperado para dar el alta definitiva


		$horaADE = $htiap + $holgtotal; //hora alta definitiva esperada




		//aca vamos a hacer la resta con la hora actual.
		//$rs = $horaADE - time(); //calculamos la diferencia entre la hora esperada de salida y la hora en la que se efectua la salida Definitiva

		if(time()<$horaADE)
		{
			return false;
		}
		return true;


    }

  function estado_del_alta($whis,$wing,&$westado,$whoralp,$wmuerte)
  {
	  global $wbasedato;
	  global $conex;
	  global $wespera;
	  global $wfecha;
	  global $waltadm;


	  //En facturacion
	  $q= " SELECT Cuefac, Cuegen, Cuepag, Cuecok, Cuefpa, Cuehpa, Cueffa, Cuehfa, Fecha_data, Hora_data"
          ."  FROM ".$wbasedato."_000022 "
          ." WHERE Cuehis = '".$whis."'"
          ."   AND Cueing = '".$wing."'";
      $res2 = mysql_query($q, $conex) or die("ERROR EN QUERY");
      $wnum2 = mysql_num_rows($res2);

      $waltadm="off";         //Alta administrativa


      if ($wnum2 > 0)         //Si hay registros es porque ya el facturador hizo el chequeo para facturar
         {
	      $row2 = mysql_fetch_array($res2);
	      $wfac=$row2[0];    //Factura
	      $wgen=$row2[1];    //Genero factura?
	      $wpag=$row2[2];    //Paga Si o No
	      $wcok=$row2[3];    //Cuenta OK
	      $wfpa=$row2[4];    //Fecha de Pago
	      $whpa=$row2[5];    //Hora de pago
	      $wffa=$row2[6];    //Fecha de facturado
	      $whfa=$row2[7];    //Hora de Facturado
	      $wfda=$row2[8];    //Fecha Data
	      $whda=$row2[9];    //Hora Data


	      if ($wfecha>$wfpa)
             $wespera="Desde ".$wfpa." a las ".$whpa;
            else
               $wespera="Desde las ".$whpa;


	      if ($wcok == "on")                             //Puede facturar
	         {
	          if ($wgen=="on")                           //Genero factura
	             {
	              if ($wpag=="on")                       //Ya se hizo el pago
                     {
	                   $westado = "con ALTA Administrativa ".$wespera;
	                   $waltadm="on";                    //Alta administrativa
	                 }
                    else
                       {
	                    $wespera="Desde las ".$whfa;     //Hora en que se facturo
	                    $westado = "Pendiente de Pago ".$wespera;
	                   }
                 }
                else
                  {
	               $wespera="Desde las ".$whda;          //Hora en que se chequeo que se podia facturar, osea que no hay devolucion pendiente
                   $westado = "Pendiente de facturar ".$wespera;
                  }
             }
            else                                         //No se puede facturar todavia
               {
                $westado = "Pendiente DEVOLUCION, ".$wespera;
               }
         }
        else
           $westado="En Facturación desde las ".$whoralp;

      if ($wmuerte=="on")
         $westado="Falleció. ".$westado;
	}

	function generar_menu_justificaciones()
	{

        global $conex;

		$wmovhos = consultarAliasPorAplicacion($conex, "01", "movhos");

		$query = "SELECT Juscod, Jusdes
					FROM `".$wmovhos."_000023`
				   WHERE Justip = 'R'"
                   ."AND Jusest = 'on'";
		$rs = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
		echo "<center><table border=0>";
		echo "<tr><td class='fila2' align=center colspan=4><b>CAUSA DEMORA EN EL ALTA </b></td></tr>";
		echo "<tr>";
		echo "<td class='fila2' align=center>";
		echo "<select name='mjust' id='mjust'>";
		 echo"<option value = '--' selected>--</option>";
		 $num = mysql_num_rows($rs);
		 for($i = 0; $i <$num; $i++)
			{
			$row = mysql_fetch_array($rs);
			echo "<option value ='".$row[0]."'>".$row[0]." - ".$row[1]."</option>";
			}
		echo "</select></td></tr>";
		echo "</table>";
	}

    function cambiarusuario($wemp_pmla, $wbasedato, $whis, $wing, $wfecha, $wnuevousuario, $whab, $wcco, $wuser)
    {


		global $conex;

	    $q = "UPDATE ".$wbasedato."_000018"
			."   SET ubihot = '".$wnuevousuario."'"
			." WHERE ubihis = '".$whis."'"
            ."   AND ubiing = '".$wing."'"
            ."   AND ubihac = '".$whab."'"
            ."   AND ubisac = '".$wcco."'"
            ."   AND ubialp = 'on'";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


    }

	// separando cada campo por el caracter |
function obtenerRegistrosFila($qlog)
{
	global $conex;

	$reslog = mysql_query($qlog, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qlog . " - " . mysql_error());
	$rowlog = mysql_fetch_row($reslog);
	$datosFila = implode("|", $rowlog);
	return $datosFila;
}

	//ANTES DE INSERTAR UNA ALTA O UNA MUERTE PARA UN PACIENTE SE CONSULTA SI YA TUVO ALTA O MUERTE Y SE ELIMINAN
	function BorrarAltasMuertesAntesDeAgregarNueva($conex, $wbasedato, $whis, $wing, $bandera)
	{
		$user_session = explode('-',$_SESSION['user']);
		$seguridad = $user_session[1];

		$q = "	SELECT *
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

				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$num = mysql_num_rows($res);
				$row = mysql_fetch_assoc($res);


				$existe_en_la_67 = false;
				$q67 = " SELECT * "
					."   FROM ".$wbasedato."_000067 "
					."  WHERE Fecha_data = '".$wfecha."'"
					."    AND Habhis = '".$whis."'"
					."    AND Habing = '".$wing."'";

				$res67 = mysql_query($q67,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
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

				$query_para_log = "	SELECT *
					FROM ".$wbasedato."_000033
					WHERE Historia_clinica = '".$whis."'
					AND Num_ingreso = '".$wing."'
					AND Tipo_egre_serv = '".$wtipoEgresoABorrar."'";
				$registrosFila = obtenerRegistrosFila($query_para_log);

				$q ="	DELETE FROM ".$wbasedato."_000033
						 WHERE Historia_clinica = '".$whis."'
						   AND Num_ingreso = '".$wing."'
						   AND Tipo_egre_serv = '".$wtipoEgresoABorrar."'";
				$res = mysql_query($q,$conex);

				$num_affect = mysql_affected_rows();
				if($num_affect>0)
				{

					$q = " UPDATE ".$wbasedato."_000038 "
						."    SET Ciemmay = '".$muerteMayor."',"
						."  	  Ciemmen = '".$muerteMenor."',"
						."  	  Cieeal = '".$egresosAlta."',"
						."  	  Cieegr = '".$cant_egresos."',"
						."  	  Cieocu = '".$cant_camas_ocupadas."',"
						."  	  Ciedis = '".$cant_camas_disponibles."'"
						."  WHERE Fecha_data = '".$wfecha."'"
						."    AND Cieser = '".$wcco."'"
						."  LIMIT 1 ";

					$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

					//Guardo LOG de borrado en tabla movhos 33 - Activacion paciente
					$q = "	INSERT INTO log_agenda
											  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
									   VALUES
											  ('".date('Y-m-d')."', '".date('H:i:s')."', '".$whis."', '".$wing."', 'Borrado tabla movhos_000033', '".$seguridad."', '".$bandera."', '".$registrosFila."')";
					$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				}
			}
		}
	}




//=====================================================================================================================================================================

    if (isset($consultaAjax))
		     {

				switch($consultaAjax)
					{

				case 'cambiarusuario':
					{

                    echo cambiarusuario($wemp_pmla,$wbasedato,$whis,$wing,$wfecha, $wnuevousuario, $whab, $wcco, $wuser);

					}
				break;

                default : break;
					}
				return;

			 }

   if (!isset($consultaAjax))
	{


  encabezado("Pacientes en Proceso de Alta", $wactualiz, 'clinica');


  //FORMA ================================================================
  echo "<form name='hoteleria' id='hoteleria' action='Hoteleria.php' method=post>";

  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
  echo "<input type='HIDDEN' name='wcencam' value='".$wcencam."'>";
  echo "<input type='HIDDEN' id='wconsulta' value='".$wconsulta."'>";




  //echo "<br>";
  echo "<center><table>";


  if (strpos($user,"-") > 0)
     $wusuario = substr($user,(strpos($user,"-")+1),strlen($user));

  //===============================================================================================================================================
  //ACA COMIENZA EL MAIN DEL PROGRAMA
  //===============================================================================================================================================


  $wmovhos = consultarAliasPorAplicacion($conex, "01", "movhos");
  $q = " SELECT Ubihac, Ubihis, Ubiing, ".$wbasedato."_000018.id, Ubihot, Ubisac "
      ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000020, ".$wbasedato."_000011 "
      ."  WHERE Ubiptr != 'on' "             //Solo los pacientes que no esten siendo trasladados
      ."    AND Ubialp  = 'on' "
      ."    AND Ubiald != 'on' "			 //Que no este en Alta Definitiva
      ."    AND Ubisac  = ccocod "
	  ."    AND Ubihis = Habhis "    		 //Valida que el paciente si este en la habitacion asignada
	  ."    AND Ubiing = Habing "
      ."    AND Ccohos  = 'on' "             //Que el CC sea hospitalario
      ."    AND Ccourg != 'on' "             //Que no se de Urgencias
      ."    AND Ccocir != 'on' "             //Que no se de Cirugia
      ."    AND Ccohot  = 'on' "             //Que tenga servicio de hoteleria
	  ."  GROUP BY 1,2,3,4,5 ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res);

	if ($num >= 1)
	{
		for ($i=1;$i<=$num;$i++)
	    {
			$row = mysql_fetch_array($res);

			//==========================================================================================================================================================
			//==========================================================================================================================================================
			//Si seleccionaron *** EN HOTELERIA ***
			//==========================================================================================================================================================
			if (isset($whoteleria[$i])) // and $wid[$i] == $row[7])
			{
				//=======================================================================================================================================================
				//Actualizo la hotelera que atiende el ALTA
				$q = " UPDATE ".$wbasedato."_000018 "
					."    SET Ubifho  = '".$wfecha."',"    //Fecha en que la hotelera selecciona el paciente
					."        Ubihho  = '".$whora."', "    //Hora en que la hotelera selecciona el paciente
					."        Ubihot  = '".$wusuario."'"   //Codigo de la hotelera
					."  WHERE Ubihis  = '".$row[1]."'"
					."    AND Ubiing  = '".$row[2]."'"
					."    AND Ubialp  = 'on' "
					."    AND Ubiald != 'on' "
					."    AND Ubiptr != 'on' "
					."    AND Ubihot  = '' "
					."    AND id      = ".$row[3];
				$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				unset($whoteleria[$i]);
			}

			//==========================================================================================================================================================
			//==========================================================================================================================================================
			//Si seleccionaron *** ALTA DEFINITIVA ***
			//==========================================================================================================================================================
			if (isset($wdefinitiva[$i]) and $wid[$i] == $row[3])
			{
				//Verifico que el ALTA si la vaya a dar la hotelera que tiene el caso, si no aviso.
				$q = " SELECT COUNT(*) "
					."   FROM ".$wbasedato."_000018 "
					."  WHERE Ubihis  = '".$row[1]."'"
					."    AND Ubiing  = '".$row[2]."'"
					."    AND Ubialp  = 'on' "
					."    AND Ubiald != 'on' "
					."    AND Ubiptr != 'on' "
					."    AND Ubihot  = '".$wusuario."'"
					."    AND id      = ".$row[3];
				$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$fil = mysql_fetch_array($err);

				if ($fil[0] > 0)  //Si entra es porque el caso si lo tiene esa hotelera
				{
					$continuar = false; //esta variable me va a controlar el proceso de cancelado de almimentación, limpieza de habitación etc...
						//=======================================================================================================================================================

					$wreqjust = requiere_justificacion($wid[$i]);
					if(!$wreqjust)//sino requiere justificación ejecuta el query sin la justificación
					{
						$q = " UPDATE ".$wbasedato."_000018 "
							."    SET Ubiald  = 'on', "
							."        Ubifad  = '".$wfecha."',"
							."        Ubihad  = '".$whora."', "
							."        Ubiuad  = '".$wusuario."' "
							."  WHERE Ubihis  = '".$row[1]."'"
							."    AND Ubiing  = '".$row[2]."'"
							."    AND Ubialp  = 'on' "
							."    AND Ubiald != 'on' "
							."    AND Ubiptr != 'on' "
							."    AND id      = ".$row[3]
							."    AND Ubihot  = '".$wusuario."'";   //Codigo de la hotelera, esto lo hago para que solo la hotelera que tenia el ALTA pueda dar el ALTA DEFINITIVA
						$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

						$continuar = true;
					}else{
						if(isset($wjust))
						{
							$q = " UPDATE ".$wbasedato."_000018 "
								."    SET Ubiald  = 'on', "
								."        Ubifad  = '".$wfecha."',"
								."        Ubihad  = '".$whora."', "
								."        Ubiuad  = '".$wusuario."', "
								."		  Ubijus  = '".$wjust."'"
								."  WHERE Ubihis  = '".$row[1]."'"
								."    AND Ubiing  = '".$row[2]."'"
								."    AND Ubialp  = 'on' "
								."    AND Ubiald != 'on' "
								."    AND Ubiptr != 'on' "
								."    AND id      = ".$row[3]
								."    AND Ubihot  = '".$wusuario."'";   //Codigo de la hotelera, esto lo hago para que solo la hotelera que tenia el ALTA pueda dar el ALTA DEFINITIVA
							$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							$continuar = true;
						}

					}

						if($continuar == true)
						{
							//=================================================================
							//Busco si la historia tiene pedido de alimentacion para cancelarlo
							//=================================================================
							cancelar_pedido_alimentacion($row[1], $row[2], $row[5], "Cancelar", $wusuario);      //Febrero 10 2010
							cancelarPedidoInsumos($conex, $wbasedato, $row[1], $row[2]);

							//Con este proceso si al desaparecer una linea la que sigue no estaba setiada, hago que siga dessetiada
							for ($j=$i+1;$j<=$num;$j++)
							{
								if (!isset($wproceso[$j]))
								{
									if (isset($wproceso[$j-1])) unset($wproceso[$j-1]);
								}
							}
							//=======================================================================================================================================================


							//==========================================================================================
							//Ago 13 de 2010
							//Si se coloco habitacion, traigo el nombre del paciente
							if ($row[0] != "")
							{
								$whabpac=$row[0];

								$q = " SELECT Pacno1, Pacno2, Pacap1, Pacap2 "
									."   FROM root_000036, root_000037, ".$wbasedato."_000020 "
									."  WHERE Habcod = '".$whabpac."'"
									."    AND Habhis = orihis "
									."    AND Habing = oriing "
									."    AND Oriori = '".$wemp_pmla."'"
									."    AND Oriced = pacced "
									."    AND Oritid = pactid ";
								$reshab = mysql_query($q,$conex);
								$rowhab = mysql_fetch_array($reshab);

								$numhab = mysql_num_rows($reshab);

								if ($numhab > 0)
								$whabpac="<b>".$whabpac."</b><br>Pac: ".$rowhab[0]." ".$rowhab[1]." ".$rowhab[2]." ".$rowhab[3];
							}
							//==========================================================================================


							$wfecha = date("Y-m-d");
							$whora = (string)date("H:i:s");
							//=======================================================================================================================================================
							//Actualizo o pongo en modo de limpieza la habitación en la que estaba el paciente
							$q = " UPDATE ".$wbasedato."_000020 "
								."    SET Habali = 'on', "
								."        Habdis = 'off', "
								."        Habhis = '', "
								."        Habing = '', "
								."        Habfal = '".$wfecha."', "
								."        Habhal = '".$whora."', "
								."        Habprg = '' "
								."  WHERE Habcod = '".$row[0]."'"
								."    AND Habhis = '".$row[1]."'"
								."    AND Habing = '".$row[2]."'";
							$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							//=======================================================================================================================================================

							//=======================================================================================================================================================
							/**
							 * * Consulta que permite obtener el número de ingreso al servicio
							 * * Segmento de código agregado 20/08/2021
							 * @author Joel Payares Hernández <joel.payares@lasamericas.com.co>
							 */
							$sql_num_ing_serv = "
									SELECT	Num_ing_Serv
									FROM	{$wbasedato}_000032
									WHERE	Historia_clinica = '{$whis}'
										AND	Num_ingreso      = '{$wing}'
										AND	Servicio         = '{$wcco}'
								ORDER BY	Num_ing_serv DESC
									LIMIT	1;
							";
							$err = mysql_query( $sql_num_ing_serv, $conex ) or die (mysql_errno().$q." - ".mysql_error());	
							$row_num_ing_serv = mysql_fetch_array( $err );
							$wnum_ing_serv = $row_num_ing_serv[0];

							//==================================================
							/**
							 * * Consulta que permite obtener la fecha y hora de ingreso al servicio del cual egresa por ALTA
							 * * Segmento de código agregado 20/08/2021
							 * @author Joel Payares Hernández <joel.payares@lasamericas.com.co>
							 */
							// $q=  "
							// 		SELECT	CONCAT( Fecha_ing,' ', Hora_ing ) as Fecha_hora, Num_ing_Serv
							// 		FROM	{$wbasedato}_000032
							// 		WHERE	Historia_clinica	=	'{$whis}'
							// 			AND	Num_ingreso			=	'{$wing}'
							// 			AND	Servicio			=	'{$wcco}'
							// 			AND	Num_ing_Serv		=	'{$wnum_ing_serv}'
							// 	GROUP BY	2
							// ";
							// $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
							// $rowdia = mysql_fetch_array($err);

							// $wdiastan = dias_estancia_servicio( (string) $rowdia[0], (string) date("Y-m-d H:i:s") );

							// $wnuming = $rowdia[1];

							$q = "
								  SELECT	ROUND(
									  			TIMESTAMPDIFF(
													MINUTE,
													CONCAT( Fecha_ing,' ', Hora_ing ),
													now()
												)/(24*60), 2
											),
											Num_ing_Serv
									FROM	{$wbasedato}_000032
								   WHERE	Historia_clinica	=	'{$whis}'
									 AND	Num_ingreso			=	'{$wing}'
									 AND	Servicio			=	'{$wcco}'
									 AND	Num_ing_Serv		=	'{$wnum_ing_serv}'
								GROUP BY	2
							";
							$err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
							$rowdia = mysql_fetch_array($err);
							$wdiastan = $rowdia[0];
							$wnuming = $rowdia[1];

							if ($wdiastan=="" or $wdiastan==0)
								$wdiastan=0;

							if ($wnuming=="" or $wnuming==0)
								$wnuming=1;

							//BUSCO SI EL ALTA ES POR MUERTE O NO
							$q = " SELECT Ubimue "
								."   FROM ".$wbasedato."_000018 "
								."  WHERE Ubihis = '".$row[1]."'"
								."    AND Ubiing = '".$row[2]."'";
							$err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
							$rowmue = mysql_fetch_array($err);

							if ($rowmue[0]!="on")
							{
								BorrarAltasMuertesAntesDeAgregarNueva($conex, $wbasedato, $row[1], $row[2], 'Egreso Existente');

								$wmotivo="ALTA";
								//Grabo el registro de egreso del paciente del servicio
								$q = " INSERT INTO ".$wbasedato."_000033 (   Medico       ,   Fecha_data,   Hora_data,   Historia_clinica,   Num_ingreso,   Servicio   ,  Num_ing_Serv,   Fecha_Egre_Serv ,   Hora_egr_Serv ,    Tipo_Egre_Serv,  Dias_estan_Serv, Seguridad        ) "
											."                    VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$row[1]."'      ,'".$row[2]."' ,'".$row[5]."' ,".$wnuming."  ,'".$wfecha."'      ,'".$whora."'     , '".$wmotivo."'   ,".$wdiastan."    , 'C-".$wusuario."')";
								$err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
							}
							//=======================================================================================================================================================


							//=======================================================================================================================================================
							//Pido el servicio de Camillero
							//Traigo el nombre del Origen de la tabla 000004 de la base de datos de camilleros con el centro de costos actual
							$q = " SELECT Nombre "
								."   FROM ".$wcencam."_000004 "
								."  WHERE mid(Cco,1,instr(Cco,'-')-1) = '".$row[5]."'"
								."  GROUP BY 1 ";
							$err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
							$rowori = mysql_fetch_array($err);
							$worigen=$rowori[0];

							//$wcentral="CAMILLEROS";
							$wcco=$row[5];
							//=======================================
							//Traigo el Tipo de Central
							$q = " SELECT Tip_central "
								."   FROM ".$wcencam."_000001 "
								."  WHERE Descripcion = 'PACIENTE DE ALTA'"
								."    AND Estado = 'on' ";
							$restce = mysql_query($q,$conex);
							$rowcen = mysql_fetch_array($restce);
							$wtipcen = $rowcen[0];
							//=======================================
							//=============================================================================
							//=============================================================================
							//Traigo la Central asignada para el Centro de Costos según el Tipo de Central
							$q = " SELECT Rcccen "
								."   FROM ".$wcencam."_000009 "
								."  WHERE Rcccco = '".$wcco."'"
								."    AND Rcctic = '".$wtipcen."'";
							$rescen = mysql_query($q,$conex);
							$rowcen = mysql_fetch_array($rescen);
							$wcentral=$rowcen[0];

							//En caso de responder vacio o falso en la central, se consultara con el *.
							if ($wcentral == FALSE)
								{
								$q = " SELECT Rcccen "
									."   FROM ".$wcencam."_000009 "
									."  WHERE Rcccco = '*'"
									."    AND Rcctic = '".$wtipcen."'";
								$rescen = mysql_query($q,$conex);
								$rowcen = mysql_fetch_array($rescen);
								$wcentral=$rowcen[0];
								}
								else
									{
									$wcentral=$wcentral;
									}

							//=======================================================================================================================================================

							if ($rowmue[0]!="on")  //No pide el camillero si el paciente Murio, porque se pidio cuando marco la muerte
								{
								//=======================================================================================================================================================
								//Grabo el registro solicitud del camillero
								$q = " INSERT INTO ".$wcencam."_000003 (   Medico     ,   Fecha_data,   Hora_data,   Origen     , Motivo           ,   Habitacion  , Observacion                                                                                                            , Destino ,    Solicito    ,    Ccosto  , Camillero, Hora_respuesta, Hora_llegada, Hora_Cumplimiento, Anulada, Observ_central,    Central     , Seguridad        ) "
									."                  VALUES ('".$wcencam."','".$wfecha."','".$whora."','".$worigen."','PACIENTE DE ALTA','".$whabpac."' , 'Se dio alta definitiva desde el sistema de altas (Hotelería) a la Historia: ".$row[1]."-".$row[2]." a las ".$whora."' , 'ALTA'  , '".$wusuario."', '".$wcco."', ''       , ''            , ''          , ''               , 'No'   , ''            , '".$wcentral."', 'C-".$wusuario."')";
								$err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
								//=======================================================================================================================================================
								}

						}
				}
				else
					{
					?>
						<script>
						alert ("EL ALTA LO DEBE DAR LA HOTELERA(O) QUE TENGA EL CASO");
						</script>
					<?php

					}
			}
		    }
         }

  //Aca trae los pacientes que esten en proceso de alta en cualquier estado
  $q = " SELECT Ubihac, Ubihis, Ubiing, ".$wbasedato."_000018.id, Ubihot, Ubihap, Ubimue, Ubihan, Ubisac "
      ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000020, ".$wbasedato."_000011 "
      ."  WHERE Ubiptr != 'on' "             //Solo los pacientes que no esten siendo trasladados
      ."    AND Ubialp  = 'on' "
      ."    AND Ubiald != 'on' "			 //Que no este en Alta Definitiva
	  ."    AND Ubihis = Habhis "    		 //Valida que el paciente si este en la habitacion asignada
	  ."    AND Ubiing = Habing "
      ."    AND Ubisac  = ccocod "
      ."    AND Ccohos  = 'on' "             //Que el CC sea hospitalario
      ."    AND Ccourg != 'on' "             //Que no se de Urgencias
      ."    AND Ccocir != 'on' "             //Que no se de Cirugia
      ."    AND Ccohot  = 'on' "             //Que tenga servicio de hoteleria
	  ."  GROUP BY 1,2,3,4,5,6 ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res);

  echo "<tr><td align=left bgcolor=#fffffff colspan=3><font size=2 text color=#CC0000><b>Hora: ".$whora."</b></font></td><td align=left bgcolor=#fffffff colspan=10><font size=2 text color=#CC0000><b>Cantidad de Altas: ".$num."</b></font></td></tr>";
  echo "<tr>";
  echo "<td> </td>";
  echo "<td> </td>";
  echo "<td> </td>";
  echo "<td class=fondorojo colspan=4> <b>NOTA:</b> Debe justificar si se supera el tiempo promedio</td></tr>";
  echo "<tr class='encabezadoTabla'>";
  echo "<th>Tipo POS</th>";
  echo "<th>Habitacion</th>";
  echo "<th>Historia</th>";
  echo "<th>Paciente</th>";
  echo "<th>Alta Definitiva</th>";
  echo "<th>Afinidad</th>";
  echo "<th>En Hoteleria</th>";
  echo "<th>Estado del ALTA</th>";
  echo "</tr>";

  $wtipopos = '';

  if ($num > 0)
     {
	  for($i=1;$i<=$num;$i++)
		 {
		  $row = mysql_fetch_array($res);

		  if (is_integer($i/2))
             $wclass="fila1";
            else
               $wclass="fila2";

          $whab = $row[0];
		  $whis = $row[1];
		  $wing = $row[2];
		  $wreg = $row[3];
	      $whot = $row[4];
	      $whap = $row[5];
	      $wmue = $row[6];
	      $whan = $row[7];
          $wccoactual = $row[8];
		  $control_saldo_insumos = "";
		  $title_saldo = "";
		  
           //Traigo el usuario asociado al centro de costos y al codigo.
            $a = " SELECT descripcion, codigo "
                ."   FROM usuarios "
                ."  WHERE ccostos = '1081' "
                ."	 AND codigo = '".$whot."'"
                ."	 AND activo = 'A'";
            $resa = mysql_query($a,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$a." - ".mysql_error());
            $rowa = mysql_fetch_array($resa);

            $wcentrosostoshot = consultarAliasPorAplicacion($conex, "01", "Hoteleros"); //Consulta el centro de costos de los usuario de hoteleria.
            $whoteleros = explode("-", $wcentrosostoshot);
            $num_usuarios = count($whoteleros);
//            //Traigo los usuarios asociados al centro de costos
//            $b = " SELECT descripcion, codigo "
//                ."   FROM usuarios "
//                ."  WHERE ccostos = '".$wcentrosostoshot."' "
//                ."	 AND activo = 'A'";
//            $resb = mysql_query($b,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$b." - ".mysql_error());
//            $num_usuarios = mysql_num_rows($resb);


	      //Traigo los datos demograficos del paciente
          $q = " SELECT Pacno1, Pacno2, Pacap1, Pacap2, Pacced, Pactid "
              ."   FROM root_000036, root_000037 "
              ."  WHERE Orihis = '".$whis."'"
		      ."    AND Oriing = '".$wing."'"
		      ."    AND Oriori = '".$wemp_pmla."'"  //Empresa Origen de la historia,
		      ."    AND Oriced = Pacced ";
          $respac = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
          $rowpac = mysql_fetch_array($respac);

		  $wpac = $rowpac[0]." ".$rowpac[1]." ".$rowpac[2]." ".$rowpac[3];
		  $wdpa = $rowpac[4];
	      $wtid = $rowpac[5];

	      estado_del_alta($whis,$wing,$westado,$whap,$wmue);

	      $wcolor="";
		  
		$query_ins = "SELECT SUM(Carcca - Carcap - Carcde) as saldo_insumos "
					  ."FROM ".$wbasedato."_000227 "
					 ."WHERE Carhis = '".$whis."'
						 AND Caring = '".$wing."'
						 AND Carcca - Carcap - Carcde > 0
						 AND Carest = 'on'";
		$res_ins = mysql_query($query_ins, $conex) or die("Error: ".mysql_errno()." - en el query: ". $query_ins ." - ".mysql_error());
		$row_ins = mysql_fetch_array($res_ins);
		$saldo_insumos = $row_ins['saldo_insumos'];
		
		if($saldo_insumos == 0){
		
		  if ($waltadm == "on")   //Pregunto si tiene alta administrativa
			 {
			  $whabilita = "ENABLED";
			  $wcolor="FFFF00";

			  //Pregunto si el campo de hotelera tiene dato, si tiene es porque una hotelera tiene el alta, si no tiene es porque ninguna hotelera se ha
			  //encargado de la salida del paciente, por lo tanto no puede dar alta definitiva.
			  if ($whot == "")
				$whabilita = 'DISABLED';
			 }
			else{
			  $whabilita = 'DISABLED';
			}
		}else{
			$whabilita = 'DISABLED';
			$control_saldo_insumos = "on";
		}
			
		  if ($wmue=="on")
		     $whab=$whan;

	      echo "<tr class=".$wclass.">";
	      echo "<input type='HIDDEN' NAME=wid[".$i."] VALUE=".$wreg.">";                             //No se muestra el registro, se lleva una variable hidden
          $wtipopos = consultartipopos($whis, $wing);
          echo "<td align=center rowspan=1 ><font size=2 color='#CC0000'><b>".$wtipopos."</b></font></td>";
	      echo "<td align=center rowspan=1 ><font size=2><b>".$whab."</b></font></td>";
	      echo "<td align=center><font size=2><b>".$whis." - ".$wing."</b></font></td>";
	      echo "<td align=left><font size=2><b>".$wpac."</b></font></td>";
		  $wreqjust = requiere_justificacion($wreg);
		  
		  if($control_saldo_insumos == 'on'){
			  $wclass = 'fondoAmarillo';
			  $title_saldo = 'Paciente con saldo en insumos, comuníquese <br> con la jefe de enfermería en ese servicio.';
		  }
		  
		  if($wreqjust == true)
		  {
			echo "<td align=center class='fondorojo msg' title='".$title_saldo."'><INPUT TYPE=radio NAME=wdefinitiva[".$i."] ".$whabilita." onclick='fnMostrar()' </td>";
		  }else
			{
				echo "<td align=center class='".$wclass." msg'  title='".$title_saldo."' ><INPUT TYPE=radio NAME=wdefinitiva[".$i."] ".$whabilita." onclick='enter()' </td>";
			}
	      //======================================================================================================
	      //En este procedimiento pregunto si el paciente es cliente AFIN o no, y de que tipo
	      $wafin=clienteMagenta($wdpa,$wtid,$wtpa,$wcolorpac);
	      if ($wafin)
		     echo "<td align=center><font color=".$wcolorpac."><b>".$wtpa."<b></font></td>";
		    else
		      echo "<td>&nbsp</td>";
		  //======================================================================================================

		 if (trim($whot)=="")
            {
		     echo "<td align=center bgcolor=".$wcolor."><INPUT TYPE='checkbox' NAME=whoteleria[".$i."] onclick='enter()'></td>";
            }
		    else
            {
                $wusuario = explode("-", $user);
                if($wusuario[1] != $whot)
                {
                    echo "<td align=center bgcolor=".$wcolor."><b>Asignado a</b> <br>".$rowa[0]."</td>";
                }
                else
                {
                // echo "<td align=center bgcolor=".$wcolor."><b>Atendida por<br>".$whot."</b></td>";
                echo "<td align=center bgcolor=".$wcolor."><b>Asignado a</b> <br><SELECT name='wnusu".$whis.$wing."' id='wnusu".$whis.$wing."' onchange='cambiarusuario(\"".$wemp_pmla."\",\"".$wbasedato."\",\"".$whis."\",\"".$wing."\",\"".$wfecha."\", \"wnusu".$whis.$wing."\",\"".$whab."\",\"".$wccoactual."\",\"".$wuser."\")'>";

                $a = " SELECT descripcion, codigo "
                    ."   FROM usuarios "
                    ."  WHERE codigo = '".$whot."'"
                    ."	  AND activo = 'A'";
                $resa = mysql_query($a,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$a." - ".mysql_error());
                $rowa = mysql_fetch_array($resa);


                if (isset($whot))
                {
                    echo "<OPTION SELECTED value=".$whot.">".$rowa[0]."</OPTION>";
                }

                for ($j=0;$j<=($num_usuarios-1);$j++)
                    {

                    $b = " SELECT descripcion, codigo "
                        ."   FROM usuarios "
                        ."  WHERE codigo = '".$whoteleros[$j]."'"
                        ."	  AND activo = 'A'";
                    $resb = mysql_query($b,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$b." - ".mysql_error());
                    $rowb = mysql_fetch_array($resb);

                    echo "<OPTION value=".$rowb[1].">".$rowb[0]."</OPTION>";
                    }
                echo "</SELECT></td>";
            }
            }

	      echo "<td align=left bgcolor=".$wcolor."><font size=2><b>".$westado."</b></font></td>";    //Estado
	      echo "</tr>";
		  //echo "<tr class=".$wclass." id='tr".$i."'></tr>";
	     }
	  }
	 else
	    echo "NO HAY HABITACIONES OCUPADAS";
  echo "</table>";

  echo "<table>";
  echo "<tr>";
  echo "<br>";
  echo "<td align='left' style='width: 1000px; color:#CC0000'><font size=2><b>(P) Paciente tipo POS<b></font></td>";
  echo "</tr>";
  echo "</table>";

  echo "<div id='menuJusti' style='display:none;width:100%' title='JUSTIFICACIONES'>";
			generar_menu_justificaciones();
	echo "<br><INPUT TYPE='button' value='Aceptar' onClick='agregarJusti()' style='width:100'>";
  echo "</div>";

  $wini="N";
  echo "<br>";
  echo "<br>";

  echo "<meta http-equiv='refresh' content='20;url=Hoteleria.php?wemp_pmla=".$wemp_pmla."&wuser=".$user."&wconsulta=".$wconsulta."'>";

  echo "<input type='HIDDEN' name='wini' value='".$wini."'>";

  echo "<br>";


    echo "</form>";

    echo "<br>";
    echo "<table>";
    echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' id='cerrarventana' onclick='cerrarVentana()'></td></tr>";
    echo "</table>";

    }

} // if de register



include_once("free.php");

?>
