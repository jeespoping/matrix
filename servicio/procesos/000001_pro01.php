<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion de Archivo Historico</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro01.php Ver. 1.00</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro01.php' method=post>";
		if(!isset($wanop) or !isset($wmesp))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE ORDENES</td></tr>";
			echo "<tr><td align=center colspan=2>GENERACION DE ARCHIVO HISTORICO</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>Año de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesp' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			if($wmesp == 1)
			{
				$wanopa=$wanop -1;
				$wmespa=12;
			}
			else
			{
				$wanopa=$wanop;
				$wmespa=$wmesp - 1;
			}
			$k=0;
			$query = "SELECT cierre from servcli_000004 ";
  			$query = $query." WHERE ano = ".$wanop;
  			$query = $query."       and mes = ".$wmesp;
   			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
   			if ($num>0 and $row[0] == "off")
			{
				$query = "delete  from servcli_000003 ";
				$query = $query."  where ano = ".$wanop;
				$query = $query."      and mes = ".$wmesp;
				$err = mysql_query($query,$conex);
				$query = "SELECT empresa, especialidad,'1' as estado,'1' as tipo,count(*) from servcli_000001 ";
				$query = $query." where YEAR(Fecha_Recepcion) = ".$wanop;
        		$query = $query."    and  MONTH(Fecha_Recepcion) = ".$wmesp;
        		$query = $query."  group by  empresa, especialidad,estado,tipo ";
        		$query = $query." union ";
        		$query = $query. " SELECT entidad as empresa, especialidad,'1' as estado,'0' as tipo,sum(cantidad) from servcli_000003 ";
				$query = $query." where ano = ".$wanopa;
        		$query = $query."     and  mes = ".$wmespa;
        		$query = $query."     and  estado = '4' ";
        		$query = $query."  group by  empresa, especialidad,estado,tipo ";
        		$query = $query." union ";
        		$query = $query. " SELECT empresa, especialidad,'2' as estado,'1' as tipo,count(*) from servcli_000001 ";
				$query = $query." where YEAR(Fecha_Recepcion) = ".$wanop;
        		$query = $query."    and  MONTH(Fecha_Recepcion) = ".$wmesp;
        		$query = $query."    and  YEAR(Fecha_ejecucion) = ".$wanop;
        		$query = $query."    and  MONTH(Fecha_ejecucion) = ".$wmesp;
        		$query = $query."    and  estado = '2-REALIZADA' ";
        		$query = $query."  group by  empresa, especialidad,estado,tipo ";
        		$query = $query." union ";
        		$query = $query. " SELECT empresa, especialidad,'2' as estado,'0' as tipo,count(*) from servcli_000001 ";
				$query = $query." where ((YEAR(Fecha_Recepcion) = ".$wanop;
        		$query = $query."    and  MONTH(Fecha_Recepcion) < ".$wmesp.") ";
        		$query = $query."       or YEAR(Fecha_Recepcion) < ".$wanop.") ";
        		$query = $query."    and  YEAR(Fecha_ejecucion) = ".$wanop;
        		$query = $query."    and  MONTH(Fecha_ejecucion) = ".$wmesp;
        		$query = $query."    and  estado = '2-REALIZADA' ";
        		$query = $query."  group by  empresa, especialidad,estado,tipo ";
        		$query = $query." union ";
        		$query = $query. " SELECT empresa, especialidad,'3' as estado,'1' as tipo,count(*) from servcli_000001 ";
				$query = $query." where YEAR(Fecha_Recepcion) = ".$wanop;
        		$query = $query."    and  MONTH(Fecha_Recepcion) = ".$wmesp;
        		$query = $query."    and  YEAR(Fecha_ejecucion) = ".$wanop;
        		$query = $query."    and  MONTH(Fecha_ejecucion) = ".$wmesp;
        		$query = $query."    and  (estado = '1-ANULADA' ";
        		$query = $query."       or    estado = '3-RETIRADA X PACIENTE') ";
        		$query = $query."  group by  empresa, especialidad,estado,tipo ";
        		$query = $query." union ";
        		$query = $query. " SELECT empresa, especialidad,'3' as estado,'0' as tipo,count(*) from servcli_000001 ";
				$query = $query." where ((YEAR(Fecha_Recepcion) = ".$wanop;
        		$query = $query."    and  MONTH(Fecha_Recepcion) < ".$wmesp.") ";
        		$query = $query."       or YEAR(Fecha_Recepcion) < ".$wanop.") ";
        		$query = $query."    and  YEAR(Fecha_ejecucion) = ".$wanop;
        		$query = $query."    and  MONTH(Fecha_ejecucion) = ".$wmesp;
        		$query = $query."    and  (estado = '1-ANULADA' ";
        		$query = $query."       or    estado = '3-RETIRADA X PACIENTE') ";
        		$query = $query."  group by  empresa, especialidad,estado,tipo ";
        		$query = $query." union ";
        		$query = $query. " SELECT empresa, especialidad,'4' as estado,'1' as tipo,count(*) from servcli_000001 ";
				$query = $query." where YEAR(Fecha_Recepcion) = ".$wanop;
        		$query = $query."    and  MONTH(Fecha_Recepcion) = ".$wmesp;
        		$query = $query."    and  estado = '0-PENDIENTE' ";
        		$query = $query."  group by  empresa, especialidad,estado,tipo ";
        		$query = $query." union ";
        		$query = $query. " SELECT empresa, especialidad,'4' as estado,'0' as tipo,count(*) from servcli_000001 ";
				$query = $query." where ((YEAR(Fecha_Recepcion) = ".$wanop;
        		$query = $query."    and  MONTH(Fecha_Recepcion) < ".$wmesp.") ";
        		$query = $query."       or YEAR(Fecha_Recepcion) < ".$wanop.") ";
        		$query = $query."    and  estado = '0-PENDIENTE' ";
        		$query = $query."  group by  empresa, especialidad,estado,tipo ";
       			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert servcli_000003 (medico,fecha_data,hora_data, Ano, Mes, Entidad, Especialidad, Estado, Tipo, Cantidad, seguridad) values ('servcli','".$fecha."','".$hora."',".$wanop.",".$wmesp.",'".$row[0]."','".$row[1]."',".$row[2].",".$row[3].",".$row[4].",'C-servcli')";
		       			$err2 = mysql_query($query,$conex);
		       			if ($err2 != 1)
							echo mysql_errno().":".mysql_error()."<br>";
						else
						{
		           			$k++;
		           			echo "REGISTRO INSERTADO NRo  : ".$k."<br>";
	   					}
   					}
				}
				echo "TOTAL REGISTROS INSERTADOS : ".$k."<br>";
        	}
        	else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>EL PERIODO ESTA CERRADO -- NO PUEDE ACTUALIZAR LA INFORMACION</MARQUEE></FONT>";
				echo "<br><br>";			
			}
        }
}		
?>
</body>
</html>
