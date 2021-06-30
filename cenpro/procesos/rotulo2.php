
<?php
include_once("conex.php");

/*****************************************************************************************************************************************************************************
 * Requerimiento 1710-2186 
 * Por SILVIA TORO
 * Favor colocar en el rotulo de nutriciòn parenteral en concentración de lipidos (> 1%); y en Concentracion de Proteinas (> 2.5%) y 
 * ademas que salga informaciòn de SEGURA O INSEGURA dependiendo si el valor esta dentro de la especificacion o por fuera de la especificaciòn
 * 
 * Modificaciones:
 *  
 * Enero 30 de 2020 Jessica		- Se agrega al rotulo los insumos adicionales utilizados para la nutrición (Bolsa y equipos) y si se 
 *								  recibe el parámetro $consulta='on' lo campos input quedan de solo lectura y se inhabilitan los select
 * Septiembre 19 de 2019 Jessica- Se agrega filtro de tipo de documento al hacer join entre root_000036 y root_000037 para evitar obtener información incorrecta.
 *			 					- Se agrega el código de barras con el código de la NPT.
 * Agosto 15 de 2019 Jessica	- Se comenta la validación que tenía antes de insertar la información del paciente y se modifica para 
 * 								  que siempre que hagan clic en SIGUIENTE guarde los datos en cenpro_000020.
 * Julio 23 de 2019 Edwin		- En el ajax que se envía al dar clic en siguiente se envía el parametro agua y la variable $agua se deja como global
 *								  en la función pintarRotulo
 * Marzo 6 de 2018 Jessica		- Se corrige en el botón siguiente y atrás para que envíe el parámetro refrescar en true y así 
 *								conserve los datos para imprimir el rotulo pequeño.  
 * Marzo 5 de 2018 Jessica		- Se realizan correcciones para que traiga el tiempo de infusión y no quede por defecto 24 horas, 
 *								y al modificarlo se actualicen los calculos
 *								- Si no tiene lote creado permite agregar el número de lote, fecha y hora de preparación, quien revisa y prepara el lote
 * Marzo 1 de 2018 Jessica		Permite imprimir el rotulo sin tener el lote creado
 * Febrero 28 de 2018 Jessica	Se quita el submit que hace al cambiar el tiempo de infusion ya que no permitía imprimir
 * Febrero 19 de 2018 Jessica	Al modificar la estructura del script para que pinte los rótulos por ajax, para las nutriciones 
 *								externas en el rotulo pequeño (sticker) no se estaban mostrando algunos datos (input), se agregan 
 *								como parámetros para enviarlos por ajax y solucionarlo.
 * Febrero 15 de 2018 Jessica	Se agregan algunos campos y se modifica la estructura del script para que permita pintar por ajax e 
 *								imprimir el rotulo de acuerdo al lote.
 * Julio 17 de 2015 (Jonatan): Se agrega estilo negrita a los textos velocidad de infusion y volumen total y a las cantidades.
 * Marzo 29 de 2011	(Edwin MG)	SE agrega inromación de seguro o inseguro para la concentración de lipidos y en concentracion de proteínas (Requerimiento 2184  2186).
 *****************************************************************************************************************************************************************************/


//2009-04-24: Se creo una nueva clase para aumentar el tamaño del nombre y de la cama, la clase es la (texto5)

function centroCostosCM()
	{
		global $conex;
		global $wbasedatoMovhos;
		
		$sql = "SELECT
					Ccocod
				FROM
					".$wbasedatoMovhos."_000011
				WHERE
					ccofac LIKE 'on' 
					AND ccotra LIKE 'on' 
					AND ccoima !='off' 
					AND ccodom !='on'
				";
		
		$res= mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		if( $rows = mysql_fetch_array( $res ) ){
			return $rows[ 'Ccocod' ];
		}
	}


////////////////////////////////////////////////////PROGRAMA/////////////////////////////////////////////////////////

if (!isset($user))
{
	if(!isset($_SESSION['user']))
	session_register("user");
}

