<?php
include_once("conex.php");
/**
PROGRAMA:			PORCENTAJES DE PARTICIPACION TERCEROS
DESCRIPCION:		Programa que permite grabar los porcentajes de participacion de los terceros en conceptos compartidos
					ademas de esto permite visualizar lo que ya esta parametrizado y editarlo
AUTOR:				German Felipe Alvarez
FECHA DE CREACION:	24/06/2013

--------------------------------------------------------------------------------------------------------------------------------------------
*/$wactualiza='17 Diciembre 2014';/*
ACTUALIZACIONES

Diciembre 17 2014
Edwar Jaramillo:	* Se adiciona una nueva columna para clasificar los medicos según el grupo al que pertenecen, esto se hace en la sección de configuración de NITs de médicos.
--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
*/
//--------------------------------------------------------------------------------------------------------------------------------------------
// 	EJECUCION DEL SCRIPT
//--------------------------------------------------------------------------------------------------------------------------------------------


if(!isset($_SESSION['user']))
{
    echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
else
{
	//header('Content-type: text/html;charset=ISO-8859-1');
	$user_session = explode('-',$_SESSION['user']);
	$wuse = $user_session[1];
	

	include_once("root/comun.php");
	

	include_once("ips/funciones_facturacionERP.php");
	include "../../gesapl/procesos/gestor_aplicaciones_config.php";
	include_once("../../gesapl/procesos/gesapl_funciones.php");
	$conex = obtenerConexionBD("matrix");
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
	$wfecha=date("Y-m-d");
    $whora = date("H:i:s");


//=====================================================================================================================================================================
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================
//----------------------------
//	Nombre: nueva_participacion_terceros
//	Descripcion: html encargado de la grabacion de una configuracion de porcentaje de participacion
//	Entradas:
//	Salidas:
//----------------------------
function nueva_participacion_terceros()
{
	// global $wbasedato;
	// global $conex;
	// global $wemp_pmla;

	// $html= "<table align='center' width='800'>
				// <tr>
					// <td align='Left'  colspan='5' class='fila1'>Multitercero:<input type='checkbox' id='checkbox_multi' value='ok' onclick='activa_multitercero(this)'></td>
				// </tr>
				// <tr>
					// <td align='center' class='fila1'>Medico:</td>
					// <td align='center' class='fila1'>Especialidad:</td>
					// <td align='center' class='fila1'>Concepto:</td>
					// <td align='center' class='fila1'>Procedimiento:</td>
					// <td align='center' class='fila1'>Clasificacion:</td>
				// </tr>
				// <tr>
					// <td class='fila2 pad' align='center'><input type='text'  id='busc_terceros' size='35'  class='reset campoRequerido requerido' msgError='Digite el medico' onBlur='filtrar_especialidad(this);'></td>
					// <td class='fila2 pad' align='center'><Select  id='busc_especialidades' msgError class='campoRequerido requerido' ><option value='' selected>Seleccione especialidad</option></Select></td>
					// <td class='fila2 pad' align='center'><input type='text' id='busc_concepto' size='35' class='reset campoRequerido requerido' msgError='Digite el concepto' ></td>
					// <td class='fila2 pad'><input type='text' id='busc_procedimiento' size='38' class='reset campoRequerido requerido' msgError='Digite el procedimiento' ></td>
					// <td class='fila2 pad'><input type='text' id='busc_clasificacion' size='32' class='reset campoRequerido requerido' msgError='Digite la clasificacion' ></td>
				// </tr>
				// <tr>
					// <td align='center' class='fila1'>Tipo de Empresa:</td>
					// <td align='center' class='fila1'>Tarifa:</td>
					// <td align='center' class='fila1'>Entidad:</td>
					// <td align='center' class='fila1' colspan='1'>Centro de costos:</td>
					// <td align='center' class='fila1'>Clasificacion Cuadro de turno</td>

				// </tr>
				// <tr>
					// <td class='fila2'><input type='text' id='busc_tip_empresa' size='35' class='reset campoRequerido requerido'  ></td>
					// <td class='fila2'><input type='text' id='busc_tarifa' size='35' class='reset campoRequerido requerido'  ></td>
					// <td class='fila2'><input type='text' id='busc_entidad' size='35' class='reset campoRequerido requerido'  ></td>
					// <td class='fila2' ><input type='text' id='busc_cco' size='38' class='reset campoRequerido requerido'  ></td>
					// <td class='fila2'><Select id='clasificacion_cuadro_turnos'   msgError class='campoRequerido requerido' ><option value='' selected>Seleccione especialidad</option>";

					// $wbasedatomedico = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
					// $q = "SELECT Ccucod,Ccunom
							// FROM ".$wbasedatomedico."_000160
							// WHERE Ccuest='on' ";

					// $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query (mostrar lista ): ".$q." - ".mysql_error());

					// while($row = mysql_fetch_array($res))
					// {
						// $html.="<option value='".$row['Ccucod']."'>".$row['Ccunom']."</option>";

					// }


	// $html.=			"</select></td>
				// </tr>
				// <tr>
					// <td class='fila1' >Tercero Unix</td><td class='fila1' >Participacion</td><td class='fila1' >Porcentaje</td><td colspan='2'></td>
				// <tr>
				// <tr>
					// <td class='fila2' ><input type ='text' id='codigotercerounix' class='reset campoRequerido requerido'></td><td class='fila2' ><select id='Codigoparticipacion' class='reset campoRequerido requerido'>";
						// $html.= trae_participaciones();
	// $html.=				"</select></td>
					// <td><input type ='text' id='codigoporcentaje' class='reset campoRequerido requerido'></td>
					// <td colspan='2'></td>
				// <tr align='center'>
					// <td colspan='5'><input type='button' value='Grabar' onclick='guardar_relacion()'><input type='button' value='Limpiar' onclick='cancelar()'></td>
				// </tr>
				// <tr>
					// <td colspan='5'><div id='div_mensajes' class='fondoAmarillo' style='display:none;border: 1px solid #2A5DB0;padding: 5px;' align='center'></div></td>
				// </tr>
		// </table>";

	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	$html= "<table align='center' width='800'>
				<tr>
					<td align='Left'  colspan='5' class='fila1'>Multitercero:<input type='checkbox' id='checkbox_multi' value='ok' onclick='activa_multitercero(this)'></td>
				</tr>
				<tr>
					<td align='center' class='fila1'>Medico:</td>
					<td align='center' class='fila1'>Especialidad:</td>
					<td align='center' class='fila1'>Concepto:</td>
					<td align='center' class='fila1'>Procedimiento:</td>
					<td align='center' class='fila1'>Clasificacion:</td>
				</tr>
				<tr>
					<td class='fila2 pad' align='center'><input type='text'  id='busc_terceros' size='35'  class='reset campoRequerido requerido' msgError='Digite el medico' onBlur='filtrar_especialidad(this);'></td>
					<td class='fila2 pad' align='center'><Select  id='busc_especialidades' msgError class='campoRequerido requerido' ><option value='' selected>Seleccione especialidad</option></Select></td>
					<td class='fila2 pad' align='center'><input type='text' id='busc_concepto' size='35' class='reset campoRequerido requerido' msgError='Digite el concepto' ></td>
					<td class='fila2 pad'><input type='text' id='busc_procedimiento' size='38' class='reset campoRequerido requerido' msgError='Digite el procedimiento' ></td>
					<td class='fila2 pad'><input type='text' id='busc_clasificacion' size='32' class='reset campoRequerido requerido' msgError='Digite la clasificacion' ></td>
				</tr>
				<tr>
					<td align='center' class='fila1'>Tipo de Empresa:</td>
					<td align='center' class='fila1'>Tarifa:</td>
					<td align='center' class='fila1'>Entidad:</td>
					<td align='center' class='fila1' colspan='1'>Centro de costos:</td>
					<td align='center' class='fila1'>Nombre pool</td>

				</tr>
				<tr>
					<td class='fila2'><input type='text' id='busc_tip_empresa' size='35' class='reset campoRequerido requerido'  ></td>
					<td class='fila2'><input type='text' id='busc_tarifa' size='35' class='reset campoRequerido requerido'  ></td>
					<td class='fila2'><input type='text' id='busc_entidad' size='35' class='reset campoRequerido requerido'  ></td>
					<td class='fila2' ><input type='text' id='busc_cco' size='38' class='reset campoRequerido requerido'  ></td>
					<td class='fila2'><Select id='clasificacion_cuadro_turnos'   msgError class='campoRequerido requerido' ><option value='' selected>Seleccione especialidad</option>";

					// $wbasedatomedico = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
					// $q = "SELECT Ccucod,Ccunom
							// FROM ".$wbasedatomedico."_000160
							// WHERE Ccuest='on' ";

					// $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query (mostrar lista ): ".$q." - ".mysql_error());

					$wbasedatomedico = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
					$q = "SELECT Gmecod,Gmenom
							FROM ".$wbasedatomedico."_000166
							WHERE Gmeest='on' ";

					$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query (mostrar lista ): ".$q." - ".mysql_error());

					while($row = mysql_fetch_array($res))
					{
						$html.="<option value='".$row['Gmecod']."'>".$row['Gmenom']."</option>";

					}


	$html.=			"</select></td>
				</tr>
				<tr>
					<td class='fila1' >Tercero Unix</td><td class='fila1' >Participacion</td><td class='fila1' >Porcentaje</td><td colspan='2'></td>
				<tr>
				<tr>
					<td class='fila2' ><input type ='text' id='codigotercerounix' size='35' class='reset campoRequerido requerido'></td><td class='fila2' ><select id='Codigoparticipacion' class='reset campoRequerido requerido'>";
						$html.= trae_participaciones();
	$html.=				"</select></td>
					<td><input type ='text' id='codigoporcentaje'  size='35' class='reset campoRequerido requerido'></td>
					<td colspan='2'></td>
				<tr align='center'>
					<td colspan='5'><input type='button' value='Grabar' onclick='guardar_relacion()'><input type='button' value='Limpiar' onclick='cancelar()'></td>
				</tr>
				<tr>
					<td colspan='5'><div id='div_mensajes' class='fondoAmarillo' style='display:none;border: 1px solid #2A5DB0;padding: 5px;' align='center'></div></td>
				</tr>
		</table>";


	return $html;
}
//----------------------------
//	Nombre: buscador
//	Descripcion: html encargado de pintar el buscador de los porcentajes ya existentes
//	Entradas:
//	Salidas:
//----------------------------
function buscador()
{
	global $wbasedato;
	global $conex;
	global $wemp_pmla;
	$html="<table width='100%' class='Bordegris fila2' style='padding:2px;' id='tablaFiltros'>
		<tr>
			<td>
				<img width='18' height='18' src='../../images/medical/HCE/lupa.PNG' title='Buscar'>
			</td>
			<td>Medico: <input size='15' id='buscar_por_medico' type='text'  >
		</tr>
	</table>";
	return $html;
}
//----------------------------
//	Nombre:  lista_de_participaciones
//	Descripcion:
//	Entradas:
//	Salidas:
//----------------------------
function lista_de_participaciones($filtro='')
{
	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	$html ="<table align='center'  width='100%' id=tabla_lista>
				<tr>
					<td>
						<table  width='100%' align='center' id='tabla_participaciones'>";

	$wbasedatomedico = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');


	$q=  " SELECT Medno1, Medno2, Medap1, Medap2,Meddoc"
		."   FROM ".$wbasedatomedico."_000048, ".$wbasedato."_000102  "
		."  WHERE Meddoc = Relmed "
		."    AND Relest = 'on' "
		."	  AND Medest ='on' "
		."	";


	if($filtro!='')
		$q.="AND CONCAT(Medno1,Medno2,Medap1,Medap2) LIKE '%".$filtro."%' ";

	$q.=	"  GROUP BY Meddoc"
			."  ORDER BY LTRIM(Medno1),Medno2,Medap1,Medap2";

	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query (mostrar lista ): ".$q." - ".mysql_error());

	$h=0;
	while($row = mysql_fetch_array($res))
	{
		//---
		if (is_int ($h/2))
			$wcf="fila1";  // color de fondo de la fila
		else
			$wcf="fila2"; // color de fondo de la fila

		$nombre 	= 	$row['Medno1']." ".$row['Medno2']." ".$row['Medap1']." ".$row['Medap2'];
		$html.=			"<tr id='tr_".$row['Meddoc']."' class='find' style='background-color : #999999'   ><td colspan='14'  style='cursor: pointer' onclick='traer_porcentajes(\"".$row['Meddoc']."\")' ><img src='../../images/medical/hce/mas.PNG'>".$row['Meddoc']."&nbsp;-&nbsp;".utf8_encode($nombre)."</td></tr>";
		$h++;

	}
	$html.="				</table>

				</td>
			</tr>
		</table>";

	return $html;

}


function listabusquedaparticipaciones()
{





	// global $wbasedato;
	// global $conex;
	// global $wemp_pmla;



	// $wbasedatomedico = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');


	// $q=  " SELECT Medno1, Medno2, Medap1, Medap2,Meddoc,Espnom,Grudes,Pronom, Ccodes,Temdes,Empnom,Reltar,Relcla"
		// ."   FROM ".$wbasedato."_000102
			  // LEFT JOIN ".$wbasedatomedico."_000044 ON (Espcod = Relesp)
		      // LEFT JOIN  ".$wbasedato."_000200 ON ( Relcon = Grucod )
		      // LEFT JOIN  ".$wbasedato."_000103 ON ( Relpro = Procod  )
		      // LEFT JOIN  ".$wbasedato."_000003 ON ( Relcco = Ccocod  )
		      // LEFT JOIN  ".$wbasedato."_000029 ON ( Reltem = Temcod  )
		      // LEFT JOIN  ".$wbasedato."_000024 ON ( Relemp = Empcod  )
			  // , ".$wbasedatomedico."_000048   "
		// ."  WHERE Meddoc = Relmed "
		// ."    AND Relest = 'on' "
		// ."	  AND Medest ='on' ";



	// $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query (mostrar lista ): ".$q." - ".mysql_error());
	// $html = $q;
	// $html .="<table align='center'  width='100%'>";
	// $h=0;

	// while($row = mysql_fetch_array($res))
	// {
		// ---
		// if (is_int ($h/2))
			// $wcf="fila1";  // color de fondo de la fila
		// else
			// $wcf="fila2"; // color de fondo de la fila
		// if($row['Espnom'] !='')
		// {
			// $especialidad 	= $row['Espnom'];
		// }
		// else
		// {
		   // $especialidad 	= '*';
		// }
		// if($row['Grudes'] !='')
		// {
			// $concepto 	= $row['Grudes'];
		// }
		// else
		// {
		   // $concepto 	= '*';
		// }
		// if($row['Pronom'] !='')
		// {
			// $procedimiento 	= $row['Pronom'];
		// }
		// else
		// {
		   // $procedimiento 	= '*';
		// }
		// if($row['Ccodes'] !='')
		// {
			// $centrocostos 	= $row['Ccodes'];
		// }
		// else
		// {
		   // $centrocostos 	= '*';
		// }
		// if($row['Temdes'] !='')
		// {
			// $tipoempresa 	= $row['Temdes'];
		// }
		// else
		// {
		   // $tipoempresa 	= '*';
		// }
		// if($row['Empnom'] !='')
		// {
			// $nombreempresa 	= $row['Empnom'];
		// }
		// else
		// {
		   // $nombreempresa 	= '*';
		// }


		// $nombre 		 = $row['Medno1']." ".$row['Medno2']." ".$row['Medap1']." ".$row['Medap2'];
		// $html			.="<tr class='".$wcf."'  style='cursor: pointer' onclick='nada(\"".$row['Meddoc']."\")' ><td>".utf8_encode($nombre)."</td><td>".$especialidad."</td><td>".$concepto."</td><td>".$procedimiento."</td><td>".$centrocostos."</td><td>".$tipoempresa."</td><td>".$nombreempresa."</td><td>".$row['Reltar']."</td><td>".$row['Relcla']."</td><td>".$row['Relccu']."</td></tr>";
		// $h++;

	// }
	// $html.="</table>";

	// return $html;





}

function tercerosxdefecto()
{

	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	$wbasedatomedico = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	// vector con tercero y concepto por defecto
	$q = "SELECT Ctecon , Cteter , Medno1 , Medno2, Medap1, Medap2
			FROM ".$wbasedato."_000218 , ".$wbasedatomedico."_000048
		   WHERE Cteest = 'on'
		     AND Cteter = Meddoc" ;
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query (mostrar lista ): ".$q." - ".mysql_error());
	$vectorresxcon = array();
	while($row = mysql_fetch_array($res))
	{
		$vectorresxcon[$row['Ctecon']]['codigo']=$row['Cteter'];
		$vectorresxcon[$row['Ctecon']]['nombre']=$row['Medno1']." ".$row['Medno2']." ".$row['Medap1']." ".$row['Medap2'];
	}

	$html .= "<table>
				<tr>
					<td class='encabezadoTabla'>Codigo</td>
					<td class='encabezadoTabla'>Concepto</td>
					<td class='encabezadoTabla'>Tercero</td>
					<td class='encabezadoTabla'></td>
				</tr>";

	$q = "SELECT Grucod, Grudes
			FROM ".$wbasedato."_000200
			WHERE Gruest = 'on'";

	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query (mostrar lista ): ".$q." - ".mysql_error());

	$h = 0;
	while($row = mysql_fetch_array($res))
	{

		if (is_int ($h/2))
			$wcf="fila1";  // color de fondo de la fila
		else
			$wcf="fila2"; // color de fondo de la fila

		$html .="<tr class = ".$wcf."><td>".$row['Grucod']."</td><td>".$row['Grudes']."</td>
									  <td id='td_tercero_por_defecto_".$row['Grucod']."'>";
									  if(array_key_exists( $row['Grucod'] , $vectorresxcon ))
									  {
										$html.=	$vectorresxcon[$row['Grucod']]['nombre'];
										$cod_ter = $vectorresxcon[$row['Grucod']]['codigo'];
										$nom_ter = $vectorresxcon[$row['Grucod']]['nombre'];
									  }
									  else
									  {
										$html.="ningun tercero por defecto";
										$cod_ter = "nada";
										$nom_ter = "nada";

									  }
		$html .=					  "</td>
									  <td id='img_tercero_por_".$row['Grucod']."'><img onclick='edita_terceroxdefecto(\"".$row['Grucod']."\",\"".$cod_ter."\",\"".$nom_ter."\")'  style='cursor: pointer;' title='Editar'  src='../../images/medical/hce/mod.PNG'>&nbsp;&nbsp;<img onclick='elimina_terceroxdefecto(\"".$row['Grucod']."\")'  style='cursor: pointer;' title='Eliminar'  src='../../images/medical/hce/cancel.PNG'></td></tr>";
		$h++;
	}




	$html .="</table>";

	return $html;


}

function nits_terceros(&$total_listado)
{
	global $wbasedato;
	global $conex;
	global $wemp_pmla;
	$wbasedatomedico = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

	$arr_grupos_medico = gruposMedicos($conex, $wemp_pmla, $wbasedato);

	// vector con tercero y concepto por defecto
	$q = "SELECT Meddoc, Medno1 , Medno2, Medap1, Medap2, Mednho, Medgru
			FROM  ".$wbasedatomedico."_000048
		   WHERE Medest = 'on'
		   GROUP BY Meddoc
		   ORDER BY  Medno1 , Medno2, Medap1, Medap2  ";

	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query (mostrar lista ): ".$q." - ".mysql_error());

	$html = '<table id="tabla_medicos_nits" align="left" width="90%">
				<tr>
					<td class="encabezadoTabla"> </td>
					<td class="encabezadoTabla">Codigo</td>
					<td class="encabezadoTabla">Nombre</td>
					<td class="encabezadoTabla">Nit donde se graba el honorario</td>
					<td class="encabezadoTabla">Grupo</td>
				</tr>';
	$select_grupo_html = '<select ><option value="">Seleccione</option>';
	foreach ($arr_grupos_medico as $cod_grupo => $nombre_grupo)
	{
		$sltd = ($cod_grupo == $row['Medgru']) ? 'selected="selected"': '';
		$select_grupo_html .= '<option value="'.$cod_grupo.'" '.$sltd.' >'.$cod_grupo.'-'.utf8_encode($nombre_grupo).'</option>';
	}
	$select_grupo_html .= '</select>';

	$h=0;
	$total_listado = 0;
	while($row = mysql_fetch_array($res) )
	{

		if (is_int ($h/2))
			$wcf="fila1";  // color de fondo de la fila
		else
			$wcf="fila2"; // color de fondo de la fila

		$nombre = $row['Medno1']." ".$row['Medno2']." ".$row['Medap1']." ".$row['Medap2'];
		$nombre = utf8_encode($nombre);

		$select_grupo = (array_key_exists($row['Medgru'],$arr_grupos_medico)) ? $arr_grupos_medico[$row['Medgru']]: '';
		// foreach ($arr_grupos_medico as $cod_grupo => $nombre_grupo)
		// {
			// $sltd = ($cod_grupo == $row['Medgru']) ? 'selected="selected"': '';
			// $select_grupo .= '<option value="'.$cod_grupo.'" '.$sltd.' >'.$cod_grupo.'-'.utf8_encode($nombre_grupo).'</option>';
		// }

		$html .='<tr class="find">
					<td id="img_nit_codigo'.$row['Meddoc'].'" class="'.$wcf.'"><img onclick="edita_nit_codigo(\''.$row['Meddoc'].'\' , \''.$row['Mednho'].'\');"  style="cursor: pointer;" title="Editar"  src="../../images/medical/hce/mod.PNG"></td>
					<td class="'.$wcf.'">'.$row['Meddoc'].'</td>
					<td class="'.$wcf.'">'.$nombre.'</td>
					<td id="td_nit_codigo'.$row['Meddoc'].'" align="center" class="'.$wcf.'" name="nit_codigo_cobra_'.$row['Meddoc'].'">'.$row['Mednho'].'</td>
					<td id="td_grupo_codigo'.$row['Meddoc'].'" codGrupo="'.$row['Medgru'].'" align="center" class="'.$wcf.'" name="td_grupo_codigo'.$row['Meddoc'].'">
							'.$row['Medgru'].'-'.utf8_encode($select_grupo).'
					</td>
				</tr>';
		$h++;
		$total_listado++;
	}


	$html .="</table><div id='div_select' style='display:none;'>".$select_grupo_html."</div>";

	return $html;
}


function gruposMedicos($conex, $wemp_pmla, $wbasedato)
{
	$wbasedatomedico = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$sql = "SELECT 	Gmecod, Gmenom
			FROM 	{$wbasedatomedico}_000166
			WHERE 	Gmeest = 'on'";
	$result = mysql_query($sql,$conex) or die ("Error: ".mysql_errno()." - en el query (mostrar lista ): ".$sql." - ".mysql_error());
	$arr_grupos_medico = array();
	while ($row = mysql_fetch_array($result))
	{
		if(!array_key_exists($row['Gmecod'], $arr_grupos_medico))
		{
			$arr_grupos_medico[$row['Gmecod']] = "";
		}
		$arr_grupos_medico[$row['Gmecod']] = $row['Gmenom'];
	}
	return $arr_grupos_medico;
}

function traer_porcentajes($codmedico,$vector_conceptos, $vector_especialidad,$vector_tipo_entidad,$vector_entidad,$vector_procedimiento,$vector_cco,$vector_tarifas,$vector_participaciones,$vector_clasificaciones)
{
	global $wbasedato;
	global $conex;
	global $wemp_pmla;
	
	// $wbasedatomedico = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

	// $q =	"SELECT  Ccucod,Ccunom FROM ".$wbasedatomedico."_000160  ";
	// $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query (mostrar lista ): ".$q." - ".mysql_error());
	// $vector_clasificacion_cuadros = array();
	// while($row = mysql_fetch_array($res))
	// {
		// $vector_clasificacion_cuadros[$row['Ccucod']] = $row['Ccunom'];
	// }

	$wbasedatomedico = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

	$q =	"SELECT  Gmecod,Gmenom FROM ".$wbasedatomedico."_000166  ";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query (mostrar lista ): ".$q." - ".mysql_error());
	$vector_clasificacion_cuadros = array();
	while($row = mysql_fetch_array($res))
	{
		$vector_clasificacion_cuadros[$row['Gmecod']] = $row['Gmenom'];
	}

	$q= "SELECT  Relesp, Relpro, Relcon,Reltem, Relemp,Relcco,Relccu,Relpor,".$wbasedato."_000102.id,Reltar,Relpar,Relcla,Relcun,Relest
		   FROM ".$wbasedato."_000102
		 WHERE Relmed='".$codmedico."'
		   ORDER BY Relest DESC,".$wbasedato."_000102.id,Relesp,Relcon,Relpro,Reltem,Relemp,Relcco,Relccu,Relpar,Relpor";


/*
	$q= "SELECT Medno1, Medno2, Medap1, Medap2,Meddoc, Relesp, Relpro, Relcon,Reltem, Relemp,Relcco,Relccu,Relpor,".$wbasedato."_000102.id,Reltar,Relpar,Relcla,Relcun,Relest
		   FROM ".$wbasedatomedico."_000048, ".$wbasedato."_000102
		 WHERE Relmed='".$codmedico."'
		   AND Meddoc = Relmed
		   AND Medest ='on'
		   ORDER BY Meddoc,Relest DESC,".$wbasedato."_000102.id,Relesp,Relcon,Relpro,Reltem,Relemp,Relcco,Relccu,Relpar,Relpor";

*/
	$vector_conceptos= json_decode(stripslashes($vector_conceptos), true);
	$vector_conceptos['*']='Todos';
	$vector_participaciones = json_decode(stripslashes($vector_participaciones), true);
	$vector_participaciones['*']='Todos';
	$vector_especialidad= json_decode(stripslashes($vector_especialidad), true);
	$vector_especialidad['*']='Todos';
	$vector_entidad= json_decode(stripslashes($vector_entidad), true);
	$vector_entidad['*']='Todos';
	$vector_tipo_entidad= json_decode(stripslashes($vector_tipo_entidad), true);
	$vector_tipo_entidad['*']='Todos';
	$vector_procedimiento= json_decode($vector_procedimiento, true);
	$vector_procedimiento['*']='Todos';
	// print_r($vector_procedimiento);
	$vector_clasificaciones= json_decode(stripslashes($vector_clasificaciones), true);
	$vector_clasificaciones['*']='Todos';



	$vector_cco= json_decode(stripslashes($vector_cco), true);
	$vector_cco['*']='Todos';
	$vector_tarifas= json_decode($vector_tarifas, true);
	$vector_tarifas['*']='Todos';

	$vector_registro=array();
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query (mostrar lista ): ".$q." - ".mysql_error());

	$h=0;
	$html.="<tr id='detalle_".$codmedico."' class='ocultar_".$codmedico." ocultartodo'>
				<td></td>
				<td width='100' class='encabezadoTabla1'>Especialidad</td>
				<td width='240' class='encabezadoTabla1' >Concepto</td>
				<td width='190' class='encabezadoTabla1'>Procedimiento</td>
				<td width='160' class='encabezadoTabla1'>Clasificacion</td>
				<td width='140' class='encabezadoTabla1'>Tipo de Empresa</td>
				<td width='165' class='encabezadoTabla1'>Tarifa</td>
				<td width='115' class='encabezadoTabla1'>Entidad</td>
				<td width='115' class='encabezadoTabla1'>C. de costos</td>
				<td width='115' class='encabezadoTabla1'>C. de turno</td>
				<td width='100' class='encabezadoTabla1'>Tercero Unix</td>
				<td width='25' class='encabezadoTabla1'>Participación</td>
				<td width='25' class='encabezadoTabla1'>%</td>
				<td></td>
			</tr>";

	$vec_icono = array();
	while($row = mysql_fetch_array($res))
	{
			//---
			if (is_int ($h/2))
				$wcf="fila11";  // color de fondo de la fila
			else
				$wcf="fila22"; // color de fondo de la fila

			$estado_porcentaje = $row['Relest'];

			if(array_key_exists($row['Relcon'], $vector_conceptos))
			{

				$clave = $row['Relesp']."_".$row['Relcon']."_".$row['Relpro']."_".$row['Reltem']."_".$row['Reltar']."_".$row['Relemp']."_".$row['Relcco']."_".$row['Relpar'];

				$clave =  str_replace("*", "t",$clave );
				if(array_key_exists($clave,$vector_registro) )
				{
						$vec_icono[]=$clave;
				}


				$nombre = $row['id'];
				$html.="<tr   id='tr_".$nombre."' class='ocultar_".$codmedico." ".$clave." ocultartodo'>
							<td>";
				if($estado_porcentaje=='off')
				{
						$html.="<img id='desactivar_activar_muestra".$row['id']."'   style='cursor: pointer;'  width='13' height='13' title='Inactivo' src='../../images/medical/sgc/no_conforme.png'></td>";
				}
				else
				{
						$html.="<img id='desactivar_activar_muestra".$row['id']."'   style='cursor: pointer;'  width='13' height='13' title='Activo' src='../../images/medical/sgc/circuloVerde.png'></td>";

				}

					$html.="<td width='100' class='".$wcf."' id='".$row['Relesp']."' >".$row['Relesp']."-".$vector_especialidad[$row['Relesp']]."</td>
							<td width='240' class='".$wcf."' >".$row['Relcon']."-".$vector_conceptos[$row['Relcon']]."</td>
							<td width='190' class='".$wcf."' >".$row['Relpro']."-".$vector_procedimiento[$row['Relpro']]."</td>
							<td width='160' class='".$wcf."' >".$row['Relcla']."-".$vector_clasificaciones[$row['Relcla']]." </td>
							<td width='140' class='".$wcf."' >".$row['Reltem']."-".$vector_tipo_entidad[$row['Reltem']]."</td>
							<td class='".$wcf."'  width='165' >".$row['Reltar']."-".$vector_tarifas[$row['Reltar']]."</td>
							<td width='115' class='".$wcf."' >".$row['Relemp']."-".$vector_entidad[$row['Relemp']]."</td>
							<td width='115' class='".$wcf."' >".$row['Relcco']."-".$vector_cco[$row['Relcco']]."</td>
							<td width='115' class='".$wcf."' >".$row['Relccu']."-".$vector_clasificacion_cuadros[$row['Relccu']]."</td>
							<td width='100' nowrap='nowrap' class='".$wcf."' >".$row['Relcun']."</td>
							<td width='125' class='".$wcf."' >".$vector_participaciones[$row['Relpar']]."</td>
							<td width='25' class='".$wcf."' >".$row['Relpor']."</td>
							<td width='25' nowrap='nowrap'>";
							if($row['Ppapar']=='*')
										{
											$row['Ppapar']='todos';
										}

							$html.="<div id='imagenes_tr_".$nombre."' style='display: inline-block; float: right;' >
									<img onclick='edita_porcentaje(\"".$row['id']."\",\"".$row['Relpar']."\",\"tr_".$nombre."\",\"".$row['Relpor']."\",\"".$row['Relcon']."\",\"".$vector_conceptos[$row['Relcon']]."\",\"".$row['Relpro']."\" , \"".$vector_procedimiento[$row['Relpro']]."\",\"".$row['Relesp']."\",\"".$vector_especialidad[$row['Relesp']]."\",\"".$row['Reltem']."\",\"".$vector_tipo_entidad[$row['Reltem']]."\",\"".$row['Relemp']."\",\"".$vector_entidad[$row['Relemp']]."\", \"".$row['Relcco']."\",\"".$vector_cco[$row['Relcco']]."\", \"".$row['Reltar']."\",\"".$vector_tarifas[$row['Reltar']]."\",\"".$codmedico."\",\"".$vector_clasificaciones[$row['Relcla']]."\",\"".$row['Relcla']."\",\"".$row['Relccu']."\",\"".$row['Relcun']."\",\"".$vector_participaciones[$row['Relpar']]."\")'  style='cursor: pointer;' title='Editar'  src='../../images/medical/hce/mod.PNG'>&nbsp;&nbsp;
									<img onclick='elimina_porcentaje(\"".$row['id']."\",\"".$row['Relpar']."\",\"tr_".$nombre."\")'  style='cursor: pointer;'  title='Eliminar' src='../../images/medical/hce/cancel.PNG'>";

							if($estado_porcentaje=='on')
							{
								$html .="&nbsp;<img id='desactivar_activar_".$row['id']."' onclick='desactivar_porcentaje(\"".$row['id']."\")'  style='cursor: pointer;'  width='13' height='13' title='Desactivar' src='../../images/medical/sgc/circuloVerde.png'>";
							}
							else
							{
								$html .="&nbsp;<img id='desactivar_activar_".$row['id']."' onclick='activar_porcentaje(\"".$row['id']."\")'  style='cursor: pointer;'  width='13' height='13' title='activar' src='../../images/medical/sgc/no_conforme.png'>";

							}
							$html.="</div>
							</td>
					</tr>";
			}
		$h++;
		$vector_registro[$clave]='S';

	}
	$html.= "<input type='hidden' id='errores".$codmedico."' value='".json_encode($vec_icono)."'>";


	echo $html;



}

//----------------------------
//	Nombre:
//	Descripcion:
//	Entradas:
//	Salidas:
//----------------------------
function trae_participaciones()
{
	global $wbasedato;
	global $conex;

	$q = "SELECT Clacod, Clades "
		."  FROM root_000099  "
		." WHERE Claest='on'" ;

	$qres = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$qnum = mysql_num_rows($qres);

	if ($qnum != 0)
	{

		$i=1;
		$html.="<option value=''>Seleccione Participacion</option>";
		while($qrow = mysql_fetch_array($qres))
		{

			$html.="<option value='".$qrow['Clacod']."'>".$qrow['Clades']."</option>";

			$i++;
		}

	}

	return $html;
}



function guardar_relacion($cod_medico,$cod_especialidad,$cod_concepto,$Codigoparticipacion,$codporcentaje,$operacion,$wmulti_tercero,$wcco,$wprocedimiento,$wtipoempresa,$wempresa,$wcentrocostos,$wtarifa,$wclasificacion,$clasificacion_cuadro_turnos,$codigotercero)
{
	global $wbasedato;
	global $conex;
	global $wfecha;
	global $whora;

/*
	if($wmulti_tercero)
	{
		//guardo en la tabla 000102 que es la de encabezados
		$q_guardar1="INSERT INTO ".$wbasedato."_000102
								 ( Relcon,  Relmed,  Relesp, Relest, medico ,Fecha_data ,Hora_data ,Seguridad, Relmul, Relcco,Reltar,Relcla,Relccu )
						  VALUES ('".$cod_concepto."','".$cod_medico."','".$cod_especialidad."', 'on','".$wbasedato."','".$wfecha."','".$whora."','C-".$wbasedato."', 'on', '".$wcco."','".$wtarifa."','".$wclasificacion."','".$clasificacion_cuadro_turnos."')";
	}
	else
	{*/
		//guardo en la tabla 000102 que es la de encabezados
		$q_guardar1="INSERT INTO ".$wbasedato."_000102
								 ( Relcon,  Relmed,  Relesp, Relest, medico ,Fecha_data ,Hora_data ,Seguridad, Relmul, Relpro, Reltem, Relemp,Relcco,Reltar,Relcla,Relccu,Relcun, Relpar,Relpor)
						  VALUES ('".$cod_concepto."','".$cod_medico."','".$cod_especialidad."', 'on','".$wbasedato."','".$wfecha."','".$whora."','C-".$wbasedato."', 'off','".$wprocedimiento."','".$wtipoempresa."','".$wempresa."','".$wcentrocostos."','".$wtarifa."','".$wclasificacion."', '".$clasificacion_cuadro_turnos."','".$codigotercero."','".$Codigoparticipacion."','".$codporcentaje."')";

	/*}*/
	echo $resultado = mysql_query($q_guardar1,$conex) or die ("Error: ".mysql_errno()." - en el query (Grabar grupo): ".$q_guardar1." - ".mysql_error());




}
function desactivar_porcentaje($id)
{

	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	$q = "UPDATE ".$wbasedato."_000102 "
		."   SET Relest='off'"
		." 	 WHERE id ='".$id."' ";

	mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query (eliminar participacion 2paso): ".$q." - ".mysql_error());

	//traer_porcentajes($codmedico,$vector_conceptos, $vector_especialidad,$vector_tipo_entidad,$vector_entidad,$vector_procedimiento,$vector_cco,$vector_tarifas,$vector_participaciones,$vector_clasificaciones);

}
function activar_porcentaje($id)
{

	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	$q = "UPDATE ".$wbasedato."_000102 "
		."   SET Relest='on'"
		." 	 WHERE id ='".$id."' ";

	mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query (eliminar participacion 2paso): ".$q." - ".mysql_error());

	//traer_porcentajes($codmedico,$vector_conceptos, $vector_especialidad,$vector_tipo_entidad,$vector_entidad,$vector_procedimiento,$vector_cco,$vector_tarifas,$vector_participaciones,$vector_clasificaciones);

}

function elimina_porcentaje($relacion,$cod_participacion)
{
	global $wbasedato;
	global $conex;
	global $wemp_pmla;


		$q = "DELETE"
			."  FROM ".$wbasedato."_000102 "
			." WHERE id ='".$relacion."'";

		mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query (eliminar participacion 3paso): ".$q." - ".mysql_error());

}

function edita_porcentaje($relacion,$cod_participacion,$nuevo_porcentaje,$empresa,$tipo_empresa,$especialidad,$concepto,$procedimiento,$cco,$tarifa,$codmedico,$vector_conceptos, $vector_especialidad,$vector_tipo_entidad,$vector_entidad,$vector_procedimiento,$vector_cco,$vector_tarifas,$vector_clasificaciones,$clasificacion,$cod_cuadroturno,$vector_participaciones,$tercerounix,$participacion)
{
	global $wbasedato;
	global $conex;
	global $wemp_pmla;
	$wbasedatomedico = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

	$q = "UPDATE ".$wbasedato."_000102 "
		."   SET Relcon ='".$concepto."', "
		."		 Relpro ='".$procedimiento."' ,"
		."		 Relesp ='".$especialidad."' , "
		."		 Reltem ='".$tipo_empresa."' , "
		."		 Relemp='".$empresa."' , "
		." 		 Relcco='".$cco."' ,"
		."		 Reltar='".$tarifa."' ,"
		."		 Relcla='".$clasificacion."' ,"
		."       Relccu='".$cod_cuadroturno."' ,"
		."       Relpor='".$nuevo_porcentaje."' ,"
		."       Relpar='".$participacion."',"
		."		 Relcun='".$tercerounix."' "
		." 	 WHERE id ='".$relacion."' ";

	mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query (eliminar participacion 2paso): ".$q." - ".mysql_error());

	traer_porcentajes($codmedico,$vector_conceptos, $vector_especialidad,$vector_tipo_entidad,$vector_entidad,$vector_procedimiento,$vector_cco,$vector_tarifas,$vector_participaciones,$vector_clasificaciones);

}
function Obtener_array_participaciones ()
{
	global $conex;
	global $wemp_pmla;
	global $wbasedato;


	$q_par = " SELECT Clacod,Clades
			   FROM root_000099";

	$res_par = mysql_query($q_par,$conex) or die("Error en el query: ".$res_par."<br>Tipo Error:".mysql_error());

	$arr_par= array();

	while($row_par = mysql_fetch_array($res_par))
	{
		$arr_par[trim($row_par['Clacod'])] = trim($row_par['Clades']);
	}
	//$arr_par['*'] = 'Todos';
	return json_encode($arr_par);

}

//=======================================================================================================================================================
//		F I N	 F U N C I O N E S	 P H P
//=======================================================================================================================================================

//=======================================================================================================================================================
//		F I L T R O S  	 D E  	L L A M A D O S  	P O R  	J Q U E R Y  	O  	A J A X
//=======================================================================================================================================================
if(isset($accion))
{
	switch($accion)
	{
		case 'guardar_relacion':
		{

			$reultado = guardar_relacion($cod_medico,$cod_especialidad,$cod_concepto,$Codigoparticipacion,$codporcentaje,$operacion,$wmulti_tercero,$wcco,$wprocedimiento,$wtipoempresa,$wempresa,$wcentrocostos,$wtarifa,$wclasificacion,$clasificacion_cuadro_turnos,$codigotercero);
			break;
			return $resultado;
		}
		case 'ver_lista':
		{

			lista_de_participaciones($filtro='');

			break;
			return;
		}
		case 'elimina_porcentaje':
		{

			elimina_porcentaje($relacion,$cod_participacion);

			break;
			return;
		}
		case 'desactivar_porcentaje':
		{
			desactivar_porcentaje($id);
			return;
		}
		case 'activar_porcentaje':
		{
			activar_porcentaje($id);
			return;
		}
		case 'edita_porcentaje':
		{
			edita_porcentaje($relacion,$cod_participacion,$nuevo_porcentaje,$empresa,$tipo_empresa,$especialidad,$concepto,$procedimiento,$cco,$tarifa,$codmedico,$vector_conceptos, $vector_especialidad,$vector_tipo_entidad,$vector_entidad,$vector_procedimiento,$vector_cco,$vector_tarifas,$vector_clasificaciones,$clasificacion,$cod_cuadroturno,$vector_participaciones,$tercerounix,$participacion);

			break;
			return;
		}
		case 'crear_hidden_procedimientos':
		{
			$Array_proc	= Obtener_array_procedimientos_x_concepto($CodConcepto);
			echo json_encode($Array_proc);
			break;
			return;
		}
		case 'Obtener_clasificacion_procedimiento':
		{
			$Array_clas_pro= Obtener_clasificacion_procedimiento();
			echo json_encode($Array_clas_pro);
			break;
			return;
		}
		case 'ObtenerEspecialidades':
		{
			// --> Array con caracteres especiales para escaparlos en el nombre de las especialidades
			$caracter_ok = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
			$caracter_ma = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");

			// --> Consultar la base de datos de movimiento hospitalario correspondiente a la empresa
			$wbasedato_mov = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

			// --> Obtener las especialidades del medico
			$q_esp = "
			SELECT Espcod, Espnom
			  FROM ".$wbasedato_mov."_000044, ".$wbasedato_mov."_000048
			 WHERE Meddoc LIKE '".(($codTercero == '*') ? '%' : $codTercero)."'
			   AND SUBSTRING_INDEX(Medesp, '-', 1) = Espcod
			   AND Medest = 'on'
			 ";

			$res_esp = mysql_query($q_esp,$conex) or die ("Query (Consultar especialidades de un medico): ".$q_esp." - ".mysql_error());
			while($row_esp = mysql_fetch_array($res_esp))
			{
				$row_esp['Espnom'] = str_replace($caracter_ma, $caracter_ok, $row_esp['Espnom']);
				$arr_especialidades[trim($row_esp['Espcod'])] = $row_esp['Espnom'];
			}
			echo json_encode($arr_especialidades);
			break;
			return;

		}
		case 'ObtenerEntidades':
		{
			// --> Array con caracteres especiales para escaparlos en el nombre de las tarifas
			$caracter_ok = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
			$caracter_ma = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");

			$q_entidades = "SELECT Empcod, Empnom
							  FROM ".$wbasedato."_000024
							 WHERE Empest = 'on'
			".(($codTipoEmp != '*') ? " AND Emptem = '".$codTipoEmp."' " : " ")."
			".(($codTarifa  != '*') ? " AND Emptar = '".$codTarifa."' " : " ")."
						  ORDER BY Empnom ";
			$res_entidades = mysql_query($q_entidades,$conex) or die("Error en el query: ".$q_entidades."<br>Tipo Error:".mysql_error());
			$arr_entidades = array();
			while($row_entidades = mysql_fetch_array($res_entidades))
			{
				$row_entidades['Empnom'] = str_replace($caracter_ma, $caracter_ok, $row_entidades['Empnom']);
				$arr_entidades[trim($row_entidades['Empcod'])] = utf8_encode(trim($row_entidades['Empnom']));
			}

			echo json_encode($arr_entidades);
			break;
			return;
		}
		case 'traer_porcentajes':
		{
			traer_porcentajes($codmedico,$vector_conceptos, $vector_especialidad,$vector_tipo_entidad,$vector_entidad,$vector_procedimiento,$vector_cco,$vector_tarifas,$vector_participaciones,$vector_clasificaciones);
			break;
			return;
		}
		case 'lista_de_participaciones':
		{
			echo lista_de_participaciones($filtro);
			break;
			return;
		}

		case 'ObtenerTarifas':
		{
			$arr_tar = array();
			// --> Array con caracteres especiales para escaparlos en el nombre de las tarifas
			$caracter_ok = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
			$caracter_ma = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");

			// --> Obtener las tarifas segun el tipo de empresa
			$q_tar = "
					SELECT Tarcod, Tardes
					  FROM ".$wbasedato."_000024, ".$wbasedato."_000025
					 WHERE Empest = 'on'
					   ".(($codTipoEmp != '*') ? "AND Emptem = '".$codTipoEmp."'" : "")."
					   AND Emptar = Tarcod
					   AND Tarest = 'on'
			 ";

			$res_tar = mysql_query($q_tar,$conex) or die ("Query (Consultar tarifas segun el tipo de empresa): ".$q_tar." - ".mysql_error());
			while($row_tar = mysql_fetch_array($res_tar))
			{
				$row_tar['Tardes'] = str_replace($caracter_ma, $caracter_ok, $row_tar['Tardes']);
				$arr_tar[trim($row_tar['Tarcod'])] = $row_tar['Tardes'];
			}

			echo json_encode($arr_tar);
			break;
			return;
		}
		case 'guardar_tercero_defecto':
		{

			$q = "DELETE "
				."  FROM ".$wbasedato."_000218 "
				." WHERE Ctecon ='".$wconcepto."'";

			mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query (eliminar participacion 1paso): ".$q." - ".mysql_error());

			$q = "INSERT INTO ".$wbasedato."_000218
								(Medico, Fecha_data, Hora_data , Seguridad,Ctecon,Cteter,Cteest)
					VALUES    ( '".$wbasedato."' , '".$wfecha."', '".$wfecha."', 'C-".$wbasedato."' , '".$wconcepto."' , '".$wtercero."' , 'on')";
			mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query (Grabar grupo): ".$q." - ".mysql_error());
			break;
			return;
		}
		case 'guardar_nit_tercero' :
		{
			$wbasedato_mov = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

			echo $q = "UPDATE ".$wbasedato_mov."_000048
					 SET Mednho = '".$wnittercero."'
				   WHERE Meddoc = '".$wtercero."' "	;

			mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query (Grabar grupo): ".$q." - ".mysql_error());
			break;
			return;

		}
		case 'guardarGrupoMedico' :
		{
			$wbasedato_mov = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

			echo $q = "	UPDATE {$wbasedato_mov}_000048
					 			SET Medgru = '{$cod_grupo}'
				   		WHERE Meddoc = '{$wtercero}'";

			mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query (Grabar grupo del medico): ".$q." - ".mysql_error());
			break;
			return;

		}
		case 'eliminar_tercero_defecto' :
		{
			$q = "DELETE "
				."  FROM ".$wbasedato."_000218 "
				." WHERE Ctecon ='".$wconcepto."'";

			mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query (eliminar participacion 1paso): ".$q." - ".mysql_error());

			break;
			return;
		}

	}
}
//=======================================================================================================================================================
//		F I N   F I L T R O S   A J A X
//=======================================================================================================================================================


//=======================================================================================================================================================
//	I N I C I O   E J E C U C I O N   N O R M A L   D E L   P R O G R A M A
//=======================================================================================================================================================
else
{
	echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
	?>
	<html>
	<head>
	  <title>Porcentaje de Participación Terceros</title>
	</head>

	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
	<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
	<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
	<link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>
	<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
	<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
	<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
	<script type="text/javascript">
//=====================================================================================================================================================================
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T
//=====================================================================================================================================================================
//variable global que almacenara el json con la relacion de terceros
var arr_participacionterceros = "";
var operacion = "";
var arr_codigorelacion="";
var clone;
var var_medico='';

//--Se cargan los autocompletar principales (conceptos , tipos de empresa ,terceros y centros de costos)
$(document).ready(function()
{
	//Permite que al escribir en el campo buscar, se filtre la informacion del grid
	$('input#buscar_por_medico').quicksearch('#tabla_participaciones .find');



	$( "#buscar_por_medico" ).mousedown(function() {
		$(".ocultartodo").remove();
		$(".find").find('img').attr('src', '../../images/medical/hce/mas.PNG');
	});


	$( "#accordionporcentajes" ).show().accordion({
		collapsible: true,
		heightStyle: "content",
		active: 1
	});

	$( "#accordiontercerosxdefecto" ).show().accordion({
		collapsible: true,
		heightStyle: "content",
		active: 1

	});
	$( "#accordionniterceros" ).show().accordion({
		collapsible: true,
		heightStyle: "content",
		active: 1

	});


	//--> se carga autocompletar concepto
	crear_autocomplete(false, 'hidden_concepto', 'SI', 'busc_concepto', 'CargarProcedimientos', 'busc_procedimiento', true);
	//--> se carga autocompletar tipos de empresa
	crear_autocomplete(true,'hidden_tipos_empresa', 'SI', 'busc_tip_empresa', 'CargarTarifas');
	//--> se carga autocompletar centros de costos
	crear_autocomplete(true,'hidden_cco', 'SI', 'busc_cco', '');
	//--> se carga autocompletar clasificacion
	crear_autocomplete(true,'hidden_clasificacion', 'SI', 'busc_clasificacion', '');

	//----------------------------------------------------------------
	// --> Cargar autocomplete de terceros especialidad
	var arr_terceros  = eval('(' + $('#hidden_terceros_especialidad').val() + ')');
	var terceros      = new Array();
	var index		  = -1;

	for (var cod_ter in arr_terceros)
	{
		index++;
		terceros[index] = {};
		terceros[index].value  = cod_ter;
		terceros[index].label  = cod_ter+'-'+arr_terceros[cod_ter]['nombre'];
		terceros[index].especialidades  = arr_terceros[cod_ter]['especialidad'];
	}
	index++;
	terceros[index] = {};
	terceros[index].value  = "*";
	terceros[index].label  = "* Todos";
	terceros[index].especialidades  = '*';

	$( "#busc_terceros" ).autocomplete({
		minLength: 	0,
		source: 	terceros,
		select: 	function( event, ui ){
			cargarSelectEspecialidades( ui.item.especialidades );
			$( "#busc_terceros" ).attr('valor', ui.item.value);
			$( "#busc_terceros" ).val(ui.item.label);
			$("#codigotercerounix").val(ui.item.value);

			return false;
		}
	});

	$('input#id_search_medicos_por_codigo_nit').quicksearch('#tabla_medicos_nits .find',{
		onAfter: function () {
        	var cantidad_codigo_nits = $("#tabla_medicos_nits").find(".find:visible").length;
			if(cantidad_codigo_nits > 20)
			{
				$("#div_contenedor_nits").css({height: "550px", overflow: "scroll"});
			}
			else
			{
				$("#div_contenedor_nits").css({height: "auto", overflow: "auto"});
			}
		}
	});
	$("#div_contenedor_nits").css({height: "550px", overflow: "scroll"});
	//---------------------
});

//-----------------------------------------------------------------------
//	Funcion que crea los autocompletar
//-----------------------------------------------------------------------
function crear_autocomplete(AgregarOpcTodos, HiddenArray, TipoHidden, CampoCargar, AccionSelect, CampoProcedimiento, ActivarTodosProce)
{

	if(TipoHidden == 'SI')
		var ArrayValores  = eval('(' + $('#'+HiddenArray).val() + ')');
	else
		var ArrayValores  = eval('(' + HiddenArray + ')');

	if(AgregarOpcTodos)
		ArrayValores['*'] = 'Todos';

	var ArraySource   = new Array();
	var index		  = -1;
	for (var CodVal in ArrayValores)
	{
		index++;
		ArraySource[index] = {};
		ArraySource[index].value  = CodVal;
		ArraySource[index].label  = CodVal+'-'+ArrayValores[CodVal];
		ArraySource[index].nombre = ArrayValores[CodVal];
	}

	CampoCargar = CampoCargar.split('|');
	$.each( CampoCargar, function(key, value){
		$( "#"+value ).autocomplete({
			minLength: 	0,
			source: 	ArraySource,
			select: 	function( event, ui ){
				$( "#"+value ).val(ui.item.nombre);
				$( "#"+value ).attr('valor', ui.item.value);
				$( "#"+value ).attr('nombre', ui.item.nombre);
				switch(AccionSelect)
				{
					case 'CargarProcedimientos':
					{

						$("#busc_procedimiento").attr('valor' ,'');
						$("#busc_procedimiento").val('');
						$("#busc_procedimiento").attr('valor', '');
						$("#busc_procedimiento").attr('nombre','');
						crear_autocomplete_procedimientos(CampoProcedimiento, ui.item.value, true, ActivarTodosProce);
						return false;
						break;
					}
					case 'Cargarclasificacion':
					{

						$("#busc_clasificacion").attr('valor' ,'');
						$("#busc_clasificacion").val('');
						$("#busc_clasificacion").attr('valor', '');
						$("#busc_clasificacion").attr('nombre','');
						Obtener_clasificacion_procedimiento();
						return false;
						break;
					}
					case 'CargarProcedimientos_dos':
					{

						$("#buscador_procedimiento_editar").attr('valor' ,'');
						$("#buscador_procedimiento_editar").val('');
						$("#buscador_procedimiento_editar").attr('valor', '');
						$("#buscador_procedimiento_editar").attr('nombre','');
						crear_autocomplete_procedimientos(CampoProcedimiento, ui.item.value, true, ActivarTodosProce);
						return false;
						break;
					}
					case 'CargarEntidades':
					{
						$("#busc_entidad").attr('valor' ,'');
						$("#busc_entidad").val('');
						$("#busc_entidad").attr('valor', '');
						$("#busc_entidad").attr('nombre','');
						traerArrayEntidades(ui.item.value);
						return false;
					}
					case 'CargarEntidades_dos':
					{
						$("#buscador_empresa_editar").attr('valor' ,'');
						$("#buscador_empresa_editar").val('');
						$("#buscador_empresa_editar").attr('valor', '');
						$("#buscador_empresa_editar").attr('nombre','');
						traerArrayEntidades_dos(ui.item.value);
						return false;
					}
					case 'CargarTarifas':
					{
						$("#busc_tarifa").attr('valor' ,'');
						$("#busc_tarifa").val('');
						$("#busc_tarifa").attr('valor', '');
						$("#busc_tarifa").attr('nombre','');
						traerArrayTarifas(ui.item.value);
						return false;
					}
					case 'cargartarifasdesdetipodeempresa':
					{
						$("#buscador_tarifa_editar").attr('valor' ,'');
						$("#buscador_tarifa_editar").val('');
						$("#buscador_tarifa_editar").attr('valor', '');
						$("#buscador_tarifa_editar").attr('nombre','');
						traerArrayTarifas_dos(ui.item.value);
						return false;
					}

				}
				return false;
			}
		});

	});
}
//-----------------------------------------------------------------------
//	Funcion que carga las tarifas dado un tipo de empresa
//-----------------------------------------------------------------------
function traerArrayTarifas(tipoEmpresa)
{

	$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
	{
		consultaAjax:   		'',
		accion:         		'ObtenerTarifas',
		wemp_pmla:				$('#wemp_pmla').val(),
		codTipoEmp:				tipoEmpresa
	}
	,function(arrayTarifas){

		crear_autocomplete(true,arrayTarifas, 'NO', 'busc_tarifa', 'CargarEntidades');
	});
}

function traerArrayTarifas_dos(tipoEmpresa)
{
	$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
	{
		consultaAjax:   		'',
		accion:         		'ObtenerTarifas',
		wemp_pmla:				$('#wemp_pmla').val(),
		codTipoEmp:				tipoEmpresa
	}
	,function(arrayTarifas){

		crear_autocomplete(true,arrayTarifas, 'NO', 'buscador_tarifa_editar', 'CargarEntidades_dos');
	});
}
//-----------------------------------------------------------------------
//	Funcion que carga las entidades dado una tarifa
//-----------------------------------------------------------------------
function traerArrayEntidades(tarifa)
{
	$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
	{
		consultaAjax:   		'',
		accion:         		'ObtenerEntidades',
		wemp_pmla:				$('#wemp_pmla').val(),
		codTarifa:				tarifa,
		codTipoEmp:				$('#busc_tip_empresa').attr('valor')
	}
	,function(arrayEntidades){

		crear_autocomplete(true,arrayEntidades, 'NO', 'busc_entidad', '');
	});
}
//--------------------------
//-----------------------------------------------------------------------
//	Funcion que carga las entidades dado una tarifa
//-----------------------------------------------------------------------
function traerArrayEntidades_dos(tarifa)
{

	$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
	{
		consultaAjax:   		'',
		accion:         		'ObtenerEntidades',
		wemp_pmla:				$('#wemp_pmla').val(),
		codTarifa:				tarifa,
		codTipoEmp:				$('#buscador_tipoempresa_editar').attr('valor')
	}
	,function(arrayEntidades){

		crear_autocomplete(true,arrayEntidades, 'NO', 'buscador_empresa_editar', '');
	});
}

