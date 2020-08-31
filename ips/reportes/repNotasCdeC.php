<html>
	<head>

		<title> Reporte de Notas Credito Por centro de costos</title>

	</head>

	   <SCRIPT LANGUAGE="JavaScript1.2">
		   <!--
		   function onLoad() {
		   	loadMenus();
		   }
		   //-->

		   function enter()
		   {
		   	document.formNotasCdeC.submit();
		   }

		</script>
		<body>

		<?php
include_once("conex.php");
		/************************************************************************
		*     	Reporte de Notas Credito por Centro de costos y Concepto		*
		*														*
		************************************************************************/

		//==================================================================================================================================
		//PROGRAMA						:Reporte de Notas Credito por Centro de Costos y Concepto
		//AUTOR							:Juan Esteban Lopez Aguirre
		//FECHA CREACION				:Marzo de 2008
		//FECHA ULTIMA ACTUALIZACION 	:
		//DESCRIPCION					:El reporte surgio como complemento a un reporte planteado de la facturacion por centro(repCentroCostos.php)
		//								 El reporte muestra las notas credito con el centro de costos y el concepto al cual fueron ligadas
		//MODIFICACIONES:
		//-------------------------------------------------------------------------------------------------------------------------------------------
		//	-->	2013-12-24, Jerson trujillo.
		//		El maestro de conceptos que actualmente es la tabla 000004, para cuando entre en funcionamiento la nueva facturacion del proyecto
		//		del ERP, esta tabla cambiara por la 000200; Entonces se adapta el programa para que depediendo del paramatro de root_000051
		//		'NuevaFacturacionActiva' realice este cambio automaticamente.
		//=================================================================================================================================

		//Declaracion de variables
		$feInicial = date("Y-m-d");
		$feFinal = date("Y-m-d");
		$hora = (string)date("H:i:s");
		$cal="calendario('feInicial','1')";
		$entidad = '		CLINCA DEL SUR		';
		$i = 1;
		$k = 1;
		$totalGen=0;



		include_once("root/comun.php");


		$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
		$wbasedato = $institucion->baseDeDatos;

		//---------------------------------------------------------------------------------------------
		// --> 	Consultar si esta en funcionamiento la nueva facturacion
		//		Fecha cambio: 2013-12-24	Autor: Jerson trujillo.
		//---------------------------------------------------------------------------------------------
		$nuevaFacturacion = consultarAliasPorAplicacion($conex, $wemp_pmla, 'NuevaFacturacionActiva');
		//---------------------------------------------------------------------------------------------
		// --> 	MAESTRO DE CONCEPTOS:
		//		- Antigua facturacion 	--> 000004
		//		- Nueva facturacion 	--> 000200
		//		Para la nueva facturacion cuando esta entre en funcionamiento el maestro
		//		de conceptos cambiara por la tabla 000200.
		//		Fecha cambio: 2013-12-24	Autor: Jerson trujillo.
		//----------------------------------------------------------------------------------------------
		$tablaConceptos = $wbasedato.(($nuevaFacturacion == 'on') ? '_000200' : '_000004');
		//----------------------------------------------------------------------------------------------

		echo"<form action='repNotasCdeC.php' method='get' name='formNotasCdeC'>";

		echo "<input type='hidden' name='wemp_pmla' value='".$wemp_pmla."'>";

		session_start();
		if (!isset($_SESSION['user']))
		{
			echo "ERROR";
		}
		else
		{
			if(!isset($btnConsultar))
			{
				echo"<table border='2' align='center'>";
				echo"<tr>";
				echo"<td align='center'><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' width='500' height='100'></td>";
				echo"<td bgcolor='003366' align='center'><font color='white' size='5'>Reporte de Notas Credito Por Centro de Costos y Concepto</font></td>";
				echo"</tr>";
				echo"<tr bgcolor='dddddd'>";
				echo"<td align='center'>Fecha Inicial:";
				campoFechaDefecto("txtFeInicial", $feInicial);
				echo"</td>";
				?>
					<script type="text/javascript">//<![CDATA[
					Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'txtFeInicial',button:								'btnCalendar1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
					//]]>
				</script>
				<?php
				echo"<td align='center'>Fecha Final:";
				campoFechaDefecto("txtFeFinal", $feFinal);
				echo"</td>";
				echo"</tr>";
				?>
					<script type="text/javascript">//<![CDATA[
					Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'txtFeFinal',button:								'btnCalendar2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
					//]]>
				</script>
				<?php

				$q = "SELECT Ccocod, Ccodes"
					." FROM ".$wbasedato."_000003"
					." ORDER BY Ccocod";

				$res = mysql_query($q,$conex);
				$num = mysql_num_rows($res);

				echo"<tr bgcolor='dddddd'>";
				echo"<td>Centro De Costos : ";
				echo"<select name='ddCdeC'>";
				echo"<option>% - Todos los centro de costos</option>";

				for($i=1;$i<=$num;$i++)
				{
					$row = mysql_fetch_row($res);

					if ($ddCdeC!=$row[0]."-".$row[1])
					{
						echo "<option>".$row[0]."-".$row[1]."</option>";

					}//FIN if ($ddCdeC!=$row[0]."-".$row[1])

				}//Fin for($i=0;$i<=$num;$i++)
				echo"</select>";
				echo"</td>";

				$q = "Select grucod, grudes"
					 ." From ".$tablaConceptos.""
					 ." Order by grucod";

				$res = mysql_query($q,$conex);
				$num = mysql_num_rows($res);

				echo"<td>Concepto : ";


				echo"<select name='ddConcepto'>";
				echo"<option>% - Todos Los Conceptos </option>";

				for($i=1;$i<=$num;$i++)
				{
					$row = mysql_fetch_row($res);

					if ($ddConcepto!=$row[0]." - ".$row[1])
					{
						echo"<option>".$row[0]."-".$row[1]."</option>";

					}//FIN if ($ddCdeC!=$row[0]."-".$row[1])


				}

				echo"</select>";
				echo"</td>";
				echo"</tr>";
				echo"<tr bgcolor='dddddd' align='center'><td colspan='2'><input type='submit' name='btnConsultar' value='Consultar' onclick='enter()'></td></tr>";

				echo"</table>";



			}//Fin if(!isset($btnConsultar))
			else
			{
					$tempCdeC = explode('-',$ddCdeC);
					$tempConcepto = explode('-',$ddConcepto);

					echo"<table border='0' width='100%' align='center'>";
					echo"<tr>";
					echo"<td><b>FACTURACION: </b>CLINICA DELSUR</td>";
					echo"<td align='right'><b>FECHA: </b>".$feInicial."</td>";
					echo"</tr>";
					echo"<tr>";
					echo"<td><b>PROGRAMA: </b>Notas Credito por Centro de Costos</td>";
					echo"<td align='right'><b>HORA: </b>".$hora."</td>";
					echo"</tr>";
					echo"</table>";
					echo"&nbsp;";
					echo"&nbsp;";
					echo"<table border='0' align='center' width='100%'>";
					echo"<tr>";
					echo"<td align='center' colspan='2'><h1>".$entidad."</h1></td>";
					echo"</tr>";
					echo"<tr>";
					echo"<td align='center' colspan='2'><b><a name='Arriba'>Reporte De Notas Credito por Centro de Costos y Concepto</a></b></td>";
					echo"</tr>";
					echo"<tr>";
					echo"<td align='right'><b>Fecha Inicial :</b>".$txtFeInicial."</td>";
					echo"<td align='left'><b>Fecha Final :</b>".$txtFeFinal."</td>";
					echo"</tr>";
					echo"</table>";

					echo"&nbsp;";
					echo"<a href='repNotasCdeC.php'><center>VOLVER</center></a>";
					echo"&nbsp;";
					$query="SELECT cs65.fdecco, cs65.fdecon, cs65.Fdevco, cs03.ccodes, cs04.grudes"
						  ." FROM ".$wbasedato."_000020 cs20, ".$wbasedato."_000021 cs21, ".$wbasedato."_000065 cs65, ".$wbasedato."_000003 cs03, ".$tablaConceptos." cs04"
						  ." WHERE cs20.renfec between '".$txtFeInicial."' AND '".$txtFeFinal."'"
						  ." AND cs20.renest = 'on'"
						  ." AND cs20.renfue = '27'"
	   					  ." AND cs21.rdefue = cs20.renfue"
						  ." AND cs21.rdenum = cs20.rennum"
						  ." AND cs21.rdecco = cs20.rencco"
						  ." AND cs21.Rdenum = cs65.Fdedoc"
						  ." AND cs21.Rdefue = cs65.fdefue"
						  ." AND cs03.Ccocod = cs65.fdecco"
						  ." AND cs65.fdecon = cs04.grucod"
						  ." AND cs65.fdecon != '9301'"
						  ." AND cs65.fdecon != '9302'"
  						  ." AND cs65.fdecon != '9303'"
						  ." AND cs65.fdecon != '9304'"
  						  ." AND fdecco like '".trim($tempCdeC[0])."'"
						  ." AND fdecon like '".trim($tempConcepto[0])."'"
						  ." order by cs65.fdecco, cs65.fdecon";

					$res = mysql_query($query,$conex);
					$num = mysql_num_rows($res);
					$row = mysql_fetch_array($res);

					if ($num == '0')
					{
						echo"<table align='center' border='0' bordercolor='#000080' width='500' style='border:solid'>";
						echo"<tr><td align='center'colspan='2'><b><font size='3' color='#000080'>La consulta entre las fechas ".$txtFeInicial." y ".$txtFeFinal." no Contienen Ningun Documento	Asociado</font><b></td>";
						echo"</tr>";
						echo"</table>";
					}
					else
					{
						echo"<table align='center' border='0'>";

						while ($i <= $num)
						{

							echo"<tr><td colspan='3' bgcolor='navy'><font color='white'>Centro De Costos : ".$row[0]." - ".$row[3]."</font></td></tr>";
							echo"<tr bgcolor='003366'>";
							echo"<td align='center'><b><font color='#ffffff'>Codigo Concepto</font></b></td>";
							echo"<td align='center'><b><font color='#ffffff'>Descripcion Concepto</font></b></td>";
							echo"<td align='center'><b><font color='#ffffff'>Valor Concepto</font></b></td>";
							echo"</tr>";
							$tmpCdeC = $row[0];
							$totalCdC = 0;

							$totalConcepto = 0;

							while ($tmpCdeC == $row[0])
							{
								if (is_int($k/2))
								{
									$color='#DDDDDD';
									$k = $k + 1;
								}
								else
								{
									$color='#FFFFFF';
									$k = $k + 1;

								}// Fin if (is_int($k/2))
								echo"<tr>";
								$tmpConcepto = $row[1];

								echo"<td align='center' bgcolor=$color><font size='2'>".$row[1]."</font></td>";
								echo"<td align='left' bgcolor=$color><font size='2'>".$row[4]."</font></td>";
								while ($tmpConcepto == $row[1])
								{
									$totalConcepto += $row[2];
									$totalCdC += $row[2];
									$row = mysql_fetch_array($res);
									$i = $i + 1;
								}

								echo"<td align='center' bgcolor=$color><font size='2'>".number_format($totalConcepto,0,'.',',')."</font></td>";
								echo"</tr>";
								$totalConcepto=0;
//								$row = mysql_fetch_array($res);


							}
							echo"<tr>";
							echo"<td colspan='2' bgcolor='silver'>Total Centro de Costos</td>";
							echo"<td bgcolor='silver'>".number_format($totalCdC,0,'.',',')."</td>";
							echo"</tr>";
							$totalGen += $totalCdC;
						}
						echo"<tr>";
						echo"<td colspan='2' bgcolor='silver'>Total General</td>";
						echo"<td bgcolor='silver'>".number_format($totalGen ,0,'.',',')."</td>";
						echo"</tr>";
						echo"</table>";
						echo"&nbsp;";
						echo"<center><a href='#Arriba'>ARRIBA</a></center>";
					}

			}//Fin if(!isset($btnConsultar))
		}//Fin if(session_is_registered("user"))




		?>


	</body>
</html>
