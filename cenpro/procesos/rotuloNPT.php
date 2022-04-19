<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA: Rotulo Nutriciones parenterales prescritas   
//=========================================================================================================================================\\
// DESCRIPCION: 		Rotulo para nutriciones parenterales prescritas desde ordenes
//AUTOR:				Jessica Madrid Mejía
//FECHA DE CREACION: 	2016-07-27
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES 
//--------------------------------------------------------------------------------------------------------------------------------------------
// 2022-04-12 		- Angela Zuluaga (Taito): Se adapta diseño a márgenes impresora Zebra.
// 2020-01-30 		- Se agrega al rotulo los insumos adicionales utilizados para la nutrición (Bolsa y equipos)
// 2019-09-19 		- Se agrega filtro de tipo de documento al hacer join entre root_000036 y root_000037 para evitar obtener información incorrecta.
// 					- Se agrega el código de barras con el código de la NPT
// 2018-02-15 		- Se agregan algunos campos a la impresión del rótulo y se permite seleccionar el rotulo a imprimir de acuerdo al lote
// 2017-09-18 		- Se agrega el filtro Dnuest='on' en la función consultarDetalleNPT();
// 2016-10-11 		- Se corrige consulta del ingreso ya que no debe ser el ultimo sino el ingreso con el que se realizó la NPT  	
// 2022-03-29       - Se retira la magen izquierda y se modifican estilos de la tabla de sticker. Se ocultan además las líneas de Bolsa y equipos para el sticker.	
//-------------------------------------------------------------------------------------------------------------------------------------------- \\
			$wactualiz='2022-03-29';
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
	header('Content-type: text/html;charset=ISO-8859-1');
	$user_session = explode('-',$_SESSION['user']);
	$wuse = $user_session[1];
	

	include_once("root/comun.php");
	

	$conex = obtenerConexionBD("matrix");
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez");
	$wfecha=date("Y-m-d");   
    $whora = date("H:i:s");

	$codigoNPT = $codigo;
	$codigoInstitucion = $insti;

