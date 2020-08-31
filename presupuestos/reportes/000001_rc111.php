<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Reporte de Control Indirectos Presupuestados</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc111.php Ver. 2016-06-27</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
function comparacion($vec1,$vec2)
{
	if($vec1[0] > $vec2[0])
		return -1;
	elseif ($vec1[0] < $vec2[0])
				return 1;
			else
				return 0;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc111.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>REPORTE DE CONTROL INDIRECTOS PRESUPUESTADOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
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
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			$query = "select Mipind  from ".$empresa."_000055 ";
			$query = $query."  where Mipano  = ".$wanop;
			$query = $query."    and Mipemp = '".$wemp."'";
			$query = $query."    group by  Mipind";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<table border=1>";
			echo "<tr><td colspan=16 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=16 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=16 align=center>REPORTE DE CONTROL INDIRECTOS PRESUPUESTADOS</td></tr>";
			echo "<tr><td colspan=16 align=center><b>EMPRESA : ".$wempt."</b></td></tr>";
			echo "<tr><td colspan=16 align=center>A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td><b>INDIRECTO</b></td><td><b>DESCRIPCION</b></td><td><b>CRITERIO</b></td><td><b>DESCRIPCION</b></td><td><b>INCONSISTENCIA</b></td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$query = "select  Rciind, Rcicri   from ".$empresa."_000052 ";
				$query = $query."  where Rciind = '".$row[0]."'";
				$query = $query."    and Rciemp = '".$wemp."'";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if($num1 ==  0)
				{
					$query = "select  Midcod, Middes from ".$empresa."_000050 ";
					$query = $query."  where Midcod = '".$row[0]."'";
					$query = $query."    and Midemp = '".$wemp."'";
					$query = $query."    group by  Midcod ";
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					if($num1 >  0)
					{
						$row1 = mysql_fetch_array($err1);
						echo "<tr><td>".$row[0]."</td><td>".$row1[1]."</td><td>&nbsp</td><td>&nbsp</td><td>INDIRECTO SIN CRITERIO RELACIONADO</td></tr>";
					}
					else
						echo "<tr><td>".$row[0]."</td><td>&nbsp</td><td>&nbsp</td><td>&nbsp</td><td>INDIRECTO SIN CRITERIO RELACIONADO Y NO APARECE EN EL MAESTRO DE INDIRECTOS</td></tr>";
				}
				else
				{
					$row1 = mysql_fetch_array($err1);
					$query = "select  Midcod, Middes from ".$empresa."_000050 ";
					$query = $query."  where Midcod = '".$row[0]."'";
					$query = $query."    and Midemp = '".$wemp."'";
					$query = $query."    group by  Midcod ";
					$err2 = mysql_query($query,$conex);
					$num2 = mysql_num_rows($err2);
					if($num2 ==  0)
					{
						echo "<tr><td>".$row[0]."</td><td>&nbsp</td><td>&nbsp</td><td>&nbsp</td><td>INDIRECTO  NO APARECE EN EL MAESTRO DE INDIRECTOS</td></tr>";
						$indirecto="&nbsp ";
					}
					else
					{
						$row2 = mysql_fetch_array($err2);
						$indirecto=$row2[1];
					}
					$query = "select   Crides  from ".$empresa."_000051 ";
					$query = $query."     where Cricod =  '".$row1[1]."'";
					$err3 = mysql_query($query,$conex);
					$num3 = mysql_num_rows($err3);
					if($num3 ==  0)
					{
						echo "<tr><td>".$row[0]."</td><td>".$indirecto."</td><td>".$row1[1]."</td><td>&nbsp</td><td>CRITERIO NO APARECE EN EL MAESTRO DE CRITERIOS</td></tr>";
						$criterio="&nbsp ";
					}
					else
					{
						$row3 = mysql_fetch_array($err3);
						$criterio=$row3[0];
					}
					$query = "select   count(*)  from ".$empresa."_000056 ";
					$query = $query."  where Mcpcri = '".$row1[1]."'";
					$query = $query."    and Mcpemp = '".$wemp."'";
					$query = $query."    and Mcpano  = ".$wanop;
					$err4 = mysql_query($query,$conex);
					$num4 = mysql_num_rows($err4);
					$row4 = mysql_fetch_array($err4);
					if($row4[0] == 0)
						echo "<tr><td>".$row[0]."</td><td>".$indirecto."</td><td>".$row1[1]."</td><td>".$criterio."</td><td>RELACION SIN MOVIMIENTO DE CRITERIOS</td></tr>";
					elseif($indirecto != " " and $criterio != " ")
								echo "<tr><td>".$row[0]."</td><td>".$indirecto."</td><td>".$row1[1]."</td><td>".$criterio."</td><td>RELACION OK!!!!</td></tr>";
							else
								echo "<tr><td>".$row[0]."</td><td>".$indirecto."</td><td>".$row1[1]."</td><td>".$criterio."</td><td>RELACION CON MOVIMIENTO DE CRITERIOS</td></tr>";
				}
			}
		}
	}
?>
</body>
</html>
