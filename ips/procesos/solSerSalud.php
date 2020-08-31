<html>
<head>
	<title>Solicitud De Autorizacion De Servicios De Salud</title>
</head>
	<script type='text/javascript'>
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
	***	 										Solicitud De Autorizacion Servicion de Salud			     			 			 ***	
	***																																 ***
	************************************************************************************************************************************/			
	//==================================================================================================================================
	//PROGRAMA						:solSerSalud.php 
	//AUTOR							:Juan Esteban Lopez Aguirre
	//FECHA CREACION				:Agosto 03 de 2009
	//FECHA ULTIMA ACTUALIZACION 	:
	//DESCRIPCION					: 
	//								 
	//MODIFICACIONES:
	//===================================================================================================================================
	echo"<form name='forma' action='solSerSalud.php' method='post'>";
	{
	
	///////////////////////////////////////////////////////////////////////////////
	/////					Declaracion De Variables                          /////
	///////////////////////////////////////////////////////////////////////////////
	
	

	

	$fecha = date("Y-m-d");					//Se guarda la fecha en que se realizo el registro
	$mes = explode("-",$fecha);
	$hora = date("H:i");					//Se guarda la hora en que se realizo el registro
	$key = substr($user,2,strlen($user));	//Se guarda el usuario que abre el informe
	$empresa = 'clisur';					// Se guarda estatica la empresa mientras se cuadra la dinamica
	
	
	///////////////////////////////////////////////////////////////////////////////
	///////		            Validacion de datos                             ///////
	///////////////////////////////////////////////////////////////////////////////	
	

	if(isset($btnGenerar) and $btnGenerar == '1' and (trim($txtHis) == '' or trim($txtIng) == ''))//
	{	
		?>
			<script>
				alert("Debe Diligenciar Todos Los Campos del Formulario")		
			</script>
		<?php
		$error ='1';
		unset($btnGenerar);			
	}
	
	if((isset($btnGenerar) and $btnGenerar == '1') and ($ddCodDiag =='----'))
	{
		?>
			<script>
				alert("Debe Diligenciar el Diagnostico")		
			</script>
		<?php
		unset($btnGenerar);	
		$error2 ='1';
	}
	if((isset($btnGenerar) and $btnGenerar == '1') and ($ddServPpal=='----'))
	{
		?>
			<script>
				alert("Debe Diligenciar el servicio Solicitado")		
			</script>
		<?php
		unset($btnGenerar);
	}
	if((isset($btnGenerar) and $btnGenerar == '1') and (trim($txtCantidadPpal)==''))
	{
		?>
			<script>
				alert("Debe Diligenciar La cantidad del Servicio Solicitado")		
			</script>
		<?php
		unset($btnGenerar);
	}
	if (isset($rbtnGrabar) and $rbtnGrabar=='1')
	{
		
			///////////////////////////////////////////////////////////////////////////////
			///////		            Validacion de datos Antes de Grabar             ///////
			///////////////////////////////////////////////////////////////////////////////		
			
			if ($rbtnOriSolAcc == '01')
			{
				if($rbtnOriSol =='02')
					$rbtnOriSol ='16';	
				else
					if($rbtnOriSol =='06')
						$rbtnOriSol ='17';
					else
					{
						unset($rbtnOriSol);	
						unset($rbtnOriSolAcc);					
					}
				
			}
			
		
		/*$qLock = "lock table ".$empresa."_000138 LOW_PRIORITY WRITE";//Bloque la tabla para realizar otros accesos
		$res2 = mysql_query($qLock,$conex);								 //Ejecuta la instruccion de bloqueo de tabla
		*/
		if($rbtnOriSol!='' and $rbtnTipSor!='' and $rbtnPriAte!='' and $rbtnUbiPac!='')
		{
		$qInsMaest = "INSERT INTO ".$empresa."_000138 (Fecha_data,Hora_data,maitip,mainum,maifec,maihrc,mainig,mainip,maihis,maiing,maigen)"
					."	   VALUES('".$fecha."','".$hora."','03','".$wnumInf."','".$fecha."','".$hora."','".$wNitEmp."','".$wnitips."','".$wtxtHis."', '".$wtxtIng."',
					'".$key."')";
					
		$res = mysql_query($qInsMaest,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qInsMaest." - ".mysql_error());
		

	
			$qInsert = "INSERT INTO ".$empresa."_000137 (Fecha_data, Hora_data, soanum,soaora,soases,soapra,soaubc,soaser,soacdr,soacdr2,soacdr3,soacdr4,soacdr5
												 ,soacam,soagui,soajus,soadpp,soadr1,soadr2,soadr3)"
					  ."	 VALUES ('".$fecha."','".$hora."','".$wnumInf."','".$rbtnOriSol."','".$rbtnTipSor."','".$rbtnPriAte."','".$rbtnUbiPac."',
									'".$txtServ."','".$wcodigoPpal."','".$wcodigo[0]."','".$wcodigo[1]."','".$wcodigo[2]."','".$wcodigo[3]."',
									'".$espera."','".$txtGuia."','".$txtJust."','".$wdiagPp."','".$wdiagRe1."','".$wdiagRe2."','".$wdiagRe3."')";
									
			$res = mysql_query($qInsert,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qInsert." - ".mysql_error());
			
			
				
		}
		else
		{
			?>
				<script>
					alert("Debe Ingresar la Informacion requerida en el Formulario")	
				</script>
			<?php
			
			$btnGenerar=1;
			
			$ddServPpal = $wddServPpal;
			$ddCodDiag2 = $wdiagR2;
			$ddCodDiag3 = $wdiagR3;
			$ddCodDiag4 = $wdiagR4;
				$codDiagRelac1 = explode("-",$ddCodDiag2);
	$arrayDiagPrinc1 = Div($codDiagRelac1[0]);
		//Diagnostico Relacionado 2
	$codDiagRelac2 = explode("-",$ddCodDiag3);
	$arrayDiagPrinc2 = Div($codDiagRelac2[0]);
		//Diagnostico Relacionado 3
	$codDiagRelac3 = explode("-",$ddCodDiag4);
						
			$txtCantidadPpal = $wCantPpal;
			$ddCodDiag = $wddCodDiag;
			$wotroServicio=$wwotroServicio;			
			for($i=0;$i < $wotroServicio;$i++)
			{
				$ddServ[$i] = $wddServ[$i];
				
				$txtCantidad[$i+1] = $wtxtCantidad[$i];					
			}
			
			$ddPrefesional = $wddPrefesional;
				
			
		}
		//$qLock = "UNLOCK TABLE";	   
	}
	if(isset($btnGenerar) and $btnGenerar == '1')
	{
		if(isset($wtxtHis) and isset($wtxtIng))
		{
			$txtHis = $wtxtHis;
			$txtIng = $wtxtIng;
			
		}
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
					alert("Los Datos Ingresados son Errados")		
				</script>
			<?php
			$error2='1';
			unset($btnGenerar);					
		}	
	}

	
	///////////////////////////////////////////////////////////////////////////////
	/////					Se pinta el Front End                         	  /////
	///////////////////////////////////////////////////////////////////////////////
