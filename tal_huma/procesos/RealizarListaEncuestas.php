<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1");
include_once "funciones_talhuma.php";




include_once("root/comun.php");
include_once("root/magenta.php");
$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
$wbasedatos_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
$wbasedatos_cliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
$fecha= date("Y-m-d");
$hora = date("H:i:s");

if($inicial=='no' && $operacion=='entrada')
{
	echo"<div id='divpublicoobjetivo' >";
	echo"<table id='seleccionnicho' align='center' >";
	echo"<tr align='center' class='encabezadoTabla' ><td>Seleccione el publico objetivo para realizar evaluaci&oacute;n</td></tr>";
	echo"<tr align='center' class='fila1' ><td><a onClick='traeEmpleados()' style='cursor:pointer;'>Empleados</a></td></tr>";
	echo"<tr align='center' class='fila2' ><td align='center'><a onClick='traePacientes();' style='cursor:pointer;'>Pacientes</a></td></tr></table>";
	echo "<br>";
	echo"</div>";
	echo"<div id='divpacientes'>&nbsp;";
	echo"</div>";
	echo"<div id='divempleados'>&nbsp;";
	echo"</div>";
	echo "<div id='div_reporte' class='fila2' align='middle'  style='display:none;width:100%;cursor:default;background:none repeat scroll 0 0; overflow:auto;height: 600px' >";
	echo "<input id='nombreAgrupacionAgregar' type='hidden'>";
	echo "<br><br>";

	// div oculta utilizada para ingresar el paciente digitandolo
	echo "<div id='div_agregar_paciente' class='fila2' align='middle'  style='display:none;width:100%;cursor:default;background:none repeat scroll 0 0; overflow:auto;height: 400px' >";
	echo "<br>";
	echo "<br>";
	echo "<br>";
	echo "<table>";
	echo "<tr>";
	echo "<td class='encabezadoTabla' colspan='4' align='center'>AGREGAR PACIENTE A LISTA</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='encabezadoTabla'>Primer Nombre *</td>";
	echo "<td class='fila1'><input type='text' id='txt_primer_nombre' class='obligatorio'></td>";
	echo "<td class='encabezadoTabla' >Segundo Nombre</td>";
	echo "<td class='fila1'><input type='text' id='txt_segundo_nombre'></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='encabezadoTabla' >Primer Apellido *</td>";
	echo "<td class='fila1'><input type='text' id='txt_primer_apellido' class='obligatorio'></td>";
	echo "<td class='encabezadoTabla' >Segundo Apellido</td>";
	echo "<td class='fila1'><input type='text' id='txt_segundo_apellido'</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='encabezadoTabla' >Edad *</td>";
	echo "<td class='fila1'><input type='text' id='txt_edad' class='obligatorio' ></td>";
	echo "<td class='encabezadoTabla' >Numero de Identificación *</td>";
	echo "<td class='fila1'><input type='text' id='txt_numero_identificacion' class='obligatorio'></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='encabezadoTabla' >Nro de Historia *</td>";
	echo "<td class='fila1'><input type='text' id='txt_nro_historia' class='obligatorio'><input type='button' value='Validar' onclick='validarHistoria()'><div id='mensajeHistoria' ></div></td>";
	echo "<td class='encabezadoTabla' >Nro de Ingreso *</td>";
	echo "<td class='fila1'><input type='text' id='txt_nro_ingreso' class='obligatorio'></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='encabezadoTabla'>Telefono *</td>";
	echo "<td class='fila1'><input type='text' id='txt_telefono'></td>";
	echo "<td class='encabezadoTabla' >Habitación</td>";

	$q = 	 " SELECT 	Habcod "
			."   FROM ".$wbasedatos_movhos."_000020 ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	echo "<td class='fila1'>";
	echo "<select id='txt_' >";
	echo "<option value='No aplica'>No aplica</option>";
	while($row =mysql_fetch_array($res))
	{
		echo "<option value='".$row['Habcod']."'>".$row['Habcod']."</option>";
	}

	echo "</select>";

	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='encabezadoTabla'>Entidad*</td>";

	$q = 	 " SELECT Empcod, Empnom "
			."   FROM cliame_000024 ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	echo "<td class='fila1'>";
	echo "<select id='txt_entidad' class='obligatorio' >";
		echo "<option value=''>Ninguna</option>";
	while($row =mysql_fetch_array($res))
	{
		echo "<option value='".$row['Empnom']."' numero='".$row['Empcod']."'>".$row['Empnom']."</option>";
	}

	echo "</select>";
	echo "</td>";
	echo "<td class='encabezadoTabla' >Diagnostico</td>";
	echo "<td class='fila1'><input type='text' id='txt_diagnostico'></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='encabezadoTabla' >Afin</td>";
	echo "<td class='fila1' >";
	echo "<select id='txt_afin' >";
	echo "<option value = 'si' >si</option>";
	echo "<option value = 'no' >no</option>";
	echo "</select>";
	echo "</td>";
	echo "<td class='encabezadoTabla' >Encuesta</td>";

	$q = 	 " SELECT Fordes, Forcod "
			."   FROM ".$wbasedato."_000002 ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	echo "<td class='fila1'>";
	echo "<select id='txt_encuesta' >";
	while($row =mysql_fetch_array($res))
	{
		echo "<option value='".$row['Forcod']."'>".utf8_encode($row['Fordes'])."</option>";
	}

	echo "</select>";
	echo "</td>";
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo"<input type='button' value='Grabar'  onClick='GrabarPacientedigitado(); ' ><input type='button' value='Cancelar'  onClick='$.unblockUI();' style='width:100'>";
	echo"</div>";
	return;
}

if ($inicial=='no' AND $operacion=='grabarpacientedigitado')
{
	$q=	" 	SELECT Perano,Perper "
		."    FROM ".$wbasedato."_000009 "
		."   WHERE Perest='on'"
		."     AND Perfor='".$wtemainterno."'";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row =mysql_fetch_array($res);
	$wano=$row[0];
	$wperiodo=$row[1];

	// se consulta si la historia , el periodo y el ano , se encuentra en la tabla "wbasedato"_000049 si esta aquí es que ya esta encuentado
	$q  = "     SELECT Encced
	              FROM  ".$wbasedato."_000049
				 WHERE Enchis = '".trim($nrohistoria)."'
				   AND Encper ='".$wperiodo."'
				   AND Encano ='".$wano."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$cedula ='';
	//----------------------------------------------
	if($row =mysql_fetch_array($res))
	{
		$cedula = $row['Encced'];
	}

	// si cedula esta vacio es que no esta
	// de lo contrario no se puede grabar.

	if($cedula=='')
	{
		$mensaje = "1"  ;// no se encuentra el paciente , se puede grabar
	}
	else
		$mensaje = "0" ;// se encuentra, no se puede  grabar.


	if($mensaje=='1')
	{
		$q = "INSERT INTO ".$wbasedato."_000049 "
			."           ( 	Medico, "
			."				Fecha_data, "
			."				Hora_data, "
			."				Encced, "
			."				Encenc, "
			."				Enchis, "
			."				Encing, "
			."				Encno1, "
			."				Encno2, "
			."				Encap1, "
			."				Encap2, "
			."				Enceda, "
			."				Encent, "
			."				Encdia, "
			."				Enctel, "
			."				Enchab, "
			."				Encafi, "
			."				Enccco, "
			."				EncFec, "
			."				Encese, "
			."				Encest, "
			."				Seguridad,"
			."				Encano, "
			."				Encper, "
			."				Enctem, "
			."				Encpob )"
			."		VALUES "
			."			(   '".$wbasedato."' , "
			."			 	'".$fecha."', "
			."				'".$hora."', "
			."				'".$nroidentificacion."', "
			."				'".$encuesta."' , "
			."				'".trim($nrohistoria)."' , "
			."				'".trim($nroingreso)."', "
			."              '".$primernombre."', "
			."				'".$segundonombre."', "
			."				'".$primerapellido."',"
			."				'".$segundoapellido."', "
			."				'".$edad."',"
			."				'".$entidad."', "
			."				'".trim($diagnostico)."', "
			."				'".$telefono."', "
			."				'".$habitacion."', "
			."				'".$afin."' , "
			."				'".$wcco."', "
			."				'".$fecha."', " //mirar
			."				'Pendiente', "
			."				'on', "
			."				'C-".$wbasedato."' , "
			."				'".$wano."' ,"
			."   			'".$wperiodo."' , "
			."				'".$wtemainterno."', "
			."				'paciente' )";

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		echo "ok";
	}
	else
	{
		echo "no se grabo";
	}

	return;
}


if ($inicial=='no' AND $operacion=='validarpacientedigitado')
{

	// procedimiento que valida si una persona se encuentra ya encuestada en matrix
	// la validacion se hace por historia

	// se consulta el periodo activo del tema
	$q=	" 	SELECT Perano,Perper "
		."    FROM ".$wbasedato."_000009 "
		."   WHERE Perest='on'"
		."     AND Perfor='".$wtemainterno."'";
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row =mysql_fetch_array($res);
	//--------------------------------------------
	$wano=$row[0]; // se almacena el año
	$wperiodo=$row[1]; // se almacena el periodo

	// se consulta si la historia , el periodo y el ano , se encuentra en la tabla "wbasedato"_000049 si esta aquí es que ya esta encuentado
	$q  = "     SELECT Encced
	              FROM  ".$wbasedato."_000049
				 WHERE Enchis = '".trim($nrohistoria)."'
				   AND Encper ='".$wperiodo."'
				   AND Encano ='".$wano."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$cedula ='';
	//----------------------------------------------
	if($row =mysql_fetch_array($res))
	{
		$cedula = $row['Encced'];
	}

	// si cedula esta vacio es que no esta
	// de lo contrario no se puede grabar.

	if($cedula=='')
	{
		echo $mensaje = "1"  ;// no se encuentra el paciente , se puede grabar
	}
	else
		echo $mensaje = "0" ;// se encuentra, no se puede  grabar.

	return;
}