if(!isset($_SESSION['user']))
{
	echo "error";
}
else
{
	//$wemp_pmla='01';
	//$wbasedato='cenpro';
	
//	include_once( "root/comun.php" );
//    $conex = obtenerConexionBD("matrix");
//	$conex = mysql_connect('localhost','root','')
//	or die("No se ralizo Conexion");

    include_once( "conex.php" );
	include_once("root/comun.php");
	

	$wbasedatoMovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$wbasedato = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cenmez" );
	$wcostosyp = consultarAliasPorAplicacion( $conex, $wemp_pmla, "COSTOS" );
	//consulto los datos del usuario de la sesion
	$pos = strpos($user,"-");
	$wusuario = substr($user,$pos+1,strlen($user)); //extraigo el codigo del usuario

	//$test = centroCostosCM();echo $test;
/**
 * A cada mes le encuentra su numero
 *
 * @param unknown_type $mes
 * @return unknown
 */
function numero_mes($mes){
	switch ($mes){
		case '01':
		$numero_mes='Enero';
		break;
		case "02":
		$numero_mes='Febrero';
		break;
		case "03":
		$numero_mes='Marzo';
		break;
		case "04":
		$numero_mes='Abril';
		break;
		case "05":
		$numero_mes='Mayo';
		break;
		case "06":
		$numero_mes='Junio';
		break;
		case "07":
		$numero_mes='Julio';
		break;
		case "08":
		$numero_mes='Agosto';
		break;
		case "09":
		$numero_mes='Septiembre';
		break;
		case "10":
		$numero_mes='Octubre';
		break;
		case "11":
		$numero_mes='Noviembre';
		break;
		case "12":
		$numero_mes='Diciembre';
		break;
	}

	return $numero_mes;
}



function pintarUsuarios($nombreCampo,$codigoUsuario,$consulta)
{
	global $conex;
	$cco = centroCostosCM();
	$queryUsuarios = "SELECT Codigo,Descripcion 
						FROM usuarios 
					   WHERE Ccostos=".$cco." 
					ORDER BY Descripcion;";
					
	$resUsuarios = mysql_query($queryUsuarios,$conex);
	$numUsuarios = mysql_num_rows($resUsuarios);
	
	$habilitarSelect = "";
	if($consulta=="on")
	{
		$habilitarSelect = "disabled='disabled'";
	}
	
	$htmlSelectUsuarios = "";
	if($numUsuarios > 0)
	{
		$htmlSelectUsuarios .= "<select id='".$nombreCampo."' ".$habilitarSelect." onchange='seleccionarLote(0,true)'>
									<option value='0'>Seleccione...</option>";
		while($rowUsuarios = mysql_fetch_array($resUsuarios))
		{
			$usuarioSeleccionado = "";
			if($rowUsuarios['Codigo']==$codigoUsuario)
			{
				$usuarioSeleccionado = "selected";
			}
			$htmlSelectUsuarios .= "<option value='".$rowUsuarios['Codigo']."' ".$usuarioSeleccionado.">".$rowUsuarios['Descripcion']."</option>";
		}
		$htmlSelectUsuarios .= "</select>";
	}
	
	return $htmlSelectUsuarios;
}

function pintarLotes($wcenmez,$codigoNPT)
{
	global $conex;
	
	$queryLotes = "SELECT Plocod 
					 FROM ".$wcenmez."_000004 
					WHERE Plopro='".$codigoNPT."'
				 ORDER BY Plocod;";
					
	$resLotes = mysql_query($queryLotes,$conex);
	$numLotes = mysql_num_rows($resLotes);
	
	$htmlLotes = "";
	if($numLotes > 0)
	{
		$htmlLotes .= "<div id='divLotes' style='position:relative;'>
							<table align='center'>
								<tr class='encabezadoTabla'>
									<td colspan='2'>Seleccione el lote del r&oacute;tulo a imprimir</td>
								</tr>
								<tr>
									<td class='fila1'>Lote:</td>
									<td class='fila2'>
										<select id='loteRotulo' onchange='seleccionarLote(0,false);'>";
											while($rowLotes = mysql_fetch_array($resLotes))
											{
		$htmlLotes .= "							<option value='".$rowLotes['Plocod']."'>".$rowLotes['Plocod']."</option>";	
											}
		$htmlLotes .= "					</select>
									</td>
								</tr>
							</table>
							<br>
						</div>";
	}
	else
	{
		$htmlLotes = "<p align='center' id='mensajeLote'><b>Debe crear un lote para poder imprimir el r&oacute;tulo.</b></p>";
	}

	echo $htmlLotes;
}

function consultarDatosLote($wcenmez,$codigoNPT,$lote)
{
	global $conex;
	
	$queryLote = "SELECT b.Fecha_data AS Fecha,b.Hora_data AS Hora,SUBSTR(a.Seguridad, 3) AS usuarioCrea,b.Ploela AS usuarioElabora,b.Plorev AS usuarioRevisa, c.Descripcion AS nombreCrea,d.Descripcion AS nombreElabora,e.Descripcion AS nombreRevisa
					FROM ".$wcenmez."_000002 a
					LEFT JOIN usuarios c 
					ON c.Codigo=SUBSTR(a.Seguridad, 3)
					LEFT JOIN ".$wcenmez."_000004 b
					ON Plopro=Artcod
					AND Plocod='".$lote."'
					LEFT JOIN usuarios d 
					ON d.Codigo=Ploela
					LEFT JOIN usuarios e 
					ON e.Codigo=Plorev
					WHERE Artcod='".$codigoNPT."';";
	
	$resLote =  mysql_query($queryLote,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$queryLote." - ".mysql_error());
	$numLote = mysql_num_rows($resLote);	
	
	
	$arrayLote = array();
	if($numLote > 0)
	{
		$rowLote = mysql_fetch_array($resLote);
		
		$arrayLote['fecha'] = $rowLote['Fecha'];
		$arrayLote['hora'] = $rowLote['Hora'];
		$arrayLote['usuarioCrea'] = $rowLote['usuarioCrea'];
		$arrayLote['usuarioElabora'] = $rowLote['usuarioElabora'];
		$arrayLote['usuarioRevisa'] = $rowLote['usuarioRevisa'];
		$arrayLote['nombreCrea'] = $rowLote['nombreCrea'];
		$arrayLote['nombreElabora'] = $rowLote['nombreElabora'];
		$arrayLote['nombreRevisa'] = $rowLote['nombreRevisa'];
	}
	
	return $arrayLote;
}

function consultarIngreso($wbasedatoMovhos,$historia,$codigoNPT)
{
	global $conex;
	
	$q= " SELECT Enuing
			FROM ".$wbasedatoMovhos."_000214 
		   WHERE Enuhis='".$historia."' 
			  AND Enucnu='".$codigoNPT."' 
			 AND Enuest='on' 
			 AND Enurea='on';";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);
	
	$ingreso = "";
	if($num > 0)
	{
		$row = mysql_fetch_array($res);
		$ingreso =  $row['Enuing'];
	}
	
	return $ingreso;
}

function consultarEncabezadoNPT($wbasedato,$historia,$ingreso,$codigoNPT)
{
	global $conex;
	
	$q= " SELECT Enuhis,Enuing,Enuart,Enuido,Enucnu,Enupes,Enutin,Enupur,Enuvol,Enuord,SUBSTR(Seguridad, 3) AS Usuario,Descripcion   
			FROM ".$wbasedato."_000214,usuarios  
		   WHERE Enuhis='".$historia."' 
			 AND Enuing='".$ingreso."' 
			 AND Enucnu='".$codigoNPT."' 
			 AND Enuest='on' 
			 AND Enurea='on'
			 AND Codigo=SUBSTR(Seguridad, 3);";

	$res = mysql_query($q,$conex);
	$num=mysql_num_rows($res);
	
	$arrayEncabezadoNPT = array();
	if($num > 0)
	{
		
		$row = mysql_fetch_array($res);
		
		$arrayEncabezadoNPT['historia'] = $row['Enuhis'];
		$arrayEncabezadoNPT['ingreso'] = $row['Enuing'];
		$arrayEncabezadoNPT['articulo'] = $row['Enuart'];
		$arrayEncabezadoNPT['ido'] = $row['Enuido'];
		$arrayEncabezadoNPT['codigoNPT'] = $row['Enucnu'];
		$arrayEncabezadoNPT['peso'] = $row['Enupes'];
		$arrayEncabezadoNPT['tiempoInfusion'] = $row['Enutin'];
		$arrayEncabezadoNPT['purga'] = $row['Enupur'];
		$arrayEncabezadoNPT['volumen'] = $row['Enuvol'];
		
		$codigoNutricionista = "";
		$nombreNutricionista = "";
		if($row['Enuord']=="on")
		{
			$codigoNutricionista = $row['Usuario'];
			$nombreNutricionista = $row['Descripcion'];
		}
		$arrayEncabezadoNPT['codUsuario'] = $codigoNutricionista;
		$arrayEncabezadoNPT['nombreUsuario'] = $nombreNutricionista;
		
	}
		
	return $arrayEncabezadoNPT;
	
}
function consultarMedicosTratantes($wbasedatoMovhos,$historia,$ingreso,$fecha)
{
	global $conex;
	
	$queryMedicoTratante = "SELECT Medtdo,Meddoc,CONCAT_WS(' ',Medno1, Medno2, Medap1, Medap2) AS MedicosTratantes
							  FROM ".$wbasedatoMovhos."_000047, ".$wbasedatoMovhos."_000048
							 WHERE Methis = '".$historia."'
							   AND Meting = '".$ingreso."'
							   AND Metest = 'on'
							   AND Metfek = '".$fecha."'
							   AND Mettdo = Medtdo
							   AND Metdoc = Meddoc
						  GROUP BY Meddoc;";
	
	$resMedicoTratante =  mysql_query($queryMedicoTratante,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$queryMedicoTratante." - ".mysql_error());
	$numMedicoTratante = mysql_num_rows($resMedicoTratante);	
	
	
	$medicosTratantes = "";
	
	if($numMedicoTratante > 0)
	{
		while($rowMedicoTratante = mysql_fetch_array($resMedicoTratante))
		{
			// $arrayMedicoTratante[$rowMedicoTratante['Medtdo']."-".$rowMedicoTratante['Meddoc']] = $rowMedicoTratante['MedicosTratantes'];
			$medicosTratantes = $rowMedicoTratante['MedicosTratantes'];
			// $medicosTratantes .= $rowMedicoTratante['MedicosTratantes'].",";
		}
		
		// $medicosTratantes = substr($medicosTratantes, 0, -1);
	}
	
	return $medicosTratantes;
}

function consultarInsumosAdicionales($wcenmez,$wbasedato,$wemp_pmla,$codigoNPT)
{
	global $conex;
	
	// $codigoAguaNPT = consultarAliasPorAplicacion( $conex, $wemp_pmla, "codigoAguaNPT" );
	$codigoBolsasNPT = consultarAliasPorAplicacion( $conex, $wemp_pmla, "codigoBolsasNPT" );
	$codigoEquiposNPT = consultarAliasPorAplicacion( $conex, $wemp_pmla, "codigoEquiposNPT" );
	$codigoEquiposNPT = consultarAliasPorAplicacion( $conex, $wemp_pmla, "codigoEquiposNPTneonatos" );
	
	
	$insumos = $codigoBolsasNPT.",".$codigoEquiposNPT.",".$codigoEquiposNPT;
	$arrayInsumos = explode(",",$insumos);
	$arrayInsumos = array_unique($arrayInsumos);
	
	$listaInsumos = implode(",",$arrayInsumos);
	$listaInsumos = str_replace(",","','",$listaInsumos);
	
	$queryInsumos = " SELECT Pdeins, Pdecan, Artgen, Artcom, Artuni, Unides 
						FROM ".$wcenmez."_000003, ".$wcenmez."_000002, ".$wbasedato."_000027
					   WHERE Pdepro='".$codigoNPT."' 
						 AND Pdeest='on'
						 AND Pdeins IN ('".$listaInsumos."')
						 AND Artcod=Pdeins
						 AND Unicod=Artuni;";
	
	$resInsumos = mysql_query($queryInsumos, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryInsumos . " - " . mysql_error());
	$numInsumos = mysql_num_rows($resInsumos);
	
	$arrayInsumos = array();
	if($numInsumos>0)
	{
		while($rowInsumos = mysql_fetch_array($resInsumos))
		{
			$arrayInsumos[$rowInsumos['Pdeins']]['cantidad'] = $rowInsumos['Pdecan'];
			$arrayInsumos[$rowInsumos['Pdeins']]['generico'] = $rowInsumos['Artgen'];
			$arrayInsumos[$rowInsumos['Pdeins']]['comercial'] = $rowInsumos['Artcom'];
			$arrayInsumos[$rowInsumos['Pdeins']]['unidad'] = $rowInsumos['Unides'];
		}
	}
	
	return $arrayInsumos;
}
	
function pintarRotulo($wemp_pmla,$wbasedato,$historia="",$codigo,$lote,$ph,$horas,$medico,$nutricionista,$nombre="",$servicio="",$cama="",$edad="",$institucion="",$quimico="",$imprimir,$usuarioElabora="",$usuarioRevisa="",$elaborado="",$revisado="",$fecha="",$hora="",$refrescar,$insti,$consulta)
{
	global $conex;
	global $wusuario;
	global $agua;
	global $wbasedatoMovhos;
	
	//$wbasedatoMovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$arrayLote = consultarDatosLote($wbasedato,$codigo,$lote);
	
	if($fecha=="")
	{
		$fecha = $arrayLote['fecha'];
	}
	
	if($hora=="")
	{
		$hora = $arrayLote['hora'];
	}
	
	if($revisado=="" || $revisado=="null")
	{
		$revisado = $arrayLote['nombreRevisa'];
		$usuarioRevisa = $arrayLote['usuarioRevisa'];
	}
	
	if($elaborado=="" || $elaborado=="null")
	{
		$elaborado = $arrayLote['nombreElabora'];
		$usuarioElabora = $arrayLote['usuarioElabora'];
	}
	
	if($quimico=="")
	{
		$quimico = $arrayLote['nombreCrea'];
	}
	
	$insumosAdicionales = consultarInsumosAdicionales($wbasedato,$wbasedatoMovhos,$wemp_pmla,$codigo);
	
	// ---------------------------------------------------------
	
	$nutricionistaDietista = $nutricionista;
	$medicoTratante = $medico;
	
	if($historia!="")
	{
		$ingreso = consultarIngreso($wbasedatoMovhos,$historia,$codigo);
	
		$datosEncabezado = consultarEncabezadoNPT($wbasedatoMovhos,$historia,$ingreso,$codigo);
		
		if($nutricionistaDietista=="")
		{
			$nutricionistaDietista = $datosEncabezado['nombreUsuario'];
		}
		
		if($medicoTratante=="")
		{
			$medicoTratante = consultarMedicosTratantes($wbasedatoMovhos,$historia,$ingreso,$fecha);
		}
		// ---------------------------------------------------------
	}
	
	$q= " SELECT Artpes, Artpur, Arttin"
	."       FROM ".$wbasedato."_000002 "
	."    WHERE Artcod = '".$codigo."' "
	."       AND Artest = 'on' ";

	$res = mysql_query($q,$conex);
	$row=mysql_fetch_array($res);
	$peso=$row[0];
	$purga=$row[1].' ml';
	$tiempoInfusion=floor($row[2]/60);
	
	if(!isset($horas))
	{
		$horas=$tiempoInfusion;
	}
	
	// ---------------------------------------------------------
	
	$q= " SELECT Artcod, Artcom, Artgen, Artuni, Tipppe, Tipmmq, Artord "
	."       FROM ".$wbasedato."_000002, ".$wbasedato."_000001 "
	."    WHERE tipinu = 'on' "
	."       AND tipmat <> 'on' "
	."       AND Artest = 'on' "
	."       AND Tipest = 'on' "
	."       AND Tipcod = Arttip "
	."    Order by 7 ";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);
	$vol=0;
	$exp=explode(' ',$purga);
	$subtotal1=0;
	$subtotal2=0;
	for ($i=0;$i<$num;$i++)
	{
		$row = mysql_fetch_array($res);

		$q= " SELECT Pdecan, Pdefac "
		."       FROM ".$wbasedato."_000003 "
		."    WHERE  Pdepro = '".$codigo."' "
		."       AND Pdeins = '".$row[0]."' "
		."       AND Pdeest = 'on' ";

		$err = mysql_query($q,$conex);
		$row1 = mysql_fetch_array($err);

		if($row[4]=='on' and $peso!=0 and $peso!='')
		{
			$q= " SELECT Facdes "
			."       FROM ".$wbasedato."_000015 "
			."    WHERE  Facval = '".$row1[1]."' "
			."       AND Facest = 'on' "
			."       AND Facart = '".$row[0]."' ";
			$errf = mysql_query($q,$conex);
			$numf = mysql_num_rows($errf);
			if($numf>0)
			{
				$rowf = mysql_fetch_array($errf);
				$inslis[$i]['des']=$rowf[0];
			}
			else
			{
				$q= " SELECT Facdes "
				."       FROM ".$wbasedato."_000015 "
				."    WHERE  Facval = '".(($row1[1]*$peso))."' "
				."       AND Facest = 'on' "
				."       AND Facart = '".$row[0]."' ";
				$errf = mysql_query($q,$conex);
				$numf = mysql_num_rows($errf);
				$rowf = mysql_fetch_array($errf);
				$inslis[$i]['des']=$rowf[0];
			}
		}
		else
		{
			$inslis[$i]['des']='ml';
		}

		$inslis[$i]['cod']=$row[0];
		$inslis[$i]['nom']=str_replace('-',' ',$row[1]);
		$inslis[$i]['ppe']=$row[4];
		$inslis[$i]['can']=$row1[0];
		$inslis[$i]['tot']=$row1[0];
		$inslis[$i]['fac']=$row1[1];

		$inslis[$i]['agua']=$row[5];
		$vol=$vol+$inslis[$i]['can'];
	}
	
	for($i=0;$i<count($inslis);$i++)
	{
		$inslis[$i]['can'] = (float)$inslis[$i]['can'];
		$exp[0] = (float)$exp[0];
		
		if(isset($inslis[$i]['can']))
		{
			if($peso==0 or $peso=='' or $inslis[$i]['ppe']!='on')
			{
				$inslis[$i]['can']=round($inslis[$i]['can']-$inslis[$i]['can']*$exp[0]/$vol,2);
				$inslis[$i]['req']=$inslis[$i]['can'];
			}
			else
			{
				$inslis[$i]['can']=round($inslis[$i]['can']-($inslis[$i]['can']*$exp[0]/$vol),2);
				
				$inslis[$i]['req']=0;
				if($peso*$inslis[$i]['fac']>0)
				{
					$inslis[$i]['req']=round($inslis[$i]['can']/($peso*$inslis[$i]['fac']),2);
				}
			}
		}
		else
		{
			$inslis[$i]['can']=0;
			$inslis[$i]['req']=0;
			$inslis[$i]['tot']=0;
		}
		
		if($inslis[$i]['agua']!='on')
		{
			$subtotal1=$subtotal1+$inslis[$i]['can'];
			$subtotal2=$subtotal2+$inslis[$i]['tot'];
		}
	}
	
	// ---------------------------------------------------------
		if($institucion=="")
		{
			$q =  " SELECT Subcodigo, Descripcion 
					  FROM ".$wbasedato."_000002 a,det_selecciones  b
					 WHERE Artcod='".$codigo."'
					   AND Subcodigo=Artins
					   AND b.Medico = '".$wbasedato."'
					   AND Codigo = '04'
					   AND Activo = 'A';";
					   
			$err=mysql_query($q,$conex);
			$row=mysql_fetch_array($err);
			$institucion=$row[1];
		}
		

		$q= " SELECT SUM((Pdecan-(Pdecan*".($exp[0]/$vol)."))*Nutcon*Nutcal) "
		."       FROM ".$wbasedato."_000003, ".$wbasedato."_000016 "
		."    WHERE  Pdepro = '".$codigo."' "
		."       AND Pdeins = Nutart "
		."       AND Pdeest = 'on' "
		."       AND Nuttip in ('LO-Lipido', 'CO-Carbohidrato', 'PA-Proteina') "
		."       AND Nutest='on' ";


		$res = mysql_query($q,$conex);
		$row = mysql_fetch_array($res);
		$cal=$row[0]/100;
		
	$q= " SELECT Pdecan, Nutcon "
	."       FROM ".$wbasedato."_000003, ".$wbasedato."_000016 "
	."    WHERE  Pdepro = '".$codigo."' "
	."       AND Pdeins = Nutart "
	."       AND Pdeest = 'on' "
	."       AND Nuttip= 'CO-Carbohidrato' "
	."       AND Nutest='on' ";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	$carbohidratos=0;
	for ($i=0;$i<$num;$i++)
	{
		$row = mysql_fetch_array($res);
		if($vol>0)
		{
			$carbohidratos=$carbohidratos+($row[0]*$row[1]/$vol);
		}
		
	}
	
	// ---------------------------------------------------------
	
	$q= " SELECT Pdecan, Nutcon "
	."       FROM ".$wbasedato."_000003, ".$wbasedato."_000016 "
	."    WHERE  Pdepro = '".$codigo."' "
	."       AND Pdeins = Nutart "
	."       AND Pdeest = 'on' "
	."       AND Nuttip= 'PA-Proteina' "
	."       AND Nutest='on' ";

		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);

		$proteinas=0;
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($res);
			
			if($vol>0)
			{
				$proteinas=$proteinas+($row[0]*$row[1]/$vol);
			}
			
		}
		
	// ---------------------------------------------------------
	
	$q= " SELECT Pdecan, Nutcon "
	."       FROM ".$wbasedato."_000003, ".$wbasedato."_000016 "
	."    WHERE  Pdepro = '".$codigo."' "
	."       AND Pdeins = Nutart "
	."       AND Pdeest = 'on' "
	."       AND Nuttip= 'LO-Lipido' "
	."       AND Nutest='on' ";

		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);

		$lipidos=0;
		$lipidos2=0;
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($res);
			
			if($vol>0)
			{
				$lipidos=$lipidos+($row[0]*$row[1]/$vol);
				$lipidos2=$lipidos2+$row[0]/$vol;
			}
			
		}
		$lipidos2=$lipidos2*100;

	// ---------------------------------------------------------
		
	$q= " SELECT Pdecan, Nutosm "
	."       FROM ".$wbasedato."_000003, ".$wbasedato."_000016 "
	."    WHERE  Pdepro = '".$codigo."' "
	."       AND Pdeins = Nutart "
	."       AND Pdeest = 'on' "
	."       AND Nutest='on' ";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	$osm=0;
	for ($i=0;$i<$num;$i++)
	{
		$row = mysql_fetch_array($res);
		
		if($vol>0)
		{
			$osm=$osm+(($row[0]-($row[0]*$exp[0]/$vol))*$row[1]);
		}
	}
	
	if($vol>0)
	{
		$osm=$osm/($vol-(float)$purga);
	}
	
		
	// ---------------------------------------------------------	
	
	$q= " SELECT Pdecan, Nutcon "
	."       FROM ".$wbasedato."_000003, ".$wbasedato."_000016 "
	."    WHERE  Pdepro = '".$codigo."' "
	."       AND Pdeins = Nutart "
	."       AND Pdeest = 'on' "
	."       AND Nuttip= 'PA-Proteina' "
	."       AND Nutest='on' ";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	$amino=0;
	for ($i=0;$i<$num;$i++)
	{
		$row = mysql_fetch_array($res);
		if($vol>0)
		{
			$amino=$amino+(($row[0]-($row[0]*$exp[0]/$vol))*$row[1]);
		}
		
	}

	$amino=$amino/100;
	$nitro=$amino*16/100;
	
	// ---------------------------------------------------------
	

	if($nitro>0)
	{
		$q= " SELECT SUM((Pdecan-(Pdecan*".($exp[0]/$vol)."))*Nutcon*Nutcal) "
		."       FROM ".$wbasedato."_000003, ".$wbasedato."_000016 "
		."    WHERE  Pdepro = '".$codigo."' "
		."       AND Pdeins = Nutart "
		."       AND Pdeest = 'on' "
		."       AND Nuttip in ('LO-Lipido','CO-Carbohidrato') "
		."       AND Nutest='on' ";
		$res = mysql_query($q,$conex);
		$row = mysql_fetch_array($res);
		$nopro=$row[0]/100;
		$calnit=$nopro/$nitro;
	}
	
	// ---------------------------------------------------------
	
	$calami=$calnit/6.25;
	
	// ---------------------------------------------------------
	$q= " SELECT Pdecan "
	."       FROM ".$wbasedato."_000003, ".$wbasedato."_000016 "
	."    WHERE  Pdepro = '".$codigo."' "
	."       AND Pdeins = Nutart "
	."       AND Pdeest = 'on' "
	."       AND Nuttip= 'CA-Calcio Gluconato' "
	."       AND Nutest='on' ";

	$res = mysql_query($q,$conex);
	$row = mysql_fetch_array($res);
	$calcio=0;
	if($vol>0)
	{
		$calcio=$row[0]*9.3*25/$vol;
	}
	
	$fosfato=0;
	if($vol>0)
	{
		$q= " SELECT Pdecan, tipppe, Pdeins "
		."       FROM ".$wbasedato."_000003, ".$wbasedato."_000001,  ".$wbasedato."_000002,  ".$wbasedato."_000016"
		."    WHERE  Pdepro = '".$codigo."' "
		."       AND Pdeins = Nutart "
		."       AND Pdeest = 'on' "
		."       AND Nuttip= 'FO-Fosfato de Potasio' "
		."       AND Nutest='on' "
		."       AND Artcod = Nutart "
		."       AND Arttip = Tipcod ";

		$res = mysql_query($q,$conex);
		$row = mysql_fetch_array($res);
	
		if ($row[1]!='on' or $peso==0 or $peso=='')
		{
			$fosfato=$row[0]*93*32.2/$vol;
		}
		else
		{
			$q= " SELECT Pdefac "
			."       FROM ".$wbasedato."_000003"
			."    WHERE  Pdepro = '".$codigo."' "
			."       AND Pdeins = '".$row[2]."' "
			."       AND Pdeest = 'on' ";

			$res2 = mysql_query($q,$conex);
			$row2 = mysql_fetch_array($res2);
			$fosfato=round($row[0]/($peso*$row2[0]))*93*32.2/$vol;
		}
	}
	

	$calfos=$calcio*$fosfato/100;
	
	// ---------------------------------------------------------
	
	$q= " SELECT Pdecan, Pdeins "
	."       FROM ".$wbasedato."_000003,  ".$wbasedato."_000016"
	."    WHERE  Pdepro = '".$codigo."' "
	."       AND Pdeins = Nutart "
	."       AND Pdeest = 'on' "
	."       AND Nuttip in ('FO-Fosfato de Potasio', 'CA-Calcio Gluconato', 'EL-Electrolito') "
	."       AND Nutest='on' ";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	$electrolito=0;
	for ($i=0;$i<$num;$i++)
	{
		$row = mysql_fetch_array($res);
		$electrolito=$electrolito+($row[0]);
	}
	
	$electrolito=0;
	if($vol>0)
	{
		$electrolito=$electrolito*100/$vol;
	}
	
	
	// ---------------------------------------------------------
	
	$q= " SELECT Pdecan, Pdeins "
	."       FROM ".$wbasedato."_000003,  ".$wbasedato."_000016"
	."    WHERE  Pdepro = '".$codigo."' "
	."       AND Pdeins = Nutart "
	."       AND Pdeest = 'on' "
	."       AND Nuttip ='CA-Calcio Gluconato'  "
	."       AND Nutest='on' ";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);
	$precipita=0;
	$suma=0;
	for ($i=0;$i<$num;$i++)
	{
		$row = mysql_fetch_array($res);
		$precipita=	$precipita+($row[0]*0.46);
		$suma=$suma+$row[0];
	}

	$q= " SELECT Pdecan, Pdeins "
	."       FROM ".$wbasedato."_000003,  ".$wbasedato."_000016"
	."    WHERE  Pdepro = '".$codigo."' "
	."       AND Pdeins = Nutart "
	."       AND Pdeest = 'on' "
	."       AND Nuttip ='FO-Fosfato de Potasio'  "
	."       AND Nutest='on' ";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);
	for ($i=0;$i<$num;$i++)
	{
		$row = mysql_fetch_array($res);
		$precipita=	$precipita+($row[0]*3);
		$suma=$suma+$row[0];
	}

	$q= " SELECT Pdecan, Pdeins "
	."       FROM ".$wbasedato."_000003,  ".$wbasedato."_000016"
	."    WHERE  Pdepro = '".$codigo."' "
	."       AND Pdeins = Nutart "
	."       AND Pdeest = 'on' "
	."       AND Nuttip ='NA-Sodio Fosfato'  "
	."       AND Nutest='on' ";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);
	for ($i=0;$i<$num;$i++)
	{
		$row = mysql_fetch_array($res);
		$precipita=	$precipita+($row[0]);
		$suma=$suma+$row[0];
	}
	
	$precipita=	0;
	if($vol-$suma>0)
	{
		$precipita=	$precipita*100/($vol-$suma);
	}
	

	// ---------------------------------------------------------
	
	$q= " SELECT SUM(Pdecan*Artgra) "
		."       FROM ".$wbasedato."_000003, ".$wbasedato."_000002 "
		."    WHERE  Pdepro = '".$codigo."' "
		."       AND Pdeins = Artcod "
		."       AND Pdeest = 'on' "
		."       AND Artest='on' ";

		$res = mysql_query($q,$conex);
		$row = mysql_fetch_array($res);
		$pesteo=$row[0];
	
	// ---------------------------------------------------------
	
	if($nombre=="")
	{
		$q = "SELECT Oriing, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac "
		."      FROM root_000037, root_000036 "
		."     WHERE Orihis = '".$historia."' "
		."       AND Oriori = '01' "
		."       AND Oriced = Pacced "
		."       AND Oritid = Pactid ";

		$err=mysql_query($q,$conex);
		$row=mysql_fetch_array($err);
		
		$nombre=$row[1].' '.$row[2].' '.$row[3].' '.$row[4];
		
	}

	if(($servicio=="" || $cama=="") && $ingreso!="")
	{
		$q = "SELECT Ubisac, Ubihac, Cconom "
		."      FROM ".$wbasedatoMovhos."_000018, ".$wcostosyp."_000005 "
		."     WHERE Ubihis = '".$historia."' "
		."       AND ubiing = '".$ingreso."' "
		."       AND ubisac = Ccocod "
		."       AND Ccoemp = '01' ";

		$err=mysql_query($q,$conex);
		$row1=mysql_fetch_array($err);

		$servicio=$row1[2];
		$cama=$row1[1];
	}
	
	$codigoBarras = "<img src='../../../include/root/clsGenerarCodigoBarras.php?width=150&height=125&barcode=".$codigo."' style='margin-left: 17px;'>";		
	
	$htmlRotulo = "";
	
	$htmlRotulo .= "<form name='rotulo2' action='rotulo2.php?wemp_pmla=".$wemp_pmla."' method=post>";
	if (!isset($imprimir) or $imprimir==0)
	{
		$htmlRotulo .= "<table style='border-bottom: 1px solid #000000;' style='border-left: 1px solid #000000;'align='center' cellspacing='0' cellpading='0'>" ;
		$htmlRotulo .= "<tr>" ;
		$htmlRotulo .= "<td colspan='4' class='texto1'><table cellspacing='0' cellpading='0'><tr><td align='center' width='90'><img src='/matrix/images/medical/root/clinica.jpg' height='35' width='75'></td>";
		$htmlRotulo .= "<td align='center' class='texto3' width='400'> <font color='#006699'>SERVICIO FARMACEUTICO </BR> CENTRAL DE MEZCLAS PARENTERALES</font></td>";
		$htmlRotulo .= "<td align='center' width='90'> <font color='#006699'>Hab: ".$cama."<br>".$codigo."</font></td>";
		$htmlRotulo .= "<td align='center' width='90'>&nbsp;&nbsp;&nbsp;</font></td>";
		$htmlRotulo .= "<td align='center' width='90'> <font size='6'  face='3 of 9 barcode'>".$codigoBarras."</font></td></tr></table></td>";
		$htmlRotulo .= "</tr>" ;
	}
	else
	{
		$htmlRotulo .= "<table style='border-bottom: 1px solid #000000;width:390px;' style='border-left: 1px solid #000000;'align='center' cellspacing='0' cellpading='0'>" ;
		$htmlRotulo .= "<tr>" ;
		$htmlRotulo .= "<td colspan='4' class='texto1'><table cellspacing='0' cellpading='0' align='center'><tr><td align='center' width='50'><img src='/matrix/images/medical/root/clinica.jpg' height='20' width='50'></td>";
		$htmlRotulo .= "<td align='center' class='texto4'  width='120'> <font color='#006699'>SERVICIO FARMACEUTICO </BR> CENTRAL DE MEZCLAS PARENTERALES</font></td>";
		$htmlRotulo .= "<td align='center' width='110'> <font color='#006699' size='1'>Hab: ".$cama."<br>".$codigo."</font></td>";
		$htmlRotulo .= "<td align='center' width='110'> &nbsp;&nbsp;&nbsp;</font></td>";
		$htmlRotulo .= "<td align='center' width='110'> <font size='6'  face='3 of 9 barcode'>".$codigoBarras."</font></td></tr></table></td>";
		$htmlRotulo .= "</tr>" ;
	}

	$campoHabilitado="";
	if(isset($consulta) && $consulta=="on")
	{
		$campoHabilitado="readOnly='readOnly'";
	}
	
	if (!isset($imprimir) or $imprimir==0)
	{
		$htmlRotulo .= "<tr>" ;
		// if ($historia=='')
		if ($historia=='' || $insti!="01")
		{
			$q = "SELECT Pacnom, Pachis, Pacser, Paceda, Paccam, Pacmed, Pacnut "
			."      FROM ".$wbasedato."_000020 "
			."     WHERE Pacpro = '".$codigo."' ";

			$err=mysql_query($q,$conex);
			$num=mysql_num_rows($err);
			$row=mysql_fetch_array($err);

			if ($num<=0)
			{
				$nombre='ENTIDAD EXTERNA';
				$htmlRotulo .= "<td  colspan='4' class='texto1'>NOMBRE DEL PACIENTE: <input type='text' ".$campoHabilitado." class='texto1' id='nombre' name='nombre' value='".$nombre."' size='50'></td></tr>";
				$historia='';
				// $edad='';
				$htmlRotulo .= "<td  colspan='2' class='texto1'>INSTITUCION: <input type='text' ".$campoHabilitado." class='texto1' id='institucion' name='institucion' value='".$institucion."' size='50'></td>";
				$servicio='';
				$htmlRotulo .= "<td  colspan='2' class='texto1'>SERVICIO: <input type='text' ".$campoHabilitado." class='texto1' id='servicio' name='servicio' value='".$servicio."' size='50'></td></tr>";
				$htmlRotulo .= "<td  colspan='2' class='texto1'>HISTORIA No: <input type='text' ".$campoHabilitado." class='texto1' name='historia' id='historia' value='".$historia."' size='10'></td>";
				$htmlRotulo .= "<td  colspan='2' class='texto1'>EDAD: <input type='text' ".$campoHabilitado." class='texto1' id='edad' name='edad' value='".$edad."' size='5'></td></tr>";
				$htmlRotulo .= "<td  colspan='3' class='texto1'>Químico farmacéutico:<input type='text' ".$campoHabilitado." class='texto1' name='quimico' id='quimico' value='".$quimico."' size='50'></td>";
				$cama='';
				$htmlRotulo .= "<td  colspan='1' class='texto1'><b>CAMA:<input type='text' ".$campoHabilitado." class='texto1' id='cama' name='cama' value='".$cama."' size='10'></b></td></tr>";
				$medico='';
				$nutricionista='';
				$htmlRotulo .= "<input type='hidden' class='texto1' name='guardar' value='1'>";
			}
			else
			{
				$htmlRotulo .= "<td  colspan='4' class='texto1'>NOMBRE DEL PACIENTE: <input type='text' ".$campoHabilitado." class='texto1' id='nombre' name='nombre' value='".$row[0]."' size='50'></td></tr>";
				$htmlRotulo .= "<td  colspan='2' class='texto1'>INSTITUCION: <input type='text' ".$campoHabilitado." class='texto1' id='institucion' name='institucion' value='".$institucion."' size='50'></td>";
				$htmlRotulo .= "<td  colspan='2' class='texto1'>SERVICIO: <input type='text' ".$campoHabilitado." class='texto1' id='servicio' name='servicio' value='".$row[2]."' size='50'></td></tr>";
				$htmlRotulo .= "<td  colspan='2' class='texto1'>HISTORIA No: <input type='text' ".$campoHabilitado." class='texto1' name='historia'  id='historia' value='".$row[1]."' size='10'></td>";
				$htmlRotulo .= "<td  colspan='2' class='texto1'>EDAD: <input type='text' ".$campoHabilitado." class='texto1' id='edad' name='edad' value='".$row[3]."' size='5'></td></tr>";
				$htmlRotulo .= "<td  colspan='3' class='texto1'>Químico farmacéutico:<input type='text' ".$campoHabilitado." class='texto1' name='quimico' id='quimico' value='".$quimico."' size='50'></td>";
				$htmlRotulo .= "<td  colspan='1' class='texto1'><b>CAMA:<input type='text' ".$campoHabilitado." class='texto1' id='cama' name='cama' value='".$row[4]."' size='10'></b></td></tr>";
				
				
				$medico=$row[5];
				$nutricionista=$row[6];
				
				$medicoTratante = $medico;
				$nutricionistaDietista = $nutricionista;
				$htmlRotulo .= "<input type='hidden' class='texto1' name='guardar' value='0'>";
			}
		}
		else
		{
			$htmlRotulo .= "<td  colspan='4' class='texto1'>NOMBRE DEL PACIENTE: ".$nombre."</td><input type='hidden'  class='texto1' id='nombre' name='nombre' value='".$nombre."' size='50'></tr>";

			$htmlRotulo .= "<td  colspan='2' class='texto1'>INSTITUCION: ".$institucion."</td><input type='hidden' class='texto1' id='institucion' name='institucion' value='".$institucion."' size='50'>";

			$htmlRotulo .= "<td  colspan='2' class='texto1'>SERVICIO: ".$servicio."</td><input type='hidden' class='texto1' id='servicio' name='servicio' value='".$servicio."' size='50'></tr>";
			$htmlRotulo .= "<td  colspan='2' class='texto1'>HISTORIA No: ".$historia."</td><input type='hidden' class='texto1' name='historia' id='historia' value='".$historia."' size='50'>";

			$dia=date('d');
			$mes=date('m');
			$ano=date('Y');

			$exp=explode('-', $row[5]);
			if (isset($exp[1]))
			{
				//si el mes es el mismo pero el dia inferior aun no ha cumplido años, le quitaremos un año al actual
				if (($exp[1] == $mes) && ($exp[2] > $dia))
				{
					$ano=($ano-1);
				}

				//si el mes es superior al actual tampoco habra cumplido años, por eso le quitamos un año al actual

				if ($exp[1] > $mes)
				{
					$ano=($ano-1);
				}

				//ya no habria mas condiciones, ahora simplemente restamos los años y mostramos el resultado como su edad
				if($edad=="")
				{
					$edad=($ano-$exp[0]);
				}
			}
			else
			{
				// $edad='';
			}
			$htmlRotulo .= "<td  colspan='2' class='texto1'>EDAD: ".$edad."</td><input type='hidden' class='texto1' id='edad' name='edad' value='".$edad."' size='50'></tr>";
			$htmlRotulo .= "<td  colspan='3' class='texto1'>Químico farmacéutico: ".$quimico."<input type='hidden' class='texto1' id='quimico' name='quimico' value='".$quimico."' size='50'></td>";
			$htmlRotulo .= "<td  colspan='1' class='texto1'><b>CAMA: ".$cama."<input type='hidden' class='texto1' id='cama' name='cama' value='".$cama."' size='10'></b></td></tr>";
			$medico='';
			$nutricionista='';
		}

		$htmlRotulo .= "<td  colspan='2' class='texto1'>Médico tratante:<input type='text' ".$campoHabilitado." class='texto1' id='medico' name='medico' value='".$medicoTratante."' size='50'></td>";
		$htmlRotulo .= "<td  colspan='2' class='texto1'>Nutricionista Dietista:<input type='text' ".$campoHabilitado." class='texto1' id='nutricionista' name='nutricionista' value='".$nutricionistaDietista."' size='50'></td></tr>";
		$htmlRotulo .= "<td  colspan='2' class='texto1'>Bomba BAXTER PURGA CON:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;20ml</td>";
		
		if($fecha=="")
		{
			$fecha = date('Y-m-d');
		}
		$campoFecha = "<input type='text' ".$campoHabilitado." class='texto1' name='fecha' id='fecha' value='".$fecha."' size='20' readOnly='readOnly'>";
		
		if($hora=="")
		{
			$hora = date('H:i:s');
		}
		$campoHora = "<input type='text' ".$campoHabilitado." class='texto1' name='hora' id='hora' value='".$hora."' size='20' readOnly='readOnly'>";
		
		
		$htmlRotulo .= "<td  colspan='2' class='texto1'>Fecha: ".$campoFecha."</td></tr>";
		$htmlRotulo .= "<td  colspan='2' class='texto1'>Bomba ABBOTT PURGA CON:&nbsp;&nbsp;&nbsp;&nbsp;25ml</td>";
		$htmlRotulo .= "<td  colspan='2' class='texto1'>Hora: ".$campoHora."</td></tr>";
		$htmlRotulo .= "<td  colspan='2' class='texto1'>Bomba 3M PURGA CON:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;30ml</td>";

		
		$htmlRotulo .= "<td  colspan='2' class='texto1'>Peso Kg: ".$peso."</td><input type='hidden' class='texto1' name='peso' value='".$peso."' size='15'></tr>";


		$htmlRotulo .= "<td  colspan='1' align='center' class='texto1'>MACRO Y MICRONUTRIENTES</td>";
		$htmlRotulo .= "<td  colspan='1' align='center'  class='texto1'>REQUERIMIENTO</td>";
		$htmlRotulo .= "<td  colspan='1' align='center'  class='texto1'>CALCULO DE VOLUMEN (ml)</td>";
		$htmlRotulo .= "<td  colspan='1' align='center'  class='texto1'><font color='dark pink'>CALCULO X PURGA ".$purga."</td></tr>";
		$htmlRotulo .= "<input type='hidden' class='texto1' name='purga' value='".$purga."' size='50'>";

		for($i=0;$i<count($inslis);$i++)
		{
			if($inslis[$i]['agua']!='on')
			{
				if($inslis[$i]['can']>0)
				{
					$q= " SELECT Tarcpo "
					."       FROM ".$wbasedato."_000018 "
					."    WHERE  Tarcce = '".$inslis[$i]['cod']."' "
					."       AND Tarest = 'on' ";

					$cod = mysql_query($q,$conex);
					$tarifa = mysql_fetch_array($cod);

					if($inslis[$i]['des']!='')
					{
						$htmlRotulo .= "<td  colspan='1'  class='texto1'>".$tarifa[0]." &nbsp;&nbsp;  ".$inslis[$i]['nom']." (".$inslis[$i]['des'].") </td>";
					}
					else
					{
						$htmlRotulo .= "<td  colspan='1'  class='texto1'>".$tarifa[0]." &nbsp; &nbsp;  ".$inslis[$i]['nom']." </td>";
					}
				}
			}
			else
			{
				$agua=$i;
				$htmlRotulo .= "<input type='hidden' class='texto1' name='agua' value='".$agua."'>";
			}

			if($inslis[$i]['agua']!='on')
			{
				if($inslis[$i]['can']>0)
				{
					if($peso==0 or $peso=='')
					{
						$htmlRotulo .= "<td  colspan='1' class='texto1' align='right'>".number_format($inslis[$i]['req'],2,',','.')."</td>";
						$htmlRotulo .= "<td  colspan='1' class='texto1' align='right'>".number_format($inslis[$i]['can'],2,',','.')."</td>";
						$htmlRotulo .= "<td  colspan='1' class='texto1' align='right'>".number_format($inslis[$i]['tot'],2,',','.')."</></td></tr>";
					}
					else
					{
						$htmlRotulo .= "<td  colspan='1' class='texto1' align='right'>".number_format($inslis[$i]['req'],1,',','.')."</td>";
						$htmlRotulo .= "<td  colspan='1' class='texto1' align='right'>".number_format($inslis[$i]['can'],1,',','.')."</td>";
						$htmlRotulo .= "<td  colspan='1' class='texto1' align='right'>".number_format($inslis[$i]['tot'],1,',','.')."</></td></tr>";
					}
				}
			}
			$htmlRotulo .= "<input type='hidden' class='texto1' name='inslis[".$i."][nom]' value='".$inslis[$i]['nom']."' size='50'>";
			$htmlRotulo .= "<input type='hidden' class='texto1' name='inslis[".$i."][req]' value='".$inslis[$i]['req']."' size='50'>";
			$htmlRotulo .= "<input type='hidden' class='texto1' name='inslis[".$i."][can]' value='".$inslis[$i]['can']."' size='50'>";
			$htmlRotulo .= "<input type='hidden' class='texto1' name='inslis[".$i."][tot]' value='".$inslis[$i]['tot']."' size='50'>";
			$htmlRotulo .= "<input type='hidden' class='texto1' name='inslis[".$i."][des]' value='".$inslis[$i]['des']."' size='50'>";

		}
		$htmlRotulo .= "<tr><td  colspan='1' class='texto1'  bgcolor='#DDDDDD'>SUBTOTAL</></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto1'  bgcolor='#DDDDDD' align='right'>&nbsp;</></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto1' bgcolor='#DDDDDD' align='right'>".number_format($subtotal1,0,',','.')."</td>";
		$htmlRotulo .= "<td  colspan='1' class='texto1' bgcolor='#DDDDDD' align='right'>".number_format($subtotal2,0,',','.')."</td></tr>";
		$htmlRotulo .= "<input type='hidden' class='texto1' name='subtotal1' value='".$subtotal1."'>";
		$htmlRotulo .= "<input type='hidden' class='texto1' name='subtotal2' value='".$subtotal2."'>";

		$htmlRotulo .= "<tr><td  colspan='1' class='texto1'><font color='#006699'>AGUA</font></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto1' align='right'>&nbsp;</td>";
		if(isset($agua))
		{
			$htmlRotulo .= "<td  colspan='1' class='texto1' align='right'>".number_format($inslis[$agua]['can'],0,',','.')."</td>";
			$htmlRotulo .= "<td  colspan='1' class='texto1' align='right'>".number_format($inslis[$agua]['tot'],0,',','.')."</td></tr>";
		}
		else
		{
			$htmlRotulo .= "<td  colspan='1' class='texto1' align='right'>0</td>";
			$htmlRotulo .= "<td  colspan='1' class='texto1' align='right'>0</></td></tr>";
		}

		$htmlRotulo .= "<tr><td  colspan='1' class='texto1' ><b>VOLUMEN TOTAL (ml)</b></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto1'  align='right'>&nbsp;</td>";
		$htmlRotulo .= "<td  colspan='1' class='texto1' align='right'><b>".number_format(($vol-(float)$purga),0,',','.')."</b></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto1' align='right'>".number_format($vol,0,',','.')."</td></tr>";
		$htmlRotulo .= "<input type='hidden' class='texto1' name='vol' value='".$vol."'>";

		$htmlRotulo .= "<tr><td  colspan='1' class='texto1' ><font color='darkpink'><b>VELOCIDAD DE INFUSION (ml / hora)</b></font></td>";
		if(!isset($horas))
		{
			$htmlRotulo .= "<td  colspan='1' class='texto1' align='right'><font color='darkpink'><b>".number_format((($vol-(float)$purga)/24),1,',','.')."</b></font></td>";
			$htmlRotulo .= "<td colspan='2' class='texto1' align='center' >Horas:&nbsp;<input type='text' ".$campoHabilitado." class='texto1' id='horas' name='horas' value='24' size='5' onchange='seleccionarLote(0,true);'></td>";
		}
		else
		{
			$htmlRotulo .= "<td  colspan='1' class='texto1' align='right'><font color='darkpink'><b>".number_format((($vol-(float)$purga)/$horas),1,',','.')."</b></font></td>";
			$htmlRotulo .= "<td colspan='2' class='texto1' align='center' >Horas:&nbsp;<input type='text' ".$campoHabilitado." class='texto1' id='horas' name='horas' value='".$horas."' onchange='seleccionarLote(0,true);' size='5'></td>";
		}
		
		// Bolsa y equipos
		foreach($insumosAdicionales as $keyInsumo => $valueInsumo)
		{
			$htmlRotulo .=  "<tr>
								<td  colspan='1' class='texto1' >".$valueInsumo['generico']." (".ucfirst(strtolower($valueInsumo['unidad'])).")</td>
								<td  colspan='1' class='texto1' align='left'><span style='margin-left:160px'>".$valueInsumo['cantidad']."</span></td>
								<td  colspan='2' class='texto1' align='center' ></td>
							</tr>";
		}

		$htmlRotulo .= "<tr><td  colspan='1' class='texto1' >CONCENTRACION DE CARBOHIDRATO (%)</font></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto1' align='right'>".number_format($carbohidratos,2,',','.')."</font></td>";
		$htmlRotulo .= "<td colspan='2' class='texto1' align='center' >&nbsp;</td>";
		$htmlRotulo .= "<input type='hidden' class='texto1' name='carbohidratos' value='".$carbohidratos."'>";


		
		$htmlRotulo .= "<tr><td  colspan='1' class='texto1' >CONCENTRACION DE PROTEINAS (>=1%)</font></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto1' align='right'>".number_format($proteinas,2,',','.')."</font></td>";
		
		/************************************************************************************************************
		 * Marzo 29 de 2011
		 ************************************************************************************************************/
		if( $proteinas >= 1 ){
			$htmlRotulo .= "<td colspan='2' class='texto1' align='center' ><font color='green'>SEGURA</font></td>";
		}
		else{
			$htmlRotulo .= "<td colspan='2' class='texto1' align='center' ><font color='darkpink'>INSEGURA</font></td>";
		}
		/************************************************************************************************************/
		
		$htmlRotulo .= "<input type='hidden' class='texto1' name='proteinas' value='".$proteinas."'>";

		
		$htmlRotulo .= "<tr><td  colspan='1' class='texto1' >CONCENTRACION DE LIPIDOS (>1%)</font></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto1' align='right'>".number_format($lipidos,2,',','.')."</font></td>";
		
		/************************************************************************************************************
		 * Marzo 29 de 2011
		 ************************************************************************************************************/
		if( $lipidos > 1 ){
			$htmlRotulo .= "<td colspan='2' class='texto1' align='center' ><font color='green'>SEGURA</font></td>";
		}
		else{
			$htmlRotulo .= "<td colspan='2' class='texto1' align='center' ><font color='darkpink'>INSEGURA</font></td>";
		}
		/************************************************************************************************************/
		
		$htmlRotulo .= "<input type='hidden' class='texto1' name='lipidos' value='".$lipidos."'>";


		
		$htmlRotulo .= "<tr><td  colspan='1' class='texto1' >OSMOLARIDAD (mOsm / L)</font></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto1' align='right'>".number_format($osm,2,',','.')."</font></td>";
		if($osm<700)
		{
			$htmlRotulo .= "<td colspan='2' class='texto1' align='center' ><font color='darkpink'>PERIFERICA</font></td>";
		}
		else
		{
			$htmlRotulo .= "<td colspan='2' class='texto1' align='center' ><font color='darkpink'>CENTRAL</font></td>";
		}
		$htmlRotulo .= "<input type='hidden' class='texto1' name='osm' value='".$osm."'>";

		
		$htmlRotulo .= "<tr><td  colspan='1' class='texto1' >GRAMOS TOTALES DE NITORGENO</font></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto1' align='right'>".number_format($nitro,2,',','.')."</font></td>";
		$htmlRotulo .= "<td colspan='2' class='texto1' align='center' >&nbsp;</td>";
		$htmlRotulo .= "<input type='hidden' class='texto1' name='nitro' value='".$nitro."'>";



		$htmlRotulo .= "<tr><td  colspan='1' class='texto1' >relación: Cal No proteícas/g Nitrogeno</font></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto1' align='right'>".number_format($calnit,2,',','.')."</font></td>";
		$htmlRotulo .= "<td colspan='2' class='texto1' align='center' >&nbsp;</td>";
		$htmlRotulo .= "<input type='hidden' class='texto1' name='calnit' value='".$calnit."'>";

		

		$htmlRotulo .= "<tr><td  colspan='1' class='texto1' >relación: Cal No proteícas/g A.A</font></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto1' align='right'>".number_format($calami,2,',','.')."</font></td>";
		$htmlRotulo .= "<td colspan='2' class='texto1' align='center' >&nbsp;</td>";
		$htmlRotulo .= "<input type='hidden' class='texto1' name='calami' value='".$calami."'>";

		

		$htmlRotulo .= "<tr><td  colspan='1' class='texto1' >CALORIAS TOTALES</font></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto1' align='right'>".number_format($cal,2,',','.')."</font></td>";
		$htmlRotulo .= "<td colspan='2' class='texto1' align='center' >&nbsp;</td>";
		$htmlRotulo .= "<input type='hidden' class='texto1' name='cal' value='".$cal."'>";

		

		$htmlRotulo .= "<tr><td  colspan='1' class='texto1' >RELACIÓN CALCIO/FÓSFORO   (<3)</font></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto1' align='right'>".number_format($calfos,2,',','.')."</font></td>";
		if($calfos<3)
		{
			$htmlRotulo .= "<td colspan='2' class='texto1' align='center' ><font color='green'>SEGURA</font></td>";
		}
		else
		{
			$htmlRotulo .= "<td colspan='2' class='texto1' align='center' ><font color='darkpink'>INSEGURA</font></td>";
		}
		$htmlRotulo .= "<input type='hidden' class='texto1' name='calfos' value='".$calfos."'>";

		$htmlRotulo .= "<tr><td  colspan='1' class='texto1' >% EN VOLUMEN DE LIPIDOS (>5)</font></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto1' align='right'>".number_format($lipidos2,2,',','.')."</font></td>";
		/*if($lipidos2>=5 or $lipidos2==0)
		{
			$htmlRotulo .= "<td colspan='2' class='texto1' align='center' ><font color='green'>SEGURA</font></td>";
		}
		else
		{
			$htmlRotulo .= "<td colspan='2' class='texto1' align='center' ><font color='darkpink'>INSEGURA</font></td>";
		}*/
		
		//==========================================================================================================
		//Este cambio a solicitud de Silvia el *** 3 de Junio de 2008 ***, se reemplazan las intrucciones anteriores
		//que estan comentadas.
		//==========================================================================================================
		if($lipidos2>=5 or $lipidos2==0)   
		{
			$htmlRotulo .= "<td colspan='2' class='texto1' align='center' ><font color='green'>&nbsp</font></td>";
		}
		else
		{
			$htmlRotulo .= "<td colspan='2' class='texto1' align='center' ><font color='darkpink'>&nbsp</font></td>";
		}
		//=========================================================================================================
		
		$htmlRotulo .= "<input type='hidden' class='texto1' name='lipidos2' value='".$lipidos2."'>";

		

		$htmlRotulo .= "<tr><td  colspan='1' class='texto1' >% DE ELECTROLITOS</font></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto1' align='right'>".number_format($electrolito,2,',','.')."</font></td>";
		if($electrolito<$lipidos2 or $lipidos2==0)
		{
			$htmlRotulo .= "<td colspan='2' class='texto1' align='center' ><font color='green'>SEGURA</font></td>";
		}
		else
		{
			$htmlRotulo .= "<td colspan='2' class='texto1' align='center' ><font color='darkpink'>INSEGURA</font></td>";
		}
		$htmlRotulo .= "<input type='hidden' class='texto1' name='electrolito' value='".$electrolito."'>";


		
		$htmlRotulo .= "<tr><td  colspan='1' class='texto1' >FACTOR DE PRECIPITACION (<=3)</font></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto1' align='right'>".number_format($precipita,2,',','.')."</font></td>";
		if($precipita<3)
		{
			$htmlRotulo .= "<td colspan='2' class='texto1' align='center' ><font color='green'>SEGURA</font></td>";
		}
		else
		{
			$htmlRotulo .= "<td colspan='2' class='texto1' align='center' ><font color='darkpink'>INSEGURA</font></td>";
		}
		$htmlRotulo .= "<input type='hidden' class='texto1' name='precipita' value='".$precipita."'>";



		$htmlRotulo .= "<tr><td  colspan='1' class='texto1'>pH:  (4-7)</font></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto1' align='center'><input type='text' ".$campoHabilitado." class='texto1' id='ph' name='ph' value='5' size='2'></td>";
		$htmlRotulo .= "<td colspan='2' class='texto1' align='center' >&nbsp;</td>";

		

		$htmlRotulo .= "<tr><td  colspan='1' class='texto1'>PESO TEORICO (GRAMOS)</td>";
		$htmlRotulo .= "<td  colspan='1' class='texto1'  align='right'>".number_format($pesteo,1,',','.')."</td>";
		$htmlRotulo .= "<td colspan='2' class='texto1' align='center' >&nbsp;</td>";
		$htmlRotulo .= "<input type='hidden' class='texto1' name='pesteo' value='".$pesteo."'>";
		$htmlRotulo .= "<input type='hidden' class='texto1' name='imprimir' value='1'>";
		$htmlRotulo .= "<input type='hidden' class='texto1' name='insti' value='".$insti."'>";
		$htmlRotulo .= "<input type='hidden' class='texto1' name='codigo' value='".$codigo."'>";
		$htmlRotulo .= "</tr>";
		
		$campoLote = "<input type='text' ".$campoHabilitado." class='texto1' name='lote' id='lote' value='".$lote."' size='20'>";
		
		$htmlRotulo .= "<tr><td class='texto1'>Lote:</td><td class='texto1' colspan='1'>".$campoLote;
		$htmlRotulo .= "</td>";
		$htmlRotulo .= "<td class='texto1' colspan='2'>&nbsp;";
		$htmlRotulo .= "</td>";
		$htmlRotulo .= "</tr>";
		
		
		$elaborado = pintarUsuarios("usuarioElabora",$usuarioElabora,$consulta);
		$revisado = pintarUsuarios("usuarioRevisa",$usuarioRevisa,$consulta);
	
		$altoimagen = "25px;";
		$anchoimagen = "80px;";
		
		$firmaElabora = "";
		if($usuarioElabora!="" && $usuarioElabora!="0")
		{
			$firmaElabora = "<img src='../../images/medical/hce/Firmas/".$usuarioElabora.".png' height='".$altoimagen."' width='".$anchoimagen."' border='0' />";
		}
		
		$firmaRevisa = "";
		if($usuarioRevisa!="" && $usuarioRevisa!="0")
		{
			$firmaRevisa = "<img src='../../images/medical/hce/Firmas/".$usuarioRevisa.".png' height='".$altoimagen."' width='".$anchoimagen."' border='0' />";
		}
		
		
		$htmlRotulo .= "<tr>";
		$htmlRotulo .= "<td class='texto1'>Elaborado por: ".$elaborado."</td>";
		$htmlRotulo .= "<td class='texto1'>".$firmaElabora."</td>";
		$htmlRotulo .= "<td class='texto1' colspan='1'>Revisado por: ".$revisado."</td>";
		$htmlRotulo .= "<td class='texto1'>".$firmaRevisa."</td>";
		$htmlRotulo .= "</tr>";
		$htmlRotulo .= "<tr>";
		$htmlRotulo .= "<td class='texto1' colspan='4'>Conservar en nevera de 2&deg; a 8&deg; cent&iacute;grados</td>";
		$htmlRotulo .= "</tr>";
		
		$htmlRotulo .= "<tr><td  colspan='4' ALIGN='CENTER' class='texto1'><input type='button' name='enviar' value='SIGUIENTE' onclick='seleccionarLote(1,true);'></td>";
	}
	else
	{
		// if (isset($guardar) and $guardar=='1')
		// {
			// $q= " INSERT INTO ".$wbasedato."_000020 (   Medico       ,   Fecha_data,                  Hora_data,              Pacpro,      Pacnom ,     Pachis   ,   Pacser  ,             Paceda ,   Paccam ,         Pacmed    ,  Pacnut,       Seguridad) "
			// ."                               VALUES ('".$wbasedato."',  '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".$codigo."', '".$nombre."', '".$historia."' , '".$servicio."' , '".$edad."', '".$cama."'  , '".$medico."' ,  '".$nutricionista."', 'C-".$wusuario."') ";
			// //$htmlRotulo .= $q;

			// $err = mysql_query($q,$conex);
		// }
		// else if  (isset($guardar))
		// {
			// $q= "   UPDATE ".$wbasedato."_000020 "
			// ."      SET Pacnom = '".$nombre."',  "
			// ."          Pachis= '".$historia."',  "
			// ."          Pacser = '".$servicio."',  "
			// ."          Paceda = '".$edad."',  "
			// ."          Paccam = '".$cama."',  "
			// ."          Pacmed = '".$medico."',  "
			// ."          Pacnut = '".$nutricionista."'  "
			// ."    WHERE Pacpro = '".$codigo."'";
			// $err = mysql_query($q,$conex);
		// }
		
		
		
		$query = "SELECT Pacnom 
				    FROM ".$wbasedato."_000020
				  WHERE Pacpro = '".$codigo."';";
		
		$res =  mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
		$num = mysql_num_rows($res);	
		
		if($num > 0)
		{
			$q= "   UPDATE ".$wbasedato."_000020 "
			."      SET Pacnom = '".$nombre."',  "
			."          Pachis= '".$historia."',  "
			."          Pacser = '".$servicio."',  "
			."          Paceda = '".$edad."',  "
			."          Paccam = '".$cama."',  "
			."          Pacmed = '".$medico."',  "
			."          Pacnut = '".$nutricionista."'  "
			."    WHERE Pacpro = '".$codigo."'";
			$err = mysql_query($q,$conex);
		}
		else
		{
			$q= " INSERT INTO ".$wbasedato."_000020 (   Medico       ,   Fecha_data,                  Hora_data,              Pacpro,      Pacnom ,     Pachis   ,   Pacser  ,             Paceda ,   Paccam ,         Pacmed    ,  Pacnut,       Seguridad) "
			."                               VALUES ('".$wbasedato."',  '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".$codigo."', '".$nombre."', '".$historia."' , '".$servicio."' , '".$edad."', '".$cama."'  , '".$medico."' ,  '".$nutricionista."', 'C-".$wusuario."') ";
			
			$err = mysql_query($q,$conex);
		}


		$htmlRotulo .= "<tr><td  colspan='4' class='texto5'>NOMBRE DEL PACIENTE: ".$nombre."</td></tr>";

		$htmlRotulo .= "<tr><td bgcolor='#ffffcc' colspan='2' class='texto2'>INSTITUCION: ".$institucion."</td>";
		$htmlRotulo .= "<td bgcolor='#ffffcc' colspan='2' class='texto2'>SERVICIO: ".$servicio.".</td></tr>";

		$htmlRotulo .= "<tr><td bgcolor='#ffffcc' colspan='2' class='texto2'>HISTORIA: ".$historia."</td>";
		$htmlRotulo .= "<td bgcolor='#ffffcc' colspan='2' class='texto2'>EDAD: ".$edad."</td></tr>";

		$htmlRotulo .= "<tr><td bgcolor='#ffffcc' colspan='2' class='texto2'>Químico farmacéutico: ".$quimico."</td>";
		$htmlRotulo .= "<td bgcolor='#ffffcc' colspan='2' class='texto5'><b>CAMA:</b> ".$cama.".</b></td></tr>";

		$htmlRotulo .= "<tr><td bgcolor='#ffffcc' colspan='2' class='texto2'>Médico tratante: ".strtoupper($medicoTratante)."</td>";

		$htmlRotulo .= "<td bgcolor='#ffffcc' colspan='2' class='texto2'>Nutricionista Dietista: ".strtoupper($nutricionista)."</td></tr>";
		
		$htmlRotulo .= "<tr><td bgcolor='#ffffcc' colspan='2' class='texto2'>Bomba BAXTER PURGA CON:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;20ml</td>";
		$htmlRotulo .= "<td bgcolor='#ffffcc' colspan='2' class='texto2'>Fecha: ".$fecha."</td></tr>";

		$htmlRotulo .= "<tr><td bgcolor='#ffffcc' colspan='2' class='texto2'>Bomba ABBOTT PURGA CON:&nbsp;&nbsp;&nbsp;&nbsp;25ml</td>";
		$htmlRotulo .= "<td bgcolor='#ffffcc' colspan='2' class='texto2'>Hora: ".$hora."</td></tr>";

		$htmlRotulo .= "<tr><td bgcolor='#ffffcc' colspan='1' class='texto2'>Bomba 3M PURGA CON:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;30ml</td>";
		$htmlRotulo .= "<td bgcolor='#ffffcc' colspan='1' class='texto2'>&nbsp;</td>";
		$htmlRotulo .= "<td bgcolor='#ffffcc' colspan='1' class='texto2'>Peso: ".$peso."</td>";
		$htmlRotulo .= "<td bgcolor='#ffffcc' colspan='1' class='texto2'>&nbsp;</td></tr>";



		$htmlRotulo .= "<td  colspan='1' align='center' class='texto2' style='width:150px'>MACRO Y MICRONUTRIENTES</td>";
		$htmlRotulo .= "<td  colspan='1' align='center'  class='texto2'>REQUERIMIENTO</td>";
		$htmlRotulo .= "<td  colspan='1' align='center'  class='texto2'>CALCULO DE VOLUMEN (ml)</td>";
		$htmlRotulo .= "<td  colspan='1' align='center'  class='texto2'><font color='dark pink'>CALCULO X PURGA ".$purga."</td></tr>";

		for ($i=0;$i<count($inslis);$i++)
		{
			if($inslis[$i]['can']>0)
			{
				if(!isset($agua) or $i!=$agua)
				{
					if($inslis[$i]['des']!='')
					{
						$htmlRotulo .= "<td  colspan='1'  class='texto2'>".$inslis[$i]['nom']." (".$inslis[$i]['des'].")</td>";
					}
					else
					{
						$htmlRotulo .= "<td  colspan='1'  class='texto2'>".$inslis[$i]['nom']."</td>";
					}

					if($peso==0 or $peso=='')
					{
						$htmlRotulo .= "<td  colspan='1' class='texto2' align='right'>".number_format($inslis[$i]['req'],2,',','.')."</td>";
						$htmlRotulo .= "<td  colspan='1' class='texto2' align='right'>".number_format($inslis[$i]['can'],2,',','.')."</td>";
						$htmlRotulo .= "<td  colspan='1' class='texto2' align='right'>".number_format($inslis[$i]['tot'],2,',','.')."</></td></tr>";
					}
					else
					{
						$htmlRotulo .= "<td  colspan='1' class='texto2' align='right'>".number_format($inslis[$i]['req'],1,',','.')."</td>";
						$htmlRotulo .= "<td  colspan='1' class='texto2' align='right'>".number_format($inslis[$i]['can'],1,',','.')."</td>";
						$htmlRotulo .= "<td  colspan='1' class='texto2' align='right'>".number_format($inslis[$i]['tot'],1,',','.')."</></td></tr>";
					}
				}
			}
		}

		$htmlRotulo .= "<tr><td  colspan='1' class='texto2'  bgcolor='#DDDDDD'>SUBTOTAL</></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto2'  bgcolor='#DDDDDD' align='right'>&nbsp;</></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto2' bgcolor='#DDDDDD' align='right'>".number_format($subtotal1,0,',','.')."</></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto2' bgcolor='#DDDDDD' align='right'>".number_format($subtotal2,0,',','.')."</></td></tr>";

		$htmlRotulo .= "<tr><td  colspan='1' class='texto2'><font color='#006699'>AGUA</font></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto2' align='right'>&nbsp;</td>";
		if(isset($agua))
		{
			$htmlRotulo .= "<td  colspan='1' class='texto2' align='right'>".number_format($inslis[$agua]['can'],0,',','.')."</td>";
			$htmlRotulo .= "<td  colspan='1' class='texto2' align='right'>".number_format($inslis[$agua]['tot'],0,',','.')."</></td></tr>";
		}
		else
		{
			$htmlRotulo .= "<td  colspan='1' class='texto2' align='right'>0</td>";
			$htmlRotulo .= "<td  colspan='1' class='texto2' align='right'>0</></td></tr>";
		}

		$htmlRotulo .= "<tr><td  colspan='1' class='texto2'  bgcolor='#ccffff'><b>VOLUMEN TOTAL (ml)</b></></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto2'  bgcolor='#ccffff' align='right'>&nbsp;</></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto2' bgcolor='#ccffff' align='right'><b>".number_format(($vol-(float)$purga),0,',','.')."</b></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto2' bgcolor='#ccffff' align='right'>".number_format($vol,0,',','.')."</></td></tr>";

		$htmlRotulo .= "<tr><td  colspan='1' class='texto2'  bgcolor='#ccffff'><font color='darkpink'><b>VELOCIDAD DE INFUSION (ml / hora)</b></font></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto2' bgcolor='#ccffff' align='right'><b>".number_format((($vol-(float)$purga)/$horas),1,',','.')."</b></font></td>";
		$htmlRotulo .= "<td bgcolor='#ccffff' colspan='2' class='texto2' align='center' >Horas: ".$horas."</td>";
		// $htmlRotulo .= "<td bgcolor='#ccffff' colspan='2' class='texto2' align='center' >&nbsp;</td>";

		// Bolsa y equipos
		foreach($insumosAdicionales as $keyInsumo => $valueInsumo)
		{
			$htmlRotulo .=  "<tr>
								<td  colspan='1' class='texto2' bgcolor='#ccffff' >".$valueInsumo['generico']." (".ucfirst(strtolower($valueInsumo['unidad'])).")</td>
								<td  colspan='1' class='texto2' bgcolor='#ccffff' align='left'><span style='margin-left:160px'>".$valueInsumo['cantidad']."</span></td>
								<td  colspan='2' class='texto2' bgcolor='#ccffff' align='center' ></td>
							</tr>";
		}

		$htmlRotulo .= "<tr><td  colspan='1' class='texto2'  bgcolor='#ccffff'>CONCENTRACION DE CARBOHIDRATO (%)</font></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto2' bgcolor='#ccffff' align='right'>".number_format($carbohidratos,2,',','.')."</font></td>";
		$htmlRotulo .= "<td bgcolor='#ccffff' colspan='2' class='texto2' align='center' >&nbsp;</td>";

		$htmlRotulo .= "<tr><td  colspan='1' class='texto2'  bgcolor='#ccffff'>CONCENTRACION DE PROTEINAS (>=1%)</font></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto2' bgcolor='#ccffff' align='right'>".number_format($proteinas,2,',','.')."</font></td>";
		/************************************************************************************************************
		 * Marzo 29 de 2011
		 ************************************************************************************************************/
		if( $proteinas >= 1 ){
			$htmlRotulo .= "<td bgcolor='#ccffff' colspan='2' class='texto2' align='center' ><font color='green'><b>SEGURA</b></font></td>";
		}
		else
		{
			$htmlRotulo .= "<td bgcolor='#ccffff' colspan='2' class='texto2' align='center' ><font color='darkpink'><b>INSEGURA</b></font></td>";
		}
		/************************************************************************************************************/

		$htmlRotulo .= "<tr><td  colspan='1' class='texto2'  bgcolor='#ccffff'>CONCENTRACION DE LIPIDOS (>1%)</font></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto2' bgcolor='#ccffff' align='right'>".number_format($lipidos,2,',','.')."</font></td>";
		
		/************************************************************************************************************
		 * Marzo 29 de 2011
		 ************************************************************************************************************/
		if( $lipidos > 1 ){
			$htmlRotulo .= "<td bgcolor='#ccffff' colspan='2' class='texto2' align='center' ><font color='green'><b>SEGURA</b></font></td>";
		}
		else
		{
			$htmlRotulo .= "<td bgcolor='#ccffff' colspan='2' class='texto2' align='center' ><font color='darkpink'><b>INSEGURA</b></font></td>";
		}
		/************************************************************************************************************/


		$htmlRotulo .= "<tr><td  colspan='1' class='texto2'  bgcolor='#ccffff'>OSMOLARIDAD (mOsm / L)</font></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto2' bgcolor='#ccffff' align='right'>".number_format($osm,2,',','.')."</font></td>";
		if($osm<700)
		{
			$htmlRotulo .= "<td bgcolor='#ccffff' colspan='2' class='texto2' align='center' ><font color='darkpink'><b>PERIFERICA</b></font></td>";
		}
		else
		{
			$htmlRotulo .= "<td bgcolor='#ccffff' colspan='2' class='texto2' align='center' ><font color='darkpink'><b>CENTRAL</b></font></td>";
		}

		$htmlRotulo .= "<tr><td  colspan='1' class='texto2'  bgcolor='#ccffff'>GRAMOS TOTALES DE NITORIGENO</font></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto2' bgcolor='#ccffff' align='right'>".number_format($nitro,2,',','.')."</font></td>";
		$htmlRotulo .= "<td bgcolor='#ccffff' colspan='2' class='texto2' align='center' >&nbsp;</td>";

		$htmlRotulo .= "<tr><td  colspan='1' class='texto2'  bgcolor='#ccffff'>relación: Cal No proteícas/g Nitrogeno</font></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto2' bgcolor='#ccffff' align='right'>".number_format($calnit,2,',','.')."</font></td>";
		$htmlRotulo .= "<td bgcolor='#ccffff' colspan='2' class='texto2' align='center' >&nbsp;</td>";

		$htmlRotulo .= "<tr><td  colspan='1' class='texto2'  bgcolor='#ccffff'>relación: Cal No proteícas/g A.A</font></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto2' bgcolor='#ccffff' align='right'>".number_format($calami,2,',','.')."</font></td>";
		$htmlRotulo .= "<td bgcolor='#ccffff' colspan='2' class='texto2' align='center' >&nbsp;</td>";

		$htmlRotulo .= "<tr><td  colspan='1' class='texto2'  bgcolor='#ccffff'>CALORIAS TOTALES</font></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto2' bgcolor='#ccffff' align='right'>".number_format($cal,2,',','.')."</font></td>";
		$htmlRotulo .= "<td bgcolor='#ccffff' colspan='2' class='texto2' align='center' >&nbsp;</td>";

		$htmlRotulo .= "<tr><td  colspan='1' class='texto2'  bgcolor='#ccffff'>RELACIÓN CALCIO/FÓSFORO  (<3)</font></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto2' bgcolor='#ccffff' align='right'>".number_format($calfos,2,',','.')."</font></td>";
		if($calfos<3)
		{
			$htmlRotulo .= "<td bgcolor='#ccffff' colspan='2' class='texto2' align='center' ><font color='green'><b>SEGURA</b></font></td>";
		}
		else
		{
			$htmlRotulo .= "<td bgcolor='#ccffff' colspan='2' class='texto2' align='center' ><font color='darkpink'><b>INSEGURA</b></font></td>";
		}


		$htmlRotulo .= "<tr><td  colspan='1' class='texto2'  bgcolor='#ccffff'>% EN VOLUMEN DE LIPIDOS (>5)</font></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto2' bgcolor='#ccffff' align='right'>".number_format($lipidos2,2,',','.')."</font></td>";
		/*if($lipidos2>=5 or $lipidos2==0)
		{
			$htmlRotulo .= "<td bgcolor='#ccffff' colspan='2' class='texto2' align='center' ><font color='green'><b>SEGURA</b></font></td>";
		}
		else
		{
			$htmlRotulo .= "<td bgcolor='#ccffff' colspan='2' class='texto2' align='center' ><font color='darkpink'><b>INSEGURA</b></font></td>";
		}*/
		
		//==========================================================================================================
		//Este cambio a solicitud de Silvia el *** 3 de Junio de 2008 ***, se reemplazan las intrucciones anteriores
		//que estan comentadas.
		//==========================================================================================================
		if($lipidos2>=5 or $lipidos2==0)   
		{
			$htmlRotulo .= "<td colspan='2' class='texto1' align='center' ><font color='green'>&nbsp</font></td>";
		}
		else
		{
			$htmlRotulo .= "<td colspan='2' class='texto1' align='center' ><font color='darkpink'>&nbsp</font></td>";
		}
		//=========================================================================================================

		$htmlRotulo .= "<tr><td  colspan='1' class='texto2'  bgcolor='#ccffff'>% DE ELECTROLITOS</font></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto2' bgcolor='#ccffff' align='right'>".number_format($electrolito,2,',','.')."</font></td>";
		if($electrolito<$lipidos2 or $lipidos2==0)
		{
			$htmlRotulo .= "<td bgcolor='#ccffff' colspan='2' class='texto2' align='center' ><font color='green'><b>SEGURA</b></font></td>";
		}
		else
		{
			$htmlRotulo .= "<td bgcolor='#ccffff' colspan='2' class='texto2' align='center' ><font color='darkpink'><b>INSEGURA</b></font></td>";
		}

		$htmlRotulo .= "<tr><td  colspan='1' class='texto2'  bgcolor='#ccffff'>FACTOR DE PRECIPITACION (<=3)</font></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto2' bgcolor='#ccffff' align='right'>".number_format($precipita,2,',','.')."</font></td>";
		if($precipita<3)
		{
			$htmlRotulo .= "<td bgcolor='#ccffff' colspan='2' class='texto2' align='center' ><font color='green'><b>SEGURA</b></font></td>";
		}
		else
		{
			$htmlRotulo .= "<td bgcolor='#ccffff' colspan='2' class='texto2' align='center' ><font color='darkpink'><b>INSEGURA</b></font></td>";
		}

		$htmlRotulo .= "<tr><td  colspan='1' class='texto2'  bgcolor='#ccffff'>pH   (4-5)</font></td>";
		$htmlRotulo .= "<td  colspan='1' class='texto2' bgcolor='#ccffff' align='right'>".number_format($ph,1,',','.')."</font></td>";
		if($ph>=4 and $ph<=7)
		{
			$htmlRotulo .= "<td bgcolor='#ccffff' colspan='2' class='texto2' align='center' ><b><font color='green'>SEGURO</font></b></td>";
		}
		else
		{
			$htmlRotulo .= "<td bgcolor='#ccffff' colspan='2' class='texto2' align='center' ><b><font color='darkpink'>INSEGURO</font></b></td>";
		}

		$htmlRotulo .= "<tr><td  colspan='1' class='texto2' >PESO TEORICO (GRAMOS)</td>";
		$htmlRotulo .= "<td  colspan='1' class='texto2'  align='right'>".number_format($pesteo,2,',','.')."</td>";
		$htmlRotulo .= "<td colspan='2' class='texto2' align='center' >&nbsp;</td>";
		
		// $nombre[0] = '';
		// $nombre[1] = '';
		
		if( empty( $lote) || !isset($lote) ){
			$lote = '-';
		}
		else{
			$nombre = explode("-", $lote);	
		}
		
		if( !isset($revisado) || empty($revisado) ){
			$revisado = "&nbsp;";
		}
		
		
		$htmlRotulo .= "<tr>";
		$htmlRotulo .= "<td class='texto2'>Lote:</td>";
		$htmlRotulo .= "<td class='texto2'>".$lote."</td>";
		$htmlRotulo .= "<td class='texto2' colspan='2'>&nbsp;</td>";
		$htmlRotulo .= "</tr>";
		
		
		$altoimagen = "15px;";
		$anchoimagen = "50px;";
	
		$firmaElabora = "";
		if($usuarioElabora!="" && $usuarioElabora!="0")
		{
			$firmaElabora = "<img src='../../images/medical/hce/Firmas/".$usuarioElabora.".png' height='".$altoimagen."' width='".$anchoimagen."' border='0' />";
		}
		else
		{
			$elaborado = "";
		}
		
		$firmaRevisa = "";
		if($usuarioRevisa!="" && $usuarioRevisa!="0")
		{
			$firmaRevisa = "<img src='../../images/medical/hce/Firmas/".$usuarioRevisa.".png' height='".$altoimagen."' width='".$anchoimagen."' border='0' />";
		}
		else
		{
			$revisado = "";
		}
	
		
		$htmlRotulo .= "<tr>";
		$htmlRotulo .= "<td class='texto2'>Elaborado por: ".$elaborado."</td>";
		$htmlRotulo .= "<td class='texto2'>".$firmaElabora."</td>";
		$htmlRotulo .= "<td class='texto2' colspan='1'>Revisado por: ".$revisado."</td>";
		$htmlRotulo .= "<td class='texto2'>".$firmaRevisa."</td>";
		$htmlRotulo .= "</tr>";
		
		$htmlRotulo .= "<tr>";
		$htmlRotulo .= "<td class='texto2' colspan='4'>Conservar en nevera de 2&deg; a 8&deg; cent&iacute;grados</td>";
		$htmlRotulo .= "</tr>";
		
		//$htmlRotulo .= "<tr><td  colspan='4' ALIGN='CENTER' class='texto1'><input type='submit' name='enviar' value='ATRAS'></td>";
		$htmlRotulo .= "<tr><td  colspan='4' ALIGN='CENTER' class='texto1'><input type='button' name='enviar' value='ATRAS' onclick='seleccionarLote(0,true);'></td>";
	}
	$htmlRotulo .= "</form></table>";
	
	return $htmlRotulo;
	
}


