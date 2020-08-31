<html>
<head>
  	<title>Reporte para la estadistica de la unidad de medicina fisica y rehabilitacion</title>
</head>
<body  BGCOLOR="FFFFFF">
<font face='arial'>
<BODY TEXT="#000066">

<script type="text/javascript">
<!--
function enter()
{
    document.forms.rep_estad_fisia.submit();
}
//-->
</script>

<?php
include_once("conex.php");
//==================================================================================================================================
//PROGRAMA						:Reporte para la estadistica de la unidad de medicina fisica y rehabilitacion
//AUTOR							:Juan David Londoño
//FECHA CREACION				:2007-07-10
//FECHA ULTIMA ACTUALIZACION 	:2007-07-10
$wactualiz="2007-07-10";
//==================================================================================================================================
//ACTUALIZACIONES
//==================================================================================================================================
// xxxx				 
//==================================================================================================================================
// xxxx
//==================================================================================================================================



session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	
	

	


	echo "<form name=rep_estad_fisia action='' method=post>";
	$wbasedato='citasfi';
	// ENCABEZADO
	if (!isset ($fecha2) or (isset ($fecha2) and $fecha2==''))
	
	{
	   	
    	echo "<br><br><br>";
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=3><font size=5><img src='/matrix/images/medical/root/clinica.jpg' WIDTH=150 HEIGHT=100></font></td></tr>";
		echo "<tr><td><br></td></tr>";
		echo "<tr><td align=center colspan=3><font size=5>REPORTE PARA LA ESTADISTICA DE LOS PACIENTES</font></td></tr>";
		echo "</table>";
		echo "<br>";
		echo "<center><table border=0>";
		echo "<tr><td bgcolor=#dddddd align=center><b>Fecha Inicial (AAAA-MM-DD): <br></b><INPUT TYPE='text' NAME='fecha1'></td><td bgcolor=#dddddd align=center><b>Fecha Final (AAAA-MM-DD):<br></b><INPUT TYPE='text' NAME='fecha2'></td>";
		echo "<tr><td bgcolor=#dddddd align=left><b>1. Diagnosticos X Nuevo</b></td><td bgcolor=#dddddd align=left><input type='radio' name='reporte' value='1' onclick='enter()'></td></tr>";
		echo "<tr><td bgcolor=#dddddd align=left><b>2. Diagnosticos X Sexo X Grupos Etereos</b></td><td bgcolor=#dddddd align=left><input type='radio' name='reporte' value='2' onclick='enter()'></td></tr>";
		echo "<tr><td bgcolor=#dddddd align=left><b>3. Diagnosticos X Entidad</b></td><td bgcolor=#dddddd align=left><input type='radio' name='reporte' value='3' onclick='enter()'></td></tr>";
		echo "<tr><td bgcolor=#dddddd align=left><b>4. Diagnosticos X Tipo Atencion</td><td bgcolor=#dddddd align=left></b> <input type='radio' name='reporte' value='4' onclick='enter()'></td></tr>";
		echo "<tr><td bgcolor=#dddddd align=left><b>5. Terapeuta X Sesiones Asistidas</td><td bgcolor=#dddddd align=left></b> <input type='radio' name='reporte' value='5'onclick='enter()'></td></tr>";
		//echo "<td bgcolor=#dddddd align=left><b>6. </b> <input type='radio' name='reporte' value='2'></td>";
	
	}
	////////////////////////////////////////////////////////apartir de aca comienza la impresion
	else 
	{
		switch ($reporte)
		{
			case 1: // este caso es para el reporte de diagnostico x nuevo
			{
				
				// trae los diagnosticos
				$query= " SELECT distinct Diagnostico1
						    FROM ".$wbasedato."_000008
				           WHERE Fecha between '".$fecha1."' and '".$fecha2."'
				           ORDER BY 1";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				//echo mysql_errno() ."=". mysql_error();
				
				echo "<center><table border=0>";// este es el encabezado del resultado
			 	echo "<tr><td align=center colspan=7><font size=5><img src='/matrix/images/medical/root/clinica.jpg' WIDTH=150 HEIGHT=100></font></td></tr>";
			    echo "<tr><td><br></td></tr>";
			    echo "<tr><td align=center colspan=7><font size=5>REPORTE PARA LA ESTADISTICA DE LOS PACIENTES</font></td></tr>";
			    echo "<tr><td align=center colspan=7>Desde: <b>".$fecha1."</b> hasta <b>".$fecha2."</b></td></tr>";
			    echo "<tr><td>&nbsp</td></tr>";
			    echo "<tr><td align=center colspan=7><font size=3>REPORTE DE LOS DIAGNOSTICOS Y SI SON NUEVOS O NO</font></td></tr>";
	  	 	    echo "<tr><td>&nbsp</td></tr></table>";
	  	        echo "<table border=1>";
				echo "<tr bgcolor=#dddddd><td align=center><font size=2><b>DIAGNOSTICO</b></td><td align=center><font size=2><b>ANTERIOR</b></td><td align=center><font size=2><b>NUEVO</b></td><td align=center><font size=2><b>TOTAL</b></td></tr>";
			 	
			 	//mysql_data_seek($err,0); 
			 	$totan=0;
			 	$totnu=0;
			 	$tottc=0;
				for ($i=1;$i<=$num;$i++)
			      {
			      	
			      	// colores de la grilla
			      	if (is_int ($i/2))
	                $wcf="DDDDDD";
	                else
	                $wcf="CCFFFF";	
			      
				        $row=mysql_fetch_row($err);
				        $arr[$i]['diagnostico']=$row[0];
				        
				        // diagnosticos anteriores
				        $query= " SELECT *
								    FROM ".$wbasedato."_000008
						           WHERE Fecha between '".$fecha1."' and '".$fecha2."'
						             AND Diagnostico1 = '".$arr[$i]['diagnostico']."'
						             AND Dx_Nuevo1 = 'N-NO'
						           ORDER BY 1";
						$err_ant = mysql_query($query,$conex);
						$num_ant = mysql_num_rows($err_ant);
						
						$totan=$totan+$num_ant;
						// diagnosticos nuevos
						$query= " SELECT *
								    FROM ".$wbasedato."_000008
						           WHERE Fecha between '".$fecha1."' and '".$fecha2."'
						             AND Diagnostico1 = '".$arr[$i]['diagnostico']."'
						             AND Dx_Nuevo1 = 'S-SI'
						           ORDER BY 1";
						$err_nue = mysql_query($query,$conex);
						$num_nue = mysql_num_rows($err_nue);
						
						$totnu=$totnu+$num_nue;
						
						$num_tot=$num_ant+$num_nue;
						$tottc=$tottc+$num_tot;
						
				        echo "<tr bgcolor=".$wcf." border=1><td >".$arr[$i]['diagnostico']."</td><td align=right>".$num_ant."</td><td align=right>".$num_nue."</td><td align=right>".$num_tot."</td>";
			       		
			       
			       }
			      echo "<tr bgcolor=#dddddd><td align=left ><font size=2><b>TOTALES</b></td><td align=right><b>".$totan."</td>";
				  echo "<td align=right><b>".$totnu."</td><td align=right><b>".$tottc." </td>";
	       		  echo "<tr bgcolor=DDDDDD border=1><td colspan=4>&nbsp</td>"; 
				  echo "<tr bgcolor=#dddddd><td align=left colspan=2><font size=2><b>NUMERO TOTAL DE DIAGNOSTICOS</b></td><td align=center colspan=2><font size=2><b>".$num."</b></td></tr>";
			 	break;
			}
			case 2: // este es el caso para el reporte diagnosticos por sexo y por edad
			{
				
				// trae los diagnosticos
				$query= " SELECT distinct Diagnostico1
						    FROM ".$wbasedato."_000008
				           WHERE Fecha between '".$fecha1."' and '".$fecha2."'
				           ORDER BY 1";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				//echo mysql_errno() ."=". mysql_error();
				
				echo "<center><table border=0>";// este es el encabezado del resultado
			 	echo "<tr><td align=center colspan=7><font size=5><img src='/matrix/images/medical/root/clinica.jpg' WIDTH=150 HEIGHT=100></font></td></tr>";
			    echo "<tr><td><br></td></tr>";
			    echo "<tr><td align=center colspan=7><font size=5>REPORTE PARA LA ESTADISTICA DE LOS PACIENTES</font></td></tr>";
			    echo "<tr><td align=center colspan=7>Desde: <b>".$fecha1."</b> hasta <b>".$fecha2."</b></td></tr>";
			    echo "<tr><td>&nbsp</td></tr>";
			    echo "<tr><td align=center colspan=7><font size=3>REPORTE DE LOS DIAGNOSTICOS, SI SON MASCULINOS O FEMENINOS Y CON SU EDAD</font></td></tr>";
	  	 	    echo "<tr><td>&nbsp</td></tr></table>";
	  	        echo "<table border=1>";
				echo "<tr bgcolor=#dddddd><td align=center><font size=2><b>DIAGNOSTICO</b></td><td align=center colspan=2><font size=2><b>MASCULINOS/PROM. EDAD</b></td><td align=center colspan=2><font size=2><b>FEMENINOS/PROM. EDAD</b></td><td align=center><font size=2><b>TOTAL</b></td></tr>";
			 	
			 	$tottf=0;
			 	$tottl=0;
			 	$tottc=0;
			 	
				for ($i=1;$i<=$num;$i++)
			      {
				        
				    // colores de la grilla
			      	if (is_int ($i/2))
	                $wcf="DDDDDD";
	                else
	                $wcf="CCFFFF";	
	                
				        $row=mysql_fetch_row($err);
				        $arr[$i]['diagnostico']=$row[0];
				        
				        // para los diagnosticos masculinos y su edad
				        $query= " SELECT sum(edad), count(*)
								    FROM ".$wbasedato."_000008
						           WHERE Fecha between '".$fecha1."' and '".$fecha2."'
						             AND Diagnostico1 = '".$arr[$i]['diagnostico']."'
						             AND Sexo = 'M-MASCULINO'
						           ORDER BY 1";
						$err_ant = mysql_query($query,$conex);
						$num_ant = mysql_num_rows($err_ant);
						$row_ant=mysql_fetch_row($err_ant);
						
						// calcula el promedio de edad (masculinos)
						if ($row_ant[0]>0)
						{
							$prom_ant=$row_ant[0]/$row_ant[1];
						}
						else
						{
							$prom_ant=0;
						}
						$tottf=$tottf+$row_ant[1];
						// para los diagnosticos femeninos y su edad
						$query= " SELECT sum(edad), count(*)
								    FROM ".$wbasedato."_000008
						           WHERE Fecha between '".$fecha1."' and '".$fecha2."'
						             AND Diagnostico1 = '".$arr[$i]['diagnostico']."'
						             AND Sexo = 'F-FEMENINO'
						           ORDER BY 1";
						$err_nue = mysql_query($query,$conex);
						$num_nue = mysql_num_rows($err_nue);
						$row_nue=mysql_fetch_row($err_nue);
						
						// calcula el promedio de edad (femeninos)
						if ($row_nue[0]>0)
						{
							$prom_nue=$row_nue[0]/$row_nue[1];
						}
						else
						{
							$prom_nue=0;
						}
						$tottl=$tottl+$row_nue[1];
						
						$num_tot=$row_ant[1]+$row_nue[1];
						$tottc=$tottc+$num_tot;
				        echo "<tr bgcolor=".$wcf." border=1><td>".$arr[$i]['diagnostico']."</td><td align=right>".$row_ant[1]."</td><td align=right>".number_format($prom_ant,0,'.',',')." años</td><td align=right>".$row_nue[1]."</td><td align=right>".number_format($prom_nue,0,'.',',')." años</td><td align=right>".$num_tot."</td>";
			       		
			       
			       }
			      echo "<tr bgcolor=#dddddd><td align=left ><font size=2><b>TOTALES</b></td><td align=right><b>".$tottf."</td><td align=right><b>&nbsp</td>";
				  echo "<td align=right><b>".$tottl."</td><td align=right><b>&nbsp</td><td align=right><b>".$tottc." </td>";
	       		  echo "<tr bgcolor=DDDDDD border=1><td colspan=6>&nbsp</td>"; 
				  echo "<tr bgcolor=#dddddd><td align=left colspan=3><font size=2><b>NUMERO TOTAL DE DIAGNOSTICOS</b></td><td align=center colspan=3><font size=2><b>".$num."</b></td></tr>";
			 	break;
			}
			case 3: // este es el caso para el reporte diagnosticos por entidad
			{
				
				echo "<center><table border=0>";// este es el encabezado del resultado
			 	echo "<tr><td align=center colspan=7><font size=5><img src='/matrix/images/medical/root/clinica.jpg' WIDTH=150 HEIGHT=100></font></td></tr>";
			    echo "<tr><td><br></td></tr>";
			    echo "<tr><td align=center colspan=7><font size=5>REPORTE PARA LA ESTADISTICA DE LOS PACIENTES</font></td></tr>";
			    echo "<tr><td align=center colspan=7>Desde: <b>".$fecha1."</b> hasta <b>".$fecha2."</b></td></tr>";
			    echo "<tr><td>&nbsp</td></tr>";
			    echo "<tr><td align=center colspan=7><font size=3>REPORTE DE LOS DIAGNOSTICOS POR ENTIDAD</font></td></tr>";
	  	 	    echo "<tr><td>&nbsp</td></tr></table>";
				
				echo "<input type='hidden' name='fecha1' value='".$fecha1."'>";
                echo "<input type='hidden' name='fecha2' value='".$fecha2."'>";
                echo "<input type='hidden' name='reporte' value='".$reporte."'>";
				
				if (!isset ($entidad))
				{
					// aca trae las empresas
					$query= " SELECT distinct Sgs
							    FROM ".$wbasedato."_000008
					           WHERE Fecha between '".$fecha1."' and '".$fecha2."'
						       ORDER BY 1";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					
					echo "<table border=(1) align=center>";
					echo "<tr><td>ENTIDAD:</td><td><select name='entidad'>";
					
					if($num>0)
					{
						for ($j=0;$j<$num;$j++)
						{	
							$row = mysql_fetch_array($err);
							echo "<option>".$row[0]."</option>";
						}
						//echo "<option>**TODAS</option>";
					}
					echo "</select></td></tr>";
					echo "<tr><td colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
					echo "</table>";
				}
				else
				{
					// trae los diagnosticos por esa empresa
					$query= " SELECT Diagnostico1, count(*)
							    FROM ".$wbasedato."_000008
					           WHERE Fecha between '".$fecha1."' and '".$fecha2."'
					             AND Sgs = '".$entidad."'
					           GROUP BY 1
					           ORDER BY 1";
					$err_dia = mysql_query($query,$conex);
					$num_dia = mysql_num_rows($err_dia);
					
					echo "<table border=1>";
					echo "<tr bgcolor=#dddddd><td colspan=2 align=center><font size=2><b>DIAGNOSTICOS DE ".$entidad."</b></td></tr>";
				 	echo "<tr bgcolor=#dddddd><td align=center><font size=2><b>DIAGNOSTICO</b></td><td align=center><font size=2><b>TOTAL</b></td></tr>";
				 	
				 	$totdia=0;
					for ($i=1;$i<=$num_dia;$i++)
				      {
					    // colores de la grilla
				      	if (is_int ($i/2))
		                $wcf="DDDDDD";
		                else
		                $wcf="CCFFFF";	
		                
					        $row_dia=mysql_fetch_row($err_dia);
					        $totdia=$totdia+$row_dia[1];
				      		echo "<tr bgcolor=".$wcf." border=1><td>".$row_dia[0]."</td><td align=right>".$row_dia[1]."</td>";
			       	}
			      echo "<tr bgcolor=#dddddd><td align=left ><font size=2><b>TOTAL</b></td><td align=right><b>".$totdia."</td>";
			      echo "<tr bgcolor=DDDDDD border=1><td colspan=2>&nbsp</td>"; 
				  echo "<tr bgcolor=#dddddd><td align=left colspan=1><font size=2><b>NUMERO TOTAL DE DIAGNOSTICOS</b></td><td align=center colspan=1><font size=2><b>".$num_dia."</b></td></tr>";
			 	
				}
				 
				
			      
				break;
			}
			case 4: // este es el caso para el reporte diagnosticos por tipo de atencion
			{
				
				echo "<center><table border=0>";// este es el encabezado del resultado
			 	echo "<tr><td align=center colspan=7><font size=5><img src='/matrix/images/medical/root/clinica.jpg' WIDTH=150 HEIGHT=100></font></td></tr>";
			    echo "<tr><td><br></td></tr>";
			    echo "<tr><td align=center colspan=7><font size=5>REPORTE PARA LA ESTADISTICA DE LOS PACIENTES</font></td></tr>";
			    echo "<tr><td align=center colspan=7>Desde: <b>".$fecha1."</b> hasta <b>".$fecha2."</b></td></tr>";
			    echo "<tr><td>&nbsp</td></tr>";
			    echo "<tr><td align=center colspan=7><font size=3>REPORTE DE LOS DIAGNOSTICOS POR TIPO DE ATENCION</font></td></tr>";
	  	 	    echo "<tr><td>&nbsp</td></tr></table>";
	  	 	    
	  	 	    echo "<input type='hidden' name='fecha1' value='".$fecha1."'>";
                echo "<input type='hidden' name='fecha2' value='".$fecha2."'>";
                echo "<input type='hidden' name='reporte' value='".$reporte."'>";
				
				if (!isset ($entidad))
				{
					// traigo tipos de atencion
					$query= " SELECT distinct atencion
							    FROM ".$wbasedato."_000008
					           WHERE Fecha between '".$fecha1."' and '".$fecha2."'
						       ORDER BY 1";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					//echo mysql_errno() ."=". mysql_error();
					echo "<table border=(1) align=center>";
					echo "<tr><td>TIPO DE ATENCION:</td><td><select name='entidad'>";
					
					if($num>0)
					{
						for ($j=0;$j<$num;$j++)
						{	
							$row = mysql_fetch_array($err);
							echo "<option>".$row[0]."</option>";
						}
						//echo "<option>**TODAS</option>";
					}
					echo "</select></td></tr>";
					echo "<tr><td colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
					echo "</table>";
				}
				else
				{
					// diagnosticos por tipo de atencion
					$query= " SELECT Diagnostico1, count(*)
							    FROM ".$wbasedato."_000008
					           WHERE Fecha between '".$fecha1."' and '".$fecha2."'
					             AND Atencion = '".$entidad."'
					           GROUP BY 1
					           ORDER BY 1";
					$err_dia = mysql_query($query,$conex);
					$num_dia = mysql_num_rows($err_dia);
					
					echo "<table border=1>";
					echo "<tr bgcolor=#dddddd><td colspan=2 align=center><font size=2><b>TIPO DE ATENCION: ".$entidad."</b></td></tr>";
				 	echo "<tr bgcolor=#dddddd><td align=center><font size=2><b>DIAGNOSTICO</b></td><td align=center><font size=2><b>TOTAL</b></td></tr>";
				 	
				 	$totdia=0;
					for ($i=1;$i<=$num_dia;$i++)
				      {
					     // colores de la grilla
				      	if (is_int ($i/2))
		                $wcf="DDDDDD";
		                else
		                $wcf="CCFFFF";	
					        
					        $row_dia=mysql_fetch_row($err_dia);
					        $totdia=$totdia+$row_dia[1];
				      		echo "<tr bgcolor=".$wcf." border=1><td>".$row_dia[0]."</td><td align=right>".$row_dia[1]."</td>";
			       	}
			      echo "<tr bgcolor=#dddddd><td align=left ><font size=2><b>TOTAL</b></td><td align=right><b>".$totdia."</td>";
				  echo "<tr bgcolor=DDDDDD border=1><td colspan=2>&nbsp</td>"; 
				  echo "<tr bgcolor=#dddddd><td align=left colspan=1><font size=2><b>NUMERO TOTAL DE DIAGNOSTICOS</b></td><td align=center colspan=1><font size=2><b>".$num_dia."</b></td></tr>";
			 	
				}
				
				
				break;
			}
			case 5: // este es el caso para el reporte terapeuta por sesiones asistida
			{
				
				// traigo los terapeutas
				$query= " SELECT distinct Terapeuta
						    FROM ".$wbasedato."_000008
				           WHERE Fecha between '".$fecha1."' and '".$fecha2."'
				           ORDER BY 1";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				
				echo "<center><table border=0>";// este es el encabezado del resultado
			 	echo "<tr><td align=center colspan=7><font size=5><img src='/matrix/images/medical/root/clinica.jpg' WIDTH=150 HEIGHT=100></font></td></tr>";
			    echo "<tr><td><br></td></tr>";
			    echo "<tr><td align=center colspan=7><font size=5>REPORTE PARA LA ESTADISTICA DE LOS PACIENTES</font></td></tr>";
			    echo "<tr><td align=center colspan=7>Desde: <b>".$fecha1."</b> hasta <b>".$fecha2."</b></td></tr>";
			    echo "<tr><td>&nbsp</td></tr>";
			    echo "<tr><td align=center colspan=7><font size=3>REPORTE DE TERAPEUTA POR SESIONES ASISTIDA</font></td></tr>";
	  	 	    echo "<tr><td>&nbsp</td></tr></table>";
				echo "<table border=1>";
				echo "<tr bgcolor=#dddddd><td align=center><font size=2><b>TERAPEUTA</b></td><td align=center ><font size=2><b>TER. FISICA</b></td><td align=center ><font size=2><b>TER. OCUPACIONAL</b></td>";
				echo "<td align=center ><font size=2><b>TER. DE LENGUAJE</b></td><td align=center ><font size=2><b>SICOLOGIA</b></td>";
				echo "<td align=center ><font size=2><b>R. CARDIACA</b></td><td align=center><font size=2><b>TOTAL</b></td></tr>";
			 	
			 	// inicio totales
			 	$tottf=0;
			 	$totto=0;
			 	$tottl=0;
				$totts=0;
				$tottc=0;
				$tottt=0;
				
				for ($i=1;$i<=$num;$i++)
			      {
				        
				        // colores de la grilla
				      	if (is_int ($i/2))
		                $wcf="DDDDDD";
		                else
		                $wcf="CCFFFF";	
		                
				        $row=mysql_fetch_row($err);
				        $arr[$i]['terapeuta']=$row[0];
				        
				        // TERAPIA FISICA
				        $query= " SELECT sum(Nrosatf), count(*)
								    FROM ".$wbasedato."_000008
						           WHERE Fecha between '".$fecha1."' and '".$fecha2."'
						             AND Terapeuta = '".$arr[$i]['terapeuta']."'
						           ORDER BY 1";
						$err_ant = mysql_query($query,$conex);
						$num_ant = mysql_num_rows($err_ant);
						$row_fis=mysql_fetch_row($err_ant);
						
						// TERAPIA OCUPACIONAL
						$query= " SELECT sum(Nrosato), count(*)
								    FROM ".$wbasedato."_000008
						           WHERE Fecha between '".$fecha1."' and '".$fecha2."'
						             AND Terapeuta = '".$arr[$i]['terapeuta']."'
						           ORDER BY 1";
						$err_ocu = mysql_query($query,$conex);
						$num_ocu = mysql_num_rows($err_ocu);
						$row_ocu=mysql_fetch_row($err_ocu);
						
						// TERAPIA DEL LENGUAJE
						$query= " SELECT sum(Nrosatl), count(*)
								    FROM ".$wbasedato."_000008
						           WHERE Fecha between '".$fecha1."' and '".$fecha2."'
						             AND Terapeuta = '".$arr[$i]['terapeuta']."'
						           ORDER BY 1";
						$err_len = mysql_query($query,$conex);
						$num_len = mysql_num_rows($err_len);
						$row_len=mysql_fetch_row($err_len);
						
						// TERAPIA DE SICOLOGIA
						$query= " SELECT sum(Nrosasi), count(*)
								    FROM ".$wbasedato."_000008
						           WHERE Fecha between '".$fecha1."' and '".$fecha2."'
						             AND Terapeuta = '".$arr[$i]['terapeuta']."'
						           ORDER BY 1";
						$err_sic = mysql_query($query,$conex);
						$num_sic = mysql_num_rows($err_sic);
						$row_sic=mysql_fetch_row($err_sic);
						
						// TERAPIA CARDIACA
						$query= " SELECT sum(Nrosaca), count(*)
								    FROM ".$wbasedato."_000008
						           WHERE Fecha between '".$fecha1."' and '".$fecha2."'
						             AND Terapeuta = '".$arr[$i]['terapeuta']."'
						           ORDER BY 1";
						$err_car = mysql_query($query,$conex);
						$num_car = mysql_num_rows($err_car);
						$row_car=mysql_fetch_row($err_car);
						
						// totales
						$tottf=$tottf+$row_fis[0];
						$totto=$totto+$row_ocu[0];
						$tottl=$tottl+$row_len[0];
						$totts=$totts+$row_sic[0];
						$tottc=$tottc+$row_car[0];
						
						$num_tot=$row_fis[0]+$row_ocu[0]+$row_len[0]+$row_sic[0]+$row_car[0];
						$tottt=$tottt+$num_tot;
				        echo "<tr bgcolor=".$wcf." border=1><td>".$arr[$i]['terapeuta']."</td><td align=right>".$row_fis[0]."</td><td align=right>".$row_ocu[0]."</td>";
						echo "<td align=right>".$row_len[0]."</td><td align=right>".$row_sic[0]." </td><td align=right>".$row_car[0]." </td><td align=right>".$num_tot."</td>";
			       		
			       
			       }
			      echo "<tr bgcolor=#dddddd><td align=left ><font size=2><b>TOTALES</b></td><td align=right><b>".$tottf."</td><td align=right><b>".$totto."</td>";
				  echo "<td align=right><b>".$tottl."</td><td align=right><b>".$totts." </td><td align=right><b>".$tottc." </td><td align=right><b>".$tottt."</td>";
	       		  echo "<tr bgcolor=DDDDDD border=1><td colspan=7>&nbsp</td>"; 
				  echo "<tr bgcolor=#dddddd><td align=left colspan=4><font size=2><b>NUMERO TOTAL DE TERAPEUTAS</b></td><td align=center colspan=3><font size=2><b>".$num."</b></td></tr>";
			 	
				break;
			}
		}
  
 	}
}
?>
</body>
</html>