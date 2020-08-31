<html>
	<head>
		<meta http-equiv="content-Type" content="text/html; charsetcontent==iso-8859-1">
		<title>Reporte De Centros De Costos</title>
	</head>
	<body>
		<label>
			<div align="right"> Desarrollado Por: Juan Esteban Lopez A.</div>	
			&nbsp;
					
		</label>
	</body>
 
   <SCRIPT LANGUAGE="JavaScript1.2">
	   <!--
	   function onLoad() {
	   	loadMenus();
	   }
	   //-->
	
	   function enter()
	   {
	   	document.forma.submit();
	   }

	</script>
<?php
include_once("conex.php");

	/********************************************************
	*     	Reporte de Centros de costos Por Concepto		*
	*														*
	*********************************************************/
	
	//==================================================================================================================================
	//PROGRAMA						:Reporte de Centro de Costos Por concepto
	//AUTOR							:Juan Esteban Lopez Aguirre
	//FECHA CREACION				:Enero de 2008
	//FECHA ULTIMA ACTUALIZACION 	:
	//DESCRIPCION					:En el reporte se lista  CENTRO DE COSTOS - CONCEPTO DE FACTURACIÓN - NOMBRE 			
	//								 DEL CONCEPTO - VALOR FACTURADO - NOTAS CREDITO - VALOR NETO 
	// 								 dado un centro de costos y una fecha. El informe permite consultar por todos 
	//								 los centros de costos.
	//MODIFICACIONES:
	//=================================================================================================================================

include_once("root/comun.php");

if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

$conex = obtenerConexionBD("matrix");

$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

$wbasedato = strtolower($institucion->baseDeDatos);
$wentidad = $institucion->nombre;

