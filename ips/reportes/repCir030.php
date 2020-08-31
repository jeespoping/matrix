<html>
	<head>
		<title>Reporte De La Circular 030</title>
		<label>
			&nbsp
			&nbsp
			<div align='right'>Desarrollado por Juan Esteban Lopez A.</div>
			&nbsp
			&nbsp
		</label>
		
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
		
		<script language="JavaScript1.2">
			function enter()
			{
				document.forma.submit();
			}
		</script>
		<tr

	</head>

	<body>
	
	<?php
include_once("conex.php");
											/********************************************************
											*     	Reporte de Facturacion por Empresas				*
											*														*
											*********************************************************/
			
			//==================================================================================================================================
			//PROGRAMA						:Reporte de Facturacion por Empresas
			//AUTOR							:Juan Esteban Lopez Aguirre
			//FECHA CREACION				:Agosto de 2008
			//FECHA ULTIMA ACTUALIZACION 	:
			//DESCRIPCION					:Este reporte lista los el numero de usuarios ingresados en la clinica de dos formas. La primera son los
			//								 los facturados que muestra la cantidad de usuarios vistos por cada medico y la facturacion de cada medico			   //								y la segunda es los usuarios ingresados por citas.
			//MODIFICACIONES:
			//==================================================================================================================================
	
	//Declaracion de Variables
	$empresa = 'clisur';
	if ($empresa=='clisur')
	{
		$entidad = "CLINCA DEL SUR";
	}//Fin Empresa
	$i = 0;
	$totalConsultas = 0; // Total de las consultas realizadas
	$totalFacEsp = 0; // Total de la facturacion por Medico
	$totalDias = 0;
	$oportunidad = 0;
	$totalOportunidad = 0;

	if (isset($bandTipoRep))
	{
			$bandTipoRep; // Define el tipo de reporte para la seleccion del radio button		
	}
	



	session_start() ;
	if (!isset($_SESSION['user']))
	{
		echo"Error";
		
	}
	else
	{
	
	if (!isset($fechaInicial))
	{
		$fechaInicial = date("Y-m-d");
		$fechaFinal = date("Y-m-d");
		
	}
		
	if(!isset($btnGenerar))	
	{

		
		echo"<form action='repCir030.php' method='post'> ";	
		echo"<table align='center' border='2'>";
		echo"<tr align='center'>";
			echo"<td><img src='/matrix/images/medical/pos/logo_".$empresa.".png' WIDTH=500 HEIGHT=100'></td>";
			echo"<td  bgcolor='003366'><font size='+3' color='white'>Reporte de Prestacion de Servicios</td>";
		echo"</tr>";
		echo"<tr bgcolor='DDDDDD'>";
			echo"<td colspan='2'>";
			echo"<div align='center'>Fecha Inicial: <input type='text' name='txtFeInicial' readonly='readonly' value='".$fechaInicial."' size='10'>
				<input type='button' name='btnCalendarFeIni' value='...' size='10'>";
				?>
				<script type="text/javascript">//<![CDATA[
				Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'txtFeInicial',button:		
				'btnCalendarFeIni',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});//]]>
				</script>
				<?php
			echo"&nbsp &nbsp &nbsp &nbsp Fecha Final: <input type='text' name='txtFeFinal' readonly='readonly' value='".$fechaFinal."' size='10'>
				<input type='button' name='btnCalendarFeFin' value='...' ></div>";
				?>
				<script type="text/javascript">//<![CDATA[
				Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'txtFeFinal',button:		
				'btnCalendarFeFin',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});//]]>
				</script>
				<?php


		echo"<tr bgcolor='DDDDDD'>";
			echo"<td colspan='2' align='center'>";
			echo"<input type='submit' name='btnGenerar' value='Generar'>";
			echo"</td>";
		echo"</tr>";
		if (!isset($bandTipoRep))
		{
			echo"<tr>";
			echo"<td bgcolor='DDDDDD' colspan='2' align='center'><input type='radio' name='rbtnFact' value='facturados'>Ingresos Facturados<input type=
			'radio' name='rbtnFact' value='citas'>Ingresos por Citas</td>";
			echo"</tr>";
		
		}
		else
		{
			if($bandTipoRep == '1')
			{
				echo"<tr>";
				echo"<td bgcolor='DDDDDD' colspan='2' align='center'><input type='radio' name='rbtnFact' value='facturados' checked>Ingresos Facturados				   <input type='radio' name='rbtnFact' value='citas'>Ingresos por Citas</td>";
				echo"</tr>";				
			}
			if($bandTipoRep == '2')
			{
				echo"<tr>";
				echo"<td bgcolor='DDDDDD' colspan='2' align='center'><input type='radio' name='rbtnFact' value='facturados'>Ingresos Facturados				   <input type='radio' name='rbtnFact' value='citas' checked>Ingresos por Citas</td>";
				echo"</tr>";				
			}
		}//Fin Else if (!isset($bandTipoRep))
		
		
		echo"</table>";		
		echo"</form>";
	}//Fin Validacion Ingreso de Datos
	
	else
	{
		if(($txtFeInicial=='' or $txtFeFinal=='' or $txtFeFinal < $txtFeInicial))
		{
			echo"<table align='center' border='0' bordercolor='#000080' width='500' style='border:solid'>";
			echo"<tr><td align='center'colspan='2'><b><font size='3' color='#000080'> Error En El Rango De Fecha Seleccionada</font><b></td>";		
			echo"</tr>";
			echo"</table>";	
			exit(0);		
			
		}//Fin If Fecha		
		echo"<table border='0' width='100%'>";
		echo"<tr>";
		echo"<td><b>Departamento: </b> Jefatura de Enfermeria</td>";
		echo"<td align='right'><b>Fecha: </b>".date('Y-m-d')."</td>";
		echo"</tr>";		
		echo"<tr>";
			echo"<td><b>Programa: </b>Reporte Circular 030</td>";
			echo"<td align='right'><b>Hora: </b>".(string)date("H:i:s")."</td>";
		echo"</tr>";
		echo"<tr>";
		echo"<td colspan='2' align='center'><h1>".$entidad."</h1></td>";
		echo"</tr>";
		echo"<tr>";
		echo"<td colspan='2' align='center'><b>Fecha Inicial: </b>".$txtFeInicial."<b>Fecha Final: </b>".$txtFeFinal."</td>";
		echo"</tr>";
		
		
		
		

		
		if ($rbtnFact == 'facturados')
		{
			$bandTipoRep = '1';
			echo"<tr>";
				echo"<td align='center' colspan='2'><h3>Reporte de Pacientes Facturados por Especilidad</h3></td>";
			echo"</tr>";
			echo"<br> &nbsp";//Lineas Verticales En blanco 	
echo"<tr><td colspan='2' align='center'><a href=repCir030.php?fechaInicial=".$txtFeInicial."&amp;fechaFinal=".$txtFeFinal."&amp;bandTipoRep=".$bandTipoRep.">VOLVER<a></td></tr>";
			echo"</table>";
			/*echo "<tr><td align=right><font size=2><A href='est_infgnl.php?wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;wbasedato=".$wbd."&amp;bandera='1'>VOLVER</A></font></td></tr>";*/
			
			
			//,
			$qSelect = "select cs51.mednom, cs51.medcod, cs51.meddoc, cs51.medesp, count(*), sum(cs18.fenval)"
		  		      ."  from ".$empresa."_000051 cs51, ".$empresa."_000065 cs65,".$empresa."_000018 cs18 "
				      ." where cs65.fdeest = 'on' "
				  	     ."and cs51.medest = 'on' "
				  	     ."and cs65.fecha_data between '".$txtFeInicial."' and '".$txtFeFinal."' "
					     ."and cs65.fdeter = cs51.meddoc "
					     ."and cs65.fdefue ='20' "
					     ."and cs18.fenffa = '20' "
					     ."and cs18.fenfac = cs65.fdefac "
				    ."group by cs51.meddoc "
				    ."order by cs51.medesp";
			
			list($resSelect,$numSelect) = Consulta($qSelect);// Funcion Para ejecutar los Query
			
						
			if($numSelect=='0')
			{
				echo"<table align='center' border='0' bordercolor='#000080' width='500' style='border:solid'>";
				echo"<tr><td align='center'colspan='2'><b><font size='3' color='#000080'>La consulta entre las fechas ".$txtFeInicial." y ".
				$txtFeFinal." No Se Encontro Ningun Resultado</font><b></td>";							
				echo"</tr>";
				echo"</table>";	
				exit(0);		
			
			}//Fin If Fecha
		
			$qRes = mysql_fetch_row($resSelect);
							
			echo"<table border='0' align='center'>";
		
			while($i < $numSelect)
			{		
			
				echo"<tr bgcolor='003366'>";
				echo"<td colspan='3'><font color='white'>Servicio O Especialidad:".$qRes[3]." </font></td>";
				echo"</tr>";

				echo"<tr bgcolor='006699'>";
				echo"<td><font color='white'>Nombre Medico</font></td>";
				echo"<td><font color='white'>Cantidad</td>";
				echo"<td><font color='white'>Valor Facturado</td>";
				echo"</tr>";
	
				$esp = $qRes[3];
				$k=0;
				
				while($esp == $qRes[3])
				{
					
					if (is_int ($k/2))
					{
						$color='#FFFFFF';
						$k=$k+1;
					}
					else
					{
						$color='#DDDDDD';						   								
						$k=$k+1;
					}// Fin IF (is_int ($k/2))
					
					echo"<tr bgcolor=".$color.">";
					echo"<td align='left'>".$qRes[0]."</font></td>";
					echo"<td align='center'>".$qRes[4]."</td>";
					echo"<td align='right'>".number_format($qRes[5],0,'.',',')."</td>";
					echo"</tr>";
					
					$totalConsultas += $qRes[4];
					$totalFacEsp += $qRes[5];
					$qRes = mysql_fetch_row($resSelect);
					$i += 1;
												
						
				}//fin while especialidad
				
				echo"<tr bgcolor='silver' bordercolor='blue'>";
				echo"<td align='right'><b>TOTAL</b></td>";
				echo"<td align='center'><b>".$totalConsultas."</b></td>";
				echo"<td align='right'><b>".number_format($totalFacEsp,0,'.',',')."</b></td>";
				echo"</tr>";
				$totalConsultas = 0;
				$totalFacEsp = 0;
		
		}// Fin while
			
		echo"</table>";	
			
		}//Din $rbtnFact == Facturados
		else
		{
			if ($rbtnFact == 'citas')
			{
				$bandTipoRep = '2';	
				$empresa = 'citascs';
				
				echo"<tr>";
				echo"<td align='center' colspan='2'><h3>Reporte de Pacientes Con Citas por Especilidad</h3></td>";
				echo"</tr>";
				echo"</tr>";
				echo"<br> &nbsp";//Lineas Verticales En blanco 	
				echo"<tr><td colspan='2' align='center'><a href=repCir030.php?fechaInicial=".$txtFeInicial."&amp;fechaFinal=".$txtFeFinal."&amp;bandTipoRep=".$bandTipoRep.">VOLVER<a></td></tr>";	
				echo"</table>";
				
				echo"<table border='0' align='center'>";
				
				/*$qMedicos = "create temporary table if not exists tmpMed as "
							."SELECT ccs10.codigo, ccs10.Descripcion "
							  ."FROM ".$empresa."_000010 ccs10 "
						  ."GROUP BY cs10.Codigo"
					  	  ."ORDER BY cs10.Codigo";*/
					  	  
$qCitas = "SELECT ccs09.Cod_equ, ccs09.Cod_exa, ccs09.Cedula, ccs09.Nit_res, ccs11.Descripcion, count(*), sum(datediff(ccs09.fecha,ccs09.fecha_data))"
					     ."  FROM ".$empresa."_000009 ccs09, ".$empresa."_000011 ccs11"
					     ." WHERE ccs09.fecha between '".$txtFeInicial."' and '".$txtFeFinal."'"
					     ."   AND ccs09.Cod_exa = ccs11.Codigo"
					     ."   AND ccs09.Cod_equ = ccs11.Cod_equipo "
				       ."GROUP BY ccs09.Cod_exa,ccs09.Cod_equ "
					   ."ORDER BY ccs09.Cod_exa ";

				$qSelectCx = "SELECT ccs01.Cod_med, ccs01.Cod_exa, ccs01.Nit_resp, ccs08.Nombre, count(*), sum(datediff(ccs01.Fecha,ccs01.Fecha_Data))"
							."  FROM ".$empresa."_000001 ccs01, ".$empresa."_000008 ccs08"
					        ." WHERE ccs01.fecha between '".$txtFeInicial."' and '".$txtFeFinal."'"
							."   AND ccs01.Cod_med = ccs08.Codigo "
							."GROUP BY ccs01.cod_med "
							."ORDER BY ccs01.Cod_med";
					
				//list($resSelMedicos,$numSelMedico) = Consulta($qMedicos);// Funcion Para ejecutar los Query $qMedicos


 				
				list($resSelCitas,$numSelCitas) = Consulta($qCitas);// Funcion Para ejecutar los Query $qCitas
				
				$qResCitas = mysql_fetch_row($resSelCitas);
				
				//list($resSelCx,$numSelCx) = Consulta($qSelectCx);// Funcion Para ejecutar los Query $qSelectCx
				
				while($i < $numSelCitas)
				{
										
					echo"<tr bgcolor='003366'>";
					echo"<td colspan='4'><font color='white'>Servicio O Especialidad:".$qResCitas[4]." - ".$qResCitas[1]." </font></td>";
					echo"</tr>";
					echo"<tr bgcolor='006699'>";
					echo"<td><font color='white'> Codigo Medico </font></td>";
					echo"<td><font color='white'> Cantidad </td>";
					echo"<td><font color='white'> Dias Asignados </td>";
					echo"<td><font color='white'> Oportunidad </td>";
					echo"</tr>";

							
					$esp = $qResCitas[1];
					$k=0;
				
					while($esp == $qResCitas[1])
					{
						
						if (is_int ($k/2))
						{
							$color='#DDDDDD';
							$k=$k+1;
						}
						else
						{
							$color='#FFFFFF';						   								
							$k=$k+1;
						}// Fin IF (is_int ($k/2))
						
						echo"<tr bgcolor=".$color.">";
						echo"<td align='left'>".$qResCitas[0]."</font></td>";
						echo"<td align='center'>".$qResCitas[5]."</td>";
						echo"<td align='center'>".$qResCitas[6]."</td>";
											
						$totalConsultas += $qResCitas[5];
						$totalDias += $qResCitas[6];
						$oportunidad = $qResCitas[6] / $qResCitas[5];
						
						$totalOportunidad +=$oportunidad;
						
						echo"<td align='center'>".number_format($oportunidad,2,'.',',')."</td>";
						
						echo"</tr>";
						$qResCitas = mysql_fetch_row($resSelCitas);
						$i += 1;
							
					}//fin while especialidad
					
					echo"<tr bgcolor='silver' bordercolor='blue'>";
					echo"<td align='right'><b>TOTAL</b></td>";
					echo"<td align='center'><b>".$totalConsultas."</b></td>";
					echo"<td align='center'><b>".$totalDias."</b></td>";
					echo"<td align='center'><b>".number_format($totalOportunidad,2,'.',',')."</b></td>";
										
					echo"</tr>";
					$totalConsultas = 0;
					$totalDias = 0;
					$totalOportunidad = 0;
				}//while($i <= $numSelCitas)
				
								
				list($resSelCx,$numSelCx) = Consulta($qSelectCx);
				
				$qResCx = mysql_fetch_row($resSelCx);
				
				$i = 0;
				
				echo"<tr bgcolor='003366'>";
				echo"<td colspan='4'><font color='white'>Servicio O Especialidad: Cirugia</font></td>";
				echo"</tr>";
				echo"<tr bgcolor='006699'>";
				echo"<td><font color='white'>Codigo Medico</font></td>";
				echo"<td><font color='white'> Cantidad </td>";
				echo"<td><font color='white'> Dias Asignados </td>";
				echo"<td><font color='white'> Oportunidad </td>";
				echo"</tr>";

				while ($i < $numSelCx)
				{
						
						if (is_int ($k/2))
						{
							$color='#DDDDDD';
							$k=$k+1;
						}
						else
						{
							$color='#FFFFFF';						   								
							$k=$k+1;
						}// Fin IF (is_int ($k/2))
						
						echo"<tr bgcolor=".$color.">";
						echo"<td align='left'>".$qResCx[0]." - ".$qResCx[3]."</font></td>";
						echo"<td align='center'>".$qResCx[4]."</td>";
						echo"<td align='center'>".$qResCx[5]."</td>";
						
						$totalConsultas +=$qResCx[4] ;
						$totalDias += $qResCx[5];
						$oportunidad = $qResCx[5] / $qResCx[4];
						
						$totalOportunidad +=$oportunidad;
						
						echo"<td align='center'>".number_format($oportunidad,2,'.',',')."</td>";
						
						echo"</tr>";
						
						$qResCx = mysql_fetch_row($resSelCx);
						
					$i += 1;
				}// Fin while ($i < $numSelCx)
				
					echo"<tr bgcolor='silver' bordercolor='blue'>";
					echo"<td align='right'><b>TOTAL</b></td>";
					echo"<td align='center'><b>".$totalConsultas."</b></td>";
					echo"<td align='center'><b>".$totalDias."</b></td>";
					echo"<td align='center'>".number_format($totalOportunidad,2,'.',',')."</td>";
					echo"</tr>";
				
				
				
				echo"</table>";
				
				
			}//Fin If radio button Citas
		}//Fin $rbtn == citas

	
			

		
	}//Fin Muestra de Datos
}//Fin Session_is_registered
	
	Function Consulta($qSelect)
	{
		

		

		
		$resSelect = mysql_query($qSelect,$conex);
	    $numSelect = mysql_num_rows($resSelect);
		 	    
		return array($resSelect,$numSelect);
				
	}//Fin Function Consulta
	
	

	
?>
	</body>
</html>