function FiltrarListaPorcentajes()
{

	$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
	{
		consultaAjax:   		'',
		accion:         		'lista_de_participaciones',
		wemp_pmla:				$('#wemp_pmla').val(),
		filtro:					$("#buscar_por_medico").val()


	}
	,function(data){

		$("#tdlista").html(data);
	});

}


function edita_terceroxdefecto(codigo_concepto,codter,nomter)
{
	if(codter == "nada")
	{
		codter="";
		nomter="";
		$("#img_tercero_por_"+codigo_concepto).html("<img  width='13' height='13' src='../../images/medical/root/grabar16.png' style='cursor : pointer ' id='img_tercero_por_"+codigo_concepto+"' onclick='guardar_tercero_pordefecto(\""+codigo_concepto+"\",\""+codter+"\" ,\""+nomter+"\")'>&nbsp;&nbsp;<img onclick='elimina_terceroxdefecto(\""+codigo_concepto+"\")'  style='cursor: pointer;' title='Eliminar'  src='../../images/medical/hce/cancel.PNG'>");
		$("#td_tercero_por_defecto_"+codigo_concepto).html("<input type='text' size='35' id='busc_tercero_editar_ter_"+codigo_concepto+"'>");
		$("#busc_tercero_editar_ter_"+codigo_concepto).attr('valor','');
		$("#busc_tercero_editar_ter_"+codigo_concepto).val('Seleccione');
		$("#busc_tercero_editar_ter_"+codigo_concepto).attr('nombre', '');
	}else
	{

		$("#img_tercero_por_"+codigo_concepto).html("<img  width='13' height='13' src='../../images/medical/root/grabar16.png' style='cursor : pointer ' id='img_tercero_por_"+codigo_concepto+"' onclick='guardar_tercero_pordefecto(\""+codigo_concepto+"\",\""+codter+"\" ,\""+nomter+"\")'>&nbsp;&nbsp;<img onclick='elimina_terceroxdefecto(\""+codigo_concepto+"\")'  style='cursor: pointer;' title='Eliminar'  src='../../images/medical/hce/cancel.PNG'>");
		$("#td_tercero_por_defecto_"+codigo_concepto).html("<input type='text' size='35' id='busc_tercero_editar_ter_"+codigo_concepto+"'>");
		$("#busc_tercero_editar_ter_"+codigo_concepto).attr('valor',codter);
		$("#busc_tercero_editar_ter_"+codigo_concepto).val(codter+"-"+nomter);
		$("#busc_tercero_editar_ter_"+codigo_concepto).attr('nombre', nomter);
	}


	crear_autocomplete(true,'hidden_terceros', 'SI', 'busc_tercero_editar_ter_'+codigo_concepto+'','');
}

