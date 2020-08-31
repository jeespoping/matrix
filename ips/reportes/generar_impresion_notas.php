<html>
<head>
<title>IMPRESIÓN DE RECIBOS Y NOTAS POR FECHAS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<style type="text/css">
h1.SaltoDePagina
{
	PAGE-BREAK-AFTER: always
}
</style>
<script type="text/javascript">
  //Redirecciona a la pagina inicial
	function inicio()
	{
		document.location.href='generar_impresion_notas.php?wemp_pmla='+document.forms.consultar.wemp_pmla.value+'&wfecini='+document.forms.consultar.wfecini.value+'&wfecfin='+document.forms.consultar.wfecfin.value;
	}

	// Abre la venta de impresión
	function imprimir()
	{
		//var newWindow = window.open(this.getAttribute('href'), '_blank');
		url = 'generar_impresion_notas.php?wemp_pmla='+document.forms.consultar.wemp_pmla.value+'&wfecini='+document.forms.consultar.wfecini.value+'&wfecfin='+document.forms.consultar.wfecfin.value+'&wimp=1&wbasedato='+document.forms.consultar.wbasedato.value;
		blankWin = window.open(url,'_blank','menubar=yes,toolbar=yes,location=yes,directories=yes,fullscreen=no,titlebar=yes,hotkeys=yes,status=yes,scrollbars=yes,resizable=yes');
		blankWin.focus();
		return false;
	}

	// Validación del formulario
	function valida_enviar(form)
	{

		var fecini = form.wfecini.value;
		var fecfin = form.wfecfin.value;

		//Valida que la fecha final sea mayor o igual a la incial
		if(!esFechaMenorIgual(fecini,fecfin))
		{
		   alert("La fecha inicial no puede ser mayor que la fecha final");
		   form.wfecini.focus();
		   return false;
		}

		form.submit();
	}

</script>

</head>
<body>
<?php
include_once("conex.php");
  /**************************************************************************************
   *     PROGRAMA PARA CONSULTA E IMPRESIÓN DE RECIBOS Y NOTAS SEGÚN RANGO DE FECHAS    *
   *************************************************************************************/

	/*--------------------------------------------------------------------------
	| DEDSCRIPCIÓN: Consulta los recibos y notas entre las fechas dadas y las	|
	| muestra en formato de impresión con corte de página por cada una de   	|
	| modo que se puedan imprimir todas a la vez 								|
	| AUTOR: John Mario Cadavid García											|
	| FECHA DE CREACIÓN: Marzo 17 de 2011										|
	| FECHA DE ACTUALIZACIÓN: 													|
	----------------------------------------------------------------------------*/

	/*--------------------------------------------------------------------------
	| ACTUALIZACIONES															|
	|---------------------------------------------------------------------------|
	| FECHA:  																	|
	| AUTOR: 																	|
	| DESCRIPCIÓN:																|
	----------------------------------------------------------------------------*/

include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="17 de Marzo de 2011";

//Validación de usuario
$usuarioValidado = true;
if (!isset($user) || !isset($_SESSION['user'])){
	$usuarioValidado = false;
}else {
	if (strpos($user, "-") > 0)
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}
if(empty($wuser) || $wuser == ""){
	$usuarioValidado = false;
}

session_start();

if(!isset($wimp))
	$wimp = 0;

if ($wimp==0)
{
	//Llamo a la función para formar el encabezado del reporte llevándole Título, Fecha e Imagen o logo
	encabezado("IMPRESIÓN DE RECIBOS Y NOTAS POR FECHAS",$wactualiz,"clinica");
}

if (!isset($wres))
	$wres = 0;

