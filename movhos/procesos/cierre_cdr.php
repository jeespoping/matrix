<?php
include_once("conex.php");
// funcion ajax que trae diagnosticos segun la historia y el numero de ingreso del paciente seleccionado
function pinta_lista_diagnosticos($conexunix,$whis)
{


	$explode = explode('_',$whis);
	$his_tmp = explode(':',$explode[1]);
	$ing_tmp = explode(':',$explode[2]);
	// $whis solo contiente la historia, despues de las anteriores operaciones
	$whis = trim($his_tmp[1]);
	// $wingaux contiene el ingreso, despues de las anteriores operaciones
	$wingaux = trim($ing_tmp[1]);



	//ACA TRAIGO LOS DIAGNOSTICOS QUE TENGA LA HISTORIA EN EL UNIX
	 $query = " SELECT diacod, dianom, egrhis, egrnum"
			 ."   FROM inpaci, inmegr, india, inmdia "
			 ."  WHERE pachis  = '".$whis."'"
			 ."    AND pachis  = egrhis "
			 ."    AND egrhis  = mdiahis "
			 ."    AND egrnum  = '".$wingaux."'"
			 ."    AND mdiadia = diacod "
			 ."    AND mdiatip = 'P' "
			 ."  GROUP BY 1,2,3,4 "
			 ."  ORDER BY 2, 3 desc ";
	 $res = odbc_do($conexunix,$query);

	 echo "<select name='wdiag' id='wdiag' onchange='javascript:carga_procedxdiagnostico()'>";
	 echo "<option selected>XXXX - NO ESPECIFICADO</option>";
	 while(odbc_fetch_row($res))
		 echo "<option>".odbc_result($res,1)." - "
						.odbc_result($res,2)." - "
						."Historia: ".odbc_result($res,3)
						." - Ingreso Nro: ".odbc_result($res,4)
			 ."</option>";
	 echo "</select>";
}
// funcion ajax que trae los procedimientos segun la historia y el numero de ingreso del paciente seleccionado
function pinta_lista_procedimientos($conexunix,$whis)
{
	$explode = explode('_',$whis);
	$his_tmp = explode(':',$explode[1]);
	$ing_tmp = explode(':',$explode[2]);
	// $whis solo contiente la historia, despues de las anteriores operaciones
	$whis = trim($his_tmp[1]);
	// $wingaux contiene el ingreso, despues de las anteriores operaciones
	$wingaux = trim($ing_tmp[1]);



	//ACA TRAIGO LOS PROCEDIMIENTOS QUE TENGA LA HISTORIA EN EL UNIX
	  $query = " SELECT procod, pronom, egrhis, egrnum "
			 ."   FROM inpaci, inmegr, inpro, inmpro "
			 ."  WHERE pachis  = '".$whis. "'"
			 ."    AND pachis  = egrhis "
			 ."    AND egrhis  = mprohis "
			 ."    AND egrnum  = '".$wingaux."'"
			 ."    AND mpropro = procod "
			 ."    AND mprotip = 'P' "
			 ."  GROUP BY 1,2,3,4 "
			 ."  ORDER BY 2, 3 desc ";
	 $res = odbc_do($conexunix,$query);


	 echo "<select name='wpro' id='wpro'>";
	 echo "<option selected>XXXX - NO ESPECIFICADO</option>";
	 while(odbc_fetch_row($res))
		 echo "<option>".odbc_result($res,1)." - "
						.odbc_result($res,2)." - "
						."Historia: ".odbc_result($res,3)
						." - Ingreso Nro: ".odbc_result($res,4)
			 ."</option>";
	 echo "</select>";
}

// sí la $consultaAjax existe se conecta a Unix y segun el valor de $wop recarga la lista de diagnosticos o la de procedimientos