function edita_nit_codigo(codter, nitter)
{
	$("#img_nit_codigo"+codter).html("<img  width='13' height='13' src='../../images/medical/root/grabar16.png' style='cursor : pointer '  onclick='guardar_nit_tercero(\""+codter+"\",\""+nitter+"\" )'>");
	$("#td_nit_codigo"+codter).html("<input id='input_tercero_nit"+codter+"' type='text' size='35' value='"+nitter+"'>");
	var selected = $("#div_select").html();

	var codagrupa = $("#td_grupo_codigo"+codter).attr("codgrupo");
	$("#td_grupo_codigo"+codter).html(selected);

	$("#td_grupo_codigo"+codter).find("select").val(codagrupa);
	$("#td_grupo_codigo"+codter).find("select").attr("onchange" , "guardarGrupoMedico("+codter+")");


}
// function guardarGrupoMedico(codagrupa,codter)
// {
	// alert(codter);
// }

function guardarGrupoMedico(codter)
{
	var cod_grupo = $("#td_grupo_codigo"+codter).find("select").val();
	//alert($("#td_grupo_codigo"+codter).find("select").val());


	$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
	{
		consultaAjax : '',
		accion       : 'guardarGrupoMedico',
		wemp_pmla    : $('#wemp_pmla').val(),
		wtercero     : codter,
		cod_grupo    : cod_grupo
	}
	,function(data){
			$("#td_grupo_codigo"+codter).attr("codgrupo" , cod_grupo);
	});
}


