<head>
  <title>PREPARACION MEDICAMENTOS</title>

  <style type="text/css">

    	A	{text-decoration: none;color: #000066;}
    	.tipo3V{color:#000066;background:#dddddd;font-size:15pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo3V:hover {color: #000066; background: #999999;}
        .tipo4V{color:#000066;background:#dddddd;font-size:15pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo4V:hover {color: #000066; background: #999999;}

    </style>

</head>

<script type="text/javascript">
function enter()
	{
	 document.forms.preparacion.submit();
	}

function cerrarVentana()
	 {
      window.close()
     }

function parpadear() {
  var blink = document.all.tags("BLINK")
  for (var i=0; i < blink.length; i++)
    blink[i].style.visibility = blink[i].style.visibility == "" ? "hidden" : ""
}

function empezar() {
    setInterval("parpadear()",500)
}
window.onload = empezar;
</script>

<body>

<?php
include_once("conex.php");
  /*********************************************************
   *   APLICACION DE MEDICAMENTOS Y MATERIAL A PACIENTES   *
   *    EN LA UNIDAD EN DONDE SE ENCUENTRE EL PACIENTE     *
   *     				CONEX, FREE => OK				   *
   *********************************************************/


//=============================================================================================================================================
//M O D I F I C A C I O N E S
//=============================================================================================================================================
// 2019-03-06 ( Edwin MG )	Se muestra el cco de urgencias y pide zonas según el cco
//=============================================================================================================================================
// 2018-02-15 ( Edwin MG )	Los medicamentos de ayudas diagnósticas no se muestran en piso
//=============================================================================================================================================
// 2017-12-18 ( Jessica )	Se agrega el llamado a la función consultarDiagnosticoPaciente() de comun.php que devuelve la lista de los 
// 							diagnósticos actuales del paciente
//=============================================================================================================================================
// 2016-12-05 ( Jessica )	Se modifican las alertas para que traiga las ingresadas en movhos_000220.
//=============================================================================================================================================
// 2016-09-21 ( Edwin MG )	Se cambia la función query_articulos1 para que no tenga en cuenta el campo kardex confirmado(Karcon), ya que se debe mirar siempre
//							el día actual siempre y cuando halla ordenes para el día en curso.
//=============================================================================================================================================
// 2016-08-31 ( Edwin MG )	Si un medicamento ya cumplió las dosis máxima, no se muestra en la lista
//=============================================================================================================================================
// 2016-07-12 ( Edwin MG )	Se modifica para que no tenga en cuenta los articulos genericos de Levs e IC y se muestre la observación
//							más reciente de los articulos.
//=============================================================================================================================================
// 2013-12-12 ( Camilo ZZ) se modificó el programa para que consulte los pendientes tambien en la tabla 000143 ( tabla de contingencia)
//=============================================================================================================================================
//=============================================================================================================================================
// Junio 26 de 2012 Viviana Rodas.  Se agrega el llamado a las funciones consultaCentrosCostos que hace la consulta de los centros de costos
// de un grupo seleccionado y dibujarSelect que dibuja el select con los centros de costos obtenidos de la primera funcion.
//=============================================================================================================================================
// Marzo 5 de 2012
//=============================================================================================================================================
// Se modifica para mostrar si el paciente esta en proceso de Alta
//=============================================================================================================================================
//=============================================================================================================================================
// Diciembre 9 de 2011
//=============================================================================================================================================
// Se modificó la función elegir_historia para que descarte los pacientes del servicio que no tienen Kardex asignado en una ronda
// se adicionó una columna para la frecuencia de administración del medicamento
// se eliminó la columna Aprobado regente ( se comentaron las lineas 822 y 988 )
//=============================================================================================================================================


session_start();

if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");


if(!isset($_SESSION['user']))
	echo "error";
else
{

  

  include_once("root/comun.php");
  include_once("movhos/movhos.inc.php");
  


  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user));


		                                         // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="2017-12-18";                     // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                             // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //

  echo "<br>";
  echo "<br>";

  //*******************************************************************************************************************************************
  //F U N C I O N E S
  //===========================================================================================================================================
  
	function SeleccionarZona()
	{
		global $conex;
		global $wbasedato;
		global $wcco;
		global $wzona;

		$wcco1 = explode("-",$wcco);

		/////////////
		echo "<center><table>";
		// $q = " SELECT ccozon "
		  // ."   FROM ".$wbasedato."_000011 "
		  // ."  WHERE ccocod = '".trim($wcco1[0])."'";

		$q = "SELECT Arecod, Aredes
			  FROM ".$wbasedato."_000020, ".$wbasedato."_000169
			 WHERE habcco = '".trim($wcco1[0])."'
			   AND habzon = Arecod
		  GROUP BY habzon, habcco";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		// $row = mysql_fetch_array($res);

		// $wzon = explode(",",$row[0]);   //Devuelve las zonas

		echo "<tr class=fila1><td align=center><font size=30>Seleccione la Zona : </font></td></tr>";
		echo "</table>";
		echo "<br><br><br>";
		echo "<center><table>";
		echo "<tr><td align=center><select name='wzona' style=' font-size:40px; font-family:Verdana, Arial, Helvetica, sans-serif;' onchange='enter()'>";
		echo "<option>&nbsp</option>";
		// for ($i=0;$i<=(count($wzon)-1);$i++)
		 // {
		  // echo "<option>".$wzon[$i]."</option>";
		 // }
		while( $row = mysql_fetch_array($res) ){
			echo "<option value='".$row['Arecod']."'>".$row['Aredes']."</option>";;
		}
		 
		echo "</select></td></tr>";
		echo "</table>";
	}
  
	function CcoTieneZonas()
	{
		global $conex;
		global $wbasedato;
		global $wcco;

		$wcco1 = explode("-",$wcco);
		//echo "wcco1".$wcco1;

		// $q = " SELECT ccozon "
		    // ."   FROM ".$wbasedato."_000011 "
		    // ."  WHERE ccocod = '".trim($wcco1[0])."'";
			
		$q = "SELECT Arecod, Aredes
			  FROM ".$wbasedato."_000020, ".$wbasedato."_000169
			 WHERE habcco = '".trim($wcco1[0])."'
			   AND habzon = Arecod
		  GROUP BY habzon, habcco";
			
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$row = mysql_fetch_array($res);
		$wcan = mysql_num_rows($res);   //Si el explode devuelve algo es porque hay zonas para el Cco
		// $wcan = COUNT(EXPLODE(",",$row[0]));   //Si el explode devuelve algo es porque hay zonas para el Cco

		// if ($wcan > 1)
		if ($wcan > 0)
			return true;      //Tiene Zonas
		else
			return false;	   //No tiene Zonas
	}
  
  /************************************************************************************************************
   * Devuelve la ronda en formato unix de la última aplicacion del medicamento
   ************************************************************************************************************/
  function consultarUltimaAplicacion( $conex, $wbasedato, $his, $ing, $articulo, $ido ){

	$val = 0;
	
	$sql = "SELECT
				CONCAT( Aplfec,' ', SUBSTRING( Aplron, 1, 2 ), ':00:00' ) as Aplfec
			FROM
				{$wbasedato}_000015
			WHERE
				aplhis = '$his'
				AND apling = '$ing'
				AND aplart = '$articulo'
				AND aplido = '$ido'
				AND aplest = 'on'
			ORDER BY 1 DESC
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );

	if( $rows = mysql_fetch_array( $res ) ){
		$val = strtotime( $rows['Aplfec'] );
	}

	return $val;
  }
  
  function mostrar_empresa($wemp_pmla)
     {
	  global $user;
	  global $conex;
	  global $wcenmez;
	  global $wafinidad;
	  global $wbasedato;
	  global $wtabcco;
	  global $winstitucion;
	  global $wactualiz;

	  //Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos
	  $q = " SELECT detapl, detval, empdes "
	      ."   FROM root_000050, root_000051 "
	      ."  WHERE empcod = '".$wemp_pmla."'"
	      ."    AND empest = 'on' "
	      ."    AND empcod = detemp ";
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
	         }
	     }
	    else
	       echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";

	  $winstitucion=$row[2];

	  encabezado("Preparación de Medicamentos ",$wactualiz, "clinica");
     }

	 
	 

  function elegir_centro_de_costo()
     {
	  global $user;
	  global $conex;
	  global $wcenmez;
	  global $wafinidad;
	  global $wbasedato;
	  global $wtabcco;
	  global $winstitucion;
	  global $wactualiz;
	  global $wcco;

	  global $whora_par_actual;
	  global $whora_par_sigte;
	  global $whora_par_anterior;


	  echo "<center><table>";
      echo "<tr class=encabezadoTabla><td align=center><font size=20>PREPARACION MEDICAMENTOS</font></td></tr>";
	  echo "</table>";

	  echo "<br><br>";

	  //Seleccionar RONDA
	  echo "<center><table>";
      echo "<tr class=fila1><td align=center><font size=20>Seleccione Ronda : </font></td></tr>";
	  echo "</table>";

	  echo "<center><table>";
	  echo "<tr><td align=rigth><select name='whora_par_actual' size='1' style=' font-size:50px; font-family:Verdana, Arial, Helvetica, sans-serif; height:50px'>";
	  //On echo "<option>".$whora_par_anterior."</option>";
	  echo "<option selected>".$whora_par_actual."</option>";
	  //On echo "<option>".$whora_par_sigte."</option>";

	  //==========================================================================================================
	  //Si la hora actual esta entre las 6:00 PM y las 6:00 AM doy la posibilidad de que se pueda ver 2 Rondas mas
	  /*//On if (trim($whora_par_actual) >= 16 or trim($whora_par_actual) <= 4)
	     {
		  echo "<option>".($whora_par_actual+4)."</option>";
	      echo "<option>".($whora_par_actual+6)."</option>";
         }*/
      //==========================================================================================================

      for ($i=2; $i<= 24; $i=$i+2)
         {
	      if ($whora_par_actual != $i)
	         echo "<option>".$i."</option>";
         }

	  echo "</select></td></tr>";
	  echo "</table>";


	  echo "<br><br><br>";


	  //Seleccionar CENTRO DE COSTOS

	  //**************** llamada a la funcion consultaCentrosCostos y dibujarSelect************
					$cco="Ccohos,Ccourg";
					$sub="on";
					$tod="";
					$ipod="on";
					//$cco=" ";
					$centrosCostos = consultaCentrosCostos($cco);

					echo "<table align='center' border=0>";
					$dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod);

					echo $dib;
					echo "</table>";
     }

		//echo "<br>wcco ".$wcco;

  function elegir_historia($wzona)
     {
	  global $user;
	  global $conex;
	  global $wcenmez;
	  global $wafinidad;
	  global $wbasedato;
	  global $wtabcco;
	  global $winstitucion;
	  global $wactualiz;
	  global $wemp_pmla;

	  global $wcco;
	  global $wnomcco;

	  global $whab;
	  global $whis;
	  global $wing;
	  global $wpac;
	  global $wtid;                                      //Tipo documento paciente
	  global $wdpa;

	  global $whora_par_actual;
	  global $wfecha_actual;
	  global $wfecha;


	  $wcco1=explode("-",$wcco);
	  
	  if ($wzona == "")
	     $wzona = "%";


	  //Selecciono todos los pacientes del servicio seleccionado
	  $q = " SELECT habcod, habhis, habing, pacno1, pacno2, pacap1, pacap2 " //, pactid, pacced "
	      ."   FROM ".$wbasedato."_000020, ".$wbasedato."_000018, root_000036, root_000037 "
	      ."  WHERE habcco  = '".$wcco1[0]."'"
	      ."    AND habali != 'on' "            //Que no este para alistar
	      ."    AND habdis != 'on' "            //Que no este disponible, osea que este ocupada
	      // ."    AND habcod  = ubihac "		//2019-03-06. Se comenta está linea para que puedan salir los pacients de urgencias
	      ."    AND ubihis  = orihis "
	      ."    AND ubiing  = oriing "
	      ."    AND ubiald != 'on' "
	      ."    AND ubiptr != 'on' "
	      ."    AND ubisac  = '".$wcco1[0]."'"
	      ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
	      ."    AND oriced  = pacced "
		  ."    AND oritid  = pactid "
	      ."    AND habhis  = ubihis "
	      ."    AND habing  = ubiing "
		  ."    AND ( UPPER(habzon) LIKE '".$wzona."' "
		  ."	 OR habzon NOT IN( SELECT arecod FROM ".$wbasedato."_000169 WHERE areest = 'on' ) )" //2019-03-06. Se agregar filtro de zona
	      ."  GROUP BY 1,2,3,4,5,6,7 "
	      ."  ORDER BY Habord, Habcod ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res);

	  echo "<center><table>";

	  echo "<tr class=titulo>";
	  echo "<td colspan=5 align=center><font size=5><b>Servicio o Unidad: ".$wcco."</b></font></td>";
	  echo "</tr>";

	  echo "<tr class=encabezadoTabla>";
      echo "<th colspan=12 align=center><font size=4>Hora Aplicación: </font><font size=6 color='00FF00'>".$whora_par_actual.":00</font></th>";
      echo "</tr>";

	  echo "<tr class=encabezadoTabla>";
	  echo "<th><font size=4>Habitacion</font></th>";
	  echo "<th><font size=4>Historia</font></th>";
	  echo "<th><font size=4>Paciente</font></th>";
	  echo "<th><font size=4>Acción</font></th>";
	  echo "</tr>";

	  if ($num > 0)
	     {
		  $wclass="fila2";
		  for($i=1;$i<=$num;$i++)
			 {
			   $row = mysql_fetch_array($res);

			   $whab = $row[0];
			   $whis = $row[1];
			   $wing = $row[2];
			   $wpac = $row[3]." ".$row[4]." ".$row[5]." ".$row[6];
			   //$wtid = $row[7];                                      //Tipo documento paciente
			   //$wdpa = $row[8];                                      //Documento del paciente

			   query_articulos($whis, $wing, $wfecha, $res1);
			   $num1 = mysql_num_rows($res1);

			   $wreg[0]=0;
			   $wkardex_Actualizado="Hoy";

			   if ($num1==0)   //Si entra aca es porque NO hay articulos en fecha_actual para la RONDA indicada (hora_par_actual) o no hay Kardex generado
				  {
					//Verifico si NO hay Kardex en esta fecha
					$q = " SELECT COUNT(*) "
						."   FROM ".$wbasedato."_000053 "
						."  WHERE karhis = '".$whis."'"
						."    AND karing = '".$wing."'"
						."    AND fecha_data = '".$wfecha_actual."'"
						."    AND karcon = 'on' "
						."    AND karcco = '*' ";
					$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$wreg = mysql_fetch_array($res1);
				  }

			   if ($num1 == 0 and $wreg[0]==0)     //wreg[0]==0: Indica que NO hay kardex de la fecha_actual
				  {
					$dia = time()-(1*24*60*60);   //Te resta un dia. (2*24*60*60) te resta dos y //asi...
					$wayer = date('Y-m-d', $dia); //Formatea dia

					query_articulos($whis, $wing, $wayer, $res1);
					$num1 = mysql_num_rows($res1);

					$wkardex_Actualizado="Ayer";                       //Julio 21 de 2011
				  }

			   if ($num1 > 0)
				   {
				    if ($wclass=="fila1")
					  $wclass="fila2";
					 else
						$wclass="fila1";

					echo "<tr class=".$wclass.">";
					echo "<td align=center><font size=4><b>".$whab."</b></font></td>";
					echo "<td align=center><font size=4><b>".$whis."</b></font></td>";
					echo "<td align=left  ><font size=4><b>".$wpac."</b></font></td>";

					echo "<td align=center><A HREF='Preparacion_Medicamentos.php?wemp_pmla=".$wemp_pmla."&user=".$user."&whora_par_actual=".$whora_par_actual."&whis=".$whis."&wing=".$wing."&wcco=".$wcco."&whab=".$whab."&wpac=".$wpac."&wzona=".$wzona."' class=tipo3V>Ver</A></td>";

					if ($wkardex_Actualizado=="Ayer")
						echo "<td align=center bgcolor=FFFF99><font size=6><b> </b></font></td>";
					  else
						echo "<td align=center><font size=6><b> </b></font></td>";

					echo "<tr><td colspan=4>&nbsp;</td></tr>";
					echo "</tr>";
				   }
			 }
		  }
		 else
		    echo "NO HAY HABITACIONES OCUPADAS";
	  echo "</table>";
	 }


