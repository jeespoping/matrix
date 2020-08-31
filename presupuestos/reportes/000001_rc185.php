<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Detalle Movimiento Obligaciones Financieras T135</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc185.php Ver. 2017-08-31</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc185.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanoi) or !isset($wemp) or $wemp == "Seleccione" or !isset($wanof) or !isset($wmesi) or !isset($wmesf)  or $wmesi < 1 or $wmesi > 12 or $wmesf < 1 or $wmesf > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>DETALLE MOVIMIENTO OBLIGACIONES FINANCIERAS T135</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanoi' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanof' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesi' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesf' size=2 maxlength=2></td></tr>";
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
			echo "<tr><td bgcolor=#cccccc colspan=2 align=center><input type='RADIO' name=x value=0 checked>Real&nbsp&nbsp<input type='RADIO' name=x value=1>Presupuestado&nbsp&nbsp<input type='RADIO' name=x value=2>Todos</td></tr>";
			echo "<tr><td bgcolor=#cccccc colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			//                   0       1       2      3        4       5       6       7      8       9       10 
			$query  = "select Movnid, Moftob, Mofent, Movano, Movmes, Movsai, Movfca, Movfin, Movcin, Movsaf, Moftip from ".$empresa."_000135,".$empresa."_000132 ";
			$query .= "	where ((Movano =  ".$wanoi;
			$query .= "	  and Movmes >= ".$wmesi." and Movano < ".$wanof.") ";
			$query .= "	   or (Movano > ".$wanoi;
			$query .= "	  and  Movano < ".$wanof.") ";
			$query .= "	   or (Movano = ".$wanof;
			$query .= "	  and  Movmes <= ".$wmesf." and Movano > ".$wanoi.") ";
			$query .= "	   or (Movano = ".$wanoi."  and Movano = ".$wanof; 
			$query .= "	  and  Movmes >= ".$wmesi." and Movmes <= ".$wmesf.")) ";
			$query .= "   and Movemp = '".$wemp."'";
			$query .= "	  and Movemp = Mofemp ";
			$query .= "	  and Movnid = Mofnid ";
			switch ($x)
			{
				case 0:
					$query .= " and Moftip = 'R'";
				break;
				case 1:
					$query .= " and Moftip = 'P'";
				break;
				case 2:
					$query .= " ";
				break;
			}
			$query .= "	order by 4,5  ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			
			echo "<table border=1>";
			echo "<tr><td colspan=12 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=12 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=12 align=center>DETALLE FACTURACI&Oacute;N X ENTIDAD - CONCEPTO - CCO</td></tr>";
			echo "<tr><td colspan=12 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=12 align=center><b>A&Ntilde;O INICIAL: ".$wanoi." A&Ntilde;O FINAL : ".$wanof."</b></td></tr>";
			echo "<tr><td colspan=12 align=center><b>MES INICIAL: ".$wmesi." MES FINAL : ".$wmesf."</b></td></tr>";
			echo "<tr><td><b>Obligacion</b></td><td><b>Tipo<br>Obligacion</b></td><td><b>Entidad</b></td><td><b>A&ntilde;o</b></td><td><b>Mes</b></td><td><b>Saldo<br>Inicial</b></td><td><b>Flujo<br>Capital</b></td><td><b>Flujo<br>Interes</b></td><td><b>Causacion<br>Interes</b></td><td><b>Saldo<br>Final</b></td><td><b>Tipo</b></td></tr>";
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo"<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td align=right>".number_format((double)$row[5],0,'.',',')."</td><td align=right>".number_format((double)$row[6],0,'.',',')."</td><td align=right>".number_format((double)$row[7],0,'.',',')."</td><td align=right>".number_format((double)$row[8],0,'.',',')."</td><td align=right>".number_format((double)$row[9],0,'.',',')."</td><td align=right>".$row[10]."</td></tr>";
				}
			}
		}
	}
?>
</body>
</html>