function guardar_nit_tercero(codter, nitter)
{
	$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'guardar_nit_tercero',
			wemp_pmla:				$('#wemp_pmla').val(),
			wtercero:				codter,
			wnittercero:			$("#input_tercero_nit"+codter).val()
		}
		,function(data){
			$("#img_nit_codigo"+codter).html("<img onclick='edita_nit_codigo(\""+codter+"\" , \""+$("#input_tercero_nit"+codter).val()+"\")'  style='cursor: pointer;' title='Editar'  src='../../images/medical/hce/mod.PNG'>");
			$("#td_nit_codigo"+codter).html($("#input_tercero_nit"+codter).val());

			if($("#td_grupo_codigo"+codter).find("select option:selected").val()=='')
			{
				$("#td_grupo_codigo"+codter).html("-");
			}
			else
			{
				$("#td_grupo_codigo"+codter).html($("#td_grupo_codigo"+codter).find("select option:selected").text());
			}
		//	alert($("#input_tercero_nit"+codter).val());
			//$("#td_grupo_codigo"+codter).html($("#td_grupo_codigo"+codter).find("select option:selected").text();)
				});
}


function elimina_terceroxdefecto(concepto)
{
	$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
	{
		consultaAjax:   		'',
		accion:         		'eliminar_tercero_defecto',
		wemp_pmla:				$('#wemp_pmla').val(),
		wconcepto:				concepto,
		wtercero:				$("#busc_tercero_editar_ter_"+concepto).attr("valor")
	}
	,function(data){

		$("#img_tercero_por_"+concepto).html("<img  width='16' height='16' src='../../images/medical/hce/mod.PNG' style='cursor : pointer ' id='img_tercero_por_"+concepto+"' onclick='edita_terceroxdefecto(\""+concepto+"\",\"nada\", \"nada\")'>&nbsp;&nbsp;<img  width='11' height='11' src='../../images/medical/hce/cancel.PNG' style='cursor : pointer ' id='img_tercero_por_"+concepto+"' onclick='elimina_terceroxdefecto(\""+concepto+"\")'>");
		$("#td_tercero_por_defecto_"+concepto).html("ningun tercero por defecto");

	});


}

