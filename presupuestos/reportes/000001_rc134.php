<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Reporte de Control Obligaciones Financieras</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc134.php Ver. 2017-06-29</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc134.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
			echo "<tr><td align=center colspan=2>REPORTE DE CONTROL OBLIGACIONES FINANCIERAS</td></tr>";
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
			$wemp=substr($wemp,0,strpos($wemp,"-"));
			$query = "select Moftob, Mofnid   from ".$empresa."_000132 ";
			$query = $query."  where Mofest  = 'on' ";
			$query = $query."    and Mofemp = '".$wemp."' ";
			$query = $query."  Order by  Moftob, Mofnid ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<table border=1>";
			echo "<tr><td colspan=16 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=16 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=16 align=center>REPORTE DE CONTROL OBLIGACIONES FINANCIERAS</td></tr>";
			echo "<tr><td><b>TIPO<BR>OBLIGACION</b></td><td><b>NUMERO</b></td><td><b>INCONSISTENCIA</b></td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$query = "select sum(Dofpor)  from ".$empresa."_000133 ";
				$query = $query."     where Doftob = '".$row[0]."'";
				$query = $query."       and Dofnid = '".$row[1]."'";
				$query = $query."       and Dofemp = '".$wemp."' ";
				$err1 = mysql_query($query,$conex);
				$row1 = mysql_fetch_array($err1);
				if($row1[0] == 0)
					echo "<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>OBLIGACION FINANCIERA SIN DISTRIBUCION PORCENTUAL</td></tr>";
				elseif(round($row1[0],4) != 1)
						echo "<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>OBLIGACION FINANCIERA CON DISTRIBUCION PORCENTUAL DIFERENTE DE 1 ".$row1[0]."</td></tr>";
			}
		}
	}
?>
</body>
</html>
