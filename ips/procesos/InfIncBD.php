<html>
<head>
	<title>Informe de Inconsistencias En La Base de Datos De la Eps Responsable del pago</title>
		
	<!-- UTF-8 is the recommended encoding for your pages -->
    <meta http-equiv="content-type" content="text/xml; charset=utf-8" />
    <title>Zapatec DHTML Calendar</title>

	<!-- Loading Theme file(s) -->
	    <link rel="stylesheet" href="../../zpcal/themes/fancyblue.css" />

	<!-- Loading Calendar JavaScript files -->
	    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
	    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
	    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>

	<!-- Loading language definition file -->
	    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>
</head>
	<script type="text/javascript">
		function enter()
		{		
			document.forms.forma.submit();
		}
	</script>
	
<body>
	<?php
include_once("conex.php");
	/***********************************************************************************************************************************
	***    																															 ***				    
	***	 	Informe De Posibles Inconsistencias En La Base de Datos de La Entidad Responsable del Pago				     			 ***	
	***																																 ***
	************************************************************************************************************************************/			
	//==================================================================================================================================
	//PROGRAMA						:InfIncBD.php 
	//AUTOR							:Juan Esteban Lopez Aguirre
	//FECHA CREACION				:Julio 21 de 2009
	//FECHA ULTIMA ACTUALIZACION 	:
	//DESCRIPCION					: 
	//								 
	//MODIFICACIONES:
	//===================================================================================================================================
	
	///////////////////////////////////////////////////////////////////////////////
	/////					Declaracion De Variables                          /////
	///////////////////////////////////////////////////////////////////////////////
	

	

	$fecha = date("Y-m-d");					//Se guarda la fecha en que se realizo el registro
	$hora = date("H:i");					//Se guarda la hora en que se realizo el registro
	$key = substr($user,2,strlen($user));
	$empresa = 'clisur';				  	//Se guarda la empresa
	
	if(!isset($wIfQuery))
	{
		$wIfQuery='0';		
	}