if(!isset($btnGenerar))
{
	echo"<table align='center' border='1' width='800'>";
	echo"<tr>";
		echo"<td colspan='1'><img src='/matrix/images/medical/pos/logo_".$empresa.".png' width='200' height='70' vspace=15 hspace=15></td>";
		echo"<td align='center' bgcolor='#227CE8' colspan='3'><font size='6' color='white'>Solicitud De Autorizacion De Servicios De Salud</font></td>";
		echo"</tr>";
		echo"<tr>";
		if(isset($txtHis) and isset($txtIng) and $txtHis != '' and $txtIng != '')
		{
			echo"<td align='center' colspan='6'><b>Historia: </b><input type='text' name='txtHis' size='8' value='".$txtHis."'><b> Ingreso: </b><input type='text' name='txtIng' size='2' value='".$txtIng."'></td>";
		}
		else
		{			
		    echo"<td align='center' colspan='4'><b>Historia: </b><input type='text' name='txtHis' size='8'><b> Ingreso: </b><input type='text' name='txtIng' size='2'></td>";
		}
		///////////////////////////////////////////////////////////////////
		echo"<tr>";
					echo"<td colspan='5' align='center' align='center'><b>Impresion Diagnostica</td>";
		echo"</tr>";
		echo"<tr><td><b>Diagnostico Principal</td>";
			echo"<td colspan='4' align='center'><input type='text' name='txtDesDiag' value='%' onchange='enter()' size='10'>";
				if(isset($txtDesDiag))
				{
					if(trim($txtDesDiag) != '')
					$qDiag= "SELECT Codigo,Descripcion "
						 	." FROM root_000011 "
							."WHERE Descripcion like '%".$txtDesDiag."%'";
					list($resDiag,$numDiag) = Consulta($qDiag,$conex);// Funcion Para ejecutar los Query
				}
			
					
			echo"<select name='ddCodDiag' style='width:450px' onchange='enter()'>";
				
				echo"<option Selected>----</option>";
				
				if(isset($ddCodDiag) and $ddCodDiag != '----')
				{
					echo"<option selected>".$ddCodDiag."</option>";
				}
				else
				{
					//list($resDiag,$numDiag) = Consulta($qDiag,$conex);// Funcion Para ejecutar los Query
					for($i=0;$i < $numDiag;$i++)
					{
						$rowDiag = mysql_fetch_row($resDiag);

							echo"<option>".$rowDiag[0]."-".$rowDiag[1]."</option>";
					}
				}					
			echo"</select>";
			
								
				echo"</tr>";
				echo"<tr>";
					echo"<td><b>Diagnostico Relacionado 1</td>";
					echo"<td colspan='4' align='center'><input type='text' name='txtDesDiag2' value='%' onchange='enter()' size='10'>";
					
					if(isset($txtDesDiag2) and $txtDesDiag2 != '%')
					{
						$qDiag= "SELECT Codigo,Descripcion "
							 	." FROM root_000011 "
								."WHERE Descripcion like '%".$txtDesDiag2."%'";
						
						list($resDiag,$numDiag) = Consulta($qDiag,$conex);// Funcion Para ejecutar los Query
					}

					echo"<select name='ddCodDiag2'  style='width:450px' onchange='enter()'>";
					echo"<option Selected>----</option>";
					
					if(isset($ddCodDiag2) and $ddCodDiag2 != '----')
					{
						echo"<option selected>".$ddCodDiag2."</option>";
					}
					else
					{	
						if(isset($txtDesDiag2) and $txtDesDiag2 != '%')
						{								
							for($i=0;$i < $numDiag;$i++)
							{
								$rowDiag = mysql_fetch_row($resDiag);
								
								if(isset($ddCodDiag2) and $ddCodDiag2 == $rowDiag[0]."-".$rowDiag[1])
								{
									echo"<option selected>".$rowDiag[0]."-".$rowDiag[1]."</option>";
								}
								else
								{
									echo"<option>".$rowDiag[0]."-".$rowDiag[1]."</option>";
								}
									
							}
						}					
					}					
					echo"</select>";
				echo"</tr>";
				echo"<tr>";
				echo"<td><b>Diagnostico Relacionado 2</td>";
					echo"<td colspan='4' align='center'><input type='text' name='txtDesDiag3' value='%' onchange='enter()' size='10'>";
					
					if(isset($txtDesDiag3) and $txtDesDiag3 != '%')
					{
						$qDiag= "SELECT Codigo,Descripcion "
							 	." FROM root_000011 "
								."WHERE Descripcion like '%".$txtDesDiag3."%'";
						
						list($resDiag,$numDiag) = Consulta($qDiag,$conex);// Funcion Para ejecutar los Query
					}
					
					echo"<select name='ddCodDiag3' style='width:450px' onchange='enter()'>";
					echo"<option Selected>----</option>";
					if(isset($ddCodDiag3) and $ddCodDiag3 != '----')
					{
						echo"<option selected>".$ddCodDiag3."</option>";
					}
					else
					{
						if(isset($txtDesDiag3) and $txtDesDiag3 != '%')
						{
							for($i=0;$i < $numDiag;$i++)
							{
								$rowDiag = mysql_fetch_row($resDiag);
								
								if(isset($ddCodDiag3) and $ddCodDiag3 == $rowDiag[0]."-".$rowDiag[1])
								{
									echo"<option selected>".$rowDiag[0]."-".$rowDiag[1]."</option>";
								}
								else
								{
									echo"<option>".$rowDiag[0]."-".$rowDiag[1]."</option>";
								}
									
							}
						}
					}					
					echo"</select>";
				echo"</tr>";
				echo"<tr>";
				echo"<td><b>Diagnostico Relacionado 3</td>";
					echo"<td colspan='4' align='center'><input type='text' name='txtDesDiag4' value='%' onchange='enter()' size='10'>";

					if(isset($txtDesDiag4) and $txtDesDiag4 != '%')
					{
						$qDiag= "SELECT Codigo,Descripcion "
							 	." FROM root_000011 "
								."WHERE Descripcion like '%".$txtDesDiag4."%'";
								
						list($resDiag,$numDiag) = Consulta($qDiag,$conex);// Funcion Para ejecutar los Query
					}
					
					echo"<select name='ddCodDiag4' style='width:450px' onchange='enter()'>";
					echo"<option Selected>----</option>";
					
					if(isset($ddCodDiag4) and $ddCodDiag4 != '----')
					{
						echo"<option selected>".$ddCodDiag4."</option>";
					}
					else
					{
						if(isset($txtDesDiag4) and $txtDesDiag4 != '%')
						{
							
							for($i=0;$i < $numDiag;$i++)
							{
								$rowDiag = mysql_fetch_row($resDiag);
								
								if(isset($ddCodDiag4) and $ddCodDiag4 == $rowDiag[0]."-".$rowDiag[1])
								{
									echo"<option selected>".$rowDiag[0]."-".$rowDiag[1]."</option>";
								}
								else
								{							
									echo"<option>".$rowDiag[0]."-".$rowDiag[1]."</option>";
								}
								
							}
						}
					}					
					echo"</select>";
				echo"</tr>";
				echo"<tr><td colspan='5' align='center' align='center'><b>Servicios Solicitados</td></tr>";
				echo"<tr><th colspan='4'>Descripcion del Servicio<th colspan='1'>Cantidad</tr>";
				echo"<tr><td colspan='4' align='center'><input type='text' name='txtFindServPpal' size='10' onchange='enter()')>";
				
				if(isset($txtFindServPpal) and $txtFindServPpal == '')
				{
					$qServPpal= "SELECT Codigo,Nombre "
						 	." FROM root_000012";							 	
				}
				else
				{
					if (isset($txtFindServPpal))
					{
						$qServPpal= "SELECT Codigo,Nombre "
							 	." FROM root_000012 "
								."WHERE Nombre like '%".$txtFindServPpal."%'";
					}					
				}
									 
				echo"<select name='ddServPpal' style='width:500px' onchange='enter()'>";
				echo"<option Selected>----</option>";
				
				if((isset($txtFindServPpal) and $txtFindServPpal != '') or ($ddServPpal != '----' and $ddServPpal!=''))				
				{					
					list($resServ,$numServ) = Consulta($qServPpal,$conex);// Funcion Para ejecutar los Query
					
					if(isset($ddServPpal) and $ddServPpal != '----')
					{
						echo"<option selected>".$ddServPpal."</option>";
						$k = 0;
						$otroServicio=1;
						$k = $k+ 1;
					}
					else
					{	
						for($i=0;$i < $numServ;$i++)
						{
							$rowServ = mysql_fetch_row($resServ);
						
							if(isset($txtFindServPpal) and $ddServPpal == $rowServ[0]."-".$rowServ[1])
							{
								echo"<option selected>".$rowServ[0]."-".$rowServ[1]."</option>";
							}
							else
							{
								echo"<option>".$rowServ[0]."-".$rowServ[1]."</option>";
							}//if(isset($txtFindServ) and $ddServ == $rowServ[0]."-".$rowServ[1])
							
						}//for($i=0;$i < $numServ;$i++)

					}

				}
				if($txtCantidadPpal!='')
				{
					echo"<td colspan='1' align='center'><input type='text' name='txtCantidadPpal' size='2' value='".$txtCantidadPpal."'></td></tr>";
				}
				else
				{
					echo"<td colspan='1' align='center'><input type='text' name='txtCantidadPpal' size='2'></td></tr>";
				}					
			echo"</select></td>";
					
				echo"</td>";
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				if(isset($otroServicio))
				{
				$i=0;
				while($i < $otroServicio)
				{				
				echo"<tr><td colspan='4' align='center'><input type='text' name='txtFindServ[".$otroServicio."]' size='10' onchange='enter()')>";
						
					if(isset($txtFindServ[$otroServicio]) and $txtFindServ[$otroServicio] == '')
					{
						$qServ[$otroServicio]= "SELECT Codigo,Nombre "
							 				 	   ." FROM root_000012";							 	
					}
					else
					{
						if (isset($txtFindServ[$otroServicio]))
						{
							$qServ[$otroServicio]= "SELECT Codigo,Nombre "
				 				 	 			   ." FROM root_000012 "
									 			   ."WHERE Nombre like '%".$txtFindServ[$otroServicio]."%'";
						}									
					}
							
					echo"<select name='ddServ[".$otroServicio."]' style='width:500px' onchange='enter()'>";
					echo"<option Selected>----</option>";
						
		if((isset($txtFindServ[$otroServicio]) and $txtFindServ[$otroServicio] != '') or ($ddServ[$otroServicio] != '----' 
				  and $ddServ[$otroServicio]!=''))						
				  {						
						list($resServ,$numServ) = Consulta($qServ[$otroServicio],$conex);// Funcion Para ejecutar los Query
							
						if(isset($ddServ[$otroServicio]) and $ddServ[$otroServicio] != '----')
						{
							echo"<option selected>".$ddServ[$otroServicio]."</option>";
							echo"</select></td>";
					
					echo"</td>";				
							$otroServicio = $otroServicio + 1;
							echo"<input type='hidden' name='wotroServicio' value='".$otroServicio."'>";
							$k = $k+ 1;
							
							if($txtCantidad[$otroServicio]!='')
							{
								echo"<td colspan='1' align='center'><input type='text' name='txtCantidad[".$otroServicio."]' size='2' 
								value='".$txtCantidad[$otroServicio]."'></td></tr>";
							}
							else
							{
								echo"<td colspan='1' align='center'><input type='text' name='txtCantidad[".$otroServicio."]' size='2'></td></tr>";
							}
						}
						else
						{								
							for($i=0;$i < $numServ;$i++)
							{
								$rowServ = mysql_fetch_row($resServ);
								
								if(isset($txtFindServ[$otroServicio]) and $ddServ[$otroServicio] == $rowServ[0]."-".$rowServ[1])
								{
									echo"<option selected>".$rowServ[0]."-".$rowServ[1]."</option>";
								}
								else
								{
									echo"<option>".$rowServ[0]."-".$rowServ[1]."</option>";
								}//if(isset($txtFindServ) and $ddServ == $rowServ[0]."-".$rowServ[1])
								
							}//for($i=0;$i < $numServ;$i++)
						}
							
					}					
					echo"</select></td>";
					
					echo"</td>";
					
					
					$i++;
				}//FIN WHILE
			
			}//Fin IF
				
				
				
				echo"<tr><th colspan='5'>Profesional que Solicita</th></tr>";
				echo"<tr><td align='right' colspan='1'><input type='text' name='txtFinProfesional' size='10' value='%%%%%' onchange='enter()'></td>";
				
				if (isset($txtFinProfesional))
				{
					if ($txtFinProfesional != "%%%%%" and $txtFinProfesional != '')
					{
						$qMedicos = "  SELECT CS51.Mednom, CS51.Medreg, CS51.Meddoc"
								   ."    FROM ".$empresa."_000051 CS51"
								   ."   WHERE Medest = 'on'"
								   ."     AND CS51.Mednom like '%".$txtFinProfesional."%'"							   
								   ."ORDER BY CS51.Mednom";
						
						list($resMed,$numMed) = Consulta($qMedicos,$conex);// Funcion Para ejecutar los Query
					}
				}				
								
				echo"<td align='left' colspan='4'><select name='ddPrefesional' style='width:500px'>";
					echo"<option Selected>----</option>";
					if(isset($ddPrefesional) and $ddPrefesional != '----')
					{
						echo"<option selected>".$ddPrefesional."</selected>";
					}
					else
					{
						for($i=0;$i < $numMed;$i++)
						{
							$rowMed = mysql_fetch_row($resMed);
							echo"<option>".$rowMed[0]." : ".$rowMed[1]."</option>";
						}
					}					
					
				echo"</tr>";
				
				echo"<tr><td colspan='5' align='center'><input type='radio' name='btnGenerar' value='1' onclick='enter()'>Generar<input type='radio' name='btnConsultar' value='1' onclick='enter()'>Consultar</td></tr>";
			
		///////////////////////////////////////////////////////////////////
		echo"</tr></table>";				
}	