function guardar_tercero_pordefecto (concepto,codigo ,nombre)
{


	if($("#busc_tercero_editar_ter_"+concepto).attr("valor") =="")
	{
		elimina_terceroxdefecto(concepto);
	}else
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'guardar_tercero_defecto',
			wemp_pmla:				$('#wemp_pmla').val(),
			wconcepto:				concepto,
			wtercero:				$("#busc_tercero_editar_ter_"+concepto).attr("valor")
		}
		,function(data){

			$("#img_tercero_por_"+concepto).html("<img  width='16' height='16' src='../../images/medical/hce/mod.PNG' style='cursor : pointer ' id='img_tercero_por_"+concepto+"' onclick='edita_terceroxdefecto(\""+concepto+"\",\""+$("#busc_tercero_editar_ter_"+concepto).attr("valor")+"\" , \""+$("#busc_tercero_editar_ter_"+concepto).attr("nombre")+"\")'>&nbsp;&nbsp;<img onclick='elimina_terceroxdefecto(\""+concepto+"\")'  style='cursor: pointer;' title='Eliminar'  src='../../images/medical/hce/cancel.PNG'>");
			$("#td_tercero_por_defecto_"+concepto).html($("#busc_tercero_editar_ter_"+concepto).attr("nombre"));

		});
	}
}
function traer_porcentajes(medico)
{

	if($("#detalle_"+medico).length==0)
	{


		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'traer_porcentajes',
			wemp_pmla:				$('#wemp_pmla').val(),
			codmedico:				medico,
			vector_conceptos:		$("#h_concepto").val(),
			vector_especialidad:	$("#h_especialidad").val(),
			vector_entidad:			$("#h_empresa").val(),
			vector_tipo_entidad: 	$("#h_tipo_entidad").val(),
			vector_procedimiento: 	$("#h_procedimiento").val(),
			vector_cco: 			$("#h_cco").val(),
			vector_tarifas:			$("#h_tarifas").val(),
			vector_participaciones: $("#h_participaciones").val(),
			vector_clasificaciones: $("#h_clasificaciones").val()

		}
		,function(data){
			$("#tr_"+medico).after(data);
			var clave=0;
			var ArrayErrores = eval('(' + $("#errores"+medico).val()+ ')');
			for (clave in 	ArrayErrores )
			{
				$("."+ArrayErrores[clave]).each(function(){
					//$(this).find("td").eq(0).html("<img src ='../../images/medical/sgc/Warning-32.png' title='Cambiar registro para que no se presenten ambiguedades'>");
				});

			}

		});
	}
	else
	{
		$(".ocultar_"+medico).remove();
	}

	if( $("#tr_"+medico).find('img').attr('src') == '../../images/medical/hce/mas.PNG' )
		$("#tr_"+medico).find('img').attr('src', '../../images/medical/hce/menos.PNG');
	else
		$("#tr_"+medico).find('img').attr('src', '../../images/medical/hce/mas.PNG');

}

