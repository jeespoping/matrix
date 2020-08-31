<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Informe de Explicaciones Distribuibles</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc141.php Ver. 2016-03-18</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc141.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1) or $wper1 < 1 or $wper1 > 12 or !isset($wcco1))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>INFORME DE EXPLICACIONES DISTRIBUIBLES</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centro Costos </td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
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
			$query = "SELECT ccocod, cconom from ".$empresa."_000005 ";
			$query .= "  where ccocod = '".$wcco1."' ";
			$query .= "    and ccoemp = '".$wemp."'";
			$err = mysql_query($query,$conex);
			$row = mysql_fetch_array($err);
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=4><font size=2>PROMOTORA MEDICA LAS AMERICAS S.A.</font></td></tr>";
			echo "<tr><td align=center colspan=4><font size=2>DIRECCION DE INFORMATICA</font></td></tr>";
			echo "<tr><td align=center colspan=4><font size=2>INFORME DE EXPLICACIONES DISTRIBUIBLES</font></td></tr>";
			echo "<tr><td align=center colspan=4><font size=2>EMPRESA : ".$wempt."</font></td></tr>";
			echo "<tr><td align=center bgcolor=#999999 colspan=4><font size=2><b>UNIDAD : ".$row[0]."-".$row[1]."</b></font></td></tr>";
			echo "<tr><td bgcolor=#CCCCCC align=center><b>RUBRO</b></td><td bgcolor=#CCCCCC align=center><b>NIT</b></td><td bgcolor=#CCCCCC align=center><b>DESCRIPCION</b></td><td bgcolor=#CCCCCC align=center><b>COSTO<BR>TOTAL</b></td></tr>";
			$query = "select Expcpr, Expnit, Expnte, SUM(Expmon)  from ".$empresa."_000011,".$empresa."_000028,".$empresa."_000139  ";
			$query .= " where Expcco = '".$wcco1."' ";
			$query .= "   and Expemp = '".$wemp."'";
  			$query .= "   and Expano = ".$wanop;
            $query .= "   and Expper = ".$wper1;
            $query .= "   and Expcpr = Mgacod ";
            $query .= "   and Expemp = Pfeemp ";
            $query .= "   and Expcco = Pfecco ";
            $query .= "   and Expcpr = Pferub ";
            $query .= "   and Expnit = Pfenit ";
            $query .= "   and Pfecri = 'on' ";
 			$query .= "  group by 1,2,3 ";
 			$query .= "  order by 1,2,3 ";
 			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				if($i % 2 == 0)
					$color="#99CCFF";
				else
					$color="#ffffff";
				$row = mysql_fetch_array($err);
				echo "<tr><td bgcolor=".$color."><font size=2>".$row[0]."</font></td><td bgcolor=".$color."><font size=2>".$row[1]."</font></td><td bgcolor=".$color."><font size=2>".$row[2]."</font></td><td bgcolor=".$color." align=right><font size=2>".number_format($row[3],0,'.',',')."</font></td></tr>";
    		}
    		echo "</table></center>";
		}
	}
?>
</body>
</html>
