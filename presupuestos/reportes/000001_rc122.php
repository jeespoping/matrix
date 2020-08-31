<html>
<head>
  <title>MATRIX</title>
	<script type="text/javascript">
		<!--
		function todos(num)
		{
			for (i=1;i<=num;i++)
			{
				document.getElementById('C'+i).checked=true;
			}
		}
		//-->
	</script>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Personal Presupuestado x Unidad</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc122.php Ver. 2018-03-15</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc122.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wcco1)  or !isset($wcco2))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>PERSONAL PRESUPUESTADO X UNIDAD</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Presupuestacion</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			if($key=="costosyp" or (isset($call) and $call == "SIG") or (isset($call) and $call == "SIC"))
			{
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
			}
			else
			{
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Proceso</td><td bgcolor=#cccccc align=center>";
				$query = "SELECT Cc,cconom  from ".$empresa."_000125,".$empresa."_000161 where empleado ='".$key."'  and cc=ccocod group by 1 order by Cc";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					echo "<select name='wcco1'>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						echo "<option>".$row[0]."-".$row[1]."</option>";
					}
					echo "</select>";
				}
				echo "</td></tr>";
				$wcco2="NO";
				echo "<center><input type='HIDDEN' name= 'wcco2' value='".$wcco2."'>";
				echo "<tr><td bgcolor=#cccccc align=center>Empresa de  Proceso</td><td bgcolor=#cccccc align=center>";
				$query = "SELECT Empcod,Empdes  from ".$empresa."_000153,".$empresa."_000125 where empleado = '".$key."' and empresa = Empcod group by 1 order by Empcod";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					echo "<select name='wemp' OnChange='enter()'>";
					echo "<option>Seleccione</option>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						if(isset($wemp) and substr($wemp,0,strpos($wemp,"-")) == $row[0])
							echo "<option selected>".$row[0]."-".$row[1]."</option>";
						else
							echo "<option>".$row[0]."-".$row[1]."</option>";
					}
					echo "</select>";
				}
				echo "</td></tr>";
			}
			echo "<tr><td bgcolor=#cccccc align=center>Tipo de Empleado</td>";
			echo "<td bgcolor=#cccccc align=left>";
			$query  = "select Mtecod,Mtedes from ".$empresa."_000143 where Mteest='on' ";
			$query .= "  order by 1 ";
			$err = mysql_query($query,$conex)or die (mysql_errno().":".mysql_error());
			$numE = mysql_num_rows($err);
			echo "<input type='checkbox' name='x[0]' value='0' onClick=\"todos('".$numE."')\">TODOS<br>";
			echo "<input type='HIDDEN' name= 'numE' value='".$numE."'>";
			for ($i=1;$i<=$numE;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<input type='checkbox' name='x[".$i."]' id='C".$i."' value='".$row[0]."'>".$row[1]."<br>";
			}
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Con Subtotales</td><td bgcolor=#cccccc align=center><input type='checkbox' name='wsubt'>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$wcco1 = substr($wcco1,0,4);
			if($wcco2 == "NO")
				$wcco2 = $wcco1;
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			$wtipos="";
			for ($i=1;$i<=$numE;$i++)
			{
				if(isset($x[$i]))
					$wtipos .= "'".$x[$i]."',";
			}
			$wtipos=substr($wtipos,0,strlen($wtipos)-1);
			//                   0       1       2       3       4       5      6              7            8      9      10      11
			$query  = "select Nomcco, Cconom, Nomofi, Carnom, Nomobs, Nommin, Nommfi, ((Nombas * Nomhco)),Nomhco,Nomtip,Nompre, Nomrec from ".$empresa."_000034, ".$empresa."_000005, ".$empresa."_000004 ";
			$query .= "  where Nomano = ".$wanop;
			$query .= "    and Nomemp = '".$wemp."' ";
			$query .= "    and Nomcco between '".$wcco1."' and '".$wcco2."' ";
			$query .= "    and Nomcco = Ccocod";
			$query .= "    and Nomemp = Ccoemp";
			$query .= "    and Nomofi = Carcod";
			$query .= "    and Nomemp = Caremp";
			$query .= "    and Nomtip IN (".$wtipos.") ";
			$query .= "  order by 1,9,3 ";
			$err = mysql_query($query,$conex)or die (mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			echo "<table border=0 align=center>";
			echo "<tr><td colspan=15 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=15 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=15 align=center>PERSONAL PRESUPUESTADO X UNIDAD</td></tr>";
			echo "<tr><td colspan=15 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=15 align=center>A&Ntilde;O DE PROCESO : ".$wanop."</td></tr>";
			echo "<tr><td align=center bgcolor=#cccccc><b>CENTRO<BR>COSTOS</b></td><td align=center bgcolor=#cccccc><b>TIPO<BR>EMPLEADO</b></td><td align=center bgcolor=#cccccc><b>DESCRIPCION</b></td><td align=center bgcolor=#cccccc><b>CODIGO<BR>CARGO</b></td><td align=center bgcolor=#cccccc><b>DESCRIPCION</b></td><td align=center bgcolor=#cccccc><b>HORAS<br>CONTRATADAS</b></td><td align=center bgcolor=#cccccc><b>MES<BR>INICIAL</b></td><td align=center bgcolor=#cccccc><b>MES<BR>FINAL</b></td><td align=right bgcolor=#cccccc><b>BASICO<BR>MES</b></td><td align=right bgcolor=#cccccc><b>FACTOR<BR>PRESTACIONAL</b></td><td align=right bgcolor=#cccccc><b>FACTOR<BR>DE RECARGO</b></td></tr>";
			$klave="";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($klave != $row[0].$row[2])
				{
					if($i > 0)
					{
						$color="#C3D9FF";
						if(isset($wsubt))
							echo "<tr><td bgcolor=".$color." colspan=3><b>Totales Cargo ".$Carnom." x C.Costos : </b></td><td bgcolor=".$color."><b>".$totemp."</b></td><td bgcolor=".$color."></td><td bgcolor=".$color."><b>".$tothor."</b></td><td bgcolor=".$color." colspan=5></td></tr>";
					}
					$totemp = 0;
					$tothor = 0;
					$klave = $row[0].$row[2];
					$Carnom = $row[3];
				}
				$totemp++;
				$tothor += $row[8];
				if($i % 2 == 0)
					$color="#dddddd";
				else
					$color="#ffffff";
				echo "<tr><td bgcolor=".$color.">".$row[0]."</td>";
				echo "<td bgcolor=".$color.">".$row[9]."</td>";
				echo "<td bgcolor=".$color.">".$row[1]."</td>";
				echo "<td bgcolor=".$color.">".$row[3]."</td>";
				echo "<td bgcolor=".$color.">".$row[4]."</td>";
				echo "<td bgcolor=".$color.">".$row[8]."</td>";
				echo "<td bgcolor=".$color.">".$row[5]."</td>";
				echo "<td bgcolor=".$color.">".$row[6]."</td>";
				$FP = $row[10] * 100;
				$FR = $row[11] * 100;
				echo "<td bgcolor=".$color." align=right>".number_format((double)$row[7],0,'.',',')."</td>"; 
				echo "<td bgcolor=".$color." align=right>".number_format((double)$FP,2,'.',',')."%</td>"; 
				echo "<td bgcolor=".$color." align=right>".number_format((double)$FR,2,'.',',')."%</td></tr>"; 
			}
			$color="#C3D9FF";
			if(isset($wsubt))
				echo "<tr><td bgcolor=".$color." colspan=3><b>Totales Cargo ".$Carnom." x C.Costos : </b></td><td bgcolor=".$color."><b>".$totemp."</b></td><td bgcolor=".$color."></td><td bgcolor=".$color."><b>".$tothor."</b></td><td bgcolor=".$color." colspan=5></td></tr>";
			echo "</table>";
		}
	}
?>
</body>
</html>