echo"<form name='forma' action='InfIncBD.php' method='post'>";
{
	///////////////////////////////////////////////////////////////////////////////
	///////		            Validacion de datos                             ///////
	///////////////////////////////////////////////////////////////////////////////	
	if(isset($rbtnGrabar) and $rbtnGrabar== '1')
	{

			if ((isset($rbtnTipInc) and $rbtnTipInc == '1') or (isset($rbtnTipInc) and $rbtnTipInc =='2'))
			{
				
				$qInsMaest = "INSERT INTO ".$empresa."_000138 (Fecha_data,Hora_data,maitip,mainum,maifec,maihrc,mainig,mainip,maihis,maiing,maigen,
															   seguridad,medico)"
							."	   VALUES('".$fecha."','".$hora."','01','".$wnumInf."','".$fecha."','".$hora."','".$wNitEmp."','".$wCodemp."',										   						".$wtxtHis.", '".$wtxting."','".$key."','C-".$key."','".$empresa."')";
							
				$res = mysql_query($qInsMaest,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qInsMaest." - ".mysql_error());
				
				//$qLock = "UNLOCK TABLE";	     
				//$res2 = mysql_query($qLock,$conex);
				
				//$qLock = "lock table ".$empresa."_000135 LOW_PRIORITY WRITE";//Bloque la tabla para realizar otros accesos
				//$res2 = mysql_query($qLock,$conex);								 //Ejecuta la instruccion de bloqueo de tabla
				
				if(isset($chkFlas) and $chkFlas =='1')
					$tError='SI';
				else 
					$tError='NO';
				if(isset($chkSlas) and $chkSlas =='1')
					$tError1='SI';
				else 
					$tError1='NO';				
				if(isset($chkFname) and $chkFname =='1')
					$tError2='SI';
				else 
					$tError2='NO';				
				if(isset($chkSname) and $chkSname ==1)
					$tError3='SI';
				else 
					$tError3='NO';				
				if(isset($chkTipDoc) and $chkTipDoc =='1')
					$tError4='SI';
				else 
					$tError4='NO';				
				if(isset($chkNumDoc) and $chkNumDoc =='1')
					$tError5='SI';
				else 
					$tError5='NO';			
				if(isset($chkFecNac) and $chkFecNac =='1')
					$tError6='SI';
				else 
					$tError6='NO';
				
					/*chkTipDoc' value='1'><b>Tipo Documento de Identificacion<br>";
					echo"<input type='checkbox' name='chkNumDoc' value='1'><b>Numero Documento de Identificacion<br>";
					echo"<input type='checkbox' name='chkFecNac'*/				
				$qInsert = "INSERT INTO ".$empresa."_000135 (incInf,inctdo,incndo,incfen,incno1,incno2,incap1,incap2,incte1,incte2,incte3,incte4
															,incte5,incte6,incte7,inctpi,incObs,Fecha_data,Hora_data,seguridad,medico) "	
						  ."     VALUES ('".$wnumInf."','".$wtDoc."','".$wtxtDoc."','".$wtxtFeNac."', '".$wtxtFnam."','".$wtxtSnam."',
						  				 '".$wtxtFlas."','".$wtxtSlas."','".$tError."','".$tError1."','".$tError2."','".$tError3."',
										   '".$tError4."','".$tError5."','".$tError6."','".$rbtnTipInc."','".$txtObs."','".$fecha."','".$hora."',
										   'C-".$key."','".$empresa."')";
										  			  
				$res = mysql_query($qInsert,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qInsert." - ".mysql_error());

			}
			else
			{
				?>
					<script>
						alert("Debe Diligenciar el campo Tipo de Incosistencia")		
					</script>
				<?php
				
				$error3 ='1';
				$rbtnGenerar = '1';	
				$txtHis = $wtxtHis;
				$txtIng = $wtxting; 
				$numInf = $wnumInf;
				$txtFeNac = $wtxtFeNac;
				$tDoc = $wtDoc;
				$txtDoc = $wtxtDoc;
				$txtFnam = $wtxtFnam;
				$txtSnam = $wtxtSnam;
				$txtFlas = $wtxtFlas;
				$txtSlas = $wtxtSlas;
				$tDoc = $wTipDoc;
				unset($rbtnGenerar);			
				
			}
	

	}//FIN if($rbtnGrabar== '1')
	
	if((!isset($error3)) and (isset($rbtnGenerar) and $rbtnGenerar == '1') and (trim($txtHis) == '' or trim($txtIng) == '' or isset($TipDoc[0]) or trim($txtDoc)== '' or trim($txtFeNac) == '' or trim($txtFnam) == '' or trim($txtFlas) == ''))//
	{	
		?>
			<script>
				alert("Debe Diligenciar Todos Los Campos del Formulario")		
			</script>
		<?php
		$error ='1';
		unset($rbtnGenerar);			
	}// FIN ----- if($btnGenerar == '1' and (trim($txtHis) == '' or trim($txtIng) == ''))-----
	
	
	
	if(((isset($rbtnGenerar) and $rbtnGenerar == '1') or (isset($wIfQuery) and $wIfQuery == '1')) and (!isset($error)))//and $error!='1'
	{
		// Se Trae la Informacion del paciente que se va a reportar que previamente ya se le hizo un ingreso
	
		$qPacInf = "SELECT CS100.pachis, CS100.pactdo, CS100.Pacdoc,CS100.pacap1, CS100.pacap2, CS100.pacno1, CS100.pacno2, CS100.pacfna,
						   CS100.pacdir, CS100.pactel,CS100.pacdep, CS24.Empnom ,CS100.paciu, CS100.Pactus, CS101.Ingent, CS24.Empcmi,
						   CS101.Ingfei,CS101.Inghin,CS101.Ingcem "
		   		  ."  FROM ".$empresa."_000100 CS100, ".$empresa."_000024 CS24, ".$empresa."_000101 CS101 "
		   		  ." WHERE CS100.pachis = '".$txtHis."'"		   
		   		  ."   AND CS100.pachis = CS101.Inghis "  
		   		  ."   AND CS101.ingnin = '".$txtIng."'"
		  	      ."   AND CS101.Ingcem = CS24.Empcod";
	
			list($resPacInf,$numPacInf) = Consulta($qPacInf,$conex);
	
			$rowSelUsu = mysql_fetch_row($resPacInf);
		
		if($numPacInf =='0')
		{
			?>
				<script>
					alert("Los Datos de la Historia estan errados")		
				</script>
			<?php
			$error2='1';
			unset($rbtnGenerar);
			//$rbtnGenerar = '0';					
		}	
	}// FIN ------ if($btnGenerar == '1' and $error!='1') -----	
	if(isset($rbtnConsultar) and $rbtnConsultar == '1')
	{
		if(trim($txtIng) != '')
		{
			$qConsulta = "SELECT CS138.maitip, CS138.mainum, CS138.maiFec, CS138.maihrc, CS138.mainig, CS138.mainip, CS138.maigen,CS138.maihis, CS138.maiing"
					."		FROM ".$empresa."_000138 CS138"
					." 	   WHERE CS138.maihis = '".$txtHis."'"
					."   	 AND CS138.maiing = '".$txtIng."'"
					."  ORDER BY Fecha_data";
		}
		else
		{
			$qConsulta = "SELECT CS138.maitip, CS138.mainum, CS138.maiFec, CS138.maihrc, CS138.mainig, CS138.mainip, CS138.maigen,CS138.maihis, CS138.maiing"
					."		FROM ".$empresa."_000138 CS138"
					." 	   WHERE CS138.maihis = '".$txtHis."'"
					."  ORDER BY Fecha_data";
		}
		
		list($resConsulta,$numConsulta) = Consulta($qConsulta,$conex);
		
		$rowConsulta = mysql_fetch_row($resConsulta);
		
		if(isset($numConsulta) and $numConsulta == '0')		
		{
			?>
				<script>
					alert("La Historia No tiene Informes Asociados")
				</script>
				
			<?php
		}

		
		
	}// FIN ------ if($rbtnConsultar == '1') -----
		
	///////		            Fin de la Validacion de datos                             ///////
		

	///////////////////////////////////////////////////////////////////////////////
	/////		Se pinta el Front End Antes de Generar el Formato 			  /////
	///////////////////////////////////////////////////////////////////////////////