if (isset($wlistaempleados) AND  $wlistaempleados=='si')
{
	$q=	" 	SELECT Perano,Perper "
		."    FROM ".$wbasedato."_000009 "
		."   WHERE Perest='on'"
		."     AND Perfor='".$wtemainterno."'";
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row =mysql_fetch_array($res);

	$wano=$row[0];
	$wperiodo=$row[1];

	// Consulta el parámetro de programa multiples encuesta 

	$sql = "SELECT Detval
			FROM root_000051
			WHERE Detemp = '{$wemp_pmla}'
			AND Detapl = 'programa_multiples_encuestas'; ";
	
	$res = mysql_query($sql, $conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row =mysql_fetch_assoc($res);

	$temasConMultipleEncuesta = explode(',', $row['Detval']);

	in_array($wtema, $temasConMultipleEncuesta) ? $programaMultiplesEncuestas = on : $programaMultiplesEncuestas = off;

	// si el tipo de formato es una encuesta interna
	if($wtipointerno =='03')
	{
		$q  = "SELECT  Enchis, Encenc,Encese"
			. "  FROM ".$wbasedato."_000049 "
			."  WHERE  Encpob ='empleado' "
			."    AND  Encper ='".$wperiodo."' "
			."    AND  Encano ='".$wano."' "
			."    AND  Enctem = '".$wtemainterno."' ";
	}
	else
	{
	//--------
		$q  = "SELECT  Empcod, Empeva, Empeve"
			. "  FROM ".$wbasedato."_000055 "
			."  WHERE  Empano = '".$wano."' "
			."    AND  Empper = '".$wperiodo."' "
			."    AND  Emptem = '".$wtemainterno."' ";
	}

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	if($programaMultiplesEncuestas === on)
	{
		while($row =mysql_fetch_array($res))
		{
			$pacientesprogramados[$row[0]] =  $row[1];
			$encuestasProgramadas[$row[0]][$row[1]] = $row[2];
		}
	}
	else
	{
		while($row =mysql_fetch_array($res))
		{
			$pacientesprogramados[$row[0]] =  $row[1];
			$pacientescerrados[$row[0]] =  $row[2];
		}
	}
	
	echo '<div id="div_titulo" align="left" style="font-weight:bold; text-align: Left; ">LISTA DE EMPLEADOS</div>';
	echo '<div id="div_empleados" align="left" class="borderDiv displ" >';
	echo"<table id='tabemple'  align='center'>";

	$q  = "SELECT Ideuse,Ideno1,Ideno2,Ideap1,Ideap2,Cardes "
		. "  FROM talhuma_000013, root_000079"
		. " WHERE Ideest = 'on' "
		. "   AND Carcod = Ideccg "
		. "   AND Idecco = '".$wcco."'"
		. " ORDER BY TRIM(Ideno1), TRIM(Ideno2), TRIM(Ideap1), TRIM(Ideap2)" ;

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	$i = 0;
	echo "<tr class='encabezadoTabla'>";
	echo "<td></td>";
	echo "<td>Codigo</td>";
	echo "<td>Nombre</td>";
	echo "<td>Cargo</td>";
	echo "<td>Evaluacion</td>";
	echo "</tr>";
	while($row =mysql_fetch_array($res))
	{
		$row['Ideno1'] = utf8_decode($row['Ideno1']);
		$row['Ideno2'] = utf8_decode($row['Ideno2']);
		$row['Ideap1'] = utf8_decode($row['Ideap1']);
		$row['Ideap2'] = utf8_decode($row['Ideap2']);
		
		$contenedor= "nombreevaluacion-".$row['Ideuse']."";
		$contenedor=str_replace(' ','',$contenedor);

		if (($i%2)==0)
	    {
			$wcf="fila1";  // color de fondo de la fila
	    }
		else
	    {
			$wcf="fila2"; // color de fondo de la fila
		}

		if($programaMultiplesEncuestas === on)
		{	
			if ($encuestasProgramadas[$row['Ideuse']] != null)
			{
				if (in_array('cerrado', $encuestasProgramadas[$row['Ideuse']]))
				{
					$estadodesabilitado = 'Disabled';
				}
				else
				{
					$estadodesabilitado = '';
				}
			}
			else
			{
				$estadodesabilitado = '';
			}			
		}
		else
		{
			if ($pacientescerrados[$row['Ideuse']] =='cerrado')
			{
				$estadodesabilitado = 'Disabled';
			}
			else
			{
				$estadodesabilitado = '';
			}
		}

		

	    if(@array_key_exists($row['Ideuse'],$pacientesprogramados))
		{
			$q = " SELECT ".$wbasedato."_000002.Forcod, ".$wbasedato."_000002.Fordes"
				."   FROM ".$wbasedato."_000002, ".$wbasedato."_000042 "
				." 	WHERE  (".$wbasedato."_000042.Fortip = '04' OR ".$wbasedato."_000042.Fortip = '03' OR ".$wbasedato."_000042.Fortip = '05')  "
				."    AND  ".$wbasedato."_000042.Forcod = ".$wbasedato."_000002.Fortip "
				."    AND  ".$wbasedato."_000002.Fortip = '".$wtemainterno."'";

			$res1 = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

			echo"<tr>";
			if($programaMultiplesEncuestas === on)
			{
				echo "<td class='".$wcf."' align='center'><input ".$estadodesabilitado." type='checkbox' checked onClick='agregarSelectEvaluaciones(this)'></td>";
			}
			else
			{
				echo"<td class='".$wcf."' align='center'><input ".$estadodesabilitado." type='checkbox'  checked id='check-".$row['Ideuse']."' value='".$row['Ideuse']."' onClick='seleccionevaluacion(this,\"".$contenedor."\",\"".$row['Ideuse']."\",\"".$row['Ideno1']."\" ,\"".$row['Ideno2']."\",\"".$row['Ideap1']."\", \"".$row['Ideap2']."\")'></td>";
			}
			echo"<td class='".$wcf."' >".$row['Ideuse']."</td>";
			echo"<td class='".$wcf."' no1='".$row['Ideno1']."' no2='".$row['Ideno2']."' ap1='".$row['Ideap1']."' ap2='".$row['Ideap2']."'>".$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']."</td>";
			echo"<td class='".$wcf."'>".$row['Cardes']."</td>";
			echo"<td class='".$wcf."'><div id='".$contenedor."'>";

			if($programaMultiplesEncuestas === on)
			{
				echo "<select multiple class='multiencuesta'>";
				while($row1 =mysql_fetch_array($res1))
				{
					if(array_key_exists($row1['Forcod'], $encuestasProgramadas[$row['Ideuse']])) 
					{
						$deshabilitado = '';
						if($encuestasProgramadas[$row['Ideuse']][$row1['Forcod']] == 'cerrado')
						{
							$deshabilitado = 'disabled="disabled"';
						}
						echo "<option selected='selected' {$deshabilitado} value='".$row1['Forcod']."'>".htmlentities($row1['Fordes'])."</option>";
					}
					else
					{
						echo "<option  value='".$row1['Forcod']."'>".$row1['Fordes']."</option>";
					}
				}
			}			
			else
			{
				// echo "<select ".$estadodesabilitado." onchange='cambiaUltimaEvaluacion(this, \$(this).parents(\"tr\"));'>";
				echo "<select ".$estadodesabilitado." onchange='cambiaUltimaEvaluacion(this, \$(this).parents(\"tr\"));' evaluacionAnterior='".$pacientesprogramados[$row['Ideuse']]."'>";
				echo "<option value=''>Seleccione una evaluaci&oacute;n</option>";
				
				while($row1 =mysql_fetch_array($res1))
				{
					if($row1['Forcod']==$pacientesprogramados[$row['Ideuse']]) 
					{
						echo "<option  selected value='".$row1['Forcod']."'>".$row1['Fordes']."</option>";
					}
					else
					{
						echo "<option  value='".$row1['Forcod']."'>".$row1['Fordes']."</option>";
					}
				}
			}

			echo "</select>";
			echo"</div></td>";
			echo "</tr>";

		}
		else
		{
			echo"<tr>";
			if($programaMultiplesEncuestas === on)
			{
				echo"<td class='".$wcf."' align='center'><input type='checkbox' onClick='agregarSelectEvaluaciones(this)'></td>";
			}
			else
			{
				echo"<td class='".$wcf."' align='center'><input type='checkbox'  id='check-".$row['Ideuse']."' value='".$row['Ideuse']."' onClick='seleccionevaluacion(this,\"".$contenedor."\",\"".$row['Ideuse']."\",\"".$row['Ideno1']."\" ,\"".$row['Ideno2']."\",\"".$row['Ideap1']."\", \"".$row['Ideap2']."\")'></td>";
			}
			echo"<td class='".$wcf."' >".$row['Ideuse']."</td>";
			echo"<td class='".$wcf."' no1='".$row['Ideno1']."' no2='".$row['Ideno2']."' ap1='".$row['Ideap1']."' ap2='".$row['Ideap2']."'>".$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']."</td>";
			echo"<td class='".$wcf."'>".$row['Cardes']."</td>";
			echo"<td class='".$wcf."'><div id='".$contenedor."'></div></td>";
			echo"</tr>";
		}
		$i++;
	}
	echo"</table>";
	echo '</div >';
	return;
}

//----------si selecciono pacientes se pinta esto
if (isset($wobjetivo)  AND $wobjetivo == 'pacientes')
{
	// tabla principal, contiene todas las demas , organizando la estructura
	echo "<table><tr><td>";
	echo "<div id='divppal'  class='borderDiv displ' align='center'>";
	echo "<table class='fila1' id='tabppal'  align='center' >";
	// Primer tr fechas de filtrado
	echo "<tr><td>";
	$fechaactual= date('Y-m-d');

	$wfecha_i = $fechaactual;
	$wfecha_f = $fechaactual;

	echo "<table align='center' id='tabfechas'>";
	echo "<tr align='center' class='fila1'>";
	echo "<td align='center'><b>Fecha Inicial: </b>";
	campofechaDefecto("wfecha_i",$wfecha_i);
	echo "</td>";
	echo "<td align='center'><b>Fecha final: </b>";
	campofechaDefecto("wfecha_f",$wfecha_f);
	echo "</td>";
	echo "</tr>";
	echo "</table>";

	// fin primer tr
	echo "</td></tr>";
	// segundo tr centros de costos
	// consulta de centro de costos

	$q = " SELECT Ccocod,Cconom"
	. "   FROM ".$wbasedatos_movhos."_000011 "
	. "  WHERE Ccoest='on' "
	. "     OR (Ccohos='on' OR Ccourg='on' OR Ccocir='on') ";


	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	//-----------------------------
	echo "<tr><td>";
	echo "<table align='center'id='tabccoclinicos'>";
	echo "<tr align='center' class='fila1'>";
	echo "<td align='right'><b>Centro de Costos:</b>";
	echo "</td>";
	echo "<td align='right'>";
	echo "<select id='wcco' >";
	while($row =mysql_fetch_array($res))
	{
		echo "<option value='".$row['Ccocod']."'>".$row['Cconom']."</option>";
	}
	echo "</select>";
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "<tr align='center'>";	
	echo "	<td>";
	echo "		<table align='center'>";
	echo "			<tr align='center'>";	
	echo "				<td align='right'><b>Información desde:</b>";
	echo "				<td align='center' colspan='1'><input type='radio' checked name='momento' value='ingreso' onclick='mostrarfiltrodiagnostico(this)'/>Ingreso</td>";
	echo "				<td align='left' colspan='1'><input type='radio' name='momento' value='egreso' onclick='mostrarfiltrodiagnostico(this)'/>Egreso</td>";
	echo "			</tr>";
	echo "		</table>";
	echo "	</td>";
	echo "</tr>";
	echo "<tr align='center' id='diagnostico' cargado='false' style='display:none;'>";	
	echo "	<td>";
	echo "		<table>";
	echo "			<tr>";	
	echo "				<td align='right'><b>Diagnóstico:</b>";
	echo "				<td align='center' class='input--grande'><input id='selectDiagnostico'></td>";
	echo "			</tr>";
	echo "		</table>";
	echo "	</td>";
	echo "</tr>";
	echo "<tr align='center'>";
	echo "<td align='center' colspan='2'><input type='button' Value='Buscar' onClick='ConsultarLista()'/></td>";
	echo "</tr>";
	
	// fin  segundo tr
	echo "</td></tr>";
	echo "<tr><td>";
	echo "</td></tr>";
	echo "</table>";
	echo "</div>";
	echo "</td></tr>";
	echo "</table>";
	echo "<br>";
	echo "<br>";
	echo "<br>";
	// tabla de respuesta  a la consulta
	echo "<table  align='center'>";
	echo"<tr><td>";
	echo "<div id='divlista' align='center'>";
	echo "</div>";
	echo"</td></tr>";
	echo "</table>";
	return;
	//-------------------------
}
//------------Se pinta si va a seleccionar empleados
if (isset($wobjetivo)  AND $wobjetivo == 'empleados')
{
	echo "<table><tr><td>";
	echo "<div id='divppal'  class='borderDiv displ' align='center'>";
	echo"<table border='0' cellspacing='1px' cellpadding='0' align='center'>";

	echo"<tr>
			<td class='encabezadoTabla' style='width:235px;'>&nbsp;Buscar centro de costos:</td>
			<td class='fila2' style='width:450px;' align='center'>&nbsp;
				<img title='Busque el nombre o parte del nombre del centro de costo' width='12 ' height='12' border='0' src='../../images/medical/HCE/lupa.PNG' />
				<input id='wnomccosto' name='wnomccosto' value='' size='60' onkeypress='return enterBuscar(\"wnomccosto\",\"wccostos_pfls\",\"costos\",\"load_costo\",event);' onfocus='cambioImagen(\"ccsel\",\"ccload\");' onBlur='recargarLista(\"wnomccosto\",\"wccostos_pfls\",\"load_costo\"); cambioImagen(\"ccload\",\"ccsel\");' />
			</td>
		</tr>";

	echo"<tr>
			<td class='encabezadoTabla'>&nbsp;Seleccionar centro de costos:</td>
			<td class='fila1'>
				<table border='0' cellspacing='0' cellpadding='0' >
					<tr>
						<td>
							<div id='ccsel'><img title='Seleccione un centro de costos' width='10 ' height='10' border='0' src='../../images/medical/iconos/gifs/i.p.next[1].gif' /></div>
							<div id='ccload' style='display:none;' ><img width='10 ' height='10' border='0' src='../../images/medical/ajax-loader9.gif' /></div>
						</td>
						<td>
							<select style='width:430px;' id='wccostos_pfls' name='wccostos_pfls' onChange='traelistaempleado(this)'>
								<?php
									echo $centro_costos;
									// foreach($centro_costos as $key => $value)
									// {
										// echo '<option value='$key' >$value</option>';
									// }
								?>
							</select>
						</td>
					</tr>
				</table>
			</td>
		</tr>";
	echo"</table>";
	echo"</td></tr>";
	echo"</table>";
	// tabla de respuesta  a la consulta
	echo "<table  align='center'>";
	echo"<tr><td>";
	echo "<div id='divlista' align='center'>";
	echo "</div>";
	echo"</td></tr>";
	echo "</table>";
	return;
	//-------------------------
}

if (isset($wfecha_i) AND isset($wfecha_f) AND isset($wcco) AND $woperacion == 'resultadosLista')
{
	$q=	" 	SELECT Perano,Perper "
		."    FROM ".$wbasedato."_000009 "
		."   WHERE Perest='on'"
		."     AND Perfor='".$wtemainterno."'";
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row =mysql_fetch_array($res);
	$wano=$row[0];
	$wperiodo=$row[1];

	$q  = "SELECT  Encced, Encenc"
		. "  FROM ".$wbasedato."_000049 "
		."  WHERE  Encfec  BETWEEN  '".$wfecha_i."' AND '".$wfecha_f."' "
		."    AND Enctem = '".$wtemainterno."' 
			  AND Encese !='cerrado'" ;

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	while($row =mysql_fetch_array($res))
	{
		$pacientesprogramados[$row['Encced']] =  $row['Encenc'];
	}

	switch ($wfuente) {
		case 'ingreso':
			// Resultados de la consulta
			// trae los pacientes que esten actualmente hospitalizados en un rango de tiempo  (clinica oriori ='01' empresa clinica las americas)  siempre y cuando tenga un movimiento de pisos unido a
			// A los que hayan sido hospitalizados y tengan historia clinica
			//
			$q =  " SELECT ".$wbasedatos_movhos."_000017.Fecha_data,pacno1, pacno2, pacap1, pacap2, Pactid, Pacced, Oriced, Oritid, Orihis, Oriing, Eyrhis, Eyring, Eyrsor AS origen, Eyrsde AS destino, Eyrhor, Eyrhde, Habcod, Habcco ,Ccocod, Cconom, Ingnre, Pacnac,Ubimue,Ingtel,Ubifad,Ingres"
			. "   FROM root_000036, root_000037, ".$wbasedatos_movhos."_000017, ".$wbasedatos_movhos."_000020, ".$wbasedatos_movhos."_000011,".$wbasedatos_movhos."_000016,".$wbasedatos_movhos."_000018"
			. "  WHERE Oriced = Pacced "
			. "    AND Oritid = Pactid "
			. "    AND Orihis = Eyrhis "
			. "    AND Oriing = Eyring "
			. "    AND Eyrsde = Habcco "
			. "    AND Eyrhde = Habcod "
			. "    AND Habcco = Ccocod "
			. "    AND Oriori = '01' "
			. "    AND Ubihis = Orihis "
			. "    AND Ubiing = Oriing "
			. "	   AND Inghis = Orihis "
			. "    AND Inging = Oriing "
			. "    AND Eyrest = 'on' "
			. "    AND Eyrtip = 'recibo'  "
			. "    AND Ccocod = '".$wcco."'  "
			. "	   AND Ccourg !='on'"
			. "    AND Ccohos = 'on' "
			. "    AND ".$wbasedatos_movhos."_000017.Fecha_data   BETWEEN '".$wfecha_i."' AND '".$wfecha_f."' "
			."   UNION "              //Ene 28 2016
			." SELECT A.Fecha_data, pacno1, pacno2, pacap1, pacap2, Pactid, Pacced, Oriced, Oritid, Orihis, Oriing, Orihis, Oriing, '' AS origen, '' AS destino, '', ' ' Eyrhde, ' ' Habcod, ' ' Habcco , Mtrcci, Cconom, Ingnre, Pacnac, '' Ubimue, Ingtel, Ubifad,Ingres"
			. "   FROM root_000036, root_000037, hce_000022 as A ,".$wbasedatos_movhos."_000016,".$wbasedatos_movhos."_000011,".$wbasedatos_movhos."_000018 "
			. "  WHERE A.Fecha_data   BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'
				AND oriori = '".$wemp_pmla."'
				AND mtrhis = ubihis
				AND mtring = ubiing
				AND mtrhis = inghis
				AND mtring = inging
				AND mtrhis = orihis
				AND oriori = '".$wemp_pmla."'
				AND mtrcci = ccocod
				AND mtrcci = '".$wcco."'
				AND oriced = pacced
				AND oritid = pactid
				ORDER BY TRIM(pacno1), TRIM(pacno2), TRIM(pacap1), TRIM(pacap2) ";
			break;
		case 'egreso':
			$tablaDiagnostico = "";
			$condicionesDiagnostico = "";
			if($wdiagnostico != '')
			{
				$condicionesDiagnostico = " AND Diahis = Egrhis
											AND Diaing = Egring
											AND Diatip = 'P'
											AND Diacod = '{$wdiagnostico}' ";
				$tablaDiagnostico = ", {$wbasedatos_cliame}_000109 ";
			}
			$q = "	SELECT A.Egrfee AS Fecha_data, pacno1, pacno2, pacap1, pacap2, Pactdo AS Pactid, Pacdoc AS Pacced, Pacdoc AS Oriced, Pactdo 		AS Oritid, Egrhis AS 		Orihis, Egring AS Oriing, '' AS origen, '' AS destino, ' ' Eyrhde, ' ' Habcod, ' ' 		Habcco, Ccocod, Cconom, Pacfna AS Pacnac, 			Ingnre, Ingres, '' Ubimue, Ingtel, Ubifad
					FROM {$wbasedatos_cliame}_000108 AS A, {$wbasedatos_cliame}_000100, {$wbasedatos_cliame}_000112, {$wbasedatos_movhos}_000011, {$wbasedatos_movhos}_000016, {$wbasedatos_movhos}_000018 {$tablaDiagnostico}
					WHERE A.Egrfee BETWEEN '{$wfecha_i}' AND '{$wfecha_f}'
					AND Pachis = Egrhis
					AND Serhis = Egrhis
					AND Sering = Egring
					AND Inghis = Egrhis
					AND Ubihis = Egrhis 
					AND Ubiing = Egring 
					AND Inging = Egring
					AND Sercod = '{$wcco}'
					AND Seregr = 'on'
					AND Ccocod = Sercod
					{$condicionesDiagnostico}
					ORDER BY TRIM(pacno1), TRIM(pacno2), TRIM(pacap1), TRIM(pacap2) ";
			break;
	}
	
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	echo "<table >";
	echo "<tr>";
	echo "<td>";
	echo '<div id="div_titulo" align="left" style="font-weight:bold; text-align: Left; ">LISTA DE PACIENTES</div>';
	echo "</td>";
	echo "<td><input type='button' value='Agregar Paciente' onclick=fnMostrar2() align='right'></td>";
	echo "</tr>";
	echo "</table>";
	echo '<div id="div_pacientes" align="left" class="borderDiv displ" >';
	echo "<table align='center' id='tablista'>";
	echo "<tr class='encabezadoTabla'>";
	echo " <td >Seleccionar</td>";
	echo " <td >Nombre</td>";
	echo " <td>Tipo Doc</td>";
	echo " <td>N. Documento</td>";
	echo " <td>Edad</td>";
	echo " <td>Historia</td>";
	echo " <td>Ingreso</td>";
	echo " <td>Entidad</td>";
	echo " <td>Habitacion</td>";
	echo " <td>Telefono</td>";
	echo " <td>Nombre Servicio</td>";
	echo " <td>Afin</td>";
	echo " <td>Fallecido</td>";
	echo " <td wrap='nowrap'>Fecha Entrada</td>";
	echo " <td>Fecha Alta</td>";
	echo " <td>ultima encuesta</td>";
	echo " <td style= 'background-color : white;'>&nbsp;&nbsp;</td>";
	echo " <td>Encuesta</td>";
	echo "</tr>";

    $i=0;
	while($row =mysql_fetch_array($res))
	{
		$fechaultimaencuesta='';
		$query2= "SELECT Encfce "
				."  FROM  ".$wbasedato."_000049 "
				." WHERE  Enchis='".$row['Orihis']."' "
				."   AND Encano = '".$wano."' "
				."   AND Encper = '".$wperiodo."' "
				."   AND Enctem = '".$wtemainterno."' " ;

		if($wbasedato=='encumage')
		{
		 	$query2= "SELECT Encfce ,Encese , MAX(Encper) as Encper  "
					."  FROM  ".$wbasedato."_000049 "
					." WHERE  Enchis='".$row['Orihis']."' "
					."   AND Enctem = '".$wtemainterno."' " ;
		
		}
		$resquery = mysql_query($query2,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$query2." - ".mysql_error());
		$rowquery =mysql_fetch_array($resquery);

		$fechaultimaencuesta=$rowquery['Encfce'];
		if($fechaultimaencuesta=='0000-00-00')
		{
			$fechaultimaencuesta='Pendiente por cerrar';
		}
	
		if($wbasedato=='encumage')
		{
			if($rowquery['Encese']=='pendiente')
			{
				$fechaultimaencuesta='Pendiente por cerrar';
			}
			if($rowquery['Encese']=='rechazado')
			{
				$fechaultimaencuesta='rechazado';
			}
		}

		if (($i%2)==0)
		{
			$wcf="fila1";  // color de fondo de la fila
		}
		else
		{
			$wcf="fila2"; // color de fondo de la fila
		}

		$contenedor= "nombreEncuesta-".$row['Orihis']."-".$row['Oriing']."-".$row['Pacced']."-".$row['Habcod']."";
		$contenedor=str_replace(' ','',$contenedor);

		//edad del paciente
		$wfnac=(integer)substr($row['Pacnac'],0,4)*365 +(integer)substr($row['Pacnac'],5,2)*30 + (integer)substr($row['Pacnac'],8,2);
		$wfhoy=(integer)date("Y")*365 +(integer)date("m")*30 + (integer)date("d");
		$wedad=(($wfhoy - $wfnac)/365);
		$wedad1=round((($wfhoy - $wfnac)/365));
		//-------------------

		// afin o no
		$wtpa='';
		clienteMagenta($row['Pacced'],$row['Pactid'],$wtpa,$wcolorpac);
	
		$telefonocliame ='';
		$qtelefono = "SELECT Pactel  FROM cliame_000100 WHERE  Pacdoc = '".$row['Pacced']."' AND Pachis='".$row['Orihis']."' AND Pactdo='".$row['Pactid']."'";
		$restelefono = mysql_query($qtelefono,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qtelefono." - ".mysql_error());
		if($rowtelefono =mysql_fetch_array($restelefono))
		{
			$telefonocliame = $rowtelefono['Pactel'];
		}

		if(@array_key_exists($row['Pacced'],$pacientesprogramados))
		{
			$q = " SELECT ".$wbasedato."_000002.Forcod, ".$wbasedato."_000002.Fordes"
				."   FROM ".$wbasedato."_000002, ".$wbasedato."_000042 "
				." 	WHERE  ".$wbasedato."_000042.Fortip = '03' "
				."    AND  ".$wbasedato."_000042.Forcod = ".$wbasedato."_000002.Fortip ";

			$res1 = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

			if($fechaultimaencuesta == "Pendiente por cerrar" )
			{
				echo "<tr class='".$wcf."'>";
				echo "<td align='center'><input type='checkbox' checked id='check-".$row['Orihis']."' value='".$row['Orihis']."' onClick='guardarenLista(\"".$row['pacno1']."\",\"".$row['pacno2']."\",\"".$row['pacap1']."\",\"".$row['pacap2']."\",\"".$row['Pacced']."\",\"".$row['Pactid']."\",\"".$row['Habcod']."\",\"".$row['Orihis']."\",\"".$row['Oriing']."\",\"".$row['Ccocod']."\",\"".$row['Fecha_data']."\",\"".$contenedor."\",this,\"".$telefonocliame."\",\"".$wedad1."\",\"".$row['Ingnre']."\",\"".$wtpa."\",\"".$fechaultimaencuesta."\",\"".$row['Ingres']."\)'></td>";
				echo " <td>".$row['pacno1']." ".$row['pacno2']." ".$row['pacap1']." ".$row['pacap2']."</td>";
				echo " <td>".$row['Pactid']."</td>";
				echo " <td>".$row['Pacced']."</td>";
				echo " <td>".$wedad1."</td>";
				echo " <td>".$row['Orihis']."</td>";
				echo " <td>".$row['Oriing']."</td>";
				echo " <td>".$row['Ingnre']."</td>";
				echo " <td>".$row['Habcod']."</td>";
				echo " <td>".$telefonocliame."</td>";
				echo " <td>".$row['Cconom']."</td>";
				echo " <td ><font color=".$wcolorpac.">".$wtpa."</font></td>";
				echo " <td>".(($row['Ubimue']=='on') ? "si" : "no")."</td>";
				echo " <td>".$row['Fecha_data']."</td>";
				echo "<td>".$row['Ubifad']."</td>";
				echo " <td>".$fechaultimaencuesta."</td>";
				echo " <td style= 'background-color : white;'>&nbsp;&nbsp;</td>";
				echo " <td><div id='".$contenedor."'>";
				if($wbasedato=='encumage')
				{
					echo "<select onchange='cambiaUltimaEncuesta(this, \$(this).parents(\"tr\"),\"".$rowquery['Encper']."\");' evaluacionAnterior='".$pacientesprogramados[$row['Pacced']]."'>";
				}
				else
				{
					echo "<select onchange='cambiaUltimaEncuesta(this, \$(this).parents(\"tr\"));' evaluacionAnterior='".$pacientesprogramados[$row['Pacced']]."'>";
				}
			
				echo "<option value=''>Seleccione una encuesta</option>";
				
				$i=0;
				while($row1 =mysql_fetch_array($res1))
				{
					if($row1['Forcod']==$pacientesprogramados[$row['Pacced']])
					{
					echo "<option selected value='".$row1['Forcod']."'>".$row1['Fordes']."</option>";
					}
					else
					{
					echo "<option  value='".$row1['Forcod']."'>".$row1['Fordes']."</option>";
					}
					$i++;
				}
				echo "</select>";
				echo"</div></td>";
				echo "</tr>";
			}
			else
			{
				echo "<tr class='".$wcf."'>";
				echo "<td align='center'><input type='checkbox' checked id='check-".$row['Orihis']."' value='".$row['Orihis']."' onClick='guardarenLista(\"".$row['pacno1']."\",\"".$row['pacno2']."\",\"".$row['pacap1']."\",\"".$row['pacap2']."\",\"".$row['Pacced']."\",\"".$row['Pactid']."\",\"".$row['Habcod']."\",\"".$row['Orihis']."\",\"".$row['Oriing']."\",\"".$row['Ccocod']."\",\"".$row['Fecha_data']."\",\"".$contenedor."\",this,\"".$telefonocliame."\",\"".$wedad1."\",\"".$row['Ingnre']."\",\"".$wtpa."\",\"".$fechaultimaencuesta."\",\"".$row['Ingres']."\")'></td>";
				echo " <td>".$row['pacno1']." ".$row['pacno2']." ".$row['pacap1']." ".$row['pacap2']."</td>";
				echo " <td>".$row['Pactid']."</td>";
				echo " <td>".$row['Pacced']."</td>";
				echo " <td>".$wedad1."</td>";
				echo " <td>".$row['Orihis']."</td>";
				echo " <td>".$row['Oriing']."</td>";
				echo " <td>".$row['Ingnre']."</td>";
				echo " <td>".$row['Habcod']."</td>";
				echo " <td>".$telefonocliame."</td>";
				echo " <td>".$row['Cconom']."</td>";
				echo " <td ><font color=".$wcolorpac.">".$wtpa."</font></td>";
				echo " <td>".(($row['Ubimue']=='on') ? "si" : "no")."</td>";
				echo " <td>".$row['Fecha_data']."</td>";
				echo "<td>".$row['Ubifad']."</td>";
				echo " <td>".$fechaultimaencuesta."</td>";
				echo " <td style= 'background-color : white;'>&nbsp;&nbsp;</td>";
				echo " <td><div id='".$contenedor."'>";
				if($wbasedato=='encumage')
				{
					echo "<select onchange='cambiaUltimaEncuesta(this, \$(this).parents(\"tr\"),\"".$rowquery['Encper']."\");' evaluacionAnterior='".$pacientesprogramados[$row['Pacced']]."'>";
				}
				else
				{
					echo "<select onchange='cambiaUltimaEncuesta(this, \$(this).parents(\"tr\"));' evaluacionAnterior='".$pacientesprogramados[$row['Pacced']]."'>";
				}
		
				echo "<option value=''>Seleccione una encuesta</option>";
				
				$i=0;
				while($row1 =mysql_fetch_array($res1))
				{
					if($row1['Forcod']==$pacientesprogramados[$row['Pacced']])
					{
						echo "<option selected value='".$row1['Forcod']."'>".$row1['Fordes']."</option>";
					}
					else
					{
						echo "<option  value='".$row1['Forcod']."'>".$row1['Fordes']."</option>";
					}
					$i++;
				}
				echo "</select>";
				echo"</div></td>";
				echo "</tr>";
			}
		}
		else
		{
			if ( $fechaultimaencuesta == '' or $fechaultimaencuesta == 'Pendiente por cerrar' or $fechaultimaencuesta=='0000-00-00' )
			{
				echo "<tr class='".$wcf."'>";
				echo "<td align='center'><input type='checkbox'   id='check-".$row['Orihis']."' value='".$row['Orihis']."' onClick='guardarenLista(\"".$row['pacno1']."\",\"".$row['pacno2']."\",\"".$row['pacap1']."\",\"".$row['pacap2']."\",\"".$row['Pacced']."\",\"".$row['Pactid']."\",\"".$row['Habcod']."\",\"".$row['Orihis']."\",\"".$row['Oriing']."\",\"".$row['Ccocod']."\",\"".$row['Fecha_data']."\",\"".$contenedor."\",this,\"".$telefonocliame."\",\"".$wedad1."\",\"".$row['Ingnre']."\",\"".$wtpa."\",\"".$fechaultimaencuesta."\",\"".$row['Ingres']."\")'></td>";
				echo "<td>".$row['pacno1']." ".$row['pacno2']." ".$row['pacap1']." ".$row['pacap2']."</td>";
				echo "<td>".$row['Pactid']."</td>";
				echo "<td>".$row['Pacced']."</td>";
				echo "<td>".$wedad1."</td>";
				echo "<td>".$row['Orihis']."</td>";
				echo "<td>".$row['Oriing']."</td>";
				echo "<td>".$row['Ingnre']."</td>";
				echo "<td>".$row['Habcod']."</td>";
				echo "<td>".$telefonocliame."</td>";
				echo "<td>".$row['Cconom']."</td>";
				echo "<td><font color=".$wcolorpac.">".$wtpa."</font></td>";
				echo "<td>".(($row['Ubimue']=='on') ? "si" : "no")."</td>";
				echo "<td>".$row['Fecha_data']."</td>";
				echo "<td>".$row['Ubifad']."</td>";
				echo "<td>".$fechaultimaencuesta."</td>";
				echo "<td style= 'background-color : white;'>&nbsp;&nbsp;</td>";
				echo "<td><div id='".$contenedor."'></div></td>";
				echo "</tr>";
			}
			else
			{
				$q = " SELECT ".$wbasedato."_000002.Forcod, ".$wbasedato."_000002.Fordes"
				."   FROM ".$wbasedato."_000002, ".$wbasedato."_000042 "
				." 	WHERE  ".$wbasedato."_000042.Fortip = '03' "
				."    AND  ".$wbasedato."_000042.Forcod = ".$wbasedato."_000002.Fortip ";

				$res1 = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				echo "<tr class='".$wcf."'>";
			
				// if($wbasedato == "encumage")
				// {
						// echo "<td align='center'>4<input type='checkbox' id='check-".$row['Orihis']."' value='".$row['Orihis']."' onClick='guardarenLista(\"".$row['pacno1']."\",\"".$row['pacno2']."\",\"".$row['pacap1']."\",\"".$row['pacap2']."\",\"".$row['Pacced']."\",\"".$row['Pactid']."\",\"".$row['Habcod']."\",\"".$row['Orihis']."\",\"".$row['Oriing']."\",\"".$row['Ccocod']."\",\"".$row['Fecha_data']."\",\"".$contenedor."\",this,\"".$telefonocliame."\",\"".$wedad1."\",\"".$row['Ingnre']."\",\"".$wtpa."\",\"".$fechaultimaencuesta."\",\"".$row['Ingres']."\" , \"".$rowquery['Encper']."\" )'></td>";
				
				// }
				// else
				// {
				echo "<td align='center'><input type='checkbox' id='check-".$row['Orihis']."' value='".$row['Orihis']."' onClick='guardarenLista(\"".$row['pacno1']."\",\"".$row['pacno2']."\",\"".$row['pacap1']."\",\"".$row['pacap2']."\",\"".$row['Pacced']."\",\"".$row['Pactid']."\",\"".$row['Habcod']."\",\"".$row['Orihis']."\",\"".$row['Oriing']."\",\"".$row['Ccocod']."\",\"".$row['Fecha_data']."\",\"".$contenedor."\",this,\"".$telefonocliame."\",\"".$wedad1."\",\"".$row['Ingnre']."\",\"".$wtpa."\",\"".$fechaultimaencuesta."\",\"".$row['Ingres']."\")'></td>";
			
				// }
				echo "<td>".$row['pacno1']." ".$row['pacno2']." ".$row['pacap1']." ".$row['pacap2']."</td>";
				echo "<td>".$row['Pactid']."</td>";
				echo "<td>".$row['Pacced']."</td>";
				echo "<td>".$wedad1."</td>";
				echo "<td>".$row['Orihis']."</td>";
				echo "<td>".$row['Oriing']."</td>";
				echo "<td>".$row['Ingnre']."</td>";
				echo "<td>".$row['Habcod']."</td>";
				echo "<td>".$telefonocliame."</td>";
				echo "<td>".$row['Cconom']."</td>";
				echo "<td><font color=".$wcolorpac.">".$wtpa."</font></td>";
				echo "<td>".(($row['Ubimue']=='on') ? "si" : "no")."</td>";
				echo "<td>".$row['Fecha_data']."</td>";
				echo "<td>".$row['Ubifad']."</td>";
				echo "<td>".$fechaultimaencuesta."</td>";
				echo "<td style= 'background-color : white;'>&nbsp;&nbsp;</td>";
				echo "<td><div id='".$contenedor."'>";
				/*echo "<select onchange='cambiaUltimaEncuesta(this, \$(this).parents(\"tr\"));'>";
				$i=0;
				while($row1 =mysql_fetch_array($res1))
				{

					if($row1['Forcod']==$pacientesprogramados[$row['Pacced']])
					{

					echo "<option selected value='".$row1['Forcod']."'>".$row1['Fordes']."</option>";
					}
					else
					{
					echo "<option  value='".$row1['Forcod']."'>".$row1['Fordes']."</option>";
					}
					$i++;
				}
				echo "</select>";*/				
			
				echo "</div></td>";
				echo "</tr>";
			}
		}
		$i++;
	}
	echo "</table>";
	echo "</div>";
	return;
}

if ($woperacion=='agregarencuesta' )
{
	$q = " SELECT ".$wbasedato."_000002.Forcod, ".$wbasedato."_000002.Fordes"
		."   FROM ".$wbasedato."_000002, ".$wbasedato."_000042 "
		." 	WHERE  ".$wbasedato."_000042.Fortip = '03' "
		."    AND  ".$wbasedato."_000042.Forcod = ".$wbasedato."_000002.Fortip ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	echo "<select onchange='cambiaUltimaEncuesta(this, \$(this).parents(\"tr\"));'>";
	echo "<option value=''>Seleccione una encuesta</option>";
	if($wultimaencusta=='0')
	{
		$i=0;
		while($row =mysql_fetch_array($res))
		{

			if( $i==0)
			{
			 echo "<option selected value='".$row['Forcod']."'>".($row['Fordes'])."</option>";
			}
			else
			{
			 echo "<option value='".$row['Forcod']."'>".($row['Fordes'])."</option>";
			}
			$i++;
		}
	}
	else
	{
		$i=$wultimaencuesta;
		while($row =mysql_fetch_array($res))
		{
			if( $i==$row['Forcod'])
			{
				echo "<option selected value='".$row['Forcod']."'>".($row['Fordes'])."</option>";
			}
			else
			{
				echo "<option value='".$row['Forcod']."'>".($row['Fordes'])."</option>";
			}
		}

	}
	echo "</select>";
	return;
}

if ($woperacion=='agregarevaluacion' )
{
	$q = " SELECT ".$wbasedato."_000002.Forcod, ".$wbasedato."_000002.Fordes"
		."   FROM ".$wbasedato."_000002, ".$wbasedato."_000042 "
		." 	WHERE  (".$wbasedato."_000042.Fortip = '04' OR  ".$wbasedato."_000042.Fortip = '03' OR  ".$wbasedato."_000042.Fortip = '05')  "
		."    AND  ".$wbasedato."_000042.Forcod = ".$wbasedato."_000002.Fortip "
		."    AND  ".$wbasedato."_000002.Fortip = '".$wtemainterno."'";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	echo "<select onchange='cambiaUltimaEvaluacion(this, \$(this).parents(\"tr\"));'>";
	
	
	echo "<option value=''>Seleccione una evaluaci&oacute;n</option>";
	while($row =mysql_fetch_array($res))
	{
		echo "<option value='".$row['Forcod']."'>".($row['Fordes'])."</option>";
	}
	
	// $i=0;
	// if($wultimaencusta=='0')
	// {
		// $i=0;
		// while($row =mysql_fetch_array($res))
		// {

			// if( $i==0)
			// {
			 // echo "<option selected value='".$row['Forcod']."'>".($row['Fordes'])."</option>";
			// }
			// else
			// {
			 // echo "<option value='".$row['Forcod']."'>".($row['Fordes'])."</option>";
			// }
			// $i++;
		// }
	// }
	// else
	// {
		// $i=$wultimaencuesta;
		// while($row =mysql_fetch_array($res))
		// {
			// if( $i==$row['Forcod'])
			// {
				// echo "<option selected value='".$row['Forcod']."'>".($row['Fordes'])."</option>";
			// }
			// else
			// {
				// echo "<option value='".$row['Forcod']."'>".($row['Fordes'])."</option>";
			// }
		// }

	// }
	echo "</select>";
	return;
}

if ($woperacion === 'agregarevaluaciones')
{
	$q = " SELECT ".$wbasedato."_000002.Forcod, ".$wbasedato."_000002.Fordes"
		."   FROM ".$wbasedato."_000002, ".$wbasedato."_000042 "
		." 	WHERE  (".$wbasedato."_000042.Fortip = '04' OR  ".$wbasedato."_000042.Fortip = '03' OR  ".$wbasedato."_000042.Fortip = '05')  "
		."    AND  ".$wbasedato."_000042.Forcod = ".$wbasedato."_000002.Fortip "
		."    AND  ".$wbasedato."_000002.Fortip = '".$wtemainterno."'";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	$posiblesEvaluaciones = array();

	while($row =mysql_fetch_array($res))
	{
		$posiblesEvaluaciones[$row['Forcod']] = $row['Fordes'];
	}

	echo json_encode($posiblesEvaluaciones);
	return;
}

if ($woperacion == 'consultarDiagnosticos')
{
	$sql = "SELECT Codigo, Descripcion 
			FROM root_000011
			WHERE Estado = 'on'";

	$res = mysql_query($sql,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	$diagnosticos = array();

	while($row =mysql_fetch_array($res))
	{
		$diagnosticos[] = $row['Codigo'].'-'.utf8_encode($row['Descripcion']);
	}

	echo json_encode($diagnosticos);
	return;
}

if ($woperacion=='grabarPacienteEncuesta' )
{
	$q=	" 	SELECT Perano,Perper "
		."    FROM ".$wbasedato."_000009 "
		."   WHERE Perest='on'"
		."     AND Perfor='".$wtemainterno."'";
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row =mysql_fetch_array($res);

	$wano=$row[0];
	$wperiodo=$row[1];
			
	if($wbasedato=='encumage')
    {		
		// miro si tiene una encuesta y veo el periodo mayor 
		$q="SELECT MAX(Encper) as Encper
			  FROM ".$wbasedato."_000049 
			 WHERE Enchis='".$whis."'  
			";
		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	
			
		if ($row=mysql_fetch_array($res))
		{			
			$wperiodo= ($row['Encper']*1) + 1;			
		}	
	}			

	$fecha= date("Y-m-d");
	$hora = date("H:i:s");

	$q = " INSERT INTO ".$wbasedato."_000049 "
   		. "            ( Medico			,Fecha_data		,	Hora_data		,	Encced		,		Encenc		,	Enchis		,	Encing		,Encno1				,Encno2			,Encap1			,Encap2				,Enceda			,Encent				,Encdia							,Enctel				,Enchab			,Encafi			,Enccco			,		Encfec				,Encest		,Seguridad				,Encano			,Encper				,Enctem					,Encfpr			,Encpob) "
   		. "      VALUES('".$wbasedato."'	,'".$fecha."'	,	'".$hora."'		,	'".$wced."'	,	'".$wencuesta."',	'".$whis."'	,	'".$wing."'	,'".$wno1."'		,'".$wno2."'	,'".$wap1."'	,'".$wap2."'		,'".$wedad."'	,'".$wentidad."'	,'".trim($wcodentidad)."'		,'".$wtelefono."'	,'".$whcod."'	,'".$wtpa."'	,'".$wcco."'	,	'".$wfechaing."'		,'on'		,'C-".$wbasedato."'		,'".$wano."'	,'".$wperiodo."'	,'".$wtemainterno."'	,'".$fecha."'	,'".$wpublicoobjetivo."')" ;

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	return;
}

if ($woperacion=='grabarEmpleadoEvaluacion' )
{
	$q=	" 	SELECT Perano,Perper "
		."    FROM ".$wbasedato."_000009 "
		."   WHERE Perest='on'"
		."     AND Perfor='".$wtemainterno."'";
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row =mysql_fetch_array($res);

	$wano=$row[0];
	$wperiodo=$row[1];

	$fecha= date("Y-m-d");
	$hora = date("H:i:s");

	$q = " INSERT INTO ".$wbasedato."_000055 "
   		. "            ( Medico,Fecha_data,Hora_data,Empcod,Empeva,Empest,Seguridad,Empeve,Empano,Empper,Emptem) "
   		. "      VALUES('".$wbasedato."','".$fecha."','".$hora."','".$wevaluado."','".$wevaluacion."','on','C-".$wbasedato."','pendiente','".$wano."','".$wperiodo."','".$wtemainterno."')" ;

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	return;
}

if ($woperacion == 'eliminarEncuestasPendientes')
{
	$q=	" 	SELECT Perano,Perper "
		."    FROM ".$wbasedato."_000009 "
		."   WHERE Perest='on'"
		."     AND Perfor='".$wtemainterno."'";
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row =mysql_fetch_array($res);

	$wano=$row[0];
	$wperiodo=$row[1];

	$q = "	DELETE 
			FROM ".$wbasedato."_000049
			WHERE Enchis= '".$usuario."'
			AND Encano = '".$wano."'
			AND Encper = '".$wperiodo."'
			AND Encese = 'pendiente' " ;
	
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row =mysql_fetch_array($res);

	return;
}

if ($woperacion == 'consultarEncuestasCerradas')
{
	$respuesta = consultarEncuestasCerradas($conex, $wbasedato, $wtemainterno, $usuario, $wano, $wperiodo);

	echo json_encode($respuesta);
	return;
}

if ($woperacion=='eliminarPacienteEncuesta')
{
	$q=	" 	SELECT Perano,Perper "
		."    FROM ".$wbasedato."_000009 "
		."   WHERE Perest='on'"
		."     AND Perfor='".$wtemainterno."'";
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row =mysql_fetch_array($res);

	$wano=$row[0];
	$wperiodo=$row[1];	
	
	if($wbasedato!="encumage")
	{
		$q = "SELECT Encese "
			."  FROM ".$wbasedato."_000049 "
			." WHERE Enchis= '".$whis."' "
			."   AND Encano = '".$wano."' "
			."   AND Encper = '".$wperiodo."' "
			."   AND Enctem = '".$wtemainterno."' " ;

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$row =mysql_fetch_array($res);

		$westadoencuesta=$row['Encese'];

		if( $westadoencuesta =='cerrado' or $westadoencuesta=='Cerrado')
		{
			$mensaje ='!!!no se puede borrar porque el paciente tiene una encuesta ya hecha';

		}
		else
		{
			$q = "DELETE "
				."  FROM ".$wbasedato."_000049 "
				." WHERE Enchis= '".$whis."' "
				."   AND Encano = '".$wano."' "
				."   AND Encper = '".$wperiodo."' "
				."   AND Enctem = '".$wtemainterno."' " ;

			$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$mensaje='Encuesta borrada';
		}
	}
	else
	{
		$q = "DELETE "
			."  FROM ".$wbasedato."_000049 "
			." WHERE Enchis= '".$whis."' "
			."   AND Enctem = '".$wtemainterno."' 
			     AND Encese !='cerrado'" ;

			$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$mensaje='Encuesta borrada';
	}

	echo $mensaje;
	return;
}

if ($woperacion=='eliminarEmpleadoEvaluacion')
{

	$q=	" 	SELECT Perano,Perper "
		."    FROM ".$wbasedato."_000009 "
		."   WHERE Perest='on'"
		."     AND Perfor='".$wtemainterno."'";
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row =mysql_fetch_array($res);

	$wano=$row[0];
	$wperiodo=$row[1];

	$q=	" 	SELECT Perano,Perper "
		."    FROM ".$wbasedato."_000009 "
		."   WHERE Perest='on'"
		."     AND Perfor='".$wtemainterno."'";
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row =mysql_fetch_array($res);

	$wano=$row[0];
	$wperiodo=$row[1];

	$q = "DELETE "
		."  FROM ".$wbasedato."_000055 "
		." WHERE Empcod = '".$wusuario."' "
		."   AND Empeva = '".$wevaluacion."' "
		."   AND Empano = '".$wano."' "
		."   AND Empper = '".$wperiodo."' "
		."   AND Emptem = '".$wtemainterno."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	echo $q;
	return;
}

if($woperacion=='actualizadato')
{
  	if($wbasedato=='encumage')
  	{
	  	$q=	" 	SELECT Perano,Perper "
			."    FROM ".$wbasedato."_000009 "
			."   WHERE Perest='on'"
			."     AND Perfor='".$wtemainterno."'";
		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$row =mysql_fetch_array($res);

		$wano=$row[0];
		$wperiodo=$row[1];
			
			
		$wperiodo = $periodo;
		var_dump($wperiodo);
		if($wperiodo=='undefined')
		{
			$query2 ="SELECT Encfce ,Encese , MAX(Encper) as Encper  "
					."  FROM  ".$wbasedato."_000049 "
					." WHERE  Enchis='".$whis."' "
					."   AND Enctem = '".$wtemainterno."' " ;
	

			$resquery = mysql_query($query2,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$query2." - ".mysql_error());
			$rowquery =mysql_fetch_array($resquery);
			$wperiodo = $rowquery['Encper'];
		}

		$q= "UPDATE ".$wbasedato."_000049 "
			. "   SET Encenc='".$wencuesta."' "
			. " WHERE Enchis = '".$whis."' "
			."    AND Encano = '".$wano."' "
			."    AND Encper = '".$wperiodo."' "
			."    AND Enctem = '".$wtemainterno."' " ;

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
  	}
  	else
  	{
		$filtroEstado = " AND Encese = 'pendiente'";
		$filtroEncAnt = " AND Encenc = '".$evaluacionAnterior."'";
	
 		$q=	" 	SELECT Perano,Perper "
			."    FROM ".$wbasedato."_000009 "
			."   WHERE Perest='on'"
			."     AND Perfor='".$wtemainterno."'";
		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$row =mysql_fetch_array($res);

		$wano=$row[0];
		$wperiodo=$row[1];

		$q= "UPDATE ".$wbasedato."_000049 "
		. "   SET Encenc='".$wencuesta."' "
		. " WHERE Enchis = '".$whis."' "
		."    AND Encano = '".$wano."' "
		."    AND Encper = '".$wperiodo."' "
		."    AND Enctem = '".$wtemainterno."' " 
		.	 $filtroEstado
		.	 $filtroEncAnt;

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  	}
  	echo $q;
  	return;
}

if($woperacion=='actualizaEmpleadoEvaluacion')
{
	$q=	" 	SELECT Perano,Perper "
		."    FROM ".$wbasedato."_000009 "
		."   WHERE Perest='on'"
		."     AND Perfor='".$wtemainterno."'";
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row =mysql_fetch_array($res);

	$wano=$row[0];
	$wperiodo=$row[1];

	$q= "UPDATE ".$wbasedato."_000055 "
	  . "   SET Empeva='".$wevaluacion."' "
	  . " WHERE Empcod = '".$wusuario."' "
	  ."   AND Empano = '".$wano."' "
	  ."   AND Empper = '".$wperiodo."' "
	  ."   AND Emptem = '".$wtemainterno."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	echo $q;
	return;
}

if($woperacion=='consultarEvaluacionProgramada')
{
	$evaluacionProgramada = consultarEvaluacionProgramada($conex,$wbasedato,$whis,$wencuesta,$wtemainterno);
	
	echo json_encode($evaluacionProgramada);
	return;
}

if($woperacion=='consultarEncuestaProgramada')
{
	$encuestaProgramada = consultarEncuestaProgramada($conex,$wbasedato,$whis,$wencuesta,$wtemainterno);
	
	echo json_encode($encuestaProgramada);
	return;
}

function consultarEncuestasCerradas($conex, $wbasedato, $wtemainterno, $usuario, $wano, $wperiodo)
{
	$q=	" 	SELECT Perano,Perper "
		."    FROM ".$wbasedato."_000009 "
		."   WHERE Perest='on'"
		."     AND Perfor='".$wtemainterno."'";
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row =mysql_fetch_array($res);

	$wano=$row[0];
	$wperiodo=$row[1];

	$arr_res = array();

	$q = "	SELECT Encenc
			FROM ".$wbasedato."_000049
			WHERE Enchis= '".$usuario."'
			AND Encano = '".$wano."'
			AND Encper = '".$wperiodo."'
			AND Encese = 'cerrado' " ;
	
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	while($row =mysql_fetch_assoc($res))
	{
		$arr_res[] = $row['Encenc'];
	}

	return $arr_res;
}

function consultarPeriodo($conex,$wbasedato,$wtemainterno)
{
	$q=	" 	SELECT Perano,Perper "
		."    FROM ".$wbasedato."_000009 "
		."   WHERE Perest='on'"
		."     AND Perfor='".$wtemainterno."'";
		
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
	
	$arrayPeriodo = array();
	if($num>0)
	{
		$row =mysql_fetch_array($res);
		$arrayPeriodo['wano'] = $row['Perano'];
		$arrayPeriodo['wperiodo'] = $row['Perper'];
	}
	
	return $arrayPeriodo;
}

function consultarEvaluacionProgramada($conex,$wbasedato,$usuario,$encuesta,$wtemainterno)
{
	$arrayPeriodo = consultarPeriodo($conex,$wbasedato,$wtemainterno);
	
	$wano = $arrayPeriodo['wano'];
	$wperiodo = $arrayPeriodo['wperiodo'];
	
	$query = "SELECT id 
				FROM ".$wbasedato."_000049 
			   WHERE Enchis='".$usuario."' 
			     AND encano='".$wano."' 
				 AND encper='".$wperiodo."' 
				 AND Encenc='".$encuesta."';";
	
	$res = mysql_query($query, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $query . " - " . mysql_error());		   
	$num = mysql_num_rows($res);
	
	$evaluacionProgramada = false;
	if($num>0)
	{
		$evaluacionProgramada = true;
	}
	
	return $evaluacionProgramada;
}

function consultarEncuestaProgramada($conex,$wbasedato,$usuario,$encuesta,$wtemainterno)
{
	$arrayPeriodo = consultarPeriodo($conex,$wbasedato,$wtemainterno);
	
	$wano = $arrayPeriodo['wano'];
	$wperiodo = $arrayPeriodo['wperiodo'];
	
	$query = "SELECT id 
				FROM ".$wbasedato."_000049 
			   WHERE Enchis='".$usuario."' 
			     AND encano='".$wano."' 
				 AND encper='".$wperiodo."' 
				 AND Encenc='".$encuesta."';";
	
	$res = mysql_query($query, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $query . " - " . mysql_error());		   
	$num = mysql_num_rows($res);
	
	$encuestaProgramada = false;
	if($num>0)
	{
		$encuestaProgramada = true;
	}
	
	return $encuestaProgramada;
}

?>
<head>
  <title>Lista de Encuestados</title>
  <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
</head>
<script src="../../../include/gentelella/vendors/jquery/dist/jquery.min.js" ></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>    
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.multiselect.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.multiselect.filter.js"></script>
<script type="text/javascript">
var selectencuestas = null;

function SeleccionTema(tema,wtip,elemento)
{
	elemento = jQuery(elemento);

	$.get("RealizarListaEncuestas.php",
	{
		consultaAjax 	: '',
		inicial			: 'no',
		operacion		: 'entrada',
		wtema           : $('#wtema').val(),
		wuse			: $('#wuse').val(),
		wemp_pmla		: $('#wemp_pmla').val()

	}
	, function(data)
	{
		$('#tabletemas').children('tbody').children('tr').each(function (){
		$(this).css('background-color' , '');
		});

		elemento.css({'background-color': '#ffe'});

		$('#divdiv').html(data);
		$('#ultimaencuesta').val('');
		selectencuestas = null;
	});
	$('#wtemainterno').val(tema);
	$('#wtipointerno').val(wtip);
}

function validarHistoria()
{
	$("#mensajeHistoria").html("");
	if($("#txt_nro_historia").val() == "")
	{
		alert("Debe llenar el campo historia");
		return;
	}

	$.get("RealizarListaEncuestas.php",
	{
		consultaAjax 	: '',
		inicial			: 'no',
		operacion		: 'validarpacientedigitado',
		wemp_pmla		: $('#wemp_pmla').val(),
		wtema           : $('#wtema').val(),
		wuse			: $('#wuse').val(),
		nroidentificacion  :$('#txt_numero_identificacion').val(),
		nrohistoria		: $('#txt_nro_historia').val(),
		nroingreso		: $('#txt_nro_ingreso').val(),
		encuesta		: $('#txt_encuesta').val(),
		wcco			: $('#wcco').val(),
		wtemainterno	: $('#wtemainterno').val()

	}, function(data) {
		if(data!='1')
		{
			//alert("No se puede grabar");
			$("#mensajeHistoria").html("<font color='red'>La historia ya esta Encuestada</font>");
		}
		else
		{
			$("#mensajeHistoria").html("<font color='green'>La historia no esta Encuestada</font>");
		}

	});
}

function GrabarPacientedigitado()
{
	$(".obligatorio").removeClass("faltanDatos");
	var datoscompletos = true;
	$(".obligatorio").each(function(){
		if($(this).val() =='')
		{
			$(this).addClass("faltanDatos");
			datoscompletos = false;
		}
	});

	if(!datoscompletos)
	{
		alert('Por favor ingrese campos obligatorios');
		return;
	}

	validarHistoria ();

	$.get("RealizarListaEncuestas.php",
	{
		consultaAjax 	: '',
		inicial			: 'no',
		operacion		: 'grabarpacientedigitado',
		wemp_pmla		: $('#wemp_pmla').val(),
		wtema           : $('#wtema').val(),
		wuse			: $('#wuse').val(),
		primernombre	: $('#txt_primer_nombre').val(),
		segundonombre	: $('#txt_segundo_nombre').val(),
		primerapellido	: $('#txt_primer_apellido').val(),
		segundoapellido	: $('#txt_segundo_apellido').val(),
		edad			: $('#txt_edad').val(),
		nroidentificacion  :$('#txt_numero_identificacion').val(),
		nrohistoria		: $('#txt_nro_historia').val(),
		nroingreso		: $('#txt_nro_ingreso').val(),
		telefono		: $('#txt_telefono').val(),
		habitacion		: $('#txt_habitacion').val(),
		diagnostico		: $('#txt_entidad option:selected').attr('numero'),
		entidad			: $('#txt_entidad').val(),
		afin			: $('#txt_afin').val(),
		encuesta		: $('#txt_encuesta').val(),
		wcco			: $('#wcco').val(),
		wtemainterno	: $('#wtemainterno').val()

	}
	, function(data) {
		if (data=='ok')
		{
			alert('Paciente grabado satisfactoriamente');
			$.unblockUI();
		}
		else
		{
			alert('El paciente ya tiene una encuesta , debe ingresar otro paciente');
		}

	});
}

function fnMostrar2( )
{
	var  celda ;
	var  celda = 'div_agregar_paciente';
	$("#mensajeHistoria").html("");
	$("#txt_primer_nombre").val("");
	$("#txt_primer_apellido").val("");
	$("#txt_edad").val("");
	$("#txt_telefono").val("");
	$("#txt_segundo_nombre").val("");
	$("#txt_segundo_apellido").val("");
	$("#txt_numero_identificacion").val("");
	$("#txt_nro_ingreso").val("");
	$("#txt_diagnostico").val("");

	if( $('#'+celda ) )
	{
		$.blockUI({ 
			message: $('#'+celda ),
			css: { 
				left: ( $(window).width() - 1000 )/2 +'px',
				top: '200px',
				width: '1000px'
			}
		});
	}
}

function ConsultarLista ()
{
	var emp_pmla = document.getElementById('wemp_pmla').value;
	var tema =  document.getElementById('wtema').value;
	var cco = document.getElementById('wcco').value;
	var fecha_i = document.getElementById('wfecha_i').value;
	var fecha_f = document.getElementById('wfecha_f').value;

	var fuente = $("input[name='momento']:checked").val();

	var selectDiagnostico = $('#selectDiagnostico').val();

	var diagnosticoAux =  selectDiagnostico.split('-');

	var diagnostico = diagnosticoAux[0];	

	var params   = 'RealizarListaEncuestas.php?consultaAjax=&woperacion=resultadosLista&wemp_pmla='+emp_pmla+'&wtema='+tema+'&wfecha_i='+fecha_i+'&wfecha_f='+fecha_f+'&wcco='+cco+'&wtemainterno='+$('#wtemainterno').val()+'&wfuente='+fuente+'&wdiagnostico='+diagnostico;

	$.get(params, function(data) {
		$('#divlista').html(data);
	});
}

function  guardarenLista (pacno1,pacno2,pacap1,pacap2,pacced,pactid,Habcod,Orihis,Oriing,ccocod,fecha,contenedor,elemento,telefono,edad,nre,wtpa,fechaultimaencuesta,codentidad)
{	
	var emp_pmla = document.getElementById('wemp_pmla').value;
	var tema =  document.getElementById('wtema').value;
	var cco = document.getElementById('wcco').value;
	var elementojq = jQuery(elemento);
	var ultimaencuesta = document.getElementById('ultimaencuesta').value;

	if (fechaultimaencuesta =='Pendiente por cerrar' || fechaultimaencuesta=='' )
	{

		if (ultimaencuesta.length == 0)
		{
			// ultimaencuesta = 0;
			ultimaencuesta = "";
		}

		if (elemento.checked)
		{
			//alert("1");
			evaluacionAnterior = "";
			if (selectencuestas != null)
			{
				$('#'+contenedor).html(selectencuestas);
				if ( ultimaencuesta != 0)
				{
					// $('#'+contenedor+" select").val(ultimaencuesta);
					
					ultimaencuesta = "";
					$('#'+contenedor+" select").val(ultimaencuesta);
				}
				guardarpaciente(pacno1,pacno2,pacap1,pacap2,pacced,pactid,Habcod,Orihis,Oriing,ccocod,fecha,contenedor,elemento,ultimaencuesta,telefono,edad,nre,wtpa,codentidad);
			}
			else
			{
				var params   = 'RealizarListaEncuestas.php?consultaAjax=&woperacion=agregarencuesta&wemp_pmla='+emp_pmla+'&wtema='+tema+'&wultimaencuesta='+ultimaencuesta;

				$.get(params, function(data) {
					$('#'+contenedor).html(data);
					selectencuestas=data;
					ultimaencuesta = $("#"+contenedor+" select").val();
					evaluacionAnterior = ultimaencuesta;
					$("#"+contenedor+" select").attr("evaluacionAnterior",ultimaencuesta);
					guardarpaciente(pacno1,pacno2,pacap1,pacap2,pacced,pactid,Habcod,Orihis,Oriing,ccocod,fecha,contenedor,elemento,ultimaencuesta,telefono,edad,nre,wtpa,codentidad);
				});
			}
			
			$("#"+contenedor+" select").attr("evaluacionAnterior",ultimaencuesta);
		}
		else
		{
			//alert("2");
			$('#'+contenedor).html('');
			var params   = 'RealizarListaEncuestas.php?consultaAjax=&woperacion=eliminarPacienteEncuesta&wemp_pmla='+emp_pmla+'&wtema='+tema+'&wced='+pacced+'&wno1='+pacno1+'&wno2='+pacno2+'&wap1='+pacap1+'&wap2='+pacap2+'&wtid='+pactid+'&whcod='+Habcod+'&wing='+Oriing+'&wcco='+cco+'&wfechaing='+fecha+'&whis='+Orihis+'&wtemainterno='+$('#wtemainterno').val();

			$.get(params, function(data) {
				if (data=='Encuesta borrada')
				{
					var nombretr= $(elementojq).attr('id');

					//alert($("#"+nombretr+" td").eq(13).text());
					$("#"+nombretr+"").parent().parent().find("td").eq(15).text(" ");
				}
				else
				{
					alert(data);
				}
    		});
		}
	}
	else
	{
		if (elemento.checked)
		{
			// alert("3");
			var params   = 'RealizarListaEncuestas.php?consultaAjax=&woperacion=agregarencuesta&wemp_pmla='+emp_pmla+'&wtema='+tema+'&wultimaencuesta='+ultimaencuesta;
			//alert(params);
			$.get(params, function(data) {
				$('#'+contenedor).html(data);
				selectencuestas=data;
				ultimaencuesta = $("#"+contenedor+" select").val();
				evaluacionAnterior = ultimaencuesta;
				$("#"+contenedor+" select").attr("evaluacionAnterior",ultimaencuesta);
				guardarpaciente(pacno1,pacno2,pacap1,pacap2,pacced,pactid,Habcod,Orihis,Oriing,ccocod,fecha,contenedor,elemento,ultimaencuesta,telefono,edad,nre,wtpa,codentidad);
			});
		}
		else
		{
			//alert("aaaa");
			$('#'+contenedor).html('');
		
			var params   = 'RealizarListaEncuestas.php?consultaAjax=&woperacion=eliminarPacienteEncuesta&wemp_pmla='+emp_pmla+'&wtema='+tema+'&wced='+pacced+'&wno1='+pacno1+'&wno2='+pacno2+'&wap1='+pacap1+'&wap2='+pacap2+'&wtid='+pactid+'&whcod='+Habcod+'&wing='+Oriing+'&wcco='+cco+'&wfechaing='+fecha+'&whis='+Orihis+'&wtemainterno='+$('#wtemainterno').val();
			// alert(params+111);
			$.get(params, function(data) {
				/*if (data=='Encuesta borrada')
				{
					var nombretr= $(elementojq).attr('id');

					//alert($("#"+nombretr+" td").eq(13).text());
					$("#"+nombretr+"").parent().parent().find("td").eq(15).text(" ");


				}*/
				// alert(data);			
			});
		}
		//$("#check-"+Orihis).attr('checked', true);
		//alert('Encuesta ya fue cerrada no se puede borrar');
		//guardarpaciente(pacno1,pacno2,pacap1,pacap2,pacced,pactid,Habcod,Orihis,Oriing,ccocod,fecha,contenedor,elemento,ultimaencuesta,telefono,edad,nre,wtpa,codentidad);	
	}
}

function cambiaUltimaEncuesta (elemento,tr1,periodo)
{
	//alert(periodo);
	
	var emp_pmla = document.getElementById('wemp_pmla').value;
	var tema =  document.getElementById('wtema').value;
	var cco = document.getElementById('wcco').value;
	var ultimaencuesta = elemento.value;
	document.getElementById('ultimaencuesta').value = ultimaencuesta;

	var tr = tr1[0];
	
	pactid = ($(tr).find('td').eq(2).html()) ;
	pacced = ($(tr).find('td').eq(3).html()) ;
	Orihis = ($(tr).find('td').eq(5).html()) ;
	Oriing = ($(tr).find('td').eq(6).html()) ;
	Habcod = ($(tr).find('td').eq(8).html()) ;
	fecha  =($(tr).find('td').eq(13).html()) ;
	ccocod = document.getElementById('wcco').value;
	var periodo = periodo;
	
	
	// $conex,$wbasedato,$usuario,$encuesta,$wtemainterno
	$.ajax({
		url: "RealizarListaEncuestas.php",
		type: "POST",
		dataType: "json",
		data:{
			consultaAjax 	: '',
			woperacion		: 'consultarEncuestaProgramada',
			wemp_pmla		: emp_pmla,
			wtema			: tema,
			whis			: Orihis,
			wencuesta		: ultimaencuesta,
			wtemainterno	: $("#wtemainterno").val()
			},
			async: false,
			success:function(respuesta) {
				
				if(!respuesta)
				{
					var params   = 'RealizarListaEncuestas.php?consultaAjax=&woperacion=actualizadato&wemp_pmla='+emp_pmla+'&wtema='+tema+'&wced='+pacced+'&wtid='+pactid+'&whcod='+Habcod+'&wing='+Oriing+'&wcco='+ccocod+'&wfechaing='+fecha+'&whis='+Orihis+'&wencuesta='+ultimaencuesta+'&wtemainterno='+$('#wtemainterno').val()+'&periodo='+periodo+'&evaluacionAnterior='+$(elemento).attr('evaluacionAnterior');
					$.get(params, function(data) {

					});
									
					$(elemento).attr('evaluacionAnterior',elemento.value);
					console.log($(elemento).attr('evaluacionAnterior'));
				}
				else
				{
					alert("El paciente ya tiene la encuesta progamada");
					$(elemento).val($(elemento).attr('evaluacionAnterior'))
				}
				
			}
	});
	
	
	// var params   = 'RealizarListaEncuestas.php?consultaAjax=&woperacion=actualizadato&wemp_pmla='+emp_pmla+'&wtema='+tema+'&wced='+pacced+'&wtid='+pactid+'&whcod='+Habcod+'&wing='+Oriing+'&wcco='+ccocod+'&wfechaing='+fecha+'&whis='+Orihis+'&wencuesta='+ultimaencuesta+'&wtemainterno='+$('#wtemainterno').val()+'&periodo='+periodo;
	// $.get(params, function(data) {

	// });
}

function cambiaUltimaEvaluacion (elemento,tr)
{
	var emp_pmla = document.getElementById('wemp_pmla').value;
	var tema =  document.getElementById('wtema').value;
	var cco = document.getElementById('wccostos_pfls').value;

	var ultimaencuesta = elemento.value;
	document.getElementById('ultimaencuesta').value = ultimaencuesta;

	var empleado=($(tr).find('td').eq(1).html()) ;
	if(empleado == "") {
		empleado = $(tr).children().eq(1).text();
	}

	if ($('#wtipointerno').val() =='03' || $('#wtipointerno').val() =='05')
  	{
		
		$.ajax({
			url: "RealizarListaEncuestas.php",
			type: "POST",
			dataType: "json",
			data:{
				consultaAjax 	: '',
				woperacion		: 'consultarEvaluacionProgramada',
				wemp_pmla		: emp_pmla,
				wtema			: tema,
				whis			: empleado,
				wencuesta		: ultimaencuesta,
				wtemainterno	: $("#wtemainterno").val()
				},
				async: false,
				success:function(respuesta) {
					
					if(!respuesta)
					{
						var params   = 'RealizarListaEncuestas.php?consultaAjax=&woperacion=actualizadato&wemp_pmla='+emp_pmla+'&wtema='+tema+'&whis='+empleado+'&wencuesta='+ultimaencuesta+'&wtemainterno='+$('#wtemainterno').val()+'&evaluacionAnterior='+$(elemento).attr('evaluacionAnterior');
						$.get(params, function(data) {

						});
						
						$(elemento).attr('evaluacionAnterior',elemento.value);
						console.log($(elemento).attr('evaluacionAnterior'));
					}
					else
					{
						alert("El usuario ya tiene la evaluacion progamada");
						$(elemento).val($(elemento).attr('evaluacionAnterior'))
					}
					
				}
		});
  	}
  	else
  	{
		var params   = 'RealizarListaEncuestas.php?consultaAjax=&woperacion=actualizaEmpleadoEvaluacion&wemp_pmla='+emp_pmla+'&wtema='+tema+'&wusuario='+empleado+'&wevaluacion='+ultimaencuesta+'&wtemainterno='+$('#wtemainterno').val();
		$.get(params, function(data) {

		});
  	}
}

function guardarpaciente(pacno1,pacno2,pacap1,pacap2,pacced,pactid,Habcod,Orihis,Oriing,ccocod,fecha,contenedor,elemento,ultimaencuesta,telefono,edad,nre,wtpa,codentidad)
{
   	var emp_pmla = document.getElementById('wemp_pmla').value;
	var tema =  document.getElementById('wtema').value;
	var cco = document.getElementById('wcco').value;
	document.getElementById('ultimaencuesta').value = ultimaencuesta;
	var params   = 'RealizarListaEncuestas.php?consultaAjax=&woperacion=grabarPacienteEncuesta&wemp_pmla='+emp_pmla+'&wtema='+tema+'&wced='+pacced+'&wno1='+pacno1+'&wno2='+pacno2+'&wap1='+pacap1+'&wap2='+pacap2+'&wtid='+pactid+'&whcod='+Habcod+'&wing='+Oriing+'&wcco='+cco+'&wfechaing='+fecha+'&whis='+Orihis+'&wencuesta='+ultimaencuesta+'&wtelefono='+telefono+'&wedad='+edad+'&wentidad='+nre+'&wtpa='+wtpa+'&wtemainterno='+$('#wtemainterno').val()+'&wpublicoobjetivo='+$('#wpublicoobjetivo').val()+'&wcodentidad='+codentidad; 
   
	$.get(params, function(data) {

	});
}

function guardarempleado(usuario,ultimaencuesta,no1,no2,ap1,ap2)
{
	var emp_pmla = document.getElementById('wemp_pmla').value;
	var tema =  document.getElementById('wtema').value;
	var cco = document.getElementById('wccostos_pfls').value;

	document.getElementById('ultimaencuesta').value = ultimaencuesta;

  	if ($('#wtipointerno').val() =='03' || $('#wtipointerno').val() =='05')
  	{
   		var params   = 'RealizarListaEncuestas.php?consultaAjax=&woperacion=grabarPacienteEncuesta&wemp_pmla='+emp_pmla+'&wtema='+tema+'&wced='+usuario+'&wno1='+no1+'&wno2='+no2+'&wap1='+ap1+'&wap2='+ap2+'&wtid='+usuario+'&whcod=no aplica&wing=1&wcco='+cco+'&wfechaing=no aplica&whis='+usuario+'&wencuesta='+ultimaencuesta+'&wtelefono=no aplica&wedad=no aplica&wentidad=no aplica&wtpa=no aplica&wtemainterno='+$('#wtemainterno').val()+'&wpublicoobjetivo='+$('#wpublicoobjetivo').val();
   		$.get(params, function(data) {

   		});
	}
	else
	{
   		var params   = 'RealizarListaEncuestas.php?consultaAjax=&woperacion=grabarEmpleadoEvaluacion&wemp_pmla='+emp_pmla+'&wtema='+tema+'&wevaluado='+usuario+'&wevaluacion='+ultimaencuesta+'&wtemainterno='+$('#wtemainterno').val();
   		$.get(params, function(data) {

   		});
  	}
}

function traePacientes()
{
	var emp_pmla = document.getElementById('wemp_pmla').value;
	var tema =  document.getElementById('wtema').value;
	$('#wpublicoobjetivo').val('paciente');

	var params   = 'RealizarListaEncuestas.php?consultaAjax=&wobjetivo=pacientes&wemp_pmla='+emp_pmla+'&wtema='+tema;
	$.get(params, function(data) {
		$('#divpacientes').html('');
		$('#divempleados').html('');
		$('#divpacientes').html(data);
		$('#divpublicoobjetivo').html('<table><tr><td style="font-size:16pt">SELECCI&Oacute;N DE PACIENTES</td></tr></table><br><br>');
	});

}

function traeEmpleados()
{
	var  emp_pmla = document.getElementById('wemp_pmla').value;
	var tema =  document.getElementById('wtema').value;
	$('#wpublicoobjetivo').val('empleado');

	var params   = 'RealizarListaEncuestas.php?consultaAjax=&wobjetivo=empleados&wemp_pmla='+emp_pmla+'&wtema='+tema;
	$.get(params, function(data) {
		$('#divpacientes').html('');
		$('#divempleados').html('');
		$('#divempleados').html(data);
		$('#divpublicoobjetivo').html('<table><tr><td style="font-size:16pt">SELECCI&Oacute;N DE EMPLEADOS</td></tr></table><br><br>');
	});

}

function cambioImagen(img1, img2)
{
	$('#'+img1).hide(1000);
	$('#'+img2).show(1000);
}

function enterBuscar(ele,hijo,op,form,e)
{
	tecla = (document.all) ? e.keyCode : e.which;
	if(tecla==13) { $("#"+hijo).focus(); }
	else { return true; }
	return false;
}

function recargarLista(id_padre, id_hijo, form)
{
	val = $("#"+id_padre).val();
	if(val != '*')
	{
		$('#'+id_hijo).load("gestion_perfiles.php",
		{
			consultaAjax:   '',
			wemp_pmla:  $("#wemp_pmla").val(),
			wtema:      $("#wtema").val(),
			temaselect: $('#temaselect').val(),
			accion:     'load',
			id_padre:   val,
			form:       form
		});
	}
}

function traelistaempleado(cco)
{
	var centrocostos = cco.value;
	var  emp_pmla = document.getElementById('wemp_pmla').value;
	var tema =  document.getElementById('wtema').value;

	var params   = 'RealizarListaEncuestas.php?consultaAjax=&wlistaempleados=si&wemp_pmla='+emp_pmla+'&wtema='+tema+'&wcco='+centrocostos+'&wtemainterno='+$('#wtemainterno').val()+'&wpublicoobjetivo='+$('#wpublicoobjetivo').val()+'&wtipointerno='+$('#wtipointerno').val();

	$.get(params, function(data) {
		$('#divlista').html(data);
		$('.multiencuesta').multiselect({
			multiple:true,
			selectedText: '# seleccionados'
		});
			
		$('.multiencuesta').on("multiselectclose", function(event, ui){
			programarEncuestas($(this).val(), $(this).parent().parent().prev().prev());
		});
	});
}

function seleccionevaluacion(elemento,contenedor,usuario,no1,no2,ap1,ap2)
{
	var emp_pmla = document.getElementById('wemp_pmla').value;
	var tema =  document.getElementById('wtema').value;
	var ultimaencuesta = document.getElementById('ultimaencuesta').value;

	if (ultimaencuesta.length == 0)
	{
		// ultimaencuesta = 0;
		ultimaencuesta = "";
	}
 	if (elemento.checked)
 	{
		evaluacionAnterior = "";
		if (selectencuestas != null)
	 	{
		 	$('#'+contenedor).html(selectencuestas);
		 	if ( ultimaencuesta != 0){
			 	// $('#'+contenedor+" select").val(ultimaencuesta);
				
				ultimaencuesta = "";
			 	$('#'+contenedor+" select").val(ultimaencuesta);
			}
			guardarempleado(usuario,ultimaencuesta,no1,no2,ap1,ap2);
	 	}
	 	else
 		{
			var params   = 'RealizarListaEncuestas.php?consultaAjax=&woperacion=agregarevaluacion&wemp_pmla='+emp_pmla+'&wtema='+tema+'&wultimaencuesta='+ultimaencuesta+'&wtemainterno='+$('#wtemainterno').val()+'&usuario='+usuario;

			$.get(params, function(data) {
			 	$('#'+contenedor).html(data);
			 	selectencuestas=data;
			 	ultimaencuesta = $("#"+contenedor+" select").val();
				evaluacionAnterior = ultimaencuesta;
				$("#"+contenedor+" select").attr("evaluacionAnterior",ultimaencuesta);
				guardarempleado(usuario,ultimaencuesta,no1,no2,ap1,ap2);
			});
	 	}
		
		$("#"+contenedor+" select").attr("evaluacionAnterior",ultimaencuesta);
		
		// $("#"+contenedor+" select").attr("evaluacionAnterior",evaluacionAnterior);
		// console.log("ultimaencuesta: "+evaluacionAnterior);
	}
 	else
	{
		var evaluacion = $("#"+contenedor+" select").val();
		$('#'+contenedor).html('');

  		if ($('#wtipointerno').val() =='04' )
  		{
			var params   = 'RealizarListaEncuestas.php?consultaAjax=&woperacion=eliminarEmpleadoEvaluacion&wemp_pmla='+emp_pmla+'&wtema='+tema+'&wusuario='+usuario+'&wevaluacion='+evaluacion+'&wtemainterno='+$('#wtemainterno').val();

			$.get(params, function(data) {

    		});
  		}
		else
  		{
			var params   = 'RealizarListaEncuestas.php?consultaAjax=&woperacion=eliminarPacienteEncuesta&wemp_pmla='+emp_pmla+'&wtema='+tema+'&wced='+usuario+'&whis='+usuario+'&wtemainterno='+$('#wtemainterno').val();
			$.get(params, function(data) {

    		});
  		}
 	}
}

function programarEncuestas(arr_encuestas, tdNombres)
{
	var usuario = $(tdNombres).prev().text();
	var no1 = $(tdNombres).attr('no1');
	var no2 = $(tdNombres).attr('no2');
	var ap1 = $(tdNombres).attr('ap1');
	var ap2 = $(tdNombres).attr('ap2');

	realizarRequest('eliminarEncuestasPendientes', usuario);

	if(arr_encuestas !== null)
	{
		var encuestasCerradas = realizarRequest('consultarEncuestasCerradas', usuario);		
		
		$.each(arr_encuestas, function(index, encuesta) {
			if(!encuestasCerradas.includes(encuesta))
			{
				guardarempleado(usuario,encuesta,no1,no2,ap1,ap2);
			}
		});
	}
}

function realizarRequest(operacion, usuario)
{
	var emp_pmla = $('#wemp_pmla').val();
	var tema =  $('#wtema').val();
	var cco = $('#wccostos_pfls').val();

	var params   = 'RealizarListaEncuestas.php?consultaAjax=&woperacion='+operacion+'&wemp_pmla='+emp_pmla+'&wtema='+tema+'&usuario='+usuario+'&wcco='+cco+'&wtemainterno='+$('#wtemainterno').val();

	var respuesta;

	$.ajax({
		url : params,
		type : "get",
		dataType: 'json',
		async: false,
		success : function(data) {
			respuesta = data;
		},
		error: function() {
		
		}
	});

	return respuesta;
}

function mostrarfiltrodiagnostico(elem)
{
	var emp_pmla = $('#wemp_pmla').val();
	var tema =  $('#wtema').val();
	var fuente = $(elem).val();
	var datosCargados = $('#diagnostico').attr('cargado');

	if(datosCargados === 'false')
	{
		var params   = 'RealizarListaEncuestas.php?consultaAjax=&woperacion=consultarDiagnosticos&wemp_pmla='+emp_pmla+'&wtema='+tema;

		$.get(params, function(data) {
			$('#datosDiagnosticos').data('datos', data);			
			
		}, 'json').done(function(){
			$( "#selectDiagnostico" ).autocomplete({
				source: $('#datosDiagnosticos').data('datos')
			});
		});

		$('#diagnostico').attr('cargado', 'true');
	}

	if(fuente === 'egreso')
	{
		$('#diagnostico').show();
	}
	else if(fuente === 'ingreso')
	{
		$('#diagnostico').hide();
	}
}

function agregarSelectEvaluaciones(elem)
{
	var wemp_pmla = $('#wemp_pmla').val();
	var tema =  $('#wtema').val();
	var ultimaencuesta = $('ultimaencuesta').val();

	var tdObjetivo = $(elem).parent().parent().children().last();

	var tdNombres = $(elem).parent().next().next();

	if(elem.checked)
	{
		var params   = 'RealizarListaEncuestas.php?consultaAjax=&woperacion=agregarevaluaciones&wemp_pmla='+wemp_pmla+'&wtema='+tema+'&wultimaencuesta='+ultimaencuesta+'&wtemainterno='+$('#wtemainterno').val();		

		$.get(params, function(data) {
			var markup = "<select multiple>";
			$.each(data, function( key, value ) 
			{
				markup += "<option value='"+ key +"'>"+ value +"</option>";
			});			
			markup += "</select>";			

			tdObjetivo.html(markup);
			var select = $(tdObjetivo).children().first();

			select.multiselect({
				multiple:true,
				selectedText: '# seleccionados'
			});
			
			select.on("multiselectclose", function(event, ui){
				programarEncuestas(select.val(), tdNombres);
			});
			// $('#'+contenedor).html(data);
			// selectencuestas=data;
			// ultimaencuesta = $("#"+contenedor+" select").val();
			// guardarempleado(usuario,ultimaencuesta,no1,no2,ap1,ap2);
		}, 'json');
	}
	else
	{
		var usuario = $(tdNombres).prev().text();

		realizarRequest('eliminarEncuestasPendientes', usuario);

		tdObjetivo.html('');
	}
}

function consultarEvaluacionProgramada(emp_pmla,tema,empleado,ultimaencuesta)
{
	var respuesta = false;
	// var params   = 'RealizarListaEncuestas.php?consultaAjax=&woperacion=consultarEvaluacionProgramada&wemp_pmla='+emp_pmla+'&wtema='+tema+'&whis='+empleado+'&wencuesta='+ultimaencuesta+'&wtemainterno='+$('#wtemainterno').val();
	// $.get(params, function(data) {
		// console.log("data: "+data);
	// });
	
	
	
	$.ajax({
		url: "RealizarListaEncuestas.php",
		type: "POST",
		dataType: "json",
		data:{
			consultaAjax 	: '',
			woperacion		: 'consultarEvaluacionProgramada',
			wemp_pmla		: emp_pmla,
			wtema			: tema,
			whis			: empleado,
			wencuesta		: ultimaencuesta,
			wtemainterno	: $("#wtemainterno").val()
			},
			async: false,
			success:function(respuesta) {
				
				console.log("data: "+respuesta);
				
			}
	});
	// $.post("RealizarListaEncuestas.php",
	// {
		// consultaAjax 	: '',
		// woperacion		: 'consultarEvaluacionProgramada',
		// wemp_pmla		: emp_pmla,
		// wtema			: tema,
		// whis			: empleado,
		// wencuesta		: ultimaencuesta,
		// wtemainterno	: $("#wtemainterno").val()
	// }
	// , function(data) {
		
		// console.log("data: "+data);
	
	// },'json');
	
	return respuesta;
}

</script>

<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.css" />
<link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.filter.css" />

<style type="text/css">
    .displ{
        display:block;

    }
    .borderDiv {
        border: 3px solid #2A5DB0;
        padding: 5px;
		width: auto;
    }
    .resalto{
        font-weight:bold;
    }
    .parrafo1{
        color: #676767;
        font-family: verdana;
    }

	.faltanDatos{
		   border: 1px solid red;
	}
	.input--grande{
		width: 100%;
	}

	.ui-autocomplete-input{
		width: 90%;
	}
</style>

<body>

<?php
  /*********************************************************
   *               VISUALIZACION DE RELACIONES             *
   *                                                       *
   *     				                        		   *
   *********************************************************/
//==================================================================================================================================
//PROGRAMA                   : RealizarListaEncuestas.php
//AUTOR                      : Felipe Alvarez Sanchez
//
//FECHA CREACION             : Septiembre 20 de 2012
//FECHA ULTIMA ACTUALIZACION : Octubre 25 de 2018

//DESCRIPCION
//========================================================================================================================================\\
//========================================================================================================================================\\
//   Este Programa realiza un registro de personas a encuestar de una lista total de pacientes  en cada servicio
//   De esta manera se facilita la realizacion de las encuestas															                    \\
//========================================================================================================================================\\
//========================================================================================================================================\\
//ACTUALIZACIONES                                                                        \\
//========================================================================================================================================\\
//Julio 30 de 2019 - Jessica Madrid Mejía: Se modifica el programa para que al hacer clic sobre un paciente para programar una 
// 										   encuesta la primera opción sea el mensaje "Seleccione una encuesta" y se inserte el registro 
// 										   con el campo encuesta vacio y se agrega un atributo al select para relacionar la encuesta
// 										   anterior y así garantizar que se actualice solo la encuesta que se esta modificando.
//Julio 4 de 2019 - Jessica Madrid Mejía: Al hacer clic sobre un empleado o paciente para programar una evaluación se realizaba por 
// 										  defecto un insert con la primera evaluación del select, se realiza la modificación para 
// 										  que la primera opción sea el mensaje "Seleccione una evaluación" y se inserte el registro 
// 										  con el campo evaluación vacio y se agrega un atributo al select para relacionar la evaluación
// 										  anterior y así garantizar que se actualice solo la evaluación que se esta modificando. 
// 										  Adicionalmente se corrige problema de tildes. 
//Octubre 25 2018: Juan Felipe Balcero: Se añade un parámetro en la tabla root_000051 llamado programa_multiples_encuestas donde se configuran los tema de encuestas que pueden más de una encuesta por periodo. Se hacen los cambios necesarios para que el programa permita este funcionamiento. Se añaden funciones para guardar correctamente las múltiples programaciones.
//Febrero 1 2016: Felipe Alvarez: Se modifica la opcion de  GrabarPacientedigitado mejorandolo, se añade funcionalidad
//de poder saber , si la historia ya esta guardada y a los campos obligatorios que hay que digitar se les pone una clase nueva
//Enero 28 2016: Juan C Hdez: Se modifica el query principal para seleccionar los pacientes, era demasiado demorado
//Enero 22 de 2016 Jonatan: Se corrige la consulta principal ya que demoraba en responder, ademas se cambio la base de datos movhos
//por la variable $wbasedatos_movhos.
//========================================================================================================================================\\
echo "<input type='hidden' id='wtema' value='".$wtema."'>";
echo "<input type='hidden' id='wemp_pmla' value='".$wemp_pmla."'>";
echo "<input type='hidden' id='usuario' value='".substr($user,2,strlen($user))."-".$wemp_pmla."'>";
echo "<input type='hidden' id='ultimaencuesta' value='".$ultimaencuesta."'>";
echo "<input type='hidden' id='wtemainterno' value=''>";
echo "<input type='hidden' id='wtipointerno' value=''>";
echo "<input type='hidden' id='wpublicoobjetivo' value=''>";
echo "<input type='hidden' id='datosDiagnosticos' value=''>";
// Query que indica para los tipos de evaluaciones que funciona esta lista.

$q =  " SELECT Forcod, Fordes, 	Fortip "
	 ."   FROM ".$wbasedato."_000042 "
	 ."  WHERE  Fortip = 03 OR Fortip =04 OR Fortip =05";
$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

echo "<div id='temas'>";
echo '<table  style= "border: #2A5DB0 2px solid;  "  whidth="400" id="tabletemas">
				<tr class="encabezadoTabla"   style="cursor:pointer; border-color:#666666;  border-width:1px;">
					<td>
						TEMAS
					</td>
				</tr>';
$k=0;
while ($row =mysql_fetch_array($res))
{
   if (is_int ($k/2))
   {
	$wcf="fila1";  // color de fondo de la fila
   }
   else
   {
	 $wcf="fila2"; // color de fondo de la fila
   }
   echo"<tr style='cursor:pointer;' class='".$wcf."' id = '".$row['Forcod']."-".$row['Fortip']."' name = 'tdc-".$row['Forcod']."' onclick='SeleccionTema(\"".$row['Forcod']."\",\"".$row['Fortip']."\",this)'>
			<td>".($row['Fordes'])."</td>
		</tr>";
   $k++;

}
echo '</table><br><br>';
echo "</div>";
echo "<div id='divdiv'></div>";
?>
</body>