<html>
<head>
<title>Reporte de historias por odontologo</title>
</head>
<body >
<font face='arial'>
<BODY TEXT="#000066">

<?php
include_once("conex.php");

/********************************************************
*    REPORTE DE HISTORIAS POR ODONTOLOGO				*
*														*
*********************************************************/

//==================================================================================================================================
//PROGRAMA						:Reporte de historias por odontologo
//AUTOR							:Juan David Londoño
//FECHA CREACION				:ABRIL 2007
//FECHA ULTIMA ACTUALIZACION 	:03 de Abril de 2007
//DESCRIPCION					:Este es el reporte muestra las historias clinicas hechas por cada odontologo, para 
//								 realizar auditorias internas.
//
//ACTUALIZACIONES: 
//==================================================================================================================================
$actu='2007-04-03';

session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{

	

	


	echo "<form name='datos' form action='' method=post>";
	$empresa='soe1';
	if (!isset ($odon))
	{
		echo "<table border=1 align=center >";
		echo "<tr><td align=center><img SRC='/MATRIX/images/medical/SOE/SOE1.JPG' width='242' height='133'></td>";
		echo "<tr><td align=center><font size='4'><br>REPORTE DE HISTORIAS POR ODONTOLOGO<br><font size='1'>Ver. ".$actu."</td></tr>";
		echo "<tr><td align=center>ODONTOLOGO: <select name='odon'>";
		// este es el query para traer todos los odontologos
		$query="SELECT subcodigo, descripcion 
				  FROM det_selecciones
				 WHERE Medico='soe1'  
			       AND codigo='008'
			  ORDER BY 1";
			  
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		
		for ($j=0;$j<$num;$j++)
		{
			$row = mysql_fetch_array($err);
			echo "<option>".$row[0]."-".$row[1]."</option>";
		}
		echo "</select></td></tr>";
		echo"<tr><td colspan=2 align=center><input type='submit' value='ACEPTAR'></td></tr></form>";
		echo "</table>";
	}else // apartir de aca comienza la impresion
		{
			$query="SELECT Identificacion 
					  FROM ".$empresa."_000011
					 WHERE Odontologo='".$odon."'";
			  
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			//echo mysql_errno() ."=". mysql_error();
			echo "<table border=1 align=center >";
			echo "<tr bgcolor=#FFFFF><td align=center><font size='4'><br>REPORTE DE HISTORIAS POR ODONTOLOGO<br><font size='1'>Ver. ".$actu."</td></tr>";
			echo "<tr bgcolor=#FFFFF><td align=center><font size='3'><B>ODONTOLOGO: ".$odon."</td></tr>";
			
			for ($j=1;$j<=$num;$j++)
			{
				if (is_int ($j/2))
                $wcf="DDDDDD";//gris
                else
                $wcf="FFFFF";//crema
                
			    $row = mysql_fetch_array($err);
				//echo $row[0]."<br>";
				$hyper="<A HREF='/matrix/soe/reportes/reporteHC.php?pac=".$row[0]."'>".$row[0]."</a>";
				echo "<tr bgcolor=".$wcf."><td  colspan= '4' align=left><font size=3 face='arial' >".$j."--$hyper</td></tr>";
			}
			echo "<tr bgcolor=#FFFFF><td align=right><font size='3'><B>TOTAL: ".$num."</td></tr>";
		}
		
}

