function obtenerVectorAplicacionMedicamentos($fechaActual, $fechaInicioSuministro, $horaInicioSuministro, $horasPeriodicidad)
   {
	$arrAplicacion = array();

	$horaPivote = 1;

	$caracterMarca = "*";

	$vecHoraInicioSuministro   = explode(":",$horaInicioSuministro);
	$vecFechaInicioSuministro  = explode("-",$fechaInicioSuministro);

	$vecFechaActual			   = explode("-",$fechaActual);

	$fechaActualGrafica 	= mktime($horaPivote, 0, 0, date($vecFechaActual[1]), date($vecFechaActual[2]), date($vecFechaActual[0]));
	$fechaSuministroGrafica = mktime(intval($vecHoraInicioSuministro[0]), 0, 0, date($vecFechaInicioSuministro[1]), date($vecFechaInicioSuministro[2]), date($vecFechaInicioSuministro[0]));

	$horasDiferenciaHoyFechaSuministro = ROUND(($fechaActualGrafica - $fechaSuministroGrafica)/(60*60));

	if($horasDiferenciaHoyFechaSuministro <= 0 && abs($horasDiferenciaHoyFechaSuministro) >= 24)
	  {
	   $caracterMarca = "";
	  }

	/************************************************************************************************************************************************
	 * Febrero 22 de 2011
	 ************************************************************************************************************************************************/
	if( date( "Y-m-d", $fechaActualGrafica+(24*3600) ) == date( "Y-m-d", $fechaSuministroGrafica ) && $vecHoraInicioSuministro[0] == "00" ){
		$caracterMarca = "";
	}
	/************************************************************************************************************************************************/

	if ($horasPeriodicidad <= 0)
	  {
	   $horasPeriodicidad = 1;
	  }

	$horaUltimaAplicacion = abs($horasDiferenciaHoyFechaSuministro) % $horasPeriodicidad;

	$cont1 = 1;   //Desplazamiento de 24 horas
	$cont2 = 0;   //Desplazamiento desde la hora inicial

	$inicio = false;	//Guia de marca de hora inicial

	if ($fechaActual == $fechaInicioSuministro)
	   {
		$cont1 = intval($vecHoraInicioSuministro[0]);
		$arrAplicacion[$cont1] = $caracterMarca;

		while($cont1 <= 24)
		  {
			$out = "-";
			if ($cont2 % $horasPeriodicidad == 0)
			   {
			    $out = $caracterMarca;
			   }
			$cont2++;

			$arrAplicacion[$cont1] = $out;
			$cont1++;
		  }
	   }
	  else
	    {
		 while ($cont1 <= 24)
		   {
		    $out = "-";
			//Hasta llegar a la aplicacion
			if ($cont1 == abs($horaPivote+$horasPeriodicidad-$horaUltimaAplicacion) || ($cont1==1 && $horaUltimaAplicacion == 0))
			  {
			   $out = $caracterMarca;
			   $inicio = true;
			  }

			if ($inicio)
			  {
			   if($cont2 % $horasPeriodicidad == 0)
			     {
				  $out = $caracterMarca;
				 }
			   $cont2++;
		      }
			$arrAplicacion[$cont1] = $out;
			$cont1++;
		   }
	    }
	return $arrAplicacion;
   }


  //Se pregunta por cada medicamento si ya fue presionado el boton de APLICAR, para llevarlo a una variable unica de Aplicados y poderla enviar en el HREF
  function aplicados($num)
    {
	 global $waplicados;
	 global $wapl;

	 for ($i=1;$i<=$num;$i++)
	   {
		if (isset($wapl[$i]))
		   $waplicados=$waplicados."&wapl[".$i."]=".$wapl[$i];
	   }
	}


  //Se consigue la hora PAR anterior a la hora actual(si es hora impar) si no se deja la hora PAR actual
  function hora_par()
    {
	 global $whora_par_actual;
	 global $whora_par_anterior;
	 global $wfecha;


	 $whora_Actual=date("H");
	 $whora_Act=($whora_Actual/2);

	 if (!is_int($whora_Act))   //Si no es par le resto una hora
	    {
	     $whora_par_actual=$whora_Actual-1;
	    }
	   else
	     {
		  if ($whora_Actual=="00" or $whora_Actual=="0")    //Esto se hace porque el Kardex o el metodo que calcula las horas de aplicacion reconoce es las 24 horas y no las 00
	         $whora_par_actual="24";
		    else
		       $whora_par_actual=$whora_Actual;
	     }

	  if ($whora_Actual=="02" or $whora_Actual=="2")        //Esto se hace porque el Kardex o el metodo que calcula las horas de aplicacion reconoce es las 24 horas y no las 00
	     $whora_par_anterior="24";
	    else
	       $whora_par_anterior = $whora_par_actual-2;
	}


  function buscoSiYaFueAplicado($whis, $wing, $wart, $wcco, $wdosis, $whora_par_actual)
    {
	 global $user;
	 global $conex;
	 global $wcenmez;
	 global $wafinidad;
	 global $wbasedato;
	 global $wtabcco;
	 global $winstitucion;
	 global $wactualiz;
	 global $wemp_pmla;
	 global $wfecha;


	 $wlarghor = strlen($whora_par_actual);

	 if ($wlarghor == 2)
	    {
		 $q = " SELECT COUNT(*) "
		     ."   FROM ".$wbasedato."_000015 "
		     ."  WHERE aplhis          = '".$whis."'"
		     ."    AND apling          = '".$wing."'"
		     ."    AND fecha_data      = '".$wfecha."'"
		     ."    AND mid(aplron,1,2) = '".$whora_par_actual."'"
		     ."    AND aplart          = '".$wart."'"
		     ."    AND aplest          = 'on' ";
	    }
	   else
	      {
		   $q = " SELECT COUNT(*) "
		       ."   FROM ".$wbasedato."_000015 "
		       ."  WHERE aplhis          = '".$whis."'"
		       ."    AND apling          = '".$wing."'"
		       ."    AND fecha_data      = '".$wfecha."'"
		       ."    AND mid(aplron,1,1) = '".$whora_par_actual."'"
		       ."    AND aplart          = '".$wart."'"
		       ."    AND aplest          = 'on' ";
	      }
	 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 $row = mysql_fetch_array($res);

	 if ($row[0] > 0)
	    return true;
	   else
	      return false;
    }

  function esANecesidad($wcond)
    {
	 global $user;
	 global $conex;
	 global $wbasedato;
	 global $wfecha;

	 $q = " SELECT contip "
	     ."   FROM ".$wbasedato."_000042 "      //Tabla condiciones de administracion
	     ."  WHERE concod = '".$wcond."'";
	 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 $row = mysql_fetch_array($res);

	 if ($row[0] == "AN")      //'AN' : significa que es A NECESIDAD y es un tipo
	    return true;
       else
	      return false;
    }

  function buscarSiEstaSuspendido($whis, $wing, $wart, $whora)
    {
	 global $user;
	 global $conex;
	 global $wbasedato;
	 global $wfecha;


	 $q = " SELECT COUNT(*)  "
	     ."   FROM ".$wbasedato."_000055 A "
	     ."  WHERE kauhis  = '".$whis."'"
	     ."    AND kauing  = '".$wing."'"
	     ."    AND kaufec  = '".$wfecha."'"
	     ."    AND kaudes  = '".$wart."'"
	     ."    AND kaumen  = 'Articulo suspendido' "
	     ."    AND mid(hora_data,1,2) >= '".$whora."'";   //Si la hora de suspención es mayor o igual a la hora PAR de la RONDA actual, se puede aplicar
	 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 $row = mysql_fetch_array($res);

	 if ($row[0] > 0)
	    return false;  //Indica que el articulo fue suspendido hace menos de dos horas, es decir que se puede aplicar, asi este suspendido
	   else
	      return true; //Indica que fue Suspendido hace mas de dos horas
	}


  function validar_aplicacion($whis, $wing, $wart, $wcco, $wdosis)
    {
	 global $user;
	 global $conex;
	 global $wcenmez;
	 global $wafinidad;
	 global $wbasedato;
	 global $wtabcco;
	 global $winstitucion;
	 global $wactualiz;
	 global $wemp_pmla;

	 global $wmensaje;

	 $wsalart = 0;
	 $wsalsre = 0;


	 //Traigo el saldo del articulo en el Cco
	 $q = " SELECT SUM(spauen-spausa) "
	     ."   FROM ".$wbasedato."_000004, ".$wbasedato."_000011 "
	     ."  WHERE spahis  = '".$whis."'"
	     ."    AND spaing  = '".$wing."'"
	     ."    AND spaart  = '".$wart."'"
	     ."    AND (spacco = '".$wcco."'"
	     ."     OR (spacco  = ccocod "
	     ."    AND  ccotra  = 'on' "
	     //."    AND  ccoima != 'on' "
	     ."    AND  ccofac  = 'on')) ";
	 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 $row = mysql_fetch_array($res);

	 if ($row[0] > 0)
	    {
		 $wsalart=$row[0];   //Saldo del articulo

		 //Busco si el articulo tiene equivalencias o fracciones en la tabla 000059
		 $q = " SELECT deffra "
		     ."   FROM ".$wbasedato."_000059, ".$wbasedato."_000011 "
		     ."  WHERE  defart  = '".$wart."'"
		     ."    AND (defcco  = '".$wcco."'"
	         ."     OR (defcco  = ccocod "
	         ."    AND  ccotra  = 'on' "
	         //."    AND  ccoima != 'on' "
	         ."    AND  ccofac  = 'on')) ";
	     $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	     $row = mysql_fetch_array($res);

	     $wartfra=$row[0];   //Fracción del articulo

	     if ($wartfra > 0)
	        $wsalart=$wsalart*$wartfra;
	    }
	   else
	      {
		   $wsalart=0;
		   $wartfra=0;
	      }

	 //Traigo la cantidad SIN RECIBIR de este articulo
	 $q = " SELECT COUNT(*) "
	 	 ."   FROM ( SELECT a.*"
			      ."   FROM ".$wbasedato."_000002 a, ".$wbasedato."_000003 "
			      ."  WHERE fenhis = '".$whis."'"
			      ."    AND fening = '".$wing."'"
			      ."    AND fencco = '".$wcco."'"
			      ."    AND fennum = fdenum "
			      ."    AND fdeart = '".$wart."'"
			      ."    AND fdedis = 'on'"
			      ."  UNION "
			      ." SELECT c.* "
			      ."   FROM ".$wbasedato."_000002 c, ".$wbasedato."_000143 "
			      ."  WHERE fenhis = '".$whis."'"
			      ."    AND fening = '".$wing."'"
			      ."    AND fencco = '".$wcco."'"
			      ."    AND fennum = fdenum "
			      ."    AND fdeart = '".$wart."'"
			      ."    AND fdedis = 'on' ) a ";
	 $res = mysql_query($q,$conex);
     $row = mysql_fetch_array($res);

     $wsalsre = $row[0];  //Cantidad SIN RECIBIR


     if ($wsalart > $wsalsre)                   //Saldo del articulo es mayor a la cantidad que falta por recibir PUEDE APLICARSE maximo la diferencia
	     {
		  if (($wsalart-$wsalsre) >= $wdosis)   //Si la diferencia es mayor a igual a la dosis PUEDE APLICARSE
		     {
			  //On
	          //echo "Articulo 1111: ".$wart." Saldo: ".$wsalart." Sin Recibir: ".$wsalsre." Dosis: ".$wdosis." Fraccion: ".$wartfra." mensaje: ".$wmensaje."<br>";

		      return true;
	         }
		    else
		       {
		        if ($wsalsre > 0)
		           $wmensaje = "La DOSIS a aplicarse es mayor al Saldo disponible para el paciente y tiene pendiente de recibir DOSIS en el carro";
		          else
		             $wmensaje = "No hay saldo suficiente para aplicar";

		        //On
	            //echo "Articulo 2222: ".$wart." Saldo: ".$wsalart." Sin Recibir: ".$wsalsre." Dosis: ".$wdosis." Fraccion: ".$wartfra." mensaje: ".$wmensaje."<br>";

		        return false;
	           }
	     }
	    else
	       {
		    if ($wsalsre > 0)
	           $wmensaje = "La DOSIS a aplicarse es mayor al Saldo disponible para el paciente y tiene pendiente de recibir DOSIS en el carro";
	          else
	             $wmensaje = "No hay saldo suficiente para aplicar";

	        //On
	        //echo "Articulo 3333: ".$wart." Saldo: ".$wsalart." Sin Recibir: ".$wsalsre." Dosis: ".$wdosis." Fraccion: ".$wartfra." mensaje: ".$wmensaje."<br>";

		    return false;
           }
    }

  function query_articulos1($whis, $wing, $wfecha, &$res)
     {
	  global $conex;
	  global $wbasedato;
	  global $wcenmez;
	  global $wemp_pmla;

      //Traigo los Kardex GENERADOS con articulos de DISPENSACION
	  $q = " SELECT A.fecha_data, kadart, artcom, kaduma, kadfin, substr(kadhin,1,2), perequ, Kadcfr, Kadufr, Kadsus, Kadcnd, Kadvia, Kadcma, Kadobs, Kadess, Kadare, Kadlev, Kaddma, Kadido "
	      ."   FROM ".$wbasedato."_000054 A, ".$wbasedato."_000026 B, ".$wbasedato."_000043 C,".$wbasedato."_000053 D,".$wbasedato."_000011 E "
	      ."  WHERE kadhis  = '".$whis."'"
	      ."    AND kading  = '".$wing."'"
	      ."    AND kadfec  = '".$wfecha."'"
	      ."    AND kadest  = 'on' "
	      ."    AND kadart  = artcod "
	      ."    AND kadori  = 'SF' "
	      ."    AND kadper  = percod "
	      ."    AND kadhis  = karhis "
	      ."    AND kading  = karing "
	      // ."    AND karcon  = 'on' "
	      //."    AND karcco  = kadcco "
	      //."    AND kadare  = 'on' "
	      ."    AND ((karcco  = ccocod "
          ."    AND   ccolac != 'on') "
          ."     OR   karcco  = '*') "
          ."    AND D.fecha_data = kadfec "
	      ." UNION  "
	      //Traigo los Kardex GENERADOS con articulos de CENTRAL DE MEZCLAS
	      ." SELECT A.fecha_data, kadart, artcom, kaduma, kadfin, substr(kadhin,1,2), perequ, Kadcfr, Kadufr, Kadsus, Kadcnd, Kadvia, Kadcma, Kadobs, Kadess, Kadare, Kadlev, Kaddma, Kadido  "
	      ."   FROM ".$wbasedato."_000054 A, ".$wcenmez."_000002 B, ".$wbasedato."_000043 C,".$wbasedato."_000053 D,".$wbasedato."_000011 E "
	      ."  WHERE kadhis  = '".$whis."'"
	      ."    AND kading  = '".$wing."'"
	      ."    AND kadfec  = '".$wfecha."'"
	      ."    AND kadest  = 'on' "
	      ."    AND kadart  = artcod "
	      ."    AND kadori  = 'CM' "
	      ."    AND kadper  = percod "
	      ."    AND kadhis  = karhis "
	      ."    AND kading  = karing "
	      // ."    AND karcon  = 'on' "
	      //."    AND karcco  = kadcco "
	      //."    AND kadare  = 'on' "
	      ."    AND ((karcco  = ccocod "
          ."    AND   ccolac != 'on') "
          ."     OR   karcco  = '*') "
          ."    AND D.fecha_data = kadfec "
	      ." UNION  "
	      //Traigo los Kardex en TEMPORAL (000060) con articulos de DISPENSACION
	      ." SELECT A.fecha_data, kadart, artcom, kaduma, kadfin, substr(kadhin,1,2), perequ, Kadcfr, Kadufr, Kadsus, Kadcnd, Kadvia, Kadcma, Kadobs, Kadess, Kadare, Kadlev, Kaddma, Kadido  "
	      ."   FROM ".$wbasedato."_000060 A, ".$wbasedato."_000026 B, ".$wbasedato."_000043 C,".$wbasedato."_000053 D,".$wbasedato."_000011 E "
	      ."  WHERE kadhis  = '".$whis."'"
	      ."    AND kading  = '".$wing."'"
	      ."    AND kadfec  = '".$wfecha."'"
	      ."    AND kadest  = 'on' "
	      ."    AND kadart  = artcod "
	      ."    AND kadori  = 'SF' "
	      ."    AND kadper  = percod "
	      ."    AND kadhis  = karhis "
	      ."    AND kading  = karing "
	      // ."    AND karcon  = 'on' "
	      //."    AND karcco  = kadcco "
	      //."    AND kadare  = 'on' "
	      ."    AND ((karcco  = ccocod "
          ."    AND   ccolac != 'on') "
          ."     OR   karcco  = '*') "
          ."    AND D.fecha_data = kadfec "
	      ." UNION  "
	      //Traigo los Kardex en TEMPORAL (000060) con articulos de CENTRAL DE MEZCLAS
	      ." SELECT A.fecha_data, kadart, artcom, kaduma, kadfin, substr(kadhin,1,2), perequ, Kadcfr, Kadufr, Kadsus, Kadcnd, Kadvia, Kadcma, Kadobs, Kadess, Kadare, Kadlev, Kaddma, Kadido  "
	      ."   FROM ".$wbasedato."_000060 A, ".$wcenmez."_000002 B, ".$wbasedato."_000043 C,".$wbasedato."_000053 D,".$wbasedato."_000011 E "
	      ."  WHERE kadhis  = '".$whis."'"
	      ."    AND kading  = '".$wing."'"
	      ."    AND kadfec  = '".$wfecha."'"
	      ."    AND kadest  = 'on' "
	      ."    AND kadart  = artcod "
	      ."    AND kadori  = 'CM' "
	      ."    AND kadper  = percod "
	      ."    AND kadhis  = karhis "
	      ."    AND kading  = karing "
	      // ."    AND karcon  = 'on' "
	      //."    AND karcco  = kadcco "
	      //."    AND kadare  = 'on' "
	      ."    AND ((karcco  = ccocod "
          ."    AND   ccolac != 'on') "
          ."     OR   karcco  = '*') "
          ."    AND D.fecha_data = kadfec "
	      ."  ORDER BY 6 ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	  //On
	  //echo $q."<br>";
	}

  //===========================================================================================================================================
  //*******************************************************************************************************************************************



  //===========================================================================================================================================
  //===========================================================================================================================================
  // P R I N C I P A L
  //===========================================================================================================================================
  //===========================================================================================================================================
  echo "<form name='preparacion' action='Preparacion_Medicamentos.php' method=post>";

  $wfecha = date("Y-m-d");
  $whora  = (string)date("H:i:s");


  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";

  mostrar_empresa($wemp_pmla);

  if (!isset($wcco))
     {
      hora_par();
      elegir_centro_de_costo();
     }
	else
       {
	    echo "<input type='HIDDEN' name='wcco' VALUE='".$wcco."'>";
	    echo "<input type='HIDDEN' name='whora_par_actual' VALUE='".$whora_par_actual."'>";

	    if (isset($whis))
	       {
		    echo "<input type='HIDDEN' name='whis' value='".$whis."'>";
		    echo "<input type='HIDDEN' name='wing' value='".$wing."'>";
		    echo "<input type='HIDDEN' name='whab' value='".$whab."'>";
		    echo "<input type='HIDDEN' name='wpac' value='".$wpac."'>";

		    query_articulos1($whis, $wing, $wfecha, $res);
		    $num = mysql_num_rows($res);

			$wfec=$wfecha;

		    if ($num == 0)  //Si no se encuentra Kardex Confirmado en la fecha actual, traigo kardex del dia anterior
		       {
			    $dia = time()-(1*24*60*60); //Te resta un dia (2*24*60*60) te resta dos y //asi...

			    //$dia_fin = date('d-m-Y', $dia); //Formatea dia
				$wayer = date('Y-m-d', $dia); //Formatea dia

				query_articulos1($whis, $wing, $wayer, $res);
				$num = mysql_num_rows($res);

				$wfec=$wayer;
			   }


	        echo "<center><table>";
		    echo "<tr class=encabezadoTabla>";
		    echo "<th><font size=6>Habitación: "."</font><font size=9 color='00FF00'>".$whab."</font><font size=6>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Historía: ".$whis."</font></th>";
		    echo "</tr>";
		    echo "<tr class=encabezadoTabla>";
		    echo "<th><font size=6>Paciente: ".$wpac."</font></th>";
		    echo "</tr>";
		    echo "<tr class=encabezadoTabla>";
		    echo "<th><font size=6>Hora Aplicación: </font><font size=6 color='00FF00'>".$whora_par_actual.":00</font></th>";
		    echo "</tr>";
		    echo "</table>";

		    echo "<br>";

			//=====================================================================================================================
			//Marzo 5 de 2012
		    //=====================================================================================================================
			//Averiguo si esta en proceso de Alta
			$q = " SELECT COUNT(*) "
			    ."   FROM ".$wbasedato."_000018 "
			    ."  WHERE ubihis  = '".$whis."'"
			    ."    AND ubiing  = '".$wing."'"
				."    AND ubialp  = 'on' "
				."    AND ubiald != 'on' ";
			$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row1 = mysql_fetch_array($res1);

			if (trim($row1[0]) > 0)
			   {
				echo "<center><table>";
				echo "<tr>";
				echo "<th bgcolor='FFCC66'><font size=6><b><blink id=blink>EN PROCESO DE ALTA</blink></b></font></th>";
				echo "</tr>";
				echo "</table>";
		       }
			//====================================================================================================================
			echo "<br>";

		    //=====================================================================================================================
			//Noviembre 26 de 2010
		    //=====================================================================================================================
			// //Traigo el Diagnostico
			// $q = " SELECT kardia, karale "
			    // ."   FROM ".$wbasedato."_000053, ".$wbasedato."_000011 "
			    // ."  WHERE   karhis  = '".$whis."'"
			    // ."    AND   karing  = '".$wing."'"
			    // ."    AND ((karcco  = ccocod "
		        // ."    AND   ccolac != 'on') "
		        // ."     OR   karcco  = '*') "
		        // ."    AND   karcon  = 'on' ";
			// $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			// $row1 = mysql_fetch_array($res1);
			// $num1 = mysql_num_rows($res);

			// if (trim($row1[0]) != "" and trim($row1[1]) != "")
			  // {
				// echo "<center><table>";
				// echo "<tr><td bgcolor='FFFF99'><font size=4><b>Diagnostico  : </b>".$row1[0]."</font></td></tr>";    //Diagnostico
				// echo "<tr><td bgcolor='FFFF99'><font size=4><b>Alertas  : </b>".$row1[1]."</font></td></tr>";        //Alertas
				// echo "</table>";
		       // }

			// Consultar alertas en movhos_000220
			$alergiasAnteriores = consultarAlergiaAlertas($whis, $wing);
			
			// Consultar los diagnosticos actuales del paciente
			$diagnosticos = consultarDiagnosticoPaciente($conex,$wbasedato,$whis,$wing,false);
			
			
			echo "<center><table>";
			if (trim($row1[0]) != "")
			{
				// echo "<tr><td bgcolor='FFFF99'><font size=4><b>Diagnostico  : </b>".$row1[0]."</font></td></tr>";    //Diagnostico
				echo "<tr><td bgcolor='FFFF99'><font size=4><b>Diagnostico  :<br>  </b>".str_replace( "\n","<br>", $diagnosticos)."</font></td></tr>";    //Diagnostico
			}
			if (trim($alergiasAnteriores) != "")
			{
				// echo "<tr><td bgcolor='FFFF99'><font size=4><b>Alertas  : </b>".$alergiasAnteriores."</font></td></tr>";        //Alertas
				echo "<tr><td bgcolor='FFFF99'><font size=4><b>Alertas  :<br> </b>".str_replace( "\n","<br>", $alergiasAnteriores )."</font></td></tr>";        //Alertas
			}			
			echo "</table>";
			
			   
			//====================================================================================================================
			echo "<br>";

			echo "<center><table>";
		    echo "<tr class=encabezadoTabla>";
		    echo "<th colspan=2><font size=5>Medicamento</font></th>";
		    echo "<th><font size=5>Dosis</font></th>";
		    echo "<th><font size=5>Vía</font></th>";
		    echo "<th colspan=2><font size=5>Condición</font></th>";
		    //echo "<th><font size=5>Aprobado<br>regente<br>el ".$wfec."</font></th>";
			echo "<th><font size=5>Frecuencia</font></th>";

		    $waplicados="";
		    aplicados($num);   //Por cada submit hago esto, es decir, llevo a una variable todos los medicamentos aplicados para cuando regrese el submit

		    $j=1;
		    for ($i=1;$i<=$num;$i++)   //Recorre cada uno de los medicamentos
		        {
		         $row = mysql_fetch_array($res);
				 
				 $esArticuloAyudaDx = false;
				 if( empty( $ccoayuda ) ){
					$esArticuloAyudaDx = esArticuloDeAyudaDiagnostica( $conex, $wbasedato, $whis, $wing, $wfec, $row['kadart'], $row['Kadido'] );
				 }
				 
				 if( $esArticuloAyudaDx )
					 continue;
				 
				 /************************************************************************************************************
				  * Agosto 30 de 2016
				  * En caso de tener dosis máxima se verifica que no se halla cumplido todas las dosis para que pueda mostrarse
				  * el medicamento. En caso de que se cumpla todas las dosis no se muestra el articulo
				  ************************************************************************************************************/
				 if( trim( $row['Kaddma'] ) != '' ){
					$totalAplicaciones = consultarTotalAplicacionesEfectivasInc( $conex, $wbasedato, $whis, $wing, $row['kadart'], 0, strtotime( date( "Y-m-d ".$whora_par_actual.":00:00") )-2*3600, $row['Kadido'] );
					if( $row['Kaddma'] - $totalAplicaciones <= 0 ){
						continue;
					}
					//Dejo esto comentado por si más adelante se necesita
					//Estos calculos determinan cuando se acaba un medicamento de acuerdo a la última fecha de aplicación
					// $totalAplicaciones = consultarTotalAplicacionesEfectivasInc( $conex, $wbasedato, $whis, $wing, $row['kadart'], 0, time(), $row['Kadido'] );
					// $aplicacionesFaltantes = $row['Kaddma'] - $totalAplicaciones;
					// //Si las aplicaciones son mayores a 0 resto uno por que se tiene en cuenta la hora de inicio
					// if( $aplicacionesFaltantes > 0 ) $aplicacionesFaltantes--;
					
					// //Busco la última aplicacion Esta en formato Unix
					// $ultimaAplicacion = consultarUltimaAplicacion( $conex, $wbasedato, $whis, $wing, $row['kadart'], $row['Kadido'] );
					// $ultimaAplicacion = max( $ultimaAplicacion, strtotime( $row['kadfin']." ".$row[5].":00:00" ) );
					// $terminacionAdministracion = $ultimaAplicacion + $row['perequ']*3600*($aplicacionesFaltantes);
					
					// if( $terminacionAdministracion < strtotime( date( "Y-m-d ".$whora_par_actual.":00:00") ) ){
						// continue;
					// }
					
					//Si ya se termino la aplicación del medicamento no se muestra
				 }
				 /************************************************************************************************************/
				 
				 /**************************************************************************************************************
				  * Julio 12 de 2016
				  **************************************************************************************************************/
				 //No se muestran los articulos genéricos de articulos ordenados por Lev o IC
				 if( $row['Kadlev'] == 'on' && esArticuloGenerico( $row['kadart'] ) ){
					continue;
				 }
				 
				 if( !empty($row['Kadobs']) ){
					 //Dejo las observaciones mostrando lo último ingresado
					 $row['Kadobs'] = explode( "<div>", $row['Kadobs'] );
					 $row['Kadobs'] = explode( "</div>", $row['Kadobs'][0] );
					 $row[13] = $row['Kadobs'][0]."</div>";
				 }
				 /**************************************************************************************************************/

		         //                              .                  Fecha grabacion Articulo, fecha Inicio aplicacion, hora inicio aplicacion, frecuencia
			     $arrAplicacion = obtenerVectorAplicacionMedicamentos($wfecha,                      $row[4],                $row[5],           $row[6]);
				 $horaArranque = 0;

				 $cont1 = 1;
				 $caracterMarca = "*";

				 while($cont1 <= 24)
				  {
					$wok=false;        //Indicador para verificar si el articulo se puede aplicar o no, según la validaciones de la función 'validar_aplicacion()'
					$waplicado="off";

					//========================================================================================================================================================================
					$wanecesidad=false;
					$wcond="";

					//Pregunto si es una condicion a NECESIDAD
					if ($row[10]!="")  //Indica que puede ser un medicamento a Necesidad
					   {
						//Traigo la descripcion de la CONDICION
						$q = " SELECT condes "
						    ."   FROM ".$wbasedato."_000042 "
						    ."  WHERE concod = '".$row[10]."'";
						$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						$row1 = mysql_fetch_array($res1);

						$wcond=$row1[0];   //Descripcion de la condicion

						//           Condición
					    $wanecesidad=esANecesidad($row[10]);
					    $wok=true;   //Indica que si esta validada la aplicación, osea que se puede aplicar
				       }
					//=======================================================================================================================================================================

					$wcco1=explode("-",$wcco);

					//Explicación del IF ****************************************************************************************************************************************************
					//Entra a este if cuando : Cuando el ('arrAplicacion[$cont1]' esta setiado desde la funcion 'obtenerVectorAplicacionMedicamentos' y esa posición del arreglo
					//tiene el caracter '*' y cont1 corresponda con la hora de aplicación actual) O (cuando el articulo sea 'a necesidad' y el contador este en la hora de aplicación actual)
					if ((isset($arrAplicacion[$cont1]) && $arrAplicacion[$cont1] == $caracterMarca and $cont1==$whora_par_actual) or ($wanecesidad and $cont1==$whora_par_actual))
					   {
						if (is_integer($j/2))
			               $wclass="fila1";
			              else
			                 $wclass="fila2";
			            $j++;

			            $wsuspendido=false;
			            if ($row[9]=="on")   //Si esta suspendido verifico que no halla sido dentro la ronda actual
			               {
			                                                 // Hist   Ing    Articulo, hora
			                $wsuspendido=buscarSiEstaSuspendido($whis, $wing, $row[1],  $whora_par_actual);
		                   }

		                if (!$wsuspendido)   //No esta suspendido
			               {
							echo "<tr class=".$wclass.">";
							echo "<td><font size=5>".$row[1]."</font></td>";                           //Codigo Medicamento
							echo "<td><font size=6>".$row[2]."</font></td>";                           //Nombre Medicamento
							echo "<td align=right bgcolor='FFFF99'><font size=6>".$row[7]."  ".$row[8]."</font></td>";  //Dosis

							if (!$wanecesidad)   //Entra si No es a necesidad
						       {
								//                                     CodMed   Cco            Frecuencia  Hora
								if (buscoSiYaFueAplicado($whis, $wing, $row[1], trim($wcco1[0]), $row[7], $whora_par_actual))
								   {
									$waplicado="on";
								   }
							      else
							        {
								     //                                      CodMed   Cco              Frecuencia
								     $wok = validar_aplicacion($whis, $wing, $row[1], trim($wcco1[0]), $row[7]);
							        }
							   }

							//Traigo la descripcion de la VIA
							$q = " SELECT viades "
							    ."   FROM ".$wbasedato."_000040 "
							    ."  WHERE viacod = '".$row[11]."'";
							$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
 							$row1 = mysql_fetch_array($res1);

     						$wvia    = $row1[0];         //Descripcion de la condicion

							$wenviar = "";
                            if ($row[14] == "on")        //Enviar
							   $wenviar="No enviar";

                            $waprobado = "No";
                            if ($row[15] == "on")        //Aprobado
							   $waprobado="Si";

							echo "<td align=center><font size=5>".$wvia."</font></td>";
							echo "<td align=center><font size=5>".$wcond."</font></td>";
							echo "<td align=center><font size=5>".$wenviar."</font></td>";
							//echo "<td align=center><font size=5>".$waprobado."</font></td>";
							echo "<td align=center><font size=5>CADA ".$row[6]." HORAS</font></td>";

							echo "</tr>";
							echo "<tr class=".$wclass."><td colspan=8 bgcolor='FFFF99'><font size=5>".$row[13]."</font></td></tr>";   //Observaciones


							echo "<tr><td colspan=4>&nbsp;</td></tr>";
							echo "<tr><td colspan=4>&nbsp;</td></tr>";
					       }
					   }
					  else
					     {
						  if (isset($arrAplicacion[$cont1]) && $arrAplicacion[$cont1] == $caracterMarca and $cont1 < $whora_par_actual)
						     {
							  if (!buscoSiYaFueAplicado($whis, $wing, $row[1], trim($wcco1[0]), $row[7], $whora_par_actual))
								 {
								  $waplicado="on";
								 }
							 }
						 }
					$cont1++;
				  }
			    }
		     echo "</table>";

		    echo "<br><br>";
		    echo "<table>";
		    echo "<tr><td><A HREF='Preparacion_Medicamentos.php?wemp_pmla=".$wemp_pmla."&user=".$user."&wcco=".$wcco."&whora_par_actual=".$whora_par_actual."&wzona=".$wzona."' class=tipo4V>Retornar</A></td></tr>";
		    echo "</table>";
		   }
	      else
	         {
				 //Mayo 26 de 2011
				if (CcoTieneZonas())
				{
					if (!isset($wzona) or trim($wzona) == "")
					{
						$wzona=strtoupper(SeleccionarZona());
						echo "<br><br><br><br>";
					}
					else{
						elegir_historia($wzona);
					}
				}
				else{
					elegir_historia("");
				}

		      echo "<br><br>";
		      echo "<table>";
		      echo "<tr><td><A HREF='Preparacion_Medicamentos.php?wemp_pmla=".$wemp_pmla."&user=".$user."&whora_par_actual=".$whora_par_actual."' class=tipo4V>Retornar</A></td></tr>";
		      echo "</table>";
		     }
       }

  echo "<br><br><br>";
  echo "<table>";
  echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
  echo "</table>";

} // if de register

?>
</body>