function crear_autocomplete_procedimientos(Campo, CodConcepto, Show, ActivarTodosProce)
{
	$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
	{
		consultaAjax:   		'',
		accion:         		'crear_hidden_procedimientos',
		wemp_pmla:				$('#wemp_pmla').val(),
		CodConcepto:			CodConcepto
	}
	,function(respuesta){
		if(Show)
			$( "#"+Campo ).show(300);

		crear_autocomplete(ActivarTodosProce, respuesta, 'NO', Campo);
	});
}

function Obtener_clasificacion_procedimiento(Campo, CodConcepto, Show, ActivarTodosProce)
{
	$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
	{
		consultaAjax:   		'',
		accion:         		'crear_hidden_procedimientos',
		wemp_pmla:				$('#wemp_pmla').val(),
		CodConcepto:			CodConcepto
	}
	,function(respuesta){
		if(Show)
			$( "#"+Campo ).show(300);

		crear_autocomplete(ActivarTodosProce, respuesta, 'NO', Campo);
	});
}

function traerArrayEspecialidades(tercero)
{
	$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
	{
		consultaAjax:   		'',
		accion:         		'ObtenerEspecialidades',
		wemp_pmla:				$('#wemp_pmla').val(),
		codTercero:				tercero
	}
	,function(arrayEspecialidades){
		crear_autocomplete(true,arrayEspecialidades, 'NO', 'buscador_especialidad_editar');
	});
}




	function filtrarConBusqueda( valor, ver )
	{

	   valor = $.trim( valor );
	   valor = valor.toUpperCase();

	   $(".borrar").remove();

	   if( valor == "" ){
			   return;
	   }

		if (ver =='vermenos')
		{
			$("#tabla_participaciones tr").hide();
			var patt1 = new RegExp( valor , "g" );
			var entre ='no';// variable que va controlar si almenos se encuentra un resultado
			$('.medico').each(function(){
				   texto = $(this).find('td').text();
				   texto = $.trim(texto);
				   if ( patt1.test( texto ) )
				   {
					 $(this).show();
					 entre ='si';
				   }
			});

			if(entre=='no')
			{
				$("#tabla_participaciones").append("<tr class='borrar'><td align='center' width='800' colspan='5'><br><br><br><img src='../../images/medical/root/alerta.gif'  width='30' height='30'><br><b>No se encontraron datos</b></td></tr>");
				setTimeout(ver_listatodos,1000);
			}
		}

    }

	function ver_listatodos()
	{

		$("#tabla_participaciones tr").hide();
		$(".medico").show();
		$(".medico").each(function (){
			$(this).find('img').attr('src', '../../images/medical/hce/mas.PNG')
		});
		$("#buscador_lista").val('');

	}

	function traer_participacion( cod_concepto)
	{

		var terceros = $("#busc_terceros").attr('valor');
		var especialidad = $("#busc_especialidades").val();

		if(arr_participacionterceros[terceros] != undefined)
		{
			var participaciones = arr_participacionterceros[terceros][especialidad][cod_concepto];
			var clave=0;
			$("input[id^=participacion-]").val("");
			operacion='grabar'
			for (clave in participaciones){

				$("#participacion-"+clave).val(participaciones[clave]);
				operacion = 'editar';

			}
		}

	}
	//------------------------------------------------------------------------
	//	Nombre:			cargarSelectEspecialidades
	//	Descripcion:	Segun el tercero trae sus especialidades y las carga en el select especialidaes
	//	Entradas:		cadena: especialidades del tercero
	//	Salidas:
	//-----------------------------------------------------------------------
    function cargarSelectEspecialidades( cadena )
    {
		var especialidades = cadena.split(",");
		var html_options = "<option value='*'>* Todos</option>";
		for( var i in especialidades ){
			var especialidad = especialidades[i].split("-");
			html_options+="<option value='"+especialidad[0]+"'>"+especialidad[1]+"</option>";
		}
		$("#busc_especialidades").html( html_options );
		//$("#busc_especialidades").removeClass('campoRequerido');

    }
	//------------------------------------------------------------------------
	//	Nombre:			blanquear_campos
	//	Descripcion:	pone en blanco los campos principales
	//	Entradas:
	//	Salidas:
	//-----------------------------------------------------------------------
	function blanquear_campos()
	{
		$("#busc_concepto").val('');
		$("#busc_concepto").attr('valor','');
		$("[id^='participacion']").val('');
		$("[id^='participacion']").attr('valor','');
		$("#busc_terceros").attr('valor','');
		$("#busc_terceros").val('');
		$("#busc_especialidades").val('');
		$("#busc_procedimiento").attr('valor','');
		$("#busc_procedimiento").val('');
		$("#busc_tip_empresa").attr('valor','');
		$("#busc_tip_empresa").val('');
		$("#busc_entidad").attr('valor','');
		$("#busc_entidad").val('');
		$("#busc_cco").attr('valor','');
		$("#busc_cco").val('');

	}
	//------------------------------------------------------------------------
	//	Nombre:			Filtrar lista
	//	Descripcion:	Realiza la busqueda de terceros
	//	Entradas:
	//	Salidas:
	//-----------------------------------------------------------------------
	function filtrar_especialidad(ele)
	{
		ele = jQuery(ele);


	}
	//------------------------------------------------------------------------
	//	Nombre:			cancelar
	//	Descripcion:	Cancela el procesos de agregar una nueva relacion
	//	Entradas:
	//	Salidas:
	//-----------------------------------------------------------------------
	function cancelar()
	{
		$("#busc_concepto").val('');
		$("[id^='participacion']").val('');
		$("#busc_terceros").val('');
		$("#busc_especialidades").html("<option value=''>Seleccione Medico</option>");
		$("#checkbox_multi").attr("checked" ,false);
		$(".multitercero").hide();
		$("#busc_concepto").val('');
		$("#busc_concepto").attr('valor','');
		$("[id^='participacion']").val('');
		$("[id^='participacion']").attr('valor','');
		$("#busc_terceros").attr('valor','');
		$("#busc_terceros").val('');
		$("#busc_especialidades").val('');
		$("#busc_procedimiento").attr('valor','');
		$("#busc_procedimiento").val('');
		$("#busc_tip_empresa").attr('valor','');
		$("#busc_tip_empresa").val('');
		$("#busc_entidad").attr('valor','');
		$("#busc_entidad").val('');
		$("#busc_cco").attr('valor','');
		$("#busc_cco").val('');
		$("#busc_tarifa").attr('valor','');
		$("#busc_tarifa").val('');
		$("#busc_clasificacion").attr('valor','');
		$("#busc_clasificacion").val('');
		$("#codigotercerounix").val('');
		$("#codigoporcentaje").val('');
		$("#clasificacion_cuadro_turnos").val('');
		$("#Codigoparticipacion").val('');


	}
	//------------------------------------------------------------------------
	//	Nombre:			guardar_relacion
	//	Descripcion:	guarda la relacion tercero-especialidad-medico-participacion
	//	Entradas:
	//	Salidas:
	//-----------------------------------------------------------------------
	function guardar_relacion()
	{

		//------Se construye una cadena con las participaciones y sus valores
		var participaciones='';
		var i=0;
		var relacion='';
		var multi_tercero = 0;
		var cco;
		$("[id^='participacion']").each(function(){
			if($(this).val() != '')
			{
				var idparticipacion = $(this).attr("id").split('-');
				if( i>0 )
					participaciones+="||";

				participaciones += idparticipacion[1]+","+$(this).val();
				i++;
				// si  la operacion es igual a editar
				if (operacion=='editar')
				{

					relacion = arr_codigorelacion[$("#busc_terceros").attr("valor")][$("#busc_especialidades").val()][$("#busc_concepto").attr("valor")][$("#busc_clasificacion").attr("valor")];

					$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
					{
						consultaAjax:      '',
						wemp_pmla:         $('#wemp_pmla').val(),
						accion:            'edita_porcentaje',
						relacion:		   relacion,
						cod_participacion: idparticipacion[1],
						nuevo_porcentaje:  $(this).val(),


					},function () {

						var documento ='';
						FiltrarListaPorcentajes();
					});
				}
			}
		});
		if (operacion!='editar')
		{

			var validacion =true;
			$(".campoRequerido").each(function(){
				if($(this).val().length==0)
				{
					CampoObligatorio($(this).attr('id'));
					validacion=false;
				}
			})
			if(validacion)
			{

				$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
				{
					consultaAjax:     '',
					wemp_pmla:        $('#wemp_pmla').val(),
					accion:           'guardar_relacion',
					cod_medico:	 	  $("#busc_terceros").attr("valor"),
					cod_especialidad: $("#busc_especialidades").val(),
					cod_concepto:	  $("#busc_concepto").attr("valor"),
					codporcentaje: $("#codigoporcentaje").val(),
					Codigoparticipacion: $("#Codigoparticipacion").val(),
					operacion:		  operacion,
					wmulti_tercero:	  multi_tercero,
					wcco:			  cco,
					wprocedimiento:	  $("#busc_procedimiento").attr("valor"),
					wtipoempresa:	  $("#busc_tip_empresa").attr("valor"),
					wempresa:		  $("#busc_entidad").attr("valor"),
					wcentrocostos:	  $("#busc_cco").attr("valor"),
					wtarifa:		  $("#busc_tarifa").attr("valor"),
					wclasificacion:	  $("#busc_clasificacion").attr("valor"),
					clasificacion_cuadro_turnos: $("#clasificacion_cuadro_turnos").val(),
					codigotercero:   $("#codigotercerounix").val()

				},function(data) {
					if(data ==1)
					{
						var documento ='';
						FiltrarListaPorcentajes();
						mostrar_mensaje("Grabacion Exitosa");
						//blanquear_campos();
						var str = data;
						var patt = new RegExp("Duplicate");
						var res = patt.test(str);
					}
					else
					{
						var str = data;
						var patt = new RegExp("Duplicate");
						var res = patt.test(str);
						if(res)
							mostrar_mensaje("Existe un porcentaje con esta configuracion ya guardado");
						else
							mostrar_mensaje("Se ha presentado un erro al guardar");
					}
				});
			}
			else
			{
				mostrar_mensaje("Debe llenar los campos ");
			}

		}

	}
	function mostrar_mensaje(mensaje)
	{
		$("#div_mensajes").html("<img width='15' height='15' src='../../images/medical/root/info.png' />&nbsp;"+mensaje);
		$("#div_mensajes").css({"width":"250","opacity":" 0.6","fontSize":"11px"});
		$("#div_mensajes").hide();

		$("#div_mensajes").effect("pulsate", {}, 1500);
			setTimeout(function() {
			$("#div_mensajes").hide(400);
		}, 10000);
	}

	function CampoObligatorio(Elemento)
	{
		$("#"+Elemento).css("border","1px dotted #FF0000").attr('bordeObligatorio','si');
	}
	//------------------------------------------------------------------------
	//	Nombre:			cargalistaparticipaciones
	//	Descripcion:	Muestra la Lista de participaciones
	//	Entradas:		documento del medico
	//	Salidas:
	//-----------------------------------------------------------------------
	/*function cargalistaparticipaciones ( documento )
	{
		$("#div_tabla_participaciones").hide();
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
				{
					consultaAjax:     '',
					wemp_pmla:        $('#wemp_pmla').val(),
					accion:           'ver_lista',
					cod_medico:	 	  documento

				},function(data) {

					$("#tabla_participaciones").html(data);
					$("#div_tabla_participaciones").show();

					//Se consulta la variable arr_participacionterceros que viene en un input hidden y se convierte en un json
					arr_participacionterceros = $("#arr_participacionterceros").val();
					arr_participacionterceros = eval( '('+arr_participacionterceros+")" );
					arr_codigorelacion = $("#arr_codigorelacion").val();
					arr_codigorelacion = eval( '('+arr_codigorelacion+")" );

					if (documento=='')
					{
						$("#tabla_participaciones tr").hide();
						$(".medico").show();
						$(".medico").each(function (){
							$(this).find('img').attr('src', '../../images/medical/hce/mas.PNG')
						});
					}
					else
					{
						var ver ='vermenos';
						filtrarConBusqueda($("#busc_terceros").val(),ver);
					}
				});


	}*/
	//------------------------------------------------------------------------
	//	Nombre:			muestra_participaciones
	//	Descripcion:	En la lista de participaciones, cuando se cliquea en el + a un concepto se despliegan las participaciones
	//					asociadas a este
	//	Entradas:		ele: aqui viene el concepto , documento del medico
	//	Salidas:
	//-----------------------------------------------------------------------
	function muestra_participaciones(ele, cod_tercero)
	{

		var id;
		var ver_ocultar ='';
		ele = jQuery(ele);
		id = ele.attr('id');

		ver_ocultar = id.split('||');
		if( ele.attr('src') == '../../images/medical/hce/mas.PNG' )
		{
			ele.attr('src', '../../images/medical/hce/menos.PNG');

			$('.'+ver_ocultar[1]+'.'+cod_tercero).each(function(){
				if( $(this).attr("class").split(/\s+/).length == 3 ){
					$(this).show();
					$(this).find(".tag_img").attr('src','../../images/medical/hce/mas.PNG');
				}
			});
		}
		else
		{
			ele.attr('src', '../../images/medical/hce/mas.PNG');
			$('.'+ver_ocultar[1]+'.'+cod_tercero).hide();
		}


	}
	//------------------------------------------------------------------------
	//	Nombre:			muestra_conceptos
	//	Descripcion:	En la lista de participaciones, cuando se cliquea en el + a una especialidad de un tercero
	//					se despliegan los conceptos asociados a este
	//	Entradas:		ele: aqui viene la espcialidad
	//	Salidas:
	//-----------------------------------------------------------------------
	function muestra_conceptos (ele,cod_tercero)
	{
		var id;
		var ver_ocultar ='';
		ele = jQuery(ele);
		id = ele.attr('id');

		ver_ocultar = id.split('||');
		if( ele.attr('src') == '../../images/medical/hce/mas.PNG' )
		{
			ele.attr('src', '../../images/medical/hce/menos.PNG');

			$('.'+ver_ocultar[1]+'.'+cod_tercero).each(function(){
				if( $(this).attr("class").split(/\s+/).length == 2 ){
					$(this).show();
					$(this).find(".tag_img").attr('src','../../images/medical/hce/mas.PNG');
				}
			});
		}
		else
		{
			ele.attr('src', '../../images/medical/hce/mas.PNG');
			$('.'+ver_ocultar[1]).hide();
		}
	}

	//------------------------------------------------------------------------
	//	Nombre:			muestra_especialidad
	//	Descripcion:	En la lista de participaciones, cuando se cliquea en el + a un tercero
	//					se despliegan las especialidades asociadas a este
	//	Entradas:		ele: aqui viene el tercero
	//	Salidas:
	//-----------------------------------------------------------------------
	function muestra_especialidad(ele)
	{
		var id;
		var ver_ocultar ='';
		ele = jQuery(ele);
		id = ele.attr('id');
		ver_ocultar = id.split('||');

		if( ele.attr('src') == '../../images/medical/hce/mas.PNG' )
		{
			ele.attr('src', '../../images/medical/hce/menos.PNG');

			$('.'+ver_ocultar[1]).each(function(){
				if( $(this).attr("class").split(/\s+/).length == 1 )
				{
					$(this).show();
					// para cambiar el mas a menos en los hijos que abre
					$(this).find(".tag_img").attr('src','../../images/medical/hce/mas.PNG');
					$(this).find(".tag_img2").attr('src','../../images/medical/hce/mas.PNG');
				}
			});
		}
		else
		{
			ele.attr('src', '../../images/medical/hce/mas.PNG');
			$('.'+ver_ocultar[1]).hide();
		}
	}
	//------------------------------------------------------------------------
	//	Nombre:			elimina_porcentaje
	//	Descripcion:	elimina el registro de la tabla detalle (161) si no quedan mas detalles borra el registro en la
	//					tabla encabezado, y graficamente elimina el tr
	//	Entradas:		codigo de la relacion , codigo de la participacion, nombre del tr
	//	Salidas:
	//-----------------------------------------------------------------------
	function elimina_porcentaje(cod_relacion,cod_participacion,nombretr)
	{
		var siono = confirm("Desea Eliminar esta participación ?");
		if(siono)
		{
			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
					{
						consultaAjax:      '',
						wemp_pmla:         $('#wemp_pmla').val(),
						accion:            'elimina_porcentaje',
						relacion:		   cod_relacion,
						cod_participacion: cod_participacion

					},function (data) {

						$("#"+nombretr).remove();
					});
		}
	}
	function desactivar_porcentaje(id)
	{
		//alert(id);
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:      '',
			wemp_pmla:         $('#wemp_pmla').val(),
			accion:            'desactivar_porcentaje',
			id:					id

		},function (data) {

			$("#desactivar_activar_"+id).attr("src", "../../images/medical/sgc/no_conforme.png");
			$("#desactivar_activar_"+id).attr("onclick", "activar_porcentaje("+id+")");
			$("#desactivar_activar_"+id).attr("title", "Activar");
			$("#desactivar_activar_muestra"+id).attr("src", "../../images/medical/sgc/no_conforme.png");
			$("#desactivar_activar_muestra"+id).attr("title", "Inactivo");

		});
	}

	function activar_porcentaje(id)
	{
		//alert(id);
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:      '',
			wemp_pmla:         $('#wemp_pmla').val(),
			accion:            'activar_porcentaje',
			id:					id

		},function (data) {

				$("#desactivar_activar_"+id).attr("src", "../../images/medical/sgc/circuloVerde.png");
				$("#desactivar_activar_"+id).attr("onclick", "desactivar_porcentaje("+id+")");
				$("#desactivar_activar_"+id).attr("title", "Desactivar");
				$("#desactivar_activar_muestra"+id).attr("src", "../../images/medical/sgc/circuloVerde.png");
				$("#desactivar_activar_muestra"+id).attr("title", "Activo");
		});
	}
	//------------------------------------------------------------------------
	//	Nombre:			edita_porcentaje
	//	Descripcion:	edita el porcentaje de la tabla detalle (161)
	//	Entradas:		codigo de la relacion , codigo de la participacion, nombre del tr, porcentaje
	//	Salidas:
	//-----------------------------------------------------------------------

	function edita_porcentaje(cod_relacion,cod_participacion,nombretr, dato,concepto,nom_concepto,procedimiento,nom_procedimiento,especialidad,nom_especialidad,tipo_empresa, nom_tipo_empresa,empresa,nom_empresa,cco,nom_cco,tarifa,nom_tarifa,medico='',clasificacion,cod_clasificacion,cod_cuadroturno,tercerounix,nombre_participacion)
	{
		if(medico!='')
			var_medico = medico ;


		auxcod_participacion = cod_participacion;

		if(cod_participacion=='*')
		{
			cod_participacion='todos';
		}



		if($("#text-"+cod_relacion+"-"+cod_participacion).length==0)
		{

			$("#img_guardar").remove();
			$("#ant_concepto").val(concepto);
			$("#ant_procedimiento").val(procedimiento);
			$("#ant_especialidad").val(especialidad);
			$("#ant_tipo_empresa").val(tipo_empresa);
			$("#ant_empresa").val(empresa);


			var Arrayvector = eval('(' +  $("#h_cuadrosturnos").val()+ ')');
			var selectehtml="" ;
			for (clave in 	Arrayvector )
			{
				if (clave == cod_cuadroturno )
				{
					selectehtml = selectehtml +"<option value='"+clave+"' selected>"+Arrayvector[clave]+"</option>";
				}
				else
				{
					selectehtml = selectehtml +"<option value='"+clave+"' >"+Arrayvector[clave]+"</option>";
				}
			}

			var a = eval('(' +  $("#h_participaciones").val()+ ')');
			var selectparticipaciones ="";
			for( iclave in a ){
				if(iclave == auxcod_participacion )
				{
					selectparticipaciones = selectparticipaciones +"<option selected value='"+iclave+"'>"+iclave+"-"+a[iclave]+"</option>";
				}
				else
				{
					selectparticipaciones = selectparticipaciones +"<option  value='"+iclave+"'>"+iclave+"-"+a[iclave]+"</option>";

				}
			}



			if($("#buscador_concepto_editar").length==0)
			{
				clone= $("#"+nombretr).clone();
				$("#"+nombretr).addClass('activa');
				$("#imagenes_"+nombretr).append("<img id='img_guardar' width='13' height='13' src='../../images/medical/root/grabar16.png' style='cursor : pointer ' onclick='edita_porcentaje(\""+cod_relacion+"\",\""+cod_participacion+"\",\""+nombretr+"\",\""+dato+"\")'>");

			}
			else
			{

				$(".activa").after(clone);
				$("#img_guardar").remove();
				$("#imagenes_"+nombretr).append("<img id='img_guardar' width='13' height='13' src='../../images/medical/root/grabar16.png' style='cursor : pointer ' onclick='edita_porcentaje(\""+cod_relacion+"\",\""+cod_participacion+"\",\""+nombretr+"\",\""+dato+"\")'>");
				$(".activa").remove();
				clone= $("#"+nombretr).clone(true);
				$("#"+nombretr).addClass('activa');
			}
			$("#imagenes_"+nombretr+" img").eq(0).hide();
			$("#"+nombretr+" td").eq(12).html("<input id='text-"+cod_relacion+"-"+cod_participacion+"' type='text' style='text-align: center' size='3'  value='"+dato+"' >");
			$("#"+nombretr+" td").eq(1).html("<input id='buscador_especialidad_editar' size='15' type='text'  >");
			$("#buscador_especialidad_editar").attr('valor',especialidad);
			$("#buscador_especialidad_editar").val(especialidad+"-"+nom_especialidad);
			$("#buscador_especialidad_editar").attr('nombre', nom_especialidad);

			$("#"+nombretr+" td").eq(2).html("<input id='buscador_concepto_editar' size='25' type='text'  >");
			$("#"+nombretr+" td").eq(3).html("<input id='buscador_procedimiento_editar'  size='25' type='text'  >");
			$("#"+nombretr+" td").eq(4).html("<input id='buscador_clasificacion_editar'  size='20' type='text'  >");
			$("#"+nombretr+" td").eq(5).html("<input id='buscador_tipoempresa_editar'  size='20' type='text'  >");
			$("#"+nombretr+" td").eq(7).html("<input id='buscador_empresa_editar'  size='15' type='text'   >");
			$("#"+nombretr+" td").eq(6).html("<input id='buscador_tarifa_editar'  size='20' type='text'   >");
			$("#"+nombretr+" td").eq(8).html("<input id='buscador_cco_editar'  size='15' type='text'   >");


			$("#"+nombretr+" td").eq(9).html("<Select style='width : 100px'id='select_cuadrosturno'>"+selectehtml+"</Select>");
			$("#"+nombretr+" td").eq(10).html("<input id='tercero_unix_editar' size='20' type='text'>");
			$("#"+nombretr+" td").eq(11).html("<Select style='width : 100px' id='buscador_participacion_editar' >"+selectparticipaciones+"</select>");

			$("#buscador_tipoempresa_editar").attr('valor',tipo_empresa);
			$("#buscador_tipoempresa_editar").val(tipo_empresa+"-"+nom_tipo_empresa);
			$("#buscador_tipoempresa_editar").attr('nombre', nom_tipo_empresa);

			crear_autocomplete(true,'hidden_clasificacion', 'SI', 'buscador_clasificacion_editar', '');
			$("#buscador_clasificacion_editar").attr('valor',cod_clasificacion);
			$("#buscador_clasificacion_editar").val(cod_clasificacion+"-"+clasificacion);
			$("#buscador_clasificacion_editar").attr('nombre', clasificacion);



			traerArrayEspecialidades(medico);
			crear_autocomplete(false, 'hidden_concepto', 'SI', 'buscador_concepto_editar', 'CargarProcedimientos_dos', 'buscador_procedimiento_editar', true);
			crear_autocomplete_procedimientos('buscador_procedimiento_editar', concepto, true, true);
			crear_autocomplete(true,'hidden_tipos_empresa', 'SI', 'buscador_tipoempresa_editar', 'cargartarifasdesdetipodeempresa');
			traerArrayTarifas_dos(tipo_empresa);
			traerArrayEntidades_dos(tarifa);
			crear_autocomplete(true,'hidden_cco', 'SI', 'buscador_cco_editar', '');

			$("#buscador_concepto_editar").attr('valor',concepto);
			$("#buscador_concepto_editar").val(concepto+"-"+nom_concepto);
			$("#buscador_concepto_editar").attr('nombre', nom_concepto);


			$("#buscador_procedimiento_editar").attr('valor',procedimiento);
			$("#buscador_procedimiento_editar").val(procedimiento+"-"+nom_procedimiento);
			$("#buscador_procedimiento_editar").attr('nombre', nom_procedimiento);

			$("#buscador_cco_editar").attr('valor',cco);
			$("#buscador_cco_editar").val(cco+"-"+nom_cco);
			$("#buscador_cco_editar").attr('nombre', nom_cco);

			$("#buscador_tarifa_editar").attr('valor',tarifa);
			$("#buscador_tarifa_editar").val(tarifa+"-"+nom_tarifa);
			$("#buscador_tarifa_editar").attr('nombre', nom_tarifa);


			$("#buscador_empresa_editar").attr('valor',empresa);
			$("#buscador_empresa_editar").val(empresa+"-"+nom_empresa);
			$("#buscador_empresa_editar").attr('nombre', nom_empresa);
			$("#tercero_unix_editar").val(tercerounix);

			//crear_autocomplete(true,'hidden_clasificacion', 'SI', 'buscador_clasificacion_editar', '');
			// $("#buscador_participacion_editar").attr('valor',auxcod_participacion);
			// $("#buscador_participacion_editar").val(auxcod_participacion+"-"+nombre_participacion);
			// $("#buscador_participacion_editar").attr('nombre', nombre_participacion);






		}
		else
		{
				var siono = confirm("Desea Editar esta participación?");
				if(siono)
				{


						if($("#buscador_especialidad_editar").attr('valor')=='' || $("#buscador_especialidad_editar").val()=='')
						{
							alert('falta especialidad por llenar');
							return;
						}
						if($("#buscador_concepto_editar").attr('valor')=='' || $("#buscador_concepto_editar").val()=='')
						{
							alert('falta concepto por llenar');
							return;
						}
						if($("#buscador_procedimiento_editar").attr('valor')=='' || $("#buscador_procedimiento_editar").val()=='')
						{
							alert('falta procedimiento por llenar');
							return;
						}
						if($("#buscador_tipoempresa_editar").attr('valor')=='' || $("#buscador_tipoempresa_editar").val()=='')
						{
							alert('falta tipo de empresa por llenar');
							return;
						}

						if($("#buscador_tarifa_editar").attr('valor')=='' || $("#buscador_tarifa_editar").val()=='')
						{
							alert('falta tarifa por llenar');
							return;
						}

						if($("#buscador_empresa_editar").attr('valor')=='' || $("#buscador_empresa_editar").val()=='')
						{
							alert('falta tipo de empresa por llenar');
							return;
						}

						if($("#buscador_cco_editar").attr('valor')=='' || $("#buscador_cco_editar").val()=='')
						{
							alert('falta centro de costos por llenar');
							return;
						}

						if($("#buscador_clasificacion_editar").attr('valor')=='' || $("#buscador_clasificacion_editar").val()=='')
						{
							alert('falta clasificacion por llenar');
							return;
						}

						if($("#text-"+cod_relacion+"-"+cod_participacion).val()=='')
						{
							alert('falta porcentaje por llenar');
							return;
						}

						if($("#buscador_participacion_editar").attr('valor')=='' || $("#buscador_participacion_editar").val()==''  )
						{
							alert('falta participacion por llenar ');
							return;
						}


						$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
						{
							consultaAjax:      	'',
							wemp_pmla:         	$('#wemp_pmla').val(),
							accion:            	'edita_porcentaje',
							relacion:		   	cod_relacion,
							cod_participacion: 	auxcod_participacion,
							nuevo_porcentaje:  	$("#text-"+cod_relacion+"-"+cod_participacion).val(),
							empresa:			$("#buscador_empresa_editar").attr('valor'),
							tipo_empresa:		$("#buscador_tipoempresa_editar").attr('valor'),
							procedimiento:		$("#buscador_procedimiento_editar").attr('valor'),
							concepto:			$("#buscador_concepto_editar").attr('valor'),
							especialidad:		$("#buscador_especialidad_editar").attr('valor'),
							cco:				$("#buscador_cco_editar").attr('valor'),
							tarifa:				$("#buscador_tarifa_editar").attr('valor'),
							codmedico:				var_medico,
							vector_conceptos:		$("#h_concepto").val(),
							vector_especialidad:	$("#h_especialidad").val(),
							vector_entidad:			$("#h_empresa").val(),
							vector_tipo_entidad: 	$("#h_tipo_entidad").val(),
							vector_procedimiento: 	$("#h_procedimiento").val(),
							vector_cco: 			$("#h_cco").val(),
							vector_tarifas:			$("#h_tarifas").val(),
							vector_clasificaciones: $("#h_clasificaciones").val(),
							clasificacion:			$("#buscador_clasificacion_editar").attr('valor'),
							vec_cuadroturno:		Arrayvector,
							cod_cuadroturno:		$("#select_cuadrosturno").val(),
							vector_participaciones: $("#h_participaciones").val(),
							tercerounix:			$("#tercero_unix_editar").val(),
							participacion:			$("#buscador_participacion_editar").val()

						},function (data) {
							$(".ocultar_"+var_medico).remove();
							$("#tr_"+var_medico).after(data);


							var clave=0;
							var ArrayErrores = eval('(' + $("#errores"+var_medico).val()+ ')');
							for (clave in 	ArrayErrores )
							{
								$("."+ArrayErrores[clave]).each(function(){
									//$(this).find("td").eq(0).html("<img src ='../../images/medical/sgc/Warning-32.png' title='Cambiar registro para que no se presenten ambiguedades'>");
								});
							}

						});
				}
				else
					$("#"+nombretr+" td").eq(4).find('div:first').html($("#text-"+cod_relacion+"-"+cod_participacion).val()+"%");

		}

	}

	function activar_regex()
	{
		// --> Validar enteros
		$('.entero').keyup(function(){
			if ($(this).val() !="")
				$(this).val($(this).val().replace(/[^0-9]/g, ""));
		});
	}

	function activa_multitercero(elemento)
	{
		elemento = jQuery(elemento);
		if (elemento.is(':checked'))
			$(".multitercero").show();
		else
			$(".multitercero").hide();

	}

	$(function(){

		$('.requerido').on({
			focusout: function(e) {
				if($(this).val().replace(/ /gi, "") == '')
				{
					$(this).addClass("campoRequerido");
					$(this).attr("valor" ,"");
					$(this).attr("nombre" ,"");
				}
				else
				{
					$(this).removeClass("campoRequerido");
					$(this).css("border","").removeAttr('bordeObligatorio');
				}
			}
		});

	});