$feInicial = date("Y-m-d");
	$feFinal = date("Y-m-d");
    $hora = (string)date("H:i:s");
	$totalConceptoConsulta = 0; 
	$totalDescuentoConsulta = 0;
	$totalGenConsulta = 0;
	
	//Variables consultas Detallado
	$totalNotasCredito = 0;
	$totalGenConsulta = 0;
	$l = 0;


	//Fin declaracion de variables
	
	//Estilo del reporte
	
	  $wcf1 = "003366";  // color del fondo   -- Azul mas claro
	  $wcf = "DDDDDD";   //COLOR DEL FONDO    -- Gris claro
	  $wcf2 = "003366";  //COLOR DEL FONDO 2  -- Azul claro
	  $wcf3 = "006699";  //COLOR DEL FONDO 3  -- GRIS MAS OSCURO
	  $wcf4 = "99CCFF";  //COLOR DEL FONDO 4  -- AZUL
	  $wcf5 = "00CCFF";  // 
	  $wclfa = "FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
	  $wclfg = "003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro 	
	  
	//
	
	echo "<form action='repCentroCostos.php' method='post' name='forma'>";
	
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
	
	session_start();
	if (!isset($_SESSION['user']))
	{
		echo "ERROR";
	}
	else
	{
// and $btnConsultar != 'Consultar'
		if (!isset($btnConsultar))
		{
			echo "<center><table border='0'>";
			echo "<tr><td align='center' rowspan='2'><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=500 HEIGHT=100'></td></tr>";
			echo "<tr  bgcolor=".$wcf1."><td><font size='5' text color =#ffffff>Reporte Centro De Costos</font></td></tr>";
			echo "<tr bgcolor=".$wcf."><td>Fecha Inicial : ";
			campoFecha("txtFeInicial");
			echo "</td>";
			
			echo "<td>Fecha Final : ";
			campoFecha("txtFeFinal");
			echo "</td></tr>";
			
			//Query Obtiene todos los centros de costos contenidos en la base de datos para ser mostrados en un dropDownList 		
			$q = "SELECT Ccocod, Ccodes"
			."		FROM ".$wbasedato."_000003"
			."	ORDER BY Ccocod";
			
			$res = mysql_query($q,$conex);// Contiene el resultado de ejecutar el query en la base de datos
			$num = mysql_num_rows($res); // Contiene el numero de filas afectadas al ejecutar el query en la base de datos
			
			echo "<tr bgcolor=".$wcf."><td aligh=center colspan=2>Centro de costos : ";
			echo "<select name=ddCdeC>";
			
			echo "<option>% - Todos los centros de costos</option>";
			for($i=0;$i<=$num;$i++)
			{
				
				$row = mysql_fetch_row($res);
				if ($ddCdeC!=$row[0]."-".$row[1])
				{
				
					echo "<option>".$row[0]."-".$row[1]."</option>";		
				
				}//FIN if ($ddCdeC!=$row[0]."-".$row[1])
				
			}// Fin for($i=1;$i<=$num;$i++)
			echo "<tr bgcolor=".$wcf."><td align='center'colspan='2'>Desplegar Reporte Resumido<input type='radio' name='rbTipoRep' value='s' checked>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbspDesplegar Reporte Detallado<input type='radio' name='rbTipoRep' value='n'></tr>";
			echo "<tr bgcolor=".$wcf." align='center'><td colspan='2'><input type='submit' name='btnConsultar' value='Consultar' onclick='enter()'>&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td></tr>";
			echo "</table>";

		}
		else
		{			
			// Muestra Los Datos 
			echo "<table border=0 width=100%>";
			echo "<tr><td align=left><B>Facturacion:</B>$wentidad</td>";
			echo "<td align=right><B>Fecha:</B> ".date('Y-m-d')."</td></tr>";
			echo "<tr><td align=left><B>Programa:</B> Facturacion Por Centro de Costo</td>"; 
			echo "<td align=right><B>Hora :</B> ".$hora."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>"; 
			echo "</table>";
			echo "<table border=0 align=center >";
			echo "<tr><td align=center><H1>$wentidad</H1></td></tr>";
			if ($rbTipoRep=='s')
			{
			  	echo "<tr><td><B>REPORTE DE FACTURAS GENERAL RESUMIDO</B></td></tr>";
			}
			else
			{
			    echo "<tr><td><B>REPORTE DE FACTURAS GENERAL DETALLADO</B></td></tr>";
			}
			echo "</table></br>";
			
			echo "<table border=0 align=center >";
		    echo "<tr><td><B>Fecha inicial:</B> ".$txtFeInicial."</td>";
			echo "<td><B>Fecha final:</B> ".$txtFeFinal."</td>";
			echo "<tr><td colspan=3><B>Centro de Costo :</B> ".$ddCdeC."</td></tr>";
			echo "</table></br>";
			echo "<A href=repCentroCostos.php?wemp_pmla=".$wemp_pmla."><center>VOLVER</center></A><br>";
			echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";

				
			$temp=explode ('-',$ddCdeC);
			$codCdC=$temp[0];
			$i = 1;// Controla la entreda al del primer query
			$y = 1;//Controla la entrada del segundo query
				
							
		$qRes ="SELECT cs18.fenfac, cs106.tcarser, cs03.ccodes, cs106.tcarconcod, cs106.tcarconnom, cs106.tcarprocod, cs106.tcarpronom, cs66.rcfval"
					." FROM ".$wbasedato."_000003 cs03,".$wbasedato."_000018 cs18, ".$wbasedato."_000066 cs66, ".$wbasedato."_000106 cs106"
					." WHERE cs18.fenfec between '".$txtFeInicial."' and '".$txtFeFinal."'"
					." AND cs18.fenffa = cs66.rcfffa"
					." AND cs18.fenfac = cs66.rcffac"
					." AND cs106.tcarser = cs03.ccocod"
					." AND cs106.tcarser like '".trim($temp[0])."'"
					." AND cs106.id = cs66.rcfreg"
					." AND cs106.tcarconcod NOT IN ('9301','9302','9303')"
					." AND cs66.rcfest = 'on'"
//					." AND cs106.tcarest = 'on'"
					." AND cs18.fenest = 'on'"
					." ORDER BY cs106.tcarser,cs106.tcarconcod,cs106.tcarprocod";
					
				$Res = mysql_query($qRes,$conex);
				$num = mysql_num_rows($Res);
				$row = mysql_fetch_array($Res);


				
			$qDes = "select cs65.fdecco, cs65.fdecon, sum(fdevde)"
					." from ".$wbasedato."_000018 cs18, ".$wbasedato."_000065 cs65"
					." where cs18.fenfec between '".$txtFeInicial."' and '".$txtFeFinal."'"
					." and cs18.fenfac = cs65.fdefac"
					." and cs65.fdeest ='on'"
					." and cs65.fdevde !='0'"
					." group by cs65.fdecco, cs65.fdecon";
					
				$resDes = mysql_query($qDes,$conex);
				$numDes = mysql_num_rows($resDes);
				$rowDes = mysql_fetch_array($resDes);
				

			if ( $num == 0)
			{
				echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
				echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>Sin ningún documento en el rango de fechas ||							seleccionado</td><tr>";
				echo"</table>";
				
			}
			else
			{
				echo "<table border=0 align =center>";
				if ($rbTipoRep == 's')
				{				
					while (	$i <= $num )
					{
		  			   
					   echo "<tr><td align=left bgcolor=$wcf2 colspan='6'><font size=3 text color=#FFFFFF><b>Centro De Costos: ".$row[1]." - ".$row							[2]."</b></font></td></tr>";
					   echo "<tr><td align=right bgcolor=$wcf3 ><font size=2 text color=#FFFFFF>CONCEPTO</font></td>";
	        			echo "<td align=CENTER bgcolor=$wcf3><font size=2 text color=#FFFFFF>DESCRIPCION</font></td>";
	        			echo "<td align=CENTER bgcolor=$wcf3><font size=2 text color=#FFFFFF>VALOR TOTAL</font></td>";
	        			echo "<td align=CENTER bgcolor=$wcf3><font size=2 text color=#FFFFFF>DESCUENTO</font></td>";
						echo "<td align=right bgcolor=$wcf3 ><font size=2 text color=#FFFFFF>VALOR CON DESCUENTO</font></td></tr>";
	
	        			$auxConcepto = $row[3]; // Variable para determinar que el concepto sea el mismo
						$auxCentros = $row[1]; // Variable para determinar que el centro de costos sea el mismo 

						  
						$totalNetoConcepto = 0;
						$totalNetoDescuento = 0;
						$totalGenNeto = 0;
						$k = 0;
						
						while ( $row[1] == $auxCentros )
						{
							$totalConcepto = 0;
							$totalDescuento = 0;
							$totalGenConcepto = 0;
	
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
							
							echo "<tr><td align=center bgcolor=$color><font size=2 >".$row[3]."</font></td>";
							echo "<td align=left bgcolor=$color ><font size=2 >".$row[4]."</font></td>";
							
							$auxConcepto = $row[3]; // Variable para determinar que el concepto sea el mismo


							if ($rowDes[0] == $auxCentros and $auxConcepto == $rowDes[1] and $l<=$numDes)
							{	

						  		$totalDescuento = $totalDescuento + $rowDes[2];
						  		$rowDes = mysql_fetch_array($resDes);
						  		$l = $l +1;

					  		}// Fin Suma Descuento
							  							
							while ( $auxConcepto == $row[3] )
							{					
								$totalConcepto = $totalConcepto + $row[7];
								$totalGenConcepto = $totalConcepto - ($totalDescuento + $totalNotasCredito);
																
								$row = mysql_fetch_array($Res);	
								
						
								$i = $i + 1;
										
							}// Fin While $auxConcepto == $row[1]
								
							if ($auxConcepto == '9304' )
							{
						
								$totalDescuento = $totalNetoDescuento + ($totalConcepto*-1);
								$totalNetoDescuento = $totalNetoDescuento + $totalDescuento;
								$totalGenNeto = $totalGenNeto - $totalDescuento;	
								$totalConcepto = 0;	
									
							}
							else
							{		
							
								$totalNetoConcepto = $totalNetoConcepto + $totalConcepto;
								$totalNetoDescuento = $totalNetoDescuento + $totalDescuento;
								$totalGenNeto = $totalGenNeto + $totalGenConcepto;
									
							}
								//Variables para total de la consulta
					
							echo "<td align=right bgcolor=".$color." ><font size=2>".number_format($totalConcepto,0,'.',',')."</font></td>";
							echo "<td align=right bgcolor=".$color."><font size=2>".number_format($totalDescuento,0,'.',',')."</font></td>";
							echo "<td align=right bgcolor=".$color."><font size=2>".number_format($totalGenConcepto,0,'.',',')."</font></td></tr>";
							
							
						}// Fin while ( $row[10] == $auxCentros)
						
						$totalConceptoConsulta = $totalConceptoConsulta + $totalNetoConcepto;
						$totalDescuentoConsulta = $totalDescuentoConsulta + $totalNetoDescuento;
						$totalGenConsulta = $totalGenConsulta + $totalGenNeto; 
						
						echo "<tr><td align=CENTER bgcolor=gray ><font size=4text color=#FFFFFF>TOTAL CENTRO DE COSTOS</font></td>";
						echo "<td align=right bgcolor=gray colspan=2><font size=4 text color=#FFFFFF>VALOR NETO :".number_format($totalNetoConcepto,0,'.',',')."</font></td>";
	        		   echo"<td align=CENTER bgcolor=gray><font size=4 text color=#FFFFFF>DESCUENTO :".number_format($totalNetoDescuento,0,'.',',')."</font></td>";
	        			echo "<td align=right bgcolor=gray><font size=4 text color=#FFFFFF>".number_format($totalGenNeto,0,'.',',')."</font></td></tr>";				
					}// Fin While while (	$i <= $num )
					
				   echo "<tr><td align=CENTER bgcolor=navy ><font size=4text color=#FFFFFF>TOTAL</font></td>";
				   echo "<td align=right bgcolor=navy colspan=2><font size=4 text color=#FFFFFF>Total Concepto :".number_format($totalConceptoConsulta,0,'.',',')."</font></td>";
        		   echo"<td align=CENTER bgcolor=navy><font size=4 text color=#FFFFFF>DESCUENTO :".number_format($totalDescuentoConsulta,0,'.',',')."</font></td>";
          		   echo "<td align=right bgcolor=navy><font size=4 text color=#FFFFFF>".number_format($totalGenConsulta,0,'.',',')."</font></td></tr>";				
				}
				if ($rbTipoRep == 'n')
				{
					
					while ($i <= $num)
        			{

        				$totalNetoConcepto = 0;
						$totalGenNeto = 0;
						$totalGenNetoCdeC = 0;
						$k = 0;
	
	        			$auxConcepto = $row[3]; // Variable para determinar que el concepto sea el mismo
						$auxCentros = $row[1]; // Variable para determinar que el centro de costos sea el mismo
						
						echo "<tr><td align=left bgcolor=$wcf3 colspan='4'><font size=3 text color=#FFFFFF><b>Centro De Costos: ".$row[1]." - ".$row[2]."</b></font></td></tr>";	
						
						
        				while ( $row[1] == $auxCentros )
        				{
        					        				
							echo "<tr><td align=left bgcolor=$wcf2 colspan='1'><font size=3 text color=#FFFFFF><b>Concepto:".$row[3]."</b></font></td>";					  echo "<td align=CENTER bgcolor=$wcf3 colspan='3'><font size=4 text color=#FFFFFF>".$row[4]."</font></td></tr>";
							echo "<tr><td align=right bgcolor=$wcf3 ><font size=2 text color=#FFFFFF>PROCEDIMIENTO</font></td>";
        					echo "<td align=CENTER bgcolor=$wcf3><font size=2 text color=#FFFFFF>DESCRIPCION</font></td>";
        					echo "<td align=CENTER bgcolor=$wcf3><font size=2 text color=#FFFFFF>CANTIDAD</font></td>";
        					echo "<td align=CENTER bgcolor=$wcf3><font size=2 text color=#FFFFFF>VALOR TOTAL</font></td></tr>";

							$totalNetoConcepto = 0;// Variable para determinar el valor total del concepto
       						$auxConcepto = $row[3]; // Variable para determinar que el concepto sea el mismo
      					
							while ($row[3] == '9304' and $row[1] == '2300')
							{
							 	$row = mysql_fetch_array($Res);
							 	$i = $i + 1;
							
							}
						
							while ( $auxConcepto == $row[3] )
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
					
								echo "<tr><td align=center bgcolor=$color><font size=2 >".$row[5]."</font></td>";
								echo "<td align=left bgcolor=$color ><font size=2 >".$row[6]."</font></td>";
								
								$auxProc = $row[5];
								$contProc = 0;
								$totalConcepto = 0;
								$totalGenNeto = 0; // Contiene el valor total del procedimiento 
								
		
								while ( $auxProc == $row[5] )
								{
									 										 	
								 	$totalConcepto = $totalConcepto + $row[7];
									$totalGenNeto = $totalConcepto;
   								    $contProc = $contProc + 1;	// posiblemente row[7]								
									$i = $i + 1;
									
								 	$row = mysql_fetch_array($Res);
										 										
								}// Fin while ($auxProc == $row[5])
									
															
								echo "<td align=center bgcolor=".$color."><font size=2 >".$contProc."</font></td>";
								echo "<td align=center bgcolor=".$color."><font size=2 >".number_format($totalGenNeto,0,'.',',')."</font></td></tr>";
									/*echo "<td align=right bgcolor=".$color."><font size=2>".number_format($totalGenConcepto,0,'.',',')."</font></td></tr>";*/
								$totalNetoConcepto = $totalNetoConcepto + $totalGenNeto;
									
									
										
							}// Fin While $auxConcepto == $row[1]
						
							echo "<tr><td align=CENTER bgcolor=gray ><font size=4 text color=#FFFFFF>TOTAL CONCEPTO</font></td>";
							echo "<td align=right bgcolor=gray colspan='4'><font size=5 text color=#FFFFFF>VALOR NETO :".number_format($totalNetoConcepto,0,'.',',')."</font></td>";
						$totalGenNetoCdeC = $totalGenNetoCdeC + $totalNetoConcepto;	
														   
							        												
						}// Fin while ( $row[10] == $auxCentros )
						
						echo "<tr><td align=CENTER bgcolor=gray ><font size=4text color=#FFFFFF>TOTAL CENTRO DE COSTOS</font></td>";
						echo "<td align=right bgcolor=navy colspan=5><font size=5 text color=#FFFFFF>VALOR NETO :".number_format($totalGenNetoCdeC,0,'.',',')."</font></td>";
						$totalGenConsulta = $totalGenConsulta + $totalGenNetoCdeC;
						
					}//Fin while ($i <= $num)
					echo "<tr><td align=CENTER bgcolor=gray ><font size=4text color=#FFFFFF>TOTAL GENERAL</font></td>";
				echo "<td align=right bgcolor=navy colspan=1><font size=5 text color=#FFFFFF>VALOR NETO :".number_format($totalGenConsulta,0,'.',',')."</font></td>";
					
				}/// Fin if $rbTipoRep == 's'
				
				
				echo "</TABLE>";
				echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
				
				
			}//FIN if ( $num == 0)



		
	  }//if (!isset($btnConsultar) and $btnConsultar!='Consultar')
}// FIN (!isset($_SESSION['user']))
liberarConexionBD($conex);
?>
</html>