//=======================================================================================================================================================
//		F I L T R O S  	 D E  	L L A M A D O S  	P O R  	J Q U E R Y  	O  	A J A X
//=======================================================================================================================================================
if(isset($accion)) 
{
	switch($accion)
	{
		case 'pintarRotulo':
		{	
			$data = pintarRotulo($wemp_pmla,$wbasedato,$historia,$codNPT,$lote,$ph,$horas,$medico,$nutricionista,$nombre,$servicio,$cama,$edad,$institucion,$quimico,$imprimir,$usuarioElabora,$usuarioRevisa,$elabora,$revisa,$fecha,$hora,$refrescar,$insti,$consulta);
			$data = utf8_encode($data);
			echo json_encode($data);
			break;
			return;
		}
	}
}
//=======================================================================================================================================================
//		F I N   F I L T R O S   A J A X 
//=======================================================================================================================================================	
else
{
	?>
	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>APLICACI&Oacute;N DE CENTRAL DE MEZCLAS</title>
	</head>
	
		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<script src="../../../include/root/print.js" type="text/javascript"></script>
		
		<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
		<link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>

	<script type="text/javascript">
    
	
	$.datepicker.regional['esp'] = {
		closeText: 'Cerrar',
		prevText: 'Antes',
		nextText: 'Despues',
		monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
		'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
		monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
		'Jul','Ago','Sep','Oct','Nov','Dic'],
		dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
		dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
		dayNamesMin: ['D','L','M','M','J','V','S'],
		weekHeader: 'Sem.',
		dateFormat: 'yy-mm-dd',
		yearSuffix: ''
	};

	$.datepicker.setDefaults($.datepicker.regional['esp']);	
	
	
	
	
	$(document).ready(function(){
	
		$("#loteRotulo option:last").prop("selected", "selected");
		seleccionarLote(0,false);
	});
	
	function enter()
    {
		document.rotulo2.imprimir.value=0;
    	document.rotulo2.submit();
    }
	
	function seleccionarLote(imprimir,refrescar)
	{
		if(imprimir=="1")
		{
			$("#divLotes").hide();
		}
		else
		{
			$("#divLotes").show();
		}
		
		var historia = $('#historia').val();
		if($('#historia').val()===undefined &&$('#historia').val()!="")
		{
			historia = $('#historiaNPT').val();
		}
		
		var lote = $("#loteRotulo").val();
		if(lote===undefined)
		{
			lote = $("#lote").val();
		}
		
		var ph = $("#ph").val();
		var horas = $("#horas").val();
		var medico = $("#medico").val();
		var nutricionista = $("#nutricionista").val();
		var nombre = $("#nombre").val();
		var servicio = $("#servicio").val();
		var cama = $("#cama").val();
		var edad = $("#edad").val();
		var institucion = $("#institucion").val();
		var quimico = $("#quimico").val();
		var usuarioElabora = $("#usuarioElabora").val();
		var usuarioRevisa = $("#usuarioRevisa").val();
		var elabora = $("#usuarioElabora option:selected").html();
		var revisa = $("#usuarioRevisa option:selected").html();
		var fecha = $("#fecha").val();
		var hora = $("#hora").val();
		
		// si no debe refrescar sino cargar nuevamente, se deben inicializar los valores
		if(!refrescar)
		{
			medico = "";
			nutricionista = "";
			usuarioElabora = "";
			usuarioRevisa = "";
			revisa = "";
			elabora = "";
			fecha = "";
			hora = "";
		}
		
		var data = {
						consultaAjax 	: '',
						accion			: 'pintarRotulo',
						wemp_pmla		: $('#wemp_pmla').val(),
						wbasedato		: $('#wbasedato').val(),
						historia		: historia,
						codNPT			: $('#codigo').val(),
						lote			: lote,
						ph				: ph,
						horas			: horas,
						medico			: medico,
						nutricionista	: nutricionista,
						nombre			: nombre,
						servicio		: servicio,
						cama			: cama,
						edad			: edad,
						institucion		: institucion,
						quimico			: quimico,
						imprimir		: imprimir,
						usuarioElabora	: usuarioElabora,
						usuarioRevisa	: usuarioRevisa,
						elabora			: elabora,
						revisa			: revisa,
						fecha			: fecha,
						hora			: hora,
						refrescar		: refrescar,
						insti			: $('#insti').val(),
						consulta		: $('#consulta').val(),
					}
		
		if( $( "[name=agua]" ).length > 0 ){
			data.agua = $( "[name=agua]" ).val();
		}
		
		$.post("rotulo2.php",
		
			data
		
		, function(data) {
			
			$("#divRotulo").html(data);
			
			if($('#consulta').val()!="on")
			{
				$("#fecha").datepicker({
			
					showOn: "button",
					buttonImage: "../../images/medical/root/calendar.gif",
					buttonText: "Seleccione la fecha de preparacion",
					dateFormat: 'yy-mm-dd',
					buttonImageOnly: true,
					changeMonth: true,
					changeYear: true
				});
				
				$('#hora').timepicker({
					showPeriodLabels: false,
					hourText: 'Hora',
					minuteText: 'Minuto',
					amPmText: ['AM', 'PM'],
					closeButtonText: 'Aceptar',
					nowButtonText: 'Ahora',
					deselectButtonText: 'Deseleccionar',
					defaultTime: 'now',
					onSelect : function(dato) {
						$('#hora').val(dato+":00")
					}
			   });
			}
			
		   
			if(imprimir==1 && $("#institucion").val()!=$('#wemp_pmla').val())
			{
				$("#mensajeLote").hide();
			}
			
		},'json');
	}
	
    </script>
	<style type="text/css">
        //body{background:white url(portal.gif) transparent center no-repeat scroll;}
        .titulo1{color:#FFFFFF;background:#006699;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
        .titulo2{color:#006699;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
        .titulo3{color:#003366;background:#ccffff;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
        .texto1{font-size:9pt;font-family:Tahoma; border-right: 1px solid #000000; border-top: 1px solid #000000;}
        .texto2{font-size:4.7pt;font-family:Tahoma; border-right: 1px solid #000000; border-top: 1px solid #000000;height:0.1em;}
        .texto3{font-size:9pt;}
        .texto4{font-size:4pt;}
        .texto5{font-size:6.5pt;font-family:Tahoma; border-right: 1px solid #000000; border-top: 1px solid #000000;height:0.1em;}
        
		body 
		{
			width:auto;
		}

   </style>
<body >
<?php
	
	
	echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='hidden' id='wbasedato' name='wbasedato' value='".$wbasedato."'>";
	echo "<input type='hidden' id='codigo' name='codigo' value='".$codigo."'>";
	echo "<input type='hidden' id='historiaNPT' name='historiaNPT' value='".$historia."'>";
	echo "<input type='hidden' id='insti' name='insti' value='".$insti."'>";
	echo "<input type='hidden' id='consulta' name='consulta' value='".$consulta."'>";
	
	
	pintarLotes($wbasedato,$codigo);

	
	echo "  <div id='divRotulo' style=''>
			</div>";
	
	?>
	</body>
</html>
	<?php
}
	
}
?>