//=======================================================================================================================================================
//	F I N  F U N C I O N E S  J A V A S C R I P T
//=======================================================================================================================================================
	</script>




<!--=====================================================================================================================================================================
	E S T I L O S
=====================================================================================================================================================================-->
	<style type="text/css">
		.ui-autocomplete{
			max-width:         230px;
			max-height: 150px;
			overflow-y: auto;
			overflow-x: hidden;
			font-size:         9pt;
        }
		.Titulo_azul{
			color:#3399ff;
			font-weight: bold;
			font-family: verdana;
			font-size: 10pt;
		}
		.BordeGris{
			border: 1px solid #999999;
		}
		.BordeNaranja{
			border: 1px solid orange;
		}

		.pad{
            padding: 3px;
            }

		.campoRequerido{
			border: 1px outset #3399ff ;
			background-color:lightyellow;
			color:gray;
		}

		.Scrollvertical {
			height: 340px;
			overflow: auto;
			overflow-y: scroll;
		}


		 .ui-accordion .ui-accordion-content {width: 120%;}

		 .fila11
		{
			 background-color: #C3D9FF;
			 color: #000000;
			 font-size: 8pt;
		}
		.fila22
		{
			 background-color: #E8EEF7;
			 color: #000000;
			 font-size: 8pt;
		}
		.encabezadoTabla1
		{
			 background-color: #2A5DB0;
			 color: #FFFFFF;
			 font-size: 8pt;
			 font-weight: bold;
		}


	</style>