//=====================================================================================================================================================================     
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================
	
	function consultarIngreso($wbasedato,$historia,$codigoNPT)
	{
		global $conex;
		
		$q= " SELECT Enuing
				FROM ".$wbasedato."_000214 
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
	
	function consultarNombrePaciente()
	{
		global $conex;
		global $wcenmez;
		global $codigoNPT;
		global $historia;
		global $wemp_pmla;
		
		if($historia!="")
		{
			$q = "SELECT Pacno1, Pacno2, Pacap1, Pacap2, Pacnac 
					FROM root_000037, root_000036
				   WHERE Orihis = '".$historia."'
				     AND Oriori = '".$wemp_pmla."'
					 AND Oriced = Pacced
					 AND Oritid = Pactid";

			$res=mysql_query($q,$conex);
			$row=mysql_fetch_array($res);

			$nombrePaciente = $row['Pacno1'].' '.$row['Pacno2'].' '.$row['Pacap1'].' '.$row['Pacap2'];
		}
		else
		{
			$q = "SELECT Pacnom, Pachis, Pacser, Paceda, Paccam, Pacmed, Pacnut 
					FROM ".$wcenmez."_000020
				   WHERE Pacpro = '".$codigoNPT."' ";

			$res=mysql_query($q,$conex);
			$num=mysql_num_rows($res);
			
			if($num>0)
			{
				$row = mysql_fetch_array($res);
				$nombre = $row['Pacnom'];
				$nombrePaciente = "<input type='text' class='texto1' name='nombre' value='".$nombre."' size='50'>";
				
			}
			else
			{
				$nombre = "ENTIDAD EXTERNA";
				$nombrePaciente = "<input type='text' class='texto1' name='nombre' value='".$nombre."' size='50'>";
			}
		}
		
		return $nombrePaciente;
	}
		
	function consultarNutricionista()
	{
		global $conex;
		global $wcenmez;
		global $codigoNPT;
		
		$q = "SELECT Pacnut 
				FROM ".$wcenmez."_000020
			   WHERE Pacpro = '".$codigoNPT."' ";

		$res=mysql_query($q,$conex);
		$num=mysql_num_rows($res);
		
		if($num>0)
		{
			$row = mysql_fetch_array($res);
			$nutricionista = $row['Pacnut'];
		}
		else
		{
			$nutricionista = "<input type='text' class='texto6' name='nutricionista' value='' size='25'>";
		}
		
		
		return $nutricionista;
	}
	
	function consultarInstituciones($wcenmez,$codigoNPT)
	{
		global $conex;
		
		$q =  " SELECT Subcodigo, Descripcion 
				  FROM ".$wcenmez."_000002 a,det_selecciones  b
				 WHERE Artcod='".$codigoNPT."'
				   AND Subcodigo=Artins
				   AND b.Medico = '".$wcenmez."'
				   AND Codigo = '04'
				   AND Activo = 'A';";

		$res=mysql_query($q,$conex);
		$row=mysql_fetch_array($res);
		
		if($row['Descripcion'] == "")
		{
			$institucion = "<input type='text' class='texto1' name='institucion' value='' size='50'>";
		}
		else
		{
			$institucion = $row['Descripcion'];
		}
		
		return $institucion;
	}
	
	function consultarServicio($ingreso)
	{
		global $conex;
		global $wbasedato;
		global $codigoInstitucion;
		global $historia;
		
		if($historia!="")
		{
			$q = "SELECT Habcco,Cconom 
					FROM ".$wbasedato."_000020,".$wbasedato."_000011 
				   WHERE Habhis='".$historia."' 
				     AND Habing='".$ingreso."'
					 AND Habcco=Ccocod;";

			$res=mysql_query($q,$conex);
			$row=mysql_fetch_array($res);

			// $servicio = $row['Habcco'].' - '.$row['Cconom'];
			$servicio = $row['Cconom'];
		}
		else
		{
			$servicio = "<input type='text' class='texto1' name='servicio' value='' size='50'>";
		}
		
		return $servicio;
	}
	
	function consultarEdad()
	{
		global $conex;
		
		global $historia;
		global $wemp_pmla;
		
		if($historia!="")
		{
			$q = "SELECT Pacnac 
					FROM root_000037,root_000036 
				   WHERE Orihis='".$historia."' 
				     AND Oriori='".$wemp_pmla."' 
					 AND Oriced=Pacced
					 AND Oritid = Pactid;";

			$res=mysql_query($q,$conex);
			$row=mysql_fetch_array($res);

			$fechaNacimiento = $row['Pacnac'];
			$fechaNac = explode("-",$fechaNacimiento);
			
			$dia=date('d');
			$mes=date('m');
			$ano=date('Y');

			
			//si el mes es el mismo pero el dia inferior aun no ha cumplido años, le quitaremos un año al actual
			if (($fechaNac[1] == $mes) && ($fechaNac[2] > $dia))
			{
				$ano=($ano-1);
			}

			//si el mes es superior al actual tampoco habra cumplido años, por eso le quitamos un año al actual

			if ($fechaNac[1] > $mes)
			{
				$ano=($ano-1);
			}

			//ya no habria mas condiciones, ahora simplemente restamos los años y mostramos el resultado como su edad
			$edad=($ano-$fechaNac[0]);
		}
		else
		{
			$edad = "<input type='text' class='texto1' name='edad' value='' size='5'>";
		}
		
		return $edad;
	}
	
	function consultarCama($ingreso)
	{
		global $conex;
		global $wbasedato;
		global $codigoInstitucion;
		global $historia;
		
		if($historia!="")
		{
			$q = "SELECT Habcpa
					FROM ".$wbasedato."_000020
				   WHERE Habhis='".$historia."' 
				     AND Habing='".$ingreso."';";

			$res=mysql_query($q,$conex);
			$row=mysql_fetch_array($res);

			$cama = $row['Habcpa'];
		}
		else
		{
			$cama = "<input type='text' class='texto1' name='cama' value='' size='10'>";
		}
		
		return $cama;
	}
	
	function consultarPesoyPurga($wcenmez,$codigoNPT,$historia)
	{
		global $conex;
		
		$q= " SELECT Artpes, Artpur
			    FROM ".$wcenmez."_000002 
			   WHERE Artcod = '".$codigoNPT."' 
			     AND Artest = 'on'
				 AND Arthis= '".$historia."'; ";

		$res = mysql_query($q,$conex);
		$num=mysql_num_rows($res);
		
		$pesoyPurga = array();
		if($num > 0)
		{
			$row=mysql_fetch_array($res);
			$pesoyPurga['peso']=$row['Artpes'];
			$pesoyPurga['purga']=$row['Artpur'];
		}
		else
		{
			$pesoyPurga['peso']="";
			$pesoyPurga['purga']="";
		}
		
		return $pesoyPurga;
	}
	
	
	function consultarEncabezadoNPT($wbasedato,$historia,$ingreso,$codigoNPT)
	{
		global $conex;
		
		$q= " SELECT Enuhis,Enuing,Enuart,Enuido,Enucnu,Enupes,Enutin,Enupur,Enuvol,SUBSTR(Seguridad, 3) AS Usuario,Descripcion   
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
			$arrayEncabezadoNPT['codUsuario'] = $row['Usuario'];
			$arrayEncabezadoNPT['nombreUsuario'] = $row['Descripcion'];
			
		}
			
		return $arrayEncabezadoNPT;
		
	}
	
	function consultarDetalleNPT($wbasedato,$wcenmez,$historia,$ingreso,$codigoNPT,$ido)
	{
		global $conex;
		
		$qCodNutricion = "	SELECT Arkcod 
							  FROM ".$wcenmez."_000001, ".$wcenmez."_000002 ,".$wbasedato."_000068
							 WHERE tippro = 'on' 
							   AND tipnco ='off' 
							   AND tipcdo !='on'
							   AND tipest = 'on'
							   AND arttip = Tipcod
							   AND artcod = Arkcod
							   AND tiptpr = arktip";

		$resCodNutricion = mysql_query($qCodNutricion,$conex);
		$numCodNutricion = mysql_num_rows($resCodNutricion);
		
		$cadenaNutriciones = "";
		while($rowCodNutricion = mysql_fetch_array($resCodNutricion))
		{
			$cadenaNutriciones .= "'".$rowCodNutricion['Arkcod']."',";
		}
		if($cadenaNutriciones!="")
		{
			$cadenaNutriciones = substr($cadenaNutriciones, 0, -1);
		}
		
		$q= " SELECT Dnucod,Dnupcm,Insord,Insfov,Insfop,Artcom,Tarcpo 
				FROM ".$wbasedato."_000215,".$wbasedato."_000210,".$wcenmez."_000002 ,".$wcenmez."_000018 
			   WHERE Dnuhis='".$historia."' 
				 AND Dnuing='".$ingreso."' 
			     AND Dnuart IN (".$cadenaNutriciones.") 
				 AND Dnuido='".$ido."'
				 AND Dnuest='on'
				 AND Inscod=Dnucod
				 AND Inscod=Dnucod
				 AND Artcod=Dnucod
				 AND Artest='on'
				 AND Tarcce=Dnucod
				 AND Tarest='on' 
				 AND Insest = 'on'
			ORDER BY Artord";
			
		$res = mysql_query($q,$conex);
		$num=mysql_num_rows($res);
		
		$arrayDetalleNPT = array();
		if($num > 0)
		{
			$cont=0;
			while($row = mysql_fetch_array($res))
			{
				$arrayDetalleNPT[$cont]['codigoInsumo'] = $row['Dnucod'];
				$arrayDetalleNPT[$cont]['prescripcionCM'] = $row['Dnupcm'];
				$arrayDetalleNPT[$cont]['orden'] = $row['Insord'];
				$arrayDetalleNPT[$cont]['formulaVolumen'] = strtoupper (str_replace(",",".",$row['Insfov']));
				$arrayDetalleNPT[$cont]['FormulaPurga'] = strtoupper (str_replace(",",".",$row['Insfop']));
				$arrayDetalleNPT[$cont]['NombreComercial'] =  strtoupper (utf8_encode($row['Artcom']));
				$arrayDetalleNPT[$cont]['codigoNoPos'] =  strtoupper ($row['Tarcpo']);
				
				$cont++;
			}
		}

		return $arrayDetalleNPT;

	}
	
	function consultarFormulas($wbasedato)
	{
		global $conex;
				
		$qFormulasNPT  = " SELECT Fortip,Forsti,Forcod,Fordes,Forfor,Formj1,Fortt1,Fortt2
							 FROM ".$wbasedato."_000213
							WHERE Forest='on'
						 ORDER BY Fortip,Forcod;";
		
		$resFormulasNPT = mysql_query($qFormulasNPT, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qNutriciones . " - " . mysql_error());
		
		$arrayFormulas=array();
		while($rowsFormulasNPT = mysql_fetch_array($resFormulasNPT))
		{
			$arrayFormulas[$rowsFormulasNPT['Fortip']][$rowsFormulasNPT['Forcod']]['subtipo'] = $rowsFormulasNPT['Forsti'];
			$arrayFormulas[$rowsFormulasNPT['Fortip']][$rowsFormulasNPT['Forcod']]['descripcion'] = $rowsFormulasNPT['Fordes'];
			$arrayFormulas[$rowsFormulasNPT['Fortip']][$rowsFormulasNPT['Forcod']]['formula'] = strtoupper (str_replace(",",".",$rowsFormulasNPT['Forfor']));
			$arrayFormulas[$rowsFormulasNPT['Fortip']][$rowsFormulasNPT['Forcod']]['mensaje1'] = str_replace(",",".",$rowsFormulasNPT['Formj1']);
			$arrayFormulas[$rowsFormulasNPT['Fortip']][$rowsFormulasNPT['Forcod']]['tooltip1'] = $rowsFormulasNPT['Fortt1'];
			$arrayFormulas[$rowsFormulasNPT['Fortip']][$rowsFormulasNPT['Forcod']]['tooltip2'] = $rowsFormulasNPT['Fortt2'];
		}
		
		return $arrayFormulas;
	}
	
	function consultarIdo($wbasedato,$historia,$codigoNPT)
	{
		global $conex;
		
		$q= " SELECT Enuart,Enuido
				FROM ".$wbasedato."_000214 
			   WHERE Enuhis='".$historia."' 
			      AND Enucnu='".$codigoNPT."' 
				 AND Enuest='on' 
				 AND Enurea='on';";

		$res = mysql_query($q,$conex);
		$num=mysql_num_rows($res);
		
		$ido = "";
		if($num > 0)
		{
			$row = mysql_fetch_array($res);
			$ido =  $row['Enuido'];
		}
		
		return $ido;
	}
	
	function pintarLotes()
	{
		global $conex;
		global $wcenmez;
		global $codigoNPT;
		
		$queryLotes = "SELECT Plocod 
						 FROM ".$wcenmez."_000004 
						WHERE Plopro='".$codigoNPT."'
					 ORDER BY Plocod;";
						
		$resLotes = mysql_query($queryLotes,$conex);
		$numLotes = mysql_num_rows($resLotes);
		
		$htmlLotes = "";
		if($numLotes > 0)
		{
			$htmlLotes .= "<div id='divLotes' style='position:absolute; left:20%;'>
								<table align='center'>
									<tr class='encabezadoTabla'>
										<td colspan='2'>Seleccione el lote del r&oacute;tulo a imprimir</td>
									</tr>
									<tr>
										<td class='fila1'>Lote:</td>
										<td class='fila2'>
											<select id='loteRotulo' onchange='seleccionarLote();'>";
												while($rowLotes = mysql_fetch_array($resLotes))
												{
			$htmlLotes .= "							<option value='".$rowLotes['Plocod']."'>".$rowLotes['Plocod']."</option>";	
												}
			$htmlLotes .= "					</select>
										</td>
									</tr>
								</table>
							</div><br><br><br>";
		}
		else
		{
			$htmlLotes = "<p align='center'><b>Debe crear un lote para poder imprimir el r&oacute;tulo.</b></p>";
		}		

		echo $htmlLotes;
	}
	
	function consultarMedicosTratantes($wbasedato,$historia,$ingreso,$fecha)
	{
		global $conex;
		
		$queryMedicoTratante = "SELECT Medtdo,Meddoc,CONCAT_WS(' ',Medno1, Medno2, Medap1, Medap2) AS MedicosTratantes
								  FROM ".$wbasedato."_000047, ".$wbasedato."_000048
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
		// $arrayMedicoTratante = array();
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
	
	function consultarDatosLote($wcenmez,$codigoNPT,$lote)
	{
		global $conex;
		
		$queryLote = "SELECT b.Fecha_data AS Fecha,b.Hora_data AS Hora,SUBSTR(a.Seguridad, 3) AS usuarioCrea,b.Ploela AS usuarioElabora,b.Plorev AS usuarioRevisa, c.Descripcion AS nombreCrea,d.Descripcion AS nombreElabora,e.Descripcion AS nombreRevisa,Arttin 
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
			$arrayLote['tiempoInfusion'] = floor($rowLote['Arttin']/60);
		}
		
		return $arrayLote;
	}
	
	function consultarInsumosAdicionales($wcenmez,$wbasedato,$wemp_pmla,$codigoNPT)
	{
		global $conex;
		
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
	
	function pintarFormatoRotulo($wemp_pmla,$wbasedato,$wcenmez,$historia,$codigoNPT,$lote)
	{
		global $wuse;
		
		
		$ingreso = consultarIngreso($wbasedato,$historia,$codigoNPT);
		$ido = consultarIdo($wbasedato,$historia,$codigoNPT);
		$pesoyPurga = consultarPesoyPurga($wcenmez,$codigoNPT,$historia);
		
		$datosEncabezado = consultarEncabezadoNPT($wbasedato,$historia,$ingreso,$codigoNPT);
		$datosDetalle = consultarDetalleNPT($wbasedato,$wcenmez,$historia,$ingreso,$codigoNPT,$ido);
		
		$arrayFormulas = consultarFormulas($wbasedato);
		
		$arrayLote = consultarDatosLote($wcenmez,$codigoNPT,$lote);
		$medicosTratantes = consultarMedicosTratantes($wbasedato,$historia,$ingreso,$arrayLote['fecha']);
		$horas = $arrayLote['tiempoInfusion'];
		
		$cama = consultarCama($ingreso);
		
		$insumosAdicionales = consultarInsumosAdicionales($wcenmez,$wbasedato,$wemp_pmla,$codigoNPT);
		
		$htmlRotulo = "";
		if(count($datosDetalle)>0)
		{
			if($historia!="")
			{
				$historiaPaciente = $historia;
			}
			else
			{
				$historiaPaciente = "<input type='text' class='texto1' name='historia' value='' size='10'>";
			}
			
			$codigoBarras = "<img src='../../../include/root/clsGenerarCodigoBarras.php?width=150&height=150&barcode=".$codigoNPT."' style='margin-left: 17px;'>";		
	
			
			$htmlRotulo .=  "	<div id='rotuloNPT'  class='areaimprimirRotulo'>
			
						<table id='tablaNutricionesNPT'   width='735px' style='margin-left: 25px ;border-bottom: 1px solid #000000;border-left: 1px solid #000000;' cellspacing='0' cellpading='0'  >
							<tr>
								<td colspan='6' class='texto1'><table cellspacing='0' cellpading='0'><tr><td align='center' width='90'><img src='/matrix/images/medical/root/clinica.jpg' height='35' width='75'></td>
								<td align='center' class='texto3' width='400'> <font color='#006699'>SERVICIO FARMACEUTICO </BR> CENTRAL DE MEZCLAS PARENTERALES</font></td>
								
								<td align='center' width='90' class='titulo1'> <font color='#006699'>Hab: ".$cama."<br>".$codigoNPT."</font></td>
								<td align='center' width='90'> &nbsp;&nbsp;&nbsp;</td>
								<td align='center' width='90'> <font size='6'  face='3 of 9 barcode'>".$codigoBarras."</font></td></tr></table></td>
								
							</tr>
							<tr>
								<td colspan='5' class='titulo1'>NOMBRE DEL PACIENTE: ".consultarNombrePaciente()."</td>
								<td colspan='1' class='texto1'>Lote: ".$lote."</td>
							</tr>
							<tr>
								<td  colspan='3' class='texto1'>INSTITUCI&Oacute;N: ".consultarInstituciones($wcenmez,$codigoNPT)."</td>
								<td  colspan='2' class='texto1'>SERVICIO: ".consultarServicio($ingreso)."</td>
								<td  colspan='1' class='texto1'><b>CAMA: ".$cama."</td>
							</tr>
							<tr>
								<td  colspan='2' class='texto1'>HISTORIA No: ".$historiaPaciente."</td>
								<td  colspan='1' class='texto1'>EDAD: ".consultarEdad()."</td>
								<td  colspan='1' class='texto1'>Peso Kg: ".$pesoyPurga['peso']."</td>
								<td  colspan='2' class='texto1'>Fecha y hora: ".$arrayLote['fecha']." ".$arrayLote['hora']."</td>
							</tr>
							<tr>
								<td  colspan='3' class='texto1'>Bomba BAXTER PURGA CON:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;20ml</td>
								<td  colspan='3' class='texto1'>Qu&iacute;mico farmac&eacute;utico: ".$arrayLote['nombreCrea']."</td>
							</tr>
							<tr>							
								<td  colspan='3' class='texto1'>Bomba ABBOTT PURGA CON:&nbsp;&nbsp;&nbsp;&nbsp;25ml</td>
								<td  colspan='3' class='texto1'>M&eacute;dico tratante: ".$medicosTratantes."</td>
							</tr>
							<tr>	
								<td  colspan='3' class='texto1'>Bomba 3M PURGA CON:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;30ml</td>
								<td  colspan='3' class='texto1'>Nutricionista Dietista: ".$datosEncabezado['nombreUsuario']."</td>
							</tr>
							<tr>
								<td  colspan='3' align='center' class='texto1'>MACRO Y MICRONUTRIENTES</td>
								<td  colspan='1' align='center'  class='texto1'>REQUERIMIENTO</td>
								<td  colspan='1' align='center'  class='texto1'>CALCULO DE VOLUMEN (ml)</td>
								<td  colspan='1' align='center'  class='texto1'><font color='dark pink'>CALCULO X PURGA ".$pesoyPurga['purga'].' ml'."</font>
								
								<input type='hidden' id='NPT_PESO' value='".$pesoyPurga['peso']."'>
								<input type='hidden' id='NPT_TIEMPOINFUSION' value='".$horas."'>
								<input type='hidden' id='NPT_PURGA' value='".$pesoyPurga['purga']."'>
								<input type='hidden' id='NPT_VT' value='".$datosEncabezado['volumen']."'>
								</td>
							</tr>
							
							
							";
							
							
							for($i=0;$i<count($datosDetalle);$i++)
							{
								$htmlRotulo .=  "<tr>";
								$htmlRotulo .=  "<td  colspan='3' class='texto1'>".$datosDetalle[$i]['codigoNoPos']." &nbsp; &nbsp;  ".$datosDetalle[$i]['NombreComercial']." </td>";
								$htmlRotulo .=  "<td  colspan='1' class='texto1' align='right'><span id='NPT_PRESCRIPCION".$datosDetalle[$i]['orden']."'>".$datosDetalle[$i]['prescripcionCM']." </span></td>";
								$htmlRotulo .=  "<td  colspan='1' class='texto1' align='right'><span id='NPT_V".$datosDetalle[$i]['orden']."'>0.00</span><input type='hidden' id='formulaVolumen".$datosDetalle[$i]['orden']."' value='".strtoupper ($datosDetalle[$i]['formulaVolumen'])."'></td>";
								$htmlRotulo .=  "<td  colspan='1' class='texto1' align='right'><span id='NPT_CP".$datosDetalle[$i]['orden']."'>0.00 </span><input type='hidden' id='formulaPurga".$datosDetalle[$i]['orden']."' value='".strtoupper ($datosDetalle[$i]['FormulaPurga'])."'></td>";
								$htmlRotulo .=  "</tr>";
							}
							
							// SUBTOTAL
							foreach($arrayFormulas as $tipoFormula => $codFormula)
							{
								if($tipoFormula=="SUBTOTAL")
								{
									$htmlRotulo .=  "<tr>
											<td  colspan='4' class='texto1'  bgcolor='#DDDDDD'>SUBTOTAL</></td>";
											foreach($codFormula as $keyFormula => $valueFormula)
											{
												if($valueFormula['subtipo']=="VOLUMEN")
												{
													$htmlRotulo .=  "<td  class='texto1'  bgcolor='#DDDDDD' align='right'><span id='NPT_STV'>0.00</span><input type='hidden' id='NPT_Formula_STV' value='".$valueFormula['formula']."'></td>";
												}
												elseif($valueFormula['subtipo']=="CORRECCION_PURGA")
												{
													$htmlRotulo .=  "<td  class='texto1'  bgcolor='#DDDDDD' align='right'><span id='NPT_STCP'>0.00</span><input type='hidden' id='NPT_Formula_STCP' value='".$valueFormula['formula']."'></td>";
												}
											}
									$htmlRotulo .=  "</tr>";
									break;
								}
							}
							
							// AGUA_ESTERIL
							foreach($arrayFormulas as $tipoFormula => $codFormula)
							{
								if($tipoFormula=="AGUA_ESTERIL")
								{
									$htmlRotulo .=  "<tr>
											<td  colspan='4' class='texto1'><font color='#006699'>AGUA</font></td>";
											foreach($codFormula as $keyFormula => $valueFormula)
											{
												if($valueFormula['subtipo']=="VOLUMEN")
												{
													$htmlRotulo .=  "<td colspan='1' class='texto1' align='right'><span id='NPT_AEV'>0.00</span><input type='hidden' id='NPT_Formula_AEV' value='".$valueFormula['formula']."'><input type='hidden' id='NPT_codigoAguaEsteril' value='".$codigoAguaEsteril."'></td>";
												}
												elseif($valueFormula['subtipo']=="CORRECCION_PURGA")
												{
													$htmlRotulo .=  "<td colspan='1' class='texto1' align='right'><span id='NPT_AECP'>0.00</span><input type='hidden' id='NPT_Formula_AECP' value='".$valueFormula['formula']."'></td>";
												}
											}
									$htmlRotulo .=  "</tr>";	
									break;
								}
							}
							
							// VOLUMEN TOTAL
							foreach($arrayFormulas as $tipoFormula => $codFormula)
							{
								if($tipoFormula=="VOLUMEN_TOTAL")
								{
									$htmlRotulo .=  "<tr>
											<td  colspan='4' class='texto1' ><b>VOLUMEN TOTAL (ml)</b></td>";
											foreach($codFormula as $keyFormula => $valueFormula)
											{
												if($valueFormula['subtipo']=="VOLUMEN")
												{
													$htmlRotulo .=  "<td colspan='1' class='texto1' align='right'><span id='NPT_VTV'>0.00</span><input type='hidden' id='NPT_Formula_VTV' value='".$valueFormula['formula']."'></td>";
												}
												elseif($valueFormula['subtipo']=="CORRECCION_PURGA")
												{
													$htmlRotulo .=  "<td colspan='1' class='texto1' align='right'><span id='NPT_VTCP'>0.00</span><input type='hidden' id='NPT_Formula_VTCP' value='".$valueFormula['formula']."'></td>";
												}
											}
									$htmlRotulo .=  "</tr>";
									break;				
								}
							}
							
							
							// VELOCIDAD DE INFUSIÓN
							foreach($arrayFormulas as $tipoFormula => $codFormula)
							{
								if($tipoFormula=="VELOCIDAD_INFUSION")
								{
									foreach($codFormula as $keyFormula => $valueFormula)
									{
										$htmlRotulo .=  "<tr>
												<td  colspan='3' class='texto1' ><font color='darkpink'><b>".$valueFormula['descripcion']."</b></font></td>
												<td  colspan='1' class='texto1' align='center'><font color='darkpink'><b>ml / hora</b></font></td>
												<td colspan='1'  class='texto1' align='right'><font color='darkpink'><b><span  id='NPT_VI' style='margin-left:30px;font-size:10px !important'>0.00</span></b></font><input type='hidden' id='NPT_Formula_VI' value='".$valueFormula['formula']."'></td>
												<td colspan='1' class='texto1' align='center' >Tiempo de infusi&oacute;n (h):&nbsp;".$horas."</td>
											</tr>";
										break 2;
									}
								}
							}
							
							// Bolsa y equipos
							//Se debe ocultar solo para el sticker, para la impresión general se debe mostrar 28-03-2022
							foreach($insumosAdicionales as $keyInsumo => $valueInsumo)
							{
								$htmlRotulo .=  "<tr class='carta'>
													<td  colspan='3' class='texto1' >".$valueInsumo['generico']."</td>
													<td  colspan='1' class='texto1' align='center' >".ucfirst(strtolower($valueInsumo['unidad']))."</td>
													<td  colspan='2' class='texto1' align='left'><span style='margin-left:160px'>".$valueInsumo['cantidad']."</span></td>
												</tr>";
							}
							
							// REQUERIMIENTO LÍQUIDOS
							foreach($arrayFormulas as $tipoFormula => $codFormula)
							{
								if($tipoFormula=="REQUERIMIENTOS_LIQUIDOS")
								{
									foreach($codFormula as $keyFormula => $valueFormula)
									{
										$imgInfo1 = "";	
										
										$htmlRotulo .=  "<tr>
											<td colspan='3' class='texto1' align='left'>".$valueFormula['descripcion']."</td>
											<td colspan='1' class='texto1' align='center'>m1/kg</td>
											<td colspan='2' class='texto1' align='left'><span id='NPT_RL' style='margin-left:160px'>0.00</span>".$imgInfo1."<input type='hidden' id='NPT_Formula_RL' value='".$valueFormula['formula']."'></td>
										</tr>";
										break 2;
									}
								}
							}
							
							
							// CLORO TOTAL
							foreach($arrayFormulas as $tipoFormula => $codFormula)
							{
								if($tipoFormula=="CLORO_TOTAL")
								{
									foreach($codFormula as $keyFormula => $valueFormula)
									{
										$imgInfo1 = "";	
										
										$htmlRotulo .=  "<tr>
												<td colspan='3' class='texto1' align='left'>".$valueFormula['descripcion']."</td>
												<td colspan='1' class='texto1' align='center'>mEq/Kg/D&iacute;a</td>
												<td colspan='2' class='texto1' align='left'><span id='NPT_CT' style='margin-left:160px'>0.00</span>".$imgInfo1."<input type='hidden' id='NPT_Formula_CT' value='".$valueFormula['formula']."'></td>
											</tr>";
											break 2;
									}
								}
							}
							
							// POTASIO TOTAL
							foreach($arrayFormulas as $tipoFormula => $codFormula)
							{
								if($tipoFormula=="POTASIO_TOTAL")
								{
									foreach($codFormula as $keyFormula => $valueFormula)
									{
										$imgInfo1 = "";	
										
										$htmlRotulo .=  "<tr>
												<td colspan='3' class='texto1' align='left'>".$valueFormula['descripcion']."</td>
												<td colspan='1' class='texto1' align='center'>mEq/Kg/D&iacute;a</td>
												<td colspan='2' class='texto1' align='left'><span id='NPT_PT' style='margin-left:160px'>0.00</span>".$imgInfo1."<input type='hidden' id='NPT_Formula_PT' value='".$valueFormula['formula']."'></td>
											</tr>";
											break 2;
									}
								}
							}
							
							// SODIO TOTAL
							foreach($arrayFormulas as $tipoFormula => $codFormula)
							{
								if($tipoFormula=="SODIO_TOTAL")
								{
									foreach($codFormula as $keyFormula => $valueFormula)
									{
										$imgInfo1 = "";	
										
										$htmlRotulo .=  "<tr>
												<td colspan='3' class='texto1' align='left'>".$valueFormula['descripcion']."</td>
												<td colspan='1' class='texto1' align='center'>mEq/Kg/D&iacute;a</td>
												<td colspan='2' class='texto1' align='left'><span id='NPT_ST' style='margin-left:160px'>0.00</span>".$imgInfo1."<input type='hidden' id='NPT_Formula_ST' value='".$valueFormula['formula']."'></td>
											</tr>";
											break 2;
									}
								}
							}
							
							// APORTE GRASAS VITALIPID INFANT+ APORTE DE GRASAS ORDENADAS
							foreach($arrayFormulas as $tipoFormula => $codFormula)
							{
								if($tipoFormula=="APORTE_GRASAS")
								{
									foreach($codFormula as $keyFormula => $valueFormula)
									{
										$imgInfo1 = "";	
										

										$htmlRotulo .=  "<tr>
												<td colspan='3' class='texto1' align='left'>".$valueFormula['descripcion']."</td>
												<td colspan='1' class='texto1' align='center'>g/Kg/D&Iacute;a</td>
												<td colspan='2' class='texto1' align='left'><span id='NPT_AG' style='margin-left:160px'>0.00</span>".$imgInfo1."<input type='hidden' id='NPT_Formula_AG' value='".$valueFormula['formula']."'></td>
											</tr>";
											break 2;
									}
								}
							}
							
							
							// PARÁMETROS NUTRICIONALES Y FARMACÉUTICOS		
							$htmlRotulo .=  "<tr>
									<td colspan='6'  align='center' class='texto1'  bgcolor='#DDDDDD'>PAR&Aacute;METROS NUTRICIONALES Y FARMAC&Eacute;UTICOS</td>
								</tr>";
								
							
								
							foreach($arrayFormulas as $tipoFormula => $codFormula)
							{	
								
									$fila_lista = "texto1";
								
									if($tipoFormula=="PARAMETROS_NUTRICIONALES_Y_FARMACEUTICOS")
									{
										foreach($codFormula as $keyFormula => $valueFormula)
										{
											$imgInfo1 = "";	
											
											
											$imgInfo2 = "";	
										
											
											$mensajeValidacion1 = "";	
											if($valueFormula['mensaje1']!="")
											{
												$mensajeValidacion1 = "<input type='hidden' id='NPT_FMF1_".$keyFormula."' value='".strtoupper ($valueFormula['mensaje1'])."'>";
											}
											
											$htmlRotulo .=  "<tr >
													<td class='".$fila_lista."' colspan=3 align='left' id='NPT_ParametroNutricional".$keyFormula."'>".$valueFormula['descripcion']."</td>
													<td class='".$fila_lista."' colspan=2 align='center'><span id='NPT_RF".$keyFormula."' >0.00</span>".$imgInfo1."<input type='hidden' id='NPT_F".$keyFormula."' value='".$valueFormula['formula']."'></td>
													<td class='".$fila_lista."' colspan=1 align='center'><b><span id='NPT_MF1_".$keyFormula."'></span></b>".$imgInfo2."".$mensajeValidacion1."</td>
												</tr>";
									}
								}
							}
							
							// VÍA DE ADMINISTRACIÓN
							foreach($arrayFormulas as $tipoFormula => $codFormula)
							{
								if($tipoFormula=="VIA_ADMINISTRACION")
								{
									$htmlRotulo .=  "<tr>
											<td colspan=2 class='fondoAmarillo texto1' align='left'>V&Iacute;A DE ADMINISTRACI&Oacute;N</td>";
											foreach($codFormula as $keyFormula => $valueFormula)
											{
												if($valueFormula['subtipo']=="CENTRAL_PERIFERICA_PEDIATRICOS")
												{
													$htmlRotulo .=  "<td class='fondoAmarillo texto1' colspan='2' align='center'><span id='NPT_MVAF1_".$keyFormula."'></span><input type='hidden' id='NPT_FMVAF1_".$keyFormula."' value='".strtoupper ($valueFormula['mensaje1'])."'></td>";
												}
												elseif($valueFormula['subtipo']=="CENTRAL_PERIFERICA_ADULTOS")
												{
													$htmlRotulo .=  "<td class='fondoAmarillo texto1' colspan='1' align='center'><span id='NPT_MVAF2_".$keyFormula."'></span><input type='hidden' id='NPT_FMVAF2_".$keyFormula."' value='".strtoupper ($valueFormula['mensaje1'])."'></td>";
												}
												elseif($valueFormula['subtipo']=="RIESGO")
												{
													$htmlRotulo .=  "<td class='texto1' colspan=1 style='background-color:#CDCDCD' align='center'><span id='NPT_MVAF3_".$keyFormula."'></span><input type='hidden' id='NPT_FMVAF3_".$keyFormula."' value='".strtoupper ($valueFormula['mensaje1'])."' indicadorSeguridad='on'></td>";
												}
											}
									$htmlRotulo .=  "</tr>";
									break;				
								}
							}
							
							$altoimagen = "25px;";
							$anchoimagen = "80px;";
							$htmlRotulo .=  "<tr>
									<td class='texto1' colspan='2'>Elaborado por: ".$arrayLote['nombreElabora']."</td>
									<td class='texto1' colspan='1'><img src='../../images/medical/hce/Firmas/".$arrayLote['usuarioElabora'].".png' height='".$altoimagen."' width='".$anchoimagen."' border='0' /></td>
									<td class='texto1' colspan='2'>Revisado por: ".$arrayLote['nombreRevisa']."</td>
									<td class='texto1'><img src='../../images/medical/hce/Firmas/".$arrayLote['usuarioRevisa'].".png' height='".$altoimagen."' width='".$anchoimagen."' border='0' /></td>
								</tr>";
							$htmlRotulo .=  "<tr>
									<td class='texto1' colspan='6'> Conservar en nevera de 2&deg; a 8&deg; cent&iacute;grados</td>
								</tr>";
								
			$htmlRotulo .= "	</table>
				
					</div>";

				$htmlRotulo .=   "<br><div><table id='tablaBotones' style='margin-left: 25px ' width='800px' align='center'>";
				if($historia!="")
				{
					$htmlRotulo .=   "	<tr align='center'>
								<td align='center' colspan='6'>
									<input type='button' class='printerSticker' onclick='boton_impSticker();' value='Imprimir Sticker'>
									<input type='button' value='Imprimir' onclick='boton_impNormal();' >
								</td>
							</tr>"	;
				}	
				else
				{
					$htmlRotulo .=   "	<tr align='center'>
								<td align='center'><input type='button' class='printerSticker' value='Imprimir Sticker' onclick='registrarPacienteExterno();'></td>
								<td align='center'><input type='button'  value='Imprimir'   onclick='boton_impNormal();'></td>
							</tr>"	;
				}	
				$htmlRotulo .=   "</table></div>";		
				
			
		}
		else
		{
			$htmlRotulo .=  "	<div>
						<table align='center' style='border-bottom: 1px solid #000000;border-left: 1px solid #000000;' cellspacing='0' cellpading='0'  >
							<tr>
								<td colspan='6' class='texto1'>No existen insumos creados para la nutricion</td>
							</tr>
						</table>
					</div>";
		}
		
		
		return $htmlRotulo;	
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
		case 'registrarPacienteExterno':
		{	
			// registrarPacienteExterno($month,$year,$clasereq,$centrocostos,$costos,"Todos");
			break;
			return;
		}
		case 'seleccionarLote':
		{	
			$data = pintarFormatoRotulo($wemp_pmla,$wbasedato,$wcenmez,$historia,$codNPT,$lote);
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


//=======================================================================================================================================================
//	I N I C I O   E J E C U C I O N   N O R M A L   D E L   P R O G R A M A
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
		<script src="../../../include/root/jquery.quicksearch.js" type="text/javascript"></script>
		<script src="../../../include/root/print.js" type="text/javascript"></script>
		
		<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
		<!-- <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script> -->
		
			
			
		<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
		<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>

	<script type="text/javascript">
	
//=====================================================================================================================================================================     
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T 
//=====================================================================================================================================================================
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
	
	// recalcularVolumenyPurgaModalNPT();
	// boton_imp()
	if($("#loteRotulo").length>0)
	{
		$("#loteRotulo option:last").prop("selected", "selected");
		seleccionarLote();
			
		$("#fecha").datepicker({
			
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
		}); 
	}
	
});
		
function boton_imp(){

	
	$(".printer").bind("click",function()
	{
		$( ".texto1" ).css({fontSize:"8pt"})
		$( ".texto6" ).css({fontSize:"7pt"})
		$( ".texto3" ).css({fontSize:"8pt"})
		
		$(".areaimprimirRotulo").printArea({			
			
			popClose: false,
			popTitle : 'RotuloNPT',
			popHt    : 500,
			popWd    : 1200,
			popX     : 200,
			popY     : 200,
			
		});
	});
}			
function boton_impNormal(){

	$("#divLotes").show();
	
	$( ".texto1" ).css({fontSize:"8pt"})
	$( ".texto6" ).css({fontSize:"7pt"})
	$( ".texto3" ).css({fontSize:"8pt"})
	
	$('#tablaNutricionesNPT').css({ 
		width:735,position: "absolute",
        left: 10,
		top: 60
	});
	$('#tablaBotones').css({ 
		width:800,position: "absolute",
        left: 25,
		top: $('#tablaNutricionesNPT').outerHeight()+100
	});
	
	$(".areaimprimirRotulo").printArea({			
			
		popClose: false,
		popTitle : 'RotuloNPT',
		popHt    : 500,
		popWd    : 1200,
		popX     : 200,
		popY     : 200,
		
	});
}		
			
function boton_impSticker(){

	$("#divLotes").hide();

	$( ".texto1" ).css({fontSize:"4.7pt"})
	$( ".texto6" ).css({fontSize:"4.7pt"})
	$( ".texto3" ).css({fontSize:"4pt"})
	$( ".carta" ).css({display:"none"})

	$('#tablaNutricionesNPT').css({ 
		width:390,position: "absolute",
        left: -15,
		top: 5
    });
	$('#tablaBotones').css({ 
		width:390,position: "absolute",
        //left: 178,
        top: $('#tablaNutricionesNPT').outerHeight()+10
    });
	
	
	$(".areaimprimirRotulo").printArea({			
			
			popClose: false,
			popTitle : 'RotuloNPT',
			popHt    : 500,
			popWd    : 1200,
			popX     : 200,
			popY     : 200,
			
		});
}		
			

function recalcularValoresNPT()
{
	var volumenTotal= $("#NPT_VT").val();
	volumenTotal = parseFloat(volumenTotal);
	$("#NPT_VT").val(volumenTotal.toFixed(2));
		
	
	$('table[id=tablaNutricionesNPT] input[id^=NPT_Formula_]').each(function(){
		var idFormula = $(this).attr('id');
		var formulasTotales = $(this).val();
	  
		idFormula = idFormula.split("_");
		idFormula = idFormula[2];
		
		var operadoresFormula = /[*/+-]/g;
		var elementosFormula = formulasTotales.split(operadoresFormula);
		
		for(var i=0;i<elementosFormula.length;i++)
		{
			
			elemFormula = elementosFormula[i];
			elemFormula = elemFormula.split(/[()]/g);
			
			for (var j=0;j<elemFormula.length;j++)
			{
				if(elemFormula[j]!="")
				{
					elemFormula[j] = $.trim(elemFormula[j]);
					// console.log(elemFormula[j]);
					// console.log("#NPT_"+elemFormula[j]);
					valorFormula = $("#NPT_"+elemFormula[j]).html();
					if(valorFormula!==undefined && valorFormula!==null && valorFormula!="")
					{
						formulasTotales = formulasTotales.replace(elemFormula[j],valorFormula);
						
					}
					else
					{
						valorFormula = $("#NPT_"+elemFormula[j]).val();
						if(valorFormula!==undefined && valorFormula!==null && valorFormula!="")
						{
							formulasTotales = formulasTotales.replace(elemFormula[j],valorFormula);
							
						}
						else
						{
							if(isNaN(parseFloat(elemFormula[j])))
							{
								formulasTotales = formulasTotales.replace(elemFormula[j],0);
							}
						}
					}
				}
			}
		}
		// console.log(formulasTotales);
		if(formulasTotales != "")
		{			
			try 
			{
				resultadoFormula= eval(formulasTotales);
			
				if(!isNaN(parseFloat(resultadoFormula)) && isFinite(parseFloat(resultadoFormula)))
				{
					resultadoFormula=resultadoFormula.toFixed(2);
					$("#NPT_"+idFormula).html(resultadoFormula);
				}
			}
			catch(err) {
				// jAlert("Error en la configuración de las formulas","ALERTA");
				alert("Error en la configuración de las formulas");
				// $("#btnGrabarNPT").attr('disabled', 'disabled');
			}
			
		}
	});
	
	// recalcularCorreccionPurga();
	
	// // ========================================================================
	// //					PARÁMETROS NUTRICIONALES Y FARMACÉUTICOS
	// // ========================================================================
	
	$('table[id=tablaNutricionesNPT] input[id^=NPT_F]').each(function(){
		formulaParamNutricYFarmace = $(this).val();
		idFormula= $(this).attr('id');
		
		
		idFormulasPNYF = idFormula.split("_");
		var idFijo = idFormulasPNYF[0]+"_"+idFormulasPNYF[1]+"_";
		
		if(idFijo != "NPT_Formula_" && idFijo != "NPT_FMF1_" && idFijo != "NPT_FMF2_" && idFijo != "NPT_FMVAF1_" && idFijo != "NPT_FMVAF2_" && idFijo != "NPT_FMVAF3_")
		{
			idFormula= $(this).attr('id');
			// console.log(idFormula);
			
			idFormula = idFormula.replace("NPT_F","");
		
			var operadoresFormula = /[*/+-]/g;
			var elementosFormula = formulaParamNutricYFarmace.split(operadoresFormula);
			
			// console.log(formulaParamNutricYFarmace);
			
			for(var i=0;i<elementosFormula.length;i++)
			{
				elementosFormula[i]=$.trim(elementosFormula[i]);
				elemFormula = elementosFormula[i];
				elemFormula = elemFormula.split(/[()]/g);
				
				for (var j=0;j<elemFormula.length;j++)
				{
					if(elemFormula[j]!="")
					{
						elemFormula[j] = $.trim(elemFormula[j]);
						// console.log(elemFormula[j]);
						valorFormula = $("#NPT_"+elemFormula[j]).html();
						if(valorFormula!==undefined && valorFormula!==null && valorFormula!="")
						{
							formulaParamNutricYFarmace = formulaParamNutricYFarmace.replace(elemFormula[j],valorFormula);
						}
						else
						{
							valorFormula = $("#NPT_"+elemFormula[j]).val();
							if(valorFormula!==undefined && valorFormula!==null && valorFormula!="")
							{
								formulaParamNutricYFarmace = formulaParamNutricYFarmace.replace(elemFormula[j],valorFormula);
							}
							else
							{
								if(isNaN(parseFloat(elemFormula[j])))
								{
									formulaParamNutricYFarmace = formulaParamNutricYFarmace.replace(elemFormula[j],0);
								}
							}
						}
						
					}
				}
				
			}
			// console.log(formulaParamNutricYFarmace);
				
			if(formulaParamNutricYFarmace != "")
			{
				// console.log(formulaParamNutricYFarmace);
				try 
				{
					resultadoFormula= eval(formulaParamNutricYFarmace);
				
					if(!isNaN(parseFloat(resultadoFormula)) && isFinite(parseFloat(resultadoFormula)))
					{
						resultadoFormula=resultadoFormula.toFixed(2);
						$("#NPT_RF"+idFormula).html(resultadoFormula);
					}
				}
				catch(err) {
					// jAlert("Error en la configuración de las formulas de parámetros nutricionales y farmacéuticos","ALERTA");
					alert("Error en la configuración de las formulas de parámetros nutricionales y farmacéuticos");
					// $("#btnGrabarNPT").attr('disabled', 'disabled');
				}
			}
		}
	});
	
	
	// ========================================================================
	//								Mensajes
	// ========================================================================
	$('table[id=tablaNutricionesNPT] input[id^=NPT_FMF]').each(function(){
		formulaMensaje = $(this).val();
		idFormula= $(this).attr('id');
		
		idMensaje = idFormula.replace("NPT_F","");
		
		var operadoresCondic = /[()]/g;
		var condiciones = formulaMensaje.split(operadoresCondic);
		
		// se organizan las condiciones y se asignan los valores
		for(var i=0;i<condiciones.length-1;i++)
		{
			if(condiciones[i]!="" && condiciones[i].charAt(0)!="{" && condiciones[i].charAt(condiciones[i].length-1)!="}")
			{
				condiciones[i]=$.trim(condiciones[i]);
				condicion=condiciones[i];
				// console.log(condicion)
				condicion=condicion.replace(/O/g,"||");
				condicion=condicion.replace(/Y/g,"&&");
				
				var operadoresCondicion = /[<>=|&]/g;
				var elementosCondicion = condicion.split(operadoresCondicion);
				
				for(var j=0;j<elementosCondicion.length;j++)
				{
					if(elementosCondicion[j]!="")	
					{
						elementosCondicion[j] = $.trim(elementosCondicion[j]);
						
						valorCondicion = $("#NPT_"+elementosCondicion[j]).html();
						if(valorCondicion!==undefined && valorCondicion!==null && valorCondicion!="")
						{
							condicion = condicion.replace(elementosCondicion[j],valorCondicion);
						}
						else
						{
							valorCondicion = $("#NPT_"+elementosCondicion[j]).val();
							if(valorCondicion!==undefined && valorCondicion!==null && valorCondicion!="")
							{
								condicion = condicion.replace(elementosCondicion[j],valorCondicion);
							}
							else
							{
								if(isNaN(parseFloat(elementosCondicion[j])))
								{
									condicion = condicion.replace(elementosCondicion[j],0);
								}
							}
						}
					}
				}
				// console.log(condicion)
				formulaMensaje=formulaMensaje.replace(condiciones[i],condicion);
			}
		}
		// console.log(formulaMensaje);
		// se agregan if y else segun el caso
		formulaMensaje=formulaMensaje.replace(/[(]/g,"if(");
		
		// formulaMensaje=formulaMensaje.replace(RegExp("if(if(","g"),"if((");
		formulaMensaje=formulaMensaje.replace("if(if(","if((");
		// formulaMensaje=formulaMensaje.replace(/[if(if(]/g,"if((");
		
		// formulaMensaje=formulaMensaje.replace(RegExp("|| if","g"),"|| ");
		formulaMensaje=formulaMensaje.replace("|| if","|| ");
		// formulaMensaje=formulaMensaje.replace(/[|| if]/g,"|| ");
		
		// formulaMensaje=formulaMensaje.replace(RegExp("&& if","g"),"&& ");
		formulaMensaje=formulaMensaje.replace("&& if","&& ");
		// formulaMensaje=formulaMensaje.replace(/[&& if]/g,"&& ");
		
		formulaMensaje=formulaMensaje.replace(/[;]/g,"}else{");
		// console.log(formulaMensaje);
		var operadoresMensaje = /[{}]/g;
		var mensajes = formulaMensaje.split(operadoresMensaje);

		// se organizan y reemplazan los mensajes
		var contMensajeSeguro=0;
		for(var j=0;j<mensajes.length-1;j++)
		{
			if(mensajes[j].charAt(mensajes[j].length-1)!=")" && mensajes[j]!= "else")
			{
				if(mensajes[j]!="")
				{
					mensajeCondicional = mensajes[j];
					
					var mensajeCondicionalCompleto="";
					if(contMensajeSeguro==0)
					{
						mensajeCondicionalCompleto += '$("#NPT_"+idMensaje).html("'+mensajeCondicional+'");';
						mensajeCondicionalCompleto += '$("#NPT_"+idMensaje).attr("mensaje","seguro");';
						mensajeCondicionalCompleto += '$("#NPT_"+idMensaje).css("color", "#4A8540");';
						$("#NPT_"+idMensaje).attr("bien",mensajeCondicional);
						formulaMensaje=formulaMensaje.replace(mensajes[j],mensajeCondicionalCompleto);
						contMensajeSeguro++;
					}
					else
					{
						mensajeCondicionalCompleto += '$("#NPT_"+idMensaje).html("'+mensajeCondicional+'");';
						mensajeCondicionalCompleto += '$("#NPT_"+idMensaje).attr("mensaje","inseguro");';
						mensajeCondicionalCompleto += '$("#NPT_"+idMensaje).css("color", "#C02C2C");';
						
						formulaMensaje=formulaMensaje.replace(RegExp(mensajes[j],"g"),mensajeCondicionalCompleto);
						break;
						
					}
				}
			}
		}
		// console.log(formulaMensaje);
		//Se evalua la condicion
		eval(formulaMensaje);
	
	});	
	
	
	// -------------------------------
	
	
	// //Via de administracion 
	
	 $('table[id=tablaNutricionesNPT] input[id^=NPT_FMVAF]').each(function(){
		formulaMensaje = $(this).val();
		idFormula= $(this).attr('id');
		
		indicadorSeguridad = $(this).attr('indicadorSeguridad');
		
		idMensaje = idFormula.replace("NPT_F","");
		
		var operadoresCondic = /[()]/g;
		var condiciones = formulaMensaje.split(operadoresCondic);
		
		// se organizan las condiciones y se asignan los valores
		for(var i=0;i<condiciones.length-1;i++)
		{
			if(condiciones[i]!="" && condiciones[i].charAt(0)!="{" && condiciones[i].charAt(condiciones[i].length-1)!="}")
			{
				condiciones[i]=$.trim(condiciones[i]);
				condicion=condiciones[i];
				
				condicion=condicion.replace(/O/g,"||");
				condicion=condicion.replace(/Y/g,"&&");
				
				var operadoresCondicion = /[<>=|&]/g;
				var elementosCondicion = condicion.split(operadoresCondicion);
				
				for(var j=0;j<elementosCondicion.length;j++)
				{
					if(elementosCondicion[j]!="")	
					{
						elementosCondicion[j] = $.trim(elementosCondicion[j]);
						
						valorCondicion = $("#NPT_"+elementosCondicion[j]).html();
						if(valorCondicion!==undefined && valorCondicion!==null && valorCondicion!="")
						{
							condicion = condicion.replace(elementosCondicion[j],valorCondicion);
						}
						else
						{
							valorCondicion = $("#NPT_"+elementosCondicion[j]).val();
							if(valorCondicion!==undefined && valorCondicion!==null && valorCondicion!="")
							{
								condicion = condicion.replace(elementosCondicion[j],valorCondicion);
							}
							else
							{
								if(isNaN(parseFloat(elementosCondicion[j])))
								{
									condicion = condicion.replace(elementosCondicion[j],0);
								}
							}
						}
					}
				}
				
				formulaMensaje=formulaMensaje.replace(condiciones[i],condicion);
			}
		}
		
		// se agregan if y else segun el caso
		formulaMensaje=formulaMensaje.replace(/[(]/g,"if(");
		formulaMensaje=formulaMensaje.replace(/[;]/g,"}else{");
		
		var operadoresMensaje = /[{}]/g;
		var mensajes = formulaMensaje.split(operadoresMensaje);

		// se organizan y reemplazan los mensajes
		var contMensajeSeguro=0;
		for(var j=0;j<mensajes.length-1;j++)
		{
			if(mensajes[j].charAt(mensajes[j].length-1)!=")" && mensajes[j]!= "else")
			{
				var mensajeCondicionalCompleto="";
				if(mensajes[j]!="")
				{
					mensajeCondicional = mensajes[j];
					
					if(contMensajeSeguro==0)
					{
						if(idFormula == "NPT_FMVAF3_33")
						{
							$("#NPT_"+idMensaje).attr("bien",mensajeCondicional);
							mensajeCondicionalCompleto += '$("#NPT_"+idMensaje).attr("mensaje","seguro");';
						}
						
						mensajeCondicionalCompleto += '$("#NPT_"+idMensaje).html("'+mensajeCondicional+'");';
						formulaMensaje=formulaMensaje.replace(mensajes[j],mensajeCondicionalCompleto);
						contMensajeSeguro++;
					}
					else
					{
						if(idFormula == "NPT_FMVAF3_33")
						{
							mensajeCondicionalCompleto += '$("#NPT_"+idMensaje).attr("mensaje","inseguro");';
						}
						mensajeCondicionalCompleto += '$("#NPT_"+idMensaje).html("'+mensajeCondicional+'");';
						formulaMensaje=formulaMensaje.replace(RegExp(mensajes[j],"g"),mensajeCondicionalCompleto);
					}
				}
				
			}
		}
		
		//Se evalua la condicion
		eval(formulaMensaje);
		
	});	
}

function recalcularVolumenyPurgaModalNPT()
{
	recalcularValoresNPT();
	$('table[id=tablaNutricionesNPT] span[id^=NPT_PRESCRIPCION]').each(function(){
		
		var idOrdenInsumo = $(this).attr('id');	
		idOrdenInsumo = idOrdenInsumo.replace("NPT_PRESCRIPCION","");
		
		var prescripcion = $(this).val();
		prescripcion = parseFloat(prescripcion);
		
		if(!isNaN(prescripcion))
		{
			$("#NPT_PRESCRIPCION"+idOrdenInsumo).val(prescripcion.toFixed(2));
		}
		else
		{
			$("#NPT_PRESCRIPCION"+idOrdenInsumo).val("0.00");
		}
				
		var formulaVolumen = $("#formulaVolumen"+idOrdenInsumo).val();
		
		var operadoresFormula = /[*/+-]/g;
		var elementosFormulaVolumen = formulaVolumen.split(operadoresFormula);
		
		
		// console.log(formulaVolumen);
		
		
		volumen=0;
		for(var i=0;i<elementosFormulaVolumen.length;i++)
		{
			elemFormulaVol = elementosFormulaVolumen[i];
			elemFormulaVol = elemFormulaVol.split(/[()]/g);  
			// console.log(elemFormulaVol);
			for (var j=0;j<elemFormulaVol.length;j++)
			{
				if(elemFormulaVol[j]!="")
				{
					elemFormulaVol[j] = $.trim(elemFormulaVol[j]);	

					var elementoFormula = elemFormulaVol[j];
					if(elemFormulaVol[j] == "PRESCRIPCION")
					{
						elementoFormula = elemFormulaVol[j]+idOrdenInsumo;	
					}
					
					
					// console.log(elemFormulaVol[j]);
					// valorFormula = $("#NPT_"+elemFormulaVol[j]).html();
					valorFormula = $("#NPT_"+elementoFormula).html();
					if(valorFormula!==undefined && valorFormula!==null && valorFormula!="")
					{
						formulaVolumen = formulaVolumen.replace(elemFormulaVol[j],valorFormula);
						
					}
					else
					{
						// valorFormula = $("#NPT_"+elemFormulaVol[j]).val();
						valorFormula = $("#NPT_"+elementoFormula).val();
						if(valorFormula!==undefined && valorFormula!==null && valorFormula!="")
						{
							formulaVolumen = formulaVolumen.replace(elemFormulaVol[j],valorFormula);
						}
						else
						{
							// if(isNaN(parseFloat(elemFormulaVol[j])))
							if(isNaN(parseFloat(elementoFormula)))
							{
								formulaVolumen = formulaVolumen.replace(elemFormulaVol[j],0);
							}
						}
					}
			  	}
			}
		}
		
		// console.log(formulaVolumen);
		if(formulaVolumen != "")
		{			
			try 
			{
				volumen= eval(formulaVolumen);
				if(!isNaN(parseFloat(volumen)) && isFinite(parseFloat(volumen)))
				{
					volumen=volumen.toFixed(2);
					$("#NPT_V"+idOrdenInsumo).html(volumen);
				}
			}
			catch(err) {
				// jAlert("Error en la configuración de las formulas del volumen","ALERTA");
				alert("Error en la configuración de las formulas del volumen","ALERTA");
				// $("#btnGrabarNPT").attr('disabled', 'disabled');
			}
		}
		
		
		var formulaCorreccionPurga = $("#formulaPurga"+idOrdenInsumo).val();
		
		var operadoresFormula = /[*/+-]/g;
		var elementosFormulaCorreccionPurga = formulaCorreccionPurga.split(operadoresFormula);
		
		
		// console.log(formulaCorreccionPurga);
		
		
		correccionPurga=0;
		for(var i=0;i<elementosFormulaCorreccionPurga.length;i++)
		{
			elemFormulaCorrPurga = elementosFormulaCorreccionPurga[i];
			elemFormulaCorrPurga = elemFormulaCorrPurga.split(/[()]/g);  
			// console.log(elemFormulaCorrPurga);
			for (var j=0;j<elemFormulaCorrPurga.length;j++)
			{
				if(elemFormulaCorrPurga[j]!="")
				{
					elemFormulaCorrPurga[j] = $.trim(elemFormulaCorrPurga[j]);

					var elementoFormula = elemFormulaCorrPurga[j];
					if(elemFormulaCorrPurga[j] == "V")
					{
						elementoFormula = elemFormulaCorrPurga[j]+idOrdenInsumo;	
					}
					
					// console.log(elemFormulaCorrPurga[j]);
					// valorFormula = $("#NPT_"+elemFormulaCorrPurga[j]).html();
					valorFormula = $("#NPT_"+elementoFormula).html();
					if(valorFormula!==undefined && valorFormula!==null && valorFormula!="")
					{
						formulaCorreccionPurga = formulaCorreccionPurga.replace(elemFormulaCorrPurga[j],valorFormula);
						
					}
					else
					{
						// valorFormula = $("#NPT_"+elemFormulaCorrPurga[j]).val();
						valorFormula = $("#NPT_"+elementoFormula).val();
						if(valorFormula!==undefined && valorFormula!==null && valorFormula!="")
						{
							formulaCorreccionPurga = formulaCorreccionPurga.replace(elemFormulaCorrPurga[j],valorFormula);
							
						}
						else
						{
								
							// if(isNaN(parseFloat(elemFormulaCorrPurga[j])))
							if(isNaN(parseFloat(elementoFormula)))
							{
								formulaCorreccionPurga = formulaCorreccionPurga.replace(elemFormulaCorrPurga[j],0);
							}
						}
					}
			  
				}
			}
		}
		
		// console.log(formulaCorreccionPurga);
		if(formulaCorreccionPurga != "")
		{			
			try 
			{
				correccionPurga= eval(formulaCorreccionPurga);
				if(!isNaN(parseFloat(correccionPurga)) && isFinite(parseFloat(correccionPurga)))
				{
					correccionPurga=correccionPurga.toFixed(2);
					$("#NPT_CP"+idOrdenInsumo).html(correccionPurga);
				}
			}
			catch(err) {
				// jAlert("Error en la configuración de las formulas de la corrección purga","ALERTA");
				alert("Error en la configuración de las formulas de la corrección purga");
				// $("#btnGrabarNPT").attr('disabled', 'disabled');
			}
		}
	});
	
	recalcularValoresNPT();
}

function registrarPacienteExterno()
{
	$.post("../procesos/rotuloNPT.php",
	{
		consultaAjax 	: '',
		accion			: 'registrarPacienteExterno',
		wemp_pmla		: $('#wemp_pmla').val(),
		wbasedato		: $('#wbasedato').val(),
		wcco			: cco
	}
	, function(data) {
		
		
	},'json');
}

function seleccionarLote()
{
	$.post("rotuloNPT.php",
	{
		consultaAjax 	: '',
		accion			: 'seleccionarLote',
		wemp_pmla		: $('#wemp_pmla').val(),
		wbasedato		: $('#wbasedato').val(),
		wcenmez			: $('#wcenmez').val(),
		historia		: $('#historia').val(),
		codNPT			: $('#codigoNPT').val(),
		lote			: $("#loteRotulo").val()
	}
	, function(data) {
		
		$("#divRotulo").html(data);
		recalcularVolumenyPurgaModalNPT();
		boton_imp();
	},'json');
}
	
	
//=======================================================================================================================================================	
//	F I N  F U N C I O N E S  J A V A S C R I P T 
//=======================================================================================================================================================	
	</script>
	

	
	
<!--=====================================================================================================================================================================     
	E S T I L O S 
=====================================================================================================================================================================-->
	<style type="text/css">
		#tooltip{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-Index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:4px;opacity:1;}
		#tooltip h6, #tooltip div{margin:0; width:auto}
		.Titulo_azul{
			color:#000066; 
			font-weight: bold; 
			font-family: verdana;
			font-size: 11pt;
		}
		.borderDiv2{
			border: 2px solid #2A5DB0;
			padding: 15px;
		}
		.borderDiv{
			border: 1px solid #e0e0e0;
			padding: 5px;
		}
		.Titulo_azul{
			color:#000066; 
			font-weight: bold; 
			font-family: verdana;
			font-size: 11pt;
		}
		.parrafo_text{
		background-color: #666666;
		color: #FFFFFF;
		font-family: verdana;
		font-size: 10pt;
		font-weight: bold;
		}
		.fila_detalle{
		background-color: #C1DEF7;
		font-family: verdana;
		font-size: 10pt;
		}
		.titulo1{color:#FFFFFF;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
        .titulo2{color:#006699;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
        .titulo3{color:#003366;background:#ccffff;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
        .texto1{font-size:9pt;font-family:Arial; border-right: 1px solid #000000; border-top: 1px solid #000000;}
        .texto2{font-size:4.7pt;font-family:Tahoma; border-right: 1px solid #000000; border-top: 1px solid #000000;height:0.1em;}
        .texto3{font-size:8pt;}
        .texto4{font-size:4pt;}
        .texto5{font-size:6.5pt;font-family:Tahoma; border-right: 1px solid #000000; border-top: 1px solid #000000;height:0.1em;}
		.texto6{font-size:7pt;}
		.textoImprimir{font-size:18pt;}
	</style>
<!--=====================================================================================================================================================================     
	F I N   E S T I L O S 
=====================================================================================================================================================================-->



<!--=====================================================================================================================================================================     
	I N I C I O   B O D Y
=====================================================================================================================================================================-->	
	<body>
	<?php
	// -->	ENCABEZADO
	
		echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
		echo "<input type='hidden' id='wbasedato' name='wbasedato' value='".$wbasedato."'>";
		echo "<input type='hidden' id='wcenmez' name='wcenmez' value='".$wcenmez."'>";
		echo "<input type='hidden' id='codigoNPT' name='codigoNPT' value='".$codigoNPT."'>";
		echo "<input type='hidden' id='historia' name='historia' value='".$historia."'>";
		// echo "<input type='hidden' id='horas' name='horas' value='".$horas."'>";
	
	
		if($loteRotulo!="")
		{
			echo "<input type='hidden' id='loteRotulo' name='loteRotulo' value='".$loteRotulo."'>";
			echo "<script>seleccionarLote();</script>";
		}
		else
		{
			pintarLotes();
		}
	
	//Mensaje de espera		
	echo "  <center>
				<div id='msjEspere' style='display:none;'><br>
					<img src='../../images/medical/ajax-loader5.gif'/>
					<br><br> Por favor espere un momento ... <br><br>
				</div>
			</center>";
			
	echo "  <div id='divRotulo' style=''>
			</div>";
	
	
	?>
	</body>
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