if(isset($consultaAjax))
{
	

	

	$conexunix = odbc_connect('admisiones','infadm','1201')
						  or die("No se ralizo Conexion con el Unix");

	switch($wop)
	{
		case 'diagnosticos':
			echo pinta_lista_diagnosticos($conexunix,$whis);
		break;
		case 'procedimientos':
			echo pinta_lista_procedimientos($conexunix,$whis);
		break;
		default :
		break;
	}
	odbc_close($conexunix);
	odbc_close_all();
}
else
{
?>
	<head>
	<title>CERTIFCADO DE ESTADISTICA</title>
	</head>
	<body>
	<script type="text/javascript">
	//funcion para cargar procedimientos segun la historia seleccionada
	function carga_procedxdiagnostico()
	{
		var parametros = "";


		parametros = "wop=procedimientos&consultaAjax=cargaPacientes&whis="+document.forms.form.whis.value;

		try
		{

			var ajax = nuevoAjax();

			ajax.open("POST", "cer_estadistica.php",true);

			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);

			ajax.onreadystatechange=function()
			{

				var contenedor = document.getElementById('lista_procedimientos');
				if (ajax.readyState==4 && ajax.status==200)
					contenedor.innerHTML=ajax.responseText;
			}

			if ( !estaEnProceso(ajax) )
			{
				ajax.send(null);
			}
		}catch(e){	}
	}


    //funcion para cargar diagnostico segun la historia seleccionada
	function carga_datosxhistoria()
	{
		var parametros = "";


		parametros = "wop=diagnosticos&consultaAjax=cargaPacientes&whis="+document.forms.form.whis.value;


		try
		{

			var ajax = nuevoAjax();

			ajax.open("POST", "cer_estadistica.php",true);

			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);

			ajax.onreadystatechange=function()
			{

				var contenedor = document.getElementById('lista_diagnosticos');
				if (ajax.readyState==4 && ajax.status==200)
					contenedor.innerHTML=ajax.responseText;
			}

			if ( !estaEnProceso(ajax) )
			{
				ajax.send(null);
			}
		}catch(e){	}
	}



	</script>
	<?php
	  /***************************************************
	   *       GRABA LOS CERTIFICADOS DE ESTADISTICA     *
	   * HOSPITALIZADOS, CIRUGIA AMBULATORIA Y EGRESADOS *
	   *				CONEX, FREE => OK				 *
	   ***************************************************/

	//==================================================================================================================================
	//PROGRAMA                   : cer_estadistica.php
	//AUTOR                      : Juan Carlos Hernández M.
	//FECHA CREACION             : Marzo 15 de 2005
	//FECHA ULTIMA ACTUALIZACION :
	  $wactualiz="(Ver. 2012-04-12)";
	//DESCRIPCION
	//==================================================================================================================================
	//Este programa se hace con el objetivo de tener una base de datos de todos los certificados de estancia que se hacen en
	//registros medicos, el cual pide como parametros, Tipo de certificado (hay 4 tipos), el documento de identidad, nombres y apellidos
	//del paciente, con alguno oalgunos parametros de estos, se va ha buscar los ingresos y egresos que tenga en el momento el paciente
	//y asi el usuario de registros medicos selecciona sobre cual ingreso va sacar el certificado. El diagnostico y el procedimiento se
	//deben seleccionar tambien de acuerdo al numero de ingreso seleccionado.
	//Para reimprimir un certificado se debe saber el numero de este. y digitarlo en el campo nro de certificado y enter y saldra la
	//misma información que se imprimio cuando se genero el original.
	//==================================================================================================================================

	//==================================================================================================================================
	//MODIFICACIONES 2007-04-03: Se agrega un campo para poner observaciones y se crea el campo en la tabla cerest_000001.
	//==================================================================================================================================
	// Septiembre 1
	// Se adionado el certifcado de Urgencias
	//==================================================================================================================================
	// MODIFICACIONES 2012-04-12:
	// Se agrega la funcionalidad de poder anular un certificado
	// - Se agrega un campo de:
	// 		- cerest (estado).
	//		- cermoa (motivo de anulación)
	//		- cerusa (usuario que anula)
	//		- cerfea (fecha de anulacion)
	//		- cerhoa (hora de anulacion)
	//	en la tabla cerest_000001.
	// - En la impresion de certificados se agrega la palabra Anulado si el estado es off
	// - se incluye el comun
	// - Se agrega nuevo estilo a todo el script.
    // - Se adicciona funcion ajax que se encarga de recargar el dianostico y el procedimiento segun lo seleccionado en la historia
    // - Se arregla el problema que ocurria cuando se buscaba a un paciente por nombre y apellido, el problema consistia que de esta
    //   Manera no traia documento y cuando se buscaba este certificado no traia datos pues en unix se busca por documento (y etaba vacio)
	//==================================================================================================================================


	

	include_once("root/comun.php");


	global $wopimprimir;
	global $wfecing;
	global $wfecegreso;
	global $wap1;
	global $wap2;
	global $wopanular;
	global $wanular;
	global $wmoa;
	global $wnom;
	global $wdoc;


	session_start();



	if (!isset($user))
		{
		 if(!isset($_SESSION['user']))
			session_register("user");
		}

	if(!isset($_SESSION['user']))
		echo "error";
	else
	{

	  


	  $conexunix = odbc_connect('admisiones','infadm','1201')
						  or die("No se ralizo Conexion con el Unix");

	  $pos = strpos($user,"-");
	  $wusuario = substr($user,$pos+1,strlen($user));

	  if ($wopimprimir != "OK"){
	  $titulo = "CERTIFICADO ESTADISTICA";
	  encabezado($titulo,$wactualiz, "clinica");
      }
	  echo "<br>";
	  echo "<br>";

	  echo "<form name='form' id= 'form' action='cer_estadistica.php' method=post>";


	  //========================================================================================================
	  //Aca entra solo cuando se va ha consultar un certificado
	  //========================================================================================================
	  if (isset($wcer) and $wcer != "")
		 {

		  //Traigo el certificado digitado
		  $q = "  SELECT fecha_data, Nro_certificado, Tipo , Historia  , Ingreso, Documento, Coddia, Nomdia, "
			  ."         Codpro, Nompro, Destino, Solicitado, Firmo, Observacion, Cerest "
			  ."    FROM cerest_000001 "
			  ."   WHERE Nro_certificado = ".$wcer;
		  $res1 = mysql_query($q,$conex);
		  $num1 = mysql_num_rows($res1);
		  //echo mysql_errno() ."=". mysql_error();
		if ($num1 > 0)
		   {
			$row = mysql_fetch_array($res1);
			$wfecha  = $row[0];
			$wcer    = $row[1];
			$wtip    = $row[2];
			$whis    = $row[3];
			$whis1   = $row[3];
			$wing    = $row[4];
			$wdoc    = $row[5];
			$wcoddia = $row[6];
			$wnomdia = $row[7];
			$wcodpro = $row[8];
			$wnompro = $row[9];
			$wdes    = $row[10];
			$wsol    = $row[11];
			$wfir    = $row[12];
			$obser   = $row[13];
			$wcerest = $row[14];



			//Traigo los datos que corresponden al certificado pero que estan solo almacenados en el UNIX
		   $query = " SELECT pacfec, pacnom, pacap1, pacap2, pacfec "
					."   FROM inpac "
					."  WHERE pacced = '".$wdoc."'"
					."    AND pachis = '".$whis1."'"
					."    AND pacnum = '".$wing."'"

					//se comenta esta parte porque no es necesaria
					/*."  UNION ALL "

					." SELECT pacing, pacnom, pacap1, pacap2, pacing "
					."   FROM inpaci "
					."  WHERE pacced = '".$wdoc."'"
					."    AND pachis = '".$whis1."'"
					."    AND pacnum = '".$wing."'"

				  */."  UNION ALL "

					." SELECT egring, pacnom, pacap1, pacap2, egregr "
					."   FROM inpaci, inmegr "
					."  WHERE pacced = '".$wdoc."'"
					."    AND pachis = '".$whis1."'"
					//."  AND pacnum = '".$wing."'"
					."    AND pachis =  egrhis "
					."    AND egrnum = '".$wing."'"
					."  GROUP BY 5, 1, 2, 3, 4 "
					."  ORDER BY 5 desc, 1, 2 ";



			$res = odbc_do($conexunix,$query);

			while(odbc_fetch_row($res))
				{
				 $wfecing = odbc_result($res,1);
				 $wnom    = odbc_result($res,2);
				 $wap1    = odbc_result($res,3);
				 $wap2    = odbc_result($res,4);
				 $wfecegreso = odbc_result($res,5);
				}



			//$wfecing=2006/02/02;
			//Traigo el cargo del responsable de la firma
			$q = "  SELECT cargo "
				."    FROM cerest_000002 "
				."   WHERE Nombre = '".$wfir."'";
			$res1 = mysql_query($q,$conex);
			$num1 = mysql_num_rows($res1);
			if ($num1 > 0)
			  {
			   $row = mysql_fetch_array($res1);
			   $wempleado = $wfir;
			   $wcargo    = $row[0];
			  }
		   }
		  else
			 echo "<table align='center'><tr class = Fila1><td><b>EL CERTIFICADO NO EXISTE</b></td></tr></table>";
		 }

		  //========================================================================================================
		  //Aca entra solo si es para crear un nuevo certificado
		  //========================================================================================================
		  if(!isset($wtip) or !isset($wdoc) or !isset($whis))
			{
			 echo "<table align='center'>";

			 if (!isset($wtip) or !isset($wdoc))
				{
				 //TIPOS DE CERTIFICADO
				 $q = "  SELECT subcodigo, descripcion "
					 ."    FROM det_selecciones "
					 ."   WHERE medico = 'cerest' "
					 ."     AND codigo = '01' ";

				 $res1 = mysql_query($q,$conex);
				 $num1 = mysql_num_rows($res1);
				 echo "<tr class=fila1>";
				 echo "<td><b>Tipo de certificado: </b></td><td><select name='wtip'>";
				 for($i=1;$f<$num1;$f++)
					{
					 $row = mysql_fetch_array($res1);
					 echo "<option>".$row[0]." - ".$row[1]."</option>";
					}
				 echo "</select></td>";
				 echo "</td>";
				 echo "</tr>";

				 //NRO DE CERTIFICADO
				 echo "<tr class=fila1><td><b>Nro de certificado :</b></td><td><INPUT TYPE='text' NAME='wcer' SIZE=15></td></tr>";

				 //DOCUMENTO
				 echo "<tr class=fila2><td><b>Documento :</b></td><td><INPUT TYPE='text' NAME='wdoc' SIZE=15 VALUE='*'></td></tr>";

				 //NOMBRES
				 echo "<tr class=fila2><td><b>Nombres :</b></td><td><INPUT TYPE='text' NAME='wnom' SIZE=30 VALUE='*'></td></tr>";

				 //PRIMER APELLIDO
				 echo "<tr class=fila2><td><b>Primer apellido :</b></td><td><INPUT TYPE='text' NAME='wap1' SIZE=30 VALUE='*'></td></tr>";

				 //SEGUNDO APELLIDO
				 echo "<tr class=fila2><td><b>Segundo apellido :</b></td><td><INPUT TYPE='text' NAME='wap2' SIZE=30 VALUE='*'></td></tr>";

				}

			 //No ha seleccionado la historia pero si los otros datos
			 if (!isset($whis) and (isset($wdoc) or isset($wnom) or isset($wap1) or isset($wap2)))
				{
				 //Si digita solo asteriscos, no se hace la consulta porque seria muy grande y demorada
				 if ($wdoc != '*' or $wnom != '*' or $wap1 != '*' or $wap2 != '*')
					{
					 //TIPO DE CERTIFICADO
					 echo "<tr class = Fila1><td><b>Tipo de certificado : </b></td><td>".$wtip."</td></tr>";

					 $wdoc = strtoupper($wdoc);
					 $wap1 = strtoupper($wap1);
					 $wap2 = strtoupper($wap2);
					 $wnom = strtoupper($wnom);


					 //TRAIGO TODOS LOS INGRESOS DEL PACIENTE HISTORIA, NOMBRE FECHAS DE INGRESO Y EGRESO
					 $query = " SELECT pachis, pacnum, pacnom, pacap1, pacap2, pacfec, pacfec, 'Activo',pacced "
							 ."   FROM inpac "
							 ."  WHERE pacced  matches '".$wdoc."'"
							 ."    AND pacnom  matches '".$wnom."'"
							 ."    AND pacap1  matches '".$wap1."'"
							 ."    AND (pacap2 matches '".$wap2."'"
							 ."     OR  pacap2 is null ) "

							 ."  UNION ALL "

							 /*
							 ." SELECT pachis, pacnum, pacnom, pacap1, pacap2, pacing, pacing, 'Ultimo Egresado' "
							 ."   FROM inpaci "
							 ."  WHERE pacced matches '".$wdoc."'"
							 ."    AND pacnom matches '".$wnom."'"
							 ."    AND pacap1 matches '".$wap1."'"
							 ."    AND pacap2 matches '".$wap2."'"

							 ."  UNION ALL "
							 */

							 ." SELECT egrhis, egrnum, pacnom, pacap1, pacap2, egring, egregr, 'Egresado',pacced "
							 ."   FROM inpaci, inmegr "
							 ."  WHERE pacced  matches '".$wdoc."'"
							 ."    AND pacnom  matches '".$wnom."'"
							 ."    AND pacap1  matches '".$wap1."'"
							 ."    AND (pacap2 matches '".$wap2."'"
							 ."     OR  pacap2 is null ) "
							 ."    AND pachis  = egrhis "
							 //."    AND pacnum != egrnum "
							 ."  GROUP BY 6, 1, 2, 3, 4, 5, 7, 8,9 "
							 ."  ORDER BY 6 desc, 1, 2 ";

					 $res = odbc_do($conexunix,$query);

					 echo "<tr class = Fila2><td><b>Seleccione la Historia e ingreso: </b></td><td><select name='whis' id='whis' onchange='javascript:carga_datosxhistoria()'>";
					 echo "<option selected>XXXX - NO ESPECIFICADO</option>";
					 while(odbc_fetch_row($res))
						 echo "<option value = 'Fecha Ingreso: ".odbc_result($res,6)." _  Historia: ".odbc_result($res,1)." _ Ingreso Nro: ".odbc_result($res,2)." _ ".odbc_result($res,3)." _ ".odbc_result($res,4)." _ ".odbc_result($res,5)." _Fecha Egreso: ".odbc_result($res,7)." _ ".odbc_result($res,8)." _Doc: ".odbc_result($res,9)."'><b>Fecha Ingreso: </b>".odbc_result($res,6)." _ "  //Fecha de ingreso
									 ."<b>Historia:      </b>".odbc_result($res,1)." _ "  //Historia
									 ."<b>Ingreso Nro:   </b>".odbc_result($res,2)." _ "  //Ingreso
															  .odbc_result($res,3)." _ "  //Nombre paciente
															  .odbc_result($res,4)." _ "  //1er apellido
															  .odbc_result($res,5)." _ "  //2do apellido
									 ."<b>Fecha Egreso:  </b>".odbc_result($res,7)." _ "  //Fecha de egreso
															  .odbc_result($res,8)." _ "  //Estado
									 ."<b>Doc:           </b>".odbc_result($res,9)        //Documento
							 ."</option>";
					 echo "</select></td></tr>";
					 //apartir de aca viene el ajax

					 //ACA TRAIGO LOS DIAGNOSTICOS QUE TENGA LA HISTORIA EN EL UNIX
					 /*$query = " SELECT diacod, dianom, egrhis, egrnum "
							 ."   FROM inpaci, inmegr, india, inmdia "
							 ."  WHERE pacced  matches '".$wdoc."'"
							 ."    AND pacnom  matches '".$wnom."'"
							 ."    AND pacap1  matches '".$wap1."'"
							 ."    AND (pacap2 matches '".$wap2."'"
							 ."     OR  pacap2 is null ) "
							 ."    AND pachis  = egrhis "
							 ."    AND egrhis  = mdiahis "
							 ."    AND egrnum  = mdianum "
							 ."    AND mdiadia = diacod "
							 ."    AND mdiatip = 'P' "
							 ."  GROUP BY 1,2,3,4 "
							 ."  ORDER BY 2, 3 desc ";
					 $res = odbc_do($conexunix,$query);*/

					 echo "<tr class = Fila2><td><b>Seleccione el diagnostico: </b></td>";
					 echo "<td>	<div id='lista_diagnosticos' >
								<select name='wdiag'>";
					 echo "<option selected>XXXX - NO ESPECIFICADO</option>";
					 /*while(odbc_fetch_row($res))
						 echo "<option>".odbc_result($res,1)." - "
										.odbc_result($res,2)." - "
										."Historia: ".odbc_result($res,3)
										." - Ingreso Nro: ".odbc_result($res,4)
							 ."</option>";*/
					 echo "</select></div></td></tr>";

					 //ACA TRAIGO LOS PROCEDIMIENTOS QUE TIENE LA HISTORIA EN EL UNIX



					 echo "<tr class = Fila2 ><td><b>Seleccione el procedimiento: </b></td>";

					 echo "<td><div id='lista_procedimientos' ><select name='wpro'>";
					 echo "<option selected>XXXX - NO ESPECIFICADO</option>";

					 echo "</select></div></td></tr>";

					 //SE PIDE LA OBSERVACION
					 echo "<tr class = Fila2><td><b>Observacion :</b></td><td><TEXTAREA Name='obser' rows='3' cols='50'>*</TEXTAREA></td></tr>";

					  //PIDO LA ENTIDAD DESTINO
					 echo "<tr class = Fila2><td><b>Entidad a quien se dirige :</b></td><td><INPUT TYPE='text' NAME='wdes' SIZE=80 VALUE='*'></td></tr>";

					 //PIDO EL SOLICITANTE
					 echo "<tr class = Fila2><td><b>A solicitud de: </b></td><td><INPUT TYPE='text' NAME='wsol' SIZE=80 VALUE='*'></td></tr>";

					 //PIDO DEL EMPLEADO QUE FIRMA
					 $q = "  SELECT nombre, cargo "
						 ."    FROM cerest_000002 ";
					 $res1 = mysql_query($q,$conex);
					 $num1 = mysql_num_rows($res1);

					 echo "<tr class = Fila2><td><b>Empleado que registra: </b></td><td><select name='wfir'>";
					 for($i=1;$f<$num1;$f++)
						{
						 $row = mysql_fetch_array($res1);
						 echo "<option>".$row[0]." - ".$row[1]."</option>";
						}
					 echo "</select></td></tr>";


					 echo "<input type='HIDDEN' name= 'wtip' value='".$wtip."'>";
					 echo "<input type='HIDDEN' name= 'wdoc' value='".$wdoc."'>";


					}
				}
				echo "<br>";
				echo"<tr class = fila2><td></td><td align=center><input type='submit' value='ACEPTAR'></td></tr></form>";
				echo "</table>";
			}
		   else
			  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  // Ya estan todos los campos setiados o iniciados ===================================================================================
			  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  {
			   echo "<input type='HIDDEN' name= 'wtip' value='".$wtip."'>";
			   echo "<input type='HIDDEN' name= 'wdoc' value='".$wdoc."'>";

			   $wfecha=date("Y-m-d");
			   $wano = substr($wfecha,0,4);
			   $wmes = substr($wfecha,5,2);
			   $wdia = substr($wfecha,8,2);


			   //Si el certificado se esta consultando no entra a este if. Para ingresar a este if es porque se esta creando el certificado
			   if (!isset($wcer) or $wcer == "" )
				  {


				   $pos0    = strpos($whis,":");

				   $pos1    = strpos($whis,"_",$pos0+1);
				   $wfecing = substr($whis,$pos0+1,$pos1-$pos0-1);

				   $pos1_2    = strpos($whis,":",$pos1+1);

				   $pos2    = strpos($whis,"_",$pos1_2+1);
				   $whis1   = substr($whis,$pos1_2+1,$pos2-$pos1_2-1);

				   $pos2_2    = strpos($whis,":",$pos2+1);

				   $pos3    = strpos($whis,"_",$pos2_2+1);
				   $wing    = substr($whis,$pos2_2+1,$pos3-$pos2_2-1);

				   $pos4    = strpos($whis,"_",$pos3+1);
				   $wnom    = substr($whis,$pos3+1,$pos4-$pos3-1);

				   $pos5    = strpos($whis,"_",$pos4+1);
				   $wap1    = substr($whis,$pos4+1,$pos5-$pos4-1);

				   $pos6    = strpos($whis,"_",$pos5+1);
				   $wap2    = substr($whis,$pos5+1,$pos6-$pos5-1);

				   $pos6_2    = strpos($whis,":",$pos6+1);

				   $pos7    = strpos($whis,"_",$pos6_2+1);
				   $wfecegreso = substr($whis,$pos6_2+1,$pos7-$pos6_2-1);

				   $wtempdoc = explode(':',$whis);
				   $wdoc = trim($wtempdoc[5]);

				   //SEPARO codigo y nombre del diagnostico
				   $pos1    = strpos($wdiag,"-");
				   $wcoddia = substr($wdiag,0,$pos1);

				   $pos2    = strpos($wdiag,"-",$pos1+1);
				   if ($pos2 == 0)
					  $wnomdia = substr($wdiag,$pos1+1,strlen($wdiag));
					 else
						$wnomdia = substr($wdiag,$pos1+1,$pos2-$pos1-1);

				   //SEPARO codigo y nombre del procedimiento
				   $pos1    = strpos($wpro,"-");
				   $wcodpro = substr($wpro,0,$pos1);

				   $pos2    = strpos($wpro,"-",$pos1+1);
				   if ($pos2 == 0)
					  $wnompro = substr($wpro,$pos1+1,strlen($wpro));
					 else
						$wnompro = substr($wpro,$pos1+1,$pos2-$pos1-1);

				   //SEPARO nombre y cargo del empleado
				   $pos1    = strpos($wfir,"-");
				   $wempleado = substr($wfir,0,$pos1);
				   $wcargo = substr($wfir,$pos1+1,strlen($wfir));

				   echo "<input type='HIDDEN' name= 'wdiag' value='".$wdiag."'>";
				   echo "<input type='HIDDEN' name= 'wpro' value='".$wpro."'>";
				   echo "<input type='HIDDEN' name= 'wdes' value='".$wdes."'>";
				   echo "<input type='HIDDEN' name= 'wsol' value='".$wsol."'>";
				   echo "<input type='HIDDEN' name= 'wfir' value='".$wfir."'>";
				   echo "<input type='HIDDEN' name= 'wfecegreso' value='".$wfecegreso."'>";



				   //Traigo el ultimo numero de los certificados
				   $q = "  SELECT MAX(nro_certificado) "
					   ."    FROM cerest_000001 ";
				   $res1 = mysql_query($q,$conex);
				   $row = mysql_fetch_array($res1);
				   $wnrocer = $row[0]+1;

				   $wcer = $wnrocer;



				   $hora = (string)date("H:i:s");


				   $q = "     insert into cerest_000001 (Medico  ,   Fecha_data,    Hora_data,   Nro_certificado     ,   Tipo    ,   Historia ,   Ingreso ,   Documento,   Coddia     ,   Nomdia     ,   Codpro     ,   Nompro     ,   Destino ,   Solicitado,   Firmo        , Observacion,     Seguridad) "
					   ."                        values ('cerest','".$wfecha."' ,'".$hora."' ,'".$wnrocer."'         ,'".$wtip."', ".$whis1." , ".$wing." ,'".$wdoc."' ,'".$wcoddia."','".$wnomdia."','".$wcodpro."','".$wnompro."','".$wdes."','".$wsol."'  ,'".$wempleado."', '".$obser."','C-".$wusuario."')";
				   $res2 = mysql_query($q,$conex) or die(mysql_errno().":".mysql_error());
				  }


				if($wopimprimir != "OK" and $wopanular != "OK" ){

				echo "<table align='center' cellspacing='2'>";
				echo "<tr class=fila1><td><b>NUMERO CERTIFICADO: </b></td><td>".$wcer."</td></tr>";
				echo "<tr class=fila2><td><b> NOMBRE: </b></td><td>".$wnom." ".$wap1." ".$wap2."</td></tr>";
				echo "<tr class=fila1><td><b>IDENTIFICACION: </b></td><td>".$wdoc."</td></tr>";
				echo "<input type='HIDDEN' name= 'wcer' value='".$wcer."'>";
				echo "<input type='HIDDEN' name= 'wnom' value='".$wnom."'>";
				echo "<input type='HIDDEN' name= 'wap1' value='".$wap1."'>";
				echo "<input type='HIDDEN' name= 'wap2' value='".$wap2."'>";
				echo "<input type='HIDDEN' name= 'wdoc' value='".$wdoc."'>";
                echo "<input type='HIDDEN' name= 'wfecegreso' value='".$wfecegreso."'>";
				// Se crea boton imprimir y anular dependiendo del que se le de click se  cambia el valor a la variable wopimprimir o de
				// wopanular
				echo"<tr class=fila2>";
				echo'<td><input type="button" value="IMPRIMIR" onclick="document.form.wopimprimir.value=\'OK\'; document.form.submit();" >';
				echo"<input type='HIDDEN' name='wopimprimir' id='wopimprimir' value=''>";
				echo"<input type='HIDDEN' name='wopanular' id='wopanular' value=''>";
				echo"<input type='button' value='RETORNAR' onclick='document.form2.submit();'></td><td>";
				echo'<input type="button" value="ANULAR" onclick="document.form.wopanular.value=\'OK\'; document.form.submit();" >';
				echo"</td>";
				echo"</tr>";
				echo"</form>";
				echo"<form name='form2' id='form2' action='cer_estadistica.php'></form>";
				echo"</table>";
				}

			//este if controla el paso para imprimir por medio de la variable $wopimprimir.
			if($wopimprimir == "OK")
			{
			   echo"<table width='75%'  ><tr><td>";
			   switch ($wmes)
				 {
				  case "01": $wmes="Enero"; break;
				  case "02": $wmes="Febrero"; break;
				  case "03": $wmes="Marzo"; break;
				  case "04": $wmes="Abril"; break;
				  case "05": $wmes="Mayo"; break;
				  case "06": $wmes="Junio"; break;
				  case "07": $wmes="Julio"; break;
				  case "08": $wmes="Agosto"; break;
				  case "09": $wmes="Septiembre"; break;
				  case "10": $wmes="Octubre"; break;
				  case "11": $wmes="Noviembre"; break;
				  case "12": $wmes="Diciembre"; break;
				 }
			   //echo $wfecing;
			   $wfechai = date(trim($wfecing)); //Fecha de Ingreso

			   $wanoi   = substr($wfechai,0,4);
			   $wmesi   = substr($wfechai,5,2);
			   $wdiai  = substr($wfechai,8,2);



			   // condicion que  verifica la variable $wcerest, y asi mostrar si el certificado esta o no anulado
			   if ($wcerest != "on")
			   {
				echo "<p align=right><font color = 'red' size=5><b>ANULADO</b></font></p>";
			   }
			   echo "Medellín, ".$wdia." de ".$wmes." de ".$wano;
			   echo "<br><br><br><br>";
			   echo "<center><font size=4> LA COORDINADORA DE REGISTROS MEDICOS</font></center>";
			   echo "<br><br>";
			   echo "<p align=right><b>CERTIFICADO NRO.: $wcer</b></p>";
			   echo "<br>";
			   echo "<center><font size=3>CERTIFICA:</font></center>";
			   echo "<br><br><br><br>";
			   echo "<div align='justify'>";
			   switch (Trim($wtip))
				  {
				   case "01 - HOSPITALIZADO":    //Hospitalizado
					  {
					   switch ($wmesi)
						 {
						  case "01": $wmesi="Enero"; break;
						  case "02": $wmesi="Febrero"; break;
						  case "03": $wmesi="Marzo"; break;
						  case "04": $wmesi="Abril"; break;
						  case "05": $wmesi="Mayo"; break;
						  case "06": $wmesi="Junio"; break;
						  case "07": $wmesi="Julio"; break;
						  case "08": $wmesi="Agosto"; break;
						  case "09": $wmesi="Septiembre"; break;
						  case "10": $wmesi="Octubre"; break;
						  case "11": $wmesi="Noviembre"; break;
						  case "12": $wmesi="Diciembre"; break;
						 }
					   echo  "<font size=3>Que el(la) paciente ".$wnom." ".$wap1." ".$wap2." con identificación ".$wdoc.", "
							."es atendido(a) en esta institución en el servicio de hospitalización desde el "
							.$wdiai." de ".$wmesi." de ".$wanoi." hasta nueva orden médica.</font>";

					   echo "<br><br><br>";

					   echo "<font size=3>Diagnóstico: ".$wnomdia."</font><br><br>";
					   echo "<font size=3>Procedimiento: ".$wnompro."</font>";

					   if ($obser != '*' and $obser !='')// este es el if para cuando no existe observacion
					   {
						   echo "<br><br><br>";
						   echo "<font size=3>Observacion: ".$obser."</font>";
					   }
					   echo "<br><br><br>";
					   echo "<font size=3>Este certificado se expide para presentar en: ".$wdes."</font><br><br>";
					   echo "<font size=3>A solicitud de: ".$wsol."</font>";
					   echo "<br><br><br>";
					   echo  "<font size=3>'Esta información pertenece a la historia clínica, cuya confidencialidad "
							."está protegida por ley, y ésta prohibe cualquier otro uso de ella o "
							."revelarla a otros, sin el consentimiento escrito de la persona a quien "
							."pertenece'.</font>";
					   echo "<br><br><br>";
					   echo "<font size=3>Cordialmente,</font><br>";
					   echo "<br><br><br>";
					   echo "<font size=3>".$wempleado."</font><br>";
					   echo "<font size=3>".$wcargo."</font><br>";
					   BREAK;
					  }

				   case "02 - CIRUGIA AMBULATORIA":    //Cirugia Ambulatoria
					  {
					   switch ($wmesi)
						 {
						  case "01": $wmesi="Enero"; break;
						  case "02": $wmesi="Febrero"; break;
						  case "03": $wmesi="Marzo"; break;
						  case "04": $wmesi="Abril"; break;
						  case "05": $wmesi="Mayo"; break;
						  case "06": $wmesi="Junio"; break;
						  case "07": $wmesi="Julio"; break;
						  case "08": $wmesi="Agosto"; break;
						  case "09": $wmesi="Septiembre"; break;
						  case "10": $wmesi="Octubre"; break;
						  case "11": $wmesi="Noviembre"; break;
						  case "12": $wmesi="Diciembre"; break;
						 }
					   echo  "<font size=3>Que el(la) paciente ".$wnom." ".$wap1." ".$wap2." con identificación ".$wdoc.", "
							."fue atendido(a) en esta institución en el servicio de cirugía ambulatoria el "
							.$wdiai." de ".$wmesi." de ".$wanoi.".</font>";

					   echo "<br><br><br>";

					   echo "<font size=3>Diagnóstico: ".$wnomdia."</font><br><br>";
					   echo "<font size=3>Procedimiento: ".$wnompro."</font>";

						if ($obser != '*' and $obser !='')// este es el if para cuando no existe observacion
					   {
						   echo "<br><br><br>";
						   echo "<font size=3>Observacion: ".$obser."</font>";
					   }
					   echo "<br><br><br>";
					   echo "<font size=3>Este certificado se expide para presentar en: ".$wdes."</font><br><br>";
					   echo "<font size=3>A solicitud de: ".$wsol."</font>";
					   echo "<br><br><br>";
					   echo  "<font size=3>'Esta información pertenece a la historia clínica, cuya confidencialidad "
							."está protegida por ley, y ésta prohibe cualquier otro uso de ella o "
							."revelarla a otros, sin el consentimiento escrito de la persona a quien "
							."pertenece'.</font>";
					   echo "<br><br><br>";
					   echo "<font size=3>Cordialmente,</font><br>";
					   echo "<br><br><br>";
					   echo "<font size=3>".$wempleado."</font><br>";
					   echo "<font size=3>".$wcargo."</font><br>";
					   BREAK;
					  }
				   case "03 - EGRESADO DE HOSPITALIZACION":    //Egresados de Hospitalización
					  {
					   switch ($wmesi)
						 {
						  case "01": $wmesi="Enero"; break;
						  case "02": $wmesi="Febrero"; break;
						  case "03": $wmesi="Marzo"; break;
						  case "04": $wmesi="Abril"; break;
						  case "05": $wmesi="Mayo"; break;
						  case "06": $wmesi="Junio"; break;
						  case "07": $wmesi="Julio"; break;
						  case "08": $wmesi="Agosto"; break;
						  case "09": $wmesi="Septiembre"; break;
						  case "10": $wmesi="Octubre"; break;
						  case "11": $wmesi="Noviembre"; break;
						  case "12": $wmesi="Diciembre"; break;
						 }


					   $wfecha_e=date(trim($wfecegreso)); //Fecha de Ingreso
					   $wano_e = substr($wfecha_e,0,4);
					   $wmes_e = substr($wfecha_e,5,2);
					   $wdia_e = substr($wfecha_e,8,2);

					   switch ($wmes_e)
						 {
						  case "01": $wmes_e="Enero"; break;
						  case "02": $wmes_e="Febrero"; break;
						  case "03": $wmes_e="Marzo"; break;
						  case "04": $wmes_e="Abril"; break;
						  case "05": $wmes_e="Mayo"; break;
						  case "06": $wmes_e="Junio"; break;
						  case "07": $wmes_e="Julio"; break;
						  case "08": $wmes_e="Agosto"; break;
						  case "09": $wmes_e="Septiembre"; break;
						  case "10": $wmes_e="Octubre"; break;
						  case "11": $wmes_e="Noviembre"; break;
						  case "12": $wmes_e="Diciembre"; break;
						 }

					   echo  "<font size=3>Que el(la) paciente ".$wnom." ".$wap1." ".$wap2." con identificación ".$wdoc.", "
							."fue atendido(a) en esta institución en el servicio de hospitalización desde el "
							.$wdiai." de ".$wmesi." de ".$wanoi." hasta el ".$wdia_e." de ".$wmes_e." de ".$wano_e.".</font>";

					   echo "<br><br><br>";

					   echo "<font size=3>Diagnóstico: ".$wnomdia."</font><br><br>";
					   echo "<font size=3>Procedimiento: ".$wnompro."</font>";

						if ($obser != '*' and $obser !='')// este es el if para cuando no existe observacion
					   {
						   echo "<br><br><br>";
						   echo "<font size=3>Observacion: ".$obser."</font>";
					   }
					   echo "<br><br><br>";
					   echo "<font size=3>Este certificado se expide para presentar en: ".$wdes."</font><br><br>";
					   echo "<font size=3>A solicitud de: ".$wsol."</font>";
					   echo "<br><br><br>";
					   echo  "<font size=3>'Esta información pertenece a la historia clínica, cuya confidencialidad "
							."está protegida por ley, y ésta prohibe cualquier otro uso de ella o "
							."revelarla a otros, sin el consentimiento escrito de la persona a quien "
							."pertenece'.</font>";
					   echo "<div>";
					   echo "<br><br><br>";
					   echo "<font size=3>Cordialmente,</font><br>";
					   echo "<br><br><br>";
					   echo "<font size=3><b>".$wempleado."</b></font><br>";
					   echo "<font size=3><b>".$wcargo."</b></font><br>";
					   BREAK;
					  }

				   case "04 - HOSPITALIZADO FALLECIDO":    //Egresados de Hospitalización
					  {
					   switch ($wmesi)
						 {
						  case "01": $wmesi="Enero"; break;
						  case "02": $wmesi="Febrero"; break;
						  case "03": $wmesi="Marzo"; break;
						  case "04": $wmesi="Abril"; break;
						  case "05": $wmesi="Mayo"; break;
						  case "06": $wmesi="Junio"; break;
						  case "07": $wmesi="Julio"; break;
						  case "08": $wmesi="Agosto"; break;
						  case "09": $wmesi="Septiembre"; break;
						  case "10": $wmesi="Octubre"; break;
						  case "11": $wmesi="Noviembre"; break;
						  case "12": $wmesi="Diciembre"; break;
						 }


					   $wfecha_e=date(trim($wfecegreso)); //Fecha de Ingreso
					   $wano_e = substr($wfecha_e,0,4);
					   $wmes_e = substr($wfecha_e,5,2);
					   $wdia_e = substr($wfecha_e,8,2);

					   switch ($wmes_e)
						 {
						  case "01": $wmes_e="Enero"; break;
						  case "02": $wmes_e="Febrero"; break;
						  case "03": $wmes_e="Marzo"; break;
						  case "04": $wmes_e="Abril"; break;
						  case "05": $wmes_e="Mayo"; break;
						  case "06": $wmes_e="Junio"; break;
						  case "07": $wmes_e="Julio"; break;
						  case "08": $wmes_e="Agosto"; break;
						  case "09": $wmes_e="Septiembre"; break;
						  case "10": $wmes_e="Octubre"; break;
						  case "11": $wmes_e="Noviembre"; break;
						  case "12": $wmes_e="Diciembre"; break;
						 }

					   echo  "<font size=3>Que el(la) paciente ".$wnom." ".$wap1." ".$wap2." con identificación ".$wdoc.", "
							."fue atendido(a) en esta institución en el servicio de hospitalización desde el "
							.$wdiai." de ".$wmesi." de ".$wanoi." hasta el ".$wdia_e." de ".$wmes_e." de ".$wano_e." y falleció.</font>";

					   echo "<br><br><br>";

					   echo "<font size=3>Diagnóstico: ".$wnomdia."</font><br><br>";
					   echo "<font size=3>Procedimiento: ".$wnompro."</font>";

						if ($obser != '*' and $obser !='')// este es el if para cuando no existe observacion
					   {
						   echo "<br><br><br>";
						   echo "<font size=3>Observacion: ".$obser."</font>";
					   }
					   echo "<br><br><br>";
					   echo "<font size=3>Este certificado se expide para presentar en: ".$wdes."</font><br><br>";
					   echo "<font size=3>A solicitud de: ".$wsol."</font>";
					   echo "<br><br><br>";
					   echo  "<font size=3>'Esta información pertenece a la historia clínica, cuya confidencialidad "
							."está protegida por ley, y ésta prohibe cualquier otro uso de ella o "
							."revelarla a otros, sin el consentimiento escrito de la persona a quien "
							."pertenece'.</font>";
					   echo "<br><br><br>";
					   echo "<font size=3>Cordialmente,</font><br>";
					   echo "<br><br><br>";
					   echo "<font size=3><b>".$wempleado."</b></font><br>";
					   echo "<font size=3><b>".$wcargo."</b></font><br>";
					   BREAK;
					  }

				   case "05 - URGENCIAS":    //Urgencias
					  {
					   switch ($wmesi)
						 {
						  case "01": $wmesi="Enero"; break;
						  case "02": $wmesi="Febrero"; break;
						  case "03": $wmesi="Marzo"; break;
						  case "04": $wmesi="Abril"; break;
						  case "05": $wmesi="Mayo"; break;
						  case "06": $wmesi="Junio"; break;
						  case "07": $wmesi="Julio"; break;
						  case "08": $wmesi="Agosto"; break;
						  case "09": $wmesi="Septiembre"; break;
						  case "10": $wmesi="Octubre"; break;
						  case "11": $wmesi="Noviembre"; break;
						  case "12": $wmesi="Diciembre"; break;
						 }
					   echo  "<font size=3>Que el(la) paciente ".$wnom." ".$wap1." ".$wap2." con identificación ".$wdoc.", "
							."fue atendido(a) en esta institución en el servicio de urgencias el día ".$wdiai.
							" de ".$wmesi." de ".$wanoi.".</font>";

					   echo "<br><br><br>";

					   echo "<font size=3>Diagnóstico: ".$wnomdia."</font><br><br>";
					   echo "<font size=3>Procedimiento: ".$wnompro."</font>";

						if ($obser != '*' and $obser !='')// este es el if para cuando no existe observacion
					   {
						   echo "<br><br><br>";
						   echo "<font size=3>Observacion: ".$obser."</font>";
					   }
					   echo "<br><br><br>";
					   echo "<font size=3>Este certificado se expide para presentar en: ".$wdes."</font><br><br>";
					   echo "<font size=3>A solicitud de: ".$wsol."</font>";
					   echo "<br><br><br>";
					   echo  "<font size=3>'Esta información pertenece a la historia clínica, cuya confidencialidad "
							."está protegida por ley, y ésta prohibe cualquier otro uso de ella o "
							."revelarla a otros, sin el consentimiento escrito de la persona a quien "
							."pertenece'.</font>";
					   echo "<br><br><br>";
					   echo "<font size=3>Cordialmente,</font><br>";
					   echo "<br><br><br>";
					   echo "<font size=3>".$wempleado."</font><br>";
					   echo "<font size=3>".$wcargo."</font><br>";
					   BREAK;
					  }

				  }
			echo"</td></tr></table>";
			}

			if($wopanular == "OK")
			{
				if($wcerest == "on")
				{

					if($wanular != "OK")
					{
						  echo "<form name='form' id='form' action='cer_estadistica.php' method=post>";
						  echo "<table align='center'>";
						  echo "<tr class=fila1><td><b>NUMERO CERTIFICADO: </b></td><td>".$wcer."</td></tr>";
						  echo "<tr class=fila2><td><b> NOMBRE: </b></td><td>". $wnom." ".$wap1." ".$wap2."</td></tr>";
						  echo "<tr class=fila1><td><b>IDENTIFICACION: </b></td><td> ".$wdoc." </td></tr>";
						  echo "<tr class=fila2><td><b>MOTIVO ANULACION :</b></td><td><TEXTAREA name='wmoa'  id='wmoa' rows='3' cols='50'  >".$wmoa."</TEXTAREA></td></tr>";
						  echo "<tr class=fila1><td><td align=center>";
						  //se valida que el campo wmoa (motivo de anulacion) tenga mas de 10 caracteres
						  echo '<input type="submit" value="ACEPTAR" onclick=" if (wmoa.value.length < 10) { alert(\'El motivo de anulacion debe contener mas de 10 caracteres\'); wmoa=wmoa.value;} else {document.form.wanular.value=\'OK\'; document.form.submit(); } ; " >';
						  echo '<input type="button" value="CANCELAR" onclick="document.location.href = \'cer_estadistica.php\' ">';
						  echo "</td></td>";
						  echo "<td><input type='HIDDEN' name='wanular' id='wanular' value=''></td>";
						  echo "</tr>";
						  echo "<input type='HIDDEN' name= 'wcer' value='".$wcer."'>";
						  echo "<input type='HIDDEN' name= 'wnom' id='wnom' value='".$wnom."'>";
						  echo "<input type='HIDDEN' name= 'wopanular' value='".$wopanular."'>";
						  echo "</table>";
						  echo"</form>";
					}
					else
					{
						$wusa = explode("-",$user);
						$wfea =date("Y-m-d");
						$whoa = (string)date("H:i:s");
						$q ="UPDATE cerest_000001 "
						."		SET Cerest = \"off \" "
						."			, Cermoa = \"".$wmoa."\" "
						."			, Cerfea = \"".$wfea."\" "
						."			, Cerhoa = \"".$whoa."\" "
						."			, Cerusa = \"".$wusa[1]."\" "
						."	  WHERE Nro_certificado = ".$wcer;

						$res1 = mysql_query($q,$conex);

						if($res != 1)
						{
							?>
							<script>
							alert("El Certificado ha sido anulado satisfactoriamente");
							document.location.href= 'cer_estadistica.php';
							</script>
							<?php
						}

					}
				}
				else
				{
					?>
					<script>
					alert("El Certificado ya estaba anulado");
					document.location.href= 'cer_estadistica.php';
					</script>
					<?php

				}

			}


			} // else de todos los campos setiados

		odbc_close($conexunix);
		odbc_close_all();

	} // if de register

	echo "<br>";
	//echo "<td colspan=3><left><font size=3><A href=cer_estadistica.php".">&nbsp;&nbsp;&nbsp;    Retornar &nbsp;&nbsp;&nbsp; </A></font></td></tr></table>";
	//echo "<td colspan=3><left><font size=1><A href=cer_estadistica.php".">Ir</A></font></td></tr></table>";

	include_once("free.php");
	//odbc_close($conexunix);


}
?>
