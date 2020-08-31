<html>
<head>
<title>Informe de Referencia y Contrareferencia</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<?php
include_once("conex.php");
/***********************************************************************
*                             Autor: Juan David Londoño				   *
*			                Fecha de Creación:2005-06-01			   *
*                El programa muestra los datos estadisticos            *
*              para el informe de referencia y contrareferencia 	   * 
*             del servicio de urgencias, pide un periodo de tiempo 	   *
*                                 en el cual se va a evaluar	       *  
***********************************************************************/
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	echo "<form action='' method=post>";
	if(!isset($fecha1))
	{
		/*Pedir los paámetros por pantalla*/
		if (!isset($fecha1))
		$fecha1="";
		if (!isset($fecha2))
		$fecha2="";
		echo  "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>CLÍNICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td colspan=2 align=center><b>INFORME DE REFERENCIA Y CONTRAREFERENCIA</b></td></tr>";
		echo "<tr><td colspan=2 align=center><b>ADMISIONES URGENCIAS</b></td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha inicial</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='fecha1' size=10 maxlength=10 value='".$fecha1."'>AAAA-MM-DD</td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha final</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='fecha2' size=10 maxlength=10 value='".$fecha2."'>AAAA-MM-DD</td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		
	else
	{
		if ( $fecha1== "" or $fecha2== "" or ($fecha1>$fecha2))
		{
		echo  "<center><table border=1>";
		echo "<tr><td align=center colspan=2><b>FECHAS NO VALIDAS<b></td></tr></table>";
		}
		else {
		/*Hacer el reporte*/
		echo "<center><table border=1 width='450'>";
		echo "<tr><td  align=center><font face='arial'><b>CLÍNICA LAS AMERICAS </b></td></tr>";
		echo "<tr><td  align=center><font face='arial'><b>INFORME DE REFERENCIA Y CONTRAREFERENCIA</b></td></tr>";
		echo "<tr><td align=center><font face='arial' size='2'><b>ADMISIONES URGENCIAS</TD></TR>"; 
		Echo "<tr><td align=center><font face='arial' size='2'><b>EN EL PERIODO COMPRENDIDO ENTRE $fecha1 Y $fecha2</b></td></tr></table><br>";
	
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2')";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Acepto= 'on'";
		$err = mysql_query($query,$conex);
		$acep = mysql_num_rows($err);
		
				
		$query = "select Observaciones from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Acepto= 'off' ";
		$err = mysql_query($query,$conex);
		$nacep = mysql_num_rows($err);
		/*
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Observaciones= '02-No especialidad'";
		$err = mysql_query($query,$conex);
		$nesp = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Observaciones= '03-No camas Hospitalizacion'";
		$err = mysql_query($query,$conex);
		$nhosp = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Observaciones= '04-No camas UCE'";
		$err = mysql_query($query,$conex);
		$nuce = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Observaciones= '05-No camas UCI'";
		$err = mysql_query($query,$conex);
		$nuci = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Observaciones= '06-No camas neonatos'";
		$err = mysql_query($query,$conex);
		$nneo = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '01-Pediatria' and Estado_Paciente='02-Ambulatorio'";
		$err = mysql_query($query,$conex);
		$pamb = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '02-Medicina Interna' and Estado_Paciente='02-Ambulatorio'";
		$err = mysql_query($query,$conex);
		$mamb = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '03-Gineco-Obstetricia' and Estado_Paciente='02-Ambulatorio'";
		$err = mysql_query($query,$conex);
		$gamb = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '04-Cirugia General' and Estado_Paciente='02-Ambulatorio'";
		$err = mysql_query($query,$conex);
		$cxamb = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '05-Cardiologia' and Estado_Paciente='02-Ambulatorio'";
		$err = mysql_query($query,$conex);
		$camb = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '06-Ortopedia' and Estado_Paciente='02-Ambulatorio'";
		$err = mysql_query($query,$conex);
		$oramb = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '07-Cirugia Plastica' and Estado_Paciente='02-Ambulatorio'";
		$err = mysql_query($query,$conex);
		$cpamb = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '08-Oftalmologia' and Estado_Paciente='02-Ambulatorio'";
		$err = mysql_query($query,$conex);
		$ofamb = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '09-Otorrinolaringologia' and Estado_Paciente='02-Ambulatorio'";
		$err = mysql_query($query,$conex);
		$otamb = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '10-Neurologia' and Estado_Paciente='02-Ambulatorio'";
		$err = mysql_query($query,$conex);
		$neuamb = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '11-Nefrologia' and Estado_Paciente='02-Ambulatorio'";
		$err = mysql_query($query,$conex);
		$nefamb = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '12-Neumologia' and Estado_Paciente='02-Ambulatorio'";
		$err = mysql_query($query,$conex);
		$nemamb = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '13-Hemodinamia' and Estado_Paciente='02-Ambulatorio'";
		$err = mysql_query($query,$conex);
		$hemamb = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '14-Oncologia' and Estado_Paciente='02-Ambulatorio'";
		$err = mysql_query($query,$conex);
		$oncoamb = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '15-Toxicologia' and Estado_Paciente='02-Ambulatorio'";
		$err = mysql_query($query,$conex);
		$toxamb = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '16-Urologia' and Estado_Paciente='02-Ambulatorio'";
		$err = mysql_query($query,$conex);
		$uroamb = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '17-Cirugia Infantil' and Estado_Paciente='02-Ambulatorio'";
		$err = mysql_query($query,$conex);
		$cxinamb = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '18-Neurocirugia' and Estado_Paciente='02-Ambulatorio'";
		$err = mysql_query($query,$conex);
		$neucxamb = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '19-Algesiologia' and Estado_Paciente='02-Ambulatorio'";
		$err = mysql_query($query,$conex);
		$algamb = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '20-Cardiovascular' and Estado_Paciente='02-Ambulatorio'";
		$err = mysql_query($query,$conex);
		$carvamb = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '21-Dermatologia' and Estado_Paciente='02-Ambulatorio'";
		$err = mysql_query($query,$conex);
		$dermamb = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '22-Cardioinfantil' and Estado_Paciente='02-Ambulatorio'";
		$err = mysql_query($query,$conex);
		$carinamb = mysql_num_rows($err);
	
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '01-Pediatria' and Estado_Paciente='01-Hospitalizado'";
		$err = mysql_query($query,$conex);
		$phos = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '02-Medicina Interna' and Estado_Paciente='01-Hospitalizado'";
		$err = mysql_query($query,$conex);
		$mhos = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '03-Gineco-Obstetricia' and Estado_Paciente='01-Hospitalizado'";
		$err = mysql_query($query,$conex);
		$ghos = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '04-Cirugia General' and Estado_Paciente='01-Hospitalizado'";
		$err = mysql_query($query,$conex);
		$cxhos = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '05-Cardiologia' and Estado_Paciente='01-Hospitalizado'";
		$err = mysql_query($query,$conex);
		$chos = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '06-Ortopedia' and Estado_Paciente='01-Hospitalizado'";
		$err = mysql_query($query,$conex);
		$orhos = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '07-Cirugia Plastica' and Estado_Paciente='01-Hospitalizado'";
		$err = mysql_query($query,$conex);
		$cphos = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '08-Oftalmologia' and Estado_Paciente='01-Hospitalizado'";
		$err = mysql_query($query,$conex);
		$ofhos = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '09-Otorrinolaringologia' and Estado_Paciente='01-Hospitalizado'";
		$err = mysql_query($query,$conex);
		$othos = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '10-Neurologia' and Estado_Paciente='01-Hospitalizado'";
		$err = mysql_query($query,$conex);
		$neuhos = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '11-Nefrologia' and Estado_Paciente='01-Hospitalizado'";
		$err = mysql_query($query,$conex);
		$nefhos = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '12-Neumologia' and Estado_Paciente='01-Hospitalizado'";
		$err = mysql_query($query,$conex);
		$nemhos = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '13-Hemodinamia' and Estado_Paciente='01-Hospitalizado'";
		$err = mysql_query($query,$conex);
		$hemhos = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '14-Oncologia' and Estado_Paciente='01-Hospitalizado'";
		$err = mysql_query($query,$conex);
		$oncohos = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '15-Toxicologia' and Estado_Paciente='01-Hospitalizado'";
		$err = mysql_query($query,$conex);
		$toxhos = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '16-Urologia' and Estado_Paciente='01-Hospitalizado'";
		$err = mysql_query($query,$conex);
		$urohos = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '17-Cirugia Infantil' and Estado_Paciente='01-Hospitalizado'";
		$err = mysql_query($query,$conex);
		$cxinhos = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '18-Neurocirugia' and Estado_Paciente='01-Hospitalizado'";
		$err = mysql_query($query,$conex);
		$neucxhos = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '19-Algesiologia' and Estado_Paciente='01-Hospitalizado'";
		$err = mysql_query($query,$conex);
		$alghos = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '20-Cardiovascular' and Estado_Paciente='01-Hospitalizado'";
		$err = mysql_query($query,$conex);
		$carvhos = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '21-Dermatologia' and Estado_Paciente='01-Hospitalizado'";
		$err = mysql_query($query,$conex);
		$dermhos = mysql_num_rows($err);
		
		$query = "select * from urgen_000003 where (Fecha between '$fecha1' and '$fecha2') and Especialidad= '22-Cardioinfantil' and Estado_Paciente='01-Hospitalizado'";
		$err = mysql_query($query,$conex);
		$carinhos = mysql_num_rows($err);
		
		*/
		
		
		echo "<table align=center border=1 width=450 >";
		echo "<tr><th align=center colspan=3  bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>INFORME</b></font></th></tr>";
	    echo "<td align=left ><font size=3  face='arial'><b>TOTAL LLAMADAS RECIBIDAS</b> </td>";
	    echo "<td align=left colspan=2><font size=3  face='arial'>".$num."</td>";
	    echo "<tr>";
	    echo " <th align=center colspan=3  bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>PACIENTES ACEPTADOS</b></font></th></tr>";
		echo "</tr>";
	    	
			  $query = "select entidad,count(*)
						 from urgen_000003 
			             where Fecha between '$fecha1' and '$fecha2' 
			             and  Acepto = 'on' 
			             group by entidad ";
			$err = mysql_query($query,$conex);
			$tot = mysql_num_rows($err);
		 if ($tot>0)
		 	{
			 	for ($i=0;$i<$tot;$i++)
			 	{
				 	$row=mysql_fetch_row($err);
				 	echo "<tr>";
					echo "<td align=left><font size=3  face='arial'><b>".$row[0]."</b> </td>";
					echo "<td align=left colspan=2><font size=3  face='arial'>".$row[1]."</td>";
					echo "</tr>";
				}
			
			}
	    echo "</tr>";
	    echo "<tr>";
		echo "<td align=left><font size=3 bgcolor='#cccccc' face='arial'><b>TOTAL</b> </td>";
	    echo "<td align=left colspan=2><font size=3  face='arial'>".$acep."</td>";
	    echo "</tr>";
	     echo "<tr>";
	    echo " <th align=center colspan=3  bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>CAUSAS PACIENTES NO ACEPTADOS</b></font></th></tr>";
		 $query = "select Observaciones,count(*)
						 from urgen_000003 
			             where Fecha between '$fecha1' and '$fecha2' 
			             and  Acepto = 'off' 
			             group by Observaciones";
			$err = mysql_query($query,$conex);
			$tot = mysql_num_rows($err);
		 if ($tot>0)
		 	{
			 	for ($i=0;$i<$tot;$i++)
			 	{
				 	$row=mysql_fetch_row($err);
				 	echo "<tr>";
					if ($row[0] == '00-')
						{
							echo "<td align=left><font size=3  face='arial'><b>SIN DATO</b> </td>";
						}
					else
						{
							echo "<td align=left><font size=3  face='arial'><b>".$row[0]."</b> </td>";
						}
					echo "<td align=left colspan=2><font size=3  face='arial'>".$row[1]."</td>";
					echo "</tr>";
				}
			
			}
	    echo "</tr>";
	    echo "<tr>";
		echo "<td align=left><font size=3 bgcolor='#cccccc' face='arial'><b>Total</b> </td>";
	    echo "<td align=left colspan=2><font size=3  face='arial'>".$nacep."</td>";
	    echo "</tr>";
	    
		echo "<table align=center border=1 width=450 >";	
	    echo "<tr>";
	    echo " <th align=center colspan=3  bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>ESPECIALIDAD POR LA QUE INGRESAN LOS PACIENTES ACEPTADOS</b></font></th></tr>";
		$query = "select Especialidad,count(*)
						 from urgen_000003 
			             where Fecha between '$fecha1' and '$fecha2' 
			             and  Acepto = 'on' 
			             group by Especialidad ";
			$err = mysql_query($query,$conex);
			$tot = mysql_num_rows($err);
		 if ($tot>0)
		 	{
			 	for ($i=0;$i<$tot;$i++)
			 	{
				 	$row=mysql_fetch_row($err);
				 	echo "<tr>";
					echo "<td align=left><font size=3  face='arial'><b>".$row[0]."</b> </td>";
					echo "<td align=left colspan=2><font size=3  face='arial'>".$row[1]."</td>";
					echo "</tr>";
				}
			
			}
	    echo "</tr>";
		
		echo "<table align=center border=1 width=450 >";
	    echo " <th align=center colspan=3  bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>ESPECIALIDADES MAS CONSULTADAS EN PACIENTES POR ENTIDAD</b></font></th></tr>";
	    $query = "select Entidad,Especialidad,COUNT(*) from urgen_000003 where Fecha between '$fecha1' and '$fecha2' and Acepto= 'on' group by 1,2 order by 1 desc";
		$err = mysql_query($query,$conex);
		$tot = mysql_num_rows($err);
		$band = 0;
		$row1 = " ";
		if ($tot>0)
		 	{
			 	for ($i=0;$i<$tot;$i++)
			 			{
				 			$row=mysql_fetch_row($err);
							echo "<tr>";
							if ($row1 != $row[0])
								$band = 0;
							if ($band == 0)
								{
									echo " <th align=center colspan=3  bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>".$row[0]."</b></font></th></tr>";
									$row1=$row[0];
									$band = 1;
								}
							echo "<td align=left colspan=2><font size=3  face='arial'>".$row[1]."</td>";
							echo "<td align=left colspan=2><font size=3  face='arial'>".$row[2]."</td>";
							echo "</tr>";
						    
				 		}		 				
    		}
		}
	}
}
include_once("free.php");
?>
