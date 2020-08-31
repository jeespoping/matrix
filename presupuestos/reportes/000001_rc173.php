<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Procedimientos con Elementos Sin Movimientos (CP)</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc173.php Ver. 2016-04-22</b></font></td></tr></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc173.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wcco1)  or !isset($wcco2) or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>PROCEDIMIENTOS CON ELEMENTOS SIN MOVIMIENTOS (CP)</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Año de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
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
			$wcco2=strtolower ($wcco2);
			//                 0        1       2      3        4       5        6
			//$query = "SELECT Procco, Propro, Procon, Proent, Proccp, Procod, Protip from ".$empresa."_000100 ";
			//                 0        1      2        3      4        5       6
			$query = "SELECT Procco, Propro, Progru, Proccp, Procod, Protip, Procon from ".$empresa."_000100,".$empresa."_000005,".$empresa."_000095 ";
		    $query .= " where procco  between '".$wcco1."' and '".$wcco2."'";
		    $query .= "   and Proemp = '".$wemp."'"; 
		    $query .= "   and Procco = ccocod ";
		    $query .= "   and Proemp = ccoemp ";
			$query .= "   and Ccocos = 'S' ";
			$query .= "   and Proemp = mpremp ";
			$query .= "   and Procco = mprcco ";
			$query .= "   and Procon = mprcon ";
			$query .= "   and Procod = mprpro ";
			$query .= "   and Progru = mprgru ";
			$query .= "   and mprtip in ('P','O') ";
		    $query .= " order by  Procco, Propro ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<table border=1>";
			echo "<tr><td colspan=15 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=15 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=15 align=center>PROCEDIMIENTOS CON ELEMENTOS SIN MOVIMIENTOS (CP)</td></tr>";
			echo "<tr><td colspan=15 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=15 align=center>PERIODO  : ".$wper1. " AÑO : ".$wanop."</td></tr>";
			echo "<tr><td><b>C.C.</b></td><td><b>PROCEDIMIENTO</b></td><td><b>GRUPO</b></td><td><b>U. ORIGEN</b></td><td><b>CODIGO</b></td><td><b>TIPO</b></td><td><b>CONCEPTO</b></td><td><b>COMENTARIOS</b></td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$query = "SELECT Mprtip from ".$empresa."_000095 ";
				$query = $query." where Mprcco = '".$row[0]."'";
				$query = $query."   and Mpremp = '".$wemp."'"; 
			    $query = $query."   and Mprpro = '".$row[1]."'";
			    $query = $query."   and Mprgru = '".$row[2]."'";
			    $query = $query."   and Mprcon = '".$row[6]."'";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				$tipo="";
				if($num1 > 0)
				{
					$row1 = mysql_fetch_array($err1);
					$tipo = $row1[0];
				}
				if($tipo != "I")
				{
					if ($row[5] == 1)
					{
						$query = "SELECT count(*) from ".$empresa."_000154 ";
					    $query = $query." where cxpsub = '".$row[4]."'";
					    $query = $query."   and cxpemp = '".$wemp."'"; 
					    $query = $query."   and cxpano = ".$wanop;
					    $query = $query."   and cxpmes = ".$wper1;
					    $query = $query."   and cxpcco = '".$row[3]."'";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						$row1 = mysql_fetch_array($err1);
						if($row1[0] == 0)
							echo "<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[5]."</td><td>".$row[6]."</td><td>SUBPROCESO SIN COSTO X ACTIVIDAD</td></tr>";
					}
					if ($row[5] == 2)
					{
						$query = "SELECT count(*) from ".$empresa."_000093 ";
						$query = $query." where mincod = '".$row[4]."'";
						$query = $query."   and minemp = '".$wemp."'"; 
						$query = $query."   and minano = ".$wanop;
						$query = $query."   and minmes = ".$wper1;
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						$row1 = mysql_fetch_array($err1);
						if($row1[0] == 0)
							echo "<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[5]."</td><td>".$row[6]."</td><td>INSUMO SIN COSTO</td></tr>";
						
					}
					if ($row[5] == 4)
					{
						$query = "SELECT count(*) from ".$empresa."_000086 ";
						$query = $query." where fijano = ".$wanop;
						$query = $query."   and fijemp = '".$wemp."'"; 
						$query = $query."   and fijcco = '".$row[3]."'";
						$query = $query."   and fijcod = '".$row[4]."'";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						$row1 = mysql_fetch_array($err1);
						if($row1[0] == 0)
							echo "<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[5]."</td><td>".$row[6]."</td><td>CODIGO SIN COSTO FIJO</td></tr>";
					}
				}
			}
			$query  = "SELECT Pqucco, Pqupro, Pqugru, Pqucod, Pqucon from ".$empresa."_000099 ";
			$query .= "where Pqucco  between '".$wcco1."' and '".$wcco2."'";
			$query .= "  and Pquemp = '".$wemp."'"; 
			$query .= "  and Pqutip = '2' ";
			$query .= "  and Pqucod not in (SELECT mincod from ".$empresa."_000093  where minano = ".$wanop." and minmes = ".$wper1.") ";
			$query .= " Group by 1,2,3 ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			for ($i=0;$i<$num1;$i++)
			{
				$row1 = mysql_fetch_array($err1);
				$query = "SELECT Mprtip from ".$empresa."_000095 ";
				$query = $query." where Mprcco = '".$row1[0]."'";
				$query = $query."   and Mpremp = '".$wemp."'"; 
			    $query = $query."   and Mprpro = '".$row1[1]."'";
			    $query = $query."   and Mprgru = '".$row1[2]."'";
			    $query = $query."   and Mprcon = '".$row1[4]."'";
				$err2 = mysql_query($query,$conex);
				$num2 = mysql_num_rows($err2);
				$tipo="";
				if($num2 > 0)
				{
					$row2 = mysql_fetch_array($err2);
					$tipo = $row2[0];
				}
				if($tipo != "I")
					echo "<tr><td>".$row1[0]."</td><td>".$row1[1]."</td><td>".$row1[2]."</td><td>&nbsp</td><td>".$row1[3]."</td><td>2</td><td>".$row1[4]."</td><td>INSUMO SIN COSTO EN PAQUETES</td></tr>";
			}
			$query  = "SELECT Procco, Propro, Progru, Protip, Procon from ".$empresa."_000100 ";
			$query .= " where Procco  between '".$wcco1."' and '".$wcco2."'";
			$query .= "   and Proemp = '".$wemp."'"; 
			$query .= "   and Propro not in (SELECT Mprpro from ".$empresa."_000095  where Mprcco = Procco and Mprgru = Progru and Mprcon = Procon and Mpremp='".$wemp."') ";
			$query .= " Group by 1,2 ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			for ($i=0;$i<$num1;$i++)
			{
				$row1 = mysql_fetch_array($err1);
				echo "<tr><td>".$row1[0]."</td><td>".$row1[1]."</td><td>".$row1[2]."</td><td>&nbsp</td><td>".$row1[3]."</td><td>2</td><td>".$row1[4]."</td><td>PROTOCOLO SIN NOMBRE TABLA 95</td></tr>";
			}
			$query  = "SELECT Pqucco, Pqupro, Pqugru, Pqutip, Pqucon from ".$empresa."_000099 ";
			$query .= " where Pqucco  between '".$wcco1."' and '".$wcco2."'";
			$query .= "   and Pquemp = '".$wemp."'"; 
			$query .= "   and Pqupro not in (SELECT Mprpro from ".$empresa."_000095  where Mprcco = Pqucco and Mprgru = Pqugru and Mprcon = Pqucon and Mpremp='".$wemp."') ";
			$query .= " Group by 1,2 ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			for ($i=0;$i<$num1;$i++)
			{
				$row1 = mysql_fetch_array($err1);
				echo "<tr><td>".$row1[0]."</td><td>".$row1[1]."</td><td>".$row1[2]."</td><td>&nbsp</td><td>".$row1[3]."</td><td>2</td><td>".$row1[4]."</td><td>PAQUETE SIN NOMBRE TABLA 95</td></tr>";
			}
			$query  = "SELECT Mprcco, Mprpro, Mprgru, Mprtip, Mprcon from ".$empresa."_000095 ";
			$query .= " where Mprcco  between '".$wcco1."' and '".$wcco2."'";
			$query .= "   and Mpremp = '".$wemp."'"; 
			$query .= "   and Mprtip in ('P') ";
			$query .= "   and Mprpro not in (SELECT Propro from ".$empresa."_000100  where Procco = Mprcco and Progru = Mprgru and Procon = Mprcon and Proemp='".$wemp."') ";
			$query .= " Group by 1,2 ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			for ($i=0;$i<$num1;$i++)
			{
				$row1 = mysql_fetch_array($err1);
				echo "<tr><td>".$row1[0]."</td><td>".$row1[1]."</td><td>".$row1[2]."</td><td>&nbsp</td><td>".$row1[3]."</td><td>2</td><td>".$row1[4]."</td><td>SIN PROTOCOLO</td></tr>";
			}
			$query  = "SELECT Mprcco, Mprpro, Mprgru, Mprtip, Mprcon from ".$empresa."_000095 ";
			$query .= " where Mprcco  between '".$wcco1."' and '".$wcco2."'";
			$query .= "   and Mpremp = '".$wemp."'"; 
			$query .= "   and Mprtip in ('C') ";
			$query .= "   and Mprpro not in (SELECT Pqupro from ".$empresa."_000099  where Pqucco = Mprcco and Pqugru = Mprgru and Pqucon = Mprcon and Pquemp='".$wemp."') ";
			$query .= " Group by 1,2 ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			for ($i=0;$i<$num1;$i++)
			{
				$row1 = mysql_fetch_array($err1);
				echo "<tr><td>".$row1[0]."</td><td>".$row1[1]."</td><td>".$row1[2]."</td><td>&nbsp</td><td>".$row1[3]."</td><td>2</td><td>".$row1[4]."</td><td>SIN PROTOCOLO DE PAQUETES</td></tr>";
			}
			echo "</table>";
		}
	}
?>
</body>
</html>