<!--=====================================================================================================================================================================
	F I N   E S T I L O S
=====================================================================================================================================================================-->



<!--=====================================================================================================================================================================
	I N I C I O   B O D Y
=====================================================================================================================================================================-->



	<BODY>
	<div id="actualiza" class="version" style="text-align:right;" >Subversi&oacute;n: <?=$wactualiza?></div>
	<?php
	$html ='';
	// -->	ENCABEZADO
	//encabezado("Porcentaje de Participación Terceros", $wactualiza, 'clinica');
	// --> Hidden de array de terceros especialidad
	$html.= "<input type='hidden' id='hidden_terceros_especialidad' name='hidden_terceros_especialidad' value='".json_encode(obtener_array_terceros_especialidad())."'>";
	// --> Hidden de array de conceptos
	$html.= "<input type='hidden' id='hidden_concepto' name='hidden_concepto' value='".json_encode(obtener_array_conceptos())."'>";
	// --> Hidden de array de terceros especialidad
	$html.= "<input type='hidden' id='hidden_terceros' name='hidden_terceros' value='".json_encode(obtener_array_terceros())."'>";
	//--> Hidden para entidades
	$html.= "<input type='hidden' id='hidden_entidades' value='".json_encode(obtener_array_entidades())."'>";
	//--> Hidden para tipos de empresa
	$html.= "<input type='hidden' id='hidden_tipos_empresa' value='".json_encode(Obtener_array_tipos_empresa())."'>";
	// --> Centros de costos
	$html.= "<input type='hidden' id='hidden_cco' value='".json_encode(Obtener_array_cco())."'>";
	// --> Hidden de array de clasificacion procedimientos
	$html.= "<input type='hidden' id='hidden_clasificacion' value='".json_encode(Obtener_clasificacion_procedimiento())."'>";
	// --> Hidden de array de conceptos

	$html.= "<input type='hidden' id='h_concepto'  value='".json_encode(obtener_array_conceptos())."'>";
	$html.= "<input type='hidden' id='h_especialidad'  value='".json_encode(obtener_array_especialidades())."'>";
	$html.= "<input type='hidden' id='h_procedimiento'  value='".json_encode(obtener_array_procedimientos($conex, $wemp_pmla, $wbasedato))."'>";
	$html.= "<input type='hidden' id='h_tipo_entidad' n value='".json_encode(Obtener_array_tipos_empresa())."'>";
	$html.= "<input type='hidden' id='h_empresa'  value='".json_encode(Obtener_array_entidades())."'>";
	$html.= "<input type='hidden' id='h_empresa'  value='".json_encode(Obtener_array_entidades())."'>";
	$html.= "<input type='hidden' id='h_cco' value='".json_encode(Obtener_array_cco())."'>";
	$html.= "<input type='hidden' id='h_tarifas' value='".json_encode(Obtener_array_tarifas())."'>";
	$html.= "<input type='hidden' id='h_participaciones' value='".(Obtener_array_participaciones())."'>";
	$html.= "<input type='hidden' id='h_clasificaciones' value='".json_encode(Obtener_clasificacion_procedimiento())."'>";

	// $wbasedatomedico = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	// $q =	"SELECT  Ccucod,Ccunom FROM ".$wbasedatomedico."_000160  ";
	// $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query (mostrar lista ): ".$q." - ".mysql_error());
	// $vector_clasificacion_cuadros = array();
	// while($row = mysql_fetch_array($res))
	// {
		// $vector_clasificacion_cuadros[$row['Ccucod']] = $row['Ccunom'];
	// }
	// $html.= "<input type='hidden' id='h_cuadrosturnos' value='".json_encode($vector_clasificacion_cuadros)."'>";
	$wbasedatomedico = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$q =	"SELECT  Gmecod,Gmenom FROM ".$wbasedatomedico."_000166 ";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query (mostrar lista ): ".$q." - ".mysql_error());
	$vector_clasificacion_cuadros = array();
	while($row = mysql_fetch_array($res))
	{
		$vector_clasificacion_cuadros[$row['Gmecod']] = $row['Gmenom'];
	}
	$html.= "<input type='hidden' id='h_cuadrosturnos' value='".json_encode($vector_clasificacion_cuadros)."'>";


	// valores anteriores para ver si se hace un update
	$html.= "<input type='hidden' id='ant_empresa' >";
	$html.= "<input type='hidden' id='ant_tipo_empresa' >";
	$html.= "<input type='hidden' id='ant_concepto' >";
	$html.= "<input type='hidden' id='ant_cco' >";
	$html.= "<input type='hidden' id='ant_procedimiento' >";
	$html.= "<input type='hidden' id='ant_especialidad' >";


	$html.= "<table ><tr><td id='prueba'></td></tr></table>";


	$html.="<div width='95%' id='accordionporcentajes'  style='display:none'>
			<h3>Porcentajes de Participacion</h3>
			<div>";
	$html.="<br>";
	$html.="<br>";

	// $html.= "<div>
				// <fieldset align='center' width='900'>
				// <legend align='left'>Agregar nuevo porcentaje</legend>
				// <table width='900'>
				// <tr>
					// <td>
						// ".listabusquedaparticipaciones()."
					// </td>
				// </tr>
				// </table>
				// </fieldset>
			  // </div>";

	$html.= "<div>
				<fieldset align='center' width='900'>
				<legend align='left'>Agregar nuevo porcentaje</legend>
				<table width='900'>
				<tr>
					<td>
						".nueva_participacion_terceros()."
					</td>
				</tr>
				</table>
				</fieldset>
			  </div><br><br><br>";


	$html.= "<div>
			<fieldset align='center' width='1200'>
			<legend align='left'>Listado de Porcentajes de participacion</legend>
			<table width='100%'>
				<tr>
					<td>
						".buscador()."
					</td>
				</tr>
				<tr>
					<td id = 'tdlista'>
						<div id = 'tdlista' class='Scrollvertical'>
							".lista_de_participaciones()."
						</div>
					</td>
				</tr>
		  </table>
		  </fieldset>
		  </div>";

	$html .="</div></div>";

	$html.="<div width='95%' id='accordiontercerosxdefecto'  style='display:none'>
			<h3>Terceros por concepto por defecto</h3>
			<div><br><br>";

	$html.= tercerosxdefecto();

	$html .="<br>";
	$html .="</div></div>";

	$total_listado = 0;
	$html_nits_ters = nits_terceros($total_listado);

	$html.='<div width="95%"  id="accordionniterceros"  style="display:none">
			<h3>Nits de Medicos</h3>
			<div><br><br>
			<table width="90%" align="left">
				<tr class="encabezadoTabla">
					<td align="left">
						Filtrar listado: <input id="id_search_medicos_por_codigo_nit" type="text" value="" name="id_search_medicos_por_codigo_nit">
					</td>
				</tr>
				<tr>
					<td   width="80%" align="left">
						<div  width="80%" align="left" id="div_contenedor_nits" >
						'.$html_nits_ters.'
						</div>
					</td>
				</tr>
			</table>
			<br>
			</div></div>';
	echo $html;

	?>
	</BODY>
<!--=====================================================================================================================================================================
	F I N   B O D Y
=====================================================================================================================================================================-->
	</HTML>
	<?php
//=======================================================================================================================================================
//	F I N  E J E C U C I O N   N O R M A L
//=======================================================================================================================================================
}

}//Fin de session
?>