if((isset($btnGenerar) and $btnGenerar == '1') or (isset($btnConsultar) and $btnConsultar =='1'))
{	
	$qNumInf = "SELECT max(soanum)"
				."FROM ".$empresa."_000137 cs137 ";			   	   
			
			list($resMax,$numInf) = Consulta($qNumInf,$conex);// Funcion Para ejecutar los Query $qNumInf
				
			if($mes[1]=='01' and $mes[2]=='01')
			{
				$numInf = '0000000001';
				$arrayNumInf = Div($numInf);		
			}
			else
			{
				$rowSelCau = mysql_fetch_row($resMax);			
				$numInf = $rowSelCau[0]+1 ;
				
				//if (strlen($numInf)=='1')
				//{
					$numInf = leading_zero(($numInf), 11-strlen($numInf), 0);
					$arrayNumInf = Div($numInf);				
				//}						
								
			}

	
	$qEmpresa = "SELECT CS49.cfgnit, CS49.cfgnom, CS49.cfgtel, CS49.cfgdir, CS49.cfgcpr, CS49.cfgdep, CS49.cfgciu, CS49.cfgcel, CS49.cfgind "
		   	   ."  FROM ".$empresa."_000049 CS49"
		       ." WHERE CS49.id = '1'";
	
	list($resQemp,$numQemp) = Consulta($qEmpresa,$conex);
	
	$rowSelEmp = mysql_fetch_row($resQemp);
	
	// Se Trae la Informacion de la Persona que realiza el informe 
	
	$qUsuario = " SELECT USU.Descripcion, CS03.Ccocod, CS03.Ccodes"
				."  FROM USUARIOS USU, ".$empresa."_000003 CS03"
				." WHERE USU.Codigo = '".$key."'"				
				."   AND CS03.Ccocod = USU.Ccostos";

	list($resQusu,$numQusu) = Consulta($qUsuario,$conex);
	
	$rowUsu = mysql_fetch_row($resQusu);
	
	   
	//Se guarda la Informacion que no se encuentra en la Bd Matrix Hasta que se cree
	
	if($rowSelEmp[0]== '890939026-1')
	{
		$arrayDep = Div('005');
		$arrayMun = Div('05266');
		$telReport = '320-671-06-47';
		$numIndi = Div('00007');
	}
	
		//Modificacion de las Variables con el fin de dar el formato	
	$arrayFecha = Div($fecha);	
	$arrayHora = Div($hora);
	$arrayNit = Div($rowSelEmp[0]);
	$arrayCodPre = Div($rowSelEmp[4]);
	//$arrayIndiMed = Div($indiMede); 
	$arrayTelPres = Div($rowSelEmp[2]);
	$arrayCodMin = Div($rowSelUsu[15]);
	$arrayNumDoc = Div($rowSelUsu[2]);
	$arrayFeNac = Div($rowSelUsu[7]);
	$arrayFeIng = Div($rowSelUsu[16]);
	$arrayHrIng = Div($rowSelUsu[17]);
	$arrayTelPac = Div($rowSelUsu[9]);
	$arrayDepPac = Div($rowSelUsu[10]);
	$arrayMunPac = Div($rowSelUsu[12]);
	
	$telReport = Div($rowSelEmp[7]);
	//Diagnostico principal
	echo"<input type='hidden' name='wddCodDiag' value='".$ddCodDiag."'>";
	$codDiagPrincipal = explode("-",$ddCodDiag);
	$arrayDiagPrinc = Div($codDiagPrincipal[0]);
	//Diagnostico Relacionado 1
	$codDiagRelac1 = explode("-",$ddCodDiag2);
	$arrayDiagPrinc1 = Div($codDiagRelac1[0]);
	echo"<input type='hidden' name='wdiagR2' value='".$ddCodDiag2."'>";
	
		//Diagnostico Relacionado 2
	$codDiagRelac2 = explode("-",$ddCodDiag3);
	$arrayDiagPrinc2 = Div($codDiagRelac2[0]);
	echo"<input type='hidden' name='wdiagR3' value='".$ddCodDiag3."'>";
		//Diagnostico Relacionado 3
	$codDiagRelac3 = explode("-",$ddCodDiag4);
	$arrayDiagPrinc3 = Div($codDiagRelac3[0]);
	echo"<input type='hidden' name='wdiagR4' value='".$ddCodDiag4."'>";
	// Servivio Solicitado 
	
	echo"<input type='hidden' name='wddServPpal' value='".$ddServPpal."'>";
	$codCupsServicioPpal = explode("-",$ddServPpal);
	$arrayServicioPpal = Div($codCupsServicioPpal[0]);
	$arrayCantidadPpal = Div($txtCantidadPpal);
	
	
	

	$numIndi = Div($rowSelEmp[8]);				//Trael el indicactivo de la departamento que genera el informe
	$arrayDep = Div($rowSelEmp[5]);		// Trae la informacion del departamento donde sae ubicac la empresa que genera el reporte
	$arrayMun = Div($rowSelEmp[6]);
		
	echo"<input type='hidden' name='wtxtHis' value=".$txtHis.">";
	echo"<input type='hidden' name='wtxtIng' value=".$txtIng.">";
	echo"<input type='hidden' name='wdiagPp' value=".$codDiagPrincipal[0].">";
	echo"<input type='hidden' name='wdiagRe1' value=".$codDiagRelac1[0].">";
	echo"<input type='hidden' name='wdiagRe2' value=".$codDiagRelac2[0].">";
	echo"<input type='hidden' name='wdiagRe3' value=".$codDiagRelac3[0].">";
	echo"<input type='hidden' name='wnumInf' value=".$numInf.">";
	echo"<input type='hidden' name='wNitGen' value=".$rowSelEmp[0].">";
	echo"<input type='hidden' name='wfeate' value=".$rowSelUsu[16].">";
	echo"<input type='hidden' name='whrate' value=".$rowSelUsu[17].">";
	echo"<input type='hidden' name='wnitips' value=".$rowSelUsu[18].">";
	echo"<input type='hidden' name='wNitEmp' value=".$rowSelEmp[0].">";
	if(isset($codCupsServicio))	
		echo"<input type='hidden' name='wCodCups' value=".$codCupsServicio[0].">";
	if(isset($codCupsServicio2))
		echo"<input type='hidden' name='wCodCups2' value=".$codCupsServicio2[0].">";
	
echo"<table width='800' border='1' align='center'>";
			echo"<tr>";
				echo"<td align='center'colspan='4'><b>Ministerio de la Proteccion Social</b></td>";	
			echo"</tr>";
			echo"<tr>";
				echo"<td align='center' rowspan='2'><img src='C:/Escudo_de_Colombia.png' width='80' height='45'></td>";
				echo"<td align='center' colspan='3'>Solicitud De Autorizacion De Servicios De Salud</td>";					
			echo"</tr>";
			echo"<tr>";				
				echo"<td colspan='1' align='center'><table border='1'><tr><td colspan='1'><b>Numero Informe</b></td>";
				for($i=0;$i < strlen($numInf);$i++)
				{
					echo"<td>".$arrayNumInf[$i]."</td>";
				}
				echo"</tr></table></td>";
					echo"<td colspan='1' align='center'><table border='1'><tr><td><b>Fecha</b></td>";
					For($i=0;$i<strlen($fecha);$i++)
					{	
							echo"<td>".$arrayFecha[$i]."";
					}
					echo"</table>";				
					echo"<td colspan='1' align='center'><table border='1'><tr><td><b>Hora</b></td>";
					For($i=0; $i < strlen($hora);$i++)
					{	
							echo"<td>".$arrayHora[$i]."";
					}
					echo"</tr></table><td colspan='1' align='center'>";
			echo"</tr>";		
			echo"<tr>";
				echo"<td colspan='4' align='center'><font size='-1'><b>INFORMACION DEL PRESTADOR<b></td>";
			echo"</tr>";
			echo"<tr>";				
				echo"<td colspan ='2' align='center'><b>Nombre</b> ".$rowSelEmp[1]."</td>";
				echo"<td align='left' colspan='1'>NIT<input type='radio' name='rbtnNit' checked><br>";
				echo"CC <input type='radio' name='rbtnNit'></td>";
				echo"<td align='center'><table border='1' align='center'><tr>";				
				For($i=0;$i < strlen($rowSelEmp[0]);$i++)
				{	
					echo"<td>".$arrayNit[$i]."";
				}
				echo"</tr>";							
				echo"<tr><td colspan='9'><font size='-2'>Numero</td><td colspan='4'><font size='-2'>DV</td></table></tr>";			
			echo"<tr>";
				echo"<td colspan='1' align='left'><table border='1'><td><b>Codigo</b>";
				For($i=0;$i < strlen($rowSelEmp[4]);$i++)
				{	
					echo"<td>".$arrayCodPre[$i]."";
				}				
			echo"</tr></table>";
				echo"<td colspan='3' align='center'><b>Direccion del Prestador </b> $rowSelEmp[3]</td>";
			echo"</tr>";
			echo"<tr><td>";				
				echo"<table border='1'><td><b>Telefono </b></td>";
				for($i=0; $i < strlen($rowSelEmp[8]);$i++){echo"<td><font size='-1'>".$numIndi[$i]."</td>";}
				for($i=0; $i < strlen($rowSelEmp[2]);$i++)
				{
					if($arrayTelPres[$i] != '-')
					echo"<td><font size='-1'>".$arrayTelPres[$i]."</td>";
				}
				echo"<td></tr><tr><td><td colspan='5'><font size='-2'>Indicativo<td colspan='7'><font size='-2'>Numero<td></tr></table></td>";
				echo"<td colspan='1' align='center'><table border='1'><tr><td colspan='3'><b>Departamento</b></b>";
				for($i=0;$i < 2;$i++)
				{
					echo"<td>".$arrayDep[$i]."</td>";
				}
				echo"</tr></table>";
				echo"<td align='center' colspan='2'><table border='1'><tr><td><b>Municipio ";
							
				for($i=2; $i < strlen($rowSelEmp[6]);$i++ )
				{
					echo"<td>".$arrayMun[$i]."</td>";
				}
				echo"</tr></td></table>";			
				echo"<tr><td colspan='3'><b>ENTIDAD A LA QUE SE LE INFORMA<Font size='-2'</b>(Pagador)</font></b> ".$rowSelUsu[11]."";
				
				echo"<td><table border='1'><tr><td><b>Codigo</b></td>";
				for($i=0;$i < strlen($rowSelUsu[15]);$i++)
				{					
					echo"<td>".$arrayCodMin[$i]."</td>";
				}	
				echo"</tr>";
				echo"</table>";
			
			echo"</table>";
			
			echo"<table width='800' border='1' align='center'>";// Inicia Tabla Datos del Paciente
	
				echo"<table border='1' align='center' width='800'>";
			echo"<tr><td colspan='4' align='center'><b>DATOS DEL PACIENTE</tr>";
				echo"<tr>";
					echo"<td align='center'>".$rowSelUsu[3]."<td align='center'>".$rowSelUsu[4]."<td align='center'>".$rowSelUsu[5]."<td align='center'>".$rowSelUsu[6]."</td>";
				echo"</tr>";
				echo"<tr>";
					echo"<td align='center'><font size='-2'>1er Apellido</td><td align='center'><font size='-2'>2do Apellido</td><td align='center'><font size='-2'>1er Nombre</td><td align='center'><font size='-2'>2do Nombre</td>";
				echo"</tr>";
			echo"<tr>";
				echo"<td colspan='4' align='center'><FONT SIZE='-1'><B>TIPO DE IDENTIFICACION</B></FONT></td>";				
			echo"</tr>";			
			echo"<tr >";
				echo"<td>";
					if($rowSelUsu[1] == 'RC')
					{
						echo"<input type='Radio' name='rbtnTipDoc' checked>Registro Civil<br>";
					}
					else
					{
						echo"<input type='Radio' name='rbtnTipDoc'>Registro Civil<br>";
					}
					
					if($rowSelUsu[1] == 'TI')
					{
						echo"<input type='Radio' name='rbtnTipDoc' checked>Tarjeta de Identidad<br>";
					}
					else
					{
						echo"<input type='Radio' name='rbtnTipDoc'>Tarjeta de Identidad<br>";
					}					
					if($rowSelUsu[1] == 'CC')
					{
						echo"<input type='Radio' name='rbtnTipDoc' checked>Cedula de Ciudadania<br>";
					}
					else
					{
						echo"<input type='Radio' name='rbtnTipDoc'>Cedula de Ciudadania<br>";
					}
					if($rowSelUsu[1] == 'CE')
					{
						echo"<input type='Radio' name='rbtnTipDoc' checked>Cedula de Extranjeria<br>";
					}
					else
					{
						echo"<input type='Radio' name='rbtnTipDoc'>Cedula de Extranjeria<br>";
					}							
				echo"</td>";
				echo"<td>";
					if($rowSelUsu[1] == 'PA')
					{
						echo"<input type='Radio' name='rbtnTipDoc' checked>Pasaporte<br>";
					}
					else
					{
						echo"<input type='Radio' name='rbtnTipDoc'>Pasaporte<br>";
					}
					if($rowSelUsu[1] == 'AS')
					{
						echo"<input type='Radio' name='rbtnTipDoc' CHECKED>Adulto sin Identificacion<br>";
					}
					else
					{
						echo"<input type='Radio' name='rbtnTipDoc'>Adulto sin Identificacion<br>";
					}
					if($rowSelUsu[1] == 'MS')
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
					for($i=0;$i < strlen($rowSelUsu[2]);$i++)
					{
						echo"<td>".$arrayNumDoc[$i]."</td>";
					}					
					echo"</tr><tr><td align='center'colspan='".strlen($rowSelUsu[2])."'><font size='-2'>Numero de Documento de Identificacion</td></tr></table>";
				echo"<table border='1'><tr><td align='center'><font size='-2'>Fecha de Nacimiento</td>";
					for($i=0;$i < strlen($rowSelUsu[7]);$i++)
					{
						echo"<td>".$arrayFeNac[$i]."</td>";
					}
				echo"</table>";
				echo"</td>";											
			echo"</tr>";
			echo"<tr>";
				echo"<td colspan='3'><b>Direccion de Residencia Habitual:</b> <input type='text' name='txtDir' SIZE='50' value='".$rowSelUsu[8]."'
				></td>";
				echo"<td><table border='1'><tr><td><b>Telefono</b></td>";
				for($i=0;$i < strlen($rowSelUsu[9]);$i++)
				{													
					echo"<td>".$arrayTelPac[$i]."</td>";
				}
				echo"</tr></table></td>";		
			echo"</tr>";
			echo"<tr>";
					echo"<td colspan='2' align='center'><table border='1'><tr><td><b>Departamento</b>";
					for($i=0; $i < strlen($rowSelUsu[10]);$i++)
					{
						echo"<td>".$arrayDepPac[$i]."</td>";
					}
					echo"</tr></table></td>";
					echo"<td colspan='2' align='center'><table border='1'><tr><td><b>Municipio</td>";
					for ($i=2; $i < strlen($rowSelUsu[12]);$i++)
					{
						echo"<td>".$arrayMunPac[$i]."</td>";
					}
					
					echo"</tr></td></table>";
					echo"<tr><th colspan='2'>Telefono celular</td><td colspan='2'><b>Correo Electronico </b> NO TIENE</th></tr>";
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
				if(isset($rowIps) and $rowIps[13] == '2')
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
			
			//Tabla De INFORMACION DE LA ATENCION Y SERVICIOS SOLICITADOS
			
			echo"<table width='800' border='1' align='center'>";
				echo"<tr><td colspan='5' align='center'><font><b>INFORMACION DE LA ATENCION Y SERVICIOS SOLICITADOS</td></tr>";
					echo"<tr><td colspan='3' align='center'><font size='-1'><b>Origen de la atencion</td>";
					echo"<td><font size='-1'><b>Tipo de Servicios Solicitados</td>";
					echo"<td><font size='-1'><b>Origen de la atencion</td></tr>";
				if(isset($rbtnOriSol) and $rbtnOriSol=='13')
					echo"<tr><td><input type='radio' name='rbtnOriSol' value='13' checked><font size='-2'>Enfermedad General<br>";
				else
					echo"<tr><td><input type='radio' name='rbtnOriSol' value='13'><font size='-2'>Enfermedad General<br>";
				if(isset($rbtnOriSol) and $rbtnOriSol=='14')
					echo"<input type='Radio' name='rbtnOriSol' value='14' checked><font size='-2'>Enfermedad Profesional";										
				else
					echo"<input type='Radio' name='rbtnOriSol' value='14'><font size='-2'>Enfermedad Profesional";
				if(isset($rbtnOriSolAcc) and $rbtnOriSolAcc=='01')
					echo"<td><input type='radio' name='rbtnOriSolAcc' value='01' checked><font size='-2'>Accidente de Trabajo<br>";
				else
					echo"<td><input type='radio' name='rbtnOriSolAcc' value='01'><font size='-2'>Accidente de Trabajo<br>";
				if(isset($rbtnOriSol) and $rbtnOriSol=='02')
					echo"<input type='radio' name='rbtnOriSol' value='02' checked><font size='-2'>Accidente de Transito";
				else
					echo"<input type='radio' name='rbtnOriSol' value='02'><font size='-2'>Accidente de Transito";
				if(isset($rbtnOriSol) and $rbtnOriSol=='06')
					echo"<td><input type='radio' name='rbtnOriSol' value='06' checked><font size='-2'>Evento Catastrofico";
				else
					echo"<td><input type='radio' name='rbtnOriSol' value='06'><font size='-2'>Evento Catastrofico";
				
				if(isset($rbtnTipSor) and $rbtnTipSor == '1')
					echo"<td><input type='radio' name='rbtnTipSor' value='1' checked><font size='-2'>Posterior a la retencion inicial de urgencias<br>";
				else
					echo"<td><input type='radio' name='rbtnTipSor' value='1'><font size='-2'>Posterior a la retencion inicial de urgencias<br>";
				if(isset($rbtnTipSor) and $rbtnTipSor=='2')
					echo"<input type='Radio' name='rbtnTipSor' value='2' checked><font size='-2'>Servicios Electivos";
				else
					echo"<input type='Radio' name='rbtnTipSor' value='2'><font size='-2'>Servicios Electivos";
				
				if(isset($rbtnPriAte) and $rbtnPriAte=='1')
					echo"<td><input type='Radio' name='rbtnPriAte' value='1' checked><font size='-2'>Prioritaria<br>";
				else			
					echo"<td><input type='Radio' name='rbtnPriAte' value='1'><font size='-2'>Prioritaria<br>";				
				if(isset($rbtnPriAte) and $rbtnPriAte=='2')
					echo"<input type='Radio' name='rbtnPriAte' value='2' checked><font size='-2'>No Prioritaria";
				else
					echo"<input type='Radio' name='rbtnPriAte' value='2'><font size='-2'>No Prioritaria";
				echo"</tr>";
				
				echo"<tr><td colspan='5'><font size='-1'><b>Ubicacion del Paciente al Momento de la Solicitud de Autorizacion</td></tr>";
				echo"<tr>";
					echo"<td><input type='Radio' name='rbtnUbiPac' value='1'><font size='-2'>Consulta Externa<br>";
					echo"<input type='Radio' name='rbtnUbiPac'value='2'><font size='-2'>Urgencias</td>";
					echo"<td><input type='Radio' name='rbtnUbiPac' value='3'><font size='-2'>Hospitalizacion</td>";
					echo"<th colspan='2'>Servicio <input type='text' name='txtServ'></td>";
					echo"<th>Cama </th>";									
			echo"</tr></table>";
			
			//Inicia La table Informacion clinica
			/*	$codCupsServicio = explode("-",$ddServ);
				$arrayServicio = Div($codCupsServicio);
				$arrayCantidad = Div($txtCantidad);*/
			echo"<table border='1' width='800' align='center'>";
			echo"<tr><td align='right'><b>Manejo Integral Segun Guia de </td><td><input type='text' name='txtGuia'></td></tr>";
			echo"<tr>";

				echo"<table border='1' width='800'align='center'>";
					echo"<tr><td><b>Codigo CUPS</td><td><b>Cantidad</td><td><b>Descripcion</td>";					
					echo"</tr>";					
					echo"<tr><td><table border='1' align='center'><tr>";
					for($i=0;$i < strlen($codCupsServicioPpal[0]);$i++){echo"<td>".$arrayServicioPpal[$i]."</td>";} 
					echo"</tr></table></td>";					
					echo"<td><table border='1' align='center'><tr>";
					for($i=0;$i < strlen($txtCantidadPpal);$i++){echo"<td>".$arrayCantidadPpal[$i]."</td>";}
					echo"</tr></table></td>";
					echo"<td>".$codCupsServicioPpal[1]."</td>";
					echo"<input type='hidden' name='wcodigoPpal' value=".$codCupsServicioPpal[0].">";
					echo"<input type='hidden' name='wDescPpal' value=".$codCupsServicioPpal[1].">";
					echo"<input type='hidden' name='wCantPpal' value=".$txtCantidadPpal.">";
					echo"</tr>";
					$i = 0;
					if(isset($wotroServicio))
					{					
						echo"<input type='hidden' name='wwotroServicio' value=".$wotroServicio.">";
					
						for($i=1;$i <= $wotroServicio;$i++)
						{					
							$codigo = explode("-",$ddServ[$i]);
							
							$codCupsServicio[$i] = $codigo[0];						
							$arrayServicio = Div($codCupsServicio[$i]);						
							$arrayCantidad = Div($txtCantidad[$i+1]);
							echo"<input type='hidden' name='wddServ[".$i."]' value='".$ddServ[$i]."'>";
							echo"<input type='hidden' name='wtxtCantidad[".$i."]' value=".$txtCantidad[$i+1].">";						
	
							if($codCupsServicio[$i] != '')
							{											
								echo"<tr><td><table border='1' align='center'><tr>";
								for($j=0;$j < '7';$j++)
									{echo"<td>".$arrayServicio[$j]."</td>";} 
								echo"</tr></table></td>";							
								echo"<td><table border='1' align='center'><tr>";
								for($j=0;$j < '3';$j++){echo"<td>".$arrayCantidad[$j]."</td>";} 
								echo"</tr></table></td>";					
								echo"<td>".$codigo[1]."</td>";							
								echo"<input type='hidden' name='wcodigo[".$otroServicio."]' value=".$codigo[0].">";
								
					
														
							}
						
						}//Fin while($i < $otroServicio)
					}				
					echo"<tr><td><b>Justificacion Clinica </b><td colspan='3'><textArea name='txtJust' rows='3' cols='80'></textarea></td>";
				echo"</table>";			
			echo"</tr></table>";
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
			echo"<table border='1' align='center' width='800'>";
						echo"<tr>";				
				echo"<td colspan='5'><table border='1' align='center'>";
				echo"<tr><td align='center'><b>Impresion Diagnostica</td><td align='center'><b>Codigo CIE 10<td colspan='3'align='center'><b>Descripcion</tr>";

					echo"<tr><td><font size='-1'>Diagnostico Principal<td><table border='1' align='center'>";
						for($i=0; $i < strlen($codDiagPrincipal[0]);$i++)
						{		
							echo"<td>".$arrayDiagPrinc[$i]."</td>";
						}
					echo"</table>";
					echo"<td>".$codDiagPrincipal[1]."</td>";
					echo"<tr><td><font size='-1'>Diagnostico Relacionado 1<td><table border='1' align='center'>";	
						for($i=0; $i < strlen($codDiagRelac1[0]);$i++)
						{		
							echo"<td>".$arrayDiagPrinc1[$i]."</td>";
						}
					echo"</table>";
					echo"<td>".$codDiagRelac1[1]."</td>";
					
					echo"<tr><td><font size='-1'>Diagnostico Relacionado 2<td><table border='1' align='center'>";
					for($i=0; $i < strlen($codDiagRelac2[0]);$i++)
						{		
							echo"<td>".$arrayDiagPrinc2[$i]."</td>";
						}
					echo"</table>";
					echo"<td>".$codDiagRelac2[1]."</td>";
					echo"<tr><td><font size='-1'>Diagnostico Relacionado 2<td><table border='1' align='center'>";
					for($i=0; $i < strlen($codDiagRelac3[0]);$i++)
						{		
							echo"<td>".$arrayDiagPrinc3[$i]."</td>";
						}
					echo"</table>";
					echo"<td>".$codDiagRelac3[1]."</td>";
				
				echo"</table></td>";				
			echo"</tr>";
		
		echo"</table>";//FIN Tabla de la informacion de la atencion
		$nomProfesional = explode(":",$ddPrefesional);
		echo"<input type='hidden' name='wddPrefesional' value='".$ddPrefesional."'>";
		
		//echo"Nombre del profesional :$nomProfesional[0]";
		echo"<table border='1' align='center' width='800'>";
				echo"<tr>";
					echo"<td align='center' colspan='4'><font size='-1'><b>INFORMACION DE LA PERSONA QUE REPORTA</b></td>";
				echo"</tr>";
				echo"<tr>";
					echo"<td><b>Nombre de quien Solicita: </b>".$nomProfesional[0]."<br>Registro Profesional: ".$nomProfesional[1]."</td>";
					echo"<td><table border='1'><tr><td>Telefono </td>";	
					for($i=0; $i < 5;$i++){echo"<td>".$numIndi[$i]."";}		
					for($i=0; $i < strlen($rowSelEmp[2]);$i++){if($arrayTelPres[$i]!='-')echo"<td>".$arrayTelPres[$i]."";}
					echo"</tr>";
					echo"<tr><td colpan='1'></td><td colspan='5'>Indicativo<td colspan='7'>Numero<td colspan='6'>Extension</tr></table>";
				echo"</tr>";					
				echo"<tr>";
					echo"<td><b>Cargo o Actividad:</b> MEDICO</td>";
					echo"<td><table border='1'><td><b>Telefono Celular </b>";
					for($i=0; $i < strlen($rowSelEmp[7]);$i++)
					{
						echo"<td>".$telReport[$i]."</td>";
					}
					
				echo"</tr></table>";
				echo"</tr>";
				echo"<tr>";
					echo"<td colspan='4' align='center'><input type='radio' name='rbtnGrabar' value='1' onclick='enter()'>Grabar</td>";
				echo"</tr>";
				echo"<tr><td colspan='4' align='left'><font size='-2'>MPS-SAS V5.0 2008-07-11</tr>";
			echo"</table>";
		
	}//FIN if($btnGenerar != '1')
}// FIN FORM
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
		$splitted = '';
		for($i=0;$i < strlen($string);$i++)
		{ 
			$splitted[$i] = $string[$i];
    	}

		return($splitted);
    }
?>

</body>

</html>