//	(isset($rbtnGenerar) or $rbtnGenerar == '')
	if((isset($rbtnConsultar) and $rbtnConsultar=='1') and (isset($wIfQuery) and $wIfQuery == '1') or (!isset($rbtnGenerar)))
	{
		//Para realizar la selecccion del tipo de documento de acuerdo al sistema
		$qTipDoc = "SELECT CS105.Selcod, CS105.Seldes "
				."    FROM ".$empresa."_000105 CS105"
				." 	 WHERE CS105.Seltip = '01'"				
				."     AND CS105.Selest = 'on'"
				." ORDER BY CS105.Selcod";
		
		list($resTipDoc,$numTipDoc) = Consulta($qTipDoc,$conex);// Funcion Para ejecutar los Query 
		
		
	echo"<table align='center' border='1' width='800'>";
	
		echo"<tr>";
			echo"<td colspan='1'><img src='/matrix/images/medical/pos/logo_".$empresa.".png' width='200' height='70' vspace=15 hspace=15></td>";
			echo"<td align='center' bgcolor='#227CE8' colspan='4'><font size='5' color='white'>Informe de Posibles Inconsistencias en la Base de
			Datos de la Entidad Responsable del Pago</font></td>";
		echo"</tr>";
		echo"<tr>";
			echo"<td align='center' colspan='5'><b>Tipo </b><select name='ddTipDoc' style='width:200px'>";
				echo"<option Selected>----</option>";
					for($i=1;$i<=$numTipDoc;$i++)
					{
						$rowSelectTipo = mysql_fetch_row($resTipDoc);
						if(isset($ddTipDoc) and $ddTipDoc == $rowSelectTipo[0]."-".$rowSelectTipo[1])
						{
							echo"<option selected>".$rowSelectTipo[0]."-".$rowSelectTipo[1]."</option>";
						}
						else
						{
							echo"<option>".$rowSelectTipo[0]."-".$rowSelectTipo[1]."</option>";
						}						
					}	
				echo"</select>";
				echo"<b> Documento </b>";
				if(!isset($txtDoc))//and $txtDoc == ''
					echo"<input type='text' name='txtDoc' size='9'></td>";
				else
					echo"<input type='text' name='txtDoc' size='9' value='".$txtDoc."'></td>";
				
				if(!isset($txtFeNac))//and $txtFeNac == ''
				{
					echo"<tr><td rowspan='2' colspan='2' align='center'><b>Fecha de Nacimiento<input type='text' name='txtFeNac' size='12' 
					READONLY><input type='button' name='btnFecNac' value='...'></td>";
					?>
						<script type="text/javascript">//<![CDATA[
						Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'txtFeNac',button:							   'btnFecNac',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
						//]]></script>
					<?php
				}
				else
				{
					echo"<tr><td rowspan='2' colspan='2' align='center'><b>Fecha de Nacimiento<input type='text' name='txtFeNac' size='12' 
					READONLY value='".$txtFeNac."'><input type='button' name='btnFecNac' value='...'></td>";
					?>
						<script type="text/javascript">//<![CDATA[
						Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'txtFeNac',button:							   'btnFecNac',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
						//]]></script>
					<?php
				}
								
				
		echo"</tr>";
			if((isset($txtHis) and trim($txtHis)=='') and (isset($txtIng) and trim($txtIng) == '') or (isset($error2) and $error2 == '1'))//and ($txtHis) == ''
			{
				echo"<td align='center' colspan='4'><b>Historia: </b><input type='text' name='txtHis' size='8'><b>Ingreso </b>
				<input type='text' name='txtIng' size='2'></td>";
				
				if(trim($txtHis) == '' and trim($txtIng) != '' )
				{				
					echo"<td align='center' colspan='4'><b>Historia: </b><input type='text' name='txtHis' size='8'><b>Ingreso </b>
					<input type='text' name='txtIng' size='2' value='".$txtIng."'></td>";
				}
				else
				{
					if(trim($txtHis) != '' and trim($txtIng) == '' )
					echo"<td align='center' colspan='4'><b>Historia: </b><input type='text' name='txtHis' size='8'  value='".$txtHis."'><b>Ingreso </b>
					<input type='text' name='txtIng' size='2''></td>";
					
				}
			}
			else
			{
				if (!isset($txtHis) or !isset($txtIng))
					echo"<td align='center' colspan='4'><b>Historia: </b><input type='text' name='txtHis' size='8'><b>Ingreso </b>
					<input type='text' name='txtIng' size='2'></td>";
				else				
				echo"<td align='center' colspan='4'><b>Historia: </b><input type='text' name='txtHis' size='8' value='".$txtHis."'><b>Ingreso </b>
					<input type='text' name='txtIng' size='2' value='".$txtIng."'></td>";//value='".$txtHis."'  value='".$txtIng."'
			}				
		echo"</tr>";
		echo"<tr>";
		if(isset($txtFlas) and trim($txtFlas)!='')		
			echo"<td align='center'><b>1<font size='-2'>er</font> Apellido </b><input type='text' name='txtFlas' size='9' value='".$txtFlas."'></td>";
		else
			echo"<td align='center'><b>1<font size='-2'>er</font> Apellido </b><input type='text' name='txtFlas' size='9'></td>";
		if(isset($txtSlas) and trim($txtSlas)!='')
			echo"<td align='center'><b>2<font size='-2'>do</font> Apellido</b><input type='text' name='txtSlas' size='9' value='".$txtSlas."'></td>";
		else
			echo"<td align='center'><b>2<font size='-2'>do</font> Apellido</b><input type='text' name='txtSlas' size='9'></td>";
		if(isset($txtFnam) and trim($txtFnam)!='')
			echo"<td align='center'><b>1<font size='-2'>er</font>Nombre </b><input type='text' name='txtFnam' size='9' value='".$txtFnam."'></td>";
		else
			echo"<td align='center'><b>1<font size='-2'>er</font>Nombre </b><input type='text' name='txtFnam' size='9'></td>";
		if(isset($txtSnam) and trim($txtSnam)!='')
			echo"<td align='center'><b>2<font size='-2'>do</font> Nombre </b><input type='text' name='txtSnam' size='9' value='".$txtSnam."'></td>";
		else
			echo"<td align='center'><b>2<font size='-2'>do</font> Nombre </b><input type='text' name='txtSnam' size='9'></td>";
					
		echo"</tr>";
		echo"<tr>";
				echo"<td colspan='5' align='center'><input type='radio' name='rbtnGenerar' value='1' onclick='enter()'>Generar 
				<input type='radio' name='rbtnConsultar' value='1' onclick='enter()'>Consultar</td>";
		echo"</tr>";
	echo"</table>";
		
		echo"<br> &nbsp";
		if((isset($rbtnConsultar) and $rbtnConsultar =='1') and (isset($numConsulta) and $numConsulta >='1'))
		{
		echo"<table border='1' align='center' width='800'>";
				echo"<tr>";
					echo"<th>fecha Informe<th>Hora Informe<th>Numero De Informe<th> Tipo de Informe<th></th>";
				echo"</tr>";
				for($i=0;$i < $numConsulta;$i++)
				{	
					if($rowConsulta[1] =='')
					{
						$rowConsulta[1]	= 'Informe de Inconsistencia';
					}
					echo"<tr>";
						echo"<td align='center'>".$rowConsulta[2]."<td align='center'>".$rowConsulta[3]."<td align='center'>".$rowConsulta[1]."<td align='center'>".$rowConsulta[0]."
						<td align='center'><a href='InfIncBD.php?wTipInfo=".$rowConsulta[0]."&wIfQuery=".$rbtnConsultar."
						&wFecha=".$rowConsulta[2]."&wNumI=".$rowConsulta[1]."&txtHis=".$rowConsulta[7]."&txtIng=".$rowConsulta[8]."
						&rbtnGenerar='1''>Consultar</td>";
					echo"</tr>";
					
					$rowConsulta = mysql_fetch_row($resConsulta);
				}

			echo"</table>";
			
		}
	}//Fin If
	
	if((isset($rbtnGenerar) and $rbtnGenerar == '1') or (isset($wIfQuery) and $wIfQuery == '1'))
	{
		//Trae el numero de informe de la base de la tabla de incosistencias
		
		if($wIfQuery != '1')
		{
			$qNumInf = "SELECT max(incInf)"
					   ."FROM ".$empresa."_000135 cs135 "
					   ."WHERE fecha_data = '".$fecha."' "
					   ."GROUP BY fecha_data";
	
			list($resMax,$numInf) = Consulta($qNumInf,$conex);// Funcion Para ejecutar los Query $qNumInf	
			
			if($numInf == '0')
			{
				$numInf = '0001';		
			}
			else
			{
				$rowSelCau = mysql_fetch_row($resMax);
				$numInf = $rowSelCau[0]+1 ;
				
				if (strlen($numInf)=='1')
				{
					$numInf = leading_zero(($numInf), 4, 0);				
				}						
				else
				{
					if(strlen($rowSelCau[0])=='2')
					{
						$numInf = leading_zero(($numInf), 3, 0);						
					}
					else
					{
						if(strlen($rowSelCau[0])=='3')
						{
							$numInf = leading_zero(($numInf), 2, 0);							
						}
					}
				}
				
				
			}
		}
		else
		{
			$qConsulta = "SELECT CS138.maiFec,CS138.maihrc,CS138.mainig,CS138.mainip,CS138.maigen,CS135.inctdo,CS135.incndo,CS135.incno1,
								 CS135.incno2,CS135.incfen,CS135.incap1,CS135.incap2,CS135.incfen,CS135.inctpi,CS135.incte1 ,CS135.incte2,
								CS135.incte3,CS135.incte4,CS135.incte5,CS135.incte6,CS135.incte7 ,CS135.incObs "
						."  FROM ".$empresa."_000135 CS135, ".$empresa."_000138 CS138"
						." WHERE CS138.mainum = '".$wNumI."'"
						."	 AND CS138.maiFec ='".$wFecha."' "
						."	 AND CS138.maitip = '".$wTipInfo."'"
						."   AND CS138.maiFec = CS135.Fecha_data"
						."   AND CS138.mainum = CS135.incInf ";
			list($resConsulta,$numConsulta) = Consulta($qConsulta,$conex);
			
			
			$rowConsulta = mysql_fetch_row($resConsulta);
			
			$key = $rowConsulta[4];			
			$numInf = $wNumI;
			$hora=$rowConsulta[1];
			$fecha = $rowConsulta[0];
			$txtFeNac = $rowConsulta[9];
			$rbtnTipInc = $rowConsulta[13];
			
			$txtSlas = $rowConsulta[11];
			$txtFlas = $rowConsulta[10];
			$txtFnam = $rowConsulta[7];
			$txtSnam = $rowConsulta[8];
			$txtDoc = $rowConsulta[6];
			$tDoc = $rowConsulta[5];
			$te1=$rowConsulta[14];
			$te2=$rowConsulta[15];
			$te3=$rowConsulta[16];
			$te4=$rowConsulta[17];
			$te5=$rowConsulta[18];
			$te6=$rowConsulta[19];
			$te7=$rowConsulta[20];	
			$incObs = $rowConsulta[21];			
			echo"<input type='hidden' name='wwIfQuery' value=".$wIfQuery.">";			
		}
	//Trae la informacion de la persona que realiza el informe de incosistencias
	
	$qUsuario = " SELECT USU.Descripcion, CS03.Ccocod, CS03.Ccodes"
				."  FROM USUARIOS USU, ".$empresa."_000003 CS03"
				." WHERE USU.Codigo = '".$key."'"				
				."   AND CS03.Ccocod = USU.Ccostos";
				
	$resQusu = mysql_query($qUsuario,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qUsuario." - ".mysql_error());
	
	$rowUsu = mysql_fetch_row($resQusu);
	
	//Trae la informacion necesaria de la empresa que va a generar el informe de incosistencias 	
	$qEmpresa = "SELECT CS49.cfgnit, CS49.cfgnom, CS49.cfgtel, CS49.cfgdir, CS49.cfgcpr, CS49.cfgdep, CS49.Cfgciu, CS49.cfgcel, CS49.cfgind "
		   ."  FROM ".$empresa."_000049 CS49"
		   ." WHERE CS49.id = '1'";
	
	$res = mysql_query($qEmpresa,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qEmpresa." - ".mysql_error());	

	$rowEmpresa = mysql_fetch_row($res);
	
	$arraTemp = Div($rowEmpresa[0]);
	
	//$arrayNit = Div($rowEmpresa[0]);
	$arrayNit = Div($rowEmpresa[0]);
	$arrayCodP = Div($rowEmpresa[4]);
	$arrayTel = Div($rowEmpresa[2]);	
	$arrayDepEmp = Div($rowEmpresa[5]);		// Trae la informacion del departamento donde sae ubicac la empresa que genera el reporte
	$arrayMunEmp = Div($rowEmpresa[6]);		// Trae la informacion del departamento donde sae ubicac la empresa que genera el reporte
	$telReport = Div($rowEmpresa[7]);
	$numIndi = Div($rowEmpresa[8]);			//Trael el indicactivo de la departamento que genera el informe	
	
	$arrayFenac = Div($txtFeNac);
	$arrayTipoDocBD = Div($txtDoc);	
	$TipDoc = explode("-",$ddTipDoc);				//Se guarda el tipo de identificacion segun la base de datos de la entidad encargada del pago
	$arrayNumInf = Div($numInf);
	$arraFeActual = Div($fecha); 	
	$arrayFecha = Div($fecha);
	$arrayHora = Div($hora);

	$arrayFenacDoc = Div($rowSelUsu[7]);
	$arrayNumDoc = Div($rowSelUsu[2]);
	$arraTel = Div($rowSelUsu[9]);
	$arrayDep = Div($rowSelUsu[10]);	
	$arrayMuni = Div($rowSelUsu[12]);
	$arrayFlast = Div($rowSelUsu[3]);	
	$arraySlast = Div($rowSelUsu[4]);
	$arrayFname = Div($rowSelUsu[5]);
	$arraySname = Div($rowSelUsu[6]);
	
	
	$arrayTipoDoc = Div($rowSelUsu[1]);
	$arrayCodips = Div($rowSelUsu[15]);	//Trae el codigo asignado de la empresa encargada del pago 	
	
	
	//Se guarda la informacion en variables para grabarla posteriormente en la Base de Datos	
	if(!isset($tDoc))
	{
		$tDoc = $TipDoc[0];
	}

	
	
	echo"<input type='hidden' name='wTipDoc' value=".$tDoc.">";
	echo"<input type='hidden' name='wtxtHis' value=".$txtHis.">";	
	echo"<input type='hidden' name='wtxting' value=".$txtIng.">";
	echo"<input type='hidden' name='wnumInf' value=".$numInf.">";	
	echo"<input type='hidden' name='wtxtFeNac' value =".$txtFeNac.">";
	echo"<input type='hidden' name='wtDoc' value ='".$tDoc."'>";
	echo"<input type='hidden' name='wtxtDoc' value =".$txtDoc.">";
	echo"<input type='hidden' name='wtxtFnam' value =".$txtFnam.">";
	echo"<input type='hidden' name='wtxtSnam' value =".$txtSnam.">";
	echo"<input type='hidden' name='wtxtFlas' value =".$txtFlas.">";
	echo"<input type='hidden' name='wtxtSlas' value =".$txtSlas.">";
	echo"<input type='hidden' name='wCodemp' value=".$rowSelUsu[18].">";
	echo"<input type='hidden' name='wNitEmp' value=".$rowEmpresa[0].">";
	echo"<table width='800' border='1' align='center'>";
		echo"<tr>";
			echo"<td align='center'colspan='5'><b>Ministerio de la Proteccion Social</b></td>";	
		echo"</tr>";
		echo"<tr>";
			echo"<td align='center' rowspan='2' colspan='1'><img src='C:/Escudo_de_Colombia.png' width='80' height='45'></td>";
			echo"<td align='center' colspan='4'> Informe De Posibles Inconsistencias En La Base De Datos De La Entidad Responsable Del Pago</td>";			
		echo"</tr>";
		echo"<tr>";				
			echo"<td colspan='2' align='center'>";
			echo"<table border='1'><tr><td><b>Numero de Informe ";
			for ($i=0;$i < strlen($numInf);$i++)
			{
				echo"<td>".$arrayNumInf[$i]."</td>";
			}
			echo"</tr></table></td>";			
			echo"<td><table border='1'><tr><td><b>Fecha</b>";
			for($i=0;$i < strlen($fecha);$i++)
			{
				echo"<td>".$arraFeActual[$i]."</td>";
			}			
			echo"</td><td></table><td><table border='1'><tr><td><b>Hora</b>";
			for($i=0;$i < strlen($hora);$i++)
			{
				echo"<td>".$arrayHora[$i]."</td>";
			}
		echo"</tr></table></td>";
		echo"</tr>";
		echo"<tr>";
			echo"<td colspan='5' align='center'><font size='-1'><b>INFORMACION DEL PRESTADOR<b></td>";
		echo"</tr>";
		echo"<tr>";				
			echo"<td colspan ='3' align='center'><b>Nombre </b> ".$rowEmpresa[1]."</td>";
			echo"<td colspan='1' align='center'>NIT<input type='radio' name='rbtnNit' checked><br>";
				echo"CC <input type='radio' name='rbtnNit'></td>";
				echo"<td align='center'><table border='1' align='center'><tr>";
				for($i=0;$i < strlen($rowEmpresa[0]);$i++)
				{
					echo"<td>".$arrayNit[$i]."</tr>";
				}
			echo"<tr><td colspan='9'><font size='-2'>Numero</td><td><td colspan='4'><font size='-2'>DV</td></table></tr>";			
			echo"<tr>";
				echo"<td colspan='2' align='center'><table border='1'><td><b>Codigo</b>";
				for($i=0;$i < strlen($rowEmpresa[4]);$i++)
				{
					echo"<td>".$arrayCodP[$i]."</td>";
				}				
				echo"</tr></table>";
				echo"<td colspan='3' align='center'><b>Direccion del Prestador </b> ".$rowEmpresa[3]."</td>";
			echo"</tr>";
			echo"<tr>";
				echo"<td align='center' colspan='2'>";
				echo"<table border='1'><td><b>Telefono </b></td>";
				for($i=0; $i < strlen($rowEmpresa[8]);$i++){echo"<td><font size='-1'>".$numIndi[$i]."</td>";}
				for($i=0; $i < strlen($rowEmpresa[2]);$i++)
				{
					if($arrayTel[$i] != '-')
					echo"<td><font size='-1'>".$arrayTel[$i]."</td>";
				}
				echo"<td></tr><tr><td><td colspan='5'><font size='-2'>Indicativo<td colspan='7'><font size='-2'>Numero<td></tr>
				</table></td>";
				echo"<td colspan='1' align='center'><table border='1'><tr><td colspan='3'><b>Departamento</b>";

				for($i=0;$i < strlen($rowEmpresa[5]);$i++)
				{
					echo"<td>".$arrayDepEmp[$i]."</td>";
				}
				echo"</tr></table>";
				echo"<td align='center' colspan='2'><table border='1'><tr><td><b>Municipio ";				
				for($i=2; $i < strlen($rowEmpresa[6]);$i++ )
				{
					echo"<td>".$arrayMunEmp[$i]."</td>";
				}
		echo"</tr></td></table>";			
		echo"</tr>";
		echo"</table>";

		//Tabla de La entidad a la que se informa (Pagador)
		
		echo"<table border='1' align='center' width='800'>";
			echo"<tr>";
				echo"<td colspan='5'><b>Entidad a la que se le Informa</b> <font size='-2'>(Pagador) </font>".$rowSelUsu[11]."";
				echo"<td colspan='1' align='right'>";
				echo"<table border='1'><tr><td><b>Codigo </td>";
				
				// Esta listo para cuando se sepa de donde sacar el codigo que el decreto dice que debe aparecer en el campo
				for($i=0; $i < strlen($rowSelUsu[15]);$i++)
				{
					echo"<td>".$arrayCodips[$i]."</td>";
				}				
				echo"</tr></table>";							
			echo"</tr>";
			echo"<tr>";
				echo"<td colspan='1' align='left'><b>Tipo de Inconsistencia </b></td>";
				
				if (isset($rbtnTipInc) and $rbtnTipInc == '1')
				{
					echo"<td colspan='5'><input type='radio' name='rbtnTipInc' value='1' checked><font size='-1'>El usuario no existe en la Base de Datos<br>";
					echo"<input type='radio' name='rbtnTipInc' value='2'>Los Datos del usuario no corresponden con los del documento de identificacion presentado
					</font></td>";
				}
				else
				{
					if (isset($rbtnTipInc) and $rbtnTipInc == '2')
					{
						echo"<td colspan='5'><input type='radio' name='rbtnTipInc' value='1'><font size='-1'>El usuario no existe en la Base de Datos<br>";
						echo"<input type='radio' name='rbtnTipInc' value='2' checked>Los Datos del usuario no corresponden con los del documento de identificacion
						presentado
						</font></td>";
					}
					else
					{
						echo"<td colspan='5'><input type='radio' name='rbtnTipInc' value='1'><font size='-1'>El usuario no existe en la Base de Datos<br>";
						echo"<input type='radio' name='rbtnTipInc' value='2'>Los Datos del usuario no corresponden con los del documento de identificacion presentado
						</font></td>";
					}
				}
			echo"</tr>";
		echo"</table>";	
		
		// table de los Datos de usuario		
		echo"<table border='1' align='center' width='800'>";
			echo"<tr>";
				echo"<td colspan='4' align='center'><font size='-1'><b>DATOS DE USUARIO </b><font size='-2'>(Como aparece en la base de datos)
				</font></td>";	
					
			echo"</tr>";
			echo"<tr>";
				if(strlen(trim($txtSlas)) <= '1')
				{
					$txtSlas = 'No Tiene';
					echo"<input type='hidden' name='wtxtSlas' value ='".$txtSlas."'>";
					
				}
				if(strlen(trim($txtSnam)) <= '1')
				{
					$txtSnam = 'No Tiene';
					echo"<input type='hidden' name='wtxtSnam' value ='".$txtSnam."'>";
				}
				
				echo"<td align='center'>".$txtFlas."</td>";
				echo"<td align='center'>".$txtSlas."</td>";
				echo"<td align='center'>".$txtFnam."</td>";
				echo"<td align='center'>".$txtSnam."</td>";				
			echo"</tr>";
			echo"<tr>";
				echo"<td align='center' border='0'><font size='-2'>(Primer Apellido)</td>";
				echo"<td align='center'><font size='-2'>(Segundo Apellido)</td>";
				echo"<td align='center'><font size='-2'>(Primer Nombre)</td>";
				echo"<td align='center'><font size='-2'>(Segundo Nombre)</td>";				
			echo"</tr>";
			echo"<tr>";
				echo"<td colspan='4' align='center'><FONT SIZE='-1'><B>TIPO DE IDENTIFICACION</B></FONT></td>";				
			echo"</tr>";
			echo"<tr >";
				echo"<td>";
					if($tDoc == 'RC')
					{
						echo"<input type='Radio' name='rbtnTipDoc' checked>Registro Civil<br>";
					}
					else
					{
						echo"<input type='Radio' name='rbtnTipDoc'>Registro Civil<br>";
					}
					
					if($tDoc == 'TI')
					{
						echo"<input type='Radio' name='rbtnTipDoc' checked>Tarjeta de Identidad<br>";
					}
					else
					{
						echo"<input type='Radio' name='rbtnTipDoc'>Tarjeta de Identidad<br>";
					}					
					if($tDoc == 'CC')
					{
						echo"<input type='Radio' name='rbtnTipDoc' checked>Cedula de Ciudadania<br>";
					}
					else
					{
						echo"<input type='Radio' name='rbtnTipDoc'>Cedula de Ciudadania<br>";
					}
					if($tDoc == 'CE')
					{
						echo"<input type='Radio' name='rbtnTipDoc' checked>Cedula de Extranjeria<br>";
					}
					else
					{
						echo"<input type='Radio' name='rbtnTipDoc'>Cedula de Extranjeria<br>";
					}							
				echo"</td>";
				echo"<td>";
					if($tDoc == 'PA')
					{
						echo"<input type='Radio' name='rbtnTipDoc' checked>Pasaporte<br>";
					}
					else
					{
						echo"<input type='Radio' name='rbtnTipDoc'>Pasaporte<br>";
					}
					if($tDoc == 'AS')
					{
						echo"<input type='Radio' name='rbtnTipDoc' CHECKED>Adulto sin Identificacion<br>";
					}
					else
					{
						echo"<input type='Radio' name='rbtnTipDoc'>Adulto sin Identificacion<br>";
					}
					if($tDoc == 'MS')
					{
						echo"<input type='Radio' name='rbtnTipDoc' CHECKED>Menor Sin Identificacion<br>";
					}
					else
					{
						echo"<input type='Radio' name='rbtnTipDoc'>Menor Sin Identificacion<br>";
					}					
				echo"</td>";
				echo"<td align='center' colspan='2'>";
					echo"<table border='1'><tr>";
					for($i=0;$i < strlen($txtDoc);$i++){echo"<td>".$arrayTipoDocBD[$i]."";}					
					echo"</tr><tr><td align='center'colspan='17'><font size='-2'>Numero de Documento de Identificacion</td></tr></table>";
				echo"<table border='1'><tr><td align='center'><font size='-2'>Fecha de Nacimiento</td>";
				
				for($i=0 ; $i < strlen($txtFeNac);$i++)
				{
					echo"<td>".$arrayFenac[$i]."</td>";
				}
				echo"</tr></table></td>";											
			echo"</tr>";
			echo"<tr>";
				echo"<td colspan='3'><b>Direccion de Residencia Habitual:</b> <input type='text' name='txtDir' SIZE='50' value='".$rowSelUsu[8]."'>
				</td>";
				echo"<td><table border='1'><tr><td><b>Telefono </b></td><td>";
					for($i=0; $i < strlen($rowSelUsu[9]);$i++)
					{
						echo"<td>".$arraTel[$i]."</td>";
					}
				echo"</tr></table></td>";		
			echo"</tr>";
			echo"<tr>";

				echo"<td colspan='2' align='center'><table border='1'><tr><td><b>Departamento</b><td>".$arrayDep[0]."<td>".$arrayDep[1]."</tr>
				</table></td>";
				echo"<td colspan='2' align='center'><table border='1'><tr><td><b>Municipio</td><td>".$arrayMuni[2]."<td>".$arrayMuni[3]."<td>"
				.$arrayMuni[4]."</tr></td></table>";
			echo"</tr>";
			echo"<tr>";
				echo"<td colspan='4' align='center'><b><font size='-1'>COBERTURA EN SALUD</b></td>";								
			echo"</tr>";
			echo"<tr>";
				if($rowSelUsu[13] == '1')
				{
					echo"<td><input type='Radio' name='rbtnCobertura' checked><font size='-2'>Regimen Contributivo<br>";
				}
				else
				{
					echo"<td><input type='Radio' name='rbtnCobertura'><font size='-2'>Regimen Contributivo<br>";
				}
				if(isset($rowIps[13]) and $rowIps[13] == '2')
				{
					echo"<input type='Radio' name='rbtnCobertura' checked><font size='-2'>Regimen Subsidiado - Total";
				}
				else
				{
					echo"<input type='Radio' name='rbtnCobertura'><font size='-2'>Regimen Subsidiado - Total";
				}				
				echo"</td>";
				echo"<td>";
				echo"<input type='Radio' name='rbtnCobertura'><font size='-2'>Regimen Contributivo<br>";
				echo"<input type='Radio' name='rbtnCobertura'><font size='-2'>Regimen Subsidiado - Total";
				echo"</td>";
				echo"<td>";
				echo"<input type='Radio' name='rbtnCobertura'><font size='-2'>Regimen Subsidiado - Parcial<br>";
				echo"<input type='Radio' name='rbtnCobertura'><font size='-2'>Poblacion pobre no Asegurada con el Sisben";
				echo"</td>";
				echo"<td>";
				echo"<input type='Radio' name='rbtnCobertura'><font size='-2'>Poblacion pobre no Asegurada sin el Sisben<br>";
				echo"<input type='Radio' name='rbtnCobertura'><font size='-2'>Regimen Subsidiado - Total";
				echo"</td>";				
				
			echo"</tr>";
			
		echo"</table>";
		
		// table de la Informacion de Posible Inconsistencia
			
		echo"<table border='1' align='center' width='800'>";
			echo"<tr>";
				echo"<td align='center' colspan='20'><b><font size='-1'>INFORMACION DE POSIBLES INCONSISTENCIAS</b></td>";
			echo"</tr>";
			echo"<tr>";
				echo"<td colspan='1' align='center'><b><font size='-1'>Variable Presuntamente Incorrecta</td>";
				echo"<td colspan='20' align='center'><b><font size='-1'>Datos Segun Documento De Identificacion(Fisico)</td>";
			echo"</tr>";
			echo"<tr>";
				echo"<td rowspan='8'>";
					
					if (isset($te1) and $te1=='SI')
						echo"<input type='checkbox' name='chkFlas' value='1' CHECKED><b>Primer Apellido<br>";
					else
						echo"<input type='checkbox' name='chkFlas' value='1'><b>Primer Apellido<br>";
					if(isset($te2) and $te2 == 'SI')
						echo"<input type='checkbox' name='chkSlas' value='1' CHECKED><b>Segundo Apellido<br>";
					else
						echo"<input type='checkbox' name='chkSlas' value='1'><b>Segundo Apellido<br>";
					if(isset($te3) and $te3 == 'SI')					
						echo"<input type='checkbox' name='chkFname' value='1' CHECKED><b>Primer Nombre<br>";
					else
						echo"<input type='checkbox' name='chkFname' value='1'><b>Primer Nombre<br>";
					if(isset($te4) and $te4 == 'SI')
						echo"<input type='checkbox' name='chkSname' value='1' CHECKED><b>Segundo Nombre<br>";
					else
						echo"<input type='checkbox' name='chkSname' value='1'><b>Segundo Nombre<br>";
					if(isset($te5) and $te5 == 'SI')
						echo"<input type='checkbox' name='chkTipDoc' value='1' CHECKED><b>Tipo Documento de Identificacion<br>";
					else
						echo"<input type='checkbox' name='chkTipDoc' value='1'><b>Tipo Documento de Identificacion<br>";
					if(isset($te6) and $te6 == 'SI')
						echo"<input type='checkbox' name='chkNumDoc' value='1' CHECKED><b>Numero Documento de Identificacion<br>";
					else
						echo"<input type='checkbox' name='chkNumDoc' value='1'><b>Numero Documento de Identificacion<br>";
					if(isset($te7) and $te7 == 'SI')
						echo"<input type='checkbox' name='chkFecNac' value='1' CHECKED>Fecha Nacimiento";
					else	
						echo"<input type='checkbox' name='chkFecNac' value='1'>Fecha Nacimiento";
					
				echo"</td>";					
					echo"<tr><td><b><font size='-1'>Primer Apellido </b><td align='left'><table border='1' align='left' HEIGHT='1'>";
					for($i=0;$i < strlen($rowSelUsu[3]);$i++){echo"<td><font size='-2'>".$arrayFlast[$i]."";}
					echo"</table>";
					echo"</td></tr>";
					echo"<tr><td><b><font size='-1'>Segundo Apellido </b><td><table border='1' align='left'>";
					for($i=0;$i < strlen($rowSelUsu[4]);$i++){echo"<td><font size='-2'>".$arraySlast[$i]."";}
					echo"</table>";
					echo"</td></tr>";
					echo"<tr><td><b><font size='-1'>Primer Nombre</b><td><table border='1' align='left'>";
					for($i=0;$i < strlen($rowSelUsu[5]);$i++){echo"<td><font size='-2'>".$arrayFname[$i]."</b></td>";}
					echo"</table>";
					echo"</td></tr>";
					echo"<tr><td><b><font size='-1'>Segundo Nombre</b><td><table border='1' align='left'>";
					for($i=0;$i < strlen($rowSelUsu[6]);$i++){echo"<td><font size='-2'>".$arraySname[$i]."";}
					echo"</table>";
					echo"</td></tr>";
					echo"<tr><td><b><font size='-1'>Tipo Documento de Identificacion</b><td><table border='1' align='left'>";
					for($i=0;$i < strlen($rowSelUsu[1]);$i++){echo"<td><font size='-2'>".$arrayTipoDoc[$i]."";}
					echo"</table>";
					echo"</td></tr>";
					echo"<tr><td><b><font size='-1'>Numero Documento de Identificacion</b><td>";
					echo"<table border='1'><table border='1'>";
					for($i=0;$i < strlen($rowSelUsu[2]);$i++){echo"<td><font size='-2'>".$arrayNumDoc[$i]."";}
					echo"</tr></table>";
					echo"<tr><td><b><font size='-1'>Fecha Nacimiento</b><td>";
					echo"<table border='1'><tr>";
					for($i=0; $i < strlen($rowSelUsu[7]);$i++)
					{
						echo"<td><font size='-2'>".$arrayFenacDoc[$i]."</td>";
					}
					echo"</tr></table>";
			echo"</tr>";				
			echo"<tr>";			
				if(isset($incObs) and $incObs!='')				
					echo"<td colspan='20' align='midlen'><b>Observaciones <b> <textArea name='txtObs' rows='2' cols='80'>".$incObs."</textarea>
					</td>";
				else
				
					echo"<td colspan='20' align='midlen'><b>Observaciones <b> <textArea name='txtObs' rows='2' cols='80'></textarea></td>";
			echo"</tr>";
		echo"</table>";	
				
			//Table de la Informacion de la persona que reporta
				
			echo"<table border='1' align='center' width='800'>";
				echo"<tr>";
					echo"<td align='center' colspan='4'><font size='-1'><b>INFORMACION DE LA PERSONA QUE REPORTA</b></td>";
				echo"</tr>";
				echo"<tr>";
					echo"<td><b>Nombre de quien Reporta: </b>".$rowUsu[0]."</td>";
					echo"<td><table border='1'><tr><td><b>Telefono </td>";	
					for($i=0; $i < 5;$i++){echo"<td>".$numIndi[$i]."";}		
					for($i=0; $i < strlen($rowEmpresa[2]);$i++){if($arrayTel[$i]!='-')echo"<td>".$arrayTel[$i]."";}
					echo"</tr>";
					echo"<tr><td colpan='1'></td><td colspan='5'>Indicativo<td colspan='7'>Numero<td colspan='6'>Extension</tr></table>";
				echo"</tr>";					
				echo"<tr>";
					echo"<td><b>Cargo o Actividad:</b> ".$rowUsu[2]."</td>";
					echo"<td><table border='1'><td><b>Telefono Celular </b>";
					for($i=0; $i < strlen($rowEmpresa[7]);$i++)
					{
						echo"<td>".$telReport[$i]."</td>";
					}
					
				echo"</tr></table>";
				if(isset($wIfQuery) and $wIfQuery != '1')
				{
					echo"<tr>";
						echo"<td colspan='4' align='center'><input type='radio' name='rbtnGrabar' onclick='enter()' value='1'>Grabar</td>";
					echo"</tr>";
				}
				echo"<tr>";
					echo"<td colspan='4' align='left'><font size='-2'>MPS-IPI V5.0 2008-07-11</td>";
				echo"</tr>";

			echo"</table>";
	
	echo"</form>";
	}// Fin Del Form
}// Fin If rbtnEnviar
	
	
	///////////////////////////////////////////////////////////////////////////////
	/////					Funciones De La Aplicacion			   			  /////
	///////////////////////////////////////////////////////////////////////////////
	
	Function Consulta($query,$conex) // Esta Funcion Ejecuta los querys que se utilizan en el desarrollo de la aplicacion
	{
		

		
		$resSelect = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
		$numSelect = mysql_num_rows($resSelect);
		
		return array($resSelect,$numSelect);
	}
	
	function leading_zero( $aNumber, $intPart, $floatPart=NULL, $dec_point=NULL, $thousands_sep=NULL) 
	{        
	   $formattedNumber = $aNumber;
	   if (!is_null($floatPart)) 
	   {    //without 3rd parameters the "float part" of the float shouldn't be touched
	      $formattedNumber = number_format($formattedNumber, $floatPart, $dec_point, $thousands_sep);
	   }
	   if ($intPart > floor(log10($formattedNumber)))
	    $formattedNumber = str_repeat("0",($intPart + -1 - floor(log10($formattedNumber)))).$formattedNumber;
	 
	  return $formattedNumber;
	}
	
	function Div($string)
	{
		for($i=0;$i < strlen($string);$i++)
		{ 
			$splitted[$i] = $string[$i];
    	}

		return($splitted);
    }
	
	?>

</body>
</html>
