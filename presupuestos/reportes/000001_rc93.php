<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Conjuntos con Elementos Sin Movimientos</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc93.php Ver. 2016-03-10</b></font></td></tr></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc93.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wcco1)  or !isset($wcco2) or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>CONJUNTOS CON ELEMENTOS SIN MOVIMIENTOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
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
			//                 0       1       2       3        4       5       6    
			$query  = "SELECT Pqucco, Pqupro, Pqugru, Pquccp, Pqucod, Pqutip, Pqucon from ".$empresa."_000099,".$empresa."_000005,".$empresa."_000095 ";
		    $query .= " where Pqucco  between '".$wcco1."' and '".$wcco2."'";
		    $query .= "   and pquemp = '".$wemp."' ";
		    $query .= "   and Pqucco = ccocod ";
		    $query .= "   and pquemp = ccoemp ";
			$query .= "   and Ccocos = 'S' ";
			$query .= "   and pquemp = mpremp ";
			$query .= "   and Pqucco = mprcco ";
			$query .= "   and Pqucon = mprcon ";
			$query .= "   and Pqupro = mprpro ";
			$query .= "   and Pqugru = mprgru ";
			$query .= "   and mprtip = 'C' ";  
		    $query .= " order by  Pqucco, Pqupro ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<table border=1>";
			echo "<tr><td colspan=15 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=15 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=15 align=center>CONJUNTOS CON ELEMENTOS SIN MOVIMIENTOS</td></tr>";
			echo "<tr><td colspan=15 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=15 align=center>PERIODO  : ".$wper1. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td><b>C.C.</b></td><td><b>PROCEDIMIENTO</b></td><td><b>GRUPO</b></td><td><b>U. ORIGEN</b></td><td><b>CODIGO</b></td><td><b>TIPO</b></td><td><b>CONCEPTO</b></td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if ($row[5] == 1)
				{
					$query = "SELECT count(*) from ".$empresa."_000083 ";
				    $query = $query." where cxasub = '".$row[4]."'";
				    $query = $query."   and cxaemp = '".$wemp."' ";
				    $query = $query."   and cxaano = ".$wanop;
				    $query = $query."   and cxames = ".$wper1;
				    $query = $query."   and cxacco = '".$row[3]."'";
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					$row1 = mysql_fetch_array($err1);
					if($row1[0] == 0)
						echo "<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[5]."</td><td>".$row[6]."</td></tr>";
				}
				if ($row[5] == 2)
				{
					$query = "SELECT count(*) from ".$empresa."_000093 ";
					$query = $query."   where mincod = '".$row[4]."'";
					$query = $query."     and minemp = '".$wemp."' ";
					$query = $query."     and minano = ".$wanop;
					$query = $query."     and minmes = ".$wper1;
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					$row1 = mysql_fetch_array($err1);
					if($row1[0] == 0)
						echo "<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[5]."</td><td>".$row[6]."</td></tr>";
				}
				if ($row[5] == 4)
				{
					$query = "SELECT count(*) from ".$empresa."_000086 ";
					$query = $query."   where fijano = ".$wanop;
					$query = $query."     and fijemp = '".$wemp."' ";
					$query = $query."     and fijcco = '".$row[3]."'";
					$query = $query."     and fijcod = '".$row[4]."'";
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					$row1 = mysql_fetch_array($err1);
					if($row1[0] == 0)
						echo "<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[5]."</td><td>".$row[6]."</td></tr>";
				}
			}
		}
	}
?>
</body>
</html>
