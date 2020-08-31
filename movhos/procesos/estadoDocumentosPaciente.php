<html>
<head>
<title>Estado Docs Pacs</title>
<style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	<!--Fondo Azul no muy oscuro y letra blanca -->
    	.tituloSup{color:#006699;background:#FFFFF;font-family:Arial;font-weight:bold;text-align:center;}
    	.tituloSup1{color:#57C8D5;background:#FFFFF;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo1{color:#FFFFFF;background:#006699;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
    	<!-- -->
    	<!--.titulo2{color:#003366;background:#57C8D5;font-size:11pt;font-family:Arial;font-weight:bold;text-align:center;}-->
    	.titulo2{color:#003366;background:#4DBECB;font-size:11pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo3{color:#0A3D6F;background:#61D2DF;font-size:11pt;font-family:Arial;text-align:center;}
    	.texto{color:#006699;background:#CCFFFF;font-size:10pt;font-family:Tahoma;text-align:center;}
    	<!-- .acumulado1{color:#003366;background:#FFCC66;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.acumulado2{color:#003366;background:#FFDBA8;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}
    	-->
    	.errorTitulo{color:#FF0000;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}
    	
    	.alert{background:#FFFFAA;color:#FF9900;font-size:10pt;font-family:Arial;text-align:center;}
    	.warning{background:#FFCC99;color:#FF6600;font-size:10pt;font-family:Arial;text-align:center;}
    	.error{background:#FFAAAA;color:#FF0000;font-size:10pt;font-family:Arial;text-align:center;}
    	
    	.tituloA1{color:#FFFFFF;background:#660099;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.textoA{color:#660066;background:#FFFFFF;font-size:11pt;font-family:Arial;}
    	    	
    </style>
</head>
<body>
<?php
include_once("conex.php");
/**
 * @wvar $pac 	
 * 				[nom]: Nom
 * 				[act]: El paciente esta ctivo en UNIX, es decir fue encontrado en la tabla inpac.
 * 				[alt]:Boolean. El usuario quiere hacerle el alta definitiva al paciente.<br>
 * 				[permisoAlta]:Boolean. Todos los registros activos del apciente estan en cargados a la cuanta, se puede efectuar el alta.<br>
 * 				[]:<br> 
 * @wvar $ok	Determina si se deben o no buscar registros procesados del paciente. Se da si el paciente esta activo en UNIX o se encontro su información en MATRIX.
 * @wvar $alta  false:debe preguntar al usuario si desea dar un alta.<br>
 * 				cco:el usuario eligio hacer un alta del paciente a un centro de costos.<br>
 * 				def: el usuario eligio hacer el alta definitiva de la institucicón.<br>
 * 
 * @modified 2007-09-27 Se hace la conexión odbc a facturación antes de llamar a ValidacionHistoriaUnix
 * @modified 2007-09-24 Entra la funcion registrosPaciente(),infoInconcistencia() y MostrarSTMI(), que antes estaba ubicadas en el include movhos/otros.php
 * @modified 2007-06-18 Se quita todo lo de altas
 * @modified 2007-06-15 Cambios en titulo.
 * 
 * @table itdroinc SELECT 
 * @table 0000002 SELECT, UPDATE
 * 
 */

/**
 * Busca la descripción de una inconsistencia en UNIX en itdroinc
 * a través de el droinnum=$num, y la droinclin=lin
 *
 * @table itdroinc SELECT 
 * 
 * @param Integer $num	númetro del registro
 * @param Integer $lin	Número de la línea
 * @return String		Descripción del error
 */
function infoInconcistencia($num, $lin)
{
	global $conex_o;

	$q= "SELECT droincdes "
	."     FROM itdroinc "
	."    WHERE droincnum = ".$num." "
	."		AND droinclin = ".$lin." ";
	$err_o= odbc_do($conex_o,$q);
	$result="";
	while (odbc_fetch_row($err_o))
	{
		/**
		 * Se que algunos de los registros fueron procesados, no cuales ni cuantos. Todavía puede que algunos esten en
		 * MATRIX, otros sin procesar u otros incosnsistentes.
		 */
		$result=$result."<br>".odbc_result($err_o,1);
	}
	return($result);
}


/**
 * Muestra en pantalla los encabezados de los cargos para el paciente con historia $pac['his'] e ingreso $pac['ing'],
 *  registros de la tabla 000002, que esten en el estado que hay en $ues (000002.Fenues=$ues).
 * 
 * @table 0000002 SELECT, UPDATE
 * 
 * @version  2007-09-14
 * 
 * @param Array		$pac 		Información del paciente
 * 								La información que debe estar en el arreglo cunado se llama la función es:</br>
 * 								[his]:Historia del paciente.</br>
 * 								[ing]:Ingreso del paciente.</br>
 * @param String[1] $ues	Estado de los registros que la función va a mostrar en pantalla.
 */
function registrosPaciente($pac, $ues)
{
	global $bd;
	global $conex;
	global $emp;

	$q = "SELECT Fecha_data, Fennum, Fendoc, Fenfue, Fencco, Fentip "
	."      FROM ".$bd."_000002 "
	."     WHERE Fenhis = '".$pac['his']."' "
	."       AND Fening = ".$pac['ing']." "
	."       AND Fenues = '".$ues."' "
	."       AND Fenest = 'on' "
	."  ORDER BY Fencco, Fenfue, Fennum ";
	$err=mysql_query($q,$conex);
	$num=mysql_num_rows($err);
	if($num > 0)
	{
		$cco['cod']="";
		for($k=0;$k<$num;$k++)
		{
			$row=mysql_fetch_array($err);
			if($cco['cod'] != $row['Fencco'])
			{
				if($k != 0)
				{
					echo "</table>";
				}
				$cco['cod'] = $row['Fencco'];
				getCco(&$cco,substr($row['Fentip'],0,1), $emp);

				echo "</br><table align='center' >";
				echo "<tr>";
				echo "<td class='titulo1' colspan='6'>".$cco['cod']."-".$cco['nom']."</td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td class='titulo2' >Fecha</td>";
				echo "<td class='titulo2' >Fuente</td>";
				echo "<td class='titulo2' >Doc. Matrix</td>";
				echo "<td class='titulo2' >Doc. UNIX</td>";
				echo "</tr>";

			}

			echo "<tr>";
			echo "<td class='texto' colspan='1'>".$row['Fecha_data']."</td>";
			echo "<td class='texto' colspan='1'>".$row['Fenfue']."</td>";
			echo "<td class='texto' colspan='1'>".$row['Fennum']."</td>";
			echo "<td class='texto' colspan='1'>".$row['Fendoc']."</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
}


/**
 * Muestra en Pantalla los registros que fueron procesados en actualizacionDetalleRegistros
 *
 * @param Array $pac	Datos del paciente
 * @param Array $datos	Aqui estan todos los registros que fueron procesados por la función actualizacionDetalleRegistros
 */
function MostrarSTMI($pac,$datos)
{
	global $emp;

	/**
	 * Numero de dronums o encabezados que componen el arreglo
	 */
	$numNum=count($datos);

	if($numNum>0)
	{
		$cco['cod']="";
		$numCns="";
		$inicio=0;
		for($k=0; $k<$numNum; $k++)
		{
			if($datos[$k]['numLin']>0)
			{
				/**
				 * Comparar el cco del nuevo registro con el anterior para saber si es necesario
				 * un nuevo titulo de centros de costos.
				 */

				if($cco['cod'] != $datos[$k]['cco'] and $datos[$k]['ues'] != 'P')
				{
					if($k != $inicio)
					{
						echo "</table>";
					}
					$cco['cod'] = $datos[$k]['cco'];
					getCco(&$cco,'C', $emp);//2007-06-12

					echo "<table align='center' BORDER='0'>";
					echo "<tr>";
					echo "<td class='titulo1' colspan='6'>".$cco['cod']."-".$cco['nom']."</td>";
					echo "</tr>";

				}


				/**
				 * Si es un nuevo dronum (encabezado) se hace un nueco titulo.
				 */
				if($numCns != $datos[$k]['num'] and $datos[$k]['ues'] != 'P')
				{

					echo "<tr></tr><tr>";
					echo "<td class='titulo2' >Fuente</td>";
					echo "<td class='titulo2' >Fecha</td>";
					echo "<td class='titulo2' >Doc. Matrix</td>";
					echo "<td class='titulo2' >Doc. UNIX</td>";
					echo "<td class='titulo2' colspan='2'>Comentario</td>";
					echo "</tr>";
					echo "<tr>";
					/**
					 * Segun el estado del encabezado en UNIX se asignan los colores de advertencia al usuario
					 */
					switch ($datos[$k]['ues'])
					{

						case 'M':
						//Todos los Registros en MAtrix, es decir ninguno esta en itdro
						$datos[$k]['cla']="warning";
						break;
						case 'P':
						//Todos los registros pasaron a la facturación, es decir que furon procesados exitosamente por el integrador.
						$datos[$k]['cla']="texto";
						break;

						case 'I':
						//Alguno de los registros esta inconsistente y/o hay algun registro en unix procesado pero no se genero número de docuemnto en itdrodoc
						$datos[$k]['cla']="error";
						break;

						default:
						//Esta en transicion tiene registros sin procesar
						$datos[$k]['cla']="alert";
						break;
					}
					if($datos[$k]['ues'] != 'P')
					{
						echo "<td class='".$datos[$k]['cla']."' colspan='1'>".$datos[$k]['fue']."</td>";
						echo "<td class='".$datos[$k]['cla']."' colspan='1'>".$datos[$k]['fec']."</td>";
						echo "<td class='".$datos[$k]['cla']."' colspan='1'>".$datos[$k]['num']."</td>";
						echo "<td class='".$datos[$k]['cla']."' colspan='1'>".$datos[$k]['doc']."</td>";
						echo "<td class='".$datos[$k]['cla']."' colspan='2'>".$datos[$k]['com']."</td>";
						echo "</tr>";

						echo "<tr>";
						echo "<td class='titulo3' >Lin</td>";
						echo "<td class='titulo3' >Hora</td>";
						echo "<td class='titulo3' >Código</td>";
						echo "<td class='titulo3' >Cant.</td>";
						echo "<td class='titulo3' >Estado</td>";
						echo "</tr>";
						$numCns=$datos[$k]['num'];
						$imprimio=true;
					}
				}

				if($datos[$k]['ues'] != 'P')
				{
					for($j=0;$j<$datos[$k]['numLin'];$j++)
					{
						/**
					 * Segun el estado de cada registro es la alerta del sistema
					 */
						switch ($datos[$k]['m'][$j]['ubi'])
						{
							case 'M':
							//el registro esta en MATRIX no ha pasado a itdro
							$datos[$k]['m'][$j]['clas']="warning";
							$datos[$k]['m'][$j]['err']['ok']="En Matrix";
							break;
							case 'P':
							//el registro fue procesado exitosamente por el integrador
							$datos[$k]['m'][$j]['clas']="texto";
							$datos[$k]['m'][$j]['err']['ok']="Paso a Facturación";
							break;

							case 'I':
							//El registro presento una inconsistenaci al ser procesado por el integrador
							$datos[$k]['m'][$j]['clas']="error";
							$datos[$k]['m'][$j]['err']['ok']="Presento un Error <br>al pasar a facturación:";
							//Se busca la descripción del error en UNIX, itdroinc
							$datos[$k]['m'][$j]['err']['ok']=$datos[$k]['m'][$j]['err']['ok']."<b>".infoInconcistencia($datos[$k]['num'],$datos[$k]['m'][$j]['lin'])."</b>";
							break;

							case 'S':
							//El registro esta en itdro pero no ha sido procesado por el integrador
							$datos[$k]['m'][$j]['clas']="alert";
							$datos[$k]['m'][$j]['err']['ok']="No ha sido procesado<br>por el integrador";
							break;
						}

						echo "<tr>";
						echo "<td class='".$datos[$k]['m'][$j]['clas']."' >".$datos[$k]['m'][$j]['lin']."</td>";
						echo "<td class='".$datos[$k]['m'][$j]['clas']."' >".$datos[$k]['m'][$j]['hor']."</td>";
						echo "<td class='".$datos[$k]['m'][$j]['clas']."' >".$datos[$k]['m'][$j]['art']['cod']."</td>";
						//echo "<td class='".$datos[$k]['m'][$j]['clas']."' colspan='2'>".$datos[$k]['m'][$j]['art']['nom']."</font></td>";
						echo "<td class='".$datos[$k]['m'][$j]['clas']."' >".$datos[$k]['m'][$j]['art']['can']."</td>";
						echo "<td class='".$datos[$k]['m'][$j]['clas']."' >".$datos[$k]['m'][$j]['err']['ok']."</td>";
						echo "</tr>";
					}
				}
			}
			else {
				/**
				 * Si el primer encabezado no tiene registros hay que aumentar este número, por que cuando se compara la primera vez que no
				 * existe el el centro de costos entonces llena la variable de centro de costos y crea una tabla, la segunda vez que cambia el 
				 * centro de costos debe cerrar la tabla anterior , pero si el primer encabezado no tiene registros entonces
				 * con el segundo registro ($k=1) cerrara la tabla y no se ha creado ninguna tabla, asi que podria ocurrir que 
				 * genere errores en el programa que llama la función
				 */
				$inicio=$k+1;
			}
		}//fin del for
		if(isset($imprimio))
		echo "</table>";
	}//Fin del $numNum>0
}

session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	include_once("movhos/validacion_hist.php");
	include_once("movhos/fxValidacionArticulo.php");
	include_once("movhos/registro_tablas.php");
	include_once("movhos/otros.php");
	

	

	connectOdbc(&$conex_o, 'inventarios');
	echo "</br></br><table align='center' border='0'>";
	echo "<tr><td align=center class='tituloSup' colspan='2'><b>ESTADO DOCUMENTOS DEL PACIENTE</b></td></tr>";
	echo "<tr><td align=center class='tituloSup' colspan='2'>estadoDocumentosPaciente.php Versión 2007-07-05</td></tr>";
	echo "<tr>";
	if($conex_o != 0)
	{

		if(!isset($historia))
		{
			/**
		 * Pide la historia y el ingreso
		 */
			echo "<form action='' method='POST'>";
			echo "<td><center><table border=0 width=300>";
			echo "<tr>";
			echo "<td class='titulo2' ><b>N° HISTORIA: </b><input type='text' size='10' name='historia'></td>";
			echo "<td class='titulo2' ><b>Ingreso: </b><input type='text' size='4' name='ing'></td></tr>";
			echo"<tr><td  class='titulo1' colspan='2'><input type='submit' value='ACEPTAR'></td></tr></form>";
			echo "</form>";
		}
		else
		{
			if($historia != "")
			{
				if($ing != "")
				{
					$pac['his']=$historia;

					//Valida que este activo
					$conex_f = odbc_connect('facturacion','','');
					$pac['unx'] = ValidacionHistoriaUnix(&$pac, &$warning, &$error);
					odbc_close($conex_f);

					if(isset($pac['ing']) and $pac['ing'] != $ing)
					{
						/**
						 * Es un ingreso diferente el que esta activo, 
						 * así que debe buscarse la información para el ingreso adecuado
			 			 * y asumir que este ingreo esta inactivo
			 			 */
						$pac['unx']=false;
					}
					//$pac['ing']=$ing;

					if(!$pac['unx'])
					{
						infoPaciente(&$pac,$emp);
						echo "<td class='titulo1' colspan='6'>".$pac['nom']."</td></tr>";
						echo "<tr><td class='titulo2' colspan='6'>HISTORIA INACTIVA EN UNIX </td>";
						echo "</tr>";
					}

					/**
		 * Si el paciente ya tiene el alta es por que todos los registros estan en P.
		 * Entonces no se hace la actualización de registros.
		 */
					$pac['act'] = !pacienteDeAlta(&$pac, "");


					if($pac['act'] )
					{

						/**
			 * El paciente esta activo en UNIX (inpac)
			 */
						if(isset($alta) and $alta == 'def')
						{
							$pac['alt']=true;
						}
						else
						{
							$pac['alt']=false;
						}

						echo "<td class='titulo1' colspan='6'>".$pac['nom']."</td></tr>";
						echo "<tr><td class='titulo1' colspan='6'>HISTORIA:".$pac['his']."&nbsp;&nbsp;INGRESO:".$pac['ing']."</td>";
						echo "</tr><tr><td>";
						/**
			 * Pone el estado de los registros en facfde y facfen tal y como debe sera.
			 * al tiempo que conforma un arrgleo con los registros del paciente que no esten en P, o
			 * los que pertenescan a la fecha actual.
			 * Además pone $pac['permisoAlta'] en true si todos los registros activos estan procesados. En false en otro caso.
			 */
						$tit=actualizacionDetalleRegistros($pac, &$datos);

						if($datos)
						{
							/**
				 * actualizacionDetalleRegistros lleno el arreglo con información
				 */
							if(!$pac['permisoAlta'])
							{
								echo "<br><br></td></td><tr>";
								echo "<td class='errorTitulo' colspan='6'><b>El ALTA SE PODRÁ REALIZAR UNA VEZ SOLUCIONE LOS SIGUIENTES PROBLEMAS:</b></td>";
								echo "</tr><tr><td></br>";
							}
							//Imprime en pantalla lo que hay n $datos, menos los encabezados y los registros de los encabezados este en "P"
							MostrarSTMI($pac,$datos);
						}
						$ok=true;
					}
					else
					{
						$ok=infoPaciente(&$pac,$emp);
						if($ok)
						{
							echo "<td class='titulo1' colspan='6'>".$pac['nom']."</td></tr>";
							echo "<tr><td class='titulo1' colspan='6'>HISTORIA:".$pac['his']."&nbsp;&nbsp;INGRESO:".$ing."</td>";
							echo "<tr><td class='titulo2' colspan='6'>ESTE PACIENTE YA FUE DADO DE ALTA </td>";
							echo "</tr>";
						}
						else
						{
							/**
				 * Error, el paciente con esa historia no existe en el sistema.
				 */
							echo "<tr><td class='error' colspan='6'>La historia ".$pac['his']." no tiene registros en MATRIX para el ingreso ".$pac['ing']."</td></tr>";
						}
					}
					/**
		 * Buscar registros que ya estan bien
		 */
					if($ok)
					{
						echo "<tr><td><br><br></td></td><tr><td class='tituloSup' colspan='6'>Registros que pasaron Adecuadamente a la facturación</td><tr><td>";
						registrosPaciente($pac, 'P');
						echo "</td></tr>";
					}


					echo "</table>";
					
					odbc_close($conex_f);
					odbc_close_all();
				}
				else
				{
					//Historia = ""
					echo "<td class='errorTitulo'>DEBE DIGITAR UN NÚMERO DE INGRESO</td>";
				}
			}
			else
			{
				//Ingreso = ""
				echo "<td class='errorTitulo'>DEBE DIGITAR UN NÚMERO DE HISTORIA</td>";
			}
		}
	}
	else
	{
		echo  "<td class='errorTitulo'>No hay conexión con UNIX, no puede efectuarce la transacción</td>";
	}
}
?>
</body>
</html>