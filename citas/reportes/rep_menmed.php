<html>
<head>
<title>Reporte mensual del tiempo laboral de los medicos</title>
</head>
<font face='arial'>
<BODY TEXT="#000066">

<?php
include_once("conex.php");

/********************************************************
*    REPORTE MENSUAL DEL TIEMPO LABORAL DE LOS MEDICOS	*
*														*
*********************************************************/

//==================================================================================================================================
//PROGRAMA						:Reporte mensual del tiempo laboral de los medicos	 
//AUTOR							:Juan David Londoño
//FECHA CREACION				:MARZO 2007
//FECHA ULTIMA ACTUALIZACION 	:
//DESCRIPCION					:Este reporte muestras el tiempo que trabajo el medico mensualmente, con el fin de verificar el cobro
//								 que ellos hacen.
//								 Esto se hace con tomando la agenda de citas y sumando las horas de las citas atendidas.
//								 
//==================================================================================================================================
$wactualiz="Ver. 2007-03-20";


session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	//$empresa='farstore';
	
	

	


	echo "<form action='' method=post>";
	
	
if(!isset($fecha2))
	{
		if (!isset($fecha1))
		$fecha1="";
		if (!isset($fecha2))
		$fecha2="";
		
		echo  "<center><table border=1>";
		echo "<tr><td  colspan=2 align=center><img SRC='/MATRIX/images/medical/citas/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td colspan=2 align=center><b>REPORTE MENSUAL DEL TIEMPO LABORAL DE LOS MEDICOS</b></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center><b><font text color=#003366 size=3> Medico: </font></b></td>";
		echo "<td bgcolor=#cccccc align=center><select name='medico'>";

        $query =  " SELECT DISTINCT (Codigo), Descripcion
                FROM ".$empresa."_000010 
             	ORDER BY Codigo ";
        $err = mysql_query($query,$conex);
        $num = mysql_num_rows($err);
        //echo "<option></option>";
	        for ($i=1;$i<=$num;$i++)
	        {
	            $row = mysql_fetch_array($err);
	            echo "<option>".$row[0]."-".$row[1]."</option>";
	        }
        echo "</select></td>";
		echo  "<tr><td bgcolor=#cccccc align=center><b><font text color=#003366 size=3>Fecha inicial</font></b></td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='fecha1' size=10 maxlength=10 value='".$fecha1."'><b><font text color=#003366 size=3> AAAA-MM-DD</font></b></td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center><b><font text color=#003366 size=3>Fecha final</font></b></td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='fecha2' size=10 maxlength=10 value='".$fecha2."'><b><font text color=#003366 size=3>AAAA-MM-DD</font></b></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	
	}else // aca comienza la impresion
		{
		
		$cod=explode("-",$medico); //explode para el codigo del medico	
		
		$query =  " SELECT Nom_pac, Hi, Hf, Fecha
                	  FROM ".$empresa."_000009
              	     WHERE Fecha between '".$fecha1."' AND '".$fecha2."'
              	       AND Cod_equ = '".$cod[0]."' 
              	  	   AND Asistida='on'	
              	  ORDER BY Fecha";
		 $err = mysql_query($query,$conex);
		 $num = mysql_num_rows($err);
		 //echo $num;
			 if ($num>0)
			 {
			 	echo  "<center><table border=0>";
				echo "<tr bgcolor=cccccc ><td  colspan=5 align=center><img SRC='/MATRIX/images/medical/pos/logo_".$empresa.".png'></td></tr>";
				echo "<tr bgcolor=cccccc ><td colspan=5 align=center><font color=003366><I><b>-REPORTE MENSUAL DEL TIEMPO LABORAL DE LOS MEDICOS-</b></td></tr>";
			 	echo "<tr bgcolor=cccccc ><td colspan=5 align=center><font color=003366><I><b>MEDICO: ".$medico."</b></td></tr>";
			 	echo "<tr bgcolor=cccccc ><td colspan=5 align=center><font color=003366><I><b>DESDE: ".$fecha1." HASTA ".$fecha2."</b></td></tr>";
			 	echo "<tr bgcolor=cccccc ><td align=center><font color=003366><I><b>-NOMBRE DEL PACIENTE-</b></td>";
	  			echo "<td align=center><font color=003366><I><b>-HORA INICIAL-</b></td>";
	  			echo "<td align=center><font color=003366><I><b>-HORA FINAL-</b></td>";
	  			echo "<td align=center><font color=003366><I><b>-TIEMPO (MINS)-</b></td>";
				echo "<td align=center><font color=003366><I><b>-FECHA-</b></td></tr>";
	
				$time=0;
				 for ($i=1;$i<=$num;$i++)
		        {
		        	
		        	if (is_int ($i/2))
	                    $wcf="DDDDDD";
	                    else
	                    $wcf="CCFFFF";
	                    
				 $row = mysql_fetch_array($err);
				 
				$mi=substr($row[1],2,2); // minutos de la hora inicial
				$mf=substr($row[2],2,2); // minutos de la hora final
				
				$hi =substr($row[1],0,2); // horas de la hora inicial
				$hf =substr($row[2],0,2); // horas de la hora final
				
				if ($mf==0) // para poner los minutos en 60 y restar una hora
					{
						$mf=60;
						$hf=$hf-1;
					}
				
				$mt=$mf-$mi; //minutos totales
				
				$ht=$hf-$hi; //horas totales
				$htot=$ht*60; // se convierten las horas totales a minutos
				
				$titot=$htot+$mt; //tiempo total en minutos
				
				// esto es para la impresion de la hora en el reporte
				$hin =substr($row[1],0,2).":".substr($row[1],2,2); //hora inicial en hora militar
				$hfi =substr($row[2],0,2).":".substr($row[2],2,2); //hora final en hora militar	
				
				echo "<tr bgcolor=".$wcf."><td align=left>".$row[0]."&nbsp</td><td align=center>".$hin."&nbsp</td>";
				echo "<td align=center>".$hfi."&nbsp</td><td align=center>".$titot."&nbsp</td><td align=center>".$row[3]."&nbsp</td></tr>";
				
				$time=$titot+$time; // la suma de todos los tiempos totales en minutos
				}
				
				$timeh=$time/60;
			echo "<tr bgcolor=cccccc ><td colspan=3 align=left><font color=003366><I><b>TIEMPO TOTAL LABORADO</b></td><td colspan=1 align=center><font color=003366><I><b>".$time." (mins)</b></td>";
			echo "<td colspan=1 align=center><font color=003366><I><b>".number_format($timeh,2,'.',',')." (horas)</b></td></tr>";
			echo "</table>";
			}else
				{
					echo "<br>";
					echo "<br>";
					echo  "<center><table border=1>";
					echo "<tr bgcolor=cccccc ><td colspan=5 align=center><font color=003366 SIZE=5><I><b>***EL MEDICO NO TIENE PACIENTES ATENDIDOS ENTRE ESAS FECHAS***</b></td></tr>";
				 	echo "</table>";
				}
		}
		
}

?>	