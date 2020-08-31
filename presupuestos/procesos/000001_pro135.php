<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Generacion de Obligaciones Financieras de Inversiones Presupuestadas</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro135.php Ver. 2015-09-25</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");

@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	echo "<form action='000001_pro135.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione")
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>APLICACION DE OBLIGACIONES FINANCIERAS</td></tr>";
		echo "<tr><td align=center colspan=2>GENERACION DE OBLIGACIONES FINANCIERAS DE INVERSIONES PRESUPUESTADAS</td></tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Empresa de  Proceso</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Empcod,Empdes  from ".$empresa."_000153 order by Empcod";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wemp'>";
			echo "<option>Seleccione</option>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}
			echo "</select>";
		}
		echo "</td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$wemp = substr($wemp,0,2);
		#INICIO PROGRAMA
		$k=0;
		$query =" select Invano, Invmes, Invcco, Invact, Invmon, Invtfi  ";
		$query = $query." from ".$empresa."_000019  ";
		$query = $query." where Invano = ".$wanop; 
		$query = $query."   and Invemp = '".$wemp."' ";
		$query = $query." and Invtfi IN ('LG','CR') ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()."<br>");
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$query =" select  Moftob, Mofnid  from ".$empresa."_000132 ";
				$query = $query." where Moftob = '".$row[5]."'"; 
				$query = $query."   and Mofnid = '".$row[3]."'"; 
				$query = $query."   and Mofemp = '".$wemp."' ";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if($num1 == 0)
				{
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert ".$empresa."_000132 (medico,fecha_data,hora_data,Mofemp, Moftob, Mofnid, Mofent, Mofani, Mofmei, Mofmon, Mofopc, Mofpla, Mofpga, Moftta, Moftas, Mofpad, Moffam, Mofper, Moftip, Mofest, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row[5]."','".$row[3]."','',".$row[0].",".$row[1].",".$row[4].",0,0,0,'','',0,'',0,'P','off','C-".$empresa."')";
	       			$err2 = mysql_query($query,$conex);
	       			if ($err2 != 1)
						echo mysql_errno().":".mysql_error()."<br>";
					else
					{
	           			$k++;
	           			echo "REGISTRO INSERTADO  : ".$k."<br>";
   					}
				}
				else
				{
					$query = "update ".$empresa."_000132 set  Mofani=".$row[0].", Mofmei=".$row[1].", Mofmon=".$row[4]." where Moftob='".$row[5]."' and Mofnid='".$row[3]."' and Mofemp='".$wemp."' ";
	       			$err2 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO OBLIGACIONES :".mysql_errno().":".mysql_error()."<br>");
	       			$k++;
	           		echo "REGISTRO ACTUALIZADO  : ".$k."<br>";
				}
				$query =" select Doftob, Dofnid, count(*)  from ".$empresa."_000133 ";
				$query = $query." where Doftob = '".$row[5]."'"; 
				$query = $query."   and Dofnid = '".$row[3]."'";
				$query = $query."   and Dofemp = '".$wemp."' "; 
				$query = $query."  group by 1,2 "; 
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if($num1 == 0)
				{
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert ".$empresa."_000133 (medico,fecha_data,hora_data,Dofemp, Doftob, Dofnid, Dofcco, Dofpor, Dofest, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row[5]."','".$row[3]."','".$row[2]."',1.0,'off','C-".$empresa."')";
	       			$err2 = mysql_query($query,$conex);
	       			if ($err2 != 1)
						echo mysql_errno().":".mysql_error()."<br>";
					else
					{
	           			$k++;
	           			echo "REGISTRO INSERTADO EN TABLA DE DISTRIBUCION DE OBLIGACIONES : ".$k."<br>";
   					}
				}
				else
				{
					$row1 = mysql_fetch_array($err1);
					if($row1[2] == 1)
					{
						$query = "update ".$empresa."_000133 set  Dofcco='".$row[2]."' where Doftob='".$row[5]."' and Dofnid='".$row[3]."' and Dofemp='".$wemp."' ";
		       			$err2 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO DISTRIBUCION DE OBLIGACIONES :".mysql_errno().":".mysql_error()."<br>");
		       			$k++;
		           		echo "REGISTRO ACTUALIZADO EN  TABLA DE DISTRIBUCION DE OBLIGACIONES : ".$k."<br>";
					}
					else
					{
						echo "LA OBLIGACION FINANCIERA ".$row[5]."-".$row[3]." TIENE MAS DE UN REGISTRO DE DISTRIBUCION  : ".$row1[2]."<br>";
					}
				}
			}
			echo "<b>NUMERO DE REGISTROS INSERTADOS/ACTUALIZADOS : ".$k."</b><br>";
		}
	}
}		
?>
</body>
</html>
