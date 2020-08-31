<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1");
include_once "funciones_talhuma.php";




include_once("root/comun.php");
include_once("root/magenta.php");
$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
$wbasedatos_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
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
		echo "<option value='".$row['Forcod']."'>".$row['Fordes']."</option>";
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

	while($row =mysql_fetch_array($res))
	{
		$pacientesprogramados[$row[0]] =  $row[1];
		$pacientescerrados[$row[0]] =  $row[2];
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
		if ($pacientescerrados[$row['Ideuse']] =='cerrado')
		{
			$estadodesabilitado = 'Disabled';
		}
		else
		{
			$estadodesabilitado = '';
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
			echo"<td class='".$wcf."' align='center'><input ".$estadodesabilitado." type='checkbox'  checked id='check-".$row['Ideuse']."' value='".$row['Ideuse']."' onClick='seleccionevaluacion(this,\"".$contenedor."\",\"".$row['Ideuse']."\",\"".$row['Ideno1']."\" ,\"".$row['Ideno2']."\",\"".$row['Ideap1']."\", \"".$row['Ideap2']."\")'></td>";
			echo"<td class='".$wcf."' >".$row['Ideuse']."</td>";
			echo"<td class='".$wcf."' >".$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']."</td>";
			echo"<td class='".$wcf."'>".$row['Cardes']."</td>";
			echo"<td class='".$wcf."'><div id='".$contenedor."'>";
			echo "<select ".$estadodesabilitado." onchange='cambiaUltimaEvaluacion(this, \$(this).parents(\"tr\"));'>";
			$i=0;
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
				$i++;
			}
			echo "</select>";
			echo"</div></td>";
			echo "</tr>";

		}
		else
		{
			echo"<tr>";
			echo"<td class='".$wcf."' align='center'><input type='checkbox'  id='check-".$row['Ideuse']."' value='".$row['Ideuse']."' onClick='seleccionevaluacion(this,\"".$contenedor."\",\"".$row['Ideuse']."\",\"".$row['Ideno1']."\" ,\"".$row['Ideno2']."\",\"".$row['Ideap1']."\", \"".$row['Ideap2']."\")'></td>";
			echo"<td class='".$wcf."' >".$row['Ideuse']."</td>";
			echo"<td class='".$wcf."' >".$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']."</td>";
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
echo "<td align='right'><b>Centro de Costos</b>";
echo "</td>";
echo "<td align='right'>";
echo "<select id='wcco' >";
while($row =mysql_fetch_array($res))
{
	echo "<option value='".$row['Ccocod']."'>".$row['Cconom']."</option>";
}
echo "<select>";
echo "</td>";
echo "</tr>";
echo "<tr align='center'>";
echo "<td align='center' colspan='2'><input type='button' Value='Buscar' onClick='ConsultarLista()'/></td>";
echo "</tr>";
echo "</table>";
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
	$query2=	 "SELECT Encfce "
				."  FROM  ".$wbasedato."_000049 "
				." WHERE  Enchis='".$row['Orihis']."' "
				."   AND Encano = '".$wano."' "
				."   AND Encper = '".$wperiodo."' "
				."   AND Enctem = '".$wtemainterno."' " ;

	if($wbasedato=='encumage')
	{
		 $query2=	 "SELECT Encfce ,Encese , MAX(Encper) as Encper  "
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
	clienteMagenta($row['Pacced'],$row['Pactid'],&$wtpa,&$wcolorpac);
	
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
					echo "<select onchange='cambiaUltimaEncuesta(this, \$(this).parents(\"tr\"),\"".$rowquery['Encper']."\");'>";
				}
				else
				{
					echo "<select onchange='cambiaUltimaEncuesta(this, \$(this).parents(\"tr\"));'>";
				}
				
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
				echo "<select onchange='cambiaUltimaEncuesta(this, \$(this).parents(\"tr\"),\"".$rowquery['Encper']."\");'>";
			}
			else
			{
				echo "<select onchange='cambiaUltimaEncuesta(this, \$(this).parents(\"tr\"));'>";
			}
			
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
	if($wultimaencusta=='0')
	{

		$i=0;
		while($row =mysql_fetch_array($res))
		{

			if( $i==0)
			{
			 echo "<option selected value='".$row['Forcod']."'>".$row['Fordes']."</option>";
			}
			else
			{
			 echo "<option value='".$row['Forcod']."'>".$row['Fordes']."</option>";
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
				echo "<option selected value='".$row['Forcod']."'>".$row['Fordes']."</option>";
			}
			else
			{
				echo "<option value='".$row['Forcod']."'>".$row['Fordes']."</option>";
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
	if($wultimaencusta=='0')
	{

		$i=0;
		while($row =mysql_fetch_array($res))
		{

			if( $i==0)
			{
			 echo "<option selected value='".$row['Forcod']."'>".$row['Fordes']."</option>";
			}
			else
			{
			 echo "<option value='".$row['Forcod']."'>".$row['Fordes']."</option>";
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
				echo "<option selected value='".$row['Forcod']."'>".$row['Fordes']."</option>";
			}
			else
			{
				echo "<option value='".$row['Forcod']."'>".$row['Fordes']."</option>";
			}
		}

	}
	echo "</select>";
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
	  ."    AND Enctem = '".$wtemainterno."' " ;

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

?>
<head>
  <title>Lista de Encuestados</title>
</head>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<script type="text/javascript">
var selectencuestas = null;
function SeleccionTema(tema,wtip,elemento){
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
		, function(data) {
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

function fnMostrar2( ){

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

		if( $('#'+celda ) ){
			$.blockUI({ message: $('#'+celda ),
							css: { left: ( $(window).width() - 1000 )/2 +'px',
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

var params   = 'RealizarListaEncuestas.php?consultaAjax=&woperacion=resultadosLista&wemp_pmla='+emp_pmla+'&wtema='+tema+'&wfecha_i='+fecha_i+'&wfecha_f='+fecha_f+'&wcco='+cco+'&wtemainterno='+$('#wtemainterno').val();

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
	ultimaencuesta = 0;
}

if (elemento.checked)
{
	alert("1");
	if (selectencuestas != null)
	{
		$('#'+contenedor).html(selectencuestas);
		if ( ultimaencuesta != 0){
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
			guardarpaciente(pacno1,pacno2,pacap1,pacap2,pacced,pactid,Habcod,Orihis,Oriing,ccocod,fecha,contenedor,elemento,ultimaencuesta,telefono,edad,nre,wtpa,codentidad);
			});

	}

}
else
{
	alert("2");
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
		alert("3");
		var params   = 'RealizarListaEncuestas.php?consultaAjax=&woperacion=agregarencuesta&wemp_pmla='+emp_pmla+'&wtema='+tema+'&wultimaencuesta='+ultimaencuesta;
		//alert(params);
		$.get(params, function(data) {
		$('#'+contenedor).html(data);
		selectencuestas=data;
		ultimaencuesta = $("#"+contenedor+" select").val();
		guardarpaciente(pacno1,pacno2,pacap1,pacap2,pacced,pactid,Habcod,Orihis,Oriing,ccocod,fecha,contenedor,elemento,ultimaencuesta,telefono,edad,nre,wtpa,codentidad);
		});
	}
	else
	{
		$('#'+contenedor).html('');
		var params   = 'RealizarListaEncuestas.php?consultaAjax=&woperacion=eliminarPacienteEncuesta&wemp_pmla='+emp_pmla+'&wtema='+tema+'&wced='+pacced+'&wno1='+pacno1+'&wno2='+pacno2+'&wap1='+pacap1+'&wap2='+pacap2+'&wtid='+pactid+'&whcod='+Habcod+'&wing='+Oriing+'&wcco='+cco+'&wfechaing='+fecha+'&whis='+Orihis+'&wtemainterno='+$('#wtemainterno').val();
		$.get(params, function(data) {
		if (data=='Encuesta borrada')
		{
			var nombretr= $(elementojq).attr('id');

			//alert($("#"+nombretr+" td").eq(13).text());
			$("#"+nombretr+"").parent().parent().find("td").eq(15).text(" ");


		}
		
	}
	
}

}

function cambiaUltimaEncuesta (elemento,tr,periodo)
{
	//alert(periodo);
	
	var emp_pmla = document.getElementById('wemp_pmla').value;
	var tema =  document.getElementById('wtema').value;
	var cco = document.getElementById('wcco').value;
	var ultimaencuesta = elemento.value;
	document.getElementById('ultimaencuesta').value = ultimaencuesta;

	 pactid = ($(tr).find('td').eq(2).html()) ;
	 pacced = ($(tr).find('td').eq(3).html()) ;
	 Orihis = ($(tr).find('td').eq(5).html()) ;
	 Oriing = ($(tr).find('td').eq(6).html()) ;
	 Habcod = ($(tr).find('td').eq(8).html()) ;
	 fecha  =($(tr).find('td').eq(13).html()) ;
	 ccocod = document.getElementById('wcco').value;
	 var periodo = periodo;

	var params   = 'RealizarListaEncuestas.php?consultaAjax=&woperacion=actualizadato&wemp_pmla='+emp_pmla+'&wtema='+tema+'&wced='+pacced+'&wtid='+pactid+'&whcod='+Habcod+'&wing='+Oriing+'&wcco='+ccocod+'&wfechaing='+fecha+'&whis='+Orihis+'&wencuesta='+ultimaencuesta+'&wtemainterno='+$('#wtemainterno').val()+'&periodo='+periodo;
	$.get(params, function(data) {

	});

}

function cambiaUltimaEvaluacion (elemento,tr)
{
	
	
var emp_pmla = document.getElementById('wemp_pmla').value;
var tema =  document.getElementById('wtema').value;
var cco = document.getElementById('wccostos_pfls').value;

var ultimaencuesta = elemento.value;
document.getElementById('ultimaencuesta').value = ultimaencuesta;

var empleado=($(tr).find('td').eq(1).html()) ;

if ($('#wtipointerno').val() =='03' || $('#wtipointerno').val() =='05')
  {
	var params   = 'RealizarListaEncuestas.php?consultaAjax=&woperacion=actualizadato&wemp_pmla='+emp_pmla+'&wtema='+tema+'&whis='+empleado+'&wencuesta='+ultimaencuesta+'&wtemainterno='+$('#wtemainterno').val();
	$.get(params, function(data) {

	});
  }
  else
  {
	var params   = 'RealizarListaEncuestas.php?consultaAjax=&woperacion=actualizaEmpleadoEvaluacion&wemp_pmla='+emp_pmla+'&wtema='+tema+'&wusuario='+empleado+'&wevaluacion='+ultimaencuesta+'&wtemainterno='+$('#wtemainterno').val();
	$.get(params, function(data) {

	});

  }

}

function	guardarpaciente(pacno1,pacno2,pacap1,pacap2,pacced,pactid,Habcod,Orihis,Oriing,ccocod,fecha,contenedor,elemento,ultimaencuesta,telefono,edad,nre,wtpa,codentidad)
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
		$('#'+id_hijo).load(
				"gestion_perfiles.php",
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
	});
}

function seleccionevaluacion(elemento,contenedor,usuario,no1,no2,ap1,ap2)
{
	var emp_pmla = document.getElementById('wemp_pmla').value;
	var tema =  document.getElementById('wtema').value;
	var ultimaencuesta = document.getElementById('ultimaencuesta').value;

	if (ultimaencuesta.length == 0)
	{
		ultimaencuesta = 0;
	}
 if (elemento.checked)
 {
	if (selectencuestas != null)
	 {
		 $('#'+contenedor).html(selectencuestas);
		 if ( ultimaencuesta != 0){
			 $('#'+contenedor+" select").val(ultimaencuesta);
		}
		guardarempleado(usuario,ultimaencuesta,no1,no2,ap1,ap2);
	 }
	 else
 {

		var params   = 'RealizarListaEncuestas.php?consultaAjax=&woperacion=agregarevaluacion&wemp_pmla='+emp_pmla+'&wtema='+tema+'&wultimaencuesta='+ultimaencuesta+'&wtemainterno='+$('#wtemainterno').val();

			 $.get(params, function(data) {
			 $('#'+contenedor).html(data);
			 selectencuestas=data;
			 ultimaencuesta = $("#"+contenedor+" select").val();
			 guardarempleado(usuario,ultimaencuesta,no1,no2,ap1,ap2);
			 });

	 }

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

  }else
  {

	var params   = 'RealizarListaEncuestas.php?consultaAjax=&woperacion=eliminarPacienteEncuesta&wemp_pmla='+emp_pmla+'&wtema='+tema+'&wced='+usuario+'&whis='+usuario+'&wtemainterno='+$('#wtemainterno').val();
	$.get(params, function(data) {

    });
  }

 }

}

</script>

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
//FECHA ULTIMA ACTUALIZACION : Enero 22 de 2016

//DESCRIPCION
//========================================================================================================================================\\
//========================================================================================================================================\\
//   Este Programa realiza un registro de personas a encuestar de una lista total de pacientes  en cada servicio
//   De esta manera se facilita la realizacion de las encuestas															                    \\
//========================================================================================================================================\\
//========================================================================================================================================\\
//ACTUALIZACIONES                                                                        \\
//========================================================================================================================================\\
//
//Febrero 1 2016: Felipe Alvarez: Se modifica la opcion de  GrabarPacientedigitado mejorandolo, se añade funcionalidad
//de poder saber , si la historia ya esta guardada y a los campos obligatorios que hay que digitar se les pone una clase nueva
//Enero 28 2016: Juan C Hdez: Se modifica el query principal para seleccionar los pacientes, era demasiado demorado
//Enero 22 de 2016 Jonatan: Se corrige la consulta principal ya que demoraba en responder, ademas se cambio la base de datos movhos
//por la variable $wbasedatos_movhos.
//========================================================================================================================================\\
echo "<input type='hidden' id='wtema' value='".$wtema."'>";
echo "<input type='hidden' id='wemp_pmla' value='".$wemp_pmla."'>";
echo "<input type='hidden' id='ultimaencuesta' value='".$ultimaencuesta."'>";
echo "<input type='hidden' id='wtemainterno' value=''>";
echo "<input type='hidden' id='wtipointerno' value=''>";
echo "<input type='hidden' id='wpublicoobjetivo' value=''>";
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
			<td>".utf8_encode($row['Fordes'])."</td>
		</tr>";
   $k++;

}
echo '</table><br><br>';
echo "</div>";
echo "<div id='divdiv'></div>";
?>
</body>