//Si el usuario no es válido se informa y no se abre el reporte
if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
} // Fin IF si el usuario no es válido
else //Si el usuario es válido comenzamos con el reporte
{  //Inicio ELSE reporte

 //Conexion base de datos




 // Consulto los datos de la empresa actual y los asigno a la variable $empresa
 $consulta = consultarInstitucionPorCodigo($conex, $wemp_pmla);
 $empresa = $consulta->baseDeDatos;

  echo "<form name='consultar' action='generar_impresion_notas.php' method=post onSubmit='return valida_enviar(this);'>";


    /////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////
    //Aca traigo las variables necesarias de la empresa
	$q = " SELECT empdes, emphos "
	    ."   FROM root_000050 "
	    ."  WHERE empcod = '".$wemp_pmla."'"
	    ."    AND empest = 'on' ";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);

	$wnominst=$row[0];
	$whosp=$row[1];

	/////////////////////////////////////////////////////////////////////////////////////////
	//Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos
	$q = " SELECT detapl, detval, empdes, empbda, emphos "
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

	      $wbasedato=strtolower($row[3]);   //Base de dato de la empresa
	      $wemphos=$row[4];     //Indica si la facturacion es Hospitalaria o POS

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
	/////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////////////////////

  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";


  /*------------------------------------
  | 		INICIO DEL PROGRAMA   		|
   ------------------------------------*/

    // Si $wimp es 0 no muestra formato de impresión e incia la consulta
	if ($wimp==0)
	{

		// Si $wres es 0 no muestra resultado de consulta e incia formulario de consulta
		if ($wres==0)
	     {
	      // Verifica si hay fechas de consulta previa para cargarla en el formulario
		  if(!isset($wfecini) || !isset($wfecfin))
		  {
			$wfecini = date("Y-m-d");
			$wfecfin = date("Y-m-d");
		  }

		  // Inica formulario de consulta ////////////////
		  echo "<br>";
		  echo "<div align='center'><b>Seleccione las fechas a consultar</b></div>";
		  echo "<br>";
		  echo "<center><table border='0' cellspacing='4'>";
		  echo "<tr>";
		  echo "<td align=left class='fila1' colspan=1> &nbsp; <b>Fecha inicial:</b> &nbsp; </td>";
		  echo "<td align=left class='fila2' colspan=1> &nbsp; ";
		  campoFechaDefecto("wfecini", $wfecini);
		  echo " &nbsp; </td>";
		  echo "</tr>";
		  echo "<tr>";
		  echo "<td align=left class='fila1' colspan=1> &nbsp; <b> Fecha final:</b> &nbsp; </td>";
		  echo "<td align=left class='fila2' colspan=1> &nbsp; ";
		  campoFechaDefecto("wfecfin", $wfecfin);
		  echo " &nbsp; </td>";
		  echo "</tr>";
		  echo "</table>";
		  echo "<input type='hidden' name='wres' value='1'>";
		  ///////////////////////////////////////////////////////
		 }
	    else
	       {
			  // Inicia resultado de la consulta
			  echo "<br>";
			  echo "<div align='center'><b>RESULTADO DE LA CONSULTA</b></div>";
			  echo "<br>";
			  echo "<br /><div align='center'><input type='button' value='Retornar' onClick='javascript:inicio();'> &nbsp; | &nbsp; <input type='button' value='Imprimir documentos' onClick='javascript:imprimir();'></div>&nbsp;&nbsp;";
		      if ($wfecini and $wfecfin)
		       {

				echo "<input type='hidden' name='wfecini' value='".$wfecini."'>";
				echo "<input type='hidden' name='wfecfin' value='".$wfecfin."'>";

			     // Query principal que genera la lista inicial del resultado
				 $q = " SELECT renfue, rennum, renfec, rencod, rencaj, rennom, renvca, renusu, rencco, renest "
					 ." FROM ".$wbasedato."_000020 "
			         ." WHERE (renfue like '31' "
					 ."    OR  renfue like '32') "
			         ."   AND renfec BETWEEN '".$wfecini."' AND '".$wfecfin."' "
			         ." GROUP BY 1,2,3,4,5,6,7,8,9,10 ";
				 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			     $num = mysql_num_rows($res);

			     // Si existen registros comience a mostrar la lista de resultados
				 if ($num > 0)
			       {

					 // Indice usado para los arreglos de cajas y cco
					 $ind = "";

					 // Se crea un arreglo para usar en el ciclo que contendra los registros del maestro de cajas
					 $cajas = array();
					 $qcaja = " SELECT Cajcod, Cajdes "
							 ." FROM ".$wbasedato."_000028 "
							 ." GROUP BY 1,2 "
							 ." ORDER BY Cajcod ";
					 $rescaja = mysql_query($qcaja,$conex);
					 $numcaja = mysql_num_rows($rescaja);

					 for($i=1;$i<=$numcaja;$i++)
					 {
						$rowcaja = mysql_fetch_array($rescaja);
						$ind = $rowcaja['Cajcod'];
						$cajas[$ind] = $rowcaja['Cajdes'];
					 }

					 // Se crea un arreglo para usar en el ciclo que contendra los registros del maestro de centro de costos
					 $cco = array();
					 $qcco = " SELECT Ccocod, Ccodes "
							 ." FROM ".$wbasedato."_000003 "
							 ." GROUP BY 1,2 "
							 ." ORDER BY Ccocod ";
					 $rescco = mysql_query($qcco,$conex);
					 $numcco = mysql_num_rows($rescco);

					 for($i=1;$i<=$numcco;$i++)
					 {
						$rowcco = mysql_fetch_array($rescco);
						$ind = $rowcco['Ccocod'];
						$cco[$ind] = $rowcco['Ccodes'];
						//echo $ind." - ".$cco[$ind]."<br>";
					 }

					 echo "<br>";
			         echo "<center><table border='0'>";

			         echo "<tr><td colspan=11 class='fila2'><b>Cantidad de recibos y notas encontrados: ".$num."</b></td></tr>";

				     // Encabezado de la lista
					 echo "<tr class='encabezadoTabla'>";
				     echo "<th>FUENTE</th>";
				     echo "<th>DOCUMENTO</th>";
				     echo "<th>FECHA</th>";
				     echo "<th>RESPONSABLE</th>";
				     echo "<th>CAJA</th>";
				     echo "<th>CENTRO DE COSTOS</th>";
				     echo "<th>VALOR CANCELADO</th>";
				     echo "<th>ESTADO DOCUMENTO</th>";
				     echo "</tr>";

				     $wver="";

				     // Ciclo para mostrar la lista de registros
					 for ($i=1;$i<=$num;$i++)
				        {
					     $row = mysql_fetch_array($res);

					     if ($i%2==0)
			                $wclass="fila1";
			               else
			                  $wclass="fila2";


			             //Le coloco nombre a los estados del documento
			             switch ($row['renest'])
			                {
				             case "on":
				               { $westreg="ACTIVA"; }
				               BREAK;
				             case "off":
				               { $westreg="ANULADA"; }
				               BREAK;
				            }

						 echo "<tr>";
					     echo "<td class='".$wclass."'>".$row['renfue']."</td>";         //Fuente documento
					     echo "<td class='".$wclass."'>".$row['rennum']."</td>";         //Numero documento
					     echo "<td class='".$wclass."'>".$row['renfec']."</td>";         //Fecha
					     echo "<td class='".$wclass."'>".$row['rencod']." - ".$row['rennom']."</td>";               //Empresa responsable
						 $ind = $row['rencaj'];
					     echo "<td class='".$wclass."'>".$cajas[$ind]."</td>";         //Caja
						 $ind = $row['rencco'];
					     echo "<td class='".$wclass."'>".$cco[$ind]."</td>";         //Centro de costos
					     echo "<td class='".$wclass."' align=right>".number_format($row['renvca'],0,'.',',')."</td>";	//Valor cancelado
					     echo "<td class='".$wclass."' align=center><b>".$westreg."</b></td>";	//Estado
					     echo "</tr>";
				        }
					}
					else
						echo "<br /><br /><div align='center'>No se encontraron recibos o notas para las fechas especificadas.</div>";
			    }
				else
					echo "<br /><br /><div align='center'>Se debe especificar un rango de fehas.</div><br />";

		   }
		   // Fin del resultado de la consulta


	  echo "<tr><td align=center colspan=11 height='41'>";
	  if($wres==1)
		echo "<br /><div align='center'><input type='button' value='Retornar' onClick='javascript:inicio();'> &nbsp; | &nbsp; <input type='button' value='Imprimir documentos' onClick='javascript:imprimir();'></div>&nbsp;&nbsp;";
	  else
		echo "<br /><div align='center'><input type='submit' value='Consultar'> | <input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></div></td></tr>";
	  echo "</table>";

	}
	else
	{
		// Inicio de los documentos consultados en formato de impresión
		if ($wfecini and $wfecfin)
		{

			// Query principal para mostrar los documentos en formato de impresión
			$qimp =  " SELECT renfue, rennum, rencco "
					."   FROM ".$wbasedato."_000020 "
					."  WHERE (renfue like '31' "
					."     OR  renfue like '32') "
					."    AND renfec BETWEEN '".$wfecini."' AND '".$wfecfin."' "
					."  GROUP BY 1, 2, 3 ";
			$resimp = mysql_query($qimp,$conex);
			$numimp = mysql_num_rows($resimp);

			 // Si existen registros inicie la impresión
			 if ($numimp > 0)
			 {

				// Ciclo que muestra los documentos en formato de impresión
				for ($imp=1;$imp<=$numimp;$imp++)
				{
					$rowimp = mysql_fetch_array($resimp);

					// Obtengo los parámetros a usar en Imp_documento.php
					$wnrodoc = $rowimp['rennum'];
					$wfuedoc = $rowimp['renfue'];
					$wcco = $rowimp['rencco'];
					$obser = '1';

					// Llamo al script que genera los documentos en formato de impresión
					include_once("../procesos/Imp_documento.php");

					// Incluyo salto de página para que imprima cada documento en páginas aparte
					echo "<h1 class=SaltoDePagina> </h1>";
				}


			 }
				else
					echo "<br /><br /><div align='center'>No se encontraron recibos o notas para las fechas especificadas.</div>";
		}
			else
				echo "<br /><br /><div align='center'>Se debe especificar un rango de fehas.</div><br />";
	}

}
?>
</body>
</html>
