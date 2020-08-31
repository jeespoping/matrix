<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Generacion Cargos Pendientes x Facturar Mes Anterior</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro50.php Ver. 2015-09-11</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro50.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wmesp) or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTOS</td></tr>";
			echo "<tr><td align=center colspan=2>GENERACION CARGOS PENDIENTES X FACTURAR MES ANTERIOR</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesp' size=2 maxlength=2></td></tr>";
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
			if($wmesp == 1)
			{
				$wmesi=12;
				$wanoi=$wanop -1;
			}
			else
			{
				$wmesi=$wmesp - 1;
				$wanoi=$wanop ;
			}
			$k=0;
			$query = "DELETE from ".$empresa."_000062 ";
			$query = $query." where mifano = ".$wanop;
			$query = $query."   and mifmes = ".$wmesp;
			$query = $query."   and mifemp = '".$wemp."' ";
			$query = $query."   and mifcla = 'CARGN' ";
			$err = mysql_query($query,$conex);

			$query = "SELECT mifano,mifmes,mifnit,mifcfa,mifcco,mifint,mifinp,mifito,mifcla from ".$empresa."_000062 ";
			$query = $query." where mifano = ".$wanoi;
			$query = $query."   and mifmes = ".$wmesi;
			$query = $query."   and mifemp = '".$wemp."' ";
			$query = $query."   and mifcla = 'CARGP' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$k++;
					$row[5]=$row[5]*(-1);
					$row[6]=$row[6]*(-1);
					$row[7]=$row[7]*(-1);
					$row[8]="CARGN";
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert ".$empresa."_000062 (medico,fecha_data,hora_data,mifemp,mifano,mifmes,mifnit,mifcfa,mifcco,mifint,mifinp,mifito,mifcla,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wmesp.",'".$row[2]."','".$row[3]."','".$row[4]."',".$row[5].",".$row[6].",".$row[7].",'".$row[8]."','C-".$empresa."')";
               		$err2 = mysql_query($query,$conex);
               		echo "REGISTROS INSERTADOS : ".$k."<br>";
           			}
               	}
               	echo "<b>NUMERO DE REGISTROS INSERTADOS : ".$k."</b><br>";
			}
}		
?>
</body>
</